<?php

namespace App\Controllers;

use App\Models\CmsPageModel;
use App\Models\CmsTemplateModel;
use App\Models\PageModel;

class PagesAdminController extends BaseAdminController
{
    protected $pages;
    private function ensureCmsAccess()
    {
        if (!hasPermission('pages') && !hasPermission('admin_panel')) {
            redirectToUrl(base_url());
            exit();
        }
    }

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->pages = new CmsPageModel();
    }

    public function index()
    {
        $this->ensureCmsAccess();
        $data['title'] = 'Páginas (CMS)';
        $data['pages'] = $this->pages->all();
        echo view('admin/includes/_header', $data);
        echo view('admin/cms/pages/index', $data);
        echo view('admin/includes/_footer');
    }

    public function add()
    {
        $this->ensureCmsAccess();
        $title = trim((string)inputPost('title'));
        $slug  = trim((string)inputPost('slug'));
        if ($title !== '' && $slug !== '') {
            $id = $this->pages->create(['title'=>$title,'slug'=>$slug,'status'=>'draft','data_json'=>json_encode(['blocks'=>[]])]);
            setSuccessMessage('Página criada');
            return redirect()->to(adminUrl('cms-pages/builder/'.$id));
        }
        setErrorMessage('Informe título e slug');
        return redirect()->back();
    }

    public function edit($id)
    {
        $this->ensureCmsAccess();
        $id = (int)$id;
        $page = $this->pages->getById($id);
        if (!$page) { setErrorMessage('Página não encontrada'); return redirect()->back(); }
        $data['title'] = 'Editar Página';
        $data['page'] = $page;
        echo view('admin/includes/_header', $data);
        echo view('admin/cms/pages/edit', $data);
        echo view('admin/includes/_footer');
    }

    public function editPost($id)
    {
        $this->ensureCmsAccess();
        $id = (int)$id;
        $title = trim((string)inputPost('title'));
        $slug  = trim((string)inputPost('slug'));
        if ($title!=='' && $slug!=='') {
            $this->pages->updatePage($id, ['title'=>$title,'slug'=>$slug]);
            setSuccessMessage('Dados atualizados');
        }
        return redirect()->to(adminUrl('cms-pages'));
    }

    public function builder($id)
    {
        $this->ensureCmsAccess();
        $id = (int)$id;
        $page = $this->pages->getById($id);
        if (!$page) { setErrorMessage('Página não encontrada'); return redirect()->back(); }
        $data['title'] = 'Builder - ' . $page->title;
        $data['page'] = $page;
        $data['layout'] = @json_decode($page->data_json, true) ?: ['blocks'=>[]];
        $tmpl = new CmsTemplateModel();
        $data['templates'] = $tmpl->all('section');
        echo view('admin/includes/_header', $data);
        echo view('admin/cms/pages/builder', $data);
        echo view('admin/includes/_footer');
    }

    public function saveBuilder($id)
    {
        if (!hasPermission('pages') && !hasPermission('admin_panel')) { return $this->response->setJSON(['success'=>false]); }
        $id = (int)$id;
        $json = (string)inputPost('data_json');
        $decoded = @json_decode($json, true);
        if (!is_array($decoded) || !array_key_exists('blocks', $decoded)) {
            return $this->response->setJSON(['success'=>false, 'error'=>'invalid_json']);
        }
        $page = $this->pages->getById($id);
        // Guard: evitar salvar rascunho vazio por engano se já há conteúdo no draft ou publicado
        if (empty($decoded['blocks'])) {
            $prev = @json_decode($page->data_json ?? '', true);
            $pub  = @json_decode($page->published_json ?? '', true);
            $hadContent = (is_array($prev) && !empty($prev['blocks'])) || (is_array($pub) && !empty($pub['blocks']));
            if ($hadContent) {
                return $this->response->setJSON(['success'=>false, 'error'=>'empty_blocks_abort']);
            }
        }
        $this->pages->updatePage($id, ['data_json' => $json]);
        return $this->response->setJSON(['success'=>true]);
    }

    public function publish($id)
    {
        $this->ensureCmsAccess();
        $id = (int)$id;
        $page = $this->pages->getById($id);
        if (!$page) { setErrorMessage('Página não encontrada'); return redirect()->to(adminUrl('cms-pages')); }
        $draft = @json_decode($page->data_json ?? '', true);
        // Evitar publicar rascunho vazio e sobrescrever conteúdo publicado
        if (is_array($draft) && !empty($draft['blocks'])) {
            $this->pages->publish($id);
            setSuccessMessage('Página publicada');
        } else {
            setErrorMessage('Rascunho vazio — publicação abortada. Conteúdo publicado anterior preservado.');
        }
        return redirect()->to(adminUrl('cms-pages'));
    }

    // Restaurar rascunho a partir do conteúdo publicado
    public function restore($id)
    {
        $this->ensureCmsAccess();
        $id = (int)$id;
        $page = $this->pages->getById($id);
        if (!$page) { setErrorMessage('Página não encontrada'); return redirect()->to(adminUrl('cms-pages')); }
        if (!empty($page->published_json)) {
            $this->pages->updatePage($id, ['data_json' => $page->published_json]);
            setSuccessMessage('Rascunho restaurado a partir do publicado.');
        } else {
            setErrorMessage('Não há conteúdo publicado para restaurar.');
        }
        return redirect()->to(adminUrl('cms-pages'));
    }

    public function delete($id)
    {
        $this->ensureCmsAccess();
        $this->pages->db->table('cms_pages')->where('id', clrNum($id))->delete();
        setSuccessMessage('Página removida');
        return redirect()->to(adminUrl('cms-pages'));
    }

    public function saveTemplate()
    {
        if (!hasPermission('pages') && !hasPermission('admin_panel')) { return $this->response->setJSON(['success'=>false]); }
        $title = trim((string)inputPost('title'));
        $json = (string)inputPost('json');
        $decoded = @json_decode($json, true);
        if ($title === '' || !is_array($decoded)) return $this->response->setJSON(['success'=>false]);
        $tmpl = new CmsTemplateModel();
        $tmpl->create(['title'=>$title, 'type'=>'section', 'json'=>$json]);
        return $this->response->setJSON(['success'=>true]);
    }

    public function deleteTemplate($id)
    {
        $this->ensureCmsAccess();
        $tmpl = new CmsTemplateModel();
        $tmpl->deleteById((int)$id);
        setSuccessMessage('Template removido');
        return redirect()->back();
    }

    // Link a legacy page (from /admin/pages) to a Visual CMS draft and open builder
    public function linkLegacy($legacyId)
    {
        checkPermission('pages');
        $legacyId = (int)$legacyId;
        $pm = new PageModel();
        $page = $pm->getPageById($legacyId);
        if (!$page) { setErrorMessage('Página não encontrada'); return redirect()->to(adminUrl('pages')); }
        $slug = $page->slug ?: ('page-'.$legacyId);
        $title = $page->title ?: 'Página '.$legacyId;
        $cms = new CmsPageModel();
        $row = $cms->getBySlug($slug);
        if (!$row) {
            $id = $cms->create(['title'=>$title, 'slug'=>$slug, 'status'=>'draft', 'data_json'=>json_encode(['blocks'=>[]])]);
            return redirect()->to(adminUrl('cms-pages/builder/'.$id));
        }
        return redirect()->to(adminUrl('cms-pages/builder/'.$row->id));
    }

    // Setup: create CMS tables if migrations not executed
    public function runSetup()
    {
        $this->ensureCmsAccess();
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $created = [];
        $ensure = function(string $table, callable $define) use ($db, $forge, &$created) {
            if (! $db->tableExists($table)) { $define(); $forge->createTable($table, true); $created[] = $table; }
        };
        $ensure('cms_pages', function() use($forge){
            $forge->addField([
                'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
                'title' => ['type'=>'VARCHAR','constraint'=>255],
                'slug' => ['type'=>'VARCHAR','constraint'=>255],
                'status' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
                'data_json' => ['type'=>'LONGTEXT','null'=>true],
                'published_json' => ['type'=>'LONGTEXT','null'=>true],
                'created_at' => ['type'=>'DATETIME','null'=>true],
                'updated_at' => ['type'=>'DATETIME','null'=>true],
                'published_at' => ['type'=>'DATETIME','null'=>true],
            ]);
            $forge->addKey('id', true);
            $forge->addKey('slug');
        });
        $ensure('cms_templates', function() use($forge){
            $forge->addField([
                'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
                'title' => ['type'=>'VARCHAR','constraint'=>255],
                'type' => ['type'=>'VARCHAR','constraint'=>50,'default'=>'section'],
                'json' => ['type'=>'LONGTEXT','null'=>true],
                'created_at' => ['type'=>'DATETIME','null'=>true],
            ]);
            $forge->addKey('id', true);
        });
        setSuccessMessage('Tabelas criadas: ' . (empty($created) ? 'nenhuma (já existiam)' : implode(', ', $created)));
        return redirect()->to(adminUrl('cms-pages'));
    }

    // Seed a ready-to-use Wealth LP as CMS page
    public function seedWealthLp()
    {
        $this->ensureCmsAccess();
        $m = new CmsPageModel();
        $slug = 'wealth';
        $page = $m->getBySlug($slug);
        $json = json_encode([
            'blocks' => [
                [
                    'type' => 'section',
                    'layout' => [7,5],
                    'cols' => [
                        [
                            ['type'=>'heading','content'=>'Wealth Manager — Gestão de Patrimônio Inteligente'],
                            ['type'=>'paragraph','content'=>'Mapeie seu cenário financeiro, simule sua liberdade financeira e receba um plano de ação com apoio de um agente consultor. Primeiro passo gratuito.'],
                            ['type'=>'button','text'=>'Começar Agora','url'=>base_url('wealth/conversa')],
                        ],
                        [
                            ['type'=>'image','url'=>base_url('assets/img/newsletter.webp'),'text'=>'GX Capital']
                        ]
                    ]
                ],
                [
                    'type'=>'cards-grid',
                    'grid_cols'=>3,
                    'items'=>[
                        ['image'=>'','title'=>'Diagnóstico rápido','content'=>'Entenda seu potencial de poupança e seu ponto de partida.'],
                        ['image'=>'','title'=>'Projeção realista','content'=>'Evolução do patrimônio e tempo até a Independência Financeira.'],
                        ['image'=>'','title'=>'Plano de ação','content'=>'Aportes e alocação sugeridos conforme seu perfil de risco.'],
                    ]
                ],
                [
                    'type'=>'section',
                    'layout'=>[6,6],
                    'cols'=>[
                        [
                            ['type'=>'heading','content'=>'Como funciona'],
                            ['type'=>'paragraph','content'=>'1) Conte um pouco sobre você\n2) Veja um resumo com KPIs e projeções\n3) Agende uma conversa gratuita com nosso consultor'],
                            ['type'=>'button','text'=>'Agendar consultoria','url'=>base_url('wealth/agendar')],
                        ],
                        [
                            ['type'=>'banner','content'=>'Seguro, confidencial e sem compromisso'],
                            ['type'=>'paragraph','content'=>'Usamos premissas transparentes e respeitamos sua privacidade.'],
                        ]
                    ]
                ],
                [
                    'type'=>'accordion',
                    'single_open'=>true,
                    'items'=>[
                        ['title'=>'O uso é gratuito?','content'=>'Sim, a primeira sessão é gratuita e sem compromisso.'],
                        ['title'=>'Preciso ter investimentos?','content'=>'Não. Vamos partir do seu cenário atual e traçar um plano.'],
                        ['title'=>'Meus dados ficam seguros?','content'=>'Sim. Seus dados são tratados de forma confidencial.'],
                    ]
                ],
                [
                    'type'=>'section',
                    'layout'=>[8,4],
                    'cols'=>[
                        [
                            ['type'=>'heading','content'=>'Crie sua conta e comece agora'],
                            ['type'=>'paragraph','content'=>'Leva menos de 1 minuto. Você poderá continuar sua jornada a qualquer momento.'],
                        ],
                        [
                            ['type'=>'register']
                        ]
                    ]
                ]
            ]
        ], JSON_UNESCAPED_UNICODE);

        if (!$page) {
            $id = $m->create(['title'=>'Wealth Manager','slug'=>$slug,'status'=>'draft','data_json'=>$json,'published_json'=>$json]);
            $m->publish($id);
            setSuccessMessage('LP Wealth criada e publicada em /p/wealth');
        } else {
            $m->updatePage($page->id, ['data_json'=>$json, 'published_json'=>$json, 'status'=>'published']);
            setSuccessMessage('LP Wealth atualizada e publicada em /p/wealth');
        }
        return redirect()->to(adminUrl('cms-pages'));
    }
}
