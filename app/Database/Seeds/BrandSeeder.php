<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Fase 0 (white-label) — semeia brand_settings (id=1) com os valores ATUAIS da GX.
 * Isso garante que, quando a Fase 1 trocar os pontos hardcoded por brand(),
 * o resultado renderizado seja idêntico ao de hoje.
 *
 * Idempotente: não sobrescreve se a linha id=1 já existir.
 * Uso:  php spark db:seed BrandSeeder
 */
class BrandSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('brand_settings')->where('id', 1)->countAllResults() > 0) {
            return;
        }

        // Menções de imprensa atuais (fonte: app/Views/common/_json_ld.php)
        $press = [
            [
                'headline'      => 'Crédito com garantia imobiliária ganha relevância na alocação de capital das empresas',
                'publisher'     => 'Valor Econômico',
                'url'           => 'https://valor.globo.com/patrocinado/pulse-brand/noticia/2026/03/20/credito-com-garantia-imobiliaria-ganha-relevancia-na-alocacao-de-capital-das-empresas-1.ghtml',
                'datePublished' => '2026-03-20',
            ],
            [
                'headline'      => 'Câmbio deixa de ser rotina operacional e ganha peso estratégico na expansão de importadores e exportadores',
                'publisher'     => 'Valor Econômico',
                'url'           => 'https://valor.globo.com/patrocinado/pulse-brand/noticia/2026/03/11/cambio-deixa-de-ser-rotina-operacional-e-ganha-peso-estrategico-na-expansao-de-importadores-e-exportadores-1.ghtml',
                'datePublished' => '2026-03-11',
            ],
            [
                'headline'      => 'Em um cenário de comércio exterior aquecido, câmbio deixa de ser rotina operacional e ganha peso nas decisões financeiras das empresas',
                'publisher'     => 'O Globo',
                'url'           => 'https://oglobo.globo.com/patrocinado/pulse-brand/noticia/2026/03/19/em-um-cenario-de-comercio-exterior-aquecido-cambio-deixa-de-ser-rotina-operacional-e-ganha-peso-nas-decisoes-financeiras-das-empresas-1.ghtml',
                'datePublished' => '2026-03-19',
            ],
        ];

        $now = date('Y-m-d H:i:s');

        $this->db->table('brand_settings')->insert([
            'id'                  => 1,
            'legal_name'          => 'GX Capital',
            'display_name'        => 'GX Capital',
            'tagline'             => 'Soluções sofisticadas em capital, proteção e patrimônio.',
            'founder_name'        => 'Vinicius Teixeira',
            'founder_title'       => 'CEO & Founder',
            'founder_schema_id'   => '#person-vinicius-teixeira',
            'email'               => 'contato@gx.capital',
            'phone'               => '+55 (51) 2042-1991',
            'whatsapp'            => '555120421991',
            'address'             => 'Porto Alegre/RS, Brasil',
            'social_json'         => null, // social continua vindo de settings; não duplicamos aqui na Fase 0
            'org_description'     => 'Câmbio estruturado, crédito, consultoria financeira e wealth advisory para empresas e famílias.',
            'area_served'         => 'Brazil',
            'press_mentions_json' => json_encode($press, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'og_image'            => null,
            'locale'              => 'pt-BR',
            'currency'            => 'BRL',
            'timezone'            => 'America/Sao_Paulo',
            'color_primary'       => '#0c3163',
            'color_gold'          => '#c9a96a',
            'color_secondary'     => '#dbc7a2',
            'logo'                => null,
            'logo_footer'         => null,
            'favicon'             => null,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);
    }
}
