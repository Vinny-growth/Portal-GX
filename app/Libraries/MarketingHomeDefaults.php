<?php

namespace App\Libraries;

use App\Models\CategoryModel;
use App\Models\PageModel;
use Config\Globals;

class MarketingHomeDefaults
{
    protected $langId;

    public function __construct($langId = null)
    {
        $activeLang = Globals::$activeLang ?? null;
        $this->langId = !empty($langId) ? (int)$langId : (int)(!empty($activeLang) && !empty($activeLang->id) ? $activeLang->id : 1);
    }

    public function getHomeConfigDefaults()
    {
        return [
            'nav' => [
                'primary_cta_label' => 'Começar agora',
                'primary_cta_url' => '#fale-especialista',
                'quick_links' => [
                    ['enabled' => 1, 'label' => 'Soluções', 'href' => '#verticais'],
                    ['enabled' => 1, 'label' => 'Simuladores', 'href' => '#simuladores'],
                    ['enabled' => 1, 'label' => 'Clipping', 'href' => '#clipping-gx'],
                    ['enabled' => 1, 'label' => 'Insights', 'href' => '#blog-tecnico'],
                    ['enabled' => 1, 'label' => 'Especialista', 'href' => '#fale-especialista'],
                ],
            ],
            'hero_stats_labels' => [
                'simulators' => 'simuladores disponíveis',
                'verticals' => 'frentes de atuação',
                'insights' => 'análises recentes',
            ],
            'hero' => [
                'enabled' => 1,
                'badge' => 'Estruturação financeira com profundidade técnica',
                'title' => 'Soluções sofisticadas em capital, proteção e patrimônio.',
                'subtitle' => 'A GX Capital estrutura crédito, câmbio, consórcios, seguros e investimentos para empresas, empresários e famílias empresárias que precisam decidir com clareza, previsibilidade e visão de longo prazo.',
                'primary_cta_label' => 'Falar com especialista',
                'primary_cta_url' => '#fale-especialista',
                'secondary_cta_label' => 'Explorar simuladores',
                'secondary_cta_url' => $this->langUrl('simuladores'),
                'proof_items' => [
                    ['enabled' => 1, 'title' => 'Capital e risco', 'text' => 'Crédito, câmbio, seguros e patrimônio lidos em conjunto.'],
                    ['enabled' => 1, 'title' => 'Ferramentas práticas', 'text' => 'Simuladores para antecipar custo, prazo e estrutura.'],
                    ['enabled' => 1, 'title' => 'Relação consultiva', 'text' => 'Direcionamento para a vertical mais aderente ao caso.'],
                ],
            ],
            'trust_strip' => [
                'enabled' => 1,
                'lead' => 'Atuação integrada',
                'items' => [
                    ['enabled' => 1, 'label' => 'Crédito Estruturado'],
                    ['enabled' => 1, 'label' => 'Câmbio & Trade Finance'],
                    ['enabled' => 1, 'label' => 'Consórcios'],
                    ['enabled' => 1, 'label' => 'Seguros Corporativos'],
                    ['enabled' => 1, 'label' => 'Wealth Management'],
                ],
            ],
            'verticals' => [
                'enabled' => 1,
                'label' => 'Verticais de negócio',
                'title' => 'Cinco frentes especializadas. Um critério integrado.',
                'description' => 'Da liquidez de curto prazo ao patrimônio de longo prazo, cada frente opera com profundidade técnica e visão de conjunto.',
                'items' => $this->getBusinessVerticals(),
            ],
            'process' => [
                'enabled' => 1,
                'label' => 'Como atuamos',
                'title' => 'Da leitura do problema à estrutura executada.',
                'description' => 'Combinamos análise financeira, modelagem comparativa e acompanhamento executivo para reduzir ruído e ganhar velocidade na decisão.',
                'items' => [
                    ['enabled' => 1, 'title' => 'Diagnóstico', 'desc' => 'Mapeamos caixa, exposição cambial, custo de capital e objetivo patrimonial antes de qualquer recomendação.'],
                    ['enabled' => 1, 'title' => 'Estruturação', 'desc' => 'Comparamos alternativas, modelamos cenários e montamos a estrutura mais aderente ao momento da empresa.'],
                    ['enabled' => 1, 'title' => 'Execução', 'desc' => 'Acompanhamos a implementação para transformar a análise em decisão executada com critério e rastreabilidade.'],
                ],
            ],
            'simulators' => [
                'enabled' => 1,
                'label' => 'Simuladores',
                'title' => 'Ferramentas para antecipar custo, risco e estrutura.',
                'description' => 'Use os simuladores para testar cenários antes da conversa comercial e chegue com a demanda mais madura.',
                'cta_label' => 'Ver catálogo completo',
                'cta_url' => $this->langUrl('simuladores'),
            ],
            'clippings' => [
                'enabled' => 1,
                'label' => 'Clipping de notícias',
                'title' => 'GX Capital em grandes portais.',
                'description' => 'Acompanhe a presença da GX Capital nos principais veículos de economia, finanças e mercado.',
                'item_cta_label' => 'Ler matéria',
                'items' => [],
            ],
            'partners' => [
                'enabled' => 1,
                'label' => 'Parceiros de qualidade',
                'title' => 'Instituições que fortalecem a atuação da GX Capital.',
                'description' => 'A GX Capital opera com instituições de referência para garantir execução, governança e acesso a soluções de mercado.',
                'items' => [],
            ],
            'blog' => [
                'enabled' => 1,
                'label' => 'Conteúdo técnico',
                'title' => 'Análises para acompanhar funding, câmbio, patrimônio e mercado.',
                'description' => 'Publicamos leituras práticas para quem decide com responsabilidade financeira e precisa de contexto para agir.',
                'featured_cta_label' => 'Ler análise',
                'cta_label' => 'Ver todos os artigos',
                'cta_url' => $this->langUrl('blog'),
            ],
            'cta' => [
                'enabled' => 1,
                'label' => 'Atendimento consultivo',
                'title' => 'Leve a demanda financeira para a frente certa desde o primeiro contato.',
                'description' => 'Se a pauta envolver funding, hedge, recebíveis, consórcio, seguros ou patrimônio, centralize a conversa com a GX Capital.',
                'primary_cta_label' => 'Falar com o time',
                'primary_cta_url' => '#fale-especialista',
                'secondary_cta_label' => 'Abrir simuladores',
                'secondary_cta_url' => $this->langUrl('simuladores'),
            ],
            'lead' => [
                'enabled' => 1,
                'label' => 'Fale com a GX Capital',
                'title' => 'Converse com um especialista.',
                'description' => 'Compartilhe o contexto da empresa, da operação ou do objetivo patrimonial e direcionamos a conversa para a vertical mais aderente.',
                'form_heading' => 'Envie sua demanda para o time GX Capital',
                'form_description' => 'Informe a estrutura, operação ou objetivo patrimonial. O retorno parte da vertical mais aderente.',
                'form_button_label' => 'Solicitar contato',
                'message_placeholder' => 'Ex.: preciso revisar hedge cambial, custo de capital, recebíveis, consórcio, seguros ou carteira de investimentos.',
                'show_phone' => 1,
                'show_email' => 1,
                'show_simulators_chip' => 1,
                'show_blog_chip' => 1,
                'simulators_chip_label' => 'Ver simuladores',
                'blog_chip_label' => 'Explorar blog',
            ],
        ];
    }

