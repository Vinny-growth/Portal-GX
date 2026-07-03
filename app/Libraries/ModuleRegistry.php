<?php namespace App\Libraries;

/**
 * Registro de módulos (white-label — Fase 0).
 *
 * Descobre manifestos em modules/*\/Config/module.php e cruza com a tabela `modules`
 * (estado enabled por install). É DEFENSIVO: qualquer falha de DB/manifesto resulta em
 * "desabilitado" em vez de exceção — porque isto é consultado durante o carregamento de
 * rotas, e um throw aqui derrubaria o site inteiro.
 */
class ModuleRegistry
{
    /** @var array<string,array>|null cache por request dos manifestos */
    protected static $manifests = null;

    /** @var array<string,int>|null cache por request do estado no DB (module_key => enabled) */
    protected static $dbStates = null;

    /** Manifestos descobertos, indexados por key. */
    public function manifests(): array
    {
        if (self::$manifests !== null) {
            return self::$manifests;
        }

        $out  = [];
        $base = ROOTPATH . 'modules';
        if (is_dir($base)) {
            foreach (glob($base . '/*/Config/module.php') as $file) {
                try {
                    $m = include $file;
                    if (is_array($m) && !empty($m['key'])) {
                        $m['_root'] = dirname($file, 2); // modules/<Nome>
                        $out[$m['key']] = $m;
                    }
                } catch (\Throwable $e) {
                    // manifesto inválido é ignorado — nunca derruba o request
                }
            }
        }

        return self::$manifests = $out;
    }

    /** Estado no DB: module_key => (int)enabled. [] se a tabela não existir ou em falha. */
    protected function dbStates(): array
    {
        if (self::$dbStates !== null) {
            return self::$dbStates;
        }

        $states = [];
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('modules')) {
                foreach ($db->table('modules')->get()->getResult() as $row) {
                    $states[$row->module_key] = (int) $row->enabled;
                }
            }
        } catch (\Throwable $e) {
            $states = [];
        }

        return self::$dbStates = $states;
    }

    /**
     * Um módulo está habilitado?
     * - se existe linha no DB para a key → o DB manda (enabled=1?);
     * - se NÃO existe linha → cai no enabled_default do manifesto;
     * - módulo inexistente → false.
     */
    public function enabled(string $key): bool
    {
        $manifests = $this->manifests();
        if (!isset($manifests[$key])) {
            return false;
        }

        $states = $this->dbStates();
        if (array_key_exists($key, $states)) {
            return $states[$key] === 1;
        }

        return !empty($manifests[$key]['enabled_default']);
    }

    /** Todos os módulos com seu estado resolvido. */
    public function all(): array
    {
        $out = [];
        foreach ($this->manifests() as $key => $m) {
            $out[$key] = ['manifest' => $m, 'enabled' => $this->enabled($key)];
        }
        return $out;
    }

    /**
     * Caminhos dos Config/Routes.php dos módulos HABILITADOS (existentes).
     * Carregados no topo do Routes.php principal para terem prioridade sobre o
     * catch-all (:any) da CMS.
     */
    public function enabledRouteFiles(): array
    {
        $files = [];
        foreach ($this->manifests() as $key => $m) {
            if (!$this->enabled($key) || empty($m['_root'])) {
                continue;
            }
            $rf = $m['_root'] . '/Config/Routes.php';
            if (is_file($rf)) {
                $files[] = $rf;
            }
        }
        return $files;
    }

    /** Itens de menu público dos módulos habilitados. */
    public function menuItems(): array
    {
        $items = [];
        foreach ($this->manifests() as $key => $m) {
            if ($this->enabled($key) && !empty($m['menu'])) {
                foreach ($m['menu'] as $mi) {
                    $items[] = $mi;
                }
            }
        }
        return $items;
    }

    /** Itens de navegação do admin dos módulos habilitados. */
    public function adminNav(): array
    {
        $items = [];
        foreach ($this->manifests() as $key => $m) {
            if ($this->enabled($key) && !empty($m['admin_nav'])) {
                foreach ($m['admin_nav'] as $ni) {
                    $items[] = $ni;
                }
            }
        }
        return $items;
    }
}
