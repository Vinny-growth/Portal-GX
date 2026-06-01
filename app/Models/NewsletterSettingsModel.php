<?php namespace App\Models;

class NewsletterSettingsModel extends BaseModel
{
    public function get()
    {
        $row = $this->db->table('newsletter_settings')->where('id', 1)->get()->getRow();
        if (!$row) {
            // shouldn't happen after migration seed, but be defensive
            $this->db->table('newsletter_settings')->insert([
                'id' => 1,
                'double_opt_in_enabled' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $row = $this->db->table('newsletter_settings')->where('id', 1)->get()->getRow();
        }
        return $row;
    }

    public function updateSettings(array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table('newsletter_settings')->where('id', 1)->update($data);
    }

    public function isDoubleOptInEnabled(): bool
    {
        $s = $this->get();
        return (int) ($s->double_opt_in_enabled ?? 0) === 1;
    }
}
