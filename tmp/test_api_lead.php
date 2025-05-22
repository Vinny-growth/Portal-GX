<?php
// Script para testar a API de leads diretamente do servidor

// Dados do lead
$leadData = [
    'name' => 'Lead via API PHP',
    'email' => 'lead.api@exemplo.com',
    'phone' => '11977776666',
    'observations' => 'Lead enviado via API PHP interno'
];

// URL da API
$url = 'http://gx.capital/api/save-simulator-lead';

// Configurar o contexto para a requisição POST
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($leadData)
    ]
];

$context = stream_context_create($options);

echo "Enviando requisição para: {$url}\n";
echo "Dados: " . json_encode($leadData) . "\n\n";

// Fazer a requisição
$result = file_get_contents($url, false, $context);

echo "Resposta recebida:\n";
if ($result === FALSE) {
    echo "Erro ao fazer a requisição!\n";
} else {
    echo $result . "\n";
}

// Verificar o lead inserido
echo "\nVerificando leads no banco de dados:\n";
require_once 'check_leads.php';