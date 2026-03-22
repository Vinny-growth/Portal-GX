<?php

namespace App\Controllers;

use App\Models\CmsPageModel;

class CmsController extends BaseController
{
    public function view($slug)
    {
        $slug = trim($slug);
        $m = new CmsPageModel();
        $page = $m->getBySlug($slug);
        if (!$page || empty($page->published_json)) {
            return redirect()->to(base_url())->with('mes_not_found', 'Página não encontrada');
        }
        $data = setPageMeta($page->title);
        $data['page'] = $page;
        $data['layout'] = @json_decode($page->published_json, true) ?: [];
        echo loadView('partials/_header', $data);
        echo loadView('cms/page', $data);
        echo loadView('partials/_footer', $data);
    }
}

