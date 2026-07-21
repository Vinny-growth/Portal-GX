# Auditoria Geral do Projeto — 21/jul/2026

> **ATENÇÃO — confidencial.** Este documento lista vulnerabilidades e caminhos sensíveis. O `.htaccess` atual serve qualquer arquivo físico do docroot (só bloqueia `.env`), então `docs/*.md` é baixável publicamente se a URL for conhecida. Recomenda-se bloquear `docs/` e `*.md` no `.htaccess` (ver achado INFRA-2) ou mover este arquivo para fora do docroot.

**Escopo:** auditoria completa (somente leitura) de rotas/views, módulo Courses, módulos Wealth/Simulators/Hello, core do app (controllers/models/libraries/helpers), configuração/infra/instalador white-label, smoke test HTTP de produção e análise dos logs dos últimos 7 dias. Executada por 6 agentes de exploração em paralelo. Branch: `feat/whitelabel-fase0-espinha`. Tema ativo: `magazine`.

**Veredito geral:** o site público está saudável (todas as páginas-chave respondem 200 sem erro PHP; motor atuarial validado contra a planilha até o centavo; pipeline de conteúdo IA robusto; zero segredos hardcoded; zero SQL injection). Os problemas se concentram em: (1) segurança de pagamento do módulo Courses (dormente, mas bloqueia a Fase 6), (2) 4 funções quebradas no painel admin, (3) exposição de arquivos sensíveis no docroot, (4) higiene de logs/infra.

---

## 1. CRÍTICOS

### CRIT-1 — Webhook Stripe não valida assinatura → membership pago forjável
- `modules/Courses/Libraries/Gateways/StripeGateway.php:50-80` + `modules/Courses/Controllers/WebhookController.php:42-55`
- O docblock anuncia `COURSES_STRIPE_WEBHOOK_SECRET` para verificação de assinatura, mas `parseWebhook()` nunca lê o header `Stripe-Signature` nem usa o secret — apenas decodifica o corpo e confia nele. A rota `courses/webhook/pagamento/(:segment)` é pública e isenta de CSRF (`app/Config/Filters.php:80,122`).
- **Consequência:** qualquer um que conheça a URL pode POSTar um evento forjado com `metadata[document]` arbitrário e conceder a si mesmo assinatura anual paga.
- **Dormente** enquanto o flag `courses` estiver desligado (está, na GX Brasil), mas bloqueia qualquer install com pagamentos reais.
- Contraste: o webhook do CRM valida segredo com `hash_equals` corretamente (`WebhookController.php:63-67`) — o padrão existe no código, só não foi aplicado aos gateways.

### CRIT-2 — Acesso "cortesia de cliente GX" (client_comp) quebrado de ponta a ponta
- `modules/Courses/Models/MembershipModel.php:35` (`linkUser()` definido mas **nunca chamado**), `modules/Courses/Libraries/MembershipService.php:99-114`, `modules/Courses/Controllers/WebhookController.php:60-99`
- O webhook do CRM cria membership por documento com `user_id = NULL` (o CRM não conhece o user do LMS). Mas o gate de acesso resolve por `user_id` (`MembershipModel::getForUser()` faz `where('user_id', ...)`). Não existe campo de documento na tabela `users` para reconciliar.
- **Consequência:** o cliente GX que deveria ganhar acesso cortesia nunca o recebe ao logar. Peça faltante mais importante da Fase 4b.

---

## 2. ALTOS

### ALTO-1 — Quatro funções quebradas no painel admin (ao vivo, hoje)
| Função | Quebra | Local |
|---|---|---|
| Editor de fontes | `editFont()` renderiza `view('admin/edit_font')` que não existe (views novas `admin/font/edit.php`/`fonts.php` ficaram órfãs — refactor abandonado no meio) | `app/Controllers/AdminController.php:1706` (redirect pós-save em `:1739`) |
| Botão "Gerar sitemap" (SEO Tools) | `sitemapPost()` renderiza `view('admin/generate_sitemap')` inexistente. O cron usa outro caminho, por isso o sitemap segue atualizado — só a geração manual quebra | `app/Controllers/AdminController.php:1277`; botão em `app/Views/admin/seo_tools.php:201` |
| Seletor de idioma do header admin | Rota `Admin/setActiveLanguagePost` (`app/Config/Routes.php:369`) aponta para método que não existe em `AdminController`; formulário ativo em `app/Views/admin/includes/_header.php:72` | `PageNotFoundException` ao submeter |
| Excluir assinante da newsletter | Rota `Admin/deleteSubscriberPost` (`app/Config/Routes.php:376`) sem método; botão gerado em `app/Controllers/AjaxController.php:221` | `PageNotFoundException` ao clicar |

