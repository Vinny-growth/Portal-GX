<?php
// Script para inserir leads diretamente no banco de dados, sem depender do framework

// Configurar cabeçalhos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Verificar método
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

// Configurações do banco de dados (mesmo do config/Database.php)
$dbHost = 'localhost';
$dbName = 'portal';
$dbUser = 'Vinny';
$dbPass = 'Mariah@2021filha';

// Obter dados da requisição
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['phone'] ?? null;
$simData = $_POST['sim_data'] ?? null;
$observations = $_POST['observations'] ?? null;

// Log para debug - desativado para evitar problemas de permissão
// file_put_contents(__DIR__ . '/api_log.txt', date('Y-m-d H:i:s') . " - Requisição recebida\n", FILE_APPEND);
// file_put_contents(__DIR__ . '/api_log.txt', "Dados: " . json_encode($_POST) . "\n", FILE_APPEND);

// Validação básica
if (empty($name) || empty($email) || empty($phone)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Os campos nome, email e telefone são obrigatórios'
    ]);
    // file_put_contents(__DIR__ . '/api_log.txt', "Erro: Campos obrigatórios não preenchidos\n", FILE_APPEND);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'O email informado é inválido'
    ]);
    // file_put_contents(__DIR__ . '/api_log.txt', "Erro: Email inválido - $email\n", FILE_APPEND);
    exit;
}

try {
    // Conectar ao banco de dados
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    
    // Preparar dados para inserção
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'sim_data' => $simData,
        'observations' => $observations,
        'status' => 'new',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Inserir no banco de dados
    $sql = "INSERT INTO sim_leads (name, email, phone, sim_data, observations, status, created_at) 
            VALUES (:name, :email, :phone, :sim_data, :observations, :status, :created_at)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);
    
    if ($result) {
        $id = $pdo->lastInsertId();
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Lead salvo com sucesso',
            'lead_id' => $id
        ]);
        // file_put_contents(__DIR__ . '/api_log.txt', "Sucesso: Lead salvo com ID $id\n", FILE_APPEND);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao salvar o lead no banco de dados'
        ]);
        // file_put_contents(__DIR__ . '/api_log.txt', "Erro: Falha ao executar a inserção\n", FILE_APPEND);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro de banco de dados: ' . $e->getMessage()
    ]);
    // file_put_contents(__DIR__ . '/api_log.txt', "Erro PDO: " . $e->getMessage() . "\n", FILE_APPEND);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
    file_put_contents(__DIR__ . '/api_log.txt', "Erro: " . $e->getMessage() . "\n", FILE_APPEND);
}