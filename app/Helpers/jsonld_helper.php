<?php

/**
 * GX Capital — JSON-LD builders (GEO/SEO).
 *
 * Funções puras que retornam arrays schema.org prontos para json_encode.
 * Reutilizáveis por qualquer controller/view. Referenciam a Organization e a
 * Person já emitidas globalmente em app/Views/common/_json_ld.php pelos seus @id
 * (base_url() . '/#organization' e '/#person-vinicius-teixeira'), evitando duplicar nós.
 *
 * Uso típico:
 *   helper('jsonld');
 *   $data['marketingSchema'] = jsonldGraph([
 *       jsonldSoftwareApplication($url, $nome, $descricao),
 *       jsonldBreadcrumb([...]),
 *   ]);
 */

if (!function_exists('jsonldGraph')) {
    /**
     * Empacota uma lista de nós em um documento { @context, @graph }.
     * Ignora entradas vazias/falsy para nunca emitir nós quebrados.
     */
    function jsonldGraph(array $nodes): array
    {
        $clean = array_values(array_filter($nodes, static fn($n) => !empty($n)));
        if (empty($clean)) {
            return [];
        }
        return [
            '@context' => 'https://schema.org',
            '@graph'   => $clean,
        ];
    }
}

if (!function_exists('jsonldSoftwareApplication')) {
    /**
     * Nó SoftwareApplication para simuladores/calculadoras (ferramentas web gratuitas).
     *
     * @param array $opts image (string), category (default FinanceApplication)
     */
    function jsonldSoftwareApplication(string $url, string $name, string $description = '', array $opts = []): array
    {
        if ($url === '' || $name === '') {
            return [];
        }
        $node = [
            '@type'               => 'SoftwareApplication',
            '@id'                 => $url . '#app',
            'name'                => $name,
            'url'                 => $url,
            'applicationCategory' => $opts['category'] ?? 'FinanceApplication',
            'operatingSystem'     => 'Web',
            'offers'              => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'BRL',
            ],
            'provider'  => ['@id' => base_url() . '/#organization'],
            'publisher' => ['@id' => base_url() . '/#organization'],
        ];
        if ($description !== '') {
            $node['description'] = $description;
        }
        if (!empty($opts['image'])) {
            $node['image'] = $opts['image'];
        }
        return $node;
    }
}

if (!function_exists('jsonldBreadcrumb')) {
    /**
     * BreadcrumbList a partir de [['name'=>, 'url'=>], ...] (url opcional no último item).
     */
    function jsonldBreadcrumb(array $items): array
    {
        $elements = [];
        $position = 1;
        foreach ($items as $item) {
            if (empty($item['name'])) {
                continue;
            }
            $el = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $item['name'],
            ];
            if (!empty($item['url'])) {
                $el['item'] = $item['url'];
            }
            $elements[] = $el;
        }
        if (empty($elements)) {
            return [];
        }
        return [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }
}

if (!function_exists('jsonldFaqPage')) {
    /**
     * FAQPage a partir de [['q'=>, 'a'=>], ...].
     * IMPORTANTE (diretriz Google): só use quando as MESMAS perguntas/respostas
     * estiverem visíveis no HTML da página. Não emita schema fantasma.
     */
    function jsonldFaqPage(array $faqs, string $id = ''): array
    {
        $entities = [];
        foreach ($faqs as $i => $faq) {
            $q = trim((string) ($faq['q'] ?? ''));
            $a = trim((string) ($faq['a'] ?? ''));
            if ($q === '' || $a === '') {
                continue;
            }
            $entities[] = [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
                'position'       => $i + 1,
            ];
        }
        if (empty($entities)) {
            return [];
        }
        $node = ['@type' => 'FAQPage', 'mainEntity' => $entities];
        if ($id !== '') {
            $node['@id'] = $id;
        }
        return $node;
    }
}
