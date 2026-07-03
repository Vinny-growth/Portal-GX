# Relatório de Viabilidade — Replicar o Portal para uma Nova Marca (Filial México)

**Data:** 02/07/2026
**Autor da análise:** Claude Code (análise técnica, somente leitura — nenhum código foi alterado)
**Objetivo:** Avaliar se o projeto atual (site institucional + blog da GX Capital) está pronto para ser replicado em outro servidor/VPS, com outra marca, outro domínio, outro modelo de negócio e outro idioma (espanhol/México), e o que precisa ser ajustado para que a instalação em novos VPS seja simples.

---

## 1. Sumário executivo (TL;DR)

**Veredito:** É **tecnicamente viável**, mas o projeto **NÃO está "pronto para replicar" hoje** sem trabalho manual relevante. Ele nunca foi construído para ser multimarca/white-label — é um site sob medida da GX Capital montado em cima de um CMS licenciado.

O que temos, na prática, são **duas camadas muito diferentes**:

| Camada | O que é | Grau de reaproveitamento |
|---|---|---|
| **Motor genérico** (CMS + blog + newsletter + SEO + web stories + bio links + IA de conteúdo) | Base licenciada "Varient" + módulos | **Alto** — ~60-70% reaproveitável trocando dados/config |
| **Camada de negócio GX/Brasil** (simuladores, atuarial, câmbio/PTAX, consórcio, persona editorial, identidade visual) | Desenvolvimento sob medida | **Baixo** — hard-coded, precisa ser removida ou reconstruída |

**Três bloqueios que precisam de decisão sua ANTES de qualquer replicação:**

1. **Licença** — a base é o script comercial **Varient** (CodeCanyon/Envato). A licença é **1 por site/produto final**. Uma segunda marca = **precisa de outra licença** (não há trava técnica, mas há trava legal).
2. **Segredos vivos no `.env`** — senha do banco, chave da OpenAI, chave do Grok, token do GitHub e chaves do CRM estão em texto puro. Ao replicar, **tudo isso precisa ser trocado/rotacionado**.
3. **Não existe instalador nem dump completo do banco no repositório** — o instalador do Varient foi removido e as migrations cobrem só as adições customizadas. Replicar hoje exige **exportar o banco vivo (mysqldump)** e copiar arquivos manualmente.

**Esforço estimado (visão macro):**

- **Clonar a GX "as-is" para o México** (mesma cara, conteúdo em espanhol, simuladores desligados): **dias a ~2 semanas**.
- **Transformar em um template white-label reinstalável de verdade** (o que deixaria "fácil como no início"): **semanas de refatoração** — mas paga-se uma vez e serve para N marcas futuras.

---

## 2. O que é o projeto hoje (stack e arquitetura)

- **Base:** **Varient — News & Magazine Script v2.4.2** (autor Codingest, vendido na CodeCanyon). Confirmado em `version.txt` e `app/Config/Constants.php:103` (`VARIENT_VERSION`).
- **Framework:** CodeIgniter 4 (v4.5.7) **vendorizado** dentro de `system/` (5 MB). PHP 8.3.
- **Sem Composer:** não há `composer.json`. Todas as dependências de terceiros estão vendorizadas em `app/ThirdParty/` (87 MB — aws-sdk, openai-php, guzzle, phpmailer, mailjet, google-apiclient, intervention-image, simplepie etc.), cada uma com seu próprio `vendor/autoload.php`. **Não precisa rodar `composer install`** — basta copiar a árvore.
- **Banco:** MySQL, base `portal`, **90 tabelas / ~60 MB**.
- **Arquivos de mídia:** `uploads/` (197 MB) servidos do disco local por padrão (S3 é opcional, ligado por setting no banco).
- **Runtime gravável:** `writable/` (1.5 GB — inclui cache, logs, sessões, chaves de criptografia, backups).
- **Repositório:** `github.com/Vinny-growth/Portal-GX.git`. `.env`, `writable/` e `uploads/` são **gitignored** (ou seja, **não estão no Git** e precisam ser transferidos por fora).

**Camadas do sistema (importante para entender o que é reaproveitável):**

