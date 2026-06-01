<?php

namespace App\Controllers;

use App\Libraries\MarketingHomeDefaults;
use App\Libraries\MarketingSimulatorsDefaults;
use App\Libraries\MarketingConsorcioDefaults;
use App\Models\MarketingHomeModel;
use App\Models\UploadModel;

class MarketingAdminController extends BaseAdminController
{
    protected $marketingHomeModel;
    protected $marketingDefaults;
    protected $marketingSimulatorsDefaults;
    protected $marketingConsorcioDefaults;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->marketingHomeModel = new MarketingHomeModel();
        $this->marketingDefaults = new MarketingHomeDefaults($this->activeLang->id);
        $this->marketingSimulatorsDefaults = new MarketingSimulatorsDefaults($this->activeLang->id);
        $this->marketingConsorcioDefaults = new MarketingConsorcioDefaults($this->activeLang->id);
    }

    public function homeCms()
    {
        $this->ensureMarketingAccess();
        $defaults = $this->marketingDefaults->getHomeConfigDefaults();
        $data['title'] = 'Home Institucional - CMS';
        $data['homeConfig'] = $this->marketingHomeModel->getHomeConfig($this->activeLang->id, $defaults);

        echo view('admin/includes/_header', $data);
        echo view('admin/marketing/home_cms', $data);
        echo view('admin/includes/_footer');
    }

    public function homeCmsPost()
    {
        $this->ensureMarketingAccess();
        $home = $this->request->getPost('home');
        $config = $this->normalizeHomeConfig(is_array($home) ? $home : []);

        if ($this->marketingHomeModel->saveHomeConfig($this->activeLang->id, $config)) {
            setSuccessMessage('Home institucional atualizada.');
        } else {
            setErrorMessage('Não foi possível salvar a home institucional.');
        }

        return redirect()->to(adminUrl('marketing/home-cms'));
    }

    public function simulatorsCms()
    {
        $this->ensureMarketingAccess();
        $defaults = $this->marketingSimulatorsDefaults->getPageConfigDefaults();
        $data['title'] = 'Simuladores de Câmbio - CMS';
        $data['simulatorsConfig'] = $this->marketingHomeModel->getSimulatorsHubConfig($this->activeLang->id, $defaults);

        echo view('admin/includes/_header', $data);
        echo view('admin/marketing/simulators_cms', $data);
        echo view('admin/includes/_footer');
    }

    public function simulatorsCmsPost()
    {
        $this->ensureMarketingAccess();
        $input = $this->request->getPost('simulators');
        $config = $this->normalizeSimulatorsConfig(is_array($input) ? $input : []);

        if ($this->marketingHomeModel->saveSimulatorsHubConfig($this->activeLang->id, $config)) {
            setSuccessMessage('Hub de câmbio atualizado.');
        } else {
            setErrorMessage('Não foi possível salvar o hub de câmbio.');
        }

        return redirect()->to(adminUrl('marketing/simulators-cms'));
    }

    public function consorcioCms()
    {
        $this->ensureMarketingAccess();
        $defaults = $this->marketingConsorcioDefaults->getPageConfigDefaults();
        $data['title'] = 'Simulador de Consórcio - CMS';
        $data['consorcioConfig'] = $this->marketingHomeModel->getConsorcioPageConfig($this->activeLang->id, $defaults);

        echo view('admin/includes/_header', $data);
        echo view('admin/marketing/consorcio_cms', $data);
        echo view('admin/includes/_footer');
    }

    public function consorcioCmsPost()
    {
        $this->ensureMarketingAccess();
        $defaults = $this->marketingConsorcioDefaults->getPageConfigDefaults();
        $existing = $this->marketingHomeModel->getConsorcioPageConfig($this->activeLang->id, $defaults);
        $input = $this->request->getPost('consorcio');
        $config = $this->normalizeConsorcioConfig(is_array($input) ? $input : [], $existing);

        if ($this->marketingHomeModel->saveConsorcioPageConfig($this->activeLang->id, $config)) {
            setSuccessMessage('Página do simulador de consórcio atualizada.');
        } else {
            setErrorMessage('Não foi possível salvar a página do simulador de consórcio.');
        }

        return redirect()->to(adminUrl('marketing/consorcio-cms'));
    }

    protected function normalizeConsorcioConfig(array $input, array $existing = [])
    {
        $uploadModel = new UploadModel();

        // OG image upload
        $ogImage = isset($existing['seo']['og_image']) ? trim((string)$existing['seo']['og_image']) : '';
        $ogImageRemove = $this->toBool($input['seo']['og_image_remove'] ?? 0);
        $ogImageUploaded = $uploadModel->uploadMarketingImage('seo_og_image', 'og_consorcio_');
        if (!empty($ogImageUploaded) && !empty($ogImageUploaded['path'])) {
            $this->safeUnlinkMarketing($ogImage);
            $ogImage = $ogImageUploaded['path'];
        } elseif ($ogImageRemove) {
            $this->safeUnlinkMarketing($ogImage);
            $ogImage = '';
        }

        // Testimonials with photos
        $testimonialsInput = is_array($input['testimonials']['items'] ?? null) ? $input['testimonials']['items'] : [];
        $existingItems = is_array($existing['testimonials']['items'] ?? null) ? $existing['testimonials']['items'] : [];
        $normalizedItems = [];

        foreach ($testimonialsInput as $index => $row) {
            if (!is_array($row)) {
                continue;
            }
            $name = $this->cleanText($row['name'] ?? '');
            $city = $this->cleanText($row['city'] ?? '');
            $text = $this->cleanText($row['text'] ?? '');
            if ($name === '' && $city === '' && $text === '') {
                continue;
            }

            $existingPhoto = isset($existingItems[$index]['photo_url']) ? trim((string)$existingItems[$index]['photo_url']) : '';
            $photoRemove = $this->toBool($row['photo_remove'] ?? 0);
            $uploaded = $uploadModel->uploadMarketingImage('testimonial_photo_' . $index, 'testimonial_');
            if (!empty($uploaded) && !empty($uploaded['path'])) {
                $this->safeUnlinkMarketing($existingPhoto);
                $existingPhoto = $uploaded['path'];
            } elseif ($photoRemove) {
                $this->safeUnlinkMarketing($existingPhoto);
                $existingPhoto = '';
            }

            $normalizedItems[] = [
                'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                'name' => $name,
                'city' => $city,
                'text' => $text,
                'photo_url' => $existingPhoto,
            ];
        }

        return [
            'seo' => [
                'og_image' => $ogImage,
            ],
            'testimonials' => [
                'enabled' => $this->toBool($input['testimonials']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['testimonials']['label'] ?? ''),
                'title' => $this->cleanText($input['testimonials']['title'] ?? ''),
                'items' => $normalizedItems,
            ],
        ];
    }

    protected function ensureMarketingAccess()
    {
        if (!hasPermission('pages') && !hasPermission('admin_panel')) {
            redirectToUrl(base_url());
            exit();
        }
    }

    protected function normalizeHomeConfig(array $input)
    {
        return [
            'nav' => [
                'primary_cta_label' => $this->cleanText($input['nav']['primary_cta_label'] ?? ''),
                'primary_cta_url' => $this->cleanUrl($input['nav']['primary_cta_url'] ?? ''),
                'quick_links' => $this->normalizeRows($input['nav']['quick_links'] ?? [], function ($row) {
                    $label = $this->cleanText($row['label'] ?? '');
                    $href = $this->cleanUrl($row['href'] ?? '');
                    if ($label === '' && $href === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'label' => $label,
                        'href' => $href,
                    ];
                }),
            ],
            'hero_stats_labels' => [
                'simulators' => $this->cleanText($input['hero_stats_labels']['simulators'] ?? ''),
                'verticals' => $this->cleanText($input['hero_stats_labels']['verticals'] ?? ''),
                'insights' => $this->cleanText($input['hero_stats_labels']['insights'] ?? ''),
            ],
            'hero' => [
                'enabled' => $this->toBool($input['hero']['enabled'] ?? 0) ? 1 : 0,
                'badge' => $this->cleanText($input['hero']['badge'] ?? ''),
                'title' => $this->cleanText($input['hero']['title'] ?? ''),
                'subtitle' => $this->cleanText($input['hero']['subtitle'] ?? ''),
                'primary_cta_label' => $this->cleanText($input['hero']['primary_cta_label'] ?? ''),
                'primary_cta_url' => $this->cleanUrl($input['hero']['primary_cta_url'] ?? ''),
                'secondary_cta_label' => $this->cleanText($input['hero']['secondary_cta_label'] ?? ''),
                'secondary_cta_url' => $this->cleanUrl($input['hero']['secondary_cta_url'] ?? ''),
                'proof_items' => $this->normalizeRows($input['hero']['proof_items'] ?? [], function ($row) {
                    $title = $this->cleanText($row['title'] ?? '');
                    $text = $this->cleanText($row['text'] ?? '');
                    if ($title === '' && $text === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'title' => $title,
                        'text' => $text,
                    ];
                }),
            ],
            'trust_strip' => [
                'enabled' => $this->toBool($input['trust_strip']['enabled'] ?? 0) ? 1 : 0,
                'lead' => $this->cleanText($input['trust_strip']['lead'] ?? ''),
                'items' => $this->normalizeRows($input['trust_strip']['items'] ?? [], function ($row) {
                    $label = $this->cleanText($row['label'] ?? '');
                    if ($label === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'label' => $label,
                    ];
                }),
            ],
            'verticals' => [
                'enabled' => $this->toBool($input['verticals']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['verticals']['label'] ?? ''),
                'title' => $this->cleanText($input['verticals']['title'] ?? ''),
                'description' => $this->cleanText($input['verticals']['description'] ?? ''),
                'items' => $this->normalizeRows($input['verticals']['items'] ?? [], function ($row) {
                    $title = $this->cleanText($row['title'] ?? '');
                    $eyebrow = $this->cleanText($row['eyebrow'] ?? '');
                    $description = $this->cleanText($row['description'] ?? '');
                    $linkLabel = $this->cleanText($row['link_label'] ?? '');
                    $linkUrl = $this->cleanUrl($row['link_url'] ?? '');
                    if ($title === '' && $eyebrow === '' && $description === '' && $linkLabel === '' && $linkUrl === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'eyebrow' => $eyebrow,
                        'title' => $title,
                        'description' => $description,
                        'link_label' => $linkLabel,
                        'link_url' => $linkUrl,
                        'accent' => $this->cleanColor($row['accent'] ?? '#C7A053'),
                    ];
                }),
            ],
            'process' => [
                'enabled' => $this->toBool($input['process']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['process']['label'] ?? ''),
                'title' => $this->cleanText($input['process']['title'] ?? ''),
                'description' => $this->cleanText($input['process']['description'] ?? ''),
                'items' => $this->normalizeRows($input['process']['items'] ?? [], function ($row) {
                    $title = $this->cleanText($row['title'] ?? '');
                    $desc = $this->cleanText($row['desc'] ?? '');
                    if ($title === '' && $desc === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'title' => $title,
                        'desc' => $desc,
                    ];
                }),
            ],
            'simulators' => [
                'enabled' => $this->toBool($input['simulators']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['simulators']['label'] ?? ''),
                'title' => $this->cleanText($input['simulators']['title'] ?? ''),
                'description' => $this->cleanText($input['simulators']['description'] ?? ''),
                'cta_label' => $this->cleanText($input['simulators']['cta_label'] ?? ''),
                'cta_url' => $this->cleanUrl($input['simulators']['cta_url'] ?? ''),
            ],
            'clippings' => [
                'enabled' => $this->toBool($input['clippings']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['clippings']['label'] ?? ''),
                'title' => $this->cleanText($input['clippings']['title'] ?? ''),
                'description' => $this->cleanText($input['clippings']['description'] ?? ''),
                'item_cta_label' => $this->cleanText($input['clippings']['item_cta_label'] ?? ''),
                'items' => $this->normalizeRows($input['clippings']['items'] ?? [], function ($row) {
                    $portal = $this->cleanText($row['portal'] ?? '');
                    $title = $this->cleanText($row['title'] ?? '');
                    $excerpt = $this->cleanText($row['excerpt'] ?? '');
                    $imageUrl = $this->cleanUrl($row['image_url'] ?? '');
                    $articleUrl = $this->cleanUrl($row['article_url'] ?? '');
                    $publishedAt = $this->cleanText($row['published_at'] ?? '');
                    if ($portal === '' && $title === '' && $excerpt === '' && $imageUrl === '' && $articleUrl === '' && $publishedAt === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'portal' => $portal,
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'image_url' => $imageUrl,
                        'article_url' => $articleUrl,
                        'published_at' => $publishedAt,
                    ];
                }),
            ],
            'partners' => [
                'enabled' => $this->toBool($input['partners']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['partners']['label'] ?? ''),
                'title' => $this->cleanText($input['partners']['title'] ?? ''),
                'description' => $this->cleanText($input['partners']['description'] ?? ''),
                'items' => $this->normalizeRows($input['partners']['items'] ?? [], function ($row) {
                    $name = $this->cleanText($row['name'] ?? '');
                    $logoUrl = $this->cleanUrl($row['logo_url'] ?? '');
                    $websiteUrl = $this->cleanUrl($row['website_url'] ?? '');
                    if ($name === '' && $logoUrl === '' && $websiteUrl === '') {
                        return null;
                    }
                    return [
                        'enabled' => $this->toBool($row['enabled'] ?? 0) ? 1 : 0,
                        'name' => $name,
                        'logo_url' => $logoUrl,
                        'website_url' => $websiteUrl,
                    ];
                }),
            ],
            'blog' => [
                'enabled' => $this->toBool($input['blog']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['blog']['label'] ?? ''),
                'title' => $this->cleanText($input['blog']['title'] ?? ''),
                'description' => $this->cleanText($input['blog']['description'] ?? ''),
                'featured_cta_label' => $this->cleanText($input['blog']['featured_cta_label'] ?? ''),
                'cta_label' => $this->cleanText($input['blog']['cta_label'] ?? ''),
                'cta_url' => $this->cleanUrl($input['blog']['cta_url'] ?? ''),
            ],
            'cta' => [
                'enabled' => $this->toBool($input['cta']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['cta']['label'] ?? ''),
                'title' => $this->cleanText($input['cta']['title'] ?? ''),
                'description' => $this->cleanText($input['cta']['description'] ?? ''),
                'primary_cta_label' => $this->cleanText($input['cta']['primary_cta_label'] ?? ''),
                'primary_cta_url' => $this->cleanUrl($input['cta']['primary_cta_url'] ?? ''),
                'secondary_cta_label' => $this->cleanText($input['cta']['secondary_cta_label'] ?? ''),
                'secondary_cta_url' => $this->cleanUrl($input['cta']['secondary_cta_url'] ?? ''),
            ],
            'lead' => [
                'enabled' => $this->toBool($input['lead']['enabled'] ?? 0) ? 1 : 0,
                'label' => $this->cleanText($input['lead']['label'] ?? ''),
                'title' => $this->cleanText($input['lead']['title'] ?? ''),
                'description' => $this->cleanText($input['lead']['description'] ?? ''),
                'form_heading' => $this->cleanText($input['lead']['form_heading'] ?? ''),
                'form_description' => $this->cleanText($input['lead']['form_description'] ?? ''),
                'form_button_label' => $this->cleanText($input['lead']['form_button_label'] ?? ''),
                'message_placeholder' => $this->cleanText($input['lead']['message_placeholder'] ?? ''),
                'show_phone' => $this->toBool($input['lead']['show_phone'] ?? 0) ? 1 : 0,
                'show_email' => $this->toBool($input['lead']['show_email'] ?? 0) ? 1 : 0,
                'show_simulators_chip' => $this->toBool($input['lead']['show_simulators_chip'] ?? 0) ? 1 : 0,
                'show_blog_chip' => $this->toBool($input['lead']['show_blog_chip'] ?? 0) ? 1 : 0,
                'simulators_chip_label' => $this->cleanText($input['lead']['simulators_chip_label'] ?? ''),
                'blog_chip_label' => $this->cleanText($input['lead']['blog_chip_label'] ?? ''),
            ],
        ];
    }

    protected function normalizeRows($rows, callable $callback)
    {
        $items = [];
        if (!is_array($rows)) {
            return $items;
        }
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $item = $callback($row);
            if ($item !== null) {
                $items[] = $item;
            }
        }
        return $items;
    }

    protected function normalizeSimulatorsConfig(array $input)
    {
        return [
            'hero' => [
                'badge' => $this->cleanText($input['hero']['badge'] ?? ''),
                'title' => $this->cleanText($input['hero']['title'] ?? ''),
                'subtitle' => $this->cleanText($input['hero']['subtitle'] ?? ''),
                'primary_cta_label' => $this->cleanText($input['hero']['primary_cta_label'] ?? ''),
                'primary_cta_url' => $this->cleanUrl($input['hero']['primary_cta_url'] ?? ''),
                'secondary_cta_label' => $this->cleanText($input['hero']['secondary_cta_label'] ?? ''),
                'secondary_cta_url' => $this->cleanUrl($input['hero']['secondary_cta_url'] ?? ''),
            ],
            'hero_proof' => $this->normalizeRows($input['hero_proof'] ?? [], function ($row) {
                $text = $this->cleanText($row['text'] ?? '');
                if ($text === '') {
                    return null;
                }

                return ['text' => $text];
            }),
            'technology' => [
                'label' => $this->cleanText($input['technology']['label'] ?? ''),
                'title' => $this->cleanText($input['technology']['title'] ?? ''),
                'description' => $this->cleanText($input['technology']['description'] ?? ''),
                'stat_primary_value' => $this->cleanText($input['technology']['stat_primary_value'] ?? ''),
                'stat_primary_label' => $this->cleanText($input['technology']['stat_primary_label'] ?? ''),
                'stat_secondary_value' => $this->cleanText($input['technology']['stat_secondary_value'] ?? ''),
                'stat_secondary_label' => $this->cleanText($input['technology']['stat_secondary_label'] ?? ''),
                'stat_tertiary_value' => $this->cleanText($input['technology']['stat_tertiary_value'] ?? ''),
                'stat_tertiary_label' => $this->cleanText($input['technology']['stat_tertiary_label'] ?? ''),
                'signals' => $this->normalizeRows($input['technology']['signals'] ?? [], function ($row) {
                    $text = $this->cleanText($row['text'] ?? '');
                    if ($text === '') {
                        return null;
                    }

                    return ['text' => $text];
                }),
            ],
            'indicators' => [
                'reference_label' => $this->cleanText($input['indicators']['reference_label'] ?? ''),
                'reference_date' => $this->cleanText($input['indicators']['reference_date'] ?? ''),
                'usd_brl' => $this->cleanText($input['indicators']['usd_brl'] ?? ''),
                'commercial_spread' => $this->cleanText($input['indicators']['commercial_spread'] ?? ''),
                'iof' => $this->cleanText($input['indicators']['iof'] ?? ''),
                'selic' => $this->cleanText($input['indicators']['selic'] ?? ''),
                'cdi' => $this->cleanText($input['indicators']['cdi'] ?? ''),
                'ipca_12m' => $this->cleanText($input['indicators']['ipca_12m'] ?? ''),
                'sofr' => $this->cleanText($input['indicators']['sofr'] ?? ''),
                'hedge_cost_monthly' => $this->cleanText($input['indicators']['hedge_cost_monthly'] ?? ''),
                'onshore_spread' => $this->cleanText($input['indicators']['onshore_spread'] ?? ''),
                'offshore_spread' => $this->cleanText($input['indicators']['offshore_spread'] ?? ''),
                'trade_finance_fee' => $this->cleanText($input['indicators']['trade_finance_fee'] ?? ''),
                'stress_scenario' => $this->cleanText($input['indicators']['stress_scenario'] ?? ''),
                'importer_target_margin' => $this->cleanText($input['indicators']['importer_target_margin'] ?? ''),
                'exporter_floor_rate' => $this->cleanText($input['indicators']['exporter_floor_rate'] ?? ''),
                'note' => $this->cleanText($input['indicators']['note'] ?? ''),
            ],
            'lead' => [
                'label' => $this->cleanText($input['lead']['label'] ?? ''),
                'title' => $this->cleanText($input['lead']['title'] ?? ''),
                'description' => $this->cleanText($input['lead']['description'] ?? ''),
                'form_title' => $this->cleanText($input['lead']['form_title'] ?? ''),
                'form_description' => $this->cleanText($input['lead']['form_description'] ?? ''),
                'button_label' => $this->cleanText($input['lead']['button_label'] ?? ''),
                'success_message' => $this->cleanText($input['lead']['success_message'] ?? ''),
            ],
        ];
    }

    protected function cleanText($value)
    {
        return trim((string)$value);
    }

    protected function cleanUrl($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^\s*javascript:/i', $value)) {
            return '';
        }
        return $value;
    }

    protected function cleanColor($value)
    {
        $value = trim((string)$value);
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return $value;
        }
        return '#C7A053';
    }

    protected function toBool($value)
    {
        return !empty($value) && $value !== '0';
    }

    /**
     * Safely delete a marketing upload file with path traversal protection.
     */
    protected function safeUnlinkMarketing(string $relativePath)
    {
        if (empty($relativePath) || strpos($relativePath, 'uploads/marketing/') !== 0) {
            return;
        }
        $fullPath = realpath(FCPATH . $relativePath);
        if ($fullPath && strpos($fullPath, realpath(FCPATH . 'uploads/marketing/')) === 0 && is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
