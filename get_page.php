<?php
// Carregando o sistema
require_once 'system/bootstrap.php';

// Inicializando a aplicação
$app = \Config\Services::codeigniter();
$app->initialize();

// Carregando o PageModel
$pageModel = new \App\Models\PageModel();

// Recuperando a página com ID 10
$page = $pageModel->getPageById(10);

// Exibindo o conteúdo da página
if (!empty($page)) {
    echo "<h1>{$page->title}</h1>";
    echo "<div>{$page->page_content}</div>";
} else {
    echo "Página com ID 10 não encontrada.";
}
?>