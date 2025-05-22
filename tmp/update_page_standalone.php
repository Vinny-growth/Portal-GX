<?php
/**
 * Script standalone para atualizar a página do simulador no banco de dados
 * Este script não depende do framework CodeIgniter
 */

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
    
    echo "Página encontrada: " . ($page['title'] ?? 'Sem título') . "\n";
    
    // Carregar o conteúdo atualizado do simulador
    $htmlContent = file_get_contents(__DIR__ . '/simulator_content_updated.html');
    
    if (!$htmlContent) {
        throw new Exception("Erro ao carregar o arquivo HTML do simulador.");
    }
    
    echo "Conteúdo HTML carregado com sucesso.\n";
    
    // Atualizar o conteúdo da página
    $stmt = $pdo->prepare("UPDATE pages SET page_content = :content WHERE id = :id");
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