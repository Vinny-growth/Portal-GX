<?php
$socialArray = getSocialLinksArray($baseSettings);
$socialLinks = [];

if (!empty($socialArray)) {
    foreach ($socialArray as $item) {
        if (!empty($item['value'])) {
            $socialLinks[] = escMeta($item['value']);
        }
    }
}

// PERSON JSON — Vinicius Teixeira (founder/CEO)
$personSchema = [
    "@context" => "https://schema.org",
    "@type" => "Person",
    "@id" => base_url() . '/#person-vinicius-teixeira',
    "name" => "Vinicius Teixeira",
    "jobTitle" => "CEO & Founder",
    "worksFor" => ["@id" => base_url() . '/#organization'],
    "knowsAbout" => [
        "Câmbio estruturado",
        "Hedge cambial",
        "Crédito corporativo",
        "Trade finance",
        "Wealth advisory",
        "Mercado de capitais"
    ]
];

echo '<script type="application/ld+json">' . json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

// ORGANIZATION JSON
$organizationSchema = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "@id" => base_url() . '/#organization',
    "name" => "GX Capital",
    "legalName" => "GX Capital",
    "url" => base_url(),
    "description" => "Câmbio estruturado, crédito, consultoria financeira e wealth advisory para empresas e famílias.",
    "logo" => [
        "@type" => "ImageObject",
        "width" => 600,
        "height" => 60,
        "url" => getLogo()
    ],
    "founder" => ["@id" => base_url() . '/#person-vinicius-teixeira'],
    "knowsAbout" => [
        "Câmbio estruturado",
        "Hedge cambial",
        "Trade finance",
        "Crédito corporativo",
        "Wealth advisory",
        "Mercado de capitais",
        "Consórcio",
        "Operações 4131",
        "Gestão de patrimônio"
    ],
    "areaServed" => [
        "@type" => "Country",
        "name" => "Brazil",
        "sameAs" => "https://en.wikipedia.org/wiki/Brazil"
    ]
];

if (!empty($baseSettings->contact_email)) {
    $organizationSchema["contactPoint"] = [
        "@type" => "ContactPoint",
        "contactType" => "customer service",
        "email" => escMeta($baseSettings->contact_email),
        "availableLanguage" => ["Portuguese", "English"]
    ];
    if (!empty($baseSettings->contact_phone)) {
        $organizationSchema["contactPoint"]["telephone"] = escMeta($baseSettings->contact_phone);
    }
}

if (!empty($socialLinks)) {
    $organizationSchema["sameAs"] = $socialLinks;
}

// Press coverage — Article schemas (subjectOf)
$pressMentions = $gxPressMentions ?? [
    [
        'headline' => 'Crédito com garantia imobiliária ganha relevância na alocação de capital das empresas',
        'publisher' => 'Valor Econômico',
        'url' => 'https://valor.globo.com/patrocinado/pulse-brand/noticia/2026/03/20/credito-com-garantia-imobiliaria-ganha-relevancia-na-alocacao-de-capital-das-empresas-1.ghtml',
        'datePublished' => '2026-03-20',
    ],
    [
        'headline' => 'Câmbio deixa de ser rotina operacional e ganha peso estratégico na expansão de importadores e exportadores',
        'publisher' => 'Valor Econômico',
        'url' => 'https://valor.globo.com/patrocinado/pulse-brand/noticia/2026/03/11/cambio-deixa-de-ser-rotina-operacional-e-ganha-peso-estrategico-na-expansao-de-importadores-e-exportadores-1.ghtml',
        'datePublished' => '2026-03-11',
    ],
    [
        'headline' => 'Em um cenário de comércio exterior aquecido, câmbio deixa de ser rotina operacional e ganha peso nas decisões financeiras das empresas',
        'publisher' => 'O Globo',
        'url' => 'https://oglobo.globo.com/patrocinado/pulse-brand/noticia/2026/03/19/em-um-cenario-de-comercio-exterior-aquecido-cambio-deixa-de-ser-rotina-operacional-e-ganha-peso-nas-decisoes-financeiras-das-empresas-1.ghtml',
        'datePublished' => '2026-03-19',
    ],
];

