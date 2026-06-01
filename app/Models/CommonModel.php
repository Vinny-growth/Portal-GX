<?php namespace App\Models;

use App\Libraries\CrmLeadClient;
use App\Libraries\LeadPhoneFormatter;

class CommonModel extends BaseModel
{
    protected $builderContact;
    protected $builderComments;
    protected $builderAds;
    protected $builderWidgets;
    protected $hasContactPhoneColumn = false;

    public function __construct()
    {
        parent::__construct();
        $this->builderContact = $this->db->table('contacts');
        $this->builderComments = $this->db->table('comments');
        $this->builderAds = $this->db->table('ad_spaces');
        $this->builderWidgets = $this->db->table('widgets');
        $this->hasContactPhoneColumn = $this->db->fieldExists('phone', 'contacts');
    }

    /*
    * --------------------------------------------------------------------
    * CONTACT
    * --------------------------------------------------------------------
    */

    //add contact message
    public function addContactMessage()
    {
        $normalizedPhone = LeadPhoneFormatter::toInternational(
            inputPost('phone_country'),
            inputPost('phone')
        );

        $email = trim((string) inputPost('email'));
        $dedupMinutes = (int) (getenv('LEAD_DEDUP_MINUTES') ?: 60);
        if ($dedupMinutes < 1) {
            $dedupMinutes = 60;
        }

        if (!empty($email) || !empty($normalizedPhone)) {
            $cutoff = date('Y-m-d H:i:s', time() - ($dedupMinutes * 60));
            $dedupBuilder = $this->db->table('contacts');
            $dedupBuilder->groupStart();
            if (!empty($email)) {
                $dedupBuilder->where('email', $email);
            }
            if (!empty($normalizedPhone) && $this->hasContactPhoneColumn) {
                if (!empty($email)) {
                    $dedupBuilder->orWhere('phone', $normalizedPhone);
                } else {
                    $dedupBuilder->where('phone', $normalizedPhone);
                }
            }
            $dedupBuilder->groupEnd();
            $dedupBuilder->where('created_at >=', $cutoff);
            $existing = $dedupBuilder->orderBy('id', 'DESC')->get(1)->getRow();
            if (!empty($existing)) {
                // Lead duplicado recente — silenciosamente retorna sucesso sem reinserir
                return true;
            }
        }

        $data = [
            'name' => inputPost('name'),
            'email' => inputPost('email'),
            'message' => inputPost('message'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($this->hasContactPhoneColumn) {
            $data['phone'] = $normalizedPhone;
        }
        $result = $this->builderContact->insert($data);

        if ($result) {
            // Capturar tudo que depende do request ANTES do defer (inputPost não funciona após finish_request)
            $insertId = $this->db->insertID();
            $contactEmail = $data['email'] ?? null;
            $contactName = $data['name'] ?? null;
            $contactPhone = $normalizedPhone ?? ($data['phone'] ?? null);
            $contactMessage = trim((string) ($data['message'] ?? ''));
            $sourceLabel = trim((string) inputPost('lead_origin')) ?: 'Formulário de contato GX Capital';
            $crmPayload = [
                'external_id' => $insertId,
                'name' => $contactName,
                'email' => $contactEmail,
                'phone' => $contactPhone,
                'message' => $contactMessage,
                'observations' => $contactMessage !== ''
                    ? 'Contato salvo na tabela contacts do site.'
                    : 'Lead salvo na tabela contacts sem mensagem detalhada.',
                'origem' => $sourceLabel,
                'landing_page' => trim((string) inputPost('landing_page')),
                'utm_source' => trim((string) inputPost('utm_source')),
                'utm_medium' => trim((string) inputPost('utm_medium')),
                'utm_campaign' => trim((string) inputPost('utm_campaign')),
                'utm_term' => trim((string) inputPost('utm_term')),
                'utm_content' => trim((string) inputPost('utm_content')),
                'source_system' => 'site-gx-php-contact',
            ];

            // Capturar event_id do client-side (se enviado pelo formulário) p/ deduplicação Pixel↔CAPI
            $clientEventId = trim((string) inputPost('event_id')) ?: null;

            // Agendar Meta API + CRM APÓS a resposta HTTP (non-blocking)
            deferAfterResponse(function () use ($contactEmail, $contactName, $contactPhone, $sourceLabel, $crmPayload, $clientEventId) {
                $firstName = null;
                $lastName = null;
                if (!empty($contactName)) {
                    $nameParts = explode(' ', trim($contactName), 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1] ?? null;
                }

                trackMetaContact(
                    $contactEmail,
                    [
                        'content_name' => $sourceLabel,
                        'content_category' => 'Contact Form',
                        'currency' => 'BRL',
                        'value' => 1,
                    ],
                    $clientEventId,
                    $contactPhone,
                    $firstName,
                    $lastName
                );

                (new CrmLeadClient())->send($crmPayload);
            });
        }

        return $result;
    }

    //get contact messages
    public function getContactMessages($limit = null)
    {
        if ($limit) {
            return $this->builderContact->orderBy('id DESC')->limit($limit)->get()->getResult();
        }
        return $this->builderContact->orderBy('id DESC')->get()->getResult();
    }

    //get contact messages
    public function getContactMessagesAll()
    {
        return $this->builderContact->orderBy('id DESC')->get()->getResult();
    }

    //get contact message
    public function getContactMessage($id)
    {
        return $this->builderContact->where('id', clrNum($id))->get()->getRow();
    }

    //delete contact message
    public function deleteContactMessage($id)
    {
        return $this->builderContact->where('id', clrNum($id))->delete();
    }

    //delete multi messages
    public function deleteMultiMessages($messages)
    {
        if (!empty($messages)) {
            foreach ($messages as $id) {
                $this->deleteContactMessage($id);
            }
        }
    }

    /*
    * --------------------------------------------------------------------
    * COMMENTS
    * --------------------------------------------------------------------
    */

    //add comment
    public function addComment()
    {
        $data = [
            'parent_id' => inputPost('parent_id'),
            'post_id' => inputPost('post_id'),
            'user_id' => user()->id,
            'comment' => inputPost('comment'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($this->generalSettings->comment_approval_system == 1) {
            $data['status'] = 0;
        } else {
            $data['status'] = 1;
        }
        $this->builderComments->insert($data);
        $this->updatePostTotalComments($data['post_id']);
    }

    //get comments
    public function getComments($postId, $limit)
    {
        return $this->builderComments->join('users', 'users.id = comments.user_id', 'left')->select('comments.*, users.username AS user_username, users.slug AS user_slug, users.avatar AS user_avatar')
            ->where('comments.post_id', clrNum($postId))->where('comments.parent_id = 0')->where('comments.status = 1')->orderBy('comments.id DESC')->get(clrNum($limit))->getResult();
    }
    
    //get latest comments
    public function getLatestComments($status, $limit)
    {
        return $this->builderComments->where('comments.status', clrNum($status))->orderBy('comments.id DESC')->limit($limit)->get()->getResult();
    }

    //get approved comments count
    public function getCommentsCount($status)
    {
        return $this->builderComments->where('comments.status', clrNum($status))->countAllResults();
    }

    //get approved comments
    public function getCommentsPaginated($status, $perPage, $offset)
    {
        return $this->builderComments->where('comments.status', clrNum($status))->orderBy('comments.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //subomments
    public function getSubComments($parentId)
    {
        return $this->builderComments->join('users', 'users.id = comments.user_id', 'left')->select('comments.*, users.username AS user_username, users.slug AS user_slug, users.avatar AS user_avatar')
            ->where('comments.parent_id', clrNum($parentId))->where('comments.status = 1')->orderBy('comments.id DESC')->get()->getResult();
    }

    //get comment count by post id
    public function getCommentCountByPostId($postId)
    {
        return $this->builderComments->where('comments.post_id', clrNum($postId))->where('parent_id = 0')->where('status = 1')->countAllResults();
    }

    //approve comment
    public function approveComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            $this->builderComments->where('id', $comment->id)->update(['status' => 1]);
            $this->updatePostTotalComments($comment->post_id);
            return true;
        }
        return false;
    }

    //approve multi comments
    public function approveMultiComments()
    {
        $commentIds = inputPost('comment_ids');
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->approveComment($id);
            }
        }
    }

    //update total comments
    public function updatePostTotalComments($postId)
    {
        //get post
        $post = $this->db->table('posts')->where('id', $postId)->get()->getRow();
        if (!empty($post)) {
            //get comment count
            $commentCount = $this->getCommentCountByPostId($post->id);
            //update post
            $this->db->table('posts')->where('id', $post->id)->update(['comment_count' => $commentCount]);
        }
    }

    //delete comment
    public function deleteComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            $this->builderComments->where('parent_id', $comment->id)->delete();
            $this->builderComments->where('id', $comment->id)->delete();
            $this->updatePostTotalComments($comment->post_id);
            return true;
        }
        return false;
    }

