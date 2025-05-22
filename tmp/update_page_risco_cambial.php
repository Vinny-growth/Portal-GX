<?php
// Script para atualizar a página do simulador de risco cambial

// Configurações do banco de dados (mesmo do config/Database.php)
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
    
    // Substituição do código de envio do formulário para usar o novo endpoint
    $replacement = '
    function submitLead(name, email, phone, observations = null, simData = null) {
        const formData = new FormData();
        
        formData.append("name", name);
        formData.append("email", email);
        formData.append("phone", phone);
        
        if (observations) {
            formData.append("observations", observations);
        }
        
        if (simData) {
            formData.append("sim_data", JSON.stringify(simData));
        }
        
        return fetch("/tmp/api_direct.php", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error("Erro ao enviar lead:", error);
            return { status: "error", message: "Erro ao processar solicitação" };
        });
    }
    
    function submitForm() {
        // Obter dados do formulário
        const name = $("#form-name").val();
        const email = $("#form-email").val();
        const phone = $("#form-phone").val();
        const observations = $("#form-observations").val();
        
        // Coletar dados da simulação
        const simData = {
            valoresDolar: valoresDolar,
            resultados: resultados,
            parametrosSimulacao: parametrosSimulacao
        };
        
        // Validação básica
        if (!name || !email || !phone) {
            alert("Por favor, preencha todos os campos obrigatórios.");
            return false;
        }
        
        // Mostrar loader
        $("#submit-button").prop("disabled", true).html("<i class=\"fa fa-spinner fa-spin\"></i> Enviando...");
        
        // Enviar dados para a API
        submitLead(name, email, phone, observations, simData)
            .then(response => {
                if (response.status === "success") {
                    // Limpar formulário
                    $("#form-name, #form-email, #form-phone, #form-observations").val("");
                    
                    // Mostrar mensagem de sucesso
                    $("#form-result").html("<div class=\"alert alert-success\">Sua simulação foi enviada com sucesso! Entraremos em contato em breve.</div>");
                    
                    // Reset do botão após 3 segundos
                    setTimeout(() => {
                        $("#submit-button").prop("disabled", false).html("Enviar Simulação");
                    }, 3000);
                } else {
                    throw new Error(response.message || "Erro desconhecido");
                }
            })
            .catch(error => {
                // Mostrar erro
                $("#form-result").html("<div class=\"alert alert-danger\">Erro ao enviar formulário: " + error.message + "</div>");
                
                // Reset do botão
                $("#submit-button").prop("disabled", false).html("Enviar Simulação");
            });
        
        return false; // Impedir envio normal do formulário
    }';
    
    // Adicionar jQuery se não existir
    if (strpos($content, 'jquery') === false) {
        $jqueryScriptTag = '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        $content = str_replace('</head>', $jqueryScriptTag . "\n</head>", $content);
    }
    
    // Localizar a posição de </script>
    $scriptPos = strrpos($content, '</script>');
    
    if ($scriptPos !== false) {
        // Insira o novo código antes do fechamento da tag script
        $updatedContent = substr($content, 0, $scriptPos) . $replacement . "\n" . substr($content, $scriptPos);
    } else {
        // Se não houver tag script, adicione ao final
        $bodyPos = strrpos($content, '</body>');
        if ($bodyPos !== false) {
            $updatedContent = substr($content, 0, $bodyPos) . "\n<script>\n" . $replacement . "\n</script>\n" . substr($content, $bodyPos);
        } else {
            $updatedContent = $content . "\n<script>\n" . $replacement . "\n</script>\n";
        }
    }
    
    // Adicionar listener para o botão de envio
    $submitListener = '
    <script>
    $(document).ready(function() {
        $("#submit-button").click(function(e) {
            e.preventDefault();
            return submitForm();
        });
    });
    </script>';
    
    // Adicionar o listener antes do fechamento do body
    $bodyPos = strrpos($updatedContent, '</body>');
    if ($bodyPos !== false) {
        $updatedContent = substr($updatedContent, 0, $bodyPos) . $submitListener . "\n" . substr($updatedContent, $bodyPos);
    } else {
        $updatedContent .= $submitListener;
    }
    
    // Verificar se existe uma modal-content ou card onde podemos inserir o formulário
    $modalContentPos = strpos($updatedContent, 'modal-content');
    $cardPos = strpos($updatedContent, 'class="card');
    
    // Formulário com estilo tailwind para combinar com o restante da página
    $formHTML = '
    <!-- Formulário de Captura de Leads -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-8">
        <h2 class="text-xl font-bold mb-4">Receba sua análise detalhada</h2>
        <p class="mb-4">Preencha os dados abaixo e enviaremos uma análise completa baseada nessa simulação.</p>
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
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="form-observations">
                Observações
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="form-observations" rows="3" placeholder="Compartilhe mais detalhes sobre sua necessidade"></textarea>
        </div>
        <div id="form-result" class="mb-4"></div>
        <div class="flex items-center justify-between">
            <button id="submit-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                Enviar Simulação
            </button>
        </div>
    </div>';
    
    // Vamos procurar um bom lugar para inserir o formulário
    $cardFound = false;
    
    // Procurar pela última div com classe card antes do fechamento do body
    if (preg_match_all('/<div[^>]*class="[^"]*card[^"]*"[^>]*>/', $updatedContent, $matches, PREG_OFFSET_CAPTURE)) {
        $lastCardPos = $matches[0][count($matches[0]) - 1][1];
        // Encontrar o fechamento dessa div
        $divDepth = 1;
        $pos = $lastCardPos + strlen($matches[0][count($matches[0]) - 1][0]);
        
        while ($divDepth > 0 && $pos < strlen($updatedContent)) {
            if (substr($updatedContent, $pos, 5) === '</div') {
                $divDepth--;
                if ($divDepth === 0) {
                    // Encontramos o fechamento da última div card
                    $closingDivPos = $pos + 6; // após o </div>
                    // Inserir o formulário após esta div
                    $updatedContent = substr($updatedContent, 0, $closingDivPos) . "\n" . $formHTML . "\n" . substr($updatedContent, $closingDivPos);
                    $cardFound = true;
                    break;
                }
            } elseif (substr($updatedContent, $pos, 4) === '<div') {
                $divDepth++;
            }
            $pos++;
        }
    }
    
    // Se não encontramos um card, inserir antes do fechamento do body
    if (!$cardFound) {
        $bodyPos = strrpos($updatedContent, '</body>');
        if ($bodyPos !== false) {
            // Inserir um container para o formulário
            $containerHTML = '
            <div class="w-full max-w-3xl mx-auto px-4 py-8">
                ' . $formHTML . '
            </div>';
            $updatedContent = substr($updatedContent, 0, $bodyPos) . $containerHTML . "\n" . substr($updatedContent, $bodyPos);
        }
    }
    
    // Atualizar a página no banco de dados
    $updateSql = "UPDATE pages SET page_content = :content WHERE id = 10";
    $updateStmt = $pdo->prepare($updateSql);
    $result = $updateStmt->execute(['content' => $updatedContent]);
    
    if ($result) {
        echo "Página do simulador de risco cambial atualizada com sucesso!\n";
    } else {
        echo "Erro ao atualizar a página do simulador.\n";
    }
    
    // Exibir o conteúdo da página após a atualização
    echo "\nConteúdo atualizado com sucesso! A página agora usa o endpoint: /tmp/api_direct.php\n";
    echo "Foi adicionado um formulário de captura de leads com estilo compatível com Tailwind CSS.\n";
    
} catch (PDOException $e) {
    echo "Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}