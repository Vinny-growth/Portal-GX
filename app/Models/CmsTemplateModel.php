<?php namespace App\Models;

class CmsTemplateModel extends BaseModel
{
    protected $table = 'cms_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title','type','json','created_at'];

    public function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->table($this->table)->insert($data);
    }

    public function all($type = 'section')
    {
        $builder = $this->db->table($this->table)->orderBy('id','DESC');
        if ($type) $builder->where('type', $type);
        return $builder->get()->getResult();
    }

    public function deleteById($id)
    {
        return $this->db->table($this->table)->where('id', clrNum($id))->delete();
    }
}