```
┌─────────────────────────────────────────────────────────┐
│  CAMADA GX/BRASIL (sob medida, hard-coded)              │  ← remover/reconstruir
│  simuladores · atuarial · câmbio/PTAX · consórcio ·     │
│  persona editorial IA · identidade visual "Nexus" ·     │
│  JSON-LD (fundador, imprensa, areaServed Brasil)        │
├─────────────────────────────────────────────────────────┤
│  CAMADA CONFIGURÁVEL (via admin/DB, mas com defaults BR)│  ← reconfigurar
│  categorias do blog · home/marketing (JSON no DB) ·     │
│  settings de marca · endpoints de CRM (.env)            │
├─────────────────────────────────────────────────────────┤
│  MOTOR GENÉRICO (Varient + módulos) — reaproveitável    │  ← reusar
│  blog · newsletter · web stories · bio links · SEO ·    │
│  page builder (cms_pages) · IA writer · i18n admin      │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Bloqueios críticos (decidir primeiro)

### 3.1 Licença do Varient (legal)
- O código **não tem "phone-home" nem validação de licença** — os campos `PURCHASE_CODE` e `LICENSE_KEY` no `.env` **não são lidos por nenhum código**. Roda em qualquer domínio.
- **Porém**, a licença Regular do Envato/CodeCanyon vale para **um único produto final/instalação**. Colocar uma segunda marca no ar **exige comprar outra licença** do Varient (~US$ 40-60). Barato, mas precisa ser feito para ficar em conformidade.
- Observação: o projeto já foi **muito customizado** para além do Varient de fábrica, o que reforça manter tudo licenciado corretamente.

### 3.2 Segredos vivos no `.env` (segurança)
O `.env` atual contém, em texto puro (todos precisam ser **substituídos e os antigos rotacionados** na nova marca):
- Senha do banco (`database.default.password`)
- `OPENAI_API_KEY` (chave de produção)
- `GROK_API_KEY`
- `GITHUB_PUSH_TOKEN` (Personal Access Token do GitHub)
- `CRM_LEAD_API_KEY`, `CRM_NEWSLETTER_ANON_KEY` (Supabase do CRM)
- Caminho do JSON de service account do Google (`GSC_SERVICE_ACCOUNT_JSON`)

> **Recomendação:** como o `.env` circula por transferência manual, considere **rotacionar essas chaves** de qualquer forma (algumas podem já estar comprometidas por estarem em backups/históricos).

### 3.3 Provisionamento do banco (operacional)
- **Não há instalador (wizard) do Varient** — foi removido; o app já está instalado.
- As **33 migrations** (`app/Database/Migrations/`, de 2025-01 a 2026-06) cobrem **apenas as adições customizadas** (dashboard, bio links, web stories, wealth, content-AI, newsletter, atuarial, SEO). **Nenhuma cria as tabelas base** (`posts`, `users`, `categories`, `languages`, `settings`, `ci_sessions`...).
- **Não existe seeder** que crie usuário admin, settings padrão, idioma padrão ou categorias.
- **Conclusão:** para replicar hoje é **obrigatório exportar o banco vivo** (`mysqldump portal`) e importar no novo servidor. `php spark migrate` sozinho **não monta** um banco funcional.

---

## 4. O que HOJE é configurável vs. hard-coded (mapa de rebranding)

### 4.1 Domínio / URL base — **FÁCIL**
- `app/Config/App.php:19` tem `baseURL = 'https://gx.capital/'`, mas o `.env` (`app.baseURL`) **sobrescreve** em runtime. Trocar o `.env` resolve a maior parte (todo `base_url()`, canônicos, JSON-LD, sitemap derivam daí).
- Ajustar também no `.env`: `SIMULATOR_ALLOWED_ORIGINS`, `GSC_SITE_URL`, `SEO_TARGET_DOMAIN`, `cookie.prefix`.

### 4.2 Marca textual, logo, contato, SEO, analytics — **FÁCIL (via admin/DB)**
Quase toda a identidade "de texto" é **dirigida pelo banco** (tabelas `general_settings` — global — e `settings` — por idioma), editável pelo painel admin **sem mexer em código**:
- Nome do site (`application_name`), logo/logo rodapé/logo e-mail, favicon, ícone PWA
- Contato (e-mail, endereço, telefone), copyright, texto "sobre" do rodapé
- Defaults de SEO (title, description, keywords), Google Analytics, códigos custom de header/footer, Meta Pixel/CAPI
- Redes sociais (todas via admin — **não hard-coded**)
- Seleção de tema e `theme_color`

> Ou seja: **trocar settings + subir logo/favicon novos re-marca todo o blog/CMS/tema clássico com zero alteração de código.** Essa é a parte boa.

### 4.3 Identidade visual (cores/design) — **DIFÍCIL (em CSS/views)**
A paleta "Nexus" (navy `#0c3163` + champagne/dourado) **não está no banco** — está embutida em:
- `colors_and_type.css` (folha-fonte de tokens `--gx-*`)
- `app/Views/marketing/_shared_styles.php` (~599 linhas)
- `app/Views/wealth/_shared_styles.php` (~136 linhas)
- `<meta name="theme-color">` fixo em vários templates; `msapplication-TileColor="#2F3BA2"` legado (azul) nos headers de tema
- `ui_kits/nexus/` (design system de referência, React) e `GX Capital Design System.zip`