    public function getBusinessVerticals()
    {
        return [
            [
                'enabled' => 1,
                'title' => 'Crédito Estruturado',
                'eyebrow' => 'Funding e capital de giro',
                'description' => 'Estruture capital, alongue prazos e compare linhas com mais clareza antes de negociar.',
                'link_label' => 'Ver frente de crédito',
                'link_url' => $this->resolveCategoryUrl('credito-empresarial', $this->langUrl('simuladores')),
                'accent' => '#0f766e',
            ],
            [
                'enabled' => 1,
                'title' => 'Câmbio e Trade Finance',
                'eyebrow' => 'Proteção cambial e execução',
                'description' => 'Combine hedge, fluxo internacional e leitura de exposição cambial em uma única frente.',
                'link_label' => 'Explorar câmbio',
                'link_url' => $this->langUrl('simuladores/cambio'),
                'accent' => '#0b5cab',
            ],
            [
                'enabled' => 1,
                'title' => 'Consórcios Estruturados',
                'eyebrow' => 'Planejamento e alavancagem',
                'description' => 'Avalie fluxo de pagamento, contemplação e custo total antes de escolher a estrutura ideal.',
                'link_label' => 'Abrir simulador',
                'link_url' => $this->resolvePageUrl('simulador-consorcio', $this->langUrl('simuladores')),
                'accent' => '#8f5b2e',
            ],
            [
                'enabled' => 1,
                'title' => 'Seguros',
                'eyebrow' => 'Proteção patrimonial e operacional',
                'description' => 'Desenhe coberturas aderentes ao risco real da empresa e traga a conversa para a mesa financeira.',
                'link_label' => 'Falar com especialista',
                'link_url' => '#fale-especialista',
                'accent' => '#b45309',
            ],
            [
                'enabled' => 1,
                'title' => 'Consultoria de Investimentos',
                'eyebrow' => 'Patrimônio, liquidez e estratégia',
                'description' => 'Conecte tesouraria, objetivos patrimoniais e alocação com uma leitura mais executável do patrimônio.',
                'link_label' => 'Conhecer wealth',
                'link_url' => $this->baseUrl('wealth'),
                'accent' => '#7c3aed',
            ],
        ];
    }