### ALTO-2 — Arquivos sensíveis servíveis pela web no docroot
- O `.htaccess` só bloqueia `.env` (`<Files .env>`); qualquer arquivo físico existente é servido diretamente.
- **`github_deploy_key`** — acessível via `https://gx.capital/github_deploy_key` (48 bytes, parece token/placeholder; não versionado, mas presente no docroot).
- **`do_push.sh` / `github_push.sh`** — servidos como texto; revelam o mecanismo de deploy e o repositório alvo (credenciais vêm do `.env`, não hardcoded — bom).
- **`get_page.php`** — endpoint de debug TRACKED que bootstrapa o CI e imprime conteúdo da página CMS id=10 sem auth.
- Também baixáveis: `GX Capital Design System.zip` (598 KB), `Dinamica seg resgatavel.xlsx` (planilha atuarial fonte-de-verdade), `Importacao Blindada.html`, arquivos de instruções internas.
- **Correção:** bloquear por extensão/nome no `.htaccess` (`.sh`, `.sql`, `.key`, `.zip`, `.xlsx`, `.md`, `github_deploy_key`, `get_page.php`) ou mover para fora do docroot.

### ALTO-3 — `writable/logs` com 2,1 GB e sem rotação
- 419 arquivos de log (mai/2025 → jul/2026), ~30–50 MB/dia, nunca purgados. Nenhum job de limpeza no crontab (só `uploads/tmp` é limpo).
- Risco real de encher o disco da VPS. Agendar purga de logs > N dias.
- A causa do volume é o ALTO-4 abaixo.

### ALTO-4 — 206.400 warnings em 7 dias por método HTTP em minúsculas
- `modules/Simulators/Config/Routes.php:30` — `$routes->match(['post','options'], 'api/quotation/preview', ...)` gera `[DEPRECATED] Passing lowercase HTTP method` (2 linhas + stack) **por request**. É ~90% do volume de log.
- **Fix trivial:** `['POST','OPTIONS']`.

---

## 3. MÉDIOS

### Courses (dormentes enquanto o módulo estiver desligado)
- **MED-1** Estorno/chargeback não revoga acesso: `WebhookController::paymentWebhook()` só trata `payment_paid` (`WebhookController.php:42`); `payment_refunded`/`payment_failed` são gravados em `payment_events` mas nenhuma mutação de membership ocorre. Não existe método de revogação no `MembershipService`.
- **MED-2** Webhook MercadoPago sem validação do header `x-signature` (`MercadoPagoGateway.php:44-74`); mitigado por re-consulta do pagamento na API, mas **o valor pago não é conferido contra o plano** — pagamento de valor arbitrário aprovado ativa o plano cheio.
- **MED-3** Progresso parcial de vídeo nunca é salvo: endpoint `curso/aula/progresso` (`StudentController.php:205-217`) implementado no servidor mas o front (`app/Views/courses/lesson.php:87-135`) só instrumenta o botão "Concluir aula" — não há listener de player nem envio periódico. `last_position_seconds` fica sempre 0; sem "continuar de onde parou" real.
- **MED-4** Perfil de membro da comunidade não é editável: `MemberProfileModel::upsert()` nunca é chamado; não há rota/view de edição. `display_name`/`bio`/`avatar_url` sempre nulos (`MemberProfileModel.php:18`, `CommunityController.php:124`).
- **MED-5** Comunidade sem gating de membership: rotas exigem só `auth` (`modules/Courses/Config/Routes.php:39-47`) — qualquer logado posta/comenta, apesar da página de assinatura vender comunidade como benefício pago. Confirmar se é intencional.
- **MED-6** `MembershipService::expireSweep()` (`:158`) nunca é agendado — a coluna `status` fica defasada na tela admin (acesso ao vivo é computado corretamente por `isActive()`).

