<?php
ob_start();
echo view('newsletter/_status_partial', [
    'glyph'    => '[ 01 / 01 ] CONCLUÍDO',
    'eyebrow'  => 'Inscrição confirmada',
    'headline' => 'Bem-vindo à inteligência GX.',
    'message'  => 'Sua primeira edição já está a caminho. Em até alguns minutos você receberá um email de boas-vindas com o material exclusivo da frente que você escolheu.',
    'ctaText'  => 'Conhecer o Wealth Manager',
    'ctaUrl'   => '/wealth',
    'secondaryCtaText' => 'Voltar ao início',
    'secondaryCtaUrl'  => '/',
    'meta' => [
        ['label' => 'Frequência', 'value' => '3× / dia'],
        ['label' => 'Cancelar', 'value' => '1 clique'],
        ['label' => 'Inbox típico', 'value' => '90s leitura'],
    ],
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => 'Bem-vindo à GX Capital',
    'bodyContent' => $body,
]);
?>
