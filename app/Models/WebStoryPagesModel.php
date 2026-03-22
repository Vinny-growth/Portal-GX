<?php

namespace App\Models;

use CodeIgniter\Model;

class WebStoryPagesModel extends CommonModel
{
    protected $builderWebStoryPages;

    public function __construct()
    {
        parent::__construct();
        $this->builderWebStoryPages = $this->db->table('web_story_pages');
    }

    public function addWebStoryPage($webStoryId, $pageData)
    {
        $data = [
            'web_story_id' => clrNum($webStoryId),
            'page_order' => $pageData['page_order'] ?? 1,
            'page_type' => $pageData['page_type'] ?? 'content',
            'title' => $pageData['title'] ?? '',
            'content' => $pageData['content'] ?? '',
            'background_type' => $pageData['background_type'] ?? 'gradient',
            'background_value' => $pageData['background_value'] ?? '',
            'image_url' => $pageData['image_url'] ?? '',
            'image_path' => $pageData['image_path'] ?? '',
            'video_url' => $pageData['video_url'] ?? '',
            'cta_text' => $pageData['cta_text'] ?? '',
            'cta_url' => $pageData['cta_url'] ?? '',
            'text_color' => $pageData['text_color'] ?? '#FFFFFF',
            'text_position' => $pageData['text_position'] ?? 'center',
            'font_size' => $pageData['font_size'] ?? 'medium',
            'animation' => $pageData['animation'] ?? '',
            'duration' => clrNum($pageData['duration'] ?? 5),
            'is_active' => $pageData['is_active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        log_message('debug', 'WebStoryPage data to insert: ' . json_encode($data));
        $result = $this->builderWebStoryPages->insert($data);
        log_message('debug', 'WebStoryPage insert result: ' . ($result ? 'success' : 'failed'));
        return $result;
    }

    public function updateWebStoryPage($pageId, $pageData)
    {
        $data = [
            'page_order' => $pageData['page_order'] ?? 1,
            'page_type' => $pageData['page_type'] ?? 'content',
            'title' => $pageData['title'] ?? '',
            'content' => $pageData['content'] ?? '',
            'background_type' => $pageData['background_type'] ?? 'gradient',
            'background_value' => $pageData['background_value'] ?? '',
            'image_url' => $pageData['image_url'] ?? '',
            'image_path' => $pageData['image_path'] ?? '',
            'video_url' => $pageData['video_url'] ?? '',
            'cta_text' => $pageData['cta_text'] ?? '',
            'cta_url' => $pageData['cta_url'] ?? '',
            'text_color' => $pageData['text_color'] ?? '#FFFFFF',
            'text_position' => $pageData['text_position'] ?? 'center',
            'font_size' => $pageData['font_size'] ?? 'medium',
            'animation' => $pageData['animation'] ?? '',
            'duration' => clrNum($pageData['duration'] ?? 5),
            'is_active' => $pageData['is_active'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->builderWebStoryPages->where('id', clrNum($pageId))->update($data);
    }

    public function getWebStoryPages($webStoryId)
    {
        return $this->builderWebStoryPages
            ->where('web_story_id', clrNum($webStoryId))
            ->where('is_active', 1)
            ->orderBy('page_order', 'ASC')
            ->get()
            ->getResult();
    }

    public function getWebStoryPage($pageId)
    {
        return $this->builderWebStoryPages
            ->where('id', clrNum($pageId))
            ->get()
            ->getRow();
    }

    public function deleteWebStoryPage($pageId)
    {
        return $this->builderWebStoryPages->where('id', clrNum($pageId))->delete();
    }

    public function deleteWebStoryPages($webStoryId)
    {
        return $this->builderWebStoryPages->where('web_story_id', clrNum($webStoryId))->delete();
    }

    public function updatePageOrder($pageId, $order)
    {
        return $this->builderWebStoryPages
            ->where('id', clrNum($pageId))
            ->update(['page_order' => clrNum($order)]);
    }

    public function duplicatePage($pageId)
    {
        $page = $this->getWebStoryPage($pageId);
        if (!$page) {
            return false;
        }

        // Get max order for this web story
        $maxOrder = $this->builderWebStoryPages
            ->selectMax('page_order')
            ->where('web_story_id', $page->web_story_id)
            ->get()
            ->getRow()
            ->page_order ?? 0;

        $newPageData = [
            'page_order' => $maxOrder + 1,
            'page_type' => $page->page_type,
            'title' => $page->title . ' (Copy)',
            'content' => $page->content,
            'background_type' => $page->background_type,
            'background_value' => $page->background_value,
            'image_url' => $page->image_url,
            'image_path' => $page->image_path,
            'video_url' => $page->video_url,
            'cta_text' => $page->cta_text,
            'cta_url' => $page->cta_url,
            'text_color' => $page->text_color,
            'text_position' => $page->text_position,
            'font_size' => $page->font_size,
            'animation' => $page->animation,
            'duration' => $page->duration,
            'is_active' => 1
        ];

        return $this->addWebStoryPage($page->web_story_id, $newPageData);
    }

    private function uploadPageImage()
    {
        if (!isset($_FILES['page_image']) || $_FILES['page_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileName = uniqid() . '_' . time() . '.' . pathinfo($_FILES['page_image']['name'], PATHINFO_EXTENSION);
        $uploadPath = FCPATH . 'uploads/web_stories/pages/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $tempPath = $_FILES['page_image']['tmp_name'];
        $targetPath = $uploadPath . $fileName;

        if (move_uploaded_file($tempPath, $targetPath)) {
            return 'uploads/web_stories/pages/' . $fileName;
        }

        return null;
    }
}