### Instalador / white-label (bloqueiam a Fase 6)
- **MED-7** `base_schema.sql` defasado 2 migrations: faltam `lessons.cover_image` e `community_spaces.cover_image` (migrations de 20 e 21/jul). Como `INSTALL.md §5` diz que não é preciso rodar `spark migrate` numa instalação nova, um install de Educação nasceria com colunas faltando → erro em runtime. **Regenerar o `base_schema.sql` a cada migration nova, ou fazer o `app:setup` rodar `migrate` ao final.**
- **MED-8** Flag `--demo` do `app:setup` é stub: coletada em `app/Commands/AppSetup.php:128` mas nunca consumida — nenhum seeder de demo é invocado.
- **MED-9** Dados atuariais (`actuarial_rates`: 104 linhas, `reserve_factors`: 9.048 linhas) vêm **só** do `ActuarialSheetSeeder` — zero INSERTs no `base_schema.sql`. Um provisionamento novo sem `php spark db:seed ActuarialSheetSeeder` deixa o simulador de seguro retornando 400 "Sem taxas para idade".
- **MED-10** Prometido pelo plano e inexistente: Docker/compose (Fase 5, descrito como futuro no INSTALL.md) e i18n `es`/`es-MX` (Fase 6 GX México — `app/Language/` só tem `en` e `pt`; `app:setup --locale es-MX` aceitaria o flag mas a UI sairia em pt/en).
- **MED-11** Tabelas do LMS sem prefixo (`courses`, `lessons`, `certificates`, `memberships`, `payments`, `points_ledger`...) — num install sobre base que já tenha homônimas, os guards `tableExists` fariam a migration **pular silenciosamente** e o schema divergiria. As de comunidade foram prefixadas (`community_*`) de propósito; o LMS não recebeu o mesmo cuidado.

### Crons faltando
- **MED-12** `newsletter:scheduler` e `newsletter:sender` existem como Commands mas **não estão no crontab** — planejamento e disparo automático de newsletters não ocorrem (só `newsletter:sync-crm` roda, 03:00 UTC). Confirmar se o disparo é intencionalmente manual.
- **MED-13** Rota `cron/update-feeds` (`CronController::checkFeedPosts`) existe mas não há linha de crontab chamando-a — captura de RSS sem agendamento.

### Conteúdo / produção
- **MED-14** ~12 capas `.webp` de jun/2026 (`/uploads/images/202606/image_*.webp`) referenciadas no banco mas **ausentes do disco** — imagens quebradas reais com referer `/blog` e páginas de post (~15 hits/semana nos logs).
- **MED-15** Link literal `/null` renderizado em posts: 48 hits de 404 com referer de posts próprios (ex.: `/tesouro-direto-ou-cdb-em-2026`) — algum campo vazio virando string "null" num `href`/`src` do template de post. Inspecionar o HTML dos posts.
- **MED-16** `DashboardIntegrationModel.php:219` — 18.940 warnings "storing credential with base64 only — encryption unavailable": falta a chave `encryption.key` no `.env`; credenciais de integração salvas sem criptografia real.
- **MED-17** Upload sem validação de MIME real e allowlist opcional (`app/Models/UploadModel.php:33-53,56-63,66-73,187`): extensão derivada do nome enviado pelo cliente; caminhos `uploadTempFile`/`uploadAudio`/`uploadFile` (com config vazia) aceitam qualquer extensão. Mitigado por nome randômico + auth de admin, mas impor allowlist + checagem MIME em todos os caminhos.

### Legado / limpeza
- **MED-18** Artefatos Aurum órfãos: `/simulador de credito` (HTML standalone com branding antigo, acessível via web), `/aurum-simulador.php` (entrypoint morto), `app/Views/simulador-aurum.php` e `app/Views/themes/magazine/simulador-aurum.php` (iframe aponta para `aurum-simulador-v2.html` **inexistente**). Nada linka para eles — remover.
- **MED-19** `wm_model = 'GPT-5'` nas configs do Wealth Manager sugere conversa por IA, mas `WealthAgent` é 100% parser de regex — zero integração LLM (`modules/Wealth/Controllers/WealthAdminController.php:39,55`). Implementar ou remover o campo para não induzir a erro.
- **MED-20** Feed RSS ID 1 auto-importando o próprio site: a guarda anti self-import segura (161 logs/semana em `app/Models/RssModel.php:177`), mas o feed continua ativo apontando para `gx.capital/gnews/feed` — desativar no admin.

