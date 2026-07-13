<?php

namespace App\Controllers;

use App\Libraries\MarketingHomeDefaults;
use App\Libraries\MarketingSimulatorsDefaults;
use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\GalleryModel;
use App\Models\MarketingHomeModel;
use App\Models\PageModel;
use App\Models\PostAdminModel;
use App\Models\PostItemModel;
use App\Models\QuizModel;
use App\Models\ReactionModel;
use App\Models\RssModel;
use App\Models\TagModel;

class HomeController extends BaseController
{
    protected $postsPerPage;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->postsPerPage = $this->generalSettings->pagination_per_page;
    }

    public function index()
    {
        $simulators = $this->getSimulatorCatalog();
        $latestPosts = getOrSetStableCache('marketing_home_latest_posts_lang_' . $this->activeLang->id, function () {
            return array_slice($this->postModel->getLatestPosts($this->activeLang->id, 4, 0), 0, 4);
        }, 300);
        $marketingHomeModel = new MarketingHomeModel();
        $homeConfig = $marketingHomeModel->getHomeConfig($this->activeLang->id, $this->getHomeConfigDefaults());
        $contactChannels = $this->getMarketingContactChannels();
        $homeWhatsAppMessage = brandLang('Home.home_wa');
        $visibleQuickLinks = array_values(array_filter($homeConfig['nav']['quick_links'] ?? [], function ($item) {
            return !empty($item['enabled']) && (!empty($item['label']) || !empty($item['href']));
        }));
        $visibleBusinessVerticals = array_values(array_filter($homeConfig['verticals']['items'] ?? [], function ($item) {
            return !empty($item['enabled']);
        }));
        $heroStatLabels = $homeConfig['hero_stats_labels'] ?? [];
        $homeDescription = $this->buildHomeDescription($homeConfig);
        $socialImage = $this->resolveHomeSocialImage($homeConfig, $latestPosts);

        $data = [
            'title' => 'Câmbio estruturado, crédito e consultoria para empresas',
            'description' => $homeDescription,
            'keywords' => $this->settings->keywords,
            'bodyClass' => 'gx-marketing-home',
            'pageHeadView' => 'marketing/_shared_styles',
            'canonicalUrl' => langBaseUrl(),
            'socialImage' => $socialImage['url'],
            'socialImageWidth' => $socialImage['width'] ?? null,
            'socialImageHeight' => $socialImage['height'] ?? null,
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'wealthUrl' => base_url('wealth'),
            'quickLinks' => $visibleQuickLinks,
            'heroStats' => [
                ['value' => count($simulators), 'label' => $heroStatLabels['simulators'] ?? 'simuladores disponíveis'],
                ['value' => count($visibleBusinessVerticals), 'label' => $heroStatLabels['verticals'] ?? 'frentes de atuação'],
                ['value' => count($latestPosts), 'label' => $heroStatLabels['insights'] ?? 'análises recentes'],
            ],
            'businessVerticals' => $visibleBusinessVerticals,
            'simulators' => $simulators,
            'latestPosts' => $latestPosts,
            'homeConfig' => $homeConfig,
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $homeWhatsAppMessage),
            'whatsAppMessage' => $homeWhatsAppMessage,
            'marketingSchema' => $this->buildHomeMarketingSchema($homeConfig, $visibleBusinessVerticals, $homeDescription, $socialImage),
            'userSession' => getUserSession()
        ];

        echo view('marketing/_home_head', $data);
        echo view('marketing/home_institutional', $data);
        echo view('marketing/_home_footer', $data);
    }

    /**
     * Institutional Blog Home
     */
    public function blog()
    {
        $this->cachePage(300);
        $data = $this->buildEditorialHomeData([
            'title' => brandLang('Home.blog_title'),
            'description' => brandLang('Home.blog_description'),
            'keywords' => trim($this->settings->keywords . ', blog gx capital, conteúdo técnico, mercado financeiro', ' ,'),
            'homeTitle' => brandLang('Home.blog_title')
        ]);

        echo loadView('partials/_header', $data);
        echo loadView('index', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Simulators Hub
     */
    public function simulatorsHub()
    {
        $canonicalUrl = langBaseUrl('simuladores');
        $description = brandLang('Home.sim_hub_description');
        $topicFronts = $this->getSimulatorTopics();
        $simulators = $this->getSimulatorCatalog();
        $legacySimulators = $this->getFxLegacySimulatorCatalog();
        $contactChannels = $this->getMarketingContactChannels();
        $whatsAppMessage = brandLang('Home.sim_hub_wa');

        $data = [
            'title' => brandLang('Home.sim_hub_title'),
            'description' => characterLimiter(preg_replace('/\s+/', ' ', $description), 160, ''),
            'keywords' => trim($this->settings->keywords . ', central de simuladores, simuladores financeiros, simuladores de câmbio, simulador de consórcio, custo de capital', ' ,'),
            'pageHeadView' => 'marketing/_shared_styles',
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => getLogo(),
            'socialImageWidth' => (int)getLogoSize('width'),
            'socialImageHeight' => (int)getLogoSize('height'),
            'homeUrl' => langBaseUrl(),
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => $canonicalUrl,
            'simulatorsFxHubUrl' => langBaseUrl('simuladores/cambio'),
            'wealthUrl' => base_url('wealth'),
            'bodyClass' => 'gx-marketing-page',
            'simulators' => $simulators,
            'topicFronts' => $topicFronts,
            'legacySimulators' => $legacySimulators,
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $whatsAppMessage),
            'whatsAppMessage' => $whatsAppMessage,
            'hubStats' => [
                ['value' => count($simulators), 'label' => lang('Home.sim_hub_stat_tools')],
                ['value' => count($topicFronts), 'label' => lang('Home.sim_hub_stat_fronts')],
                ['value' => count($legacySimulators), 'label' => lang('Home.sim_hub_stat_studies')],
            ],
            'marketingSchema' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'CollectionPage',
                        '@id' => $canonicalUrl . '#webpage',
                        'url' => $canonicalUrl,
                        'name' => brandLang('Home.sim_hub_title'),
                        'description' => characterLimiter(preg_replace('/\s+/', ' ', $description), 160, ''),
                        'inLanguage' => $this->activeLang->language_code,
                        'isPartOf' => ['@id' => base_url() . '/#website'],
                    ],
                ],
            ],
            'userSession' => getUserSession()
        ];

        echo view('marketing/_home_head', $data);
        echo view('marketing/simulators_hub', $data);
        echo view('marketing/_home_footer', $data);
    }

    /**
     * FX Simulators Hub
     */
    public function simulatorsFxHub()
    {
        $marketingHomeModel = new MarketingHomeModel();
        $pageConfig = $marketingHomeModel->getSimulatorsHubConfig($this->activeLang->id, $this->getSimulatorsConfigDefaults());
        $description = trim((string)($pageConfig['hero']['subtitle'] ?? ''));
        if ($description === '') {
            $description = brandLang('Home.fx_hub_description_fallback');
        }
        $canonicalUrl = langBaseUrl('simuladores/cambio');
        $contactChannels = $this->getMarketingContactChannels();
        $defaultWhatsAppMessage = brandLang('Home.fx_wa_default');
        $whatsAppMessagesByTool = [
            'import' => brandLang('Home.fx_wa_import'),
            'export' => brandLang('Home.fx_wa_export'),
            'hedge' => brandLang('Home.fx_wa_hedge'),
            'funding4131' => brandLang('Home.fx_wa_funding4131'),
            'trade' => brandLang('Home.fx_wa_trade'),
        ];
        $whatsAppBaseUrl = '';
        if (!empty($contactChannels['whatsapp_digits'])) {
            $whatsAppBaseUrl = 'https://wa.me/' . $contactChannels['whatsapp_digits'];
        }

        $brandName = brand('display_name', 'GX Capital');
        $fxFaqEntities = array_map(static function (array $item) use ($brandName) {
            return [
                '@type' => 'Question',
                'name' => strtr($item['q'], ['{brand}' => $brandName]),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strtr($item['a'], ['{brand}' => $brandName]),
                ],
            ];
        }, lang('Home.fx_hub_faq'));

        $data = [
            'title' => lang('Home.fx_hub_title'),
            'description' => characterLimiter(preg_replace('/\s+/', ' ', $description), 160, ''),
            'keywords' => trim($this->settings->keywords . lang('Home.fx_hub_keywords_extra'), ' ,'),
            'pageHeadView' => 'marketing/_shared_styles',
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => getLogo(),
            'socialImageWidth' => (int)getLogoSize('width'),
            'socialImageHeight' => (int)getLogoSize('height'),
            'homeUrl' => langBaseUrl(),
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'simulatorsFxHubUrl' => $canonicalUrl,
            'wealthUrl' => base_url('wealth'),
            'bodyClass' => 'gx-marketing-page',
            'legacySimulators' => $this->getFxLegacySimulatorCatalog(),
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $defaultWhatsAppMessage),
            'whatsAppBaseUrl' => $whatsAppBaseUrl,
            'whatsAppDefaultMessage' => $defaultWhatsAppMessage,
            'whatsAppMessagesByTool' => $whatsAppMessagesByTool,
            'pageConfig' => $pageConfig,
            'marketingSchema' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebPage',
                        '@id' => $canonicalUrl . '#webpage',
                        'url' => $canonicalUrl,
                        'name' => trim((string)($pageConfig['hero']['title'] ?? brandLang('Home.fx_hub_schema_webpage_name'))),
                        'description' => characterLimiter(preg_replace('/\s+/', ' ', $description), 160, ''),
                        'inLanguage' => $this->activeLang->language_code,
                        'isPartOf' => ['@id' => base_url() . '/#website'],
                        'about' => ['@id' => $canonicalUrl . '#service'],
                    ],
                    [
                        '@type' => 'FinancialService',
                        '@id' => $canonicalUrl . '#service',
                        'name' => brandLang('Home.fx_hub_schema_service_name'),
                        'provider' => ['@id' => base_url() . '/#organization'],
                        'employee' => ['@id' => base_url() . '/#person-vinicius-teixeira'],
                        'serviceType' => [
                            'Câmbio estruturado',
                            'Hedge cambial',
                            'Trade finance',
                            'Operações 4131',
                        ],
                        'areaServed' => 'BR',
                        'description' => lang('Home.fx_hub_schema_service_description'),
                    ],
                    [
                        '@type' => 'FAQPage',
                        '@id' => $canonicalUrl . '#faq',
                        'mainEntity' => $fxFaqEntities,
                    ],
                ],
            ],
            'userSession' => getUserSession()
        ];

        echo view('marketing/_home_head', $data);
        echo view('marketing/simulators_fx_hub', $data);
        echo view('marketing/_home_footer', $data);
    }

    public function simulatorsFxLegacyRedirect()
    {
        return redirect()->to(langBaseUrl('simuladores/cambio'), 301);
    }

    /**
     * Slugs de simulador legados/quebrados -> 301 para o slug canônico (Fase 6 GEO/SEO).
     * Registrado como rotas GET em Config\Routes (NÃO via addRedirect): o addRedirect
     * grava no verbo `*`, mas getRoutes() coloca as rotas do verbo GET antes das `*`,
     * então o catch-all `(:any)` -> HomeController::any casaria primeiro e devolveria 404.
     */
    public const LEGACY_SIMULATOR_REDIRECTS = [
        'simulador-risco-cambial-fx-loan-4131' => 'simuladores/cambio',
        'simulador-risco-cambial'              => 'simuladores/cambio',
        'simulador-fx-loan-4131'               => 'simuladores/cambio',
        'fx-loan-4131'                         => 'simuladores/cambio',
        'simulador-aurum-custo-de-capital'     => 'aurum-simulador-de-custo-de-capital',
        'simulador-custo-de-capital'           => 'aurum-simulador-de-custo-de-capital',
        'simulador-credito-empresarial'        => 'aurum-simulador-de-custo-de-capital',
        'simulador-antecipacao-fidc'           => 'simulador-de-custo-de-antecipacao',
        'simulador-bndes'                      => 'linhas-credito-bndes',
        // Variantes quebradas do site audit (Ubersuggest, 2026-07): links internos
        // gerados pela pipeline de IA apontando p/ slugs de simulador inexistentes.
        'aurum-custo-de-capital'                    => 'aurum-simulador-de-custo-de-capital',
        'simulador-custo-capital-aurum'             => 'aurum-simulador-de-custo-de-capital',
        'aurum-simulador-de-custo-de-capital-aurum' => 'aurum-simulador-de-custo-de-capital',
        'simulador-mercado-capitais'                => 'simulador-mercado-de-capitais',
        'mercado-de-capitais'                       => 'simulador-mercado-de-capitais',
        'simulador-fidc'                            => 'simulador-de-custo-de-antecipacao',
    ];

    public function legacyRedirect()
    {
        $segments = explode('/', trim(uri_string(), '/'));
        $slug     = end($segments);
        $target   = self::LEGACY_SIMULATOR_REDIRECTS[$slug] ?? 'simuladores';

        return redirect()->to(langBaseUrl($target), 301);
    }

    /**
     * Conteúdo duplicado detectado no site audit (Ubersuggest) -> 301 do duplicado
     * para o canônico, consolidando sinais de ranking. Dois casos:
     *  - 6 pares de POSTS gêmeos: ao colidir o slug, o CMS anexou o ID do post; o ID
     *    menor é o original (mais antigo/mais pageviews), o sufixo -<id> é o clone.
     *  - 1 canibalização de "spread bancário": consolida no artigo mais completo
     *    (id 177, 3x conteúdo, rankeia os head terms) o mais fraco (id 78).
     * Os slugs de origem também recebem visibility=0 (saem do sitemap/listagens, para
     * não deixar URL que 301 no sitemap — ver Fase 3). Mesmo padrão de rota GET da
     * Fase 6 (ver LEGACY_SIMULATOR_REDIRECTS) porque addRedirect é anulado pelo catch-all.
     */
    public const DUPLICATE_CONTENT_REDIRECTS = [
        'china-turbina-compras-de-petroleo-do-ira-e-russia-o-que-isso-muda-para-o-brasil-20'              => 'china-turbina-compras-de-petroleo-do-ira-e-russia-o-que-isso-muda-para-o-brasil',
        'dolar-forte-real-fraco-termos-de-troca-explicam-2026-guia-pratico-252'                           => 'dolar-forte-real-fraco-termos-de-troca-explicam-2026-guia-pratico',
        'gx-explica-trade-finance-cartas-de-credito-accace-e-sblc-para-impulsionar-comercio-exterior-67'  => 'gx-explica-trade-finance-cartas-de-credito-accace-e-sblc-para-impulsionar-comercio-exterior',
        'gx-insights-guia-definitivo-do-consorcio-empresarial-86'                                         => 'gx-insights-guia-definitivo-do-consorcio-empresarial',
        'itau-preve-desaceleracao-do-credito-o-que-isso-significa-para-sua-empresa-38'                    => 'itau-preve-desaceleracao-do-credito-o-que-isso-significa-para-sua-empresa',
        'selic-a-1475-analise-da-gx-capital-sobre-a-ata-do-copom-e-o-cenario-economico-atual-29'          => 'selic-a-1475-analise-da-gx-capital-sobre-a-ata-do-copom-e-o-cenario-economico-atual',
        'spread-bancario-como-e-formado-e-5-estrategias-para-pagar-menos-no-credito-empresarial'          => 'o-que-e-spread-bancario-e-por-que-o-credito-e-tao-caro-no-brasil',
        // Slug renomeado (id 68): o "&" do título ("Defesa de Crédito & Risco") virou
        // "ampamp" no slug (double-encoding). Renomeado p/ a versão limpa; 301 do antigo.
        'guia-de-defesa-de-credito-ampamp-risco-ferramentas-processos-e-exemplos-para-blindar-fluxo-de-caixa' => 'guia-de-defesa-de-credito-risco-ferramentas-processos-e-exemplos-para-blindar-fluxo-de-caixa',
    ];

    public function duplicateRedirect()
    {
        $segments = explode('/', trim(uri_string(), '/'));
        $slug     = end($segments);
        $target   = self::DUPLICATE_CONTENT_REDIRECTS[$slug] ?? '';

        return redirect()->to(langBaseUrl($target), 301);
    }

    /**
     * Playbook (ebook interativo) — Importação Blindada · 2026
     * Landing dedicada para tráfego pago capturando leads de importadores/exportadores.
     */
    public function playbookImportacaoBlindada()
    {
        $canonicalUrl = base_url('playbook/importacao-blindada');
        $contactChannels = $this->getMarketingContactChannels();
        $whatsAppMessage = brandLang('Home.pb_imp_wa');

        $description = brandLang('Home.pb_imp_description');

        $playbookConfig = [
            'id' => 'importacao-blindada',
            'title' => lang('Home.pb_imp_title'),
            'description' => $description,
            'canonicalUrl' => $canonicalUrl,
            'section' => lang('Home.pb_imp_section'),
            'keywords' => brandLang('Home.pb_imp_keywords'),
            'datePublished' => '2026-05-08T08:00:00-03:00',
            'dateModified'  => '2026-05-08T08:00:00-03:00',
            'readingTimeMin' => 18,
            'wordCount' => 4200,
            'image' => getLogo(),
            'breadcrumb' => [
                ['name' => lang('Home.pb_breadcrumb_home'),     'url' => base_url()],
                ['name' => lang('Home.pb_breadcrumb_playbooks'),  'url' => base_url('playbook')],
                ['name' => lang('Home.pb_imp_breadcrumb_current'), 'url' => $canonicalUrl],
            ],
            'chapters' => $this->brandInterp(lang('Home.pb_imp_chapters')),
            'faq' => $this->brandInterp(lang('Home.pb_imp_faq')),
        ];

        $data = [
            'title' => $playbookConfig['title'],
            'description' => $description,
            'keywords' => $playbookConfig['keywords'],
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => getLogo(),
            'socialImageWidth' => (int)getLogoSize('width'),
            'socialImageHeight' => (int)getLogoSize('height'),
            'homeUrl' => langBaseUrl(),
            'simuladorUrl' => langBaseUrl('simuladores/cambio'),
            'specialistUrl' => langBaseUrl('simuladores/cambio') . '#contato',
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $whatsAppMessage),
            'playbookConfig' => $playbookConfig,
        ];

        if (function_exists('trackMetaPageView')) {
            try { trackMetaPageView($canonicalUrl); } catch (\Throwable $e) {}
        }
        if (function_exists('trackMetaEvent')) {
            try {
                trackMetaEvent('ViewContent', [
                    'content_name' => 'Playbook Importação Blindada',
                    'content_category' => 'Câmbio · Importação',
                    'content_type' => 'playbook',
                    'currency' => 'BRL',
                ]);
            } catch (\Throwable $e) {}
        }

        return view('marketing/playbook_importacao_blindada', $data);
    }

    /**
     * Playbook (ebook interativo) — Exportação Premium · 2026
     * Landing dedicada para tráfego pago capturando leads de exportadores.
     */
    public function playbookExportacaoPremium()
    {
        $canonicalUrl = base_url('playbook/exportacao-premium');
        $contactChannels = $this->getMarketingContactChannels();
        $whatsAppMessage = brandLang('Home.pb_exp_wa');

        $description = brandLang('Home.pb_exp_description');

        $playbookConfig = [
            'id' => 'exportacao-premium',
            'title' => lang('Home.pb_exp_title'),
            'description' => $description,
            'canonicalUrl' => $canonicalUrl,
            'section' => lang('Home.pb_exp_section'),
            'keywords' => brandLang('Home.pb_exp_keywords'),
            'datePublished' => '2026-05-08T08:00:00-03:00',
            'dateModified'  => '2026-05-08T08:00:00-03:00',
            'readingTimeMin' => 18,
            'wordCount' => 4400,
            'image' => getLogo(),
            'breadcrumb' => [
                ['name' => lang('Home.pb_breadcrumb_home'),     'url' => base_url()],
                ['name' => lang('Home.pb_breadcrumb_playbooks'),  'url' => base_url('playbook')],
                ['name' => lang('Home.pb_exp_breadcrumb_current'), 'url' => $canonicalUrl],
            ],
            'chapters' => $this->brandInterp(lang('Home.pb_exp_chapters')),
            'faq' => $this->brandInterp(lang('Home.pb_exp_faq')),
        ];

        $data = [
            'title' => $playbookConfig['title'],
            'description' => $description,
            'keywords' => $playbookConfig['keywords'],
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => getLogo(),
            'socialImageWidth' => (int)getLogoSize('width'),
            'socialImageHeight' => (int)getLogoSize('height'),
            'homeUrl' => langBaseUrl(),
            'simuladorUrl' => langBaseUrl('simuladores/cambio'),
            'specialistUrl' => langBaseUrl('simuladores/cambio') . '#contato',
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $whatsAppMessage),
            'playbookConfig' => $playbookConfig,
        ];

        if (function_exists('trackMetaPageView')) {
            try { trackMetaPageView($canonicalUrl); } catch (\Throwable $e) {}
        }
        if (function_exists('trackMetaEvent')) {
            try {
                trackMetaEvent('ViewContent', [
                    'content_name' => 'Playbook Exportação Premium',
                    'content_category' => 'Câmbio · Exportação',
                    'content_type' => 'playbook',
                    'currency' => 'BRL',
                ]);
            } catch (\Throwable $e) {}
        }

        return view('marketing/playbook_exportacao_premium', $data);
    }

    /**
     * Posts Page
     */
    public function posts()
    {
        $data = setPageMeta(trans("posts"));
        $data['userSession'] = getUserSession();
        $numRows = $this->postModel->getPostCount($this->activeLang->id);
        $data['pager'] = paginate($this->postsPerPage, $numRows);
        $data['posts'] = $this->postModel->getPostsPaginated($this->activeLang->id, $this->postsPerPage, $data['pager']->offset);

        echo loadView('partials/_header', $data);
        echo loadView('post/posts', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Build editorial blog homepage data
     */
    private function buildEditorialHomeData(array $meta = [])
    {
        $data = [
            'title' => $meta['title'] ?? $this->settings->home_title,
            'description' => $meta['description'] ?? $this->settings->site_description,
            'keywords' => $meta['keywords'] ?? $this->settings->keywords,
            'homeTitle' => $meta['homeTitle'] ?? brandLang('Home.blog_title'),
            'latestPosts' => $this->postModel->getLatestPosts($this->activeLang->id, POST_NUM_LOAD_MORE, 0)
        ];

        $data['sliderPosts'] = $data['latestPosts'];
        if ($this->generalSettings->show_latest_posts_on_slider != 1) {
            $data['sliderPosts'] = getSelectedPostsByType($this->postsSelected, 'slider');
        }

        $data['featuredPosts'] = $data['latestPosts'];
        if ($this->generalSettings->show_latest_posts_on_featured != 1) {
            $data['featuredPosts'] = getSelectedPostsByType($this->postsSelected, 'featured');
        }

        $data['breakingNews'] = getSelectedPostsByType($this->postsSelected, 'breaking');
        $data['userSession'] = getUserSession();

        return $data;
    }

    /**
     * Business vertical cards for the institutional homepage
     */
    private function getBusinessVerticals()
    {
        $defaults = new MarketingHomeDefaults($this->activeLang->id);
        return $defaults->getBusinessVerticals();
    }

    /**
     * Existing public simulator pages that must keep their legacy URLs
     */
    private function getSimulatorCatalog()
    {
        $defaults = new MarketingHomeDefaults($this->activeLang->id);
        return $defaults->getSimulatorCatalog();
    }

    private function getSimulatorTopics()
    {
        $defaults = new MarketingHomeDefaults($this->activeLang->id);
        return $defaults->getSimulatorTopics();
    }

    private function getFxLegacySimulatorCatalog()
    {
        $defaults = new MarketingHomeDefaults($this->activeLang->id);
        return $defaults->getFxLegacySimulatorCatalog();
    }

    private function getHomeConfigDefaults()
    {
        $defaults = new MarketingHomeDefaults($this->activeLang->id);
        return $defaults->getHomeConfigDefaults();
    }

    private function getSimulatorsConfigDefaults()
    {
        $defaults = new MarketingSimulatorsDefaults($this->activeLang->id);
        return $defaults->getPageConfigDefaults();
    }

    private function getMarketingContactChannels()
    {
        $contactInfo = [];
        if (!empty($this->generalSettings->contact_info)) {
            $decoded = json_decode($this->generalSettings->contact_info, true);
            if (is_array($decoded)) {
                $contactInfo = $decoded;
            }
        }

        $phone = trim((string)($contactInfo['contact_phone'] ?? ($this->settings->contact_phone ?? '')));
        $email = trim((string)($contactInfo['contact_email'] ?? ($this->settings->contact_email ?? '')));
        $whatsAppRaw = trim((string)($contactInfo['contact_whatsapp'] ?? ''));
        if ($whatsAppRaw === '') {
            $whatsAppRaw = $phone;
        }

        return [
            'phone' => $phone,
            'phone_href' => $phone !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $phone) : '',
            'email' => $email,
            'whatsapp_raw' => $whatsAppRaw,
            'whatsapp_digits' => $this->normalizeWhatsAppNumber($whatsAppRaw),
        ];
    }

    private function normalizeWhatsAppNumber($value)
    {
        $digits = preg_replace('/\D+/', '', (string)$value);
        if ($digits === '') {
            return '';
        }
        if (strpos($digits, '00') === 0) {
            $digits = substr($digits, 2);
        }
        if (!preg_match('/^55/', $digits) && (strlen($digits) === 10 || strlen($digits) === 11)) {
            $digits = '55' . $digits;
        }
        return $digits;
    }

    private function buildWhatsAppUrl($digits, $message)
    {
        $digits = trim((string)$digits);
        if ($digits === '') {
            return '';
        }
        return 'https://wa.me/' . $digits . '?text=' . rawurlencode((string)$message);
    }

    /**
     * Substitui o token {brand} pelo nome da marca em strings ou arrays (recursivo).
     * Complementa brandLang() (que só trata strings escalares) para os arrays de
     * conteúdo i18n (capítulos, FAQ) dos playbooks/schema. Byte-safe (só strtr).
     */
    private function brandInterp($value)
    {
        $brandName = brand('display_name', 'GX Capital');
        if (is_array($value)) {
            array_walk_recursive($value, static function (&$v) use ($brandName) {
                if (is_string($v)) {
                    $v = strtr($v, ['{brand}' => $brandName]);
                }
            });
            return $value;
        }
        return is_string($value) ? strtr($value, ['{brand}' => $brandName]) : $value;
    }

    private function buildHomeDescription(array $homeConfig)
    {
        $description = trim((string)($homeConfig['hero']['subtitle'] ?? ''));
        if ($description !== '') {
            $description = preg_replace('/\s+/', ' ', $description);
            return characterLimiter($description, 160, '');
        }

        return 'GX Capital oferece soluções em câmbio estruturado, crédito, proteção patrimonial, seguros, wealth e consultoria estratégica para empresas e famílias.';
    }

    private function resolveHomeSocialImage(array $homeConfig, array $latestPosts)
    {
        $clippingItems = $homeConfig['clippings']['items'] ?? [];
        foreach ($clippingItems as $item) {
            if (!empty($item['enabled']) && !empty($item['image_url'])) {
                return [
                    'url' => $item['image_url'],
                    'width' => 1600,
                    'height' => 1200,
                ];
            }
        }

        foreach ($latestPosts as $post) {
            if (checkPostImg($post)) {
                return [
                    'url' => getPostImage($post, 'big'),
                    'width' => 870,
                    'height' => 580,
                ];
            }
        }

        return [
            'url' => getLogo(),
            'width' => (int)getLogoSize('width'),
            'height' => (int)getLogoSize('height'),
        ];
    }

    private function buildHomeMarketingSchema(array $homeConfig, array $visibleBusinessVerticals, $description, array $socialImage)
    {
        $serviceTypes = array_values(array_filter(array_map(static function ($item) {
            return trim((string)($item['title'] ?? ''));
        }, $visibleBusinessVerticals)));

        $pageName = trim((string)($homeConfig['hero']['title'] ?? ''));
        if ($pageName === '') {
            $pageName = brand('display_name', 'GX Capital');
        }

        $webPageSchema = [
            '@type' => 'WebPage',
            '@id' => langBaseUrl() . '#webpage',
            'url' => langBaseUrl(),
            'name' => $pageName,
            'description' => $description,
            'inLanguage' => $this->activeLang->language_code,
            'isPartOf' => ['@id' => base_url() . '/#website'],
            'about' => ['@id' => langBaseUrl() . '#financial-service'],
        ];

        if (!empty($socialImage['url'])) {
            $webPageSchema['primaryImageOfPage'] = [
                '@type' => 'ImageObject',
                'url' => $socialImage['url'],
            ];
            if (!empty($socialImage['width'])) {
                $webPageSchema['primaryImageOfPage']['width'] = (int)$socialImage['width'];
            }
            if (!empty($socialImage['height'])) {
                $webPageSchema['primaryImageOfPage']['height'] = (int)$socialImage['height'];
            }
        }

        $financialServiceSchema = [
            '@type' => 'FinancialService',
            '@id' => langBaseUrl() . '#financial-service',
            'name' => brand('display_name', 'GX Capital'),
            'url' => langBaseUrl(),
            'description' => $description,
            'provider' => ['@id' => base_url() . '/#organization'],
            'areaServed' => 'BR',
        ];

        if (!empty($serviceTypes)) {
            $financialServiceSchema['serviceType'] = $serviceTypes;
        }

        if (!empty($socialImage['url'])) {
            $financialServiceSchema['image'] = $socialImage['url'];
        }

        // FAQPage schema — perguntas frequentes sobre câmbio e serviços
        $homeFaqItems = [
            ['q' => 'O que é câmbio estruturado?', 'a' => 'Câmbio estruturado é uma operação financeira que combina contratos de câmbio com instrumentos de proteção (hedge), permitindo que empresas importadoras e exportadoras travem taxas, reduzam exposição cambial e planejem fluxo de caixa com previsibilidade. A GX Capital estrutura essas operações sob medida para cada perfil de empresa.'],
            ['q' => 'Como funciona hedge cambial?', 'a' => 'Hedge cambial é uma estratégia de proteção contra variações na taxa de câmbio. Funciona por meio de contratos a termo (NDF), opções de câmbio ou swaps que permitem fixar uma taxa futura. Empresas que importam ou exportam usam hedge para eliminar o risco de oscilação do dólar sobre suas margens operacionais.'],
            ['q' => 'Quais serviços a GX Capital oferece para empresas?', 'a' => 'A GX Capital oferece câmbio estruturado, hedge cambial, trade finance, crédito corporativo, operações 4131 (funding internacional), consultoria em mercado de capitais e wealth advisory para patrimônio de famílias e empresários.'],
            ['q' => 'O que é uma operação 4131?', 'a' => 'A operação 4131 é um empréstimo internacional regulado pela Resolução 4131 do Banco Central, que permite a captação de recursos no exterior com taxas potencialmente mais competitivas. É indicada para empresas com exposição em moeda estrangeira ou que buscam diversificação de fontes de financiamento.'],
            ['q' => 'Como funciona a consultoria de wealth advisory da GX Capital?', 'a' => 'O wealth advisory da GX Capital oferece diagnóstico patrimonial, leitura de liquidez, tese de alocação e plano executivo de próximos passos para famílias, executivos e empresários. O processo começa com um mapeamento do patrimônio e fluxo de caixa, seguido de recomendações consultivas integradas.'],
        ];

        $faqEntities = [];
        foreach ($homeFaqItems as $i => $faq) {
            $faqEntities[] = [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['a'],
                ],
                'position' => $i + 1,
            ];
        }

        $faqPageSchema = [
            '@type' => 'FAQPage',
            '@id' => langBaseUrl() . '#faq',
            'mainEntity' => $faqEntities,
        ];

        return [
            '@context' => 'https://schema.org',
            '@graph' => [$webPageSchema, $financialServiceSchema, $faqPageSchema],
        ];
    }

    private function buildConsorcioSimulatorSchema(string $canonicalUrl, string $title, string $description)
    {
        $consorcioFaqItems = [
            [
                'q' => 'O que é consórcio estruturado?',
                'a' => 'Consórcio estruturado é uma estratégia que vai além de simplesmente aderir a um grupo. Envolve comparar dezenas de administradoras, analisar taxa administrativa, fundo de reserva, frequência de assembleias e definir uma estratégia de lance calculada para contemplar no prazo desejado. A GX Capital cruza mais de 20 administradoras e 1.000+ grupos com IA para encontrar a combinação mais eficiente.',
            ],
            [
                'q' => 'Consórcio ou financiamento: qual é mais vantajoso?',
                'a' => 'Depende de entrada disponível, urgência, custo total e capacidade de lance. O consórcio não cobra juros, mas tem taxa administrativa e fundo de reserva. O financiamento libera o crédito imediatamente, mas o custo total com juros compostos costuma ser significativamente maior. O simulador da GX Capital coloca os dois cenários lado a lado com números reais para que a decisão seja técnica.',
            ],
            [
                'q' => 'Como funciona a contemplação por lance?',
                'a' => 'Contemplação por lance é quando o consorciado oferece um valor antecipado (lance) na assembleia para antecipar a liberação da carta de crédito. O lance pode ser livre (valor que o participante define), fixo (pré-determinado pela administradora) ou embutido (usando parte da própria carta). A estratégia de lance ideal depende do grupo, da concorrência média e do caixa disponível.',
            ],
            [
                'q' => 'Consórcio serve como investimento?',
                'a' => 'Sim. A tese de investimento com consórcio funciona quando o consorciado contempla a carta de crédito, adquire o bem (geralmente imóvel) com desconto e revende com margem. O retorno líquido depende do custo de carregamento (parcelas até a contemplação), do tempo até a revenda e da margem de valorização. O simulador da GX Capital projeta ROI, break-even e custo real da operação.',
            ],
            [
                'q' => 'O que a GX Capital faz de diferente no consórcio?',
                'a' => 'A GX Capital é uma boutique financeira independente — não é administradora e não tem cota própria para vender. Isso permite recomendar o grupo, a administradora e a estratégia de lance mais eficiente para cada perfil, sem viés comercial. O simulador com IA filtra mais de 20 administradoras e gera um plano de contemplação personalizado que é validado por especialista antes da decisão.',
            ],
        ];

        $faqEntities = [];
        foreach ($consorcioFaqItems as $i => $faq) {
            $faqEntities[] = [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['a'],
                ],
                'position' => $i + 1,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl . '#webpage',
                    'url' => $canonicalUrl,
                    'name' => $title,
                    'description' => $description,
                    'inLanguage' => $this->activeLang->language_code,
                    'isPartOf' => ['@id' => base_url() . '/#website'],
                    'about' => ['@id' => $canonicalUrl . '#service'],
                ],
                [
                    '@type' => 'FinancialService',
                    '@id' => $canonicalUrl . '#service',
                    'name' => 'Simulação e planejamento estratégico de consórcio',
                    'description' => 'Boutique financeira independente que compara mais de 20 administradoras e 1.000+ grupos de consórcio com IA para encontrar a rota de contemplação mais eficiente para cada perfil.',
                    'serviceType' => [
                        'Consórcio para investimento com revenda',
                        'Compra planejada de imóvel com consórcio',
                        'Comparativo entre consórcio e financiamento',
                        'Planejamento de contemplação por lance',
                    ],
                    'provider' => ['@id' => base_url() . '/#organization'],
                    'employee' => ['@id' => base_url() . '/#person-vinicius-teixeira'],
                    'areaServed' => 'BR',
                ],
                [
                    '@type' => 'FAQPage',
                    '@id' => $canonicalUrl . '#faq',
                    'mainEntity' => $faqEntities,
                ],
            ],
        ];
    }

    /**
     * Schema JSON-LD (SoftwareApplication + BreadcrumbList) para as páginas de
     * simulador servidas pelo CMS via page(). Retorna [] para páginas que não são
     * simuladores. Usa título/descrição reais da página (sem inventar conteúdo).
     */
    private function buildSimulatorPageSchema($page, array $faqItems = []): array
    {
        $slug = (string) ($page->slug ?? '');
        $fallbackDescriptions = [
            'aurum-simulador-de-custo-de-capital' => 'Simulador de custo de capital (WACC) para empresas avaliarem o custo médio ponderado de suas fontes de financiamento.',
            'simulador-mercado-de-capitais'       => 'Simulador de operações de mercado de capitais para estruturação de dívida e captação empresarial.',
            'simulador-de-custo-de-antecipacao'   => 'Simulador do custo de antecipação de recebíveis: estima deságio, taxa e custo efetivo da operação.',
            'linhas-credito-bndes'                => 'Simulador de linhas de crédito BNDES para empresas compararem condições e enquadramento.',
        ];
        if (!isset($fallbackDescriptions[$slug])) {
            return [];
        }

        helper('jsonld');

        $url  = langBaseUrl($slug);
        $name = trim((string) ($page->title ?? '')) ?: 'Simulador GX Capital';

        $description = trim(strip_tags((string) ($page->description ?? '')));
        if ($description !== '') {
            $description = characterLimiter(preg_replace('/\s+/', ' ', $description), 300, '');
        } else {
            $description = $fallbackDescriptions[$slug];
        }

        $nodes = [
            jsonldSoftwareApplication($url, $name, $description),
            jsonldBreadcrumb([
                ['name' => 'Início', 'url' => langBaseUrl()],
                ['name' => 'Simuladores', 'url' => langBaseUrl('simuladores')],
                ['name' => $name, 'url' => $url],
            ]),
        ];
        if (!empty($faqItems)) {
            $nodes[] = jsonldFaqPage($faqItems, $url . '#faq');
        }
        return jsonldGraph($nodes);
    }

    private function resolveCategoryUrl($slug, $fallback)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->getCategoryBySlug($slug);
        if (!empty($category)) {
            return generateCategoryURL($category);
        }
        return $fallback;
    }

    private function resolvePageUrl($slug, $fallback)
    {
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang($slug, $this->activeLang->id);
        if (!empty($page) && (int)$page->visibility === 1) {
            return langBaseUrl($page->slug);
        }
        return $fallback;
    }

    /**
     * Dynamic URL by Slug
     */
    public function any($slug)
    {
        $slug = cleanSlug($slug);
        if (empty($slug)) {
            return redirect()->to(langBaseUrl());
        }
        $pageModel = new PageModel();
        $data['userSession'] = getUserSession();
        $page = $pageModel->getPageByLang($slug, $this->activeLang->id);
        if (!empty($page)) {
            $this->page($page);
        } else {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getCategoryBySlug($slug);
            if (!empty($category)) {
                $this->category($category);
            } else {
                $post = $this->postModel->getPostBySlug($slug);
                if (!empty($post)) {
                    $this->post($post);
                } else {
                    $this->error404();
                }
            }
        }
    }

    /**
     * Post
     */
    private function post($post, $pageNumber = null)
    {
        if (empty($post)) {
            return redirect()->to(langBaseUrl());
        }
        $pageNumber = clrNum(inputGet('p'));
        if (empty($pageNumber) || $pageNumber < 1) {
            $pageNumber = 1;
        }
        //check post auth
        if (!authCheck() && $post->need_auth == 1) {
            setErrorMessage("message_post_auth");
            redirectToUrl(generateURL('register'));
            exit();
        }
        $data['userSession'] = getUserSession();
        $data['post'] = $post;
        $data['postJsonLD'] = $post;
        $data['postUser'] = getUserById($post->user_id);
        $tagModel = new TagModel();
        $data['postTags'] = $tagModel->getPostTags($post->id);
        $postAdminModel = new PostAdminModel();
        $data['postImages'] = $postAdminModel->getAdditionalImages($post->id);
        $data['comments'] = $this->commonModel->getComments($post->id, COMMENT_LIMIT);
        $data['commentLimit'] = COMMENT_LIMIT;
        $data['relatedPosts'] = $this->postModel->getRelatedPosts($post->category_id, $post->id, $this->categories);
        $data['postType'] = $post->post_type;
        if (!empty($post->feed_id)) {
            $rssModel = new RssModel();
            $data['feed'] = $rssModel->getFeed($post->feed_id);
        }
        $data = setPostMetaTags($post, $data['postTags'], $data);
        $reactionModel = new ReactionModel();
        $data['reactions'] = $reactionModel->getReaction($post->id);
        //gallery post
        if ($post->post_type == 'gallery') {
            if ($pageNumber == null || empty($pageNumber)) {
                $pageNumber = 1;
            }
            $postItemModel = new PostItemModel();
            $data['galleryPostNumRows'] = $postItemModel->getPostListItemsCount($post->id, $post->post_type);
            if ($pageNumber > $data['galleryPostNumRows']) {
                $pageNumber = 1;
            }
            $data['galleryPostItem'] = $postItemModel->getGalleryPostItemByOrder($post->id, $pageNumber);
            $data['pageNumber'] = $pageNumber;
            if ($pageNumber < 1) {
                redirectToUrl(generatePostURL($post));
                exit();
            }
        }
        //sorted list post
        if ($post->post_type == 'sorted_list') {
            $postItemModel = new PostItemModel();
            $data['sortedListItems'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //table of contents
        if ($post->post_type == 'table_of_contents') {
            $postItemModel = new PostItemModel();
            $data['tableOfContentsItems'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //quiz
        if ($post->post_type == 'trivia_quiz' || $post->post_type == 'personality_quiz' || $post->post_type == 'poll') {
            $quizModel = new QuizModel();
            $data['quizQuestions'] = $quizModel->getQuizQuestions($post->id);
            if ($post->post_type == 'poll') {
                $data['userPollAnswers'] = $quizModel->getUserPollAnswers($post->id);
            }
        }
        //recipe post
        if ($post->post_type == 'recipe') {
            $postItemModel = new PostItemModel();
            $data['recipeDirections'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //time spent limit
        $data['postTimeSpent'] = 0;
        $verification = unserializeData($this->generalSettings->human_verification);
        if (!empty($verification) && !empty($verification['status'])) {
            $timeSpent = !empty($verification['time_spent']) ? $verification['time_spent'] : 0;
            $timeSpent = intval($timeSpent ?? '');
            if (intval($timeSpent) < 1) {
                $timeSpent = 0;
            }
            $data['postTimeSpent'] = $timeSpent * 1000;
        }

        echo loadView('partials/_header', $data);
        echo loadView('post/post', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Page
     */
    private function page($page)
    {
        if (empty($page)) {
            return redirect()->to(langBaseUrl());
        }
        if ($page->visibility == 0) {
            $this->error404();
        } else {
            $this->checkPageAuth($page);

            if (($page->slug ?? '') === 'simulador-consorcio') {
                $this->renderConsorcioSimulatorPage($page);
                return;
            }

            $data['title'] = $page->title;
            $data['description'] = $page->description;
            $data['keywords'] = $page->keywords;
            $data['page'] = $page;
            $seoFaq = new \Config\SeoFaq();
            $simulatorFaq = $seoFaq->forSlug((string) ($page->slug ?? ''));
            $simulatorSchema = $this->buildSimulatorPageSchema($page, $simulatorFaq);
            if (!empty($simulatorSchema)) {
                $data['marketingSchema'] = $simulatorSchema;
            }
            if (!empty($simulatorFaq)) {
                $data['faqItems'] = $simulatorFaq;
                $data['faqTitle'] = $seoFaq->titleForSlug((string) ($page->slug ?? ''));
            }
            if ($page->page_default_name == 'gallery') {
                $this->gallery($page, $data);
            } elseif ($page->page_default_name == 'contact') {
                echo loadView('partials/_header', $data);
                echo loadView('contact', $data);
                echo loadView('partials/_footer');
            } else {
                echo loadView('partials/_header', $data);
                echo loadView('page', $data);
                echo loadView('partials/_footer');
            }
        }
    }

    private function renderConsorcioSimulatorPage($page)
    {
        $canonicalUrl = langBaseUrl($page->slug ?? 'simulador-consorcio');
        $title = 'Simulador de Consórcio Inteligente';
        $description = brandLang('Home.consorcio_description');
        $contactChannels = $this->getMarketingContactChannels();
        $defaultWhatsAppMessage = brandLang('Home.consorcio_wa');
        $whatsAppBaseUrl = '';
        if (!empty($contactChannels['whatsapp_digits'])) {
            $whatsAppBaseUrl = 'https://wa.me/' . $contactChannels['whatsapp_digits'];
        }

        $consorcioDefaults = (new \App\Libraries\MarketingConsorcioDefaults($this->activeLang->id))->getPageConfigDefaults();
        $consorcioConfig = (new \App\Models\MarketingHomeModel())->getConsorcioPageConfig($this->activeLang->id, $consorcioDefaults);

        $ogImagePath = trim((string)($consorcioConfig['seo']['og_image'] ?? ''));
        $socialImage = !empty($ogImagePath) ? base_url($ogImagePath) : getLogo();
        $socialImageWidth = !empty($ogImagePath) ? 1200 : (int) getLogoSize('width');
        $socialImageHeight = !empty($ogImagePath) ? 630 : (int) getLogoSize('height');

        $data = [
            'title' => $title,
            'description' => $description,
            'keywords' => trim($this->settings->keywords . ', simulador de consorcio, consorcio estruturado, comparativo consorcio financiamento, planejamento de contemplacao', ' ,'),
            'bodyClass' => 'gx-marketing-home gx-consorcio-simulator-page',
            'pageHeadView' => 'simulators/_consorcio_head',
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => $socialImage,
            'socialImageWidth' => $socialImageWidth,
            'socialImageHeight' => $socialImageHeight,
            'ogDescription' => 'Descubra quanto você pode economizar. Compare consórcio vs financiamento em minutos com IA.',
            'consorcioConfig' => $consorcioConfig,
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'wealthUrl' => base_url('wealth'),
            'contactUrl' => getPageLinkByDefaultName('contact', $this->activeLang->id),
            'termsUrl' => getPageLinkByDefaultName('terms_conditions', $this->activeLang->id),
            'simulatorPage' => $page,
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $defaultWhatsAppMessage),
            'whatsAppBaseUrl' => $whatsAppBaseUrl,
            'whatsAppDefaultMessage' => $defaultWhatsAppMessage,
            'marketingSchema' => $this->buildConsorcioSimulatorSchema($canonicalUrl, $title, $description),
            'userSession' => getUserSession(),
        ];

        echo view('marketing/_home_head', $data);
        echo view('simulators/consorcio', $data);
        echo view('marketing/_home_footer', $data);
    }

    /**
     * Simulador de Seguro de Vida Resgatável (Whole Life WL10/WL20).
     * Gate server-side: a página chama api/quotation/preview (sem R$) e só
     * revela os valores após api/quotation/unlock (grava o lead).
     */
    public function simuladorSeguroResgatavel()
    {
        $canonicalUrl = langBaseUrl('simulador-seguro-resgatavel');
        $title = 'Simulador de Seguro de Vida Resgatável';
        $description = 'Simule um seguro de vida resgatável (Whole Life) quitado em 10 anos, com correção anual e formação de reserva. Veja o ponto de break-even em que a reserva ultrapassa o que você pagou.';

        // SEO/GEO: imagem social dedicada (1200x630) + schema de ferramenta, trilha e FAQ.
        $ogImage = base_url('uploads/marketing/srs_og.jpg');
        helper('jsonld');
        $faqItems = lang('Simuladores.srs_faq');
        if (!is_array($faqItems)) {
            $faqItems = [];
        }
        $marketingSchema = jsonldGraph([
            [
                '@type'              => 'WebPage',
                '@id'                => $canonicalUrl . '#webpage',
                'url'                => $canonicalUrl,
                'name'               => $title,
                'description'        => $description,
                'inLanguage'         => $this->activeLang->language_code ?? 'pt-br',
                'isPartOf'           => ['@id' => base_url() . '/#website'],
                'primaryImageOfPage' => $ogImage,
            ],
            jsonldSoftwareApplication($canonicalUrl, $title, $description, ['image' => $ogImage]),
            jsonldBreadcrumb([
                ['name' => 'Início', 'url' => base_url()],
                ['name' => 'Simuladores', 'url' => langBaseUrl('simuladores')],
                ['name' => 'Seguro de Vida Resgatável'],
            ]),
            jsonldFaqPage($faqItems, $canonicalUrl . '#faq'),
        ]);

        $contactChannels = $this->getMarketingContactChannels();
        $defaultWhatsAppMessage = brandLang('Home.seguro_wa');
        $whatsAppBaseUrl = '';
        if (!empty($contactChannels['whatsapp_digits'])) {
            $whatsAppBaseUrl = 'https://wa.me/' . $contactChannels['whatsapp_digits'];
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'keywords' => trim(($this->settings->keywords ?? '') . ', seguro de vida resgatavel, whole life, simulador de seguro, reserva resgatavel, vida inteira, planejamento sucessorio', ' ,'),
            'bodyClass' => 'gx-marketing-home gx-srs-page',
            'pageHeadView' => 'simulators/_seguro_resgatavel_head',
            'canonicalUrl' => $canonicalUrl,
            'socialImage' => $ogImage,
            'socialImageWidth' => 1200,
            'socialImageHeight' => 630,
            'ogDescription' => $description,
            'marketingSchema' => $marketingSchema,
            'faqItems' => $faqItems,
            'hreflangAlternates' => [
                ['url' => $canonicalUrl, 'hreflang' => $this->activeLang->language_code ?? 'pt-br'],
                ['url' => $canonicalUrl, 'hreflang' => 'x-default'],
            ],
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'wealthUrl' => base_url('wealth'),
            'contactUrl' => getPageLinkByDefaultName('contact', $this->activeLang->id),
            'termsUrl' => getPageLinkByDefaultName('terms_conditions', $this->activeLang->id),
            'contactChannels' => $contactChannels,
            'whatsAppUrl' => $this->buildWhatsAppUrl($contactChannels['whatsapp_digits'] ?? '', $defaultWhatsAppMessage),
            'whatsAppBaseUrl' => $whatsAppBaseUrl,
            'whatsAppDefaultMessage' => $defaultWhatsAppMessage,
            'userSession' => getUserSession(),
        ];

        echo view('marketing/_home_head', $data);
        echo view('simulators/seguro_resgatavel', $data);
        echo view('marketing/_home_footer', $data);
    }

    /**
     * Category
     */
    private function category($category, $isParent = true)
    {
        if (empty($category)) {
            return redirect()->to(langBaseUrl());
        }
        if ($isParent && $category->parent_id != 0) {
            $this->error404();
        } else {
            $data['title'] = $category->name;
            $data['description'] = $category->description;
            $data['keywords'] = $category->keywords;
            $data['category'] = $category;

            // og:image própria por categoria (Fase 5): usa a imagem gerada se existir,
            // senão o header cai no logo. Ver command seo:gen-category-og.
            $catOgRel = 'uploads/marketing/og/og_cat_' . (int) $category->id . '.webp';
            if (is_file(FCPATH . $catOgRel)) {
                $data['categoryOgImage'] = base_url($catOgRel);
            }

            // FAQ das páginas-pilar (Fase 2 GEO): acordeão visível alimentado pela
            // MESMA fonte (Config\SeoFaq) usada no schema abaixo. Só quando há itens.
            $seoFaq = new \Config\SeoFaq();
            $faqItems = $seoFaq->forCategoryId((int) $category->id);
            if (!empty($faqItems)) {
                $data['faqItems'] = $faqItems;
                $data['faqTitle'] = $seoFaq->titleForCategoryId((int) $category->id);
            }

            $categoryTree = getCategoryTree($category->id, $this->categories);
            $numRows = $this->postModel->getPostCountByCategory($category->id, $categoryTree);
            $data['pager'] = paginate($this->postsPerPage, $numRows);
            $data['posts'] = [];
            if ($numRows > 0) {
                $data['posts'] = $this->postModel->getPostsByCategoryPaginated($category->id, $categoryTree, $this->postsPerPage, $data['pager']->offset);
            }

            // Página-pilar como hub temático (Fase 7 GEO): CollectionPage + ItemList
            // do cluster de artigos da página atual (+ FAQPage quando houver).
            // Explicita a estrutura hub->cluster p/ buscadores/LLMs e consolida tópico.
            $data['marketingSchema'] = $this->buildCategoryHubSchema($category, $data['posts'], $faqItems);

            echo loadView('partials/_header', $data);
            echo loadView('category', $data);
            echo loadView('partials/_footer');
        }
    }

    /**
     * Schema de hub da página-pilar (Fase 7 GEO): CollectionPage declarando a
     * categoria como hub temático, com ItemList do cluster de artigos da página
     * atual, e FAQPage quando houver. Segue o padrão de buildSimulatorPageSchema.
     */
    private function buildCategoryHubSchema($category, array $posts, array $faqItems = []): array
    {
        helper('jsonld');

        $url = generateCategoryURL($category);

        $collectionPage = [
            '@type'     => 'CollectionPage',
            '@id'       => $url . '#collectionpage',
            'url'       => $url,
            'name'      => $category->name,
            'isPartOf'  => ['@id' => base_url() . '/#website'],
            'publisher' => ['@id' => base_url() . '/#organization'],
            'about'     => ['@type' => 'Thing', 'name' => $category->name],
        ];

        if (!empty($category->description)) {
            $desc = trim(preg_replace('/\s+/', ' ', strip_tags($category->description)));
            if ($desc !== '') {
                $collectionPage['description'] = characterLimiter($desc, 300, '');
            }
        }

        $elements = [];
        $position = 1;
        foreach ($posts as $post) {
            if (empty($post->title)) {
                continue;
            }
            $elements[] = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'url'      => generatePostURL($post),
                'name'     => trim(preg_replace('/\s+/', ' ', strip_tags($post->title))),
            ];
        }
        if (!empty($elements)) {
            $collectionPage['mainEntity'] = [
                '@type'           => 'ItemList',
                'numberOfItems'   => count($elements),
                'itemListElement' => $elements,
            ];
        }

        $nodes = [$collectionPage];
        if (!empty($faqItems)) {
            $nodes[] = jsonldFaqPage($faqItems, $url . '#faq');
        }

        return jsonldGraph($nodes);
    }

    /**
     * Subcategory
     */
    public function subCategory($parentSlug, $slug)
    {
        $categoryModel = new CategoryModel();
        $categoryParent = $categoryModel->getCategoryBySlug($parentSlug);
        $category = $categoryModel->getCategoryBySlug($slug);
        if (empty($categoryParent) || empty($category)) {
            return redirect()->to(langBaseUrl());
        }
        $this->category($category, false);
    }

    /**
     * Tag
     */
    public function tag($tagSlug)
    {
        $model = new TagModel();
        $data['tag'] = $model->getTagBySlug($tagSlug, $this->activeLang->id);
        if (empty($data['tag'])) {
            return redirect()->to(langBaseUrl());
        }
        $data = setPageMeta($data['tag']->tag, $data);
        $data['userSession'] = getUserSession();
        $numRows = $this->postModel->getPostCountByTag($data['tag']->id, $this->activeLang->id);
        $data['pager'] = paginate($this->postsPerPage, $numRows);
        $data['posts'] = array();
        if ($numRows > 0) {
            $data['posts'] = $this->postModel->getTagPostsPaginated($data['tag']->id, $this->activeLang->id, $this->postsPerPage, $data['pager']->offset);
        }

        echo loadView('partials/_header', $data);
        echo loadView('tag', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Gallery
     */
    private function gallery($category, $data)
    {
        $model = new GalleryModel();
        $data['galleryAlbums'] = $model->getAlbumsByLang($this->activeLang->id);
        $data['jsPage'] = "gallery";
        $data['userSession'] = getUserSession();

        echo loadView('partials/_header', $data);
        echo loadView('gallery/gallery', $data);
        echo loadView('partials/_footer');
    }


    /**
     * Gallery Album Page
     */
    public function galleryAlbum($id)
    {
        $model = new GalleryModel();
        $pageModel = new PageModel();
        $data['page'] = $pageModel->getPageByDefaultName('gallery', $this->activeLang->id);
        $data['jsPage'] = "gallery";
        if (empty($data['page'])) {
            return redirect()->to(langBaseUrl());
        }
        $this->checkPageAuth($data['page']);
        if ($data['page']->visibility == 0) {
            $this->error404();
        } else {
            $data['title'] = $data['page']->title;
            $data['description'] = $data['page']->description;
            $data['keywords'] = $data['page']->keywords;
            $data['userSession'] = getUserSession();
            $data['album'] = $model->getAlbum($id);
            if (empty($data['album'])) {
                return redirect()->to(generateURL('gallery'));
            }
            $data['galleryImages'] = $model->getImagesByAlbum($data['album']->id);
            $data['galleryCategories'] = $model->getCategoriesByAlbum($data['album']->id);

            echo loadView('partials/_header', $data);
            echo loadView('gallery/gallery_album', $data);
            echo loadView('partials/_footer', $data);
        }
    }

    /**
     * Reading List Page
     */
    public function readingList()
    {
        $data = setPageMeta(trans("reading_list"));
        $data['userSession'] = getUserSession();
        $numRows = $this->postModel->getReadingListPostsCount(user()->id);
        $data['pager'] = paginate($this->postsPerPage, $numRows);
        $data['posts'] = $this->postModel->getReadingListPostsPaginated(user()->id, $this->postsPerPage, $data['pager']->offset);

        echo loadView('partials/_header', $data);
        echo loadView('reading_list', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Search Page
     */
    public function search()
    {
        $q = inputGet('q', true);
        if (!empty($q)) {
            $q = strip_tags($q);
        }
        if (empty($q)) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("search") . ': ' . $q;
        $data['description'] = trans("search") . ': ' . $q;
        $data['keywords'] = trans("search") . ', ' . $q;
        $data['q'] = $q;
        $data['userSession'] = getUserSession();
        $data['postsPerPage'] = $this->postsPerPage;
        $data['posts'] = $this->postModel->getSearchPostsPaginated($this->activeLang->id, $q, $this->postsPerPage, 0);

        echo loadView('partials/_header', $data);
        echo loadView('search', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Contact Page Post
     */
    public function contactPost()
    {
        $recaptchaEnabled = isRecaptchaEnabled($this->generalSettings);
        $ip = preg_replace('/[^0-9a-fA-F:.]/', '', (string) getIPAddress());
        $rateKey = 'contact_rl_' . md5($ip);
        $rateWindow = 3600;
        $rateMax = 3;
        $rateHits = (int) (cache($rateKey) ?? 0);
        if ($rateHits >= $rateMax) {
            setErrorMessage('Muitas tentativas em pouco tempo. Aguarde alguns minutos e tente novamente.', false);
            return redirect()->back()->withInput();
        }

        $robotCheck = inputPost('message_content');
        if (!empty($robotCheck)) {
            cache()->save($rateKey, $rateHits + 1, $rateWindow);
            setErrorMessage("msg_recaptcha");
            return redirect()->back()->withInput();
        }

        $formTs = (int) inputPost('form_ts');
        $elapsed = time() - $formTs;
        if ($formTs <= 0 || $elapsed < 2 || $elapsed > 7200) {
            cache()->save($rateKey, $rateHits + 1, $rateWindow);
            setErrorMessage('Envio recusado. Recarregue a página e tente novamente.', false);
            return redirect()->back()->withInput();
        }

        $message = (string) inputPost('message');
        $hasSpamUrl = preg_match('#(https?://|www\.|bit\.ly|tinyurl|t\.co/|goo\.gl|shorturl|ow\.ly)#i', $message) === 1;
        if ($hasSpamUrl && !$recaptchaEnabled) {
            cache()->save($rateKey, $rateHits + 1, $rateWindow);
            setErrorMessage('Não é possível enviar links na mensagem. Descreva sua necessidade em texto.', false);
            return redirect()->back()->withInput();
        }

        $val = \Config\Services::validation();
        $countryValidationList = implode(',', \App\Libraries\LeadPhoneFormatter::getCountryCodes());
        $val->setRule('name', trans("name"), 'required|max_length[200]');
        $val->setRule('email', trans("email"), 'required|valid_email|max_length[200]');
        $val->setRule('phone_country', 'País', 'required|in_list[' . $countryValidationList . ']');
        $val->setRule('phone', trans("phone"), 'required|max_length[40]');
        $val->setRule('message', trans("message"), 'required|max_length[5000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $normalizedPhone = \App\Libraries\LeadPhoneFormatter::toInternational(
                inputPost('phone_country'),
                inputPost('phone')
            );
            if ($normalizedPhone === null) {
                $errors = $val->getErrors();
                $errors['phone'] = 'Informe um telefone válido.';
                $this->session->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }
            if ($recaptchaEnabled && reCAPTCHA('validate', $this->generalSettings) == 'invalid') {
                cache()->save($rateKey, $rateHits + 1, $rateWindow);
                setErrorMessage("msg_recaptcha");
                return redirect()->back()->withInput();
            }
            cache()->save($rateKey, $rateHits + 1, $rateWindow);
            $model = new CommonModel();
            if ($model->addContactMessage()) {
                setSuccessMessage("message_contact_success");
            } else {
                setErrorMessage("message_contact_error");
            }
        }
        return redirect()->back();
    }

    /**
     * Preview
     */
    public function preview($slug)
    {
        if (!authCheck() || empty(cleanSlug($slug))) {
            return redirect()->to(langBaseUrl());
        }
        $post = $this->postModel->getPostPreview($slug);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return redirect()->to(langBaseUrl());
            }
            $this->post($post);
        } else {
            $this->error404();
        }
    }

    /**
     * Rss Feeds
     */
    public function rssFeeds()
    {
        if ($this->generalSettings->show_rss == 1) {
            $data = setPageMeta(trans("rss_feeds"));
            $data['userSession'] = getUserSession();
            echo loadView('partials/_header', $data);
            echo loadView('rss_feeds', $data);
            echo loadView('partials/_footer');
        } else {
            $this->error404();
        }
    }

    /**
     * Rss Latest Posts
     */
    public function rssLatestPosts()
    {
        if ($this->generalSettings->show_rss == 1) {
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . trans("latest_posts");
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/latest-posts');
            $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("latest_posts");
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts(null, null, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Rss By Category
     */
    public function rssByCategory($slug)
    {
        if ($this->generalSettings->show_rss == 1) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getCategoryBySlug($slug);
            if (empty($category)) {
                return redirect()->to(generateURL('rss_feeds'));
            }
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . trans("title_category") . ': ' . $category->name;
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/category/' . $category->slug);
            $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("title_category") . ': ' . $category->name;
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts(null, $category->id, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Rss By User
     */
    public function rssByUser($slug)
    {
        if ($this->generalSettings->show_rss == 1) {
            $authModel = new AuthModel();
            $user = $authModel->getUserBySlug($slug);
            if (empty($user)) {
                return redirect()->to(generateURL('rss_feeds'));
            }
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . $user->username;
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/author/') . $user->slug;
            $data['pageDescription'] = $this->settings->site_title . " - " . $user->username;
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts($user->id, null, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Google News Feeds
     */
    public function googleNewsFeeds()
    {
        if ($this->generalSettings->google_news != 1) {
            redirectToUrl(langBaseUrl());
            exit();
        }
        $data['isGoogleNews'] = true;
        $data['feedName'] = $this->settings->application_name . ' - ' . trans("google_news");
        $data['encoding'] = 'utf-8';
        $data['feedURL'] = current_url();
        $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("google_news") . ' - ' . trans("rss_feeds");
        $data['pageLanguage'] = $this->activeLang->short_form;
        $langId = clrNum(inputGet('lang'));
        if (!empty($langId)) {
            $language = getLanguage($langId);
            if (!empty($language)) {
                $data['pageLanguage'] = $language->short_form;
            }
        }

        $data['posts'] = $this->postModel->getGoogleNewsFeeds($this->categories);
        return $this->response->setXML(view('common/xml_rss', $data));
    }

    //check page auth
    private function checkPageAuth($page)
    {
        if (!authCheck() && $page->need_auth == 1) {
            setErrorMessage("message_page_auth");
            redirectToUrl(langBaseUrl('register'));
            exit();
        }
    }

    //error 404
    public function error404()
    {
        header("HTTP/1.0 404 Not Found");
        $data['title'] = $this->settings->home_title;
        $data['description'] = $this->settings->site_description;
        $data['keywords'] = $this->settings->keywords;
        $data['homeTitle'] = $this->settings->home_title;
        $data['isPage404'] = true;

        echo loadView('partials/_header', $data);
        echo view('errors/html/error_404');
        echo loadView('partials/_footer', $data);
    }
    
    /**
     * Simulador Aurum - Não mais usado, agora é uma página do sistema
     */
    public function simuladorAurum()
    {
        // A página agora é gerenciada pelo sistema de páginas do CMS
        // Redirecionar para a URL amigável da página
        return redirect()->to(generateURL('aurum-simulador-de-custo-de-capital'), 301);
    }
}
