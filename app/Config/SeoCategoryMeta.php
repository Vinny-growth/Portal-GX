<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Overrides de SEO das páginas-pilar (categorias), sem tocar no cadastro da
 * categoria no banco: o nome da categoria segue alimentando menus/breadcrumbs,
 * e aqui mora só a camada de busca (title tag, meta description e um bloco de
 * introdução visível renderizado acima da listagem).
 *
 * Mesmo padrão editável do Config\SeoFaq. Keywords-alvo baseadas em volume
 * real (Ubersuggest BR, jul/2026): "notícias economia" (1.600/mês), "cenário
 * econômico" (390), "economia brasileira hoje" (260), "análise econômica" (110).
 */
class SeoCategoryMeta extends BaseConfig
{
    /** Overrides indexados por ID de categoria. Chaves: title, description, intro (HTML). */
    public array $byCategoryId = [
        // 7 = Radar Econômico (/economia)
        7 => [
            'title'       => 'Radar Econômico: notícias de economia e análises do Brasil',
            'description' => 'Notícias de economia e análise diária do cenário econômico brasileiro: juros, inflação, câmbio e política fiscal com o olhar da GX Capital para decisões empresariais.',
            'intro'       => '<p>O Radar Econômico reúne as principais <strong>notícias de economia</strong> com análise do <strong>cenário econômico</strong> brasileiro e internacional. Acompanhamos juros, inflação, câmbio, atividade e política fiscal — os indicadores que mudam o custo do crédito, o preço do dólar e as decisões de investimento da sua empresa.</p><p>Mais do que noticiar, cada matéria traz uma análise econômica aplicada: o que o movimento da economia brasileira hoje significa para quem importa, exporta, capta crédito ou administra caixa. Atualização diária, com curadoria da equipe GX Capital.</p>',
        ],
        // 6 = Câmbio (/cambio-6)
        6 => [
            'title' => 'Câmbio para empresas: hedge cambial, dólar e trade finance',
        ],

        // 11 = GX explica (/gx-explica)
        11 => [
            'title' => 'GX Explica: glossário financeiro e guias para empresas',
        ],

        // 13 = Investimentos (/investimentos)
        13 => [
            'title' => 'Investimentos: guias e análises de renda fixa, ações e FIIs',
        ],

        // 14 = Seguro de Vida (/seguro-de-vida)
        14 => [
            'title' => 'Seguro de vida: resgatável, proteção patrimonial e sucessão',
        ],
    ];

    /** Overrides indexados por slug de página CMS (HomeController::page). */
    public array $byPageSlug = [
        'contato' => [
            'title'       => 'Contato: fale com um especialista em câmbio e crédito',
            'description' => 'Fale com a equipe GX Capital: atendimento a empresas e famílias em câmbio, crédito estruturado, consórcio, seguros e wealth. Canais diretos e retorno rápido.',
        ],
    ];

    public function forCategoryId(int $id): array
    {
        return $this->byCategoryId[$id] ?? [];
    }

    public function forPageSlug(string $slug): array
    {
        return $this->byPageSlug[$slug] ?? [];
    }
}