---

## 4. MENORES

- Sitemap regenerado (não commitado) dropou `/fx-loan`, `/simulador-de-risco-cambial` e `/web-stories/story/36` — confirmar se a exclusão foi intencional (se as páginas seguem no ar, é regressão de indexação).
- 404s internos recorrentes que merecem 301 na lista `legacyRedirect` de `modules/Simulators/Config/Routes.php`: `/trade-finance` (13), `/fechamento-de-cambio` (11), `/hedge-cambial` (10), `/4131` (8), `/hedge-ndf-termo-swap-opcoes` (5), e posts renomeados (`/fintech-banco-ou-correspondente-...-credito-pj`, `/guia-de-defesa-de-credito-amp-...` — o `-amp-` indica encoding quebrado de `&` na geração antiga do slug; o redirect novo no diff não commitado do `HomeController` já cobre parte disso).
- Catches silenciosos sem `log_message`: `ContentAIController.php:57-69` (3×), `SeoAnalysisController.php:195-197`.
- `app/Views/courses/subscription.php:62` diz "Comunidade (em breve)" — a comunidade já existe e está roteada.
- Carência (30d), meses (12) e preço default (349.00) hardcoded em `MembershipService.php:20-21` / `GatewayFactory.php:30` — para white-label, deveriam vir de config/env.
- `payments.raw_json` existe na migration mas nunca é populado (`MembershipService::activatePaid()`).
- Settings mortos no Wealth: `wm_crescimento_renda` gravado e nunca lido; propriedade `$steps` do `WealthAgent` (15 passos) declarada e nunca usada; `runSetup()` duplica migrations via Forge.
- `QuotationGate.php:36-54` — mapa ITCMD por UF marcado no código como "a validar com a área tributária"; pendência de validação jurídica, não técnica.
- Renda default `value="30.000"` hardcoded em `app/Views/simulators/seguro_resgatavel.php:104`.
- "Renovação" do membership é recompra manual (Stripe `mode=payment`), não assinatura recorrente — confirmar se é o modelo de negócio desejado.
- Duplicatas triviais em `$postRoutesArray` (`Ajax/addCommentPost` 4×, `Admin/deletePagePost` 2×, etc. — `app/Config/Routes.php:346,381,430-440,460-462`).
- Código morto: `CategoryController::subCategories()` (`:141`, sem rota, view ausente), `AdminController::updateSimulatorPage()` (`:403`, sem rota).
- Chaves usadas no código e ausentes do `.env`/`.env.example` (caem em defaults): `OPENAI_BRAND_STYLE`, `PAGEVIEW_RETENTION_DAYS`, `WEBSTORIES_DAILY_IMAGE_LIMIT`, `WEBPCONVERT_*`. Chaves `CRM_LEAD_*` no `.env` sem `env()` correspondente — verificar se estão ligadas via `config()`.
- Warning CLI `Undefined array key "REQUEST_URI"` em `app/Common.php:5` polui logs de cron.
- `SitemapModel` com deprecated de dynamic property (485 logs/semana).
- `TrendsFetcher.php:222` — endpoint do Bacen retornando HTTP 400 ~2×/dia; 3 itens do content_calendar (920, 921, 934) pulados por "Duplicate title" — precisam de edição manual do título.
- Arquivos legados TRACKED na raiz: `create_web_stories_table.sql` (já migrado), `Web.config` (IIS, irrelevante).
- `/rss` retorna 404 (o feed vive em `/rss-feeds` e `/gnews/feed`) — considerar redirect por convenção.

## 5. QUEBRAS LATENTES (só disparam se trocarem o tema ativo)

- `app/Views/themes/classic/cms/page.php` **não existe** — trocar para o tema classic quebraria TODA página CMS (`CmsController.php:21`).
- `WebStoriesController.php:58,60,98,100` usa `$activeTheme->theme` em vez de `theme_folder` — ativar o tema "news" (`theme="news"`, `theme_folder="magazine"`) montaria caminho `themes/news/partials/_header` inexistente.
- Pasta `app/Views/themes/news/` é resquício incompleto (só `_main_slider.php` + `index.html`).

