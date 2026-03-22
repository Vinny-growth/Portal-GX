<?php namespace App\Models;

class CmsPageModel extends BaseModel
{
    protected $table = 'cms_pages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title','slug','status','data_json','published_json','created_at','updated_at','published_at'];

    public function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->table($this->table)->insert($data);
        return $this->db->insertID();
    }

    public function updatePage(int $id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table($this->table)->where('id', clrNum($id))->update($data);
    }

    public function publish(int $id)
    {
        $row = $this->getById($id);
        if (!$row) return false;
        return $this->updatePage($id, [
            'published_json' => $row->data_json,
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getById(int $id)
    {
        return $this->db->table($this->table)->where('id', clrNum($id))->get()->getRow();
    }

    public function getBySlug(string $slug)
    {
        return $this->db->table($this->table)->where('slug', $slug)->get()->getRow();
    }

    public function all()
    {
        return $this->db->table($this->table)->orderBy('id','DESC')->get()->getResult();
    }
}

