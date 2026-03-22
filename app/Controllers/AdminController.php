<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\EmailModel;
use App\Models\LanguageModel;
use App\Models\NewsletterModel;
use App\Models\PageModel;
use App\Models\PollModel;
use App\Models\PostAdminModel;
use App\Models\PostModel;
use App\Models\RewardModel;
use App\Models\SettingsModel;
use App\Models\SimLeadModel;
use App\Models\SitemapModel;
use Config\Globals;

class AdminController extends BaseAdminController
{
    protected $postAdminModel;
    protected $settingsModel;
    protected $pageModel;
    protected $authModel;
    protected $commonModel;
    protected $newsletterModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->postAdminModel = new PostAdminModel();
        $this->settingsModel = new SettingsModel();
        $this->pageModel = new PageModel();
        $this->authModel = new AuthModel();
        $this->commonModel = new CommonModel();
        $this->newsletterModel = new NewsletterModel();

        if (checkCronTime(1)) {
            //delete old posts
            $this->postAdminModel->deleteOldPosts();
            //delete old page views
            $postModel = new PostModel();
            $postModel->deleteOldPageviews();
            //delete old sessions
            $this->settingsModel->deleteOldSessions();
            //update cron time
            $this->settingsModel->setLastCronUpdate();
        }
    }

    /**
     * Index Page
     */
    public function index()
    {
        checkPermission('admin_panel');
        $data['title'] = trans("index");
        $data['latestComments'] = $this->commonModel->getLatestComments(1, 5);
        $data['latestPendingComments'] = $this->commonModel->getLatestComments(0, 5);
        $data['latestContactMessages'] = $this->commonModel->getContactMessages(5);
        $data['latestUsers'] = $this->authModel->getLatestUsers();
        $data['postsCount'] = $this->postAdminModel->getPostsCount();
        $data['pendingPostsCount'] = $this->postAdminModel->getPendingPostsCount();
        $data['draftsCount'] = $this->postAdminModel->getDraftsCount();
        $data['scheduledPostsCount'] = $this->postAdminModel->getScheduledPostsCount();
        $data['panelSettings'] = panelSettings();

        $this->commonModel->fixNullRecords();

        echo view('admin/includes/_header', $data);
        echo view('admin/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Navigation
     */
    public function navigation()
    {
        checkPermission('navigation');
        $data["selectedLang"] = inputGet("lang");
        if (empty($data["selectedLang"])) {
            $data["selectedLang"] = $this->activeLang->id;
            return redirect()->to(adminUrl('navigation?lang=' . $data["selectedLang"]));
        }
        $data['title'] = trans("navigation");
        $data['panelSettings'] = panelSettings();
        $data['menuLinks'] = $this->pageModel->getMenuLinks($data["selectedLang"]);

        echo view('admin/includes/_header', $data);
        echo view('admin/navigation/navigation', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Menu Link Post
     */
    public function addMenuLinkPost()
    {
        checkPermission('navigation');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->pageModel->addLink()) {
                setSuccessMessage("msg_added");
            } else {
                setErrorMessage("msg_error");
                return redirect()->back()->withInput();
            }
        }
        return redirect()->to(adminUrl('navigation?lang=' . $this->activeLang->id));
    }

    /**
     * Update Menu Link
     */
    public function editMenuLink($id)
    {
        checkPermission('navigation');
        $data['title'] = trans("navigation");
        $data['page'] = $this->pageModel->getPageById($id);
        if (empty($data['page'])) {
            return redirect()->to(adminUrl('navigation'));
        }
        $data['panelSettings'] = panelSettings();
        $data['menuLinks'] = $this->pageModel->getMenuLinks($data["page"]->lang_id);

        echo view('admin/includes/_header', $data);
        echo view('admin/navigation/edit_link', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Menü Link Post
     */
    public function editMenuLinkPost()
    {
        checkPermission('navigation');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->pageModel->editLink($id)) {
                setSuccessMessage("msg_updated");
            } else {
                setErrorMessage("msg_error");
            }
        }
        return redirect()->to(adminUrl('navigation?lang=' . $this->activeLang->id));
    }

    /**
     * Sort Menu Items
     */
    public function sortMenuItems()
    {
        checkPermission('navigation');
        $this->pageModel->sortMenuItems();
    }

    /**
     * Hide Show Home Link
     */
    public function hideShowHomeLink()
    {
        checkPermission('navigation');
        $this->pageModel->hideShowHomeLink();
    }

    /**
     * Delete Navigation Post
     */
    public function deleteNavigationPost()
    {
        if (!hasPermission('navigation')) {
            exit();
        }
        $id = inputPost('id');
        $data["page"] = $this->pageModel->getPageById($id);
        if (!empty($data['page'])) {
            if (!empty($this->pageModel->getSubpages($id))) {
                setErrorMessage("msg_delete_subpages");
                exit();
            }
            if ($this->pageModel->deletePage($id)) {
                setSuccessMessage("msg_deleted");
            } else {
                setErrorMessage("msg_error");
            }
        }
    }

    /**
     * Menu Limit Post
     */
    public function menuLimitPost()
    {
        checkPermission('navigation');
        if ($this->pageModel->updateMenuLimit()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Themes
     */
    public function themes()
    {
        checkSuperAdmin();
        $data['title'] = trans("themes");
        $data['themes'] = $this->settingsModel->getThemes();

        echo view('admin/includes/_header', $data);
        echo view('admin/themes', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Set Theme Post
     */
    public function setThemePost()
    {
        checkSuperAdmin();
        $this->settingsModel->setTheme();
        return redirect()->to(adminUrl('themes'));
    }

    /**
     * Set Theme Settings Post
     */
    public function setThemeSettingsPost()
    {
        checkSuperAdmin();
        $this->settingsModel->setThemeSettings();
        return redirect()->to(adminUrl('themes'));
    }

    /**
     * Pages
     */
    public function pages()
    {
        checkPermission('pages');
        $data['title'] = trans("pages");
        $data['panelSettings'] = panelSettings();
        $data['pages'] = $this->pageModel->getPages();
        $data['langSearchColumn'] = 2;

        echo view('admin/includes/_header', $data);
        echo view('admin/page/pages', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Page
     */
    public function addPage()
    {
        checkPermission('pages');
        $data['title'] = trans("add_page");
        $data['menuLinks'] = $this->pageModel->getMenuLinks($this->activeLang->id);

        echo view('admin/includes/_header', $data);
        echo view('admin/page/add', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Page Post
     */
    public function addPagePost()
    {
        checkPermission('pages');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->pageModel->addPage()) {
                setSuccessMessage("msg_added");
                redirectToBackURL();
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Edit Page
     */
    public function editPage($id)
    {
        checkPermission('pages');
        $data['title'] = trans("update_page");
        $data['page'] = $this->pageModel->getPageById($id);
        if (empty($data['page'])) {
            redirectToBackURL();
        }
        $data['menuLinks'] = $this->pageModel->getMenuLinks($data['page']->lang_id);

        echo view('admin/includes/_header', $data);
        echo view('admin/page/edit', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Page Post
     */
    public function editPagePost()
    {
        checkPermission('pages');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            $redirectUrl = inputPost('redirect_url');
            if ($this->pageModel->editPage($id)) {
                setSuccessMessage("msg_updated");
                if (!empty($redirectUrl)) {
                    return redirect()->to($redirectUrl);
                }
                return redirect()->to(adminUrl('pages'));
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Delete Page Post
     */
    public function deletePagePost()
    {
        checkPermission('pages');
        $id = inputPost('id');
        $page = $this->pageModel->getPageById($id);
        if (!empty($page)) {
            if ($page->is_custom == 0) {
                setErrorMessage("msg_page_delete");
                exit();
            } else {
                if (countItems($this->pageModel->getSubpages($id)) > 0) {
                    setErrorMessage("msg_delete_subpages");
                }
                if ($this->pageModel->deletePage($id)) {
                    setSuccessMessage("msg_deleted");
                } else {
                    setErrorMessage("msg_error");
                }
            }
        }
    }

    //get menu links by language
    public function getMenuLinksByLang()
    {
        $langId = inputPost('lang_id');
        if (!empty($langId)) {
            $menuLinks = $this->pageModel->getMenuLinks($langId);
            if (!empty($menuLinks)) {
                foreach ($menuLinks as $item) {
                    if ($item->item_type != 'category' && $item->item_location == 'main' && $item->item_parent_id == 0) {
                        echo ' <option value="' . $item->item_id . '">' . esc($item->item_name) . '</option>';
                    }
                }
            }
        }
    }

    /**
     * Add Widget
     */
    public function addWidget()
    {
        checkPermission('widgets');
        $data['title'] = trans("add_widget");
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategories();

        echo view('admin/includes/_header', $data);
        echo view('admin/widget/add', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Simulator Page
     */
    public function updateSimulatorPage()
    {
        checkPermission('pages');
        
        // Read the simulator content file
        $file_path = ROOTPATH . 'tmp/simulator_content.html';
        if (file_exists($file_path)) {
            $html_content = file_get_contents($file_path);
            
            // Get page 12
            $page = $this->pageModel->getPageById(12);
            if (!empty($page)) {
                // Update page content
                $this->db->table('pages')
                    ->where('id', 12)
                    ->update(['page_content' => $html_content]);
                
                setSuccessMessage("Página do simulador atualizada com sucesso!");
            } else {
                setErrorMessage("Página com ID 12 não encontrada!");
            }
        } else {
            setErrorMessage("Arquivo do conteúdo do simulador não encontrado!");
        }
        
        return redirect()->to(adminUrl('pages'));
    }

    /**
     * Add Widget Post
     */
    public function addWidgetPost()
    {
        checkPermission('widgets');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = $this->commonModel->addWidget();
            if ($id) {
                setSuccessMessage("msg_added");
                return redirect()->to(adminUrl('widgets'));
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Edit Widget
     */
    public function editWidget($id)
    {
        checkPermission('widgets');
        $data['title'] = trans("update_widget");
        $data['widget'] = $this->commonModel->getWidget($id);
        if (empty($data['widget'])) {
            return redirect()->to(adminUrl('widgets'));
        }
        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->getParentCategories();

        echo view('admin/includes/_header', $data);
        echo view('admin/widget/edit', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Widget Post
     */
    public function editWidgetPost()
    {
        checkPermission('widgets');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->commonModel->editWidget($id)) {
                setSuccessMessage("msg_updated");
                redirectToBackURL();
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Delete Widget Post
     */
    public function deleteWidgetPost()
    {
        checkPermission('widgets');
        $id = inputPost('id');
        if ($this->commonModel->deleteWidget($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Widgets
     */
    public function widgets()
    {
        checkPermission('widgets');
        $data['title'] = trans("widgets");
        $data['widgets'] = $this->commonModel->getWidgets();
        $data['langSearchColumn'] = 2;

        echo view('admin/includes/_header', $data);
        echo view('admin/widget/widgets', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Polls
     */
    public function polls()
    {
        checkPermission('polls');
        $data['title'] = trans("polls");
        $pollModel = new PollModel();
        $data['polls'] = $pollModel->getPolls();
        $data['langSearchColumn'] = 2;

        echo view('admin/includes/_header', $data);
        echo view('admin/poll/polls', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Poll
     */
    public function addPoll()
    {
        checkPermission('polls');
        $data['title'] = trans("add_poll");

        echo view('admin/includes/_header', $data);
        echo view('admin/poll/add', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Poll Post
     */
    public function addPollPost()
    {
        checkPermission('polls');
        $val = \Config\Services::validation();
        $val->setRule('question', trans("question"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $pollModel = new PollModel();
            if ($pollModel->addPoll()) {
                setSuccessMessage("msg_added");
                return redirect()->to(adminUrl('polls'));
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Edit Poll
     */
    public function editPoll($id)
    {
        checkPermission('polls');
        $data['title'] = trans("update_poll");
        $pollModel = new PollModel();
        $data['poll'] = $pollModel->getPoll($id);
        if (empty($data['poll'])) {
            return redirect()->to(adminUrl('polls'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/poll/edit', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Poll Post
     */
    public function editPollPost()
    {
        checkPermission('polls');
        $val = \Config\Services::validation();
        $val->setRule('question', trans("question"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $pollModel = new PollModel();
            $id = inputPost('id');
            if ($pollModel->editPoll($id)) {
                setSuccessMessage("msg_updated");
                redirectToBackURL();
            }
        }
        setErrorMessage("msg_error");
        return redirect()->back()->withInput();
    }

    /**
     * Delete Poll Post
     */
    public function deletePollPost()
    {
        checkPermission('polls');
        $id = inputPost('id');
        $pollModel = new PollModel();
        if ($pollModel->deletePoll($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Contact Messages
     */
    public function contactMessages()
    {
        checkPermission('comments_contact');
        $data['title'] = trans("contact_messages");
        $data['messages'] = $this->commonModel->getContactMessagesAll();

        echo view('admin/includes/_header', $data);
        echo view('admin/contact_messages', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Simulator Leads
     */
    public function simulatorLeads()
    {
        checkPermission('comments_contact');
        $data['title'] = "Leads do Simulador";
        $simLeadModel = new SimLeadModel();
        $data['leads'] = $simLeadModel->getSimLeads();
        $data['panelSettings'] = panelSettings();

        echo view('admin/includes/_header', $data);
        echo view('admin/simulator_leads', $data);
        echo view('admin/includes/_footer');
    }
    
    /**
     * Delete Simulator Lead Post
     */
    public function deleteSimulatorLeadPost()
    {
        checkPermission('comments_contact');
        $id = inputPost('id');
        $simLeadModel = new SimLeadModel();
        if ($simLeadModel->deleteSimLead($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }
    
    /**
     * Delete Selected Simulator Leads
     */
    public function deleteSelectedSimulatorLeads()
    {
        checkPermission('comments_contact');
        $leadIds = inputPost('lead_ids');
        $simLeadModel = new SimLeadModel();
        
        if (!empty($leadIds)) {
            foreach ($leadIds as $id) {
                $simLeadModel->deleteSimLead($id);
            }
            setSuccessMessage("Leads selecionados excluídos com sucesso");
        }
        
        redirectToBackURL();
    }
    
    /**
     * Update Simulator Lead Status
     */
    public function updateSimulatorLeadStatus()
    {
        checkPermission('comments_contact');
        $id = inputPost('id');
        $status = inputPost('status');
        $simLeadModel = new SimLeadModel();
        
        if (!empty($id) && !empty($status)) {
            if ($simLeadModel->updateSimLeadStatus($id, $status)) {
                setSuccessMessage("Status do lead atualizado com sucesso");
            } else {
                setErrorMessage("Erro ao atualizar o status do lead");
            }
        }
        
        redirectToBackURL();
    }

    /**
     * Delete Contact Message Post
     */
    public function deleteContactMessagePost()
    {
        checkPermission('comments_contact');
        $id = inputPost('id');
        if ($this->commonModel->deleteContactMessage($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Delete Selected Contact Messages
     */
    public function deleteSelectedContactMessages()
    {
        checkPermission('comments_contact');
        $messages = inputPost('messages');
        $this->commonModel->deleteMultiMessages($messages);
        redirectToBackURL();
    }

    /**
     * Comments
     */
    public function comments()
    {
        checkPermission('comments_contact');
        $data['title'] = trans("approved_comments");
        $data['comments'] = $this->commonModel->getCommentsPaginated(1, 15, 0);
        $data['topButtonText'] = trans("pending_comments");
        $data['topButtonURL'] = adminUrl('pending-comments');
        $data['showApproveButton'] = false;
        $data['pager'] = (object)['links' => ''];

        echo view('admin/includes/_header', $data);
        echo view('admin/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Pending Comments
     */
    public function pendingComments()
    {
        checkPermission('comments_contact');
        $data['title'] = trans("pending_comments");
        $data['comments'] = $this->commonModel->getCommentsPaginated(0, 15, 0);
        $data['topButtonText'] = trans("approved_comments");
        $data['topButtonURL'] = adminUrl('comments');
        $data['showApproveButton'] = true;
        $data['pager'] = (object)['links' => ''];

        echo view('admin/includes/_header', $data);
        echo view('admin/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Approve Comment Post
     */
    public function approveCommentPost()
    {
        checkPermission('comments_contact');
        $id = inputPost('id');
        if ($this->commonModel->approveComment($id)) {
            setSuccessMessage("msg_approved");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Approve Selected Comments
     */
    public function approveSelectedComments()
    {
        checkPermission('comments_contact');
        $commentIds = inputPost('comment_ids');
        $this->commonModel->approveMultiComments($commentIds);
        redirectToBackURL();
    }

    /**
     * Delete Comment Post
     */
    public function deleteCommentPost()
    {
        checkPermission('comments_contact');
        $id = inputPost('id');
        if ($this->commonModel->deleteComment($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Delete Selected Comments
     */
    public function deleteSelectedComments()
    {
        checkPermission('comments_contact');
        $commentIds = inputPost('comment_ids');
        $this->commonModel->deleteMultiComments($commentIds);
        redirectToBackURL();
    }

    /**
     * Newsletter
     */
    public function newsletter()
    {
        checkPermission('newsletter');
        $data['title'] = trans("newsletter");
        $newsletterModel = new NewsletterModel();
        $data['subscribers'] = $newsletterModel->getSubscribers();
        $data['usersCount'] = $this->authModel->getUserCount();
        $data['subscribersCount'] = $newsletterModel->getSubscribersCount();
        $data['generalSettings'] = $this->generalSettings;

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/newsletter', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Newsletter Send Email
     */
    public function newsletterSendEmail()
    {
        checkPermission('newsletter');
        $data['title'] = trans("newsletter");

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/send_email', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Newsletter Send Email Post
     */
    public function newsletterSendEmailPost()
    {
        checkPermission('newsletter');
        $val = \Config\Services::validation();
        $val->setRule('subject', trans("subject"), 'required|max_length[500]');
        $val->setRule('message', trans("message"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $newsletterModel = new NewsletterModel();
            $emailModel = new EmailModel();
            $newsletterModel->loadSubscribers();
            $data = [
                'subject' => inputPost('subject'),
                'message' => inputPost('message'),
                'templatePath' => "email/newsletter",
                'to' => "thebjking@yahoo.com",
            ];
            if (count($newsletterModel->subscribers) > 0) {
                if ($emailModel->sendEmailNewsletter($data)) {
                    setSuccessMessage("msg_email_sent");
                } else {
                    setErrorMessage("msg_error");
                }
            } else {
                setErrorMessage("msg_no_subscriber");
            }
        }
        return redirect()->to(adminUrl('newsletter'));
    }

    /**
     * Newsletter Settings Post
     */
    public function newsletterSettingsPost()
    {
        checkPermission('newsletter');
        $newsletterModel = new NewsletterModel();
        if ($newsletterModel->updateSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Users
     */
    public function users()
    {
        checkPermission('users');
        $data['title'] = trans("users");
        $data['users'] = $this->authModel->getUsers();
        $data['userRoles'] = $this->authModel->getUserRoles();
        $data['activeLang'] = $this->activeLang;
        // Add pager object with empty links to prevent error
        $data['pager'] = (object)['links' => ''];

        echo view('admin/includes/_header', $data);
        echo view('admin/users/users', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * User Options Post
     */
    public function userOptionsPost()
    {
        checkPermission('users');
        $option = inputPost('option');
        $id = inputPost('id');
        $user = getUserById($id);
        if (!empty($user)) {
            if ($option == 'ban') {
                if ($this->authModel->banUser($id)) {
                    setSuccessMessage("msg_ban_removed");
                }
            }
            if ($option == 'remove_ban') {
                if ($this->authModel->removeUserBan($id)) {
                    setSuccessMessage("msg_ban_removed");
                }
            }
        }
        return redirect()->to(adminUrl('users'));
    }

    /**
     * Add User
     */
    public function addUser()
    {
        checkPermission('users');
        $data['title'] = trans("add_user");
        $data['userRoles'] = $this->authModel->getUserRoles();

        echo view('admin/includes/_header', $data);
        echo view('admin/users/add_user', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add User Post
     */
    public function addUserPost()
    {
        checkPermission('users');
        $val = \Config\Services::validation();
        $val->setRule('username', trans("username"), 'required|max_length[255]');
        $val->setRule('email', trans("email"), 'required|max_length[255]');
        $val->setRule('password', trans("password"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $email = inputPost('email');
            $username = inputPost('username');
            //is username unique
            if (!$this->authModel->isUniqueUsername($username)) {
                setErrorMessage(trans("msg_username_unique_error"));
                return redirect()->back()->withInput();
            }
            //is email unique
            if (!$this->authModel->isUniqueEmail($email)) {
                setErrorMessage(trans("message_email_unique_error"));
                return redirect()->back()->withInput();
            }
            //add user
            if ($this->authModel->addUser()) {
                setSuccessMessage(trans("msg_user_added"));
            } else {
                setErrorMessage(trans("msg_error"));
                return redirect()->back()->withInput();
            }
        }
        return redirect()->to(adminUrl('users'));
    }

    /**
     * Edit User
     */
    public function editUser($id)
    {
        checkPermission('users');
        $data['title'] = trans("edit_user");
        $data['user'] = getUserById($id);
        $data['userRoles'] = $this->authModel->getUserRoles();
        if (empty($data['user'])) {
            return redirect()->to(adminUrl('users'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/users/edit_user', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit User Post
     */
    public function editUserPost()
    {
        checkPermission('users');
        $val = \Config\Services::validation();
        $val->setRule('username', trans("username"), 'required|max_length[255]');
        $val->setRule('email', trans("email"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            $user = getUserById($id);
            if (empty($user)) {
                return redirect()->to(adminUrl('users'));
            }
            $slug = inputPost('slug');
            //is email unique
            if (!$this->authModel->isEmailUniqueOnEdit($user->id, inputPost('email'))) {
                setErrorMessage(trans("message_email_unique_error"));
                return redirect()->back()->withInput();
            }
            //is username unique
            if (!$this->authModel->isUsernameUniqueOnEdit($user->id, inputPost('username'))) {
                setErrorMessage(trans("msg_username_unique_error"));
                return redirect()->back()->withInput();
            }
            //is slug unique
            if (!$this->authModel->isSlugUniqueOnEdit($user->id, $slug)) {
                setErrorMessage(trans("msg_slug_used"));
                return redirect()->back()->withInput();
            }
            if ($this->authModel->editUser($id)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
                return redirect()->back()->withInput();
            }
        }
        return redirect()->to(adminUrl('users'));
    }

    /**
     * Change User Role
     */
    public function changeUserRolePost()
    {
        checkPermission('users');
        $id = inputPost('user_id');
        $roleId = inputPost('role_id');
        $user = getUserById($id);
        if (!empty($user) && $user->role_id != 1) {
            $this->authModel->changeUserRole($id, $roleId);
        }
    }

    /**
     * Delete User Post
     */
    public function deleteUserPost()
    {
        checkPermission('users');
        $id = inputPost('id');
        $user = getUserById($id);
        if (!empty($user) && $user->id != user()->id && $user->role_id != 1) {
            if ($this->authModel->deleteUser($id)) {
                setSuccessMessage(trans("msg_user_deleted"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
    }

    /**
     * Roles Permissions
     */
    public function rolesPermissions()
    {
        checkPermission('admin_panel');
        $data['title'] = trans("roles_permissions");
        $data['roles'] = $this->authModel->getUserRolesExceptAdmin();

        echo view('admin/includes/_header', $data);
        echo view('admin/users/roles_permissions', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Role
     */
    public function addRole()
    {
        checkPermission('admin_panel');
        $data['title'] = trans("add_role");

        echo view('admin/includes/_header', $data);
        echo view('admin/users/add_role', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Role Post
     */
    public function addRolePost()
    {
        checkPermission('admin_panel');
        $val = \Config\Services::validation();
        $val->setRule('role_name', trans("role_name"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $roleName = inputPost('role_name');
            if (!empty($roleName)) {
                $this->authModel->addRole();
                setSuccessMessage(trans("msg_added"));
            }
        }
        return redirect()->to(adminUrl('roles-permissions'));
    }

    /**
     * Edit Role
     */
    public function editRole($id)
    {
        checkPermission('admin_panel');
        $data['title'] = trans("edit_role");
        $data['role'] = $this->authModel->getRole($id);
        if (empty($data['role'])) {
            return redirect()->to(adminUrl('roles-permissions'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/users/edit_role', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Role Post
     */
    public function editRolePost()
    {
        checkPermission('admin_panel');
        $val = \Config\Services::validation();
        $val->setRule('role_name', trans("role_name"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $roleName = inputPost('role_name');
            $id = inputPost('id');
            if (!empty($roleName)) {
                $role = $this->authModel->getRole($id);
                if (!empty($role)) {
                    $this->authModel->editRole($id);
                    setSuccessMessage(trans("msg_updated"));
                }
            }
        }
        return redirect()->to(adminUrl('roles-permissions'));
    }

    /**
     * Delete Role Post
     */
    public function deleteRolePost()
    {
        checkPermission('admin_panel');
        $id = inputPost('id');
        $role = $this->authModel->getRole($id);
        if (!empty($role)) {
            $this->authModel->deleteRole($id);
            setSuccessMessage(trans("msg_deleted"));
        }
    }

    /**
     * Seo Tools
     */
    public function seoTools()
    {
        checkSuperAdmin();
        $data['title'] = trans("seo_tools");
        
        // Get selected language id
        $data['selectedLangId'] = inputGet('lang');
        if (empty($data['selectedLangId'])) {
            $data['selectedLangId'] = $this->activeLang->id;
        }
        
        // Load settings and models
        $settingsModel = new \App\Models\SettingsModel();
        $sitemapModel = new \App\Models\SitemapModel();
        
        // Get active languages
        $data['activeLanguages'] = Globals::$languages;
        
        // Get SEO settings for selected language and general settings
        $data['seoSettings'] = $settingsModel->getSeoSettings($data['selectedLangId']);
        
        // Get general settings
        $data['generalSettings'] = $this->generalSettings;
        
        // Get number of sitemaps
        $data['numSitemaps'] = count(glob(FCPATH . 'sitemap*.xml'));
        if ($data['numSitemaps'] < 1) {
            $data['numSitemaps'] = 1;
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/seo_tools', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Seo Tools Post
     */
    public function seoToolsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateSeoSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('seo-tools'));
    }

    /**
     * Sitemap Settings Post
     */
    public function sitemapSettingsPost()
    {
        checkSuperAdmin();
        $sitemapModel = new SitemapModel();
        if ($sitemapModel->updateSitemapSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('seo-tools'));
    }

    /**
     * Sitemap Post
     */
    public function sitemapPost()
    {
        checkSuperAdmin();
        $sitemapModel = new SitemapModel();
        $data = $sitemapModel->generateSitemap(0);
        $data["baseURL"] = base_url();
        $xml = view('admin/generate_sitemap', $data);
        $url = base_url();
        $url = trim($url, '/');
        $urlParts = parse_url($url);
        $baseUrl = isset($urlParts['path']) ? str_replace($urlParts['path'], '', $url) : $url;
        if (strpos($baseUrl, 'http://localhost') !== false) {
            $baseUrl = str_replace('http://localhost', '', $baseUrl);
        }
        $baseUrl = trim($baseUrl, '/');
        $baseUrl = !empty($baseUrl) ? $baseUrl . '/' : '';
        if (!empty($xml)) {
            $data['formatted_xml'] = format_sitemap($xml);
            if (write_file(FCPATH . $baseUrl . "sitemap.xml", $data['formatted_xml'])) {
                $this->settingsModel->updateSettings('sitemap_last_update', date('Y-m-d H:i:s'));
                setSuccessMessage("sitemap_generated");
            } else {
                setErrorMessage("sitemap_not_generated");
            }
        }
        return redirect()->to(adminUrl('seo-tools'));
    }

    /**
     * Google Index Post
     */
    public function googleIndexingApiPost()
    {
        checkSuperAdmin();
        $this->settingsModel->updateGoogleIndexingSettings();
        setSuccessMessage("msg_updated");
        return redirect()->to(adminUrl('seo-tools'));
    }

    /**
     * Ad Spaces
     */
    public function adSpaces()
    {
        checkSuperAdmin();
        $data['title'] = trans("ad_spaces");
        $langId = inputGet('lang');
        $data['langId'] = $langId;
        if (empty($data['langId'])) {
            $data['langId'] = $this->activeLang->id;
        }
        
        $adSpaceKey = inputGet('ad_space');
        if (empty($adSpaceKey)) {
            $adSpaceKey = 'header';
        }
        $data['adSpaceKey'] = $adSpaceKey;
        
        $data['activeLanguages'] = Globals::$languages;
        $data['arrayAdSpaces'] = [
            'header' => trans('header_desktop'),
            'header_mobile' => trans('header_mobile'),
            'index_top' => trans('index_top'),
            'index_bottom' => trans('index_bottom'),
            'post_top' => trans('post_top'),
            'post_bottom' => trans('post_bottom'),
            'sidebar_1' => trans('sidebar') . ' 1',
            'sidebar_2' => trans('sidebar') . ' 2'
        ];
        
        $data['adSpace'] = $this->commonModel->getAdSpace($data['langId'], $adSpaceKey);
        
        // Initialize CategoryModel
        $categoryModel = new \App\Models\CategoryModel();
        $data['categories'] = $categoryModel->getCategories();
        $data['activeLang'] = $this->activeLang;
        
        echo view('admin/includes/_header', $data);
        echo view('admin/ad_spaces', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Ad Spaces Post
     */
    public function adSpacesPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateAdSpaces()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('ad-spaces'));
    }

    /**
     * Google Adsense Code Post
     */
    public function googleAdsenseCodePost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateAdCodes()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('ad-spaces'));
    }

    /**
     * Cache System
     */
    public function cacheSystem()
    {
        checkSuperAdmin();
        $data['title'] = trans("cache_system");

        echo view('admin/includes/_header', $data);
        echo view('admin/cache_system', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Cache System Post
     */
    public function cacheSystemPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateCacheSystem()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('cache-system'));
    }

    /**
     * Storage
     */
    public function storage()
    {
        checkSuperAdmin();
        $data['title'] = trans("storage");

        echo view('admin/includes/_header', $data);
        echo view('admin/storage', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Storage Post
     */
    public function storagePost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateStorageSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('storage'));
    }

    /**
     * AWS S3 Post
     */
    public function awsS3Post()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateAwsS3()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('storage'));
    }

    /**
     * Google News
     */
    public function googleNews()
    {
        checkSuperAdmin();
        $data['title'] = trans("google_news");

        echo view('admin/includes/_header', $data);
        echo view('admin/google_news', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Google News Post
     */
    public function googleNewsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateGoogleNewsSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('google-news'));
    }

    /**
     * Social Login Settings
     */
    public function socialLoginSettings()
    {
        checkSuperAdmin();
        $data['title'] = trans("social_login_settings");
        
        // Clone generalSettings to avoid modifying the original object
        $generalSettings = clone $this->generalSettings;
        
        // Add missing properties
        if (!isset($generalSettings->facebook_app_id)) {
            $generalSettings->facebook_app_id = '';
        }
        if (!isset($generalSettings->facebook_app_secret)) {
            $generalSettings->facebook_app_secret = '';
        }
        if (!isset($generalSettings->facebook_login)) {
            $generalSettings->facebook_login = 0;
        }
        if (!isset($generalSettings->google_client_id)) {
            $generalSettings->google_client_id = '';
        }
        if (!isset($generalSettings->google_client_secret)) {
            $generalSettings->google_client_secret = '';
        }
        if (!isset($generalSettings->google_login)) {
            $generalSettings->google_login = 0;
        }
        if (!isset($generalSettings->vk_app_id)) {
            $generalSettings->vk_app_id = '';
        }
        if (!isset($generalSettings->vk_secure_key)) {
            $generalSettings->vk_secure_key = '';
        }
        if (!isset($generalSettings->vk_redirect_url)) {
            $generalSettings->vk_redirect_url = '';
        }
        if (!isset($generalSettings->vk_login)) {
            $generalSettings->vk_login = 0;
        }
        
        $data['generalSettings'] = $generalSettings;

        echo view('admin/includes/_header', $data);
        echo view('admin/social_login_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Social Login Settings Post
     */
    public function socialLoginSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateSocialLoginSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('social-login-settings'));
    }

    /**
     * Email Settings
     */
    public function emailSettings()
    {
        checkSuperAdmin();
        $data['title'] = trans("email_settings");

        echo view('admin/includes/_header', $data);
        echo view('admin/email_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Email Settings Post
     */
    public function emailSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateEmailSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('email-settings'));
    }

    /**
     * Email Verification Settings Post
     */
    public function emailVerificationSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateEmailVerificationSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('email-settings'));
    }

    /**
     * Contact Email Settings Post
     */
    public function contactEmailSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateContactEmailSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('email-settings'));
    }

    /**
     * Send Test Email Post
     */
    public function sendTestEmailPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        $emailModel = new EmailModel();
        $settingsModel->emailSettings();
        $subject = "Test Email";
        $message = "This is a test email";
        if (!empty(user()->email)) {
            if ($emailModel->sendTestEmail(user()->email, $subject, $message)) {
                setSuccessMessage(trans("send_test_email") . " " . trans("msg_sent") . ": " . user()->email);
            } else {
                setErrorMessage(trans("mail_error"));
            }
        } else {
            setErrorMessage(trans("mail_error"));
        }
        return redirect()->to(adminUrl('email-settings'));
    }

    /**
     * Font Settings
     */
    public function fontSettings()
    {
        checkSuperAdmin();
        $data['title'] = trans("font_settings");
        $data["fonts"] = $this->settingsModel->getFonts();
        
        // Add active languages
        $data['activeLanguages'] = isset($GLOBALS['languages']) ? $GLOBALS['languages'] : [];
        if (empty($data['activeLanguages'])) {
            $languageModel = new \App\Models\LanguageModel();
            $data['activeLanguages'] = $languageModel->getActiveLanguages();
        }
        
        // Add active language
        $data['activeLang'] = $this->activeLang;
        
        // Add fontUpdate object if show parameter is present
        if (inputGet('show') == 'update') {
            $fontId = inputGet('id');
            if (!empty($fontId)) {
                $data['fontUpdate'] = $this->settingsModel->getFont($fontId);
            } else {
                $data['fontUpdate'] = (object)[
                    'lang_id' => 0,
                    'font_name' => '',
                    'font_url' => '',
                    'font_family' => '',
                    'css_style' => ''
                ];
            }
        }
        
        echo view('admin/includes/_header', $data);
        echo view('admin/font_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Font
     */
    public function editFont($id)
    {
        checkSuperAdmin();
        $data['title'] = trans("update_font");
        $data['font'] = $this->settingsModel->getFont($id);
        if (empty($data['font'])) {
            return redirect()->to(adminUrl('font-settings'));
        }
        
        // Add active languages
        $data['activeLanguages'] = isset($GLOBALS['languages']) ? $GLOBALS['languages'] : [];
        if (empty($data['activeLanguages'])) {
            $languageModel = new \App\Models\LanguageModel();
            $data['activeLanguages'] = $languageModel->getActiveLanguages();
        }
        
        // Add fontUpdate for backward compatibility
        $data['fontUpdate'] = $data['font'];

        echo view('admin/includes/_header', $data);
        echo view('admin/edit_font', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Set Site Font Post
     */
    public function setSiteFontPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->setSiteFont()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('font-settings'));
    }

    /**
     * Edit Font Post
     */
    public function editFontPost()
    {
        checkSuperAdmin();
        $id = inputPost('id');
        $settingsModel = new SettingsModel();
        if ($settingsModel->editFont($id)) {
            setSuccessMessage("msg_updated");
            return redirect()->to(adminUrl('font-settings'));
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('edit-font') . '/' . clrNum($id));
    }

    /**
     * Add Font Post
     */
    public function addFontPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->addFont()) {
            setSuccessMessage("msg_added");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('font-settings'));
    }

    /**
     * Delete Font Post
     */
    public function deleteFontPost()
    {
        checkSuperAdmin();
        $id = inputPost('id');
        $settingsModel = new SettingsModel();
        if ($settingsModel->deleteFont($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Route Settings
     */
    public function routeSettings()
    {
        checkSuperAdmin();
        $data['title'] = trans("route_settings");
        
        // Get routes or create default routes if none exist
        $routes = $this->settingsModel->getRoutes();
        if (empty($routes)) {
            $routes = (object)[
                'admin' => 'admin',
                'profile' => 'profile',
                'tag' => 'tag',
                'reading_list' => 'reading-list',
                'settings' => 'settings',
                'social_accounts' => 'social-accounts',
                'preferences' => 'preferences',
                'visual_settings' => 'visual-settings',
                'change_password' => 'change-password',
                'forgot_password' => 'forgot-password',
                'reset_password' => 'reset-password',
                'delete_account' => 'delete-account',
                'register' => 'register',
                'posts' => 'posts',
                'search' => 'search',
                'logout' => 'logout',
                'cookies_warning' => 'cookies-warning',
                'contact' => 'contact',
                'gallery' => 'gallery',
                'gallery_album' => 'gallery-album',
                'rss_feeds' => 'rss-feeds',
                'terms_conditions' => 'terms-conditions',
                'select_membership_plan' => 'select-membership-plan',
                'paypal' => 'paypal',
                'stripe' => 'stripe'
            ];
        }
        
        $data['routes'] = $routes;

        echo view('admin/includes/_header', $data);
        echo view('admin/route_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Route Settings Post
     */
    public function routeSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateRouteSettings()) {
            setSuccessMessage("msg_updated");
            return redirect()->to(adminUrl('route-settings'));
        } else {
            setErrorMessage("msg_error");
        }
        $this->routeSettings();
    }

    /**
     * Download Database Backup
     */
    public function downloadDatabaseBackup()
    {
        checkSuperAdmin();
        $this->settingsModel->downloadBackup();
    }

    /**
     * Preferences
     */
    public function preferences()
    {
        checkSuperAdmin();
        $data['title'] = trans("preferences");
        
        // Clone generalSettings to avoid modifying the original object
        $generalSettings = clone $this->generalSettings;
        
        // Add missing properties
        if (!isset($generalSettings->date_format)) {
            $generalSettings->date_format = 'Y-m-d H:i:s';
        }
        if (!isset($generalSettings->pagination_per_page)) {
            $generalSettings->pagination_per_page = 15;
        }
        if (!isset($generalSettings->file_manager_show_files)) {
            $generalSettings->file_manager_show_files = 20;
        }
        if (!isset($generalSettings->maintenance_mode_status)) {
            $generalSettings->maintenance_mode_status = 0;
        }
        if (!isset($generalSettings->maintenance_mode_title)) {
            $generalSettings->maintenance_mode_title = 'Site em Manutenção';
        }
        if (!isset($generalSettings->maintenance_mode_description)) {
            $generalSettings->maintenance_mode_description = 'O site está temporariamente em manutenção. Volte em breve!';
        }
        if (!isset($generalSettings->comment_system)) {
            $generalSettings->comment_system = 1;
        }
        if (!isset($generalSettings->comment_approval_system)) {
            $generalSettings->comment_approval_system = 0;
        }
        if (!isset($generalSettings->emoji_reactions)) {
            $generalSettings->emoji_reactions = 1;
        }
        if (!isset($generalSettings->reward_system)) {
            $generalSettings->reward_system = 0;
        }
        if (!isset($generalSettings->registration_system)) {
            $generalSettings->registration_system = 1;
        }
        if (!isset($generalSettings->email_verification)) {
            $generalSettings->email_verification = 0;
        }
        if (!isset($generalSettings->audio_enabled)) {
            $generalSettings->audio_enabled = 0;
        }
        if (!isset($generalSettings->rss_content_type)) {
            $generalSettings->rss_content_type = 'summary';
        }
        
        $data['generalSettings'] = $generalSettings;

        echo view('admin/includes/_header', $data);
        echo view('admin/preferences', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Preferences Post
     */
    public function preferencesPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        $form = inputPost('form');
        if (empty($form)) {
            $form = 'general';
        }
        if ($settingsModel->updatePreferences($form)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('preferences'));
    }

    /**
     * AI Writer Post
     */
    public function aiWriterPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateAIWriterSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('preferences'));
    }

    /**
     * File Upload Settings Post
     */
    public function fileUploadSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateFileUploadSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('preferences'));
    }

    /**
     * Recaptcha Settings Post
     */
    public function recaptchaSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateRecaptchaSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('preferences'));
    }

    /**
     * Validate OpenAI API Key (AJAX)
     */
    public function validateOpenAIKeyPost()
    {
        checkSuperAdmin();

        $key = trim(inputPost('openai_api_key') ?? '');
        if (empty($key)) {
            $envKey = getenv('OPENAI_API_KEY');
            if (!empty($envKey)) {
                $key = $envKey;
            } else {
                // fallback to AI Writer key if available
                $ai = aiWriter();
                if (!empty($ai) && !empty($ai->apiKey)) {
                    $key = $ai->apiKey;
                }
            }
        }

        if (empty($key)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhuma chave encontrada. Informe a chave ou salve no .env.',
            ]);
        }

        // Minimal validation against OpenAI API (models endpoint)
        $ch = curl_init('https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro de conexão: ' . $error,
            ]);
        }

        if ($httpCode === 200) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Chave válida e aceita pela API.',
            ]);
        }

        // Try to extract error message
        $msg = 'HTTP ' . $httpCode;
        $decoded = json_decode($response, true);
        if (is_array($decoded) && isset($decoded['error']['message'])) {
            $msg .= ' - ' . $decoded['error']['message'];
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Chave inválida ou sem permissão: ' . $msg,
        ]);
    }

    /**
     * Validate AI Writer API Key (AJAX)
     */
    public function validateAIWriterKeyPost()
    {
        checkSuperAdmin();

        $key = trim(inputPost('api_key') ?? '');
        if (empty($key)) {
            $ai = aiWriter();
            if (!empty($ai) && !empty($ai->apiKey)) {
                $key = $ai->apiKey;
            }
        }

        if (empty($key)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nenhuma chave encontrada para o AI Writer.',
            ]);
        }

        $ch = curl_init('https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro de conexão: ' . $error,
            ]);
        }

        if ($httpCode === 200) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Chave do AI Writer válida.',
            ]);
        }

        $msg = 'HTTP ' . $httpCode;
        $decoded = json_decode($response, true);
        if (is_array($decoded) && isset($decoded['error']['message'])) {
            $msg .= ' - ' . $decoded['error']['message'];
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Chave inválida: ' . $msg,
        ]);
    }

    /**
     * Maintenance Mode Post
     */
    public function maintenanceModePost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateMaintenanceModeSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('preferences'));
    }

    /**
     * General Settings
     */
    public function generalSettings()
    {
        checkSuperAdmin();
        $data['title'] = trans("general_settings");
        
        // Buscar configurações diretamente do banco de dados
        $settingsModel = new \App\Models\SettingsModel();
        $generalSettings = $settingsModel->getGeneralSettings();
        
        if (empty($generalSettings)) {
            $generalSettings = new \stdClass();
        }
        
        // Adicionar propriedades que possam estar faltando
        if (!isset($generalSettings->application_name)) {
            $generalSettings->application_name = 'GX Capital';
        }
        if (!isset($generalSettings->timezone)) {
            $generalSettings->timezone = 'America/Sao_Paulo';
        }
        if (!isset($generalSettings->logo)) {
            $generalSettings->logo = '';
        }
        if (!isset($generalSettings->favicon)) {
            $generalSettings->favicon = '';
        }
        if (!isset($generalSettings->copyright)) {
            $generalSettings->copyright = 'Copyright © ' . date('Y') . ' GX Capital - All Rights Reserved.';
        }
        // Decodificar informações de contato do JSON
        $contactInfo = [];
        if (!empty($generalSettings->contact_info)) {
            $contactInfo = json_decode($generalSettings->contact_info, true);
        }
        
        // Definir informações de contato (usando JSON como fonte principal)
        $generalSettings->contact_address = $contactInfo['contact_address'] ?? '';
        $generalSettings->contact_email = $contactInfo['contact_email'] ?? '';
        $generalSettings->contact_phone = $contactInfo['contact_phone'] ?? '';
        $generalSettings->contact_text = $contactInfo['contact_text'] ?? '';
        // Decodificar URLs das redes sociais do JSON
        $socialUrls = [];
        if (!empty($generalSettings->social_media_urls)) {
            $socialUrls = json_decode($generalSettings->social_media_urls, true);
        }
        
        // Definir URLs das redes sociais (usando JSON ou valores individuais como fallback)
        $generalSettings->facebook_url = $socialUrls['facebook_url'] ?? $generalSettings->facebook_url ?? '';
        $generalSettings->twitter_url = $socialUrls['twitter_url'] ?? '';
        $generalSettings->instagram_url = $socialUrls['instagram_url'] ?? '';
        $generalSettings->pinterest_url = $socialUrls['pinterest_url'] ?? '';
        $generalSettings->linkedin_url = $socialUrls['linkedin_url'] ?? '';
        $generalSettings->vk_url = $socialUrls['vk_url'] ?? '';
        $generalSettings->telegram_url = $socialUrls['telegram_url'] ?? '';
        $generalSettings->youtube_url = $socialUrls['youtube_url'] ?? '';
        
        // Decodificar configurações de cookies do JSON
        $cookiesSettings = [];
        if (!empty($generalSettings->cookies_settings)) {
            $cookiesSettings = json_decode($generalSettings->cookies_settings, true);
        }
        
        // Definir configurações de cookies (usando JSON como fonte principal)
        $generalSettings->cookies_warning = $cookiesSettings['cookies_warning'] ?? $generalSettings->cookies_warning ?? 0;
        $generalSettings->cookies_warning_text = $cookiesSettings['cookies_warning_text'] ?? $generalSettings->cookies_warning_text ?? '';
        
        // Decodificar configurações da Meta Conversions API do JSON
        $metaApiSettings = [];
        if (!empty($generalSettings->meta_conversions_api)) {
            $metaApiSettings = json_decode($generalSettings->meta_conversions_api, true);
        }
        
        // Definir configurações da Meta API (usando JSON como fonte principal)
        $generalSettings->meta_pixel_id = $metaApiSettings['pixel_id'] ?? '';
        $generalSettings->meta_access_token = $metaApiSettings['access_token'] ?? '';
        $generalSettings->meta_test_event_code = $metaApiSettings['test_event_code'] ?? '';
        $generalSettings->meta_api_enabled = $metaApiSettings['api_enabled'] ?? 0;
        $generalSettings->meta_test_mode = $metaApiSettings['test_mode'] ?? 0;
        $generalSettings->meta_track_events = $metaApiSettings['track_events'] ?? [];
        
        if (!isset($generalSettings->custom_header_codes)) {
            $generalSettings->custom_header_codes = '';
        }
        if (!isset($generalSettings->custom_footer_codes)) {
            $generalSettings->custom_footer_codes = '';
        }
        
        // Definir as funções que serão usadas na view
        $data['getLogo'] = function() use ($generalSettings) {
            if (!empty($generalSettings->logo) && file_exists(FCPATH . $generalSettings->logo)) {
                return base_url($generalSettings->logo);
            }
            return base_url("assets/img/logo.svg");
        };
        
        $data['getFavicon'] = function() use ($generalSettings) {
            if (!empty($generalSettings->favicon) && file_exists(FCPATH . $generalSettings->favicon)) {
                return base_url($generalSettings->favicon);
            }
            return base_url("assets/img/favicon.png");
        };
        
        $data['generalSettings'] = $generalSettings;

        echo view('admin/includes/_header', $data);
        echo view('admin/general_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * General Settings Post
     */
    public function generalSettingsPost()
    {
        checkSuperAdmin();
        $settingsModel = new SettingsModel();
        if ($settingsModel->updateGeneralSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }

    /**
     * Cookies Warning Post
     */
    public function cookiesWarningPost()
    {
        checkSuperAdmin();
        
        if ($this->settingsModel->updateCookiesSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }
    
    /**
     * Contact Settings Post
     */
    public function contactSettingsPost()
    {
        checkSuperAdmin();
        
        // Coletar informações de contato
        $contactInfo = [
            'contact_address' => inputPost('contact_address'),
            'contact_email' => inputPost('contact_email'),
            'contact_phone' => inputPost('contact_phone'),
            'contact_text' => inputPost('contact_text')
        ];
        
        $data = [
            'contact_info' => json_encode($contactInfo)
        ];
        
        if ($this->settingsModel->builderGeneral->where('id', 1)->update($data)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }
    
    /**
     * Social Media Settings Post
     */
    public function socialMediaSettingsPost()
    {
        checkSuperAdmin();
        
        // Coletar URLs das redes sociais
        $socialUrls = [
            'facebook_url' => inputPost('facebook_url'),
            'twitter_url' => inputPost('twitter_url'),
            'instagram_url' => inputPost('instagram_url'),
            'pinterest_url' => inputPost('pinterest_url'),
            'linkedin_url' => inputPost('linkedin_url'),
            'vk_url' => inputPost('vk_url'),
            'telegram_url' => inputPost('telegram_url'),
            'youtube_url' => inputPost('youtube_url')
        ];
        
        // Atualizar facebook_url individual (para compatibilidade)
        $data = [
            'facebook_url' => inputPost('facebook_url'),
            'social_media_urls' => json_encode($socialUrls)
        ];
        
        if ($this->settingsModel->builderGeneral->where('id', 1)->update($data)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }
    
    /**
     * Custom Header Codes Post
     */
    public function customHeaderCodesPost()
    {
        checkSuperAdmin();
        $data = [
            'custom_header_codes' => inputPost('custom_header_codes'),
            'custom_footer_codes' => inputPost('custom_footer_codes')
        ];
        
        if ($this->settingsModel->builderGeneral->where('id', 1)->update($data)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }

    /**
     * Meta Conversions API Settings Post
     */
    public function metaConversionsApiPost()
    {
        checkSuperAdmin();
        
        if ($this->settingsModel->updateMetaConversionsApiSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('general-settings'));
    }

    /**
     * Load Users Dropdown
     */
    public function loadUsersDropdown()
    {
        $search = inputPost('search');
        $users = $this->authModel->getDropdownUsers($search);
        $content = '<ul class="dot-list cs-dropdown-search-results-items" data-type="addUser">';
        if (!empty($users)) {
            foreach ($users as $user) {
                $content .= '<li data-value="' . $user->id . '">' . esc($user->username) . '</li>';
            }
        }
        $content .= '</ul>';
        echo $content;
    }
}
