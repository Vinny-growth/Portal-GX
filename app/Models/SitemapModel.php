<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class SitemapModel extends BaseModel
{
    protected $urls;
    protected $urlsAdded;
    protected $limit;

    public function __construct()
    {
        parent::__construct();
        $this->sitemap = [];
        $this->urlsAdded = [];
    }

    //update sitemap settings
    public function updateSitemapSettings()
    {
        $data = [
            'sitemap_frequency' => inputPost('frequency'),
            'sitemap_last_modification' => inputPost('last_modification'),
            'sitemap_priority' => inputPost('priority')
        ];
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //add sitemap item
    private function add($loc, $changeFreq = null, $lastMod = null, $priority = null, $hreflangLinks = null)
    {
        $item = new \stdClass();
        $item->loc = $loc;
        $item->changeFreq = $changeFreq;
        $item->lastMod = $lastMod;
        $item->priority = $priority;
        $item->hreflangLinks = $hreflangLinks;

        if ($this->generalSettings->sitemap_frequency == 'none') {
            $item->changeFreq = null;
        }
        if ($this->generalSettings->sitemap_last_modification == 'none') {
            $item->lastMod = null;
        }
        if ($this->generalSettings->sitemap_priority == 'none') {
            $item->priority = null;
        }
        array_push($this->sitemap, $item);
    }

    //add static page urls
    private function addStaticURLs()
    {
        $now = date('Y-m-d\TH:i:sP');
        if (!empty($this->activeLanguages)) {
            foreach ($this->activeLanguages as $lang) {
                $baseUrl = $this->generateBaseURLByLang($lang->id, $lang->short_form);
                $this->add($baseUrl, 'always', $now, 1.0);
                $this->add($baseUrl . 'blog', 'hourly', $now, 0.9);
                $this->add($baseUrl . Globals::$customRoutes->posts, 'daily', $now, 0.6);
                $this->add($baseUrl . 'simuladores', 'weekly', $now, 0.9);
                $this->add($baseUrl . 'simuladores/cambio', 'weekly', $now, 0.85);
                $this->add($baseUrl . 'newsletter', 'weekly', $now, 0.85);
            }
        } else {
            $baseUrl = base_url() . '/';
            $this->add($baseUrl, 'always', $now, 1.0);
            $this->add($baseUrl . 'blog', 'hourly', $now, 0.9);
            $this->add($baseUrl . Globals::$customRoutes->posts, 'daily', $now, 0.6);
            $this->add($baseUrl . 'simuladores', 'weekly', $now, 0.9);
            $this->add($baseUrl . 'simuladores/cambio', 'weekly', $now, 0.85);
            $this->add($baseUrl . 'newsletter', 'weekly', $now, 0.85);
        }
    }

    //add public CMS page urls
    private function addPageURLs()
    {
        // Slugs que respondem 301 (rotas de redirect legadas) NÃO devem entrar no
        // sitemap, mesmo existindo como CMS page — Google penaliza "redirect no sitemap".
        // Fonte única: HomeController::LEGACY_SIMULATOR_REDIRECTS (Fase 6) + slugs FX legados.
        $redirectSlugs = array_merge(
            array_keys(\App\Controllers\HomeController::LEGACY_SIMULATOR_REDIRECTS),
            ['simuladores-cambio', 'simulador-de-risco-cambial', 'fx-loan']
        );

        $pages = $this->db->table('pages')
            ->select('pages.slug, pages.lang_id, pages.created_at, languages.short_form AS lang_short_form')
            ->join('languages', 'languages.id = pages.lang_id')
            ->where('languages.status', 1)
            ->where('pages.visibility', 1)
            ->where('pages.need_auth', 0)
            ->where('pages.page_type', 'page')
            ->where('pages.slug IS NOT NULL')
            ->where('pages.slug !=', '')
            ->whereNotIn('pages.slug', $redirectSlugs)
            ->orderBy('pages.id DESC')
            ->get()
            ->getResult();

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $baseURL = $this->generateBaseURLByLang($page->lang_id, $page->lang_short_form);
                if (!empty($baseURL)) {
                    $array = $this->getPostFreqLastMod($page->created_at);
                    $this->add($baseURL . $page->slug, $array['freq'], $array['lastMod'], 0.7);
                }
            }
        }
    }

    //add category urls
    private function addCategoryURLs()
    {
        $categories = $this->db->table('categories')->join('languages', 'languages.id = categories.lang_id')
            ->select('categories.*, languages.short_form AS lang_short_form, (SELECT slug FROM categories AS tbl_categories WHERE tbl_categories.id = categories.parent_id) as parent_slug')
            ->where('languages.status', 1)->orderBy('languages.id, categories.id')->get()->getResult();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $baseURL = $this->generateBaseURLByLang($category->lang_id, $category->lang_short_form);
                if (!empty($baseURL)) {
                    $url = '';
                    if (!empty($category->parent_slug)) {
                        $url = $baseURL . $category->parent_slug . '/' . $category->slug;
                    } else {
                        $url = $baseURL . $category->slug;
                    }
                    $array = $this->getCategoryFreqLastMod($category->id);
                    $this->add($url, $array['freq'], $array['lastMod'], 0.8);
                }
            }
        }
    }

    //add post urls
    private function addPostUrls($index)
    {
        $offset = $index * SITEMAP_URL_LIMIT;
        $posts = $this->db->table('posts')->select('posts.id, posts.lang_id, posts.slug, posts.updated_at, posts.created_at, languages.short_form AS lang_short_form')->join('languages', 'languages.id = posts.lang_id')
            ->where('posts.is_scheduled', 0)->where('posts.visibility', 1)->where('posts.status = 1')->orderBy('posts.id DESC, posts.lang_id')->limit(SITEMAP_URL_LIMIT, $offset)->get()->getResult();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $baseURL = $this->generateBaseURLByLang($post->lang_id, $post->lang_short_form);
                if (!empty($baseURL)) {
                    $date = !empty($post->updated_at) ? $post->updated_at : $post->created_at;
                    $array = $this->getPostFreqLastMod($date);
                    $this->add($baseURL . $post->slug, $array['freq'], $array['lastMod'], 0.8);
                }
            }
        }
    }

    //add web stories urls
    private function addWebStoryUrls()
    {
        if (!$this->db->tableExists('web_stories')) {
            return;
        }
        $now = date('Y-m-d\TH:i:sP');

        // Hub URL — dá um ponto de entrada indexável para Google descobrir
        // o conteúdo a partir da listagem (paralelo ao /blog).
        if (!empty($this->activeLanguages)) {
            foreach ($this->activeLanguages as $lang) {
                $baseUrl = $this->generateBaseURLByLang($lang->id, $lang->short_form);
                $this->add($baseUrl . 'web-stories', 'daily', $now, 0.7);
            }
        } else {
            $this->add(base_url() . '/web-stories', 'daily', $now, 0.7);
        }

        $stories = $this->db->table('web_stories')
            ->select('web_stories.id, web_stories.lang_id, web_stories.created_at, web_stories.updated_at, languages.short_form AS lang_short_form')
            ->join('languages', 'languages.id = web_stories.lang_id')
            ->where('languages.status', 1)
            ->where('web_stories.is_active', 1)
            ->orderBy('web_stories.id DESC')
            ->get()
            ->getResult();

        foreach ($stories as $story) {
            $baseURL = $this->generateBaseURLByLang($story->lang_id, $story->lang_short_form);
            if (empty($baseURL)) {
                continue;
            }
            $date = !empty($story->updated_at) ? $story->updated_at : $story->created_at;
            $info = $this->getPostFreqLastMod($date);
            $this->add($baseURL . 'web-stories/story/' . (int) $story->id, $info['freq'], $info['lastMod'], 0.6);
        }
    }

    //generate sitemap
    public function generateSitemap($index)
    {
        if ($index == 0) {
            $this->addStaticURLs();
            $this->addPageURLs();
            $this->addCategoryURLs();
            $this->addWebStoryUrls();
        }
        $this->addPostUrls($index);

        $fileName = $index == 0 ? 'sitemap.xml' : 'sitemap-' . $index . '.xml';
        $filePath = FCPATH . $fileName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        if (!empty($this->sitemap)) {
            foreach ($this->sitemap as $url) {
                $urlLoc = '';
                if (!empty($url->loc)) {
                    $urlLoc = htmlspecialchars(strtolower($url->loc));
                }
                if (!in_array($urlLoc, $this->urlsAdded)) {
                    array_push($this->urlsAdded, $urlLoc);
                    $child = $xml->addChild('url');
                    $child->addChild('loc', $urlLoc);
                    if (isset($url->changeFreq) && $url->changeFreq != 'none') {
                        $child->addChild('changefreq', $url->changeFreq);
                    }
                    if (isset($url->lastMod) && $url->lastMod != 'none') {
                        $child->addChild('lastmod', $url->lastMod);
                    }
                    if (isset($url->priority) && $url->priority != 'none') {
                        $child->addChild('priority', $url->priority);
                    }
                }
            }
            $xml->asXML($filePath);
        }
    }

    //get category frequency and last mod
    private function getCategoryFreqLastMod($categoryId)
    {
        $categoryId = clrNum($categoryId);
        $freq = null;
        $lastMod = null;
        $idx = FORCE_DB_INDEXES ? ' USE INDEX (idx_created_at)' : '';
        $row = $this->db->query("SELECT updated_at, created_at FROM posts {$idx} WHERE posts.category_id IN (
                            SELECT id FROM categories WHERE categories.id = {$categoryId} OR categories.parent_id = {$categoryId}
                      ) ORDER BY created_at DESC LIMIT 1;")->getRow();

        if (!empty($row)) {
            $lastUpdate = !empty($row->updated_at) ? $row->updated_at : $row->created_at;
            if (!empty($row)) {
                $freq = $this->calculateFrequency($lastUpdate);
                $lastMod = $this->dateToISO8601($lastUpdate);
            }
        }

        if (empty($freq)) {
            $freq = 'daily';
            $lastMod = date('Y-m-d\TH:i:sP');
        }
        return ['freq' => $freq, 'lastMod' => $lastMod];
    }

    //get post frequency and last mod
    private function getPostFreqLastMod($date)
    {
        $freq = null;
        $lastMod = null;
        if (!empty($date)) {
            $freq = $this->calculateFrequency($date);
            $lastMod = $this->dateToISO8601($date);
        }
        if (empty($freq)) {
            $freq = 'monthly';
            $lastMod = date('Y-m-d\TH:i:sP');
        }
        return ['freq' => $freq, 'lastMod' => $lastMod];
    }

    //calculate frequency
    private function calculateFrequency($updatedAt)
    {
        $now = new \DateTime();
        $updatedDate = new \DateTime($updatedAt);
        $diffInHours = $now->diff($updatedDate)->h + ($now->diff($updatedDate)->days * 24);

        if ($diffInHours <= 24) {
            return 'hourly';
        } elseif ($diffInHours <= 24 * 7) {
            return 'daily';
        } elseif ($diffInHours <= 24 * 30) {
            return 'weekly';
        } elseif ($diffInHours <= 24 * 365) {
            return 'monthly';
        } else {
            return 'yearly';
        }
    }

    //generate base URL by language
    private function generateBaseURLByLang($langId, $shortForm)
    {
        if ($langId == $this->generalSettings->site_lang) {
            return base_url() . '/';
        } else {
            return base_url($shortForm) . '/';
        }
    }

    //convert date to ISO 8601
    private function dateToISO8601($date)
    {
        $date = new \DateTime($date);
        return $date->format('Y-m-d\TH:i:sP');
    }
}
