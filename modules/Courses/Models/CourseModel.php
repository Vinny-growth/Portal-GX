<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class CourseModel extends BaseModel
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // gerenciamos created_at/updated_at manualmente
    protected $allowedFields = [
        'title', 'slug', 'subtitle', 'description', 'cover_image', 'trailer_url',
        'category', 'level', 'instructor', 'access_level_id', 'is_published', 'is_featured',
        'drip_enabled', 'xp_reward', 'estimated_minutes', 'course_order',
        'created_at', 'updated_at',
    ];

    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /** Catálogo público agrupado por categoria (para os carrosséis estilo Netflix). */
    public function getPublishedGroupedByCategory(): array
    {
        $rows = $this->where('is_published', 1)
            ->orderBy('course_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();
        $grouped = [];
        foreach ($rows as $c) {
            $cat = trim((string) ($c['category'] ?? '')) ?: 'Cursos';
            $grouped[$cat][] = $c;
        }
        return $grouped;
    }

    /** Curso em destaque para o hero do catálogo (mais recente marcado). */
    public function getFeatured(): ?array
    {
        return $this->where('is_published', 1)->where('is_featured', 1)
            ->orderBy('updated_at', 'DESC')->first();
    }

    public function getForAdmin(): array
    {
        return $this->orderBy('course_order', 'ASC')->orderBy('id', 'DESC')->findAll();
    }

    /** Gera um slug único a partir do título. */
    public function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = url_title($title, '-', true) ?: 'curso';
        $slug = $base;
        $i = 2;
        while (true) {
            $q = $this->where('slug', $slug);
            if ($ignoreId) {
                $q->where('id !=', $ignoreId);
            }
            if ($q->countAllResults() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}