    //delete multi comments
    public function deleteMultiComments()
    {
        $commentIds = inputPost('comment_ids');
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->deleteComment($id);
            }
        }
    }

    //get comment
    public function getComment($id)
    {
        return $this->builderComments->where('id', clrNum($id))->get()->getRow();
    }

    /*
    * --------------------------------------------------------------------
    * WIDGETS
    * --------------------------------------------------------------------
    */

    //get widgets
    public function getWidgets()
    {
        return $this->builderWidgets->orderBy('id')->get()->getResult();
    }

    //get widget
    public function getWidget($id)
    {
        return $this->builderWidgets->where('id', clrNum($id))->get()->getRow();
    }

    //delete widget
    public function deleteWidget($id)
    {
        return $this->builderWidgets->where('id', clrNum($id))->delete();
    }

    /*
    * --------------------------------------------------------------------
    * AD SPACES
    * --------------------------------------------------------------------
    */

    //get ad spaces
    public function getAdSpaces()
    {
        return $this->builderAds->get()->getResult();
    }

    //get ad spaces by lang
    public function getAdSpacesByLang($langId)
    {
        return $this->builderAds->where('lang_id', clrNum($langId))->get()->getResult();
    }

    //get ad spaces by id
    public function getAdSpaceById($id)
    {
        return $this->builderAds->where('id', clrNum($id))->get()->getRow();
    }

    //get ad space
    public function getAdSpace($langId, $adSpace)
    {
        $row = $this->builderAds->where('lang_id', clrNum($langId))->where('ad_space', cleanStr($adSpace))->get()->getRow();
        if (!empty($row)) {
            return $row;
        }
        $data = [
            'lang_id' => clrNum($langId),
            'ad_space' => strSlug($adSpace),
            'ad_code_desktop' => '',
            'desktop_width' => 728,
            'desktop_height' => 90,
            'ad_code_mobile' => '',
            'mobile_width' => 300,
            'mobile_height' => 250
        ];
        if ($adSpace == 'sidebar_1' || $adSpace == 'sidebar_2') {
            $data['desktop_width'] = 336;
            $data['desktop_height'] = 280;
        }
        $this->builderAds->insert($data);
        return $this->builderAds->where('lang_id', clrNum($langId))->where('ad_space', cleanStr($adSpace))->get()->getRow();
    }

    //create ad code
    public function createAdCode($url, $imgPath, $width, $height)
    {
        return '<a href="' . $url . '" aria-label="link-bn'.'"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="' . base_url($imgPath) . '" width="' . $width . '" height="' . $height . '" alt="" class="lazyload"></a>';
    }

    //update google adsense code
    public function updateGoogleAdsenseCode()
    {
        return $this->db->table('general_settings')->where('id', 1)->update(['adsense_activation_code' => inputPost('adsense_activation_code')]);
    }

    public function fixNullRecords()
    {
        $builderCategory = $this->db->table('categories');
        if ($builderCategory->where('parent_id', null)->get()->getResult()) {
            $builderCategory->where('parent_id', null)->update(['parent_id' => 0]);
        }
        $builderPages = $this->db->table('pages');
        if ($builderPages->where('parent_id', null)->get()->getResult()) {
            $builderPages->where('parent_id', null)->update(['parent_id' => 0]);
        }
    }
}
