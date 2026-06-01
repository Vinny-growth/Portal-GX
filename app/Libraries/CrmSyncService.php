<?php

namespace App\Libraries;

use App\Models\NewsletterCrmSyncModel;
use App\Models\NewsletterEditorialLineModel;

class CrmSyncService
{
    private const VALID_SOURCES = ['leads', 'clients'];
    private const SOURCE_TYPE_MAP = [
        'leads'   => 'crm_lead',
        'clients' => 'crm_client',
    ];

    protected NewsletterCrmSyncModel $syncModel;
    protected NewsletterEditorialLineModel $lineModel;
    protected \CodeIgniter\Database\BaseConnection $db;
    /** @var int[] memoized list of active editorial line ids */
    protected ?array $defaultEditorialLineIds = null;

    public function __construct()
    {
        $this->syncModel = new NewsletterCrmSyncModel();
        $this->lineModel = new NewsletterEditorialLineModel();
        $this->db = \Config\Database::connect(null, false);
    }

    /**
     * Sync both leads and clients. Returns aggregate counters.
     */
    public function syncAll(string $trigger = 'cron', ?int $performedBy = null): array
    {
        $out = ['leads' => null, 'clients' => null];
        foreach (self::VALID_SOURCES as $source) {
            $out[$source] = $this->syncSource($source, $trigger, $performedBy);
        }
        return $out;
    }

    /**
     * Sync one source. Returns array with status/counters and the audit row id.
     */
    public function syncSource(string $source, string $trigger = 'cron', ?int $performedBy = null, bool $fullSync = false): array
    {
        if (!in_array($source, self::VALID_SOURCES, true)) {
            throw new \InvalidArgumentException('Invalid sync source: ' . $source);
        }
        $endpoint = $this->getEndpoint($source);
        $apiKey = $this->env('CRM_LEAD_API_KEY');
        $anonKey = $this->env('CRM_NEWSLETTER_ANON_KEY');
        if ($endpoint === '' || $apiKey === '' || $anonKey === '') {
            throw new \RuntimeException('CRM sync misconfigured: CRM_NEWSLETTER_*_ENDPOINT, CRM_LEAD_API_KEY and CRM_NEWSLETTER_ANON_KEY must be set in .env');
        }

        $updatedSince = $fullSync ? null : $this->getIncrementalCursor($source);
        $auditId = $this->syncModel->start($source, $trigger, $updatedSince, $performedBy);

        $counters = [
            'pages_fetched'          => 0,
            'total_received'         => 0,
            'created_count'          => 0,
            'updated_count'          => 0,
            'skipped_unsubscribed'   => 0,
            'skipped_invalid'        => 0,
            'filtered_opt_out_total' => 0,
        ];
        $errors = [];

        $page = 1;
        $perPage = max(50, min(500, (int) ($this->env('CRM_NEWSLETTER_PER_PAGE') ?: 200)));
        $sourceType = self::SOURCE_TYPE_MAP[$source];

        try {
            while (true) {
                $response = $this->fetchPage($endpoint, $apiKey, $anonKey, $updatedSince, $page, $perPage);
                $counters['pages_fetched']++;
                $data = $response['data'] ?? [];
                $pagination = $response['pagination'] ?? [];
                $meta = $response['meta'] ?? [];
                $counters['total_received'] += count($data);
                $counters['filtered_opt_out_total'] += (int) ($meta['filtered_opt_out_count'] ?? 0);

                foreach ($data as $row) {
                    try {
                        $result = $this->upsertFromCrm($row, $sourceType);
                        switch ($result) {
                            case 'created': $counters['created_count']++; break;
                            case 'updated': $counters['updated_count']++; break;
                            case 'skipped_unsubscribed': $counters['skipped_unsubscribed']++; break;
                            case 'skipped_invalid': $counters['skipped_invalid']++; break;
                        }
                    } catch (\Throwable $e) {
                        $errors[] = 'row ' . ($row['id'] ?? '?') . ': ' . $e->getMessage();
                        $counters['skipped_invalid']++;
                    }
                }

                $hasMore = !empty($pagination['has_more']);
                if (!$hasMore) break;
                $page = (int) ($pagination['next_page'] ?? ($page + 1));
                if ($page > 1000) {
                    $errors[] = 'pagination safety cap hit at page 1000';
                    break;
                }
            }
            // Row-level errors are logged but don't fail the sync — only a fatal exception does.
            $status = 'success';
            $this->syncModel->finish($auditId, $status, $counters, empty($errors) ? null : implode("\n", array_slice($errors, 0, 50)));
        } catch (\Throwable $e) {
            $errors[] = 'fatal: ' . $e->getMessage();
            $this->syncModel->finish($auditId, 'failed', $counters, implode("\n", array_slice($errors, 0, 50)));
            log_message('error', 'CrmSync ' . $source . ' failed: ' . $e->getMessage());
            return array_merge($counters, ['audit_id' => $auditId, 'status' => 'failed', 'error' => $e->getMessage()]);
        }

        return array_merge($counters, ['audit_id' => $auditId, 'status' => $status]);
    }

