<?php
// Script para atualizar o simulador para mostrar o formulário após gerar a simulação

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
    
    // Modificar o HTML - Precisamos criar uma nova função para mostrar o modal do formulário
    $showFormModalFunction = '
    // Função para mostrar o modal de formulário após a simulação
    function showLeadFormModal() {
        document.getElementById("leadModalOverlay").classList.remove("hidden");
    }
    
    // Função para fechar o modal de formulário
    function closeLeadFormModal() {
        document.getElementById("leadModalOverlay").classList.add("hidden");
    }
    ';
    
    // Adicionar a função ao final do último script
    $scriptPos = strrpos($content, '</script>');
    if ($scriptPos !== false) {
        $content = substr($content, 0, $scriptPos) . $showFormModalFunction . "\n" . substr($content, $scriptPos);
    } else {
        // Se não encontrar uma tag script, adicionar no final do body
        $bodyEndPos = strrpos($content, '</body>');
        if ($bodyEndPos !== false) {
            $content = substr($content, 0, $bodyEndPos) . "\n<script>\n" . $showFormModalFunction . "\n</script>\n" . substr($content, $bodyEndPos);
        }
    }
    
    // Criar o HTML do modal com o formulário
    $modalHTML = '
    <!-- Modal para captura de leads -->
    <div id="leadModalOverlay" class="modal-overlay hidden">
        <div class="modal-content bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Receba sua análise completa</h3>
                <button onclick="closeLeadFormModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="mb-4 text-gray-600">Enviaremos uma análise detalhada baseada na sua simulação.</p>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="form-name">
                    Nome Completo *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form-name" type="text" placeholder="Seu nome" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="form-email">
                    Email *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form-email" type="email" placeholder="seu.email@empresa.com" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="form-phone">
                    Telefone *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form-phone" type="tel" placeholder="(00) 00000-0000" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="form-observations">
                    Observações
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form-observations" rows="3" placeholder="Compartilhe mais detalhes sobre sua necessidade"></textarea>
            </div>
            
            <div id="form-result" class="mb-4"></div>
            
            <div class="flex items-center justify-between">
                <button id="submit-button" onclick="submitForm()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Enviar Simulação
                </button>
            </div>
        </div>
    </div>';
    
    // Adicionar o modal antes do fechamento do body
    $bodyEndPos = strrpos($content, '</body>');
    if ($bodyEndPos !== false) {
        $content = substr($content, 0, $bodyEndPos) . $modalHTML . "\n" . substr($content, $bodyEndPos);
    } else {
        $content .= $modalHTML;
    }
    
    // Agora vamos modificar o botão "Simular Cenários" ou equivalente para mostrar o modal após a simulação
    // Primeiro, tentamos encontrar a função que exibe os resultados
    
    // Localizar o botão de simulação
    if (preg_match('/<button[^>]*>.*?[Ss]imular.*?<\/button>/s', $content, $simulateButton)) {
        echo "Botão de simulação encontrado: " . $simulateButton[0] . "\n";
        
        // Precisamos encontrar o evento onclick ou similar
        // Vamos procurar no HTML ou no JavaScript
        
        // Opção 1: Modificar o botão diretamente para chamar nossa função
        $newButton = str_replace('</button>', ' onclick="setTimeout(showLeadFormModal, 1000)"</button>', $simulateButton[0]);
        $content = str_replace($simulateButton[0], $newButton, $content);
        
        echo "Botão modificado para chamar showLeadFormModal após 1 segundo\n";
    }
    
    // Opção 2: Encontrar a função que exibe os resultados e adicionar a chamada para showLeadFormModal
    if (preg_match('/function\s+(displayResults|showResults|updateResults|renderResults|displayScenarios)[^{]*\{/i', $content, $displayFunction)) {
        $functionName = preg_replace('/function\s+|\s*\{.*$/', '', $displayFunction[0]);
        echo "Função de exibição de resultados encontrada: $functionName\n";
        
        // Localizar o final da função
        $startPos = strpos($content, $displayFunction[0]);
        if ($startPos !== false) {
            $bracketCount = 0;
            $inFunction = false;
            $endPos = $startPos;
            
            for ($i = $startPos; $i < strlen($content); $i++) {
                $char = $content[$i];
                
                if ($char === '{') {
                    $bracketCount++;
                    $inFunction = true;
                } elseif ($char === '}') {
                    $bracketCount--;
                    if ($inFunction && $bracketCount === 0) {
                        $endPos = $i;
                        break;
                    }
                }
            }
            
            if ($endPos > $startPos) {
                // Adicionar a chamada para showLeadFormModal antes do fechamento da função
                $functionBody = substr($content, $startPos, $endPos - $startPos + 1);
                $modifiedFunction = substr($functionBody, 0, -1) . "\n    // Mostrar modal de captura de leads\n    setTimeout(showLeadFormModal, 1000);\n}";
                $content = str_replace($functionBody, $modifiedFunction, $content);
                
                echo "Função $functionName modificada para chamar showLeadFormModal após exibir resultados\n";
            }
        }
    }
    
    // Verificar se há o botão "Simular Cenários" que faz um submit
    if (preg_match('/<button[^>]*type=["\']submit["\'][^>]*>.*?[Ss]imular.*?<\/button>/s', $content, $submitButton)) {
        echo "Botão de submit para simulação encontrado\n";
        
        // Vamos procurar o formulário e adicionar um evento onsubmit
        if (preg_match('/<form[^>]*>.*?<button[^>]*type=["\']submit["\'][^>]*>.*?[Ss]imular.*?<\/button>.*?<\/form>/s', $content, $formMatch)) {
            $originalForm = $formMatch[0];
            $modifiedForm = str_replace('<form', '<form onsubmit="setTimeout(showLeadFormModal, 1000);"', $originalForm);
            $content = str_replace($originalForm, $modifiedForm, $content);
            
            echo "Formulário modificado para chamar showLeadFormModal após o submit\n";
        }
    }
    
    // Atualizar a página no banco de dados
    $updateSql = "UPDATE pages SET page_content = :content WHERE id = 10";
    $updateStmt = $pdo->prepare($updateSql);
    $result = $updateStmt->execute(['content' => $content]);
    
    if ($result) {
        echo "Página do simulador atualizada com sucesso!\n";
    } else {
        echo "Erro ao atualizar a página do simulador.\n";
    }
    
} catch (PDOException $e) {
    echo "Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}