<?php namespace App\Models;

use CodeIgniter\Model;

class NewsletterModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('subscribers');
    }

    //add to subscriber
    public function addSubscriber($email, array $source = [])
    {
        $data = [
            'email' => $email,
            'token' => generateToken(),
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'active',
        ];

        $categoryId = isset($source['source_category_id']) ? (int) $source['source_category_id'] : 0;
        $postId = isset($source['source_post_id']) ? (int) $source['source_post_id'] : 0;
        $url = '';
        if (isset($source['source_url'])) {
            $raw = trim((string) $source['source_url']);
            if (filter_var($raw, FILTER_VALIDATE_URL)) {
                $url = $raw;
            }
        }

        if ($categoryId > 0) $data['source_category_id'] = $categoryId;
        if ($postId > 0) $data['source_post_id'] = $postId;
        if (!empty($url)) $data['source_url'] = mb_substr($url, 0, 500);

        // map category -> editorial line ids
        $lineIds = [];
        if ($categoryId > 0) {
            $lineModel = new NewsletterEditorialLineModel();
            $lineIds = $lineModel->getMatchingLineIdsForCategory($categoryId);
        }
        if (!empty($lineIds)) {
            $data['editorial_line_ids'] = json_encode(array_values(array_unique($lineIds)));
        }

        return $this->builder->insert($data);
    }

    public function getActiveSubscribersForLine($editorialLineId)
    {
        $editorialLineId = (int) $editorialLineId;
        // any row where editorial_line_ids JSON contains the id; treat NULL/empty as "geral" (all lines)
        $rows = $this->db->table('subscribers')
            ->where('(status IS NULL OR status = "active")', null, false)
            ->get()->getResult();
        $matched = [];
        foreach ($rows as $row) {
            $ids = [];
            if (!empty($row->editorial_line_ids)) {
                $decoded = json_decode($row->editorial_line_ids, true);
                if (is_array($decoded)) {
                    $ids = array_map('intval', $decoded);
                }
            }
            // empty editorial_line_ids = "geral" (recebe todas as linhas)
            if (empty($ids) || in_array($editorialLineId, $ids, true)) {
                $matched[] = $row;
            }
        }
        return $matched;
    }

    public function updateEngagement($subscriberId, $delta = 1.0)
    {
        $subscriberId = (int) $subscriberId;
        $this->db->table('subscribers')
            ->where('id', $subscriberId)
            ->set('engagement_score', "engagement_score + " . (float) $delta, false)
            ->set('last_engagement_at', date('Y-m-d H:i:s'))
            ->update();
    }

    //update subscriber token
    public function updateSubscriberToken($email)
    {
        $subscriber = $this->getSubscriber($email);
        if (!empty($subscriber)) {
            if (empty($subscriber->token)) {
                $this->builder->where('email', cleanStr($email))->update(['token' => generateToken()]);
            }
        }
    }

    //get subscribers count
    public function getSubscribersCount()
    {
        return $this->builder->countAllResults();
    }
    
    //get subscribers
    public function getSubscribers()
    {
        return $this->builder->orderBy('id DESC')->get()->getResult();
    }

    //load more subscribers
    public function loadMoreSubscribers($q, $perPage, $offset)
    {
        $q = cleanStr($q);
        if (!empty($q)) {
            $this->builder->like('email', $q);
        }
        return $this->builder->orderBy('id')->limit($perPage, $offset)->get()->getResult();
    }

    //get subscriber emails by ids
    public function getSubscriberEmailsByIds($ids)
    {
        $emails = array();
        $rows = $this->builder->select('email')->whereIn('id', $ids, false)->get()->getResult();
        if (!empty($rows)) {
            $emails = array_map(function ($item) {
                return $item->email;
            }, $rows);
        }
        return $emails;
    }

    //get subscriber
    public function getSubscriber($email)
    {
        return $this->builder->where('email', cleanStr($email))->get()->getRow();
    }

    //delete from subscribers
    public function deleteSubscriber($id)
    {
        return $this->builder->where('id', clrNum($id))->delete();
    }

    //get subscriber by token
    public function getSubscriberByToken($token)
    {
        return $this->builder->where('token', cleanStr($token))->get()->getRow();
    }

    //unsubscribe email — soft delete so future imports do not silently reactivate the contact (LGPD)
    public function unsubscribeEmail($email)
    {
        return $this->builder->where('email', cleanStr($email))->update([
            'status'          => 'unsubscribed',
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'newsletter_status' => inputPost('newsletter_status'),
            'newsletter_popup' => inputPost('newsletter_popup')
        ];

        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadTempFile('file');
        if (!empty($file) && !empty($file['path'])) {
            @unlink(FCPATH . $this->generalSettings->newsletter_image);
            $data['newsletter_image'] = $uploadModel->uploadNewsletterImage($file['path']);
        }
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //send email
    public function sendEmail()
    {
        $emailModel = new EmailModel();
        $email = inputPost('email');
        $subject = inputPost('subject');
        $body = inputPost('body');
        $submit = inputPost('submit');
        if ($submit == "subscribers") {
            $subscriber = $this->getSubscriber($email);
            if (!empty($subscriber)) {
                if ($emailModel->sendEmailNewsletter($subscriber, $subject, $body)) {
                    return true;
                }
            }
        } else {
            $data = [
                'subject' => $subject,
                'message' => $body,
                'to' => $email,
                'template_path' => "email/email_newsletter",
                'subscriber' => null,
            ];
            return $emailModel->sendEmail($data);
        }
        return false;
    }
}