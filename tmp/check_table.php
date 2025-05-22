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
    
    // Obter estrutura da tabela pages
    $stmt = $pdo->query("DESCRIBE pages");
    $columns = $stmt->fetchAll();
    
    echo "Estrutura da tabela 'pages':\n";
    foreach ($columns as $column) {
        echo "{$column['Field']} - {$column['Type']}" . 
             ($column['Null'] === 'NO' ? ' NOT NULL' : '') . 
             (isset($column['Default']) ? " DEFAULT {$column['Default']}" : '') . 
             "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}