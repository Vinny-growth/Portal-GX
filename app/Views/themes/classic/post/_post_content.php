<?php
$arrayContent = array();
$articleAd1 = array();
$articleAd2 = array();
if (!empty($adSpaces)) {
    foreach ($adSpaces as $item) {
        if ($item->ad_space == 'in_article_1') {
            $articleAd1 = $item;
        }
        if ($item->ad_space == 'in_article_2') {
            $articleAd2 = $item;
        }
    }
}
if (!empty($post->content)) {
    // SEO: sanitizar conteúdo — remover tags de documento e converter H1→H2
    $postContent = $post->content;
    $postContent = preg_replace('/<\/?(html|head|body|meta|link|!DOCTYPE)[^>]*>/i', '', $postContent);
    $postContent = preg_replace('/<title[^>]*>.*?<\/title>/is', '', $postContent);
    $postContent = preg_replace('/<h1([^>]*)>/i', '<h2$1>', $postContent);
    $postContent = str_ireplace('</h1>', '</h2>', $postContent);
    $postContent = str_ireplace('<h2></h2>', '', $postContent);
    $arrayContent = explode('</p>', $postContent);
}

if (!empty($arrayContent)) {
    $i = 1;
    foreach ($arrayContent as $p) {
        echo $p;
        if (!empty($articleAd1) && !empty($articleAd1->paragraph_number) && $articleAd1->paragraph_number == $i) {
            echo loadView('partials/_ad_spaces', ['adSpace' => 'in_article_1', 'class' => 'mb-3']);
        }
        if (!empty($articleAd2) && !empty($articleAd2->paragraph_number) && $articleAd2->paragraph_number == $i) {
            echo loadView('partials/_ad_spaces', ['adSpace' => 'in_article_2', 'class' => 'mb-3']);
        }
        $i++;
    }
} ?>