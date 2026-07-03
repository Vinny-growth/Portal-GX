# Fase 0 — Espinha Dorsal (detalhamento executável)

**Documento pai:** [`PLANO-WHITELABEL.md`](./PLANO-WHITELABEL.md) — este arquivo detalha a **Fase 0** em tarefas prontas para virar issues.
**Objetivo da fase:** criar as **fundações** da plataforma white-label **sem alterar em nada o comportamento da GX Brasil** (produção). Tudo aqui é **aditivo** — nenhuma substituição de string/cor/lógica existente (isso é Fase 1+).
**Princípio:** *behavior-preserving*. Ao final, a GX renderiza **igual**; ganhamos apenas a mecânica (brand config, registro de módulos, higiene de instalação) para as próximas fases usarem.

---

## 0. Preparação

- **Branch:** criar `feat/whitelabel-fase0-espinha` a partir de `main` (não continuar na `feat/geo-seo-foundation`).
- **Um PR por workstream** (A, B, C) para revisão isolada.
- **Verificação de regressão (obrigatória em cada PR):** capturar o HTML renderizado de páginas-chave da GX **antes** e **depois** e conferir equivalência: home (`/`), um post de blog, uma página de simulador, e o admin (login + dashboard). Nenhuma diferença visível esperada.

---

## Workstream A — Brand config (fonte única de marca)

> **Escopo Fase 0:** criar a **mecânica** (tabela + acesso + injeção nas views) **semeada com os valores atuais da GX**. **NÃO** trocar ainda os usos hardcoded (`HomeController`, `_json_ld.php`, cores) — isso é Fase 1.

### A1. Migration `brand_settings` (linha única)
- **Arquivo:** `app/Database/Migrations/2026-07-02-000001_CreateBrandSettings.php`
- Seguir o padrão idempotente do projeto (`if (! $this->db->tableExists('brand_settings'))`), namespace `App\Database\Migrations`, extends `CodeIgniter\Database\Migration`.
- **Colunas (linha única, `id=1`):**
  - Identidade: `legal_name`, `display_name`, `tagline`, `founder_name`, `founder_title`, `founder_schema_id`
  - Contato: `email`, `phone`, `whatsapp`, `address`
  - Social/SEO: `social_json` (JSON), `org_description`, `area_served`, `press_mentions_json` (JSON), `og_image`
  - Locale: `locale` (ex.: `pt-BR`), `currency` (ex.: `BRL`), `timezone`
  - Design tokens: `color_primary`, `color_gold`, `color_secondary`, `logo`, `logo_footer`, `favicon`
  - `created_at`/`updated_at`
- **DoD parcial:** `php spark migrate` cria a tabela sem tocar nas demais.

### A2. Seeder com valores atuais da GX
- **Arquivo:** `app/Database/Seeds/BrandSeeder.php` (padrão dos seeders existentes em `app/Database/Seeds/`).
- Popular a linha `id=1` com **exatamente** os valores GX de hoje (extrair de `general_settings`/`settings` + das strings hardcoded conhecidas: `display_name="GX Capital"`, `founder_name="Vinicius Teixeira"`, `area_served="Brasil"`, tokens `#0c3163`/`#c9a96a`/`#dbc7a2`, etc.). Isso garante que quando a Fase 1 trocar os usos por `brand()`, o resultado seja idêntico.
- Rodar: `php spark db:seed BrandSeeder`.

### A3. Carregar brand no boot + acessor
- **`app/Config/Globals.php`:** carregar a linha de `brand_settings` para `Globals::$brand` no bootstrap (mesmo lugar/estilo onde hoje se carrega `$generalSettings`/`$settings`). Cachear como os demais.
- **Helper:** `app/Helpers/brand_helper.php` com `brand(string $key, $default = null)` lendo de `Globals::$brand` (funciona em web **e** CLI/commands). Registrar em `app/Config/Autoload.php` no array `$files` (junto de `deferred_helper.php`/`meta_conversions_helper.php`).
- **Injeção nas views (1 linha):** em `app/Controllers/BaseController.php:141`, adicionar `'brand' => \Config\Globals::$brand` ao array de `$view->setData([...])`. A partir daí todas as views têm `$brand` disponível (sem uso ainda).

### A4. (Plumbing, sem uso) tokens de cor
- Criar um helper `brandCssVars()` que devolve `:root{--brand-primary:…;--brand-gold:…;…}` a partir do `brand_settings`. **Não** incluir ainda no `<head>` das páginas (Fase 1). Só deixar pronto e testado.

**DoD Workstream A:** `brand('display_name')` retorna `"GX Capital"`; `$brand` disponível nas views; **nenhuma mudança visível** na GX (nada consome ainda).

