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
    
    // Inserir um lead diretamente no banco de dados
    $insertData = [
        'name' => 'Lead Direto PHP',
        'email' => 'lead.direto@exemplo.com',
        'phone' => '11988887777',
        'observations' => 'Lead inserido diretamente por PHP',
        'status' => 'new',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $stmt = $pdo->prepare("INSERT INTO sim_leads (name, email, phone, observations, status, created_at) VALUES (:name, :email, :phone, :observations, :status, :created_at)");
    $success = $stmt->execute($insertData);
    
    if ($success) {
        $id = $pdo->lastInsertId();
        echo "Lead inserido com sucesso! ID: " . $id . "\n";
    } else {
        echo "Erro ao inserir o lead!\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}