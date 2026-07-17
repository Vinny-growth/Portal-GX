<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Central de Conteúdos IA — controle de populares (cap/cooldown/blocklist),
 * novas settings de diversidade + X seed, e categoria "Seguro de Vida".
 */
class ContentAIPopularControlAndSeguro extends Migration
{
    public function up()
    {
        // 1) Tabela de controle por post popular (contador de derivações + blocklist)
        if (!$this->db->tableExists('popular_posts_control')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'post_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'derived_count'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'last_derived_at' => ['type' => 'DATETIME', 'null' => true],
                'blocked'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'blocked_reason'  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true], // manual | auto_cap
                'blocked_at'      => ['type' => 'DATETIME', 'null' => true],
                'title'           => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('post_id');
            $this->forge->addKey('blocked');
            $this->forge->createTable('popular_posts_control', true);
        }

        // 2) Novas settings (diversidade dos populares + X Pulse como fonte de pauta)
        $newCols = [
            'popular_max_derivations'   => ['type' => 'INT', 'constraint' => 11, 'default' => 2],   // após N, auto-block
            'popular_cooldown_days'     => ['type' => 'INT', 'constraint' => 11, 'default' => 14],  // não re-derivar dentro da janela
            'popular_diversity_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'popular_per_category_cap'  => ['type' => 'INT', 'constraint' => 11, 'default' => 2],   // máx. candidatos por categoria
            'x_seed_enabled'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'x_seed_per_day'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'last_run_x_seed'           => ['type' => 'DATETIME', 'null' => true],
        ];
        $add = [];
        foreach ($newCols as $col => $def) {
            if (!$this->db->fieldExists($col, 'content_ai_settings')) {
                $add[$col] = $def;
            }
        }
        if (!empty($add)) {
            $this->forge->addColumn('content_ai_settings', $add);
        }

        // 3) Categoria "Seguro de Vida" (idempotente por slug)
        $slug = 'seguro-de-vida';
        $exists = $this->db->table('categories')->where('slug', $slug)->countAllResults();
        if ($exists === 0) {
            // herda lang_id das categorias existentes (pt = 2 neste portal)
            $langRow = $this->db->table('categories')->select('lang_id')->orderBy('id', 'ASC')->get(1)->getFirstRow();
            $langId = $langRow->lang_id ?? 2;
            $now = date('Y-m-d H:i:s');
            $this->db->table('categories')->insert([
                'lang_id'          => $langId,
                'name'             => 'Seguro de Vida',
                'slug'             => $slug,
                'parent_id'        => 0,
                'description'      => 'Seguro de vida resgatável, whole life, proteção patrimonial e planejamento sucessório para empresas e famílias.',
                'keywords'         => 'seguro de vida, seguro de vida resgatável, whole life, proteção patrimonial, planejamento sucessório',
                'color'            => '#c9a96a',
                'block_type'       => 'block-2',
                'category_order'   => 6,
                'show_on_homepage' => 1,
                'show_on_menu'     => 1,
                'category_status'  => 1,
                'created_at'       => $now,
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('popular_posts_control')) {
            $this->forge->dropTable('popular_posts_control', true);
        }
        foreach (['popular_max_derivations', 'popular_cooldown_days', 'popular_diversity_enabled', 'popular_per_category_cap', 'x_seed_enabled', 'x_seed_per_day', 'last_run_x_seed'] as $col) {
            if ($this->db->fieldExists($col, 'content_ai_settings')) {
                $this->forge->dropColumn('content_ai_settings', $col);
            }
        }
        // categoria seguro-de-vida NÃO é removida no down (pode ter posts vinculados).
    }
}
