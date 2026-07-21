<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Instalador white-label (Fase 5). Provisiona uma instância NOVA a partir de um banco vazio:
 * importa o schema base da plataforma (app/Database/schema/base_schema.sql — só estrutura,
 * sem conteúdo GX) → semeia as linhas mínimas de boot → grava brand_settings pelos parâmetros
 * (NÃO usa o BrandSeeder da GX) → liga os módulos escolhidos → cria o admin → verifica.
 *
 * Roda no ambiente atual (Apache+PHP+MySQL); Docker é um wrapper opcional que chama o mesmo
 * comando dentro do container. Idempotência: aborta se já instalado (brand_settings existe),
 * salvo --force. Use --dry-run para simular (não escreve nada).
 *
 * Ex.: php spark app:setup --brand-name="GX México" --locale=es-MX --currency=MXN \
 *        --admin-user=admin --admin-email=admin@gx.mx --admin-pass=Secreta123 \
 *        --modules=wealth,simulators --db=gxmx_db
 */
class AppSetup extends BaseCommand
{
    protected $group       = 'White-Label';
    protected $name        = 'app:setup';
    protected $description  = 'Provisiona uma nova instância white-label (schema base + brand + módulos + admin).';
    protected $usage        = 'app:setup [options]';

    private bool $dry = false;
    private $db;

    public function run(array $params)
    {
        $this->dry = CLI::getOption('dry-run') !== null;
        CLI::write('=== Instalador White-Label · app:setup' . ($this->dry ? ' [DRY-RUN]' : '') . ' ===', 'yellow');

        // ── conexão (default .env; parametrizável p/ provisionar outro banco) ──
        $def = config(Database::class)->default;
        $cfg = array_merge($def, array_filter([
            'hostname' => CLI::getOption('host'),
            'database' => CLI::getOption('db'),
            'username' => CLI::getOption('user'),
            'password' => CLI::getOption('pass'),
        ]));
        try {
            $this->db = Database::connect($cfg, false);
            $this->db->initialize();
            $this->db->query('SELECT 1');
        } catch (\Throwable $e) {
            CLI::error('Falha ao conectar no banco: ' . $e->getMessage());
            return;
        }
        CLI::write('• Conectado em ' . $cfg['database'] . ' @ ' . $cfg['hostname'], 'green');

        // ── já instalado? ──
        if ($this->db->tableExists('brand_settings') && $this->db->table('brand_settings')->where('id', 1)->countAllResults() > 0) {
            if (CLI::getOption('force') === null) {
                CLI::error('Este banco já parece instalado (brand_settings id=1 existe). Use --force para reinstalar por cima.');
                return;
            }
            CLI::write('• --force: prosseguindo por cima de instalação existente.', 'yellow');
        }

        // ── coleta de parâmetros (flags ou prompt interativo) ──
        $in = $this->collect();

        // ── 1) schema base ──
        $schemaFile = ROOTPATH . 'app/Database/schema/base_schema.sql';
        if (!is_file($schemaFile)) {
            CLI::error('Fixture não encontrado: ' . $schemaFile);
            return;
        }
        $stmts = $this->splitSql(file_get_contents($schemaFile));
        CLI::write('• Schema base: ' . count($stmts) . ' statements' . ($this->db->tableExists('users') ? ' (tabelas já existem — pulando import)' : ''));
        if (!$this->db->tableExists('users')) {
            $this->runStatements($stmts);
            CLI::write('  ✓ schema importado (' . count($stmts) . ' statements)', 'green');
        }

        // ── 2) linhas de boot mínimas ──
        $this->seedBoot($in);
        // ── 3) brand ──
        $this->seedBrand($in);
        // ── 4) módulos ──
        $this->seedModules($in);
        // ── 5) admin ──
        $this->createAdmin($in);

        // ── 6) migrations (delta pós-schema) + seeds ──
        // Só quando instalando no banco default do .env: spark migrate/db:seed usam a
        // conexão default, então com --db/--host apontaria pro banco errado.
        $sameDb = $cfg['database'] === $def['database'] && $cfg['hostname'] === $def['hostname'];
        if (!$this->dry && $sameDb) {
            $this->runMigrationsAndSeeds($in);
        } elseif (!$this->dry) {
            CLI::write('• Banco custom (--db/--host): aponte o .env pro novo banco e rode `spark migrate --all`, `spark db:seed ActuarialSheetSeeder`'
                . ($in['demo'] ? ' e o seed demo do Courses' : '') . '.', 'yellow');
        }

        // ── verificação ──
        if (!$this->dry) {
            $ok = $this->db->table('brand_settings')->where('id', 1)->countAllResults() > 0
               && $this->db->table('users')->countAllResults() > 0;
            CLI::write($ok ? '• Verificação: brand + admin presentes ✓' : '• Verificação FALHOU', $ok ? 'green' : 'red');
        }

        CLI::write("\n=== " . ($this->dry ? 'DRY-RUN concluído (nada foi escrito).' : 'Instalação concluída!') . ' ===', 'yellow');
        CLI::write('Marca: ' . $in['brand_name'] . ' · locale ' . $in['locale'] . ' · admin ' . $in['admin_email']);
        CLI::write('Próximos: configurar .env (baseURL, gateway/CRM), apontar o vhost.');
    }

