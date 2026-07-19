<?php

namespace Modules\Courses\Controllers;

use App\Controllers\BaseController;
use Modules\Courses\Models\SpaceModel;
use Modules\Courses\Models\CommunityPostModel;
use Modules\Courses\Models\CommunityCommentModel;
use Modules\Courses\Models\MemberProfileModel;
use Modules\Courses\Models\AchievementModel;
use Modules\Courses\Libraries\CommunityService;

/**
 * Comunidade (Fase 4c): feed social, espaços, post + comentários, reações, perfis, ranking,
 * notificações. Todas as rotas exigem login (auth). Gamificação compartilha a carteira de XP
 * dos cursos (points_ledger) — um único ranking.
 */
class CommunityController extends BaseController
{
    protected SpaceModel $spaces;
    protected CommunityPostModel $posts;
    protected CommunityCommentModel $comments;
    protected CommunityService $svc;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->spaces   = new SpaceModel();
        $this->posts    = new CommunityPostModel();
        $this->comments = new CommunityCommentModel();
        $this->svc      = new CommunityService();
    }

    private function userId(): int
    {
        return (int) (user()->id ?? 0);
    }

    public function index()
    {
        return $this->renderFeed(null);
    }

    public function space($slug)
    {
        $space = $this->spaces->getBySlug((string) $slug);
        if (empty($space)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->renderFeed($space);
    }

    private function renderFeed(?array $space)
    {
        $userId = $this->userId();
        $posts = $this->posts->feed($space['id'] ?? null, 40);
        $reacted = $this->svc->reactedTargets($userId, 'post', array_column($posts, 'id'));
        return view('courses/community/feed', [
            'pageTitle'  => $space ? $space['name'] : 'Comunidade',
            'spaces'     => $this->spaces->active(),
            'space'      => $space,
            'posts'      => $posts,
            'reacted'    => $reacted,
            'stats'      => $this->svc->userStats($userId),
            'unread'     => $this->svc->unreadCount($userId),
            'totalXp'    => $this->svc->userStats($userId)['xp'],
        ]);
    }

    public function post($id)
    {
        $post = $this->posts->withAuthor((int) $id);
        if (empty($post) || !empty($post['is_removed'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $userId = $this->userId();
        return view('courses/community/post', [
            'pageTitle' => $post['title'] ?: 'Publicação',
            'post'      => $post,
            'comments'  => $this->comments->forPost((int) $id),
            'reacted'   => in_array((int) $id, $this->svc->reactedTargets($userId, 'post', [(int) $id]), true),
            'unread'    => $this->svc->unreadCount($userId),
            'totalXp'   => $this->svc->userStats($userId)['xp'],
        ]);
    }

    public function createPost()
    {
        $userId = $this->userId();
        $spaceId = (int) $this->request->getPost('space_id');
        $body = trim((string) $this->request->getPost('body'));
        $title = trim((string) $this->request->getPost('title'));
        if ($spaceId <= 0 || $body === '') {
            return redirect()->to(site_url('comunidade'))->with('error', 'Escolha um espaço e escreva algo.');
        }
        $pid = $this->svc->createPost($userId, $spaceId, $title, $body);
        return redirect()->to(site_url('comunidade/post/' . $pid))->with('success', '+5 XP · publicado!');
    }

    public function createComment()
    {
        $userId = $this->userId();
        $postId = (int) $this->request->getPost('post_id');
        $body = trim((string) $this->request->getPost('body'));
        if ($postId <= 0 || $body === '') {
            return redirect()->back()->with('error', 'Escreva um comentário.');
        }
        $this->svc->createComment($userId, $postId, $body);
        return redirect()->to(site_url('comunidade/post/' . $postId))->with('success', '+2 XP · comentado!');
    }

    public function react()
    {
        $userId = $this->userId();
        $targetType = (string) $this->request->getPost('target_type');
        $targetId = (int) $this->request->getPost('target_id');
        if ($userId <= 0 || $targetId <= 0) {
            return $this->response->setJSON(['ok' => false]);
        }
        $res = $this->svc->toggleReaction($userId, $targetType, $targetId);
        return $this->response->setJSON(array_merge(['ok' => true], $res));
    }

    public function profile($userId)
    {
        $uid = (int) $userId;
        $profile = (new MemberProfileModel())->getForUser($uid);
        $posts = $this->posts->forUser($uid, 20);
        return view('courses/community/profile', [
            'pageTitle'    => $profile['display_name'] ?? 'Perfil do membro',
            'profileUser'  => $uid,
            'profile'      => $profile,
            'posts'        => $posts,
            'stats'        => $this->svc->userStats($uid),
            'achievements' => (new AchievementModel())->userAchievements($uid),
            'totalXp'      => $this->svc->userStats($this->userId())['xp'],
        ]);
    }

    public function leaderboard()
    {
        return view('courses/community/leaderboard', [
            'pageTitle'   => 'Ranking',
            'leaderboard' => $this->svc->leaderboard(30),
            'meId'        => $this->userId(),
            'totalXp'     => $this->svc->userStats($this->userId())['xp'],
        ]);
    }

    public function notifications()
    {
        $userId = $this->userId();
        $list = $this->svc->listNotifications($userId);
        $this->svc->markAllRead($userId);
        return view('courses/community/notifications', [
            'pageTitle'     => 'Notificações',
            'notifications' => $list,
            'totalXp'       => $this->svc->userStats($userId)['xp'],
        ]);
    }
}
