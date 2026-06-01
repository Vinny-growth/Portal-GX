<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'result' => 0,
        'message' => 'Método não permitido',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function loadEnvMap(): array
{
    static $vars = null;

    if ($vars !== null) {
        return $vars;
    }

    $vars = [];
    $envFile = dirname(__DIR__) . '/.env';
    if (!is_file($envFile)) {
        return $vars;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }
        if (strncmp($line, 'export ', 7) === 0) {
            $line = substr($line, 7);
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === '\'' && substr($value, -1) === '\''))) {
            $value = substr($value, 1, -1);
        }
        $vars[$key] = $value;
    }

    return $vars;
}

function envValue(string $key, string $default = ''): string
{
    $value = getenv($key);
    if ($value === false || $value === null || $value === '') {
        $env = loadEnvMap();
        $value = $env[$key] ?? $default;
    }

    return is_string($value) ? trim($value) : $default;
}

function postValue(string $key): ?string
{
    if (!array_key_exists($key, $_POST)) {
        return null;
    }

    $value = trim((string) $_POST[$key]);
    return $value === '' ? null : $value;
}

function referrer(): ?string
{
    $value = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));
    return $value === '' ? null : $value;
}

function referrerQueryValue(string $key): ?string
{
    $ref = referrer();
    if ($ref === null) {
        return null;
    }

    $query = (string) parse_url($ref, PHP_URL_QUERY);
    if ($query === '') {
        return null;
    }

    parse_str($query, $params);
    $value = trim((string) ($params[$key] ?? ''));
    return $value === '' ? null : $value;
}

function inferLandingPage(): ?string
{
    $landingPage = postValue('landing_page');
    if ($landingPage !== null) {
        return $landingPage;
    }

    return referrer();
}

function inferOrigin(?string $landingPage): string
{
    $origin = postValue('origem') ?? postValue('origin') ?? envValue('CRM_LEAD_ORIGIN');
    if ($origin !== '') {
        return $origin;
    }

    if (!empty($landingPage)) {
        $path = (string) parse_url($landingPage, PHP_URL_PATH);
        return 'Site GX Capital - ' . ($path !== '' ? $path : '/');
    }

    $ref = referrer();
    if (!empty($ref)) {
        $path = (string) parse_url($ref, PHP_URL_PATH);
        return 'Site GX Capital - ' . ($path !== '' ? $path : '/');
    }

    return 'Site GX Capital - legado simuladores';
}

function findExistingLead(PDO $pdo, string $email, string $phone, int $dedupMinutes): ?array
{
    $conditions = [];
    $params = [
        ':cutoff' => date('Y-m-d H:i:s', time() - ($dedupMinutes * 60)),
    ];

    if ($email !== '') {
        $conditions[] = 'email = :email';
        $params[':email'] = $email;
    }
    if ($phone !== '') {
        $conditions[] = 'phone = :phone';
        $params[':phone'] = $phone;
    }

    if ($conditions === []) {
        return null;
    }

    $sql = 'SELECT * FROM sim_leads WHERE (' . implode(' OR ', $conditions) . ') AND created_at >= :cutoff ORDER BY id DESC LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    return $lead !== false ? $lead : null;
}

function persistLead(PDO $pdo, array $data, int $dedupMinutes): int
{
    $existing = findExistingLead($pdo, $data['email'], $data['phone'], $dedupMinutes);
    if ($existing !== null) {
        $fields = [
            'updated_at = :updated_at',
        ];
        $params = [
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $existing['id'],
        ];

        foreach (['name', 'email', 'phone', 'sim_data', 'observations'] as $field) {
            if ($data[$field] !== null && $data[$field] !== '') {
                $fields[] = $field . ' = :' . $field;
                $params[':' . $field] = $data[$field];
            }
        }

        $sql = 'UPDATE sim_leads SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $existing['id'];
    }

    $stmt = $pdo->prepare(
        'INSERT INTO sim_leads (name, email, phone, sim_data, observations, status, created_at) VALUES (:name, :email, :phone, :sim_data, :observations, :status, :created_at)'
    );
    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':phone' => $data['phone'],
        ':sim_data' => $data['sim_data'],
        ':observations' => $data['observations'],
        ':status' => 'new',
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    return (int) $pdo->lastInsertId();
}

function sendLeadToCrm(array $payload): bool
{
    $endpoint = envValue('CRM_LEAD_ENDPOINT');
    $apiKey = envValue('CRM_LEAD_API_KEY');
    if ($endpoint === '' || $apiKey === '') {
        return false;
    }

    $timeout = (int) envValue('CRM_LEAD_TIMEOUT', '10');
    if ($timeout < 3) {
        $timeout = 3;
    }

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log('CRM lead capture error: ' . $curlErr);
        return false;
    }

    $decoded = json_decode($response, true);
    if ($httpCode >= 200 && $httpCode < 300) {
        if (!is_array($decoded)) {
            return true;
        }
        if (array_key_exists('success', $decoded)) {
            return (bool) $decoded['success'];
        }
        if (array_key_exists('ok', $decoded)) {
            return (bool) $decoded['ok'];
        }
        if (!empty($decoded['error'])) {
            error_log('CRM lead capture failed: ' . $response);
            return false;
        }
        return true;
    }

    error_log('CRM lead capture failed: HTTP ' . $httpCode . ' Response: ' . $response);
    return false;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$simData = postValue('sim_data');
$observations = postValue('observations');
$company = postValue('company');
$landingPage = inferLandingPage();
$referrer = referrer();
$utmSource = postValue('utm_source') ?? referrerQueryValue('utm_source');
$utmMedium = postValue('utm_medium') ?? referrerQueryValue('utm_medium');
$utmCampaign = postValue('utm_campaign') ?? referrerQueryValue('utm_campaign');
$utmTerm = postValue('utm_term') ?? referrerQueryValue('utm_term');
$utmContent = postValue('utm_content') ?? referrerQueryValue('utm_content');
$origin = inferOrigin($landingPage);

if ($name === '' || $email === '' || $phone === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'result' => 0,
        'message' => 'Os campos nome, email e telefone são obrigatórios',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'result' => 0,
        'message' => 'O email informado é inválido',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            envValue('database.default.hostname', 'localhost'),
            envValue('database.default.database')
        ),
        envValue('database.default.username'),
        envValue('database.default.password'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ]
    );

    $externalId = persistLead($pdo, [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'sim_data' => $simData,
        'observations' => $observations,
    ], max(1, (int) envValue('LEAD_DEDUP_MINUTES', '60')));

    $crmSynced = sendLeadToCrm([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'observations' => $observations,
        'origem' => $origin,
        'utm_source' => $utmSource,
        'utm_medium' => $utmMedium,
        'utm_campaign' => $utmCampaign,
        'utm_term' => $utmTerm,
        'utm_content' => $utmContent,
        'referrer' => $referrer,
        'landing_page' => $landingPage,
        'external_id' => $externalId,
        'source_system' => postValue('source_system') ?? envValue('CRM_LEAD_SOURCE_SYSTEM', 'site-gx-php'),
        'status' => envValue('CRM_LEAD_STATUS'),
        'assigned_to' => envValue('CRM_LEAD_ASSIGNED_TO'),
    ]);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'result' => 1,
        'message' => 'Lead salvo com sucesso',
        'lead_id' => $externalId,
        'crm_synced' => $crmSynced,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    error_log('tmp/api_direct.php failed: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'result' => 0,
        'message' => 'Erro interno do servidor: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