    public function getSimulatorCatalog()
    {
        $pageModel = new PageModel();
        $definitions = [
            'simulador-de-risco-cambial' => [
                'label' => 'FX',
                'eyebrow' => 'Câmbio e trade finance',
                'title' => 'Estudo de risco cambial',
                'description' => 'Avalie exposição, pressão sobre margem e necessidade de proteção em operações de importação e exportação.',
                'cta' => 'Abrir estudo',
            ],
            'fx-loan' => [
                'label' => '4131',
                'eyebrow' => 'Funding internacional',
                'title' => 'Estrutura 4131 e FX Loan',
                'description' => 'Compare custo local e internacional para entender quando uma captação em moeda forte merece aprofundamento.',
                'cta' => 'Avaliar estrutura',
            ],
            'aurum-simulador-de-custo-de-capital' => [
                'label' => 'CAP',
                'eyebrow' => 'Crédito estruturado',
                'title' => 'Simulador de Custo de Capital',
                'description' => 'Compare custos de funding e entenda qual estrutura de crédito faz mais sentido para a operação.',
                'cta' => 'Calcular custo',
            ],
            'simulador-mercado-de-capitais' => [
                'label' => 'MKT',
                'eyebrow' => 'Mercado de capitais',
                'title' => 'Simulador de Mercado de Capitais',
                'description' => 'Teste cenários para debentures, CRA, CRI e outras estruturas de captação fora do crédito bancário.',
                'cta' => 'Explorar estrutura',
            ],
            'simulador-de-custo-de-antecipacao' => [
                'label' => 'FIDC',
                'eyebrow' => 'Recebíveis e antecipação',
                'title' => 'Simulador de Custo de Antecipação',
                'description' => 'Compare desconto bancário e FIDC para decidir a alternativa mais eficiente de antecipação de recebíveis.',
                'cta' => 'Comparar custos',
            ],
            'simulador-consorcio' => [
                'label' => 'CONS',
                'eyebrow' => 'Consórcio estruturado',
                'title' => 'Simulador de Consórcio Estratégico',
                'description' => 'Teste cenários para investimento, compra planejada e comparação com financiamento antes de falar com o especialista.',
                'cta' => 'Simular consórcio',
            ],
        ];

        $items = [];
        $alwaysAvailable = ['simulador-de-risco-cambial', 'fx-loan'];
        foreach ($definitions as $slug => $item) {
            if (in_array($slug, $alwaysAvailable, true)) {
                $item['slug'] = $slug;
                $item['url'] = $this->langUrl($slug);
                $items[] = $item;
                continue;
            }

            $page = $pageModel->getPageByLang($slug, $this->langId);
            if (!empty($page) && (int)$page->visibility === 1) {
                if ($slug !== 'simulador-consorcio' && !empty($page->title)) {
                    $item['title'] = $page->title;
                }
                if ($slug !== 'simulador-consorcio' && !empty($page->description)) {
                    $item['description'] = $page->description;
                }
                $item['slug'] = $page->slug;
                $item['url'] = $this->langUrl($page->slug);
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getSimulatorTopics()
    {
        return [
            [
                'label' => 'FX',
                'badge' => 'Frente dedicada',
                'eyebrow' => 'Câmbio e trade finance',
                'title' => 'Hub de simuladores de câmbio',
                'description' => 'Reúne estudos para importação, exportação, hedge, trade finance e 4131 em uma jornada única de decisão.',
                'cta' => 'Explorar câmbio',
                'path' => '/simuladores/cambio',
                'url' => $this->langUrl('simuladores/cambio'),
            ],
            [
                'label' => 'CONS',
                'badge' => 'Página dedicada',
                'eyebrow' => 'Consórcio estruturado',
                'title' => 'Frente de consórcio estratégico',
                'description' => 'Acesse a jornada dedicada de consórcio para comparar contemplação, fluxo e diferença frente ao financiamento.',
                'cta' => 'Abrir consórcio',
                'path' => '/simulador-consorcio',
                'url' => $this->resolvePageUrl('simulador-consorcio', $this->langUrl('simulador-consorcio')),
            ],
            [
                'label' => 'CAP',
                'badge' => 'Catálogo aberto',
                'eyebrow' => 'Crédito, mercado e recebíveis',
                'title' => 'Ferramentas de funding e capital',
                'description' => 'Explore ferramentas de custo de capital, mercado de capitais e antecipação para amadurecer a conversa financeira.',
                'cta' => 'Ver catálogo',
                'path' => '#catalogo-completo',
                'url' => '#catalogo-completo',
            ],
        ];
    }

    public function getFxLegacySimulatorCatalog()
    {
        $legacySlugs = ['simulador-de-risco-cambial', 'fx-loan'];
        $items = array_values(array_filter($this->getSimulatorCatalog(), static function ($item) use ($legacySlugs) {
            return in_array((string)($item['slug'] ?? ''), $legacySlugs, true);
        }));

        usort($items, static function ($a, $b) use ($legacySlugs) {
            return array_search((string)($a['slug'] ?? ''), $legacySlugs, true) <=> array_search((string)($b['slug'] ?? ''), $legacySlugs, true);
        });

        return $items;
    }

    protected function resolveCategoryUrl($slug, $fallback)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->getCategoryBySlug($slug);
        if (!empty($category) && function_exists('generateCategoryURL')) {
            return generateCategoryURL($category);
        }
        return $fallback;
    }

    protected function resolvePageUrl($slug, $fallback)
    {
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang($slug, $this->langId);
        if (!empty($page) && (int)$page->visibility === 1) {
            return $this->langUrl($page->slug);
        }
        return $fallback;
    }

    protected function langUrl($path = '')
    {
        if (function_exists('langBaseUrl')) {
            return langBaseUrl($path);
        }
        return $this->baseUrl($path);
    }

    protected function baseUrl($path = '')
    {
        if (function_exists('base_url')) {
            return base_url($path);
        }
        return '/' . ltrim((string)$path, '/');
    }
}