    /**
     * Upsert one CRM row into subscribers. Idempotent and respects opt-out.
     *
     * @return string one of: 'created' | 'updated' | 'skipped_unsubscribed' | 'skipped_invalid'
     */
    protected function upsertFromCrm(array $row, string $sourceType): string
    {
        $email = strtolower(trim((string) ($row['email'] ?? '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'skipped_invalid';
        }
        // Defense in depth: CRM should have filtered, but guard anyway.
        if (!empty($row['newsletter_opt_out'])) {
            return 'skipped_invalid';
        }
        $crmId = isset($row['id']) ? mb_substr((string) $row['id'], 0, 64) : null;
        // CRM returns "nome" (PT) per the project convention; tolerate "name" (EN) too.
        $rawName = $row['nome'] ?? $row['name'] ?? null;
        $name = $rawName !== null ? mb_substr(trim((string) $rawName), 0, 255) : null;
        if ($name === '') $name = null;
        $now = date('Y-m-d H:i:s');

        // 1) Try to find by (source_type, crm_external_id) — survives email changes in CRM
        $existing = null;
        if (!empty($crmId)) {
            $existing = $this->db->table('subscribers')
                ->where('source_type', $sourceType)
                ->where('crm_external_id', $crmId)
                ->get()->getFirstRow();
        }
        // 2) Fallback: lookup by email
        if (!$existing) {
            $existing = $this->db->table('subscribers')
                ->where('email', $email)
                ->get()->getFirstRow();
        }

        if ($existing) {
            // Never reactivate someone who opted out.
            if ($existing->status === 'unsubscribed') {
                return 'skipped_unsubscribed';
            }
            $update = [
                'name'            => $name,
                'last_synced_at'  => $now,
                'crm_external_id' => $crmId ?: $existing->crm_external_id,
                'email'           => $email,
            ];
            // Only promote source_type if currently null/organic — never demote a client to a lead.
            if (empty($existing->source_type) || $existing->source_type === 'organic') {
                $update['source_type'] = $sourceType;
            } elseif ($existing->source_type === 'crm_lead' && $sourceType === 'crm_client') {
                $update['source_type'] = 'crm_client'; // lead promoted to client
            }
            $this->db->table('subscribers')->where('id', $existing->id)->update($update);
            return 'updated';
        }

        // 3) Create new — assign all active editorial lines so contact is nurtured across all themes.
        $lineIds = $this->getDefaultEditorialLineIds();
        $insert = [
            'email'              => $email,
            'name'               => $name,
            'token'              => generateToken(),
            'editorial_line_ids' => !empty($lineIds) ? json_encode(array_values($lineIds)) : null,
            'source_type'        => $sourceType,
            'crm_external_id'    => $crmId,
            'status'             => 'active',
            'last_synced_at'     => $now,
            'created_at'         => $now,
        ];
        $this->db->table('subscribers')->insert($insert);
        return 'created';
    }

    protected function getDefaultEditorialLineIds(): array
    {
        if ($this->defaultEditorialLineIds !== null) {
            return $this->defaultEditorialLineIds;
        }
        // active lines come from newsletter_editorial_lines where enabled=1
        $rows = $this->db->table('newsletter_editorial_lines')
            ->select('id')
            ->where('enabled', 1)
            ->get()->getResult();
        $this->defaultEditorialLineIds = array_map(fn($r) => (int) $r->id, $rows);
        return $this->defaultEditorialLineIds;
    }

    /**
     * Use the timestamp of the last SUCCESSFUL sync as the incremental cursor.
     * Subtract a small overlap window to absorb clock skew.
     */
    protected function getIncrementalCursor(string $source): ?string
    {
        $last = $this->syncModel->getLastSuccessful($source);
        if (!$last || empty($last->finished_at)) {
            return null; // first sync ever => full sync
        }
        // 5 minute overlap so we don't miss rows that changed during the previous run
        $cursor = (new \DateTimeImmutable($last->finished_at))->modify('-5 minutes');
        return $cursor->format('Y-m-d\TH:i:s\Z');
    }

    protected function fetchPage(string $endpoint, string $apiKey, string $anonKey, ?string $updatedSince, int $page, int $perPage): array
    {
        $query = ['page' => $page, 'per_page' => $perPage];
        if ($updatedSince) $query['updated_since'] = $updatedSince;
        $url = $endpoint . '?' . http_build_query($query);

        $timeout = max(5, (int) ($this->env('CRM_NEWSLETTER_TIMEOUT') ?: 30));
        $attempt = 0;
        $maxAttempts = 3;
        while (true) {
            $attempt++;
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'x-api-key: ' . $apiKey,
                    'apikey: ' . $anonKey,
                    'Authorization: Bearer ' . $anonKey,
                ],
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 8,
            ]);
            $body = curl_exec($ch);
            $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($body === false) {
                if ($attempt < $maxAttempts) { sleep((int) pow(2, $attempt)); continue; }
                throw new \RuntimeException('CRM transport error: ' . $curlErr);
            }
            if ($http === 429 || $http >= 500) {
                if ($attempt < $maxAttempts) { sleep((int) pow(2, $attempt)); continue; }
                throw new \RuntimeException('CRM responded HTTP ' . $http . ' after ' . $attempt . ' attempts. Body: ' . mb_substr((string) $body, 0, 300));
            }
            if ($http < 200 || $http >= 300) {
                throw new \RuntimeException('CRM responded HTTP ' . $http . '. Body: ' . mb_substr((string) $body, 0, 300));
            }
            $decoded = json_decode($body, true);
            if (!is_array($decoded) || !isset($decoded['data'])) {
                throw new \RuntimeException('CRM returned malformed JSON. Body: ' . mb_substr((string) $body, 0, 300));
            }
            return $decoded;
        }
    }

    protected function getEndpoint(string $source): string
    {
        return $source === 'leads'
            ? $this->env('CRM_NEWSLETTER_LEADS_ENDPOINT')
            : $this->env('CRM_NEWSLETTER_CLIENTS_ENDPOINT');
    }

    protected function env(string $key): string
    {
        $v = getenv($key);
        return is_string($v) ? trim($v) : '';
    }
}
