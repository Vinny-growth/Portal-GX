<?php namespace App\Models;

use CodeIgniter\Model;

class WebStoriesModel extends BaseModel
{
    protected $builderWebStories;

    public function __construct()
    {
        parent::__construct();
        $this->builderWebStories = $this->db->table('web_stories');
    }

    public function addWebStory()
    {
        log_message('debug', 'WebStoriesModel::addWebStory called');
        
        $data = [
            'title' => inputPost('title'),
            'description' => inputPost('description'),
            'link_url' => self::sanitizeOutboundUrl(inputPost('link_url')),
            'is_generated' => inputPost('is_generated') ? 1 : 0,
            'generation_prompt' => inputPost('generation_prompt'),
            'openai_image_id' => inputPost('openai_image_id'),
            'is_active' => inputPost('is_active') ? 1 : 0,
            'display_order' => inputPost('display_order') ?: 1,
            'lang_id' => inputPost('lang_id') ?: $this->activeLang->id,
            'category_id' => inputPost('category_id') ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload or generation
        if (!empty($_FILES['image']['name'])) {
            $data['image_path'] = $this->uploadWebStoryImage();
        } elseif (!empty(inputPost('generated_image_url'))) {
            log_message('debug', 'Processing generated image');
            $data['image_url'] = inputPost('generated_image_url');
            $data['image_path'] = $this->saveGeneratedImage(inputPost('generated_image_url'));
        }

        $result = $this->builderWebStories->insert($data);
        log_message('debug', 'Insert result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        if (!$result) {
            log_message('error', 'Database error: ' . $this->db->error());
            return false;
        }
        
        $insertedId = $this->db->insertID();
        log_message('debug', 'Inserted ID: ' . $insertedId);
        
        return $insertedId;
    }

    public function editWebStory($id)
    {
        $data = [
            'title' => inputPost('title'),
            'description' => inputPost('description'),
            'link_url' => self::sanitizeOutboundUrl(inputPost('link_url')),
            'is_generated' => inputPost('is_generated') ? 1 : 0,
            'generation_prompt' => inputPost('generation_prompt'),
            'openai_image_id' => inputPost('openai_image_id'),
            'is_active' => inputPost('is_active') ? 1 : 0,
            'display_order' => inputPost('display_order') ?: 1,
            'lang_id' => inputPost('lang_id') ?: $this->activeLang->id,
            'category_id' => inputPost('category_id') ?: null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload or generation
        if (!empty($_FILES['image']['name'])) {
            $webStory = $this->getWebStory($id);
            if (!empty($webStory) && !empty($webStory->image_path)) {
                $fullPath = FCPATH . $webStory->image_path;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $data['image_path'] = $this->uploadWebStoryImage();
        } elseif (!empty(inputPost('generated_image_url'))) {
            $webStory = $this->getWebStory($id);
            if (!empty($webStory) && !empty($webStory->image_path)) {
                $fullPath = FCPATH . $webStory->image_path;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $data['image_url'] = inputPost('generated_image_url');
            $data['image_path'] = $this->saveGeneratedImage(inputPost('generated_image_url'));
        }

        return $this->builderWebStories->where('id', clrNum($id))->update($data);
    }

    public function getWebStories($langId = null)
    {
        if ($langId) {
            return $this->builderWebStories->where('lang_id', clrNum($langId))->orderBy('display_order', 'ASC')->get()->getResult();
        }
        return $this->builderWebStories->orderBy('display_order', 'ASC')->get()->getResult();
    }

    public function getActiveWebStories($langId = null, $limit = null)
    {
        $builder = $this->builderWebStories->where('is_active', 1);

        if ($langId) {
            $builder->where('lang_id', clrNum($langId));
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->orderBy('display_order', 'ASC')->get()->getResult();
    }

    public function getActiveWebStoriesPaginated($langId, $limit, $offset)
    {
        $limit  = max(1, min(50, (int) $limit));
        $offset = max(0, (int) $offset);

        $builder = $this->db->table('web_stories')->where('is_active', 1);
        if ($langId) {
            $builder->where('lang_id', clrNum($langId));
        }

        return $builder
            ->orderBy('display_order', 'ASC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }

    public function countActiveWebStories($langId)
    {
        $builder = $this->db->table('web_stories')->where('is_active', 1);
        if ($langId) {
            $builder->where('lang_id', clrNum($langId));
        }
        return (int) $builder->countAllResults();
    }

    /**
     * Sanitize a user-supplied URL for outbound redirects.
     * Returns the URL only if it parses cleanly and uses http/https.
     */
    public static function sanitizeOutboundUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return '';
        }
        return $url;
    }

    public function getWebStory($id)
    {
        return $this->builderWebStories->where('id', clrNum($id))->get()->getRow();
    }

    public function deleteWebStory($id)
    {
        $webStory = $this->getWebStory($id);
        if (empty($webStory)) {
            return false;
        }

        $id = clrNum($id);
        $this->unlinkStoryAssets($webStory, $id);

        // FK CASCADE in web_story_pages takes care of the rows; we removed
        // the files manually above so they don't pile up in uploads/web_stories.
        return $this->builderWebStories->where('id', $id)->delete();
    }

    /**
     * Remove the cover image of the story plus any image_path stored in
     * web_story_pages. Image_url (remote URL) is kept untouched.
     */
    private function unlinkStoryAssets($webStory, $storyId)
    {
        if (!empty($webStory->image_path)) {
            $fullPath = FCPATH . ltrim($webStory->image_path, '/');
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $pages = $this->db->table('web_story_pages')
            ->select('image_path')
            ->where('web_story_id', $storyId)
            ->get()
            ->getResult();

        foreach ($pages as $page) {
            if (empty($page->image_path)) {
                continue;
            }
            $fullPath = FCPATH . ltrim($page->image_path, '/');
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    public function updateDisplayOrder($id, $order)
    {
        return $this->builderWebStories->where('id', clrNum($id))->update(['display_order' => clrNum($order)]);
    }

    public function toggleStatus($id)
    {
        $webStory = $this->getWebStory($id);
        if (!empty($webStory)) {
            $newStatus = $webStory->is_active == 1 ? 0 : 1;
            return $this->builderWebStories->where('id', clrNum($id))->update(['is_active' => $newStatus]);
        }
        return false;
    }

    public function incrementViewCount($id)
    {
        return $this->builderWebStories->where('id', clrNum($id))->set('view_count', 'view_count + 1', false)->update();
    }

    public function incrementClickCount($id)
    {
        return $this->builderWebStories->where('id', clrNum($id))->set('click_count', 'click_count + 1', false)->update();
    }

    /**
     * Increment view_count only if the request looks human and not already
     * counted in this session+day. Mirrors PostModel::incrementPostViews
     * — bots/crawlers don't inflate the metric, and reload spam is dampened.
     */
    public function recordViewIfHuman($id, $request)
    {
        return $this->recordHitIfHuman($id, $request, 'wsv_', 'view_count');
    }

    public function recordClickIfHuman($id, $request)
    {
        return $this->recordHitIfHuman($id, $request, 'wsc_', 'click_count');
    }

    private function recordHitIfHuman($id, $request, $sessionPrefix, $column)
    {
        $id = (int) clrNum($id);
        if ($id <= 0) {
            return false;
        }

        if ($request) {
            try {
                $agent = $request->getUserAgent();
                if ($agent && method_exists($agent, 'isRobot') && $agent->isRobot()) {
                    return false;
                }
            } catch (\Throwable $e) {
                // Fall through — better to count than to silently lose data.
            }
        }

        $sessionKey = $sessionPrefix . $id;
        $today = date('Y-m-d');
        if (function_exists('getSession') && getSession($sessionKey) === $today) {
            return false;
        }
        if (function_exists('setSession')) {
            setSession($sessionKey, $today);
        }

        return $this->builderWebStories
            ->where('id', $id)
            ->set($column, $column . ' + 1', false)
            ->update();
    }

    public function getWebStoriesWithCategories($langId = null)
    {
        $builder = $this->db->table('web_stories ws')
            ->select('ws.*, c.name as category_name')
            ->join('categories c', 'c.id = ws.category_id', 'left');
        
        if ($langId) {
            $builder->where('ws.lang_id', clrNum($langId));
        }
        
        return $builder->orderBy('ws.display_order', 'ASC')->get()->getResult();
    }

    private function uploadWebStoryImage()
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileName = uniqid() . '_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uploadPath = FCPATH . 'uploads/web_stories/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $tempPath = $_FILES['image']['tmp_name'];
        $targetPath = $uploadPath . $fileName;

        if (move_uploaded_file($tempPath, $targetPath)) {
            // Convert to WebP if not already
            $webpPath = $this->convertToWebP($targetPath);
            $this->generatePosterVariants($webpPath);
            return str_replace(FCPATH, '', $webpPath);
        }

        return null;
    }

    /**
     * Generate AMP poster variants (3:4 portrait, 1:1 square, 4:3 landscape)
     * next to the original cover image. Files are named with -3x4 / -1x1 / -4x3
     * suffixes so the AMP template can deterministically resolve them.
     *
     * Targets follow Google Web Stories spec — portrait is the only required
     * one; square and landscape ship for richer results in Discover/Search.
     */
    public function generatePosterVariants($absoluteImagePath)
    {
        if (empty($absoluteImagePath) || !is_file($absoluteImagePath)) {
            return;
        }
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromwebp')) {
            return;
        }

        $variants = [
            '-3x4' => [720,  960],   // poster-portrait-src
            '-1x1' => [720,  720],   // poster-square-src
            '-4x3' => [928,  696],   // poster-landscape-src
        ];

        foreach ($variants as $suffix => [$targetW, $targetH]) {
            $this->writeCenterCroppedWebp($absoluteImagePath, $suffix, $targetW, $targetH);
        }
    }

    private function writeCenterCroppedWebp($sourcePath, $suffix, $targetW, $targetH)
    {
        $info = pathinfo($sourcePath);
        $destPath = $info['dirname'] . '/' . $info['filename'] . $suffix . '.webp';

        $sourceImage = $this->readImageResource($sourcePath);
        if ($sourceImage === false) {
            return;
        }

        $srcW = imagesx($sourceImage);
        $srcH = imagesy($sourceImage);
        $srcAspect = $srcW / max(1, $srcH);
        $dstAspect = $targetW / $targetH;

        if ($srcAspect > $dstAspect) {
            $cropH = $srcH;
            $cropW = (int) round($srcH * $dstAspect);
            $cropX = (int) round(($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $srcW;
            $cropH = (int) round($srcW / $dstAspect);
            $cropX = 0;
            $cropY = (int) round(($srcH - $cropH) / 2);
        }

        $dest = imagecreatetruecolor($targetW, $targetH);
        try {
            imagecopyresampled(
                $dest, $sourceImage,
                0, 0, $cropX, $cropY,
                $targetW, $targetH,
                $cropW, $cropH
            );
            imagewebp($dest, $destPath, 82);
        } finally {
            imagedestroy($dest);
            imagedestroy($sourceImage);
        }
    }

    private function readImageResource($sourcePath)
    {
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        try {
            switch ($ext) {
                case 'webp': return imagecreatefromwebp($sourcePath);
                case 'jpg':
                case 'jpeg': return imagecreatefromjpeg($sourcePath);
                case 'png':  return imagecreatefrompng($sourcePath);
                case 'gif':  return imagecreatefromgif($sourcePath);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WebStoriesModel: failed to open image - ' . $e->getMessage());
        }
        return false;
    }

    private function saveGeneratedImage($imageUrl)
    {
        try {
            $imageData = $this->fetchRemoteImage($imageUrl);
            if ($imageData === null) {
                return null;
            }

            $fileName = uniqid() . '_' . time() . '.webp';
            $uploadPath = FCPATH . 'uploads/web_stories/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Save temporary file first
            $tempFile = $uploadPath . 'temp_' . $fileName;
            if (file_put_contents($tempFile, $imageData)) {
                // Convert to WebP
                $webpPath = $this->convertToWebP($tempFile);
                // Delete temp file if different from webp
                if ($tempFile !== $webpPath && is_file($tempFile)) {
                    @unlink($tempFile);
                }
                $this->generatePosterVariants($webpPath);
                return str_replace(FCPATH, '', $webpPath);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error saving generated image: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Download a remote image with bounded curl options. Replaces the bare
     * file_get_contents() call so we don't hang on slow URLs and don't load
     * unbounded blobs into memory.
     */
    private function fetchRemoteImage($imageUrl)
    {
        if (empty($imageUrl)) {
            return null;
        }

        $ch = curl_init($imageUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'WebStoriesModel: image download failed (HTTP ' . $httpCode . ') ' . $error);
            return null;
        }
        return $body;
    }

    private function convertToWebP($imagePath)
    {
        if (!file_exists($imagePath)) {
            return $imagePath;
        }

        $pathInfo = pathinfo($imagePath);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        // If already WebP, return as is
        if (strtolower($pathInfo['extension']) === 'webp') {
            return $imagePath;
        }

        try {
            $sourceImage = null;
            
            switch (strtolower($pathInfo['extension'])) {
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $sourceImage = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($imagePath);
                    break;
                default:
                    return $imagePath;
            }

            if ($sourceImage !== false && function_exists('imagewebp')) {
                if (imagewebp($sourceImage, $webpPath, 80)) {
                    imagedestroy($sourceImage);
                    // Delete original if conversion successful
                    unlink($imagePath);
                    return $webpPath;
                }
                imagedestroy($sourceImage);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error converting image to WebP: ' . $e->getMessage());
            if (isset($sourceImage) && $sourceImage !== false && $sourceImage !== null) {
                @imagedestroy($sourceImage);
            }
        }

        return $imagePath;
    }
}