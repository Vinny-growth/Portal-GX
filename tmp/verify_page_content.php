<?php
// Script para verificar o conteúdo da página do simulador de risco cambial

// Configurações do banco de dados
$dbHost = 'localhost';
$dbName = 'portal';
$dbUser = 'Vinny';
$dbPass = 'Mariah@2021filha';

try {
    // Conectar ao banco de dados
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    
    // Recuperar conteúdo atual
    $sql = "SELECT page_content FROM pages WHERE id = 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $content = $stmt->fetchColumn();
    
    if (!$content) {
        die("Não foi possível encontrar o conteúdo da página com ID 10.\n");
    }
    
    // Verificar se existe um formulário
    $hasForm = preg_match('/<form[^>]*>.*?<\/form>/s', $content);
    echo "Tem formulário no HTML? " . ($hasForm ? "SIM" : "NÃO") . "\n\n";
    
    // Verificar se há um botão com ID submit-button
    $hasSubmitButton = preg_match('/id=["\']submit-button["\']/', $content);
    echo "Tem botão de envio (submit-button)? " . ($hasSubmitButton ? "SIM" : "NÃO") . "\n\n";
    
    // Verificar se há um container que deveria conter o formulário
    $hasFormContainer = preg_match('/<div[^>]*class=["\'][^"\']*form-container[^"\']*["\'][^>]*>/', $content);
    echo "Tem container de formulário? " . ($hasFormContainer ? "SIM" : "NÃO") . "\n\n";
    
    // Vamos tentar localizar algum elemento que possa servir como container para o formulário
    echo "Procurando possíveis containers para o formulário...\n";
    
    // Procurar divs que possam servir como containers
    preg_match_all('/<div[^>]*class=["\']([^"\']*)["\'][^>]*>/', $content, $divClasses);
    
    if (!empty($divClasses[1])) {
        $uniqueClasses = array_unique($divClasses[1]);
        echo "Classes de div encontradas:\n";
        foreach ($uniqueClasses as $class) {
            echo "- " . $class . "\n";
        }
    } else {
        echo "Nenhuma div com classes encontrada.\n";
    }
    
    // Verificar a estrutura geral da página
    echo "\nEstrutura da página:\n";
    $hasHtml = strpos($content, '<html') !== false;
    $hasHead = strpos($content, '<head') !== false;
    $hasBody = strpos($content, '<body') !== false;
    
    echo "Tem tag HTML? " . ($hasHtml ? "SIM" : "NÃO") . "\n";
    echo "Tem tag HEAD? " . ($hasHead ? "SIM" : "NÃO") . "\n";
    echo "Tem tag BODY? " . ($hasBody ? "SIM" : "NÃO") . "\n";
    
    // Verificar o tamanho do conteúdo
    echo "\nTamanho do conteúdo: " . strlen($content) . " caracteres\n";
    
    // Vamos mostrar uma prévia do conteúdo
    echo "\nPrévia do conteúdo (primeiros 500 caracteres):\n";
    echo substr($content, 0, 500) . "...\n";
    
    // Vamos verificar se há algum contêiner com "simulator" ou "simulador" no nome da classe
    $hasSimulatorContainer = preg_match('/<div[^>]*class=["\'][^"\']*simul[^"\']*["\'][^>]*>/', $content);
    echo "\nTem container relacionado ao simulador? " . ($hasSimulatorContainer ? "SIM" : "NÃO") . "\n";
    
    if ($hasSimulatorContainer) {
        preg_match_all('/<div[^>]*class=["\'][^"\']*simul[^"\']*["\'][^>]*>/', $content, $simulatorContainers);
        echo "Containers do simulador encontrados: " . count($simulatorContainers[0]) . "\n";
    }
    
} catch (PDOException $e) {
    echo "Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}