    /** Aplica migrations mais novas que o schema base e semeia dados operacionais (+ demo com --demo). */
    private function runMigrationsAndSeeds(array $in): void
    {
        try {
            command('migrate --all');
            CLI::write('• Migrations: delta aplicado (todas as namespaces) ✓', 'green');
        } catch (\Throwable $e) {
            CLI::error('Migrations falharam: ' . $e->getMessage());
        }
        $seeder = Database::seeder();
        try {
            // dados atuariais (taxas/fatores da planilha) — obrigatórios p/ o simulador de seguro;
            // NÃO estão no base_schema (são dados, não estrutura)
            $seeder->call('App\Database\Seeds\ActuarialSheetSeeder');
            CLI::write('• Seed: taxas atuariais (ActuarialSheetSeeder) ✓', 'green');
        } catch (\Throwable $e) {
            CLI::error('Seed atuarial falhou: ' . $e->getMessage());
        }
        if (!empty($in['demo'])) {
            try {
                $seeder->call('Modules\Courses\Database\Seeds\CoursesDemoSeeder');
                CLI::write('• Seed: conteúdo demo do Courses (--demo) ✓', 'green');
            } catch (\Throwable $e) {
                CLI::error('Seed demo do Courses falhou: ' . $e->getMessage());
            }
        }
    }

    private function collect(): array
    {
        $opt = fn($k, $def = null) => CLI::getOption($k) ?? $def;
        $ask = function ($k, $label, $def = null) {
            $v = CLI::getOption($k);
            if ($v !== null) {
                return $v;
            }
            return $this->dry ? ($def ?? '') : CLI::prompt($label, $def);
        };
        $brandName = $ask('brand-name', 'Nome da marca (display)', 'Nova Marca');
        return [
            'brand_name'   => $brandName,
            'brand_legal'  => $opt('brand-legal', $brandName),
            'brand_email'  => $ask('brand-email', 'E-mail de contato', 'contato@exemplo.com'),
            'locale'       => $ask('locale', 'Locale (pt-BR|es-MX|...)', 'pt-BR'),
            'currency'     => $ask('currency', 'Moeda (BRL|MXN|USD)', 'BRL'),
            'timezone'     => $opt('timezone', 'America/Sao_Paulo'),
            'color_primary' => $opt('color-primary', '#0c3163'),
            'color_gold'   => $opt('color-gold', '#c9a96a'),
            'color_secondary' => $opt('color-secondary', '#dbc7a2'),
            'lang_name'    => $opt('lang-name', 'Português'),
            'admin_user'   => $ask('admin-user', 'Usuário admin', 'admin'),
            'admin_email'  => $ask('admin-email', 'E-mail admin', 'admin@exemplo.com'),
            'admin_pass'   => $opt('admin-pass') ?? ($this->dry ? 'dryrun' : CLI::prompt('Senha admin')),
            'modules'      => $opt('modules', 'default'), // "a,b" | "default" | "none" | "all"
            'demo'         => CLI::getOption('demo') !== null,
        ];
    }

    private function langShort(string $locale): string
    {
        return explode('-', $locale)[0] ?: 'pt';
    }

    private function seedBoot(array $in): void
    {
        $now = date('Y-m-d H:i:s');
        // languages (NOT NULL: name/short_form/language_code/text_direction)
        $this->insertOnce('languages', ['id' => 1], [
            'id' => 1, 'name' => $in['lang_name'], 'short_form' => $this->langShort($in['locale']),
            'language_code' => $in['locale'], 'text_direction' => 'ltr', 'status' => 1,
        ]);
        // roles (role_name serializado, formato legado)
        foreach ([[1, 'Super Admin', 1, 1], [2, 'Author', 0, 1], [3, 'Member', 0, 1]] as $r) {
            $this->insertOnce('roles', ['id' => $r[0]], [
                'id' => $r[0], 'is_super_admin' => $r[2], 'is_default' => $r[3],
                'role_name' => serialize([['lang_id' => '1', 'name' => $r[1]]]),
            ]);
        }
        // theme padrão
        $this->insertOnce('themes', ['id' => 1], ['id' => 1, 'theme_folder' => 'magazine', 'is_active' => 1]);
        // general_settings (id=1 obrigatório no boot)
        $this->insertOnce('general_settings', ['id' => 1], [
            'id' => 1, 'site_lang' => 1, 'theme_mode' => 'light', 'timezone' => $in['timezone'], 'pagination_per_page' => 10,
        ]);
        // settings por idioma
        $this->insertOnce('settings', ['lang_id' => 1], [
            'lang_id' => 1, 'site_title' => $in['brand_name'], 'site_description' => $in['brand_name'],
            'contact_email' => $in['brand_email'], 'copyright' => $in['brand_name'],
        ]);
        CLI::write('• Boot: languages/roles/themes/general_settings/settings semeados', 'green');
    }

