<?php namespace App\Models;

/**
 * Controle persistente por post popular: quantas vezes já foi DERIVADO,
 * quando foi a última vez, e se está na blocklist (manual ou auto por cap).
 * Alimenta a lógica anti-repetição/anti-canibalização do planner de populares
 * e a tela de blocklist do admin.
 */
class PopularPostsControlModel extends BaseModel
{
    protected $table = 'popular_posts_control';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'post_id', 'derived_count', 'last_derived_at',
        'blocked', 'blocked_reason', 'blocked_at', 'title',
        'created_at', 'updated_at',
    ];

    /** Registra 1 derivação do post: incrementa contador, marca data e auto-bloqueia ao atingir o cap. */
    public function recordDerivation(int $postId, string $title, string $datetime, int $maxDerivations): void
    {
        if ($postId <= 0) {
            return;
        }
        $row = $this->builder()->where('post_id', $postId)->get()->getFirstRow();
        if (empty($row)) {
            $count = 1;
            $blocked = ($maxDerivations > 0 && $count >= $maxDerivations) ? 1 : 0;
            $this->builder()->insert([
                'post_id'         => $postId,
                'derived_count'   => $count,
                'last_derived_at' => $datetime,
                'blocked'         => $blocked,
                'blocked_reason'  => $blocked ? 'auto_cap' : null,
                'blocked_at'      => $blocked ? $datetime : null,
                'title'           => mb_substr($title, 0, 500),
                'created_at'      => $datetime,
                'updated_at'      => $datetime,
            ]);
            return;
        }
        $count = (int) $row->derived_count + 1;
        $patch = [
            'derived_count'   => $count,
            'last_derived_at' => $datetime,
            'title'           => mb_substr($title, 0, 500),
            'updated_at'      => $datetime,
        ];
        // auto-block ao atingir o cap (não desbloqueia quem já foi liberado manualmente à força — apenas eleva)
        if ($maxDerivations > 0 && $count >= $maxDerivations && (int) $row->blocked === 0) {
            $patch['blocked'] = 1;
            $patch['blocked_reason'] = 'auto_cap';
            $patch['blocked_at'] = $datetime;
        }
        $this->builder()->where('post_id', $postId)->update($patch);
    }

    public function block(int $postId, string $reason = 'manual', ?string $title = null): bool
    {
        if ($postId <= 0) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        $row = $this->builder()->where('post_id', $postId)->get()->getFirstRow();
        if (empty($row)) {
            return (bool) $this->builder()->insert([
                'post_id'        => $postId,
                'derived_count'  => 0,
                'blocked'        => 1,
                'blocked_reason' => $reason,
                'blocked_at'     => $now,
                'title'          => $title !== null ? mb_substr($title, 0, 500) : null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
        return (bool) $this->builder()->where('post_id', $postId)->update([
            'blocked'        => 1,
            'blocked_reason' => $reason,
            'blocked_at'     => $now,
            'updated_at'     => $now,
        ]);
    }

    public function unblock(int $postId): bool
    {
        return (bool) $this->builder()->where('post_id', $postId)->update([
            'blocked'        => 0,
            'blocked_reason' => null,
            'blocked_at'     => null,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /** post_ids que estão fora dos populares (blocklist manual ou auto). */
    public function getBlockedIds(): array
    {
        $rows = $this->builder()->select('post_id')->where('blocked', 1)->get()->getResult();
        return array_map(fn($r) => (int) $r->post_id, $rows);
    }

    /** post_ids derivados dentro da janela de cooldown (não devem ser re-derivados ainda). */
    public function getCooldownIds(int $cooldownDays): array
    {
        if ($cooldownDays <= 0) {
            return [];
        }
        $cutoff = date('Y-m-d H:i:s', time() - $cooldownDays * 86400);
        $rows = $this->builder()->select('post_id')
            ->where('last_derived_at >=', $cutoff)
            ->get()->getResult();
        return array_map(fn($r) => (int) $r->post_id, $rows);
    }

    /** Mapa post_id => row (para enriquecer candidatos com o contador). */
    public function getControlMap(array $postIds): array
    {
        $postIds = array_values(array_filter(array_map('intval', $postIds)));
        if (empty($postIds)) {
            return [];
        }
        $rows = $this->builder()->whereIn('post_id', $postIds)->get()->getResult();
        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r->post_id] = $r;
        }
        return $map;
    }

    /** Lista para a tela de blocklist do admin (mais derivados / bloqueados primeiro). */
    public function getForAdmin(int $limit = 100): array
    {
        return $this->builder()
            ->orderBy('blocked', 'DESC')
            ->orderBy('derived_count', 'DESC')
            ->orderBy('last_derived_at', 'DESC')
            ->limit($limit)
            ->get()->getResult();
    }
}
