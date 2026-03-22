<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\GalleryModel;
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
        $latestPosts = array_slice($this->postModel->getLatestPosts($this->activeLang->id, 4, 0), 0, 4);

        $data = [
            'title' => 'Cambio estruturado, credito e consultoria para empresas',
            'description' => $this->settings->site_description,
            'keywords' => $this->settings->keywords,
            'bodyClass' => 'gx-marketing-home',
            'pageHeadView' => 'marketing/_shared_styles',
            'blogUrl' => langBaseUrl('blog'),
            'simulatorsHubUrl' => langBaseUrl('simuladores'),
            'wealthUrl' => base_url('wealth'),
            'quickLinks' => [
                ['label' => 'Solucoes', 'href' => '#verticais'],
                ['label' => 'Simuladores', 'href' => '#simuladores'],
                ['label' => 'Insights', 'href' => '#blog-tecnico'],
                ['label' => 'Especialista', 'href' => '#fale-especialista'],
            ],
            'heroStats' => [
                ['value' => count($simulators), 'label' => 'simuladores disponiveis'],
                ['value' => 5, 'label' => 'frentes de atuacao'],
                ['value' => count($latestPosts), 'label' => 'analises recentes'],
            ],
            'businessVerticals' => $this->getBusinessVerticals(),
            'simulators' => $simulators,
            'latestPosts' => $latestPosts,
            'userSession' => getUserSession()
        ];

        echo loadView('partials/_header', $data);
        echo view('marketing/home_institutional', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Institutional Blog Home
     */
    public function blog()
    {
        $this->cachePage(300);
        $data = $this->buildEditorialHomeData([
            'title' => 'Blog GX Capital',
            'description' => 'Conteudo tecnico sobre cambio, credito estruturado, mercado de capitais, consorcios e investimentos.',
            'keywords' => trim($this->settings->keywords . ', blog gx capital, conteudo tecnico, mercado financeiro', ' ,'),
            'homeTitle' => 'Blog GX Capital'
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
        $data = [
            'title' => 'Hub de simuladores financeiros',
            'description' => 'Catalogo central dos simuladores da GX Capital com preservacao das URLs individuais ja existentes.',
            'keywords' => trim($this->settings->keywords . ', simuladores financeiros, simulador de risco cambial, simulador de custo de capital', ' ,'),
            'pageHeadView' => 'marketing/_shared_styles',
            'homeUrl' => langBaseUrl(),
            'blogUrl' => langBaseUrl('blog'),
            'simulators' => $this->getSimulatorCatalog(),
            'userSession' => getUserSession()
        ];

        echo loadView('partials/_header', $data);
        echo view('marketing/simulators_hub', $data);
        echo loadView('partials/_footer', $data);
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
            'homeTitle' => $meta['homeTitle'] ?? 'Blog GX Capital',
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
        return [
            [
                'title' => 'Credito Estruturado',
                'eyebrow' => 'Funding e capital de giro',
                'description' => 'Estruture capital, alongue prazos e compare linhas com mais clareza antes de negociar.',
                'link_label' => 'Ver frente de credito',
                'link_url' => $this->resolveCategoryUrl('credito-empresarial', langBaseUrl('simuladores')),
                'accent' => '#0f766e'
            ],
            [
                'title' => 'Cambio e Trade Finance',
                'eyebrow' => 'Protecao cambial e execucao',
                'description' => 'Combine hedge, fluxo internacional e leitura de exposicao cambial em uma unica frente.',
                'link_label' => 'Explorar cambio',
                'link_url' => $this->resolveCategoryUrl('cambio-6', langBaseUrl('simuladores')),
                'accent' => '#0b5cab'
            ],
            [
                'title' => 'Consorcios Estruturados',
                'eyebrow' => 'Planejamento e alavancagem',
                'description' => 'Avalie fluxo de pagamento, contemplacao e custo total antes de escolher a estrutura ideal.',
                'link_label' => 'Abrir simulador',
                'link_url' => $this->resolvePageUrl('simulador-consorcio', langBaseUrl('simuladores')),
                'accent' => '#8f5b2e'
            ],
            [
                'title' => 'Seguros',
                'eyebrow' => 'Protecao patrimonial e operacional',
                'description' => 'Desenhe coberturas aderentes ao risco real da empresa e traga a conversa para a mesa financeira.',
                'link_label' => 'Falar com especialista',
                'link_url' => '#fale-especialista',
                'accent' => '#b45309'
            ],
            [
                'title' => 'Consultoria de Investimentos',
                'eyebrow' => 'Patrimonio, liquidez e estrategia',
                'description' => 'Conecte tesouraria, objetivos patrimoniais e alocacao com uma leitura mais executavel do patrimonio.',
                'link_label' => 'Conhecer wealth',
                'link_url' => base_url('wealth'),
                'accent' => '#7c3aed'
            ],
        ];
    }

    /**
     * Existing public simulator pages that must keep their legacy URLs
     */
    private function getSimulatorCatalog()
    {
        $pageModel = new PageModel();
        $definitions = [
            'simulador-de-risco-cambial' => [
                'label' => 'FX',
                'eyebrow' => 'Cambio e trade finance',
                'title' => 'Simulador de Risco Cambial',
                'description' => 'Projete exposicao cambial e antecipe cenarios para importacao, exportacao e protecao de margem.',
                'cta' => 'Abrir simulador'
            ],
            'aurum-simulador-de-custo-de-capital' => [
                'label' => 'CAP',
                'eyebrow' => 'Credito estruturado',
                'title' => 'Simulador de Custo de Capital',
                'description' => 'Compare custos de funding e entenda qual estrutura de credito faz mais sentido para a operacao.',
                'cta' => 'Calcular custo'
            ],
            'simulador-mercado-de-capitais' => [
                'label' => 'MKT',
                'eyebrow' => 'Mercado de capitais',
                'title' => 'Simulador de Mercado de Capitais',
                'description' => 'Teste cenarios para debentures, CRA, CRI e outras estruturas de captacao fora do credito bancario.',
                'cta' => 'Explorar estrutura'
            ],
            'simulador-de-custo-de-antecipacao' => [
                'label' => 'FIDC',
                'eyebrow' => 'Recebiveis e antecipacao',
                'title' => 'Simulador de Custo de Antecipacao',
                'description' => 'Compare desconto bancario e FIDC para decidir a melhor rota de antecipacao de recebiveis.',
                'cta' => 'Comparar custos'
            ],
            'simulador-consorcio' => [
                'label' => 'CONS',
                'eyebrow' => 'Consorcio estruturado',
                'title' => 'Simulador de Consorcio',
                'description' => 'Entenda custo total, fluxo de parcelas e diferencas entre consorcio e financiamento tradicional.',
                'cta' => 'Simular consorcio'
            ],
        ];

        $items = [];
        foreach ($definitions as $slug => $item) {
            $page = $pageModel->getPageByLang($slug, $this->activeLang->id);
            if (!empty($page) && (int)$page->visibility === 1) {
                if (!empty($page->title)) {
                    $item['title'] = $page->title;
                }
                if (!empty($page->description)) {
                    $item['description'] = $page->description;
                }
                $item['slug'] = $page->slug;
                $item['url'] = langBaseUrl($page->slug);
                $items[] = $item;
            }
        }

        return $items;
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

            $data['title'] = $page->title;
            $data['description'] = $page->description;
            $data['keywords'] = $page->keywords;
            $data['page'] = $page;
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

            $categoryTree = getCategoryTree($category->id, $this->categories);
            $numRows = $this->postModel->getPostCountByCategory($category->id, $categoryTree);
            $data['pager'] = paginate($this->postsPerPage, $numRows);
            $data['posts'] = [];
            if ($numRows > 0) {
                $data['posts'] = $this->postModel->getPostsByCategoryPaginated($category->id, $categoryTree, $this->postsPerPage, $data['pager']->offset);
            }

            echo loadView('partials/_header', $data);
            echo loadView('category', $data);
            echo loadView('partials/_footer');
        }
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
        $robotCheck = inputPost('message_content');
        if (!empty($robotCheck)) {
            setErrorMessage("msg_recaptcha");
            return redirect()->back()->withInput();
        }

        $val = \Config\Services::validation();
        $val->setRule('name', trans("name"), 'required|max_length[200]');
        $val->setRule('email', trans("email"), 'required|valid_email|max_length[200]');
        $val->setRule('message', trans("message"), 'required|max_length[5000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if (reCAPTCHA('validate', $this->generalSettings) == 'invalid') {
                setErrorMessage("msg_recaptcha");
                return redirect()->back()->withInput();
            }
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
