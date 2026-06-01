<?php

namespace App\Controllers;

use App\Models\WebStoriesModel;
use App\Models\CategoryModel;
use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;
use App\Libraries\WebStoriesGenerator;

class WebStoriesController extends BaseController
{
    protected $webStoriesModel;
    protected $categoryModel;
    protected $openAIHelper;
    protected $webpConverter;
    protected $generator;
    
    protected function getOpenAIKey()
    {
        $key = getenv('OPENAI_API_KEY');
        if (!empty($key)) {
            return $key;
        }
        try {
            $ai = aiWriter();
            if (!empty($ai) && !empty($ai->apiKey)) {
                return $ai->apiKey;
            }
        } catch (\Throwable $e) {
        }
        return '';
    }

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->webStoriesModel = new WebStoriesModel();
        $this->categoryModel = new CategoryModel();
        $this->openAIHelper = new OpenAIImageHelper();
        $this->webpConverter = new WebPConverter();
        $this->generator = new WebStoriesGenerator();
    }

    /**
     * Public web stories display
     */
    public function index()
    {
        $data['title'] = 'Web Stories - ' . esc($this->settings->site_title);
        $data['description'] = 'Explore our web stories collection';
        $data['keywords'] = 'web stories, visual content';
        
        $data['webStories'] = $this->webStoriesModel->getActiveWebStories($this->activeLang->id);
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;

        echo view('themes/' . $this->activeTheme->theme . '/partials/_header', $data);
        echo view('web_stories/index', $data);
        echo view('themes/' . $this->activeTheme->theme . '/partials/_footer', $data);
    }

    /**
     * View single web story
     */
    public function view($id)
    {
        $webStory = $this->webStoriesModel->getWebStory($id);

        if (empty($webStory) || $webStory->is_active != 1) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Bot/crawler aware view counter — see WebStoriesModel::recordViewIfHuman.
        $this->webStoriesModel->recordViewIfHuman($id, $this->request);

        $data['title'] = esc($webStory->title) . ' - ' . esc($this->settings->site_title);
        $data['description'] = esc($webStory->description);
        $data['keywords'] = 'web story, ' . esc($webStory->title);
        $data['webStory'] = $webStory;
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;
        $data['settings'] = $this->settings;
        // Tells crawlers / Discover that this URL has an AMP equivalent.
        $data['amphtmlUrl'] = base_url('web-stories/story/' . (int) $id);

        // Check if user wants AMP Stories format
        $ampParam = $this->request->getGet('amp');
        $useAmpFormat = ($ampParam !== null && $ampParam !== '0' && $ampParam !== 'false') ||
                       $this->request->getUserAgent()->isMobile() ||
                       $this->request->getGet('format') === 'story';

        if ($useAmpFormat) {
            // Use AMP Stories format
            echo view('web_stories/story_amp', $data);
        } else {
            // Use traditional format
            echo view('themes/' . $this->activeTheme->theme . '/partials/_header', $data);
            echo view('web_stories/view', $data);
            echo view('themes/' . $this->activeTheme->theme . '/partials/_footer', $data);
        }
    }

    /**
     * View web story in AMP Stories format
     */
    public function story($id)
    {
        $webStory = $this->webStoriesModel->getWebStory($id);

        if (empty($webStory) || $webStory->is_active != 1) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Bot/crawler aware view counter.
        $this->webStoriesModel->recordViewIfHuman($id, $this->request);

        $data['webStory'] = $webStory;
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;
        $data['settings'] = $this->settings;

        // Check if there are custom pages
        $webStoryPagesModel = new \App\Models\WebStoryPagesModel();
        $customPages = $webStoryPagesModel->getWebStoryPages($id);
        $data['customPages'] = $customPages;

        // Always use AMP Stories format
        echo view('web_stories/story_amp', $data);
    }

    /**
     * Track click on web story
     */
    public function click($id)
    {
        $webStory = $this->webStoriesModel->getWebStory($id);

        if (!empty($webStory) && $webStory->is_active == 1) {
            $this->webStoriesModel->recordClickIfHuman($id, $this->request);

            // Validate the destination — link_url should already be sanitized
            // at save time, but click() is a public redirect and must guard
            // against legacy rows with javascript:/data: URLs.
            $safeUrl = WebStoriesModel::sanitizeOutboundUrl($webStory->link_url ?? '');
            if ($safeUrl !== '') {
                return redirect()->to($safeUrl);
            }
        }

        return redirect()->to('/');
    }

    /**
     * Admin - List web stories
     */
    public function admin()
    {
        checkPermission('admin_panel');
        
        $data['title'] = 'Web Stories';
        $data['webStories'] = $this->webStoriesModel->getWebStoriesWithCategories();
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;

        echo view('admin/includes/_header', $data);
        echo view('admin/web_stories/index', $data);
        echo view('admin/includes/_footer', ['baseAIWriter' => aiWriter(), 'baseSettings' => $this->settings, 'activeLang' => $this->activeLang, 'activeLanguages' => $this->activeLanguages]);
    }

    /**
     * Admin - Add web story form
     */
    public function adminAdd()
    {
        checkPermission('admin_panel');

        if ($this->request->getMethod() === 'post') {
            return $this->adminAddPost();
        }

        $data['title'] = 'Add Web Story';
        $data['categories'] = $this->categoryModel->getParentCategories();
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;
        $configuredKey = getenv('OPENAI_API_KEY');
        if (empty($configuredKey)) {
            $ai = aiWriter();
            $configuredKey = !empty($ai) && !empty($ai->apiKey) ? $ai->apiKey : '';
        }
        $data['openaiApiKey'] = !empty($configuredKey) ? '***configured***' : 'Not configured';

        // Check if user wants advanced editor
        $useAdvancedEditor = $this->request->getGet('advanced') === '1';

        echo view('admin/includes/_header', $data);
        
        if ($useAdvancedEditor) {
            echo view('admin/web_stories/add_advanced', $data);
        } else {
            echo view('admin/web_stories/add', $data);
        }
        
        echo view('admin/includes/_footer', ['baseAIWriter' => aiWriter(), 'baseSettings' => $this->settings, 'activeLang' => $this->activeLang, 'activeLanguages' => $this->activeLanguages]);
    }

    /**
     * Admin - Add web story post
     */
    public function adminAddPost()
    {
        // Debug logging
        log_message('debug', 'WebStoriesController::adminAddPost called');
        log_message('debug', 'POST data: ' . json_encode($_POST));
        
        checkPermission('admin_panel');

        $val = \Config\Services::validation();
        $val->setRule('title', 'Title', 'required|max_length[255]');
        
        if (!$this->validate(getValRules($val))) {
            log_message('debug', 'Validation failed: ' . json_encode($val->getErrors()));
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        }

        log_message('debug', 'Validation passed, calling addWebStory()');
        
        // Check if this is from advanced editor
        $isAdvancedEditor = inputPost('is_advanced_editor');
        
        if ($isAdvancedEditor) {
            // Handle advanced editor with multiple pages
            $webStoryId = $this->webStoriesModel->addWebStory();
            
            if ($webStoryId) {
                // Process pages
                $this->webStoryPagesModel = new \App\Models\WebStoryPagesModel();
                $pages = inputPost('pages');
                
                log_message('debug', 'Pages data received: ' . json_encode($pages));
                
                if (!empty($pages) && is_array($pages)) {
                    foreach ($pages as $index => $pageData) {
                        log_message('debug', "Processing page {$index}: " . json_encode($pageData));
                        $this->webStoryPagesModel->addWebStoryPage($webStoryId, $pageData);
                    }
                }
                
                log_message('debug', 'Advanced web story added successfully');
                setSuccessMessage('Web story criada com sucesso!');
                return redirect()->to(adminUrl('web-stories'));
            } else {
                log_message('debug', 'Error adding advanced web story');
                setErrorMessage('Erro ao criar web story');
                return redirect()->back()->withInput();
            }
        } else {
            // Handle simple editor (legacy)
            if ($this->webStoriesModel->addWebStory()) {
                log_message('debug', 'Web story added successfully');
                setSuccessMessage('Web story added successfully');
                return redirect()->to(adminUrl('web-stories'));
            } else {
                log_message('debug', 'Error adding web story');
                setErrorMessage('Error adding web story');
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Admin - Edit web story form
     */
    public function adminEdit($id)
    {
        checkPermission('admin_panel');

        if ($this->request->getMethod() === 'post') {
            return $this->adminEditPost($id);
        }

        $data['title'] = 'Edit Web Story';
        $data['webStory'] = $this->webStoriesModel->getWebStory($id);
        
        if (empty($data['webStory'])) {
            return redirect()->to(adminUrl('web-stories'));
        }

        $data['categories'] = $this->categoryModel->getParentCategories();
        $data['activeLanguages'] = $this->activeLanguages;
        $data['activeLang'] = $this->activeLang;
        $configuredKey = getenv('OPENAI_API_KEY');
        if (empty($configuredKey)) {
            $ai = aiWriter();
            $configuredKey = !empty($ai) && !empty($ai->apiKey) ? $ai->apiKey : '';
        }
        $data['openaiApiKey'] = !empty($configuredKey) ? '***configured***' : 'Not configured';

        echo view('admin/includes/_header', $data);
        echo view('admin/web_stories/edit', $data);
        echo view('admin/includes/_footer', ['baseAIWriter' => aiWriter(), 'baseSettings' => $this->settings, 'activeLang' => $this->activeLang, 'activeLanguages' => $this->activeLanguages]);
    }

    /**
     * Admin - Edit web story post
     */
    public function adminEditPost($id)
    {
        checkPermission('admin_panel');

        $val = \Config\Services::validation();
        $val->setRule('title', 'Title', 'required|max_length[255]');
        
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        }

        if ($this->webStoriesModel->editWebStory($id)) {
            setSuccessMessage('Web story updated successfully');
            return redirect()->to(adminUrl('web-stories'));
        } else {
            setErrorMessage('Error updating web story');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Admin - Delete web story
     */
    public function adminDelete($id)
    {
        // Debug logging
        log_message('debug', 'WebStoriesController::adminDelete called with ID: ' . $id);
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'CSRF token: ' . $this->request->getPost(csrf_token()));
        
        checkPermission('admin_panel');

        if ($this->webStoriesModel->deleteWebStory($id)) {
            log_message('debug', 'Web story deleted successfully');
            setSuccessMessage('Web story deleted successfully');
        } else {
            log_message('debug', 'Error deleting web story');
            setErrorMessage('Error deleting web story');
        }

        return redirect()->to(adminUrl('web-stories'));
    }

    /**
     * Admin - Toggle web story status
     */
    public function adminToggle($id)
    {
        checkPermission('admin_panel');

        if ($this->webStoriesModel->toggleStatus($id)) {
            setSuccessMessage('Web story status updated');
        } else {
            setErrorMessage('Error updating status');
        }

        return redirect()->to(adminUrl('web-stories'));
    }

    /**
     * Admin - Update display order via AJAX
     */
    public function adminUpdateOrder()
    {
        checkPermission('admin_panel');

        $stories = inputPost('stories');
        
        if (!empty($stories)) {
            foreach ($stories as $order => $id) {
                $this->webStoriesModel->updateDisplayOrder($id, $order + 1);
            }
            
            echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No data received']);
        }
    }

    /**
     * Generate image using OpenAI
     */
    public function generateImage()
    {
        checkPermission('admin_panel');

        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $prompt = inputPost('prompt');
        $style = inputPost('style') ?: '';
        $brand = getenv('OPENAI_BRAND_STYLE') ?: '';
        if (!empty($brand)) { $style = trim($brand . ' ' . $style); }
        
        if (empty($prompt)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Prompt is required']);
        }

        try {
            $result = $this->openAIHelper->generateWebStoryImage($prompt, $style);
            
            if ($result && $result['success']) {
                $imageData = $result['data'][0];
                
                return $this->response->setJSON([
                    'success' => true,
                    'image_url' => $imageData['url'],
                    'prompt' => $prompt,
                    'created' => $result['created']
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to generate image']);
            }
        } catch (Exception $e) {
            log_message('error', 'OpenAI image generation error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error generating image: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload and convert image to WebP
     */
    public function uploadImage()
    {
        checkPermission('admin_panel');

        $file = $this->request->getFile('image');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid file uploaded']);
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid file type. Only images are allowed.']);
        }

        // Check file size (max 10MB)
        if ($file->getSize() > 10485760) {
            return $this->response->setJSON(['success' => false, 'message' => 'File too large. Maximum size is 10MB.']);
        }

        try {
            $uploadPath = FCPATH . 'uploads/web_stories/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $convertedPath = $this->webpConverter->convertUploadedFile($file, $uploadPath);
            
            if ($convertedPath) {
                $relativePath = str_replace(FCPATH, '', $convertedPath);
                $imageUrl = base_url($relativePath);
                
                return $this->response->setJSON([
                    'success' => true,
                    'image_url' => $imageUrl,
                    'image_path' => $relativePath
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to process image']);
            }
        } catch (Exception $e) {
            log_message('error', 'Image upload error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error uploading image']);
        }
    }

    /**
     * Get web stories for API
     */
    public function apiGetStories()
    {
        $langId = (int) (inputGet('lang_id') ?: $this->activeLang->id);
        $limit  = (int) (inputGet('limit') ?: 20);
        $offset = (int) (inputGet('offset') ?: 0);
        $limit  = max(1, min(50, $limit));
        $offset = max(0, $offset);

        $stories = $this->webStoriesModel->getActiveWebStoriesPaginated($langId, $limit, $offset);
        $total   = $this->webStoriesModel->countActiveWebStories($langId);

        $formattedStories = [];
        foreach ($stories as $story) {
            $formattedStories[] = [
                'id'          => $story->id,
                'title'       => $story->title,
                'description' => $story->description,
                'image_url'   => !empty($story->image_path) ? base_url($story->image_path) : $story->image_url,
                'link_url'    => $story->link_url,
                'view_count'  => (int) $story->view_count,
                'click_count' => (int) $story->click_count,
                'created_at'  => $story->created_at,
                'amp_url'     => base_url('web-stories/story/' . (int) $story->id),
            ];
        }

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=300')
            ->setJSON([
                'success' => true,
                'stories' => $formattedStories,
                'total'   => $total,
                'limit'   => $limit,
                'offset'  => $offset,
            ]);
    }

    /**
     * Test connection for Web Stories generation
     */
    public function testConnection()
    {
        log_message('debug', 'testConnection called');
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Conexão funcionando!',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check image generation status
     */
    public function checkImageStatus()
    {
        $webStoryId = inputPost('web_story_id');
        
        if (empty($webStoryId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Web Story ID é obrigatório']);
        }
        
        $webStoryPagesModel = new \App\Models\WebStoryPagesModel();
        $builder = $webStoryPagesModel->builderWebStoryPages;
        
        $pages = $builder->where('web_story_id', $webStoryId)->get()->getResult();
        $totalPages = count($pages);
        $pagesWithImages = 0;
        
        foreach ($pages as $page) {
            if (!empty($page->image_path) || $page->background_type === 'image') {
                $pagesWithImages++;
            }
        }
        
        $isComplete = ($pagesWithImages === $totalPages);
        $progress = $totalPages > 0 ? round(($pagesWithImages / $totalPages) * 100) : 0;
        
        return $this->response->setJSON([
            'success' => true,
            'complete' => $isComplete,
            'progress' => $progress,
            'pages_with_images' => $pagesWithImages,
            'total_pages' => $totalPages,
            'message' => $isComplete ? 'Todas as imagens foram geradas!' : "Progresso: {$pagesWithImages}/{$totalPages} imagens"
        ]);
    }

    /**
     * Bulk update visibility (activate/deactivate)
     */
    public function bulkVisibility()
    {
        try { checkPermission('admin_panel'); } catch (\Throwable $e) { return $this->response->setJSON(['success'=>false,'message'=>'Permissão insuficiente']); }
        $ids = inputPost('ids');
        $visibility = (int) inputPost('visibility');
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success'=>false,'message'=>'IDs inválidos']);
        }
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($v) => $v>0);
        if (empty($ids)) { return $this->response->setJSON(['success'=>false,'message'=>'IDs vazios']); }
        $updated = $this->webStoriesModel->builderWebStories->whereIn('id', $ids)->update(['is_active' => $visibility, 'updated_at'=>date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success'=> (bool) $updated, 'updated'=>count($ids)]);
    }

    /**
     * Bulk delete stories (uses model delete to cleanup files)
     */
    public function bulkDelete()
    {
        try { checkPermission('admin_panel'); } catch (\Throwable $e) { return $this->response->setJSON(['success'=>false,'message'=>'Permissão insuficiente']); }
        $ids = inputPost('ids');
        if (empty($ids) || !is_array($ids)) { return $this->response->setJSON(['success'=>false,'message'=>'IDs inválidos']); }
        $ids = array_map('intval', $ids);
        $ids = array_values(array_filter($ids, fn($v) => $v > 0));
        if (empty($ids)) { return $this->response->setJSON(['success'=>false,'message'=>'IDs vazios']); }

        $db = \Config\Database::connect();
        $deleted = [];
        $failed  = [];

        $db->transStart();
        foreach ($ids as $id) {
            if ($this->webStoriesModel->deleteWebStory($id)) {
                $deleted[] = $id;
            } else {
                $failed[] = $id;
            }
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falha na transação — nenhuma linha foi removida.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'deleted' => count($deleted),
            'deleted_ids' => $deleted,
            'failed'  => $failed,
        ]);
    }
    
    /**
     * Generate Web Stories from Article using AI
     */
    public function generateFromArticle()
    {
        try {
            checkPermission('admin_panel');
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Permissão insuficiente']);
        }

        $postId = inputPost('post_id');
        if (empty($postId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID do post é obrigatório']);
        }
        $postAdminModel = new \App\Models\PostAdminModel();
        $post = $postAdminModel->getPost($postId);
        if (empty($post)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Post não encontrado']);
        }
        if (!$this->getOpenAIKey()) {
            return $this->response->setJSON(['success' => false, 'message' => 'API do OpenAI não configurada']);
        }

        $structure = $this->generator->generateStructureFromPost($post);
        if (!$structure) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao gerar conteúdo com IA']);
        }
        $webStoryId = $this->generator->createStoryAndPages($post, $structure);
        if (!$webStoryId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao criar web story']);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Web Story criada. Gere as imagens em seguida.',
            'web_story_id' => $webStoryId,
            'redirect_url' => adminUrl('web-stories/edit/' . $webStoryId)
        ]);
    }

    /**
     * Generate ONE image per request for robustness
     */
    public function generateImagesStep()
    {
        try { checkPermission('admin_panel'); } catch (\Throwable $e) { return $this->response->setJSON(['success'=>false,'message'=>'Permissão insuficiente']); }
        $webStoryId = (int) inputPost('web_story_id');
        if (!$webStoryId) { return $this->response->setJSON(['success'=>false,'message'=>'web_story_id obrigatório']); }
        $postId = (int) inputPost('post_id');
        $post = null;
        if ($postId) {
            $postAdminModel = new \App\Models\PostAdminModel();
            $post = $postAdminModel->getPost($postId);
        }
        $res = $this->generator->generateNextImageForStory($webStoryId, $post);
        $progress = $this->generator->getImageProgress($webStoryId);
        // Include current cover image info for UI updates
        $story = $this->webStoriesModel->getWebStory($webStoryId);
        $coverUrl = '';
        $coverPath = '';
        if (!empty($story)) {
            if (!empty($story->image_path)) {
                $coverPath = $story->image_path;
                $coverUrl = base_url($story->image_path);
            } elseif (!empty($story->image_url)) {
                $coverUrl = $story->image_url;
            }
        }
        return $this->response->setJSON([
            'success'=>true,
            'done'=>($res['done']??false),
            'progress'=>$progress,
            'step'=>$res,
            'cover'=>['url'=>$coverUrl,'path'=>$coverPath]
        ]);
    }

    /**
     * Generate web stories structure from post using AI
     */
    private function generateWebStoriesFromPost($post)
    {
        try {
            // Prepare the prompt for ChatGPT
            $prompt = $this->buildAIPrompt($post);
            
            // Call OpenAI API
            $openaiResponse = $this->callOpenAI($prompt);
            
            if ($openaiResponse) {
                $aiContent = null;
                // Chat Completions shape
                if (isset($openaiResponse['choices'][0]['message']['content'])) {
                    $aiContent = $openaiResponse['choices'][0]['message']['content'];
                }
                // Responses API: direct output text
                if ($aiContent === null && isset($openaiResponse['output_text']) && !empty($openaiResponse['output_text'])) {
                    $aiContent = $openaiResponse['output_text'];
                }
                // Responses API: iterate over output items to find message content text
                if ($aiContent === null && isset($openaiResponse['output']) && is_array($openaiResponse['output'])) {
                    foreach ($openaiResponse['output'] as $outItem) {
                        if (isset($outItem['type']) && $outItem['type'] === 'message' && isset($outItem['content']) && is_array($outItem['content'])) {
                            foreach ($outItem['content'] as $contentPart) {
                                if (isset($contentPart['text']) && is_string($contentPart['text']) && $contentPart['text'] !== '') {
                                    $aiContent = $contentPart['text'];
                                    break 2;
                                }
                            }
                        }
                    }
                }
                // Some variants may expose a top-level 'content' array
                if ($aiContent === null && isset($openaiResponse['content']) && is_array($openaiResponse['content']) && isset($openaiResponse['content'][0]['text'])) {
                    $aiContent = $openaiResponse['content'][0]['text'];
                }

                if (!empty($aiContent)) {
                    // Parse AI response to extract structured data
                    return $this->parseAIResponse($aiContent);
                }
                log_message('error', 'OpenAI response did not contain usable content: ' . json_encode($openaiResponse));
            }
            
            return false;
        } catch (\Throwable $e) {
            log_message('error', 'Error calling OpenAI for web stories: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build AI prompt for web stories generation
     */
    private function buildAIPrompt($post)
    {
        $baseUrl = generateBaseURLByLangId($post->lang_id);
        $postUrl = generatePostURL($post, $baseUrl);
        
        $prompt = "Você é um especialista em criar Web Stories envolventes. Com base no artigo abaixo, crie exatamente 4 páginas de Web Stories seguindo esta estrutura:

ARTIGO:
Título: {$post->title}
Conteúdo: " . strip_tags($post->content) . "

INSTRUÇÕES:
1. Página 1 (Capa): Título chamativo e imagem de capa
2. Página 2: Primeiro ponto principal do artigo
3. Página 3: Segundo ponto principal do artigo  
4. Página 4 (CTA): Call-to-action com 'Leia matéria completa' e link para o artigo

FORMATO DE RESPOSTA (responda APENAS em JSON válido):
{
  \"title\": \"Título da Web Story\",
  \"description\": \"Descrição resumida\",
  \"pages\": [
    {
      \"page_type\": \"cover\",
      \"title\": \"Título da capa\",
      \"content\": \"Texto da capa\",
      \"background_type\": \"gradient\",
      \"background_value\": \"linear-gradient(135deg, #667eea 0%, #764ba2 100%)\",
      \"text_position\": \"center\",
      \"font_size\": \"large\"
    },
    {
      \"page_type\": \"content\",
      \"title\": \"Título da página 2\",
      \"content\": \"Conteúdo da página 2\",
      \"background_type\": \"gradient\",
      \"background_value\": \"linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)\",
      \"text_position\": \"center\",
      \"font_size\": \"medium\"
    },
    {
      \"page_type\": \"content\",
      \"title\": \"Título da página 3\",
      \"content\": \"Conteúdo da página 3\",
      \"background_type\": \"gradient\",
      \"background_value\": \"linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)\",
      \"text_position\": \"center\",
      \"font_size\": \"medium\"
    },
    {
      \"page_type\": \"cta\",
      \"title\": \"Leia a matéria completa\",
      \"content\": \"Acesse o artigo completo para mais detalhes\",
      \"background_type\": \"gradient\",
      \"background_value\": \"linear-gradient(135deg, #fa709a 0%, #fee140 100%)\",
      \"text_position\": \"center\",
      \"font_size\": \"medium\",
      \"cta_text\": \"Leia matéria completa\",
      \"cta_url\": \"{$postUrl}\"
    }
  ]
}

IMPORTANTE: Responda APENAS com o JSON válido, sem texto adicional.";

        return $prompt;
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($prompt)
    {
        $apiKey = $this->getOpenAIKey();
        
        $model = (getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini');
        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];
        // Only send temperature for models that support it
        if (strpos($model, 'gpt-5') === false) {
            $data['temperature'] = 0.7;
        }
        // Token limits vary across models/endpoints; avoid unsupported params
        if (strpos($model, 'gpt-5') === false) {
            $data['max_tokens'] = 2000;
        } else {
            // For newer models, try using completion-specific parameter
            $data['max_completion_tokens'] = 2000;
        }

        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];

        // Use Responses API for GPT-5 models for better compatibility
        $endpoint = (strpos($model, 'gpt-5') !== false)
            ? 'https://api.openai.com/v1/responses'
            : 'https://api.openai.com/v1/chat/completions';

        if ($endpoint === 'https://api.openai.com/v1/responses') {
            // Adapt payload to Responses API
            $payload = [
                'model' => $model,
                'input' => $prompt
            ];
        } else {
            $payload = $data;
        }

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $timeout = intval(getenv('OPENAI_TEXT_TIMEOUT') ?: 45);
        if ($timeout < 10) { $timeout = 10; }
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            $decoded = json_decode($response, true);
            return $decoded;
        }

        // Retry once without any token limit params if unsupported_parameter
        $decoded = json_decode($response, true);
        $retry = false;
        if (is_array($decoded) && isset($decoded['error']['code']) && $decoded['error']['code'] === 'unsupported_parameter') {
            $retry = true;
        } elseif (is_array($decoded) && isset($decoded['error']['message']) && stripos($decoded['error']['message'], 'Unsupported parameter') !== false) {
            $retry = true;
        }

        if ($retry && $endpoint !== 'https://api.openai.com/v1/responses') {
            unset($data['max_tokens'], $data['max_completion_tokens']);
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode === 200) {
                return json_decode($response, true);
            }
        }

        // Fallback to alternate model in case of timeout or error
        $fallbackModel = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
        if (!empty($fallbackModel) && $fallbackModel !== $model) {
            log_message('error', 'Primary model failed (' . $model . '). Falling back to ' . $fallbackModel);
            $useResponses = (strpos($fallbackModel, 'gpt-5') !== false);
            if ($useResponses) {
                $payload = [
                    'model' => $fallbackModel,
                    'input' => $prompt
                ];
                $endpoint = 'https://api.openai.com/v1/responses';
            } else {
                $data['model'] = $fallbackModel;
                unset($data['max_tokens'], $data['max_completion_tokens']);
                $data['temperature'] = 0.7;
                $payload = $data;
                $endpoint = 'https://api.openai.com/v1/chat/completions';
            }

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);
            if ($httpCode === 200) {
                return json_decode($response, true);
            }
        }

        log_message('error', 'OpenAI API Error: ' . ($response ?: $curlErr));
        return false;
    }

    /**
     * Parse AI response to extract structured data
     */
    private function parseAIResponse($aiContent)
    {
        // Clean the response to ensure it's valid JSON
        $aiContent = trim($aiContent);
        $aiContent = preg_replace('/^```json\s*/', '', $aiContent);
        $aiContent = preg_replace('/\s*```$/', '', $aiContent);
        
        $decoded = json_decode($aiContent, true);
        
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['pages'])) {
            return $decoded;
        }
        
        log_message('error', 'Failed to parse AI response: ' . $aiContent);
        return false;
    }

    /**
     * Create web story from AI data
     */
    private function createWebStoryFromAI($webStoryData, $post)
    {
        $data = [
            'title' => $webStoryData['title'],
            'description' => $webStoryData['description'],
            'link_url' => '',
            'is_generated' => 1,
            'generation_prompt' => 'Generated from article: ' . $post->title,
            'is_active' => 1,
            'display_order' => 1,
            'lang_id' => $post->lang_id,
            'category_id' => $post->category_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->webStoriesModel->builderWebStories->insert($data);
        
        if ($result) {
            return $this->webStoriesModel->db->insertID();
        }
        
        return false;
    }

    /**
     * Create web story pages from AI data
     */
    private function createWebStoryPagesFromAI($webStoryId, $pages, $post, $generateImages = true)
    {
        $webStoryPagesModel = new \App\Models\WebStoryPagesModel();
        
        foreach ($pages as $index => $pageData) {
            $pageData['page_order'] = $index + 1;
            
            if ($generateImages) {
                // Generate image for each page using AI
                log_message('debug', 'Attempting to generate image for page: ' . ($pageData['title'] ?? 'Unknown'));
                $imageData = $this->generatePageImage($pageData, $post);
                if ($imageData) {
                    log_message('debug', 'Image generated successfully: ' . json_encode($imageData));
                    $pageData['background_type'] = 'image';
                    $pageData['background_value'] = $imageData['image_path'];
                    $pageData['image_url'] = $imageData['image_url'];
                    $pageData['image_path'] = $imageData['image_path'];
                } else {
                    log_message('error', 'Failed to generate image for page: ' . ($pageData['title'] ?? 'Unknown'));
                }
            } else {
                log_message('debug', 'Skipping image generation for page: ' . ($pageData['title'] ?? 'Unknown'));
            }
            
            $webStoryPagesModel->addWebStoryPage($webStoryId, $pageData);
        }
    }
    
    /**
     * Schedule image generation in background
     */
    private function scheduleImageGeneration($webStoryId, $pages, $post)
    {
        try {
            // For now, we'll use a simple approach with ignore_user_abort
            // In production, this could be improved with queue systems
            ignore_user_abort(true);
            
            log_message('debug', 'Starting background image generation for web story: ' . $webStoryId);
            
            // Process images one by one to avoid memory issues
            foreach ($pages as $index => $pageData) {
                $pageOrder = $index + 1;
                
                log_message('debug', 'Generating image for page ' . $pageOrder . ': ' . ($pageData['title'] ?? 'Unknown'));
                
                $imageData = $this->generatePageImage($pageData, $post);
                if ($imageData) {
                    // Update the existing page with image data
                    $this->updatePageWithImage($webStoryId, $pageOrder, $imageData);
                    log_message('debug', 'Image updated for page ' . $pageOrder);
                } else {
                    log_message('error', 'Failed to generate image for page ' . $pageOrder);
                }
                
                // Small delay to prevent overwhelming the server
                usleep(500000); // 0.5 seconds
            }
            
            log_message('debug', 'Background image generation completed for web story: ' . $webStoryId);
            
        } catch (\Throwable $e) {
            log_message('error', 'Error in background image generation: ' . $e->getMessage());
        }
    }
    
    /**
     * Update page with generated image
     */
    private function updatePageWithImage($webStoryId, $pageOrder, $imageData)
    {
        $webStoryPagesModel = new \App\Models\WebStoryPagesModel();
        
        $updateData = [
            'background_type' => 'image',
            'background_value' => $imageData['image_path'],
            'image_url' => $imageData['image_url'],
            'image_path' => $imageData['image_path']
        ];
        
        $builder = $webStoryPagesModel->builderWebStoryPages;
        $builder->where('web_story_id', $webStoryId)
                ->where('page_order', $pageOrder)
                ->update($updateData);
        
        log_message('debug', 'Updated page with image data: ' . json_encode($updateData));
    }
    
    /**
     * Generate image for web story page using OpenAI DALL-E
     */
    private function generatePageImage($pageData, $post)
    {
        try {
            // Create image prompt based on page content
            $imagePrompt = $this->buildImagePrompt($pageData, $post);
            
            log_message('debug', 'Generating image with prompt: ' . $imagePrompt);
            
            // Generate image using OpenAI (Web Stories optimized + Brand style)
            $brand = getenv('OPENAI_BRAND_STYLE') ?: '';
            $result = $this->openAIHelper->generateWebStoryImage($imagePrompt, $brand);
            
            if ($result && $result['success']) {
                $savedImageData = false;
                if (isset($result['data'][0]['url']) && !empty($result['data'][0]['url'])) {
                    $imageUrl = $result['data'][0]['url'];
                    $savedImageData = $this->downloadAndSaveImage($imageUrl, $pageData['title']);
                } elseif (isset($result['data'][0]['b64_json']) && !empty($result['data'][0]['b64_json'])) {
                    // Handle base64 response
                    $uploadPath = FCPATH . 'uploads/web_stories/ai_generated/';
                    if (!is_dir($uploadPath)) { mkdir($uploadPath, 0755, true); }
                    $fileName = preg_replace('/[^A-Za-z0-9\-]/', '_', $pageData['title']);
                    $fileName = substr($fileName, 0, 50);
                    $uniqueId = uniqid();
                    $canWebp = function_exists('imagewebp');
                    $ext = $canWebp ? 'webp' : 'png';
                    $filename = $fileName . '_' . $uniqueId . '.' . $ext;
                    $filePath = $uploadPath . $filename;

                    $raw = base64_decode($result['data'][0]['b64_json']);
                    if ($raw !== false) {
                        if ($canWebp) {
                            $tempPng = $uploadPath . 'temp_' . $uniqueId . '.png';
                            file_put_contents($tempPng, $raw);
                            $converted = $this->convertImageToWebP($tempPng, $filePath);
                            if (file_exists($tempPng)) { @unlink($tempPng); }
                            if ($converted) {
                                $relativePath = str_replace(FCPATH, '', $filePath);
                                $savedImageData = [
                                    'image_path' => $relativePath,
                                    'image_url' => base_url($relativePath),
                                    'filename' => $filename
                                ];
                            }
                        } else {
                            file_put_contents($filePath, $raw);
                            $relativePath = str_replace(FCPATH, '', $filePath);
                            $savedImageData = [
                                'image_path' => $relativePath,
                                'image_url' => base_url($relativePath),
                                'filename' => $filename
                            ];
                        }
                    }
                }
                
                if ($savedImageData) {
                    log_message('debug', 'Image generated and saved successfully: ' . $savedImageData['image_path']);
                    return $savedImageData;
                }
            }
            
            log_message('error', 'Failed to generate image for page: ' . ($pageData['title'] ?? 'Unknown'));
            return false;
            
        } catch (\Throwable $e) {
            log_message('error', 'Error generating page image: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build image prompt for DALL-E based on page content
     */
    private function buildImagePrompt($pageData, $post)
    {
        $basePrompt = '';
        // Theme brand colors
        $brandColors = '';
        try {
            $theme = \Config\Globals::$activeTheme ?? null;
            if (!empty($theme)) {
                $c1 = $theme->theme_color ?? '';
                $c2 = $theme->block_color ?? '';
                $c3 = ($theme->theme != 'classic') ? ($theme->mega_menu_color ?? '') : '';
                $palette = array_filter([$c1, $c2, $c3]);
                if (!empty($palette)) { $brandColors = implode(' ', $palette); }
            }
        } catch (\Throwable $e) {}
        
        // Create different prompts based on page type
        switch ($pageData['page_type']) {
            case 'cover':
                $basePrompt = "Professional cover image for: {$pageData['title']}. Modern, eye-catching design";
                break;
            case 'content':
                $basePrompt = "Illustrative image for: {$pageData['title']}. Clean, professional style";
                break;
            case 'cta':
                $basePrompt = "Call-to-action background image. Professional, engaging design with space for text";
                break;
            default:
                $basePrompt = "Professional web story image for: {$pageData['title']}";
        }
        
        // Add context from the original post
        if (!empty($post->title)) {
            $basePrompt .= ". Related to: " . substr($post->title, 0, 100);
        }
        
        // Add web story specific styling
        $basePrompt .= ". High quality, vibrant colors, mobile-friendly, vertical format, clean composition, professional look, suitable for web stories, no text, keep safe margins on edges";

        if (!empty($brandColors)) {
            $basePrompt .= ". Harmonize with brand color palette (" . $brandColors . ")";
        }

        return $basePrompt;
    }
    
    /**
     * Download image from URL and save locally
     */
    private function downloadAndSaveImage($imageUrl, $fileName)
    {
        try {
            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/web_stories/ai_generated/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Generate unique filename
            $fileName = preg_replace('/[^A-Za-z0-9\-]/', '_', $fileName);
            $fileName = substr($fileName, 0, 50); // Limit length
            $uniqueId = uniqid();
            $canWebp = function_exists('imagewebp');
            $ext = $canWebp ? 'webp' : 'png';
            $filename = $fileName . '_' . $uniqueId . '.' . $ext;
            $filePath = $uploadPath . $filename;
            
            // Download image
            $imageData = file_get_contents($imageUrl);
            if ($imageData === false) {
                log_message('error', 'Failed to download image from: ' . $imageUrl);
                return false;
            }
            
            if ($canWebp) {
                // Save temp PNG then convert to WebP
                $tempPath = $uploadPath . 'temp_' . $uniqueId . '.png';
                file_put_contents($tempPath, $imageData);
                $convertedPath = $this->convertImageToWebP($tempPath, $filePath);
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                if ($convertedPath) {
                    $relativePath = str_replace(FCPATH, '', $convertedPath);
                    $imageUrl = base_url($relativePath);
                    return [
                        'image_path' => $relativePath,
                        'image_url' => $imageUrl,
                        'filename' => $filename
                    ];
                }
                return false;
            } else {
                // Save PNG directly when WebP not supported
                file_put_contents($filePath, $imageData);
                $relativePath = str_replace(FCPATH, '', $filePath);
                $imageUrl = base_url($relativePath);
                return [
                    'image_path' => $relativePath,
                    'image_url' => $imageUrl,
                    'filename' => $filename
                ];
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Error downloading and saving image: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Convert image to WebP format
     */
    private function convertImageToWebP($sourcePath, $destinationPath)
    {
        try {
            // Check if source file exists
            if (!file_exists($sourcePath)) {
                log_message('error', 'Source image file does not exist: ' . $sourcePath);
                return false;
            }
            
            // Create image resource from PNG
            $sourceImage = imagecreatefrompng($sourcePath);
            if ($sourceImage === false) {
                log_message('error', 'Failed to create image resource from PNG: ' . $sourcePath);
                return false;
            }
            
            // Convert to WebP
            if (!function_exists('imagewebp')) {
                log_message('error', 'imagewebp function is not available; GD WebP support missing');
                return false;
            }
            $success = imagewebp($sourceImage, $destinationPath, 80);
            imagedestroy($sourceImage);
            
            if ($success) {
                log_message('debug', 'Image converted to WebP successfully: ' . $destinationPath);
                return $destinationPath;
            } else {
                log_message('error', 'Failed to convert image to WebP: ' . $sourcePath);
                return false;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error converting image to WebP: ' . $e->getMessage());
            return false;
        }
    }
}
