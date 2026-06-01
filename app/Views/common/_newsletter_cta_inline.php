<?php
// Inline newsletter CTA used inside post pages.
// Varies the anchor text + URL params per category for internal-linking diversity.

$categoryId = isset($post->category_id) ? (int) $post->category_id : 0;

// Map category → editorial line slug + tailored copy
$lineConfig = [
    6 => [  // Cambio
        'slug' => 'cambio',
        'eyebrow' => 'Inteligência cambial',
        'headline' => 'Receba o radar do dólar todo dia.',
        'subhead' => 'Briefings de 90 segundos sobre câmbio, hedge e movimentos do BCB — direto no seu inbox.',
        'cta' => 'Inscrever na frente Câmbio',
    ],
    7 => [  // Radar Econômico
        'slug' => 'radar-economico',
        'eyebrow' => 'Radar Econômico',
        'headline' => 'O Focus decifrado antes do mercado abrir.',
        'subhead' => 'Selic, IPCA, Copom e balança comercial — traduzidos para a planilha do seu CFO.',
        'cta' => 'Inscrever no Radar Econômico',
    ],
    8 => [  // Crédito Empresarial
        'slug' => 'credito-empresarial',
        'eyebrow' => 'Crédito empresarial',
        'headline' => 'Pronampe, BNDES e debêntures sem ruído.',
        'subhead' => 'Linhas de crédito empresarial explicadas com matemática e prazo — sem letra miúda.',
        'cta' => 'Inscrever na frente Crédito',
    ],
    11 => [ // GX explica
        'slug' => 'gx-explica',
        'eyebrow' => 'Inteligência GX',
        'headline' => 'Quer mais conteúdo como este no seu inbox?',
        'subhead' => 'Câmbio, crédito, economia e consórcio explicados em 90 segundos por edição.',
        'cta' => 'Inscrever na newsletter',
    ],
];

$default = [
    'slug' => '',
    'eyebrow' => 'Newsletter GX Capital',
    'headline' => 'Inteligência financeira que chega antes do mercado reagir.',
    'subhead' => 'Briefings curtos sobre câmbio, crédito, economia e consórcio — 3 edições por dia, 90 segundos cada.',
    'cta' => 'Conhecer a newsletter',
];

$cfg = $lineConfig[$categoryId] ?? $default;
$href = '/newsletter' . (!empty($cfg['slug']) ? '?linha=' . $cfg['slug'] : '');
?>
<style>
    .post-nl-cta {
        margin: 32px 0;
        padding: 28px 28px 24px;
        background: linear-gradient(135deg, #0c3163 0%, #000d23 100%);
        color: #dbc7a2;
        position: relative;
        overflow: hidden;
        border-left: 4px solid #c9a96a;
    }
    .post-nl-cta::before {
        content: 'GXC';
        position: absolute; right: -10px; bottom: -30px;
        font-family: 'Inter', sans-serif; font-weight: 900;
        font-size: 140px; line-height: 0.8; letter-spacing: -0.06em;
        color: #dbc7a2; opacity: 0.04;
        pointer-events: none; user-select: none;
    }
    .post-nl-cta .eyebrow {
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.18em; text-transform: uppercase;
        color: #c9a96a; margin: 0 0 8px;
    }
    .post-nl-cta h3 {
        font-family: 'Inter', sans-serif; font-weight: 900;
        font-size: 22px; line-height: 1.2; letter-spacing: -0.02em;
        text-transform: uppercase; color: #fff;
        margin: 0 0 8px;
    }
    .post-nl-cta p {
        font-size: 14px; line-height: 1.55;
        color: #dbc7a2; opacity: 0.85;
        margin: 0 0 16px; max-width: 540px;
    }
    .post-nl-cta a.btn-nl {
        display: inline-flex; align-items: center; gap: 8px;
        background: #c9a96a; color: #000d23 !important;
        font-size: 12px; font-weight: 900;
        letter-spacing: 0.16em; text-transform: uppercase;
        padding: 12px 22px; text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 0;
    }
    .post-nl-cta a.btn-nl:hover {
        background: #dbc7a2; transform: translate(-2px, -2px);
        box-shadow: 4px 4px 0 0 rgba(201,169,106,0.4);
    }
    .post-nl-cta .small-print {
        margin-top: 10px; font-size: 11px; opacity: 0.55;
        letter-spacing: 0.04em;
    }
</style>
<aside class="post-nl-cta" aria-label="Newsletter GX Capital">
    <p class="eyebrow"><?= esc($cfg['eyebrow']); ?></p>
    <h3><?= esc($cfg['headline']); ?></h3>
    <p><?= esc($cfg['subhead']); ?></p>
    <a href="<?= esc($href); ?>" class="btn-nl">
        <?= esc($cfg['cta']); ?> &rarr;
    </a>
    <div class="small-print">Gratuita. Cancele com 1 clique. Sem spam.</div>
</aside>
