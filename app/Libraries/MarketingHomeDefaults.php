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
                'title' => lang('MarketingHome.vert_c1_title'),
                'eyebrow' => lang('MarketingHome.vert_c1_eyebrow'),
                'description' => lang('MarketingHome.vert_c1_desc'),
                'link_label' => lang('MarketingHome.vert_c1_link'),
                'link_url' => $this->resolveCategoryUrl('credito-empresarial', $this->langUrl('simuladores')),
                'accent' => '#0f766e',
            ],
            [
                'enabled' => 1,
                'title' => lang('MarketingHome.vert_c2_title'),
                'eyebrow' => lang('MarketingHome.vert_c2_eyebrow'),
                'description' => lang('MarketingHome.vert_c2_desc'),
                'link_label' => lang('MarketingHome.vert_c2_link'),
                'link_url' => $this->langUrl('simuladores/cambio'),
                'accent' => '#0b5cab',
            ],
            [
                'enabled' => 1,
                'title' => lang('MarketingHome.vert_c3_title'),
                'eyebrow' => lang('MarketingHome.vert_c3_eyebrow'),
                'description' => lang('MarketingHome.vert_c3_desc'),
                'link_label' => lang('MarketingHome.vert_c3_link'),
                'link_url' => $this->resolvePageUrl('simulador-consorcio', $this->langUrl('simuladores')),
                'accent' => '#8f5b2e',
            ],
            [
                'enabled' => 1,
                'title' => lang('MarketingHome.vert_c4_title'),
                'eyebrow' => lang('MarketingHome.vert_c4_eyebrow'),
                'description' => lang('MarketingHome.vert_c4_desc'),
                'link_label' => lang('MarketingHome.vert_c4_link'),
                'link_url' => '#fale-especialista',
                'accent' => '#b45309',
            ],
            [
                'enabled' => 1,
                'title' => lang('MarketingHome.vert_c5_title'),
                'eyebrow' => lang('MarketingHome.vert_c5_eyebrow'),
                'description' => lang('MarketingHome.vert_c5_desc'),
                'link_label' => lang('MarketingHome.vert_c5_link'),
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
                'eyebrow' => lang('MarketingHome.sim_fx_eyebrow'),
                'title' => lang('MarketingHome.sim_fx_title'),
                'description' => lang('MarketingHome.sim_fx_desc'),
                'cta' => lang('MarketingHome.sim_fx_cta'),
            ],
            'fx-loan' => [
                'label' => '4131',
                'eyebrow' => lang('MarketingHome.sim_loan_eyebrow'),
                'title' => lang('MarketingHome.sim_loan_title'),
                'description' => lang('MarketingHome.sim_loan_desc'),
                'cta' => lang('MarketingHome.sim_loan_cta'),
            ],
            'aurum-simulador-de-custo-de-capital' => [
                'label' => 'CAP',
                'eyebrow' => lang('MarketingHome.sim_cap_eyebrow'),
                'title' => lang('MarketingHome.sim_cap_title'),
                'description' => lang('MarketingHome.sim_cap_desc'),
                'cta' => lang('MarketingHome.sim_cap_cta'),
            ],
            'simulador-mercado-de-capitais' => [
                'label' => 'MKT',
                'eyebrow' => lang('MarketingHome.sim_mkt_eyebrow'),
                'title' => lang('MarketingHome.sim_mkt_title'),
                'description' => lang('MarketingHome.sim_mkt_desc'),
                'cta' => lang('MarketingHome.sim_mkt_cta'),
            ],
            'simulador-de-custo-de-antecipacao' => [
                'label' => 'FIDC',
                'eyebrow' => lang('MarketingHome.sim_fidc_eyebrow'),
                'title' => lang('MarketingHome.sim_fidc_title'),
                'description' => lang('MarketingHome.sim_fidc_desc'),
                'cta' => lang('MarketingHome.sim_fidc_cta'),
            ],
            'simulador-consorcio' => [
                'label' => 'CONS',
                'eyebrow' => lang('MarketingHome.sim_cons_eyebrow'),
                'title' => lang('MarketingHome.sim_cons_title'),
                'description' => lang('MarketingHome.sim_cons_desc'),
                'cta' => lang('MarketingHome.sim_cons_cta'),
            ],
            'simulador-seguro-resgatavel' => [
                'label' => 'VIDA',
                'eyebrow' => lang('MarketingHome.sim_vida_eyebrow'),
                'title' => lang('MarketingHome.sim_vida_title'),
                'description' => lang('MarketingHome.sim_vida_desc'),
                'cta' => lang('MarketingHome.sim_vida_cta'),
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
                'badge' => lang('MarketingHome.topic_fx_badge'),
                'eyebrow' => lang('MarketingHome.topic_fx_eyebrow'),
                'title' => lang('MarketingHome.topic_fx_title'),
                'description' => lang('MarketingHome.topic_fx_desc'),
                'cta' => lang('MarketingHome.topic_fx_cta'),
                'path' => '/simuladores/cambio',
                'url' => $this->langUrl('simuladores/cambio'),
            ],
            [
                'label' => 'CONS',
                'badge' => lang('MarketingHome.topic_cons_badge'),
                'eyebrow' => lang('MarketingHome.topic_cons_eyebrow'),
                'title' => lang('MarketingHome.topic_cons_title'),
                'description' => lang('MarketingHome.topic_cons_desc'),
                'cta' => lang('MarketingHome.topic_cons_cta'),
                'path' => '/simulador-consorcio',
                'url' => $this->resolvePageUrl('simulador-consorcio', $this->langUrl('simulador-consorcio')),
            ],
            [
                'label' => 'CAP',
                'badge' => lang('MarketingHome.topic_cap_badge'),
                'eyebrow' => lang('MarketingHome.topic_cap_eyebrow'),
                'title' => lang('MarketingHome.topic_cap_title'),
                'description' => lang('MarketingHome.topic_cap_desc'),
                'cta' => lang('MarketingHome.topic_cap_cta'),
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
