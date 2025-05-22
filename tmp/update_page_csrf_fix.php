<?php
/**
 * Script para atualizar a página do simulador no banco de dados
 * Este script atualiza o conteúdo HTML do simulador na tabela pages
 */

// Carregar configurações do banco de dados manualmente
// Caminho absoluto é necessário
require_once '/www/wwwroot/gx.capital/app/Config/Database.php';
$config = new \Config\Database();
$dbConfig = $config->default;

try {
    // Conectar ao banco de dados
    $dsn = "mysql:host={$dbConfig['hostname']};dbname={$dbConfig['database']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "Conectado ao banco de dados com sucesso.\n";
    
    // ID da página do simulador
    $pageId = 12;
    
    // Verificar se a página existe
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = :id");
    $stmt->execute(['id' => $pageId]);
    $page = $stmt->fetch();
    
    if (!$page) {
        throw new Exception("Página com ID {$pageId} não encontrada.");
    }
    
    echo "Página encontrada: {$page['title']}.\n";
    
    // Carregar o conteúdo atualizado do simulador
    $htmlContent = file_get_contents(__DIR__ . '/simulator_content_updated.html');
    
    if (!$htmlContent) {
        throw new Exception("Erro ao carregar o arquivo HTML do simulador.");
    }
    
    echo "Conteúdo HTML carregado com sucesso.\n";
    
    // Atualizar o conteúdo da página
    $stmt = $pdo->prepare("UPDATE pages SET page_content = :content, updated_at = NOW() WHERE id = :id");
    $success = $stmt->execute([
        'content' => $htmlContent,
        'id' => $pageId
    ]);
    
    if ($success) {
        echo "Página atualizada com sucesso!\n";
    } else {
        throw new Exception("Erro ao atualizar a página.");
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}