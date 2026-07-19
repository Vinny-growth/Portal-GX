<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class SpaceModel extends BaseModel
{
    protected $table         = 'community_spaces';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['name', 'slug', 'description', 'icon', 'color', 'sort', 'is_active', 'created_at', 'updated_at'];

    public function active(): array
    {
        return $this->where('is_active', 1)->orderBy('sort', 'ASC')->orderBy('name', 'ASC')->findAll();
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
