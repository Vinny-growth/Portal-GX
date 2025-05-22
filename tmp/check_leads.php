<?php
// Configurações do banco de dados
$dbHost = 'localhost';
$dbName = 'portal';
$dbUser = 'Vinny';
$dbPass = 'Mariah@2021filha';

try {
    // Conectar ao banco de dados
    $dsn = "mysql:host={$dbHost};dbname={$dbName}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "Conectado ao banco de dados com sucesso.\n";
    
    // Verificar se a tabela sim_leads existe
    $stmt = $pdo->query("SELECT * FROM sim_leads ORDER BY id DESC LIMIT 5");
    $leads = $stmt->fetchAll();
    
    echo "Últimos 5 leads cadastrados:\n";
    foreach ($leads as $lead) {
        echo "ID: " . $lead['id'] . "\n";
        echo "Nome: " . $lead['name'] . "\n";
        echo "Email: " . $lead['email'] . "\n";
        echo "Telefone: " . $lead['phone'] . "\n";
        echo "Observações: " . ($lead['observations'] ?? 'N/A') . "\n";
        echo "Status: " . $lead['status'] . "\n";
        echo "Data: " . $lead['created_at'] . "\n";
        echo "------------------------\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}