Reskin de cores = reescrever esses arquivos + trocar assets de logo. A classe-prefixo `gx-` é onipresente (cosmético, não obrigatório mudar).

### 4.4 Strings de marca cravadas no código — **DIFÍCIL (espalhado)**
"GX Capital" / "gx.capital" / identidade do fundador aparecem **hard-coded** nos pontos sob medida:
- `app/Controllers/HomeController.php` — **~49 ocorrências** (títulos, descrições SEO, mensagens de WhatsApp, respostas de FAQ). O maior ofensor.
- `app/Views/marketing/playbook_*.php` (importação/exportação) — nome da marca, **número de WhatsApp fixo `555120421991`**, telefone `+55 (51) 2042·1991`, e-mail `contato@gx.capital`, logo `assets/logo-icon.png` fixo.
- `app/Views/common/_json_ld.php` — **identidade global em toda página**: `name/legalName "GX Capital"`, nó Person do fundador **"Vinicius Teixeira"** (`@id #person-vinicius-teixeira`, "CEO & Founder"), `areaServed` = **Brasil**, e **menções de imprensa hard-coded** (Valor Econômico / O Globo, com URLs e datas). Tudo específico da GX — precisa remover/substituir.
- `app/Config/SeoFaq.php` — base de FAQ (PTAX, 4.131, BNDES, FIDC...) por categoria.
- `app/Models/ContentAISettingsModel.php` — prompts da IA com persona "editor-chefe da GX Capital / portal brasileiro".
- Arquivos estáticos na raiz: `robots.txt` (linha do Sitemap), `llms.txt` (14 refs: marca, "Porto Alegre/RS", telefone, e-mail, URLs), `sitemap.xml` (895 URLs absolutas — regeneráveis via `baseURL`).
- `app/Libraries/MarketingHomeDefaults.php` / `MarketingSimulatorsDefaults.php` / `MarketingConsorcioDefaults.php` — copy padrão (fallback) em PT-BR com produtos GX.

---

## 5. Idioma: Português → Espanhol (México)

**Existem DOIS sistemas de idioma desconexos** — este é o ponto mais delicado da migração de idioma:

### Sistema 1 — i18n dirigido por banco (admin + blog clássico) — **funciona**
- Tabelas `languages` e `language_translations`; helper `trans($label)` (`app/Common.php:426`).
- Dá para **criar o espanhol pelo próprio admin** (`LanguageModel::addLanguage()` clona traduções, settings, páginas e widgets), traduzir as strings e definir como idioma padrão. O painel admin e o blog clássico ficam traduzíveis **só com entrada de dados**.
- O `LeadPhoneFormatter` **já tem o México** cadastrado (código 52); só falta trocar o default `BR` → `MX` (`app/Libraries/LeadPhoneFormatter.php:5`).

### Sistema 2 — camada GX (marketing/simuladores/wealth/newsletter) — **100% PT-BR hard-coded**
- As views de `app/Views/marketing/*` têm **zero `trans()`** — todo texto é literal em português no HTML.
- Defaults em PHP (`MarketingHomeDefaults.php` etc.) são literais PT-BR.
- Moeda **cravada em BRL/R$** em vários lugares: simuladores (`consorcio.php`, `meta_currency=BRL`, `Intl.NumberFormat('pt-BR', {currency:'BRL'})`), `wealth/results.php`, `wealth/pdf_template.php` (`number_format($x,2,',','.')`).
- **Limitação estrutural:** `cms_pages` **não tem coluna de idioma** — a config de home/marketing é um **JSON de idioma único**. O blog (posts/categorias) tem `lang_id` e é multilíngue de verdade; o marketing não.

> **Consequência:** o caminho realista para o México é um **deploy separado, todo em espanhol**, e não um site bilíngue com toggle PT/ES no mesmo banco.

### IA de conteúdo em espanhol
- `app/Config/AIWriter.php` já suporta `es` (genérico). Mas o **pipeline automático da GX** tem PT-BR/Brasil cravado em código (`ContentAIService.php`, `ContentAIController.php`) e os defaults de persona/keywords são do **mercado brasileiro** (Copom, Selic, PTAX, Bacen, B3). Para o México, reescrever para Banxico/CETES/BMV/TIIE/CNBV — **trabalho conceitual, não só tradução**.

---

## 6. Produtos e lógica Brasil-específica (o que NÃO transfere)

O México terá **outros produtos**. Hoje **não há camada de abstração de produto/simulador** — cada produto é uma página sob medida. "Produtos diferentes" = **escrever páginas novas**, não reconfigurar.

**Reaproveitável como está (motor genérico):**
Blog/posts, categorias (DB editável), tags, RSS, sitemap, newsletter completa, web stories, bio links, ferramentas de SEO (GSC/IndexNow), IA writer base, page builder genérico (`cms_pages` + rota `p/(:any)`), encanamento de CRM (endpoints por `.env`), auth, galeria, dashboard.

**Configurável (funciona, mas vem com defaults brasileiros a sobrescrever):**
Categorias do blog, home/marketing (JSON no `marketing_settings` via admin), endpoints de CRM/newsletter (`.env`), URL/idioma.

**Hard-wired ao Brasil — remover ou reconstruir para o México:**

| Item | Por que não transfere | Onde |
|---|---|---|
| Simulador Seguro de Vida Resgatável (atuarial) | Replica a planilha `Dinamica seg resgatavel.xlsx`; IOF 0.38%, IPCA; dados em `actuarial_rates`/`reserve_factors` | `QuotationEngine.php`, seeders atuariais |
| QuotationGate | Alíquotas de **ITCMD dos 27 estados** + custo de inventário (direito sucessório BR) | `app/Libraries/QuotationGate.php:29-54` |
| Consórcio | Produto exclusivamente brasileiro; página de 1.856 linhas | `app/Views/simulators/consorcio.php` |
| Câmbio / FX (4131, ACC/ACE, PTAX) | Atrelado a USD/BRL, Resolução 4.131 | `HomeController`, playbooks, `MarketingSimulatorsDefaults` |
| Persona editorial + FAQ SEO da IA | Copom/Selic/Bacen/BNDES/FIDC; % de mix de pauta por **IDs fixos de categoria (6,8,7,13)** | `ContentAISettingsModel.php`, `SeoFaq.php` |

**CRM (Supabase):** o mecanismo é **genérico e por `.env`** — troca-se endpoint + chave + novo projeto Supabase. Só há 2 strings cosméticas ("site-gx-php", "Site GX Capital") a ajustar em `CrmLeadClient.php`.

---

## 7. Como replicar HOJE (runbook manual + armadilhas)

Se a decisão for **clonar a GX para o México agora**, este é o processo real (não há atalho automatizado hoje):

**Artefatos a obter do servidor atual (NÃO estão no Git):**
1. **Dump completo** do banco `portal` (`mysqldump`) — 90 tabelas, obrigatório (migrations não montam a base).
2. Diretório **`uploads/`** (197 MB) via rsync — mídia local (a menos que S3 esteja ligado).
3. **`writable/keys/`** (chave de criptografia — manter continuidade de dados/sessões) e **`writable/private/gsc/*.json`** (service account Google).
4. **`.env`** (contém os segredos; não versionado).
5. **Crontab** do servidor (os agendamentos `php spark ...` e as 2 URLs de cron HTTP não estão no repo).

**Passos:**
1. Copiar a árvore do projeto (inclui `system/` + `app/ThirdParty/`). **Sem `composer install`.**
2. Criar banco/usuário no MySQL e importar o dump.
3. `php spark migrate` (top-up idempotente) → aplicar `app/Database/SQL/wealth_manager_schema.sql` se as tabelas `wm_*` não vierem no dump → `php spark db:seed ActuarialSheetSeeder` (só se for manter o simulador atuarial).
4. PHP ≥ 8.1 com extensões: **mysqli, gd (+imagick opcional), curl, intl, mbstring, json, libxml, openssl, fileinfo, zip**.
5. Ajustar `.env`/`App.php`: `app.baseURL`, credenciais de banco, `SIMULATOR_ALLOWED_ORIGINS`, `GSC_SITE_URL`, `SEO_TARGET_DOMAIN`, caminho do `GSC_SERVICE_ACCOUNT_JSON`, `cookie.prefix`, `CI_ENVIRONMENT=production`, e **todas as chaves de API novas**.
6. `chmod -R 775 writable/ uploads/` (dono = usuário do webserver).
7. Webserver: Apache com `mod_rewrite` (usa `.htaccess`) ou equivalente nginx apontando para `index.php`.
8. Recriar o agendamento de cron (comandos spark + 2 URLs HTTP).
9. Re-marca: novos logos/favicon, settings no admin, e as alterações de design GX→nova marca.

**Armadilhas conhecidas (landmines):**
- **`.user.ini:1`** → `open_basedir=/www/wwwroot/gx.capital/:/tmp/` — **caminho absoluto fixo**; se não ajustar para o novo docroot, o site dá **erro 500** inteiro.
- `GSC_SERVICE_ACCOUNT_JSON` — caminho absoluto do servidor atual no `.env`.
- `tmp/update_page_csrf_fix.php:9` — script descartável com caminho fixo (ignorar/apagar).
- `baseURL` hard-coded em `App.php` além do `.env`.
- Sessão usa **DatabaseHandler** → precisa da tabela `ci_sessions` (vem no dump).
- `sitemap.xml` fica **desatualizado** até rodar o gerador de sitemap com a nova `baseURL`.

---

## 8. O que ajustar no projeto para "ficar fácil de instalar" (recomendações)

Para que futuras instalações (México e além) sejam **simples como foi no início**, sugiro tratar o projeto como **template white-label**. Recomendações, em ordem de retorno:

**P0 — Habilitadores de instalação (destrava tudo)**
1. **Criar um seeder/instalador de base** (`php spark app:setup`) que provisiona schema base + usuário admin + settings padrão + idioma padrão + categorias — eliminando a dependência do dump vivo. Alternativamente, versionar um **dump "esqueleto"** (schema + dados mínimos, sem conteúdo GX) em `app/Database/SQL/`.
2. **Externalizar 100% dos segredos** e commitar um **`.env.example`** completo e documentado (hoje só existe um `.env.example.webstories` parcial). Rotacionar as chaves atuais.
3. **Remover caminhos absolutos** — `.user.ini` (`open_basedir`) e `GSC_SERVICE_ACCOUNT_JSON` devem ser relativos ao projeto ou derivados de `ROOTPATH`.

**P1 — Desacoplar a marca do código**
4. **Centralizar a identidade em um "brand config"** (DB ou `app/Config/Brand.php`): nome, cores, fundador, dados de imprensa, `areaServed`, contatos. Tirar essas strings de `HomeController.php`, `_json_ld.php`, playbooks, `llms.txt`.
5. **Tokenizar as cores** — um único ponto (`colors_and_type.css`/variáveis) que os `_shared_styles.php` consomem, para reskin trocando só os tokens.

