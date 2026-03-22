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
        // Debug logging
        log_message('debug', 'WebStoriesModel::addWebStory called');
        log_message('debug', 'POST data: ' . json_encode($_POST));
        log_message('debug', 'FILES data: ' . json_encode($_FILES));
        
        $data = [
            'title' => inputPost('title'),
            'description' => inputPost('description'),
            'link_url' => inputPost('link_url'),
            'is_generated' => inputPost('is_generated') ? 1 : 0,
            'generation_prompt' => inputPost('generation_prompt'),
            'openai_image_id' => inputPost('openai_image_id'),
            'is_active' => inputPost('is_active') ? 1 : 0,
            'display_order' => inputPost('display_order') ?: 1,
            'lang_id' => inputPost('lang_id') ?: $this->activeLang->id,
            'category_id' => inputPost('category_id') ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        log_message('debug', 'Prepared data for insert: ' . json_encode($data));

        // Handle image upload or generation
        if (!empty($_FILES['image']['name'])) {
            log_message('debug', 'Processing image upload');
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
            'link_url' => inputPost('link_url'),
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

    public function getWebStory($id)
    {
        return $this->builderWebStories->where('id', clrNum($id))->get()->getRow();
    }

    public function deleteWebStory($id)
    {
        $webStory = $this->getWebStory($id);
        if (!empty($webStory)) {
            if (!empty($webStory->image_path)) {
                $fullPath = FCPATH . $webStory->image_path;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            return $this->builderWebStories->where('id', clrNum($id))->delete();
        }
        return false;
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
            return str_replace(FCPATH, '', $webpPath);
        }
        
        return null;
    }

    private function saveGeneratedImage($imageUrl)
    {
        try {
            $imageData = file_get_contents($imageUrl);
            if ($imageData === false) {
                return null;
            }

            $fileName = uniqid() . '_' . time() . '.webp';
            $uploadPath = FCPATH . 'uploads/web_stories/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filePath = $uploadPath . $fileName;
            
            // Save temporary file first
            $tempFile = $uploadPath . 'temp_' . $fileName;
            if (file_put_contents($tempFile, $imageData)) {
                // Convert to WebP
                $webpPath = $this->convertToWebP($tempFile);
                // Delete temp file if different from webp
                if ($tempFile !== $webpPath) {
                    unlink($tempFile);
                }
                return str_replace(FCPATH, '', $webpPath);
            }
        } catch (Exception $e) {
            log_message('error', 'Error saving generated image: ' . $e->getMessage());
        }
        return null;
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
        } catch (Exception $e) {
            log_message('error', 'Error converting image to WebP: ' . $e->getMessage());
        }

        return $imagePath;
    }
}