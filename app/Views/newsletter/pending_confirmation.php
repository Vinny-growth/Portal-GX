<?php
$emailDisplay = $email ?? '';
ob_start();
echo view('newsletter/_status_partial', [
    'glyph'    => '[ 01 / 02 ] AGUARDANDO CONFIRMAÇÃO',
    'eyebrow'  => 'Falta um passo',
    'headline' => 'Verifique seu email.',
    'message'  => 'Enviamos um link de confirmação para <strong>' . esc($emailDisplay) . '</strong>. Clique no botão dentro do email para ativar sua inscrição e receber o material exclusivo. Não esqueça de checar a caixa de spam.',
    'ctaText'  => 'Voltar ao início',
    'ctaUrl'   => '/',
    'meta' => [
        ['label' => 'Tempo médio', 'value' => '< 2 min'],
        ['label' => 'Validade do link', 'value' => '24h'],
    ],
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => 'Confirme seu email — GX Capital',
    'bodyContent' => $body,
]);
?>
