<?php
// Script para criar o endpoint diretamente, evitando o sistema de rotas
require_once '../app/Models/SimLeadModel.php';

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

// Obter dados da requisição
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['phone'] ?? null;
$simData = $_POST['sim_data'] ?? null;
$observations = $_POST['observations'] ?? null;

// Validação básica
if (empty($name) || empty($email) || empty($phone)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Os campos nome, email e telefone são obrigatórios'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'O email informado é inválido'
    ]);
    exit;
}

try {
    // Criar modelo
    $model = new \App\Models\SimLeadModel();
    
    // Preparar dados
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'sim_data' => $simData,
        'observations' => $observations
    ];
    
    // Salvar no banco
    $result = $model->addSimLead($data);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Lead salvo com sucesso'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao salvar o lead no banco de dados'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}