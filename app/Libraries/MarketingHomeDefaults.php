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
                'primary_cta_label' => lang('MarketingHome.cfg_nav_cta'),
                'primary_cta_url' => '#fale-especialista',
                'quick_links' => [
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_nav_ql1'), 'href' => '#verticais'],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_nav_ql2'), 'href' => '#simuladores'],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_nav_ql3'), 'href' => '#clipping-gx'],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_nav_ql4'), 'href' => '#blog-tecnico'],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_nav_ql5'), 'href' => '#fale-especialista'],
                ],
            ],
            'hero_stats_labels' => [
                'simulators' => lang('MarketingHome.cfg_stat_simulators'),
                'verticals' => lang('MarketingHome.cfg_stat_verticals'),
                'insights' => lang('MarketingHome.cfg_stat_insights'),
            ],
            'hero' => [
                'enabled' => 1,
                'badge' => lang('MarketingHome.cfg_hero_badge'),
                'title' => lang('MarketingHome.cfg_hero_title'),
                'subtitle' => brandLang('MarketingHome.cfg_hero_subtitle'),
                'primary_cta_label' => lang('MarketingHome.cfg_hero_primary_cta'),
                'primary_cta_url' => '#fale-especialista',
                'secondary_cta_label' => lang('MarketingHome.cfg_hero_secondary_cta'),
                'secondary_cta_url' => $this->langUrl('simuladores'),
                'proof_items' => [
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_hero_proof1_title'), 'text' => lang('MarketingHome.cfg_hero_proof1_text')],
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_hero_proof2_title'), 'text' => lang('MarketingHome.cfg_hero_proof2_text')],
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_hero_proof3_title'), 'text' => lang('MarketingHome.cfg_hero_proof3_text')],
                ],
            ],
            'trust_strip' => [
                'enabled' => 1,
                'lead' => lang('MarketingHome.cfg_strip_lead'),
                'items' => [
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_strip1')],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_strip2')],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_strip3')],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_strip4')],
                    ['enabled' => 1, 'label' => lang('MarketingHome.cfg_strip5')],
                ],
            ],
            'verticals' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_vert_label'),
                'title' => lang('MarketingHome.cfg_vert_title'),
                'description' => lang('MarketingHome.cfg_vert_desc'),
                'items' => $this->getBusinessVerticals(),
            ],
            'process' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_proc_label'),
                'title' => lang('MarketingHome.cfg_proc_title'),
                'description' => lang('MarketingHome.cfg_proc_desc'),
                'items' => [
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_proc1_title'), 'desc' => lang('MarketingHome.cfg_proc1_desc')],
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_proc2_title'), 'desc' => lang('MarketingHome.cfg_proc2_desc')],
                    ['enabled' => 1, 'title' => lang('MarketingHome.cfg_proc3_title'), 'desc' => lang('MarketingHome.cfg_proc3_desc')],
                ],
            ],
            'simulators' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_sim_label'),
                'title' => lang('MarketingHome.cfg_sim_title'),
                'description' => lang('MarketingHome.cfg_sim_desc'),
                'cta_label' => lang('MarketingHome.cfg_sim_cta'),
                'cta_url' => $this->langUrl('simuladores'),
            ],
            'clippings' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_clip_label'),
                'title' => brandLang('MarketingHome.cfg_clip_title'),
                'description' => brandLang('MarketingHome.cfg_clip_desc'),
                'item_cta_label' => lang('MarketingHome.cfg_clip_item_cta'),
                'items' => [],
            ],
            'partners' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_part_label'),
                'title' => brandLang('MarketingHome.cfg_part_title'),
                'description' => brandLang('MarketingHome.cfg_part_desc'),
                'items' => [],
            ],
            'blog' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_blog_label'),
                'title' => lang('MarketingHome.cfg_blog_title'),
                'description' => lang('MarketingHome.cfg_blog_desc'),
                'featured_cta_label' => lang('MarketingHome.cfg_blog_featured_cta'),
                'cta_label' => lang('MarketingHome.cfg_blog_cta'),
                'cta_url' => $this->langUrl('blog'),
            ],
            'cta' => [
                'enabled' => 1,
                'label' => lang('MarketingHome.cfg_cta_label'),
                'title' => lang('MarketingHome.cfg_cta_title'),
                'description' => brandLang('MarketingHome.cfg_cta_desc'),
                'primary_cta_label' => lang('MarketingHome.cfg_cta_primary'),
                'primary_cta_url' => '#fale-especialista',
                'secondary_cta_label' => lang('MarketingHome.cfg_cta_secondary'),
                'secondary_cta_url' => $this->langUrl('simuladores'),
            ],
            'lead' => [
                'enabled' => 1,
                'label' => brandLang('MarketingHome.cfg_lead_label'),
                'title' => lang('MarketingHome.cfg_lead_title'),
                'description' => lang('MarketingHome.cfg_lead_desc'),
                'form_heading' => brandLang('MarketingHome.cfg_lead_form_heading'),
                'form_description' => lang('MarketingHome.cfg_lead_form_desc'),
                'form_button_label' => lang('MarketingHome.cfg_lead_form_button'),
                'message_placeholder' => lang('MarketingHome.cfg_lead_msg_ph'),
                'show_phone' => 1,
                'show_email' => 1,
                'show_simulators_chip' => 1,
                'show_blog_chip' => 1,
                'simulators_chip_label' => lang('MarketingHome.cfg_lead_sim_chip'),
                'blog_chip_label' => lang('MarketingHome.cfg_lead_blog_chip'),
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
            'simulador-seguro-resgatavel' => [
                'label' => 'VIDA',
                'eyebrow' => 'Proteção e patrimônio',
                'title' => 'Simulador de Seguro de Vida Resgatável',
                'description' => 'Veja a projeção de um seguro de vida resgatável quitado em 10 anos: o ponto em que a reserva acumulada ultrapassa tudo que você pagou.',
                'cta' => 'Projetar reserva',
            ],
        ];

        $items = [];
        $alwaysAvailable = ['simulador-de-risco-cambial', 'fx-loan', 'simulador-seguro-resgatavel'];
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
