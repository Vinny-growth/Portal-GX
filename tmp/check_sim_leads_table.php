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
    
    // Verificar se a tabela sim_leads existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'sim_leads'");
    if ($stmt->rowCount() === 0) {
        echo "A tabela 'sim_leads' não existe. Criando...\n";
        
        // Criar tabela
        $sql = "CREATE TABLE `sim_leads` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `phone` varchar(100) NOT NULL,
            `sim_data` text DEFAULT NULL,
            `observations` text DEFAULT NULL,
            `status` varchar(20) DEFAULT 'new',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $pdo->exec($sql);
        echo "Tabela 'sim_leads' criada com sucesso!\n";
    } else {
        echo "Tabela 'sim_leads' já existe.\n";
        
        // Obter estrutura da tabela
        $stmt = $pdo->query("DESCRIBE sim_leads");
        $columns = $stmt->fetchAll();
        
        echo "Estrutura da tabela 'sim_leads':\n";
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']}" . 
                 ($column['Null'] === 'NO' ? ' NOT NULL' : '') . 
                 (isset($column['Default']) ? " DEFAULT {$column['Default']}" : '') . 
                 "\n";
        }
        
        // Verificar se o campo observations existe
        $hasObservationsColumn = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'observations') {
                $hasObservationsColumn = true;
                break;
            }
        }
        
        // Adicionar o campo observations se não existir
        if (!$hasObservationsColumn) {
            echo "Adicionando campo 'observations' à tabela...\n";
            $pdo->exec("ALTER TABLE sim_leads ADD COLUMN observations text DEFAULT NULL AFTER sim_data");
            echo "Campo 'observations' adicionado com sucesso!\n";
        }
    }
    
    // Contar quantos registros existem
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sim_leads");
    $count = $stmt->fetch();
    echo "Total de registros na tabela: {$count['total']}\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}