---

## 6. O QUE ESTÁ SÓLIDO (verificado)

- **Site público:** 16 rotas-chave testadas por HTTP em produção — todas 200 (ou 404 correto), zero erro PHP visível. Apenas 8 respostas 5xx em 7 dias, 7 delas na janela de restart do php-fpm de 16/jul (transitório, não recorre).
- **Motor atuarial (seguro resgatável):** `php spark quotation:validate` executado — todas as âncoras PASS contra a planilha (WL mensal R$ 469,82; capital ano 4 R$ 166.953,75; reserva aos 65 R$ 694.149,34). Gate LGPD, dossiê em R$ nunca exposto ao browser, handoff via WhatsApp — design coerente e completo.
- **Diff não commitado:** `HomeController` + `PostModel` + `sitemap.xml` formam uma feature completa de redirect 301 de slugs legados (com `escape()` correto, sem injeção); `wealth/_shared_styles.php` é o redesign Nexus completo, com todas as ~20 variáveis CSS resolvendo. Falta só commitar. (`app/Views/courses/certificate.php` está limpo/commitado.)
- **Core:** zero TODO/FIXME de código no app (fora ThirdParty), zero segredos hardcoded, zero SQL injection (queries raw parametrizadas ou com `escape`/`intval`), zero debug em produção. Pipeline de conteúdo IA com máquina de estados, recuperação de runs travados e logging exemplares. Admin com `checkPermission` em todos os endpoints.
- **Courses (fundações):** jornada gamificada idempotente ponta a ponta (XP ledger com unique keys, certificados determinísticos, conquistas), catálogo/course builder, comunidade completa (feed/espaços/reações/notificações/ranking), geração de imagem IA com fallback e sem segredos, dedup de webhooks, regra de corte de acesso correta na lógica pura. Migrations 100% consistentes com o código e todas aplicadas (42/42, nenhuma pendente).
- **Wealth Manager:** token system atômico, espelhamento de leads, PDF com fallback, rotas/views 100% íntegras.
- **Infra:** `.gitignore` cobre segredos corretamente; `robots.txt`/`sitemap.xml`/`llms.txt` presentes e consistentes; 9 crons GX ativos e funcionando; módulo Hello é fixture de teste deliberado do ModuleRegistry (**não remover**).

---

## 7. EXECUÇÃO — 21/jul/2026 (mesma data da auditoria)

**Correção factual descoberta na execução:** quem serve o site é o **Apache** (httpd em 80/443), não o nginx — o nginx ativo é só o painel aaPanel. Portanto o `.htaccess` vale (AllowOverride All), e o hardening foi aplicado nele (o vhost nginx também recebeu as regras, como proteção dormente caso troquem o servidor). **Segunda descoberta:** o módulo `courses` está LIGADO na GX (por isso `/cursos` responde 200) — os fixes de webhook eram relevantes ao vivo, não dormentes.

