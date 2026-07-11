<?php
$status = $result['status'] ?? 'unknown';
$ok = !empty($result['ok']);

if ($status === 'confirmed') {
    $glyph    = lang('Newsletter.cf_ok_glyph');
    $eyebrow  = lang('Newsletter.cf_eyebrow_ativa');
    $headline = lang('Newsletter.cf_ok_headline');
    $message  = lang('Newsletter.cf_ok_message');
    $ctaText  = lang('Newsletter.ty_cta');
    $ctaUrl   = '/wealth';
} elseif ($status === 'already_confirmed') {
    $glyph    = lang('Newsletter.cf_already_glyph');
    $eyebrow  = lang('Newsletter.cf_eyebrow_ativa');
    $headline = lang('Newsletter.cf_already_headline');
    $message  = lang('Newsletter.cf_already_message');
    $ctaText  = lang('Newsletter.ty_cta2');
    $ctaUrl   = '/';
} else {
    $glyph    = lang('Newsletter.cf_err_glyph');
    $eyebrow  = lang('Newsletter.cf_err_eyebrow');
    $headline = lang('Newsletter.cf_err_headline');
    $message  = lang('Newsletter.cf_err_message');
    $ctaText  = lang('Newsletter.cf_err_cta');
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
    'secondaryCtaText' => lang('Newsletter.ty_cta2'),
    'secondaryCtaUrl'  => '/',
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => $headline,
    'bodyContent' => $body,
]);
?>