$articleSchemas = [];
foreach ($pressMentions as $mention) {
    if (empty($mention['url'])) {
        continue;
    }
    $article = [
        "@type" => "NewsArticle",
        "headline" => $mention['headline'],
        "url" => $mention['url'],
        "publisher" => [
            "@type" => "NewsMediaOrganization",
            "name" => $mention['publisher']
        ]
    ];
    if (!empty($mention['datePublished'])) {
        $article['datePublished'] = $mention['datePublished'];
    }
    $articleSchemas[] = $article;
}
if (!empty($articleSchemas)) {
    $organizationSchema["subjectOf"] = $articleSchemas;
}

echo '<script type="application/ld+json">' . json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

// WEBSITE JSON
$websiteSchema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "@id" => base_url() . '/#website',
    "url" => base_url(),
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => base_url() . "/search?q={search_term_string}",
        "query-input" => "required name=search_term_string"
    ]
];

echo '<script type="application/ld+json">' . json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

if (!empty($marketingSchema)) {
    echo '<script type="application/ld+json">' . json_encode($marketingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

// NEWS ARTICLE JSON
if (!empty($postJsonLD)) {
    $dateModified = !empty($postJsonLD->updated_at) ? $postJsonLD->updated_at : $postJsonLD->created_at;
    $keywords = !empty($postJsonLD->keywords) ? explode(',', escMeta($postJsonLD->keywords)) : null;

    $newsArticleSchema = [
        "@context" => "https://schema.org",
        "@type" => "NewsArticle",
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => generatePostURL($postJsonLD)
        ],
        "headline" => escMeta($postJsonLD->title),
        "name" => escMeta($postJsonLD->title),
        "articleBody" => escMeta(strip_tags($postJsonLD->content)),
        "articleSection" => escMeta($postJsonLD->category_name),
        "datePublished" => date(DATE_ISO8601, strtotime($postJsonLD->created_at)),
        "dateModified" => date(DATE_ISO8601, strtotime($dateModified)),
        "inLanguage" => $activeLang->language_code,
        "author" => [
            "@type" => "Person",
            "name" => escMeta($postJsonLD->author_username),
            "url" => base_url("profile/" . $postJsonLD->author_slug)
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => clrQuotes($baseSettings->application_name),
            "logo" => [
                "@type" => "ImageObject",
                "width" => 600,
                "height" => 60,
                "url" => getLogo()
            ]
        ],
        "image" => array_filter([
            "@type" => "ImageObject",
            "url" => getPostImage($postJsonLD, 'discover') ?: getPostImage($postJsonLD, 'big'),
            "contentUrl" => getPostImage($postJsonLD, 'discover') ?: getPostImage($postJsonLD, 'big'),
            "width" => !empty(getPostImage($postJsonLD, 'discover')) ? 1200 : 870,
            "height" => !empty(getPostImage($postJsonLD, 'discover')) ? 675 : 580,
            "caption" => !empty($postJsonLD->image_description) ? escMeta($postJsonLD->image_description) : null,
            "description" => !empty($postJsonLD->image_alt) ? escMeta($postJsonLD->image_alt) : null,
        ]),
        "isAccessibleForFree" => true,
        "hasPart" => [
            "@type" => "WebPageElement",
            "isAccessibleForFree" => true,
            "cssSelector" => [".post-content"]
        ],
        "speakable" => [
            "@type" => "SpeakableSpecification",
            "cssSelector" => [".post-content"]
        ]
    ];

    if (!empty($keywords)) {
        $newsArticleSchema["keywords"] = $keywords;
    }

    echo '<script type="application/ld+json">' . json_encode($newsArticleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

// BREADCRUMB JSON
if (!empty($category)) {
    $breadcrumbSchema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => []
    ];

    $position = 1;

    if (!empty($parentCategory)) {
        $breadcrumbSchema["itemListElement"][] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => escMeta($parentCategory->name),
            "item" => generateCategoryURL($parentCategory)
        ];
    }

    $breadcrumbSchema["itemListElement"][] = [
        "@type" => "ListItem",
        "position" => $position,
        "name" => escMeta($category->name),
        "item" => generateCategoryURL($category)
    ];

    echo '<script type="application/ld+json">' . json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
} ?>
