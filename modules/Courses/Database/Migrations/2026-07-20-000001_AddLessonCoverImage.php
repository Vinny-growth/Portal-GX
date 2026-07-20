<?php namespace Modules\Courses\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona capa (imagem) às aulas — Fase 4a+. Cursos já têm cover_image; aulas passam a ter
 * também, para a geração de imagem por IA (mesmo pipeline das capas do blog). Aditiva/guardada.
 */
class AddLessonCoverImage extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('lessons') && !$this->db->fieldExists('cover_image', 'lessons')) {
            $this->forge->addColumn('lessons', [
                'cover_image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'slug'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('lessons') && $this->db->fieldExists('cover_image', 'lessons')) {
            $this->forge->dropColumn('lessons', 'cover_image');
        }
    }
}
