<?php
/**
 * Partial de SEO para os playbooks (ebooks interativos).
 *
 * Variáveis esperadas (passadas pelo controller / view):
 *  - $playbookConfig (array):
 *      - id (string)             ex: 'importacao-blindada'
 *      - title (string)          headline visível
 *      - description (string)    meta description (<160c)
 *      - canonicalUrl (string)
 *      - section (string)        ex: 'Câmbio · Importação'
 *      - keywords (string)       lista separada por vírgula
 *      - datePublished (ISO-8601)
 *      - dateModified  (ISO-8601)
 *      - readingTimeMin (int)
 *      - wordCount (int)
 *      - image (string)          URL absoluta 1200×630 idealmente
 *      - faq (array of [q, a])   pares pergunta/resposta para FAQPage
 *      - chapters (array)        ['Capítulo 1', 'Capítulo 2', ...]
 *      - breadcrumb (array of [name, url])
 */
$cfg = $playbookConfig ?? [];
$canonical = $cfg['canonicalUrl'] ?? current_url();
$datePublished = $cfg['datePublished'] ?? '2026-05-08';
$dateModified  = $cfg['dateModified']  ?? $datePublished;
$image = $cfg['image'] ?? getLogo();

$articleSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Article',
    '@id'      => $canonical . '#article',
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $canonical
    ],
    'headline'      => $cfg['title'] ?? 'Playbook GX Capital',
    'description'   => $cfg['description'] ?? '',
    'image'         => [$image],
    'datePublished' => $datePublished,
    'dateModified'  => $dateModified,
    'author'        => ['@id' => base_url() . '/#person-vinicius-teixeira'],
    'publisher'     => ['@id' => base_url() . '/#organization'],
    'inLanguage'    => 'pt-BR',
    'articleSection' => $cfg['section'] ?? 'Câmbio',
    'keywords'      => $cfg['keywords'] ?? '',
    'isAccessibleForFree' => true,
];

if (!empty($cfg['readingTimeMin'])) {
    $articleSchema['timeRequired'] = 'PT' . (int)$cfg['readingTimeMin'] . 'M';
}
if (!empty($cfg['wordCount'])) {
    $articleSchema['wordCount'] = (int)$cfg['wordCount'];
}
if (!empty($cfg['chapters']) && is_array($cfg['chapters'])) {
    $articleSchema['hasPart'] = array_map(static function ($name, $idx) use ($canonical) {
        return [
            '@type' => 'Chapter',
            'name'  => $name,
            'url'   => $canonical . '#ch-' . ($idx + 1),
        ];
    }, $cfg['chapters'], array_keys($cfg['chapters']));
}

echo '<script type="application/ld+json">' . json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

// FAQPage
if (!empty($cfg['faq']) && is_array($cfg['faq'])) {
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        '@id'      => $canonical . '#faq',
        'mainEntity' => array_map(static function ($pair) {
            return [
                '@type' => 'Question',
                'name'  => $pair['q'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $pair['a'] ?? '',
                ],
            ];
        }, $cfg['faq']),
    ];
    echo '<script type="application/ld+json">' . json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

// BreadcrumbList
$breadcrumbItems = $cfg['breadcrumb'] ?? [
    ['name' => 'Início',   'url' => base_url()],
    ['name' => 'Playbook', 'url' => $canonical],
];
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    '@id'      => $canonical . '#breadcrumb',
    'itemListElement' => array_map(static function ($item, $idx) {
        return [
            '@type'    => 'ListItem',
            'position' => $idx + 1,
            'name'     => $item['name'] ?? '',
            'item'     => $item['url']  ?? '',
        ];
    }, $breadcrumbItems, array_keys($breadcrumbItems)),
];
echo '<script type="application/ld+json">' . json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

// Re-exportar Person + Organization usando o partial canônico do projeto
echo view('common/_json_ld');