**P2 — Internacionalização real da camada GX**
6. **Passar o marketing/simuladores/wealth por `trans()`/arquivos de idioma** (ou aceitar tradução por deploy). **Adicionar `lang_id` a `cms_pages`** se um dia quiser bilíngue no mesmo banco.
7. **Abstrair moeda/locale** (BRL/`pt-BR` → parametrizável, ex.: MXN/`es-MX`).

**P3 — Modularizar o negócio**
8. **Tornar os simuladores módulos ligáveis/desligáveis por marca** (flag em config) — o México começa sem eles e ganha os seus próprios quando existirem.
9. **Parametrizar o pipeline de IA** — mover verticais, keywords, mix de pauta, persona e FAQ para **config por mercado** e **remover o acoplamento a IDs fixos de categoria (6,8,7,13)**.

**P4 — Reprodutibilidade**
10. **Docker/compose** (PHP+MySQL+extensões) e um **runbook versionado em `docs/`** para instalação padronizada em qualquer VPS.

---

## 9. Estimativa de esforço por cenário

| Cenário | O que entrega | Esforço aproximado |
|---|---|---|
| **A. Clone rápido GX→MX** | Mesma cara, conteúdo/UI em espanhol, simuladores desligados, CRM novo, sem os itens P0-P4 | Dias a ~2 semanas (muito trabalho manual e frágil) |
| **B. Clone + rebrand completo** | A + nova identidade visual, JSON-LD/marca limpos, IA reconfigurada p/ México | ~3-5 semanas |
| **C. White-label reinstalável (P0-P4)** | Investir na base para que México **e futuras marcas** instalem em horas | ~4-8 semanas, mas amortizado |

> Recomendação pessoal: se o México é o **primeiro de vários**, vale fazer o **P0 + P1 primeiro** (destrava instalação e desacopla marca) e só depois clonar — evita retrabalho. Se for **caso único e urgente**, o Cenário A resolve, aceitando a dívida técnica.

---

## 10. Perguntas em aberto para você decidir

1. **Licença Varient:** vamos comprar uma nova licença para a marca do México? (recomendado)
2. **Simuladores:** o México terá **produtos próprios** (quais?) — construímos simuladores novos, ou o site nasce só com institucional + blog?
3. **Bilíngue vs. deploy separado:** confirmado que será **instalação separada 100% em espanhol** (recomendado), certo?
4. **Estratégia:** Cenário **A** (rápido, dívida técnica) ou investir no **white-label (C)** já que pode haver mais filiais?
5. **CRM:** o México usa o **mesmo Supabase** (outra "origem") ou um projeto/CRM **separado**?
6. **Infra:** VPS novo com mesma stack (Apache + PHP 8.3 + MySQL) ou é oportunidade para **Docker**?

---

### Anexo — Referências de arquivo mais importantes
- Base/versão: `version.txt`, `app/Config/Constants.php:103`, `system/CodeIgniter.php` (CI 4.5.7)
- URL/domínio: `app/Config/App.php:19` + `.env` (`app.baseURL`)
- Settings de marca: `app/Models/SettingsModel.php`, `app/Views/admin/general_settings.php`
- Identidade cravada: `app/Controllers/HomeController.php` (~49), `app/Views/common/_json_ld.php`, `app/Views/marketing/playbook_*.php`, `app/Config/SeoFaq.php`, `app/Models/ContentAISettingsModel.php`
- Cores/design: `colors_and_type.css`, `app/Views/marketing/_shared_styles.php`, `app/Views/wealth/_shared_styles.php`
- Idioma: `app/Models/LanguageModel.php`, `app/Common.php:426` (`trans()`), `app/Config/App.php:96-123`, `app/Models/CmsPageModel.php` (sem `lang_id`)
- Brasil-específico: `app/Libraries/QuotationEngine.php`, `app/Libraries/QuotationGate.php:29-54`, `app/Views/simulators/consorcio.php`
- Banco/instalação: `app/Database/Migrations/` (33), `app/Database/Seeds/`, `app/Database/SQL/wealth_manager_schema.sql`, `.user.ini:1`
- Armadilha de caminho: `.user.ini:1` (`open_basedir`), `.env` (`GSC_SERVICE_ACCOUNT_JSON`)
```