    private function seedBrand(array $in): void
    {
        $now = date('Y-m-d H:i:s');
        $this->insertOnce('brand_settings', ['id' => 1], array_filter([
            'id' => 1, 'legal_name' => $in['brand_legal'], 'display_name' => $in['brand_name'],
            'email' => $in['brand_email'], 'locale' => $in['locale'], 'currency' => $in['currency'],
            'timezone' => $in['timezone'], 'color_primary' => $in['color_primary'],
            'color_gold' => $in['color_gold'], 'color_secondary' => $in['color_secondary'],
            'created_at' => $now, 'updated_at' => $now,
        ], fn($v) => $v !== null && $v !== ''), true);
        CLI::write('• Brand: brand_settings gravado (' . $in['brand_name'] . ')', 'green');
    }

    private function seedModules(array $in): void
    {
        $sel = strtolower(trim((string) $in['modules']));
        $chosen = ($sel === 'default' || $sel === '') ? null : array_filter(array_map('trim', explode(',', $sel)));
        $now = date('Y-m-d H:i:s');
        $lines = [];
        foreach (glob(ROOTPATH . 'modules/*/Config/module.php') as $file) {
            $m = @include $file;
            if (!is_array($m) || empty($m['key'])) {
                continue;
            }
            if ($chosen === null) {
                $enabled = !empty($m['enabled_default']) ? 1 : 0;
            } elseif ($sel === 'all') {
                $enabled = 1;
            } elseif ($sel === 'none') {
                $enabled = 0;
            } else {
                $enabled = in_array($m['key'], $chosen, true) ? 1 : 0;
            }
            $this->insertOnce('modules', ['module_key' => $m['key']], [
                'module_key' => $m['key'], 'name' => $m['name'] ?? $m['key'], 'version' => $m['version'] ?? '1.0.0',
                'enabled' => $enabled, 'sort' => 0, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $lines[] = $m['key'] . '=' . ($enabled ? 'on' : 'off');
        }
        CLI::write('• Módulos: ' . implode(', ', $lines), 'green');
    }

    private function createAdmin(array $in): void
    {
        $now = date('Y-m-d H:i:s');
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $in['admin_user'])) ?: 'admin';
        $this->insertOnce('users', ['email' => $in['admin_email']], [
            'username' => $in['admin_user'], 'email' => $in['admin_email'],
            'password' => password_hash($in['admin_pass'], PASSWORD_DEFAULT),
            'role_id' => 1, 'user_type' => 'registered', 'email_status' => 1, 'status' => 1,
            'slug' => $slug, 'created_at' => $now,
        ]);
        CLI::write('• Admin: ' . $in['admin_email'] . ' (role Super Admin)', 'green');
    }

    // ── infra ─────────────────────────────────────────────────────────────────
    private function insertOnce(string $table, array $where, array $data, bool $isBrandLike = false): void
    {
        if ($this->dry) {
            CLI::write('    [dry] INSERT ' . $table . ' ' . json_encode($where), 'dark_gray');
            return;
        }
        if (!$this->db->tableExists($table)) {
            CLI::write('    (tabela ausente: ' . $table . ' — pulando)', 'yellow');
            return;
        }
        $b = $this->db->table($table);
        foreach ($where as $k => $v) {
            $b->where($k, $v);
        }
        if ($b->countAllResults() > 0) {
            return; // idempotente
        }
        // filtra p/ colunas existentes (o schema pode variar entre versões)
        $cols = array_map(fn($c) => $c->name, $this->db->getFieldData($table));
        $payload = array_intersect_key($data, array_flip($cols));
        $this->db->table($table)->insert($payload);
    }

    private function splitSql(string $sql): array
    {
        $out = [];
        $buf = '';
        foreach (preg_split('/\r?\n/', $sql) as $line) {
            $t = trim($line);
            if ($t === '' || strpos($t, '--') === 0 || strpos($t, '/*') === 0) {
                continue;
            }
            $buf .= $line . "\n";
            if (substr($t, -1) === ';') {
                $out[] = trim($buf);
                $buf = '';
            }
        }
        if (trim($buf) !== '') {
            $out[] = trim($buf);
        }
        return $out;
    }

    private function runStatements(array $stmts): void
    {
        if ($this->dry) {
            CLI::write('    [dry] ' . count($stmts) . ' statements de schema seriam executados', 'dark_gray');
            return;
        }
        foreach ($stmts as $s) {
            $this->db->query($s);
        }
    }
}