Executado e verificado:
- ✅ CRIT-1/MED-2: assinatura de webhook validada (Stripe `Stripe-Signature` obrigatória em modo real; MP `x-signature` via `COURSES_MP_WEBHOOK_SECRET`); modo teste (sem credenciais) agora rejeita POSTs no webhook — o fluxo de teste ativa só via `confirmar()` autenticado. Webhook forjado testado ao vivo → `ignored`, 0 linhas gravadas.
- ✅ CRIT-2: auto-serviço de vínculo document↔user (`POST assinatura/vincular` + form "Já é cliente GX?" na página de assinatura) — `linkUser()` agora tem chamador; só vincula membership com `user_id` NULL.
- ✅ MED-1: estorno (`payment_refunded`) revoga o período pago via `MembershipService::refundPaid()` (comp de cliente preservado).
- ✅ MED-2b: valor/moeda do webhook conferidos contra o plano antes de ativar (`amount_mismatch` logado).
- ✅ MED-6: comando `spark courses:expire-sweep` criado (cron sugerido no INSTALL.md).
- ✅ MED-7: `base_schema.sql` atualizado (2 colunas `cover_image` + 2 linhas de migrations).
- ✅ MED-8/MED-9: `app:setup` agora roda `migrate --all` + `ActuarialSheetSeeder` automaticamente (banco default) e `--demo` semeia o CoursesDemoSeeder; dry-run validado.
- ✅ ALTO-1: 4 funções do admin corrigidas — `editFont` → view `admin/font/edit`; `sitemapPost` simplificado (caminho do cron); `setActiveLanguagePost` e `deleteSubscriberPost` implementados. Lint OK.
- ✅ ALTO-2: hardening `.htaccess` (+ vhost nginx): bloqueio de `.sh/.sql/.key/.zip/.xlsx/.md/.bak/.lock/.yml`, `github_deploy_key`, `get_page.php`, `spark`, `version.txt` e dos diretórios `app/ system/ writable/ docs/ tmp/ preview/ ui_kits/` + `uploads/*.php`. Verificado por HTTP (404 nos bloqueados, 200 nas páginas do site).
- ✅ ALTO-3: purga de logs — cron diário 04:30 UTC (`gx_logs_purge.sh`, >30 dias) + purga imediata: 2,1 GB → 1,1 GB.
- ✅ ALTO-4: `['POST','OPTIONS']` no Routes do Simulators (mata ~90% do volume de log).
- ✅ MED-16: `encryption.key` gerada no `.env` (`spark key:generate`; backup em `writable/private/`); `DashboardIntegrationModel` é retrocompatível com os valores base64 antigos.
- ✅ MED-18 + limpeza: removidos `get_page.php`, `aurum-simulador.php`, `simulador de credito`, as 2 views `simulador-aurum.php`, `create_web_stories_table.sql`, `Web.config`.
- ✅ MED-20: feed RSS ID 1 desativado (`auto_update=0`).
- ✅ Menores: 5 slugs legados adicionados ao `LEGACY_SIMULATOR_REDIRECTS` (301 verificados); label "Comunidade (em breve)" corrigida; catches silenciosos agora logam; warning CLI do `Common.php:5` corrigido; diff pendente commitado (redirect 301 + redesign wealth).
- ✅ MED-14/MED-15 fecharam sozinhos: as capas de 202606 já estão no disco (200) e o `/null` sumiu com o fix de post_url de 20/jul — 404s do log eram históricos.

Não executado (decisão de produto / Fase 6): MED-5 (gating da comunidade), MED-10 (Docker + i18n es-MX), MED-11 (prefixo de tabelas do LMS), MED-19 (LLM no Wealth Manager), MED-12/13 (crons de newsletter/feeds — confirmar se o disparo manual é intencional), MED-17 (validação MIME de upload), MED-3/MED-4 (progresso de vídeo e perfil editável).

## 8. PLANO DE AÇÃO SUGERIDO (por prioridade)

1. **Hoje (fixes de minutos):** `['POST','OPTIONS']` em `modules/Simulators/Config/Routes.php:30` (mata 90% do log); bloquear no `.htaccess` `.sh/.sql/.key/.zip/.xlsx/.md`, `github_deploy_key` e `get_page.php` (ou remover/mover); agendar purga de `writable/logs`; desativar feed RSS ID 1; commitar o diff pendente (redirect 301 + redesign wealth).
2. **Esta semana (admin quebrado):** restaurar/religar as 4 funções do painel — editor de fontes (apontar para `admin/font/edit`), view `generate_sitemap`, e remover ou reimplementar `setActiveLanguagePost`/`deleteSubscriberPost` (rota + UI).
3. **Esta semana (conteúdo):** repor/regerar as ~12 capas webp de 202606; caçar o `href="/null"` no template de post; adicionar 301s dos slugs legados; configurar `encryption.key` no `.env`.
4. **Antes de ligar o Courses em qualquer install (bloqueia Fase 6):** validar assinatura dos webhooks Stripe/MP; implementar o vínculo document↔user do client_comp (chamar `linkUser` no registro/login + campo documento no cadastro); tratar `payment_refunded` com revogação; conferir valor pago vs plano; regenerar `base_schema.sql` (ou `app:setup` rodar `migrate`); garantir `ActuarialSheetSeeder` no provisionamento; implementar `--demo`.
5. **Fase 6 propriamente:** i18n `es-MX`, Docker/compose, VPS/banco México + Educação.
