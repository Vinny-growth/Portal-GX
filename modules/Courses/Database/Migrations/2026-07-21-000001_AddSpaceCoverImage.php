<?php namespace Modules\Courses\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona capa (imagem) aos espaços da comunidade — Fase 4c+. Permite gerar um banner por IA
 * (mesmo pipeline das capas de curso/aula). Aditiva/guardada.
 */
class AddSpaceCoverImage extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('community_spaces') && !$this->db->fieldExists('cover_image', 'community_spaces')) {
            $this->forge->addColumn('community_spaces', [
                'cover_image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'color'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('community_spaces') && $this->db->fieldExists('cover_image', 'community_spaces')) {
            $this->forge->dropColumn('community_spaces', 'cover_image');
        }
    }
}
