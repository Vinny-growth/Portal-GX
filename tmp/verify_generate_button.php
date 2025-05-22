<?php
// Script para verificar o botão de geração do simulador

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
    
    // Procurar pelo botão de gerar simulação
    echo "Procurando pelo botão de gerar simulação...\n";
    
    // Procurar botões
    preg_match_all('/<button[^>]*id=["\']([^"\']*)["\'][^>]*>.*?<\/button>/s', $content, $buttons);
    
    if (!empty($buttons[0])) {
        echo "Botões encontrados:\n";
        foreach ($buttons[0] as $index => $button) {
            $id = $buttons[1][$index];
            echo "ID: $id\n";
            echo "HTML: " . substr($button, 0, 100) . "...\n\n";
        }
    } else {
        echo "Nenhum botão com ID encontrado.\n";
    }
    
    // Procurar pelo botão de gerar sem ID específico
    preg_match_all('/<button[^>]*>.*?gerar.*?<\/button>/si', $content, $generateButtons);
    
    if (!empty($generateButtons[0])) {
        echo "Botões com 'gerar' encontrados:\n";
        foreach ($generateButtons[0] as $button) {
            echo substr($button, 0, 100) . "...\n\n";
        }
    } else {
        echo "Nenhum botão com 'gerar' encontrado.\n";
    }
    
    // Procurar pela função JavaScript que gera a simulação
    echo "Procurando pela função JavaScript que gera a simulação...\n";
    
    // Padrões comuns para funções de geração
    $patterns = [
        'gerarSimulacao', 
        'gerar', 
        'simular', 
        'calcular', 
        'generateResults', 
        'calculate',
        'executarSimulacao'
    ];
    
    foreach ($patterns as $pattern) {
        $pos = stripos($content, $pattern);
        if ($pos !== false) {
            // Tentar extrair a função inteira
            $start = strrpos(substr($content, 0, $pos), 'function');
            if ($start !== false) {
                $start = $pos - ($pos - $start);
                // Procurar pelo fechamento da função
                $bracketCount = 0;
                $inFunction = false;
                $functionBody = '';
                
                for ($i = $start; $i < min($start + 1000, strlen($content)); $i++) {
                    $char = $content[$i];
                    $functionBody .= $char;
                    
                    if ($char === '{') {
                        $bracketCount++;
                        $inFunction = true;
                    } elseif ($char === '}') {
                        $bracketCount--;
                        if ($inFunction && $bracketCount === 0) {
                            break;
                        }
                    }
                }
                
                echo "Possível função encontrada para '$pattern':\n";
                echo substr($functionBody, 0, 500) . "...\n\n";
            } else {
                echo "Padrão '$pattern' encontrado, mas não consegui extrair a função completa.\n";
                echo "Contexto: " . substr($content, max(0, $pos - 50), 100) . "\n\n";
            }
        }
    }
    
    // Procurar elementos escondidos que poderiam conter o formulário
    echo "Procurando elementos escondidos que poderiam conter o formulário...\n";
    
    preg_match_all('/<div[^>]*class=["\'][^"\']*hidden[^"\']*["\'][^>]*>/i', $content, $hiddenElements);
    
    if (!empty($hiddenElements[0])) {
        echo "Elementos escondidos encontrados:\n";
        foreach ($hiddenElements[0] as $element) {
            echo $element . "\n";
        }
    } else {
        echo "Nenhum elemento escondido encontrado com a classe 'hidden'.\n";
    }
    
    // Procurar por código modal
    echo "\nProcurando por código de modal...\n";
    
    if (stripos($content, 'modal') !== false) {
        echo "Código de modal encontrado.\n";
        
        // Procurar funções relacionadas a modal
        preg_match_all('/function\s+(\w+Modal|\w+[Ss]how\w*|\w*[Oo]pen\w*)\s*\([^\)]*\)\s*\{/i', $content, $modalFunctions);
        
        if (!empty($modalFunctions[0])) {
            echo "Funções relacionadas a modal encontradas:\n";
            foreach ($modalFunctions[0] as $index => $func) {
                echo $modalFunctions[1][$index] . "\n";
            }
        } else {
            echo "Nenhuma função relacionada a modal encontrada.\n";
        }
    } else {
        echo "Nenhum código de modal encontrado.\n";
    }
    
} catch (PDOException $e) {
    echo "Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}