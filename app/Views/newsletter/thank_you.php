<?php
ob_start();
echo view('newsletter/_status_partial', [
    'glyph'    => lang('Newsletter.ty_glyph'),
    'eyebrow'  => lang('Newsletter.ty_eyebrow'),
    'headline' => lang('Newsletter.ty_headline'),
    'message'  => lang('Newsletter.ty_message'),
    'ctaText'  => lang('Newsletter.ty_cta'),
    'ctaUrl'   => '/wealth',
    'secondaryCtaText' => lang('Newsletter.ty_cta2'),
    'secondaryCtaUrl'  => '/',
    'meta' => [
        ['label' => lang('Newsletter.ty_meta1_l'), 'value' => lang('Newsletter.ty_meta1_v')],
        ['label' => lang('Newsletter.ty_meta2_l'), 'value' => lang('Newsletter.ty_meta2_v')],
        ['label' => lang('Newsletter.ty_meta3_l'), 'value' => lang('Newsletter.ty_meta3_v')],
    ],
]);
$body = ob_get_clean();
echo view('newsletter/_layout', [
    'title' => brandLang('Newsletter.ty_title'),
    'bodyContent' => $body,
]);
?>