---

## Workstream B — Registro de módulos + feature flags

> **Base técnica:** o CI4 já tem auto-discovery de módulos ligado (`Config/Modules.php` `$enabled=true`, aliases incluem `routes`/`services`/`registrars`). Falta só registrar o namespace `Modules\` e criar o registro/flags.

### B1. Namespace e pasta de módulos
- **`app/Config/Autoload.php`** `$psr4`: adicionar `'Modules' => ROOTPATH . 'modules'`.
- Criar diretório `modules/` na raiz.

### B2. Migration `modules` (flags por install)
- **Arquivo:** `app/Database/Migrations/2026-07-02-000002_CreateModulesTable.php` (idempotente).
- **Colunas:** `id`, `key` (único), `name`, `version`, `enabled` (TINYINT, default 0), `sort` (INT), `meta_json` (JSON), timestamps.
- Seeder opcional para registrar os módulos que já existirão (ver Fase 3 — simuladores/wealth). Na Fase 0, basta a tabela + o módulo de prova (B4).

### B3. Serviço `ModuleRegistry`
- **Arquivo:** `app/Libraries/ModuleRegistry.php`, exposto via `Config\Services::moduleRegistry()`.
- Responsabilidades:
  - Descobrir manifestos em `modules/*/Config/module.php`.
  - Cruzar com a tabela `modules` (estado `enabled` por install), cacheando.
  - API: `enabled(string $key): bool`, `all(): array`, `menuItems(): array`, `adminNav(): array`.
- **Manifesto** `modules/<Nome>/Config/module.php` retornando array:
  ```php
  return [
    'key' => 'hello', 'name' => 'Hello', 'version' => '1.0.0',
    'requires' => [], 'menu' => [...], 'admin_nav' => [...],
    'permissions' => [], 'settings' => [], 'enabled_default' => false,
  ];
  ```

### B4. Módulo de prova `Hello` (valida o contrato)
- Criar `modules/Hello/` com:
  - `Config/module.php` (manifesto acima).
  - `Config/Routes.php` que **só registra a rota se habilitado**:
    ```php
    if (service('moduleRegistry')->enabled('hello')) {
        $routes->get('_hello', 'Modules\Hello\Controllers\Hello::index');
    }
    ```
  - `Controllers/Hello.php` retornando um "ok" simples.
  - Um item de menu declarado no manifesto, renderizado **apenas** quando habilitado (hook no builder de menu — ver B5).
- Migrations de módulo (padrão para o futuro): pasta `modules/Hello/Database/Migrations` com namespace `Modules\Hello\Database\Migrations`; `spark migrate --all` descobre por namespace. (No Hello não precisa de tabela; só documentar o padrão.)

### B5. Gating de menu
- Localizar o builder de menu público (`getMenuLinks` em `BaseController`) e o do admin. Adicionar um passo que **acrescenta os itens dos módulos habilitados** via `moduleRegistry->menuItems()`/`adminNav()`. Na Fase 0, o único item possível é o do Hello — e some quando desligado.

**DoD Workstream B:** com `modules.enabled=1` para `hello`, `GET /_hello` responde 200; com `enabled=0`, `/_hello` dá 404. **Zero impacto** nos módulos GX existentes. ✅ **Verificado em produção** (toggle 200↔404, `spark routes` sem duplicata).

> **Nota de implementação (o que foi feito de fato):** a CMS tem um catch-all `(:any) → HomeController::any` no fim do `Routes.php`. A auto-descoberta nativa do CI4 anexa as rotas de módulo **depois** do catch-all, então ele captura a rota antes (404). Solução aplicada: `ModuleRegistry::enabledRouteFiles()` carrega os `Config/Routes.php` dos módulos habilitados **no topo** do `Routes.php` principal (prioridade sobre o catch-all), com um guard (`define`) contra dupla inclusão. O menu de módulos é exposto via `moduleMenuItems` no `setData` do `BaseController`; a injeção no nav visível do tema fica para a Fase 3 (evita tocar partials de tema em produção agora).

---

## Workstream C — Higiene de instalação / portabilidade

> Objetivo: deixar o repo **preparado** para instalar em outro VPS, **sem alterar** a instalação atual da GX.

### C1. `.env.example` completo
- Criar **`.env.example`** na raiz (hoje só existe `.env.example.webstories` parcial), derivado do `.env` atual **com todos os segredos substituídos por placeholders** e seções comentadas:
  - `CI_ENVIRONMENT`, `app.baseURL`
  - `database.default.*`
  - `SIMULATOR_ALLOWED_ORIGINS`, `cookie.prefix`
  - Chaves de API (placeholders): `OPENAI_API_KEY`, `GROK_API_KEY`, CRM (`CRM_LEAD_*`, `CRM_NEWSLETTER_*`), `GSC_*`, `SEO_TARGET_DOMAIN`
  - Nota: gateways de pagamento (`MERCADOPAGO_*`, `STRIPE_*`) e vídeo virão na Fase 4.
- Confirmar que `.env` continua gitignored (está) — **nunca** commitar segredos.

### C2. `.user.ini` portável
- **Problema:** `.user.ini` contém `open_basedir=/www/wwwroot/gx.capital/:/tmp/` — caminho absoluto que **quebra (500)** em outro docroot. `.user.ini` é INI estático e **não aceita caminho relativo/derivado**.
- **Ação Fase 0 (não-destrutiva):**
  - **Não** remover/alterar o `.user.ini` da GX (produção intacta).
  - Adicionar **`.user.ini.example`** com placeholder documentado (`open_basedir=__DOCROOT__:/tmp/`) e a instrução de que **cada install define o seu** (ou define `open_basedir` no pool do PHP-FPM). O wizard (Fase 5) preencherá isso.
- Documentar também que `GSC_SERVICE_ACCOUNT_JSON` (caminho absoluto no `.env`) é **por install** — já é env-driven, então basta constar no `.env.example` como placeholder.

### C3. Nota de segurança (não-bloqueante)
- Registrar recomendação de **rotacionar** as chaves atuais do `.env` (OpenAI, Grok, GitHub PAT, CRM, DB) na criação de qualquer nova instância — higiene independente da fase.

**DoD Workstream C:** existem `.env.example` (sem segredos) e `.user.ini.example`; um clone limpo tem o que precisa para ser configurado; a instalação da GX permanece intocada.

---

## Definition of Done — Fase 0 (geral)

1. **GX Brasil renderiza equivalente** (home, post, simulador, admin) — verificação de HTML antes/depois passa.
2. `brand('display_name')` → `"GX Capital"` (lido do banco), `$brand` disponível nas views, **sem uso** ainda.
3. Toggle do módulo `Hello` via `modules.enabled` alterna `/_hello` **200 ↔ 404** e mostra/esconde o item de menu.
4. `php spark migrate` aplica as 2 novas migrations de forma limpa e idempotente; nada mais muda.
5. `.env.example` e `.user.ini.example` presentes; `.env` segue gitignored.

---

## Arquivos a criar / editar (resumo)

**Criar:**
- `app/Database/Migrations/2026-07-02-000001_CreateBrandSettings.php`
- `app/Database/Migrations/2026-07-02-000002_CreateModulesTable.php`
- `app/Database/Seeds/BrandSeeder.php`
- `app/Helpers/brand_helper.php`
- `app/Libraries/ModuleRegistry.php`
- `modules/Hello/Config/module.php`, `modules/Hello/Config/Routes.php`, `modules/Hello/Controllers/Hello.php`, `modules/Hello/Views/index.php`
- `.env.example`, `.user.ini.example`

**Editar (aditivo):**
- `app/Config/Autoload.php` — `$psr4` (+`Modules`), `$files` (+`brand_helper.php`)
- `app/Config/Globals.php` — carregar `Globals::$brand`
- `app/Config/Services.php` — registrar `moduleRegistry()`
- `app/Controllers/BaseController.php:141` — injetar `'brand'` no `setData` + hook de menu de módulos

---

## Riscos da fase e mitigação

| Risco | Mitigação |
|---|---|
| Mexer no `setData` global (`BaseController`) afetar todas as páginas | Mudança de 1 chave aditiva; verificação de HTML antes/depois em cada PR |
| Auto-discovery de rotas de módulo registrar algo indevido | Rotas do módulo sempre dentro de `if (enabled)`; módulo Hello desligado por default |
| Migration tocar tabela errada | Padrão idempotente (`tableExists`); as duas migrations só criam tabelas novas |
| Seeder GX divergir dos valores reais | Extrair valores de `general_settings`/strings atuais e conferir 1:1 |

**Esforço estimado:** ~1-2 semanas (A ~3-4 dias, B ~4-5 dias, C ~1-2 dias, + verificação).

---

## Saída da Fase 0 → entrada da Fase 1
Com a espinha pronta, a **Fase 1** passa a **consumir** `brand()` nos pontos hardcoded (HomeController, `_json_ld.php`, playbooks, `robots/llms`), incluir `brandCssVars()` no `<head>` e tokenizar as cores — tudo com a rede de segurança de que os valores seedados são os da GX (resultado idêntico), e então trocáveis por install.
