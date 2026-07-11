<?php
$emailDisplay = $email ?? '';
ob_start();
echo view('newsletter/_status_partial', [
    'glyph'    => lang('Newsletter.pc_glyph'),
    'eyebrow'  => lang('Newsletter.pc_eyebrow'),
    'headline' => lang('Newsletter.pc_headline'),
    'message'  => strtr(lang('Newsletter.pc_message'), ['{email}' => '<strong>' . esc($emailDisplay) . '</strong>']),
    'ctaText'  => lang('Newsletter.ty_cta2'),
    'ctaUrl'   => '/',
    'meta' => [
        ['label' => lang('Newsletter.pc_meta1_l'), 'value' => lang('Newsletter.pc_meta1_v')],
        ['label' => lang('Newsletter.pc_meta2_l'), 'value' => lang('Newsletter.pc_meta2_v')],
    ],
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => brandLang('Newsletter.pc_title'),
    'bodyContent' => $body,
]);
?>
