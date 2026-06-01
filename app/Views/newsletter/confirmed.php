<?php
$status = $result['status'] ?? 'unknown';
$ok = !empty($result['ok']);

if ($status === 'confirmed') {
    $glyph    = '[ 02 / 02 ] CONFIRMADO';
    $eyebrow  = 'Inscrição ativa';
    $headline = 'Pronto. Seu material está chegando.';
    $message  = 'Sua inscrição foi confirmada com sucesso. Em alguns minutos você recebe o email de boas-vindas com o material exclusivo da frente que escolheu.';
    $ctaText  = 'Conhecer o Wealth Manager';
    $ctaUrl   = '/wealth';
} elseif ($status === 'already_confirmed') {
    $glyph    = '[ STATUS ] JÁ CONFIRMADO';
    $eyebrow  = 'Inscrição ativa';
    $headline = 'Você já está com a gente.';
    $message  = 'Sua inscrição já estava confirmada. Você continua recebendo a inteligência GX no inbox normalmente.';
    $ctaText  = 'Voltar ao início';
    $ctaUrl   = '/';
} else {
    $glyph    = '[ ERRO ] TOKEN INVÁLIDO';
    $eyebrow  = 'Não foi possível confirmar';
    $headline = 'Link inválido ou expirado.';
    $message  = 'Este link não foi reconhecido. Pode ser que já tenha expirado ou tenha sido usado. Tente se inscrever novamente para receber um novo link.';
    $ctaText  = 'Inscrever-me novamente';
    $ctaUrl   = '/newsletter';
}

ob_start();
echo view('newsletter/_status_partial', [
    'glyph'    => $glyph,
    'eyebrow'  => $eyebrow,
    'headline' => $headline,
    'message'  => $message,
    'ctaText'  => $ctaText,
    'ctaUrl'   => $ctaUrl,
    'secondaryCtaText' => 'Voltar ao início',
    'secondaryCtaUrl'  => '/',
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => $headline,
    'bodyContent' => $body,
]);
?>
