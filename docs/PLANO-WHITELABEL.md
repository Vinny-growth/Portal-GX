# Plano de Plataforma White-Label — Roadmap de Refatoração

**Data:** 02/07/2026
**Status:** Proposta / plano de execução (para aprovação)
**Documento irmão:** [`RELATORIO-REPLICACAO-NOVA-MARCA.md`](./RELATORIO-REPLICACAO-NOVA-MARCA.md) (análise técnica de base)
**Autor:** Claude Code (planejamento técnico)

> Este documento consolida a decisão estratégica e o roadmap de execução para transformar o portal atual da GX Capital em uma **plataforma white-label multi-instância**, mantendo a GX Brasil em produção durante todo o processo.

---

## 1. Contexto e decisão

Avaliamos três caminhos para expandir o portal para novas marcas (GX México e uma futura plataforma de educação financeira):

1. **Replicar "as-is"** (clonar e adaptar manualmente por marca) — frágil, retrabalho a cada marca.
2. **Reconstruir do zero** (Lovable + Supabase) — jogaria fora o motor mais caro (IA de conteúdo, newsletter, SEO) e causaria regressão de SEO (SPA cliente vs. SSR).
3. **Refatorar este projeto em uma plataforma white-label** — mantém todo o motor provado e o transforma em produto reinstalável.

**Decisão:** seguir o caminho **3**. O proprietário do Varient (CMS base) **liberou a GX de novas licenças**, pois o projeto hoje usa apenas uma fração ínfima da base original — o que **remove o único bloqueio legal** e torna a refatoração a opção de melhor custo-benefício.

**Modelo de execução:** refatorar **de forma incremental, dentro deste mesmo repositório**, mantendo a GX Brasil funcionando a cada passo. Quando a base estiver "redonda" (brand config + sistema de módulos + i18n prontos), criamos o **wizard de instalação** e só então subimos as novas instâncias.

---

## 2. Racional (por que este é o caminho mais inteligente)

### 2.1 O valor está no motor, não nas páginas
O que dá valor ao projeto não são as telas — é o **motor genérico** já construído, debugado e endurecido em produção:
- Pipeline de IA de conteúdo (trends + Grok/X-Pulse + geração com imagem + persona editorial + calendário).
- Automação de newsletter com CRM e tracking.
- Ferramentas de SEO (GSC, rank tracking, IndexNow, JSON-LD/GEO recém-implementado).
- Web stories, bio links, page builder, i18n do admin.

Isso é ~60-70% do trabalho acumulado. Reconstruir do zero (mesmo com IA) significaria reescrever esses 60% difíceis em outra linguagem, **perdendo o hardening de produção** — meses de trabalho e re-debug.

### 2.2 A parte "difícil" você reconstrói de qualquer forma
A camada Brasil-específica (simuladores, atuarial, câmbio) **será reconstruída no México de qualquer jeito** (outros produtos, outro idioma). Então a comparação real é só sobre o **motor genérico** — que é caro de recriar e barato de carregar adiante.

### 2.3 SEO é o core do negócio → SSR, não SPA
O negócio vive de **busca orgânica** e você acabou de investir em GEO/SEO, JSON-LD e sitemap. O app atual renderiza **HTML no servidor (SSR)** — ideal para SEO. Migrar para uma SPA cliente (Lovable/Vite) prejudicaria exatamente o que sustenta o tráfego. Manter o stack atual **preserva o ativo de SEO**.

### 2.4 Velocidade acumulada
A equipe (com IA) já produz features sofisticadas neste código com alta velocidade. Recomeçar zera esse contexto. Refatorar preserva a produtividade.

### 2.5 De custo a produto
Transformar o portal em plataforma white-label muda a natureza do investimento: em vez de "custo de replicar um site", vira **um produto interno reutilizável** que serve GX Brasil, GX México, a plataforma de educação e **qualquer marca futura** — instalável em horas.

---

## 3. Visão de arquitetura alvo

### 3.1 Multi-instância (NÃO multi-tenant)

**Um único código-base (esta plataforma) deployado N vezes.** Cada deploy tem seu próprio banco, `.env`, config de marca e conjunto de módulos ativos.

| Install | Domínio/VPS | Idioma | Módulos ativos |
|---|---|---|---|
| **#1 GX Brasil** (atual, produção) | gx.capital | PT-BR | institucional + blog + simuladores + wealth + newsletter + SEO |
| **#2 GX México** | novo VPS | ES-MX | institucional + blog + newsletter + SEO (+ simuladores próprios quando existirem) |
| **#3 Educação Financeira** | novo VPS | PT/ES | institucional + blog + **cursos + jornada + comunidade** |

> **Decisão de arquitetura:** manter **multi-instância**, com isolamento total por marca (banco, SEO, dados, idioma). **NÃO** evoluir para multi-tenant "de verdade" (um app servindo todas as marcas com `tenant_id`) — para este caso, multi-tenant só adiciona complexidade, risco de vazamento entre marcas e piora o isolamento de SEO. Multi-instância é mais simples, mais seguro e é o padrão correto aqui.

**Princípio-chave:** a GX Brasil passa a ser o **install #1 da própria plataforma** — nunca um fork. Toda refatoração beneficia a GX e evita divergência de código.

### 3.2 Camadas do sistema (estado alvo)

```
┌──────────────────────────────────────────────────────────────┐
│  MÓDULOS PLUGÁVEIS (ligados/desligados por install via flag)  │
│  Simuladores · Wealth · Cursos+Jornada+Comunidade · (futuros) │
├──────────────────────────────────────────────────────────────┤
│  CAMADA DE MARCA (brand config — fonte única de verdade)      │
│  nome · cores/tokens · fundador · contatos · JSON-LD · locale │
├──────────────────────────────────────────────────────────────┤
│  MOTOR GENÉRICO (núcleo reaproveitável)                        │
│  blog · newsletter · web stories · bio links · SEO · IA ·     │
│  page builder · i18n · auth · dashboard · admin                │
├──────────────────────────────────────────────────────────────┤
│  PLATAFORMA / INSTALAÇÃO                                        │
│  CI4 · brand_settings · modules registry · wizard · .env       │
└──────────────────────────────────────────────────────────────┘
```

### 3.3 As três peças de espinha dorsal

O que hoje **não existe** e precisa ser construído **antes** de refatorar muita coisa:

1. **Brand config — fonte única de verdade.** Tudo que hoje está hardcoded (nome, cores, fundador, contatos, JSON-LD, imprensa, idioma, moeda) passa a ler daqui.
2. **Registro de módulos + feature flags.** Cada módulo declara suas rotas/menu/admin/tabelas/permissões e só "liga" se habilitado no install.
3. **Conteúdo e navegação dirigidos por dados.** Menus, verticais, home e `cms_pages` (com `lang_id`) deixam de depender de IDs fixos ou strings cravadas.

Com a espinha pronta, o resto (de-hardcode de marca, i18n, cores) vira preenchimento mecânico e seguro.

---

## 4. Desenho técnico — Sistema de módulos

### 4.1 Base: CI4 Code Modules
O CodeIgniter 4 suporta módulos nativos via namespaces PSR-4 — **sem precisar de Composer** (registro manual em `app/Config/Autoload.php` `$psr4`). Cada módulo é auto-contido:

```
modules/
  Courses/
    Config/
      module.php        ← manifesto do módulo (declaração)
      Routes.php        ← rotas (auto-descobertas pelo CI4)
    Controllers/
    Models/
    Views/
    Database/Migrations/
    Language/{pt,es}/Courses.php
    Assets/
```

### 4.2 Contrato / manifesto de módulo (`module.php`)
Cada módulo declara de forma padronizada:

```php
return [
    'key'            => 'courses',
    'name'           => 'Cursos & Comunidade',
    'version'        => '1.0.0',
    'requires'       => ['blog'],            // dependências
    'menu'           => [ /* itens de menu público */ ],
    'admin_nav'      => [ /* itens no painel admin */ ],
    'permissions'    => ['courses.manage', 'courses.view'],
    'settings'       => [ /* defaults gravados no install */ ],
    'migrations_ns'  => 'Modules\Courses',
    'enabled_default'=> false,               // desligado por padrão
];
```

### 4.3 Feature flags (quais módulos "ligam" por install)
- Uma tabela `modules` (ou `brand_settings.enabled_modules` JSON) guarda o estado por install.
- **Gating em 3 pontos:**
  - **Rotas:** o `Routes.php` do módulo só registra se o módulo estiver habilitado.
  - **Menu/Admin nav:** o construtor de menu só exibe itens de módulos habilitados.
  - **Migrations:** rodam por namespace de módulo (ou o módulo fica inerte se desligado).
- Um serviço central `Modules::enabled('courses')` responde em qualquer lugar do código.

### 4.4 Retrofit dos módulos existentes
Os simuladores e o wealth (hoje páginas cravadas) são **migrados para o mesmo contrato** (`modules/Simulators`, `modules/Wealth`). Assim, dois+ módulos reais validam a abstração, e a GX México/Educação nascem só com os módulos que fazem sentido.

---

## 5. Desenho técnico — Brand config

### 5.1 Fonte de verdade
Uma tabela **`brand_settings`** (linha única por install) + um serviço acessor `Config\Brand` (com helper `brand('display_name')`), carregado no boot e cacheado. Conteúdo:

| Grupo | Campos |
|---|---|
| Identidade | `legal_name`, `display_name`, `tagline`, `founder_name`, `founder_title`, `founder_schema_id` |
| Contato | `email`, `phone`, `whatsapp`, `address` |
| Social | `instagram`, `linkedin`, `facebook`, ... |
| SEO/JSON-LD | `org_description`, `area_served`, `press_mentions` (JSON), `og_image` |
| Locale | `locale` (ex.: `es-MX`), `currency` (ex.: `MXN`), `timezone` |
| Design tokens | `color_primary`, `color_gold`, `color_secondary`, `logo_*`, `favicon` |

Seed inicial = **valores atuais da GX** (para produção não mudar nada).

### 5.2 Design tokens (cores)
As cores da marca deixam de ficar cravadas em `colors_and_type.css` / `_shared_styles.php`. Passam a ser **CSS variables geradas do `brand_settings`** (um partial `brand.css` renderizado antes dos estilos), e os shared styles referenciam `var(--brand-primary)` etc. Reskin de marca = editar tokens no admin, sem tocar em CSS.

### 5.3 De-hardcode de identidade
Todos os pontos cravados hoje passam a ler do brand config:
- `HomeController.php` (~49 ocorrências), `_json_ld.php` (Org, fundador, imprensa, `areaServed`), playbooks (WhatsApp/e-mail/telefone), `SeoFaq.php`, `ContentAISettingsModel.php` (persona), `robots.txt`/`llms.txt`/`sitemap.xml`.

---

## 6. Desenho técnico — i18n

**Estratégia recomendada: um idioma por install** (cada deploy monolíngue). Simples, isola SEO e evita a complexidade de bilíngue-no-mesmo-site.

- **Strings de UI dos módulos/marketing** (hoje 100% cravadas em PT-BR nas views de `marketing/*`, `simulators/*`, `wealth/*`, `newsletter/*`): migram para **arquivos de idioma CI4** (`lang()` / `Modules\X\Language\{pt,es}`). São strings de desenvolvedor.
- **Admin e blog clássico:** já têm i18n dirigido por banco (`trans()` / `language_translations`) — mantém.
- **Conteúdo** (posts, páginas, cursos): é dado → fica no idioma do install.
- **Estrutural:** adicionar `lang_id` a `cms_pages` (hoje é JSON de idioma único), para o marketing/home ter dimensão de idioma quando necessário.
- **Formatação:** moeda/número/data passam a derivar do `brand_settings.locale/currency` (fim do `R$`/`pt-BR`/`BRL` cravado nos simuladores e no `wealth`). O `LeadPhoneFormatter` já suporta México — só trocar o default por install.

---

## 7. Módulo de Cursos + Jornada + Comunidade (produto completo)

Este módulo é a **"forcing function"** da arquitetura: ao construí-lo como primeiro cidadão de primeira classe do sistema de módulos, validamos o contrato de módulo com um caso real (e depois encaixamos simuladores/wealth no mesmo padrão).

> **Escopo confirmado (jul/2026):** módulo **robusto e completo**, com referência na **Comunidade AUVP** (`comunidade.auvp.com.br`) — comunidade social completa + área de cursos **gamificada com visual estilo Netflix** em formato de **jornada**. **Não é "leve".** É um **produto de membership + LMS gamificado + comunidade social**, com peso comparável a "Circle.so + um LMS gamificado". Deve ser tratado como **iniciativa própria** dentro do programa, com seu próprio orçamento e prazo.

### 7.1 Comunidade (estilo AUVP — completa)
- **Feed social:** posts, **espaços/categorias** temáticos, comentários (aninhados), reações/curtidas.
- **Perfis de membro:** bio, avatar, histórico de atividade, conquistas exibidas.
- **Gamificação:** pontos/XP, **níveis**, **ranking/leaderboard**, **badges/conquistas**, streaks.
- **Grupos/espaços**, eventos/lives, **notificações** (in-app + e-mail), busca.
- **Moderação (admin):** fixar, destacar, remover, banir, papéis/permissões.

### 7.2 Cursos (visual Netflix + jornada gamificada)
- **UI estilo Netflix:** hero, **carrosséis por categoria**, capas grandes, "continuar de onde parou", hover/preview.
- **Jornada/trilha gamificada:** trilha visual com progresso, **desbloqueio sequencial** (drip), **XP por aula/curso**, marcos/conquistas.
- **Estrutura:** cursos → módulos/seções → aulas (vídeo/texto/quiz) + materiais/anexos.
- **Progresso & conclusão:** player com marcação de conclusão, % de progresso, **certificados**.

### 7.3 Modelo de acesso e liberação de conteúdo (confirmado — acesso único estilo AUVP)
**Modelo de negócio simplificado (confirmado jul/2026):** **um único acesso pago desbloqueia TUDO** (comunidade + todos os cursos), à la AUVP — **sem venda por curso, sem carrinho, sem catálogo de preços**. Convivem dois formatos de liberação:
1. **Conteúdo gratuito por nível de acesso (grant manual):** **níveis de acesso** (ex.: Free, Nível 1) atribuídos **manualmente pelo admin**; conteúdo marcado com o nível mínimo exigido.
2. **Conteúdo pago = membership único (anual):** **um** plano/membership que, ativo, libera todo o conteúdo pago. Cobrança **anual recorrente** (como a AUVP).

**Desenho (bem enxuto graças ao acesso único):** a decisão central `canAccess(user, content)` resolve-se em três checagens — *(conteúdo público)* OU *(usuário tem o nível manual exigido)* OU *(usuário tem membership ativo)*. Não há produtos/preços por curso — só **um** produto (o membership). Isso reduz drasticamente o modelo de dados e a lógica de entitlements.

**Fontes do membership (regra de negócio confirmada):** o membership ativo pode vir de **duas fontes** — e para o `canAccess()` tanto faz qual (ativo é ativo):
- **`paid`** — assinatura **anual recorrente** (12 meses) paga via gateway.
- **`client_comp`** — **cortesia enquanto o aluno for cliente ativo da GX** (consultoria/assessoria): vira cliente → membership ativo **sem cobrança** enquanto durar o vínculo; na renovação anual, cliente ativo = cobrança **dispensada**.

**Regra de corte de acesso ao encerrar o vínculo de cliente GX (confirmada):** ao cancelar o serviço, o CRM dispara um **webhook** → a plataforma decide o corte com base no período **pago**:
- **Ainda dentro dos 12 meses pagos** (`now < paid_until`): mantém acesso **até o fim do período pago** (honra a compra).
- **12 meses pagos já encerrados** (estava só no `client_comp`, ou nunca pagou): **30 dias de carência** a partir do cancelamento, depois corta.
- **Fim do período de 12 meses** sem comp/renovação: **corta imediatamente** (sem carência).

**Implicação arquitetural (importante):** a Educação é um **install separado** (VPS/banco próprios) e **não** consulta o banco da GX diretamente. A integração é **event-driven**: o **CRM da GX dispara webhooks** para a plataforma nos eventos de cliente (virou cliente → concede `client_comp`; cancelou → aplica a regra de corte acima). Um **sync periódico de reconciliação** (reusando `CrmSyncService`) cobre eventos perdidos.
- **Chave de identidade = número de identidade nacional** (**CPF** no Brasil; equivalente no México — **CURP/RFC**, a confirmar), **não o e-mail** — é único por pessoa (e-mail não é). Requer **coletar/validar o documento no cadastro** e o CRM expor esse campo.
- *Atenção:* CPF/CURP é **PII sensível** (LGPD no BR, LFPDPPP no MX) — armazenar/transmitir com cuidado (minimização, criptografia em repouso).

**Sub-módulo de pagamento plugável (confirmado — MercadoPago + Stripe):** uma **interface `PaymentGateway`** com **dois adaptadores**: **MercadoPago** (Brasil **e** México — PIX + métodos locais) e **Stripe** (fallback global, qualquer país). O install escolhe o gateway por config (BR/MX → MercadoPago; demais países → Stripe). Fluxo único, independente do gateway: checkout do plano → **webhook** do gateway → **normaliza** para um evento interno padrão → **ativa/renova/cancela** o membership. *Sub-decisão restante (§11):* membership **recorrente (assinatura anual, como AUVP)** ou **vitalício (pagamento único)**.

### 7.4 Área do aluno & Admin
- **Aluno:** meus cursos, continuar, dashboard de progresso/gamificação, certificados, **minhas compras/assinatura**, perfil público.
- **Admin:** construtor de curso, **gestão de níveis/entitlements**, gestão de alunos, **moderação da comunidade**, analytics, configuração de gamificação.

### 7.5 Esboço de modelo de dados (alto nível)
- **LMS:** `courses`, `course_sections`, `lessons`, `lesson_progress`, `certificates`.
- **Acesso/pagamento (enxuto — acesso único):** `access_levels`, `user_access_levels` (grant manual), `memberships` (status / **fonte** `paid|client_comp|manual` / `paid_until` / `access_until`), `payments`, `payment_events` (webhooks de pagamento) + `crm_client_events` (webhooks de cliente do CRM), identidade casada por **documento nacional (CPF/CURP)**. *Sem tabelas de produtos/preços por curso.*
- **Comunidade:** `spaces`, `posts`, `comments`, `reactions`, `member_profiles`.
- **Gamificação:** `points_ledger`, `badges`, `user_badges`, `leaderboards`, `notifications`.

### 7.6 Reaproveitamento
Auth, uploads/mídia, e-mail/newsletter, admin, SEO e blog já existem — o módulo se apoia neles em vez de reinventar. **Hospedagem de vídeo** a definir (§11): YouTube/Vimeo não-listado (barato) vs. Bunny/Mux (player próprio, DRM leve, analytics).

---

## 8. Roadmap detalhado por fases

> **Princípio transversal:** cada fase é **behavior-preserving para a GX Brasil**. Introduzimos brand config com defaults GX e flags todos ligados para a GX, de modo que produção não muda enquanto extraímos o hardcode. Trabalho em branches `feat/`, como já é feito.

### Fase 0 — Espinha dorsal (habilitadores)
> 📄 **Detalhamento executável:** [`FASE-0-ESPINHA.md`](./FASE-0-ESPINHA.md) (tarefas, arquivos, DoD por workstream).
- **Objetivo:** criar as fundações sem alterar o comportamento da GX.
- **Escopo:**
  - Tabela `brand_settings` + serviço `Config\Brand` + helper `brand()`, seed com valores GX.
  - Registro de módulos (`modules` / manifesto `module.php`) + serviço `Modules::enabled()` + gating de rotas/menu.
  - `.env.example` completo e documentado; remover caminhos absolutos (`.user.ini` `open_basedir`, `GSC_SERVICE_ACCOUNT_JSON`) → relativos/derivados de `ROOTPATH`.
- **Entregáveis:** espinha funcional; GX rodando idêntica.
- **Definition of Done:** GX Brasil passa 100% igual em produção; é possível ligar/desligar um módulo "hello world" por flag.
- **Risco:** baixo. **Esforço:** ~1-2 semanas.

### Fase 1 — De-hardcode de marca
- **Objetivo:** toda identidade passa a ler do brand config.
- **Escopo:** migrar strings/identidade de `HomeController`, `_json_ld.php`, playbooks, `SeoFaq.php`, `ContentAISettingsModel` (persona), `robots.txt`/`llms.txt`; tokenizar cores (`brand.css` + `var(--brand-*)`).
- **DoD:** trocar `brand_settings` + logo re-marca todo o site sem tocar em código; diff de HTML da GX permanece equivalente com os defaults GX.
- **Risco:** médio (muitos pontos). **Esforço:** ~2-3 semanas.

### Fase 2 — i18n da camada de marketing/módulos
- **Objetivo:** rodar toda a plataforma em espanhol (ou qualquer idioma) por install.
- **Escopo:** strings de `marketing/*`, `simulators/*`, `wealth/*`, `newsletter/*` → arquivos de idioma; `lang_id` em `cms_pages`; moeda/número/data derivados do locale; default de telefone por install.
- **DoD:** um install configurado como `es-MX` renderiza UI, moeda e formatação em espanhol/MXN sem strings PT-BR remanescentes na camada pública.
- **Risco:** médio. **Esforço:** ~2-3 semanas.

### Fase 3 — Modularizar o existente
- **Objetivo:** simuladores e wealth viram módulos plugáveis; pipeline de IA parametrizado.
- **Escopo:** mover simuladores/wealth para `modules/`; gating por flag; **remover acoplamento a IDs fixos de categoria (6/8/7/13)**; mover verticais/keywords/mix de pauta/persona/FAQ do pipeline de IA para **config por install**.
- **DoD:** desligar o módulo de simuladores em um install remove rotas/menu/telas sem erro; o pipeline de IA aceita verticais configuráveis.
- **Risco:** médio-alto (mexe em código quente). **Esforço:** ~3-4 semanas.

### Fase 4 — Módulo Cursos + Jornada + Comunidade (produto completo)
Dado o escopo confirmado (§7 — estilo AUVP, cursos gamificados Netflix, acesso híbrido grátis/pago), quebrar em **3 sub-fases entregáveis**, cada uma com valor próprio:

- **4a — LMS + Jornada (Netflix/gamificado):** catálogo, player, progresso, trilha, XP/conquistas de curso, certificados, área do aluno, construtor de curso no admin.
  - *DoD:* aluno assiste → progride → conclui → recebe certificado; UI Netflix + trilha gamificada funcionando; admin cria curso; módulo liga/desliga por flag. **~1 a 1,5 mês.**
- **4b — Acesso & Pagamento (membership anual + cortesia p/ cliente GX):** níveis (grant manual) + **um** membership anual (fontes `paid` via gateway **ou** `client_comp`), gating central `canAccess()`, **webhook de pagamento** (ativa/renova/cancela) + **webhook de eventos de cliente vindo do CRM** (concede comp ao virar cliente; aplica a regra de corte ao cancelar) + sync de reconciliação, identidade por **documento nacional (CPF/CURP)**, área "minha assinatura". *Sem produtos/preços por curso.*
  - *DoD:* (1) grátis por nível manual; (2) pago ativa e renova anualmente via gateway; (3) virar cliente GX concede acesso sem cobrança; (4) cancelar cliente aplica o **corte correto** — honra o período pago se dentro dos 12 meses, **30 dias** de carência se já fora, **imediato** no fim do prazo. **~3 a 5 semanas.**
- **4c — Comunidade completa (estilo AUVP):** feed/espaços/comentários/reações, perfis, gamificação/ranking/badges, notificações, moderação.
  - *DoD:* membro posta/interage/ganha pontos/sobe no ranking; admin modera. **~1,5 a 2 meses.**
- **Risco:** alto (produto próprio). **Esforço total:** **~3 a 5 meses**, conforme processadora, hospedagem de vídeo e profundidade da comunidade.

### Fase 5 — Wizard de instalação + provisionamento
- **Objetivo:** instalar uma nova marca em horas.
- **Escopo:** comando/instalador (`php spark app:setup` ou wizard web) que provisiona schema base + admin + settings + idioma + brand config + módulos escolhidos; seed "esqueleto" (sem conteúdo GX); idealmente **Docker/compose** para VPS reproduzível; runbook versionado.
- **DoD:** do zero, um VPS limpo sobe uma instância funcional só com os passos do wizard (sem depender de dump da GX).
- **Risco:** baixo-médio. **Esforço:** ~2-3 semanas.

### Fase 6 — Primeiras replicações
- **Objetivo:** validar a plataforma no mundo real.
- **Escopo:** subir **GX México** (institucional + blog, ES) e a **plataforma de Educação** (institucional + blog + módulo de cursos/comunidade). Ajustes finos guiados pelo uso real.
- **DoD:** ambas as instâncias no ar, isoladas, cada uma com sua marca/idioma/módulos.
- **Risco:** baixo (base já provada). **Esforço:** ~1-2 semanas por instância.

### Linha do tempo (ordem de grandeza)
Fases 0-3 (a plataforma white-label em si): **~2-3 meses**. Fase 4 (módulo de cursos/comunidade completo, estilo AUVP): **~3-5 meses** (é um produto próprio — pode rodar em paralelo/depois). Fases 5-6: **~1 mês**. **Duas trilhas:**
- **Trilha A — Plataforma white-label + GX México:** Fases 0-3 + 5-6(México) → **~3-4 meses** (GX se beneficia desde a Fase 1).
- **Trilha B — Plataforma de Educação:** depende da Trilha A (base) + Fase 4 completa → **~6-9 meses** no total.

As trilhas podem se sobrepor: a base white-label habilita o México cedo, enquanto o módulo de cursos (o item mais pesado) é desenvolvido em paralelo por ser autocontido.

---

## 9. Princípios de execução

1. **Behavior-preserving:** a GX Brasil nunca quebra; defaults GX em tudo.
2. **GX = install #1:** nunca um fork; toda melhoria volta para a GX.
3. **Strangler incremental:** extrair hardcode aos poucos, atrás de config/flags.
4. **Espinha antes de preenchimento:** não sair de-hardcodeando à toa antes da Fase 0.
5. **Wizard por último:** só quando brand config + módulos + i18n estiverem prontos.
6. **Nome neutro:** dar identidade de produto genérica à plataforma (não "coisa da GX") no repo/config.
7. **Cada fase tem DoD verificável** e mantém a suíte de fumaça/SEO da GX passando.

---

## 10. Riscos e mitigação

| Risco | Impacto | Mitigação |
|---|---|---|
| Quebrar a GX (produção, leads/SEO) durante a refatoração | Alto | Behavior-preserving + defaults GX + branches `feat/` + verificação de HTML/SEO por fase |
| Sair de-hardcodeando sem a espinha (virar bagunça) | Alto | Fase 0 obrigatória antes de tudo |
| Escopo do módulo de cursos/comunidade estourar (é produto completo estilo AUVP) | Alto | Tratar como iniciativa própria com orçamento/prazo próprios; entregar em sub-fases 4a/4b/4c com valor por etapa; travar processadora e hospedagem de vídeo antes de codar 4b |
| Divergência GX vs. plataforma (fork acidental) | Médio | GX é install #1; um único código-base |
| Segredos vivos no `.env` vazarem na replicação | Médio | Rotacionar chaves; `.env.example` sem segredos; nunca versionar `.env` |
| Regressão de SEO na camada refatorada | Médio | Manter SSR; validar JSON-LD/sitemap/canônicos por fase |
| i18n incompleto (strings PT-BR vazando em ES) | Baixo-médio | DoD da Fase 2 exige varredura sem PT-BR na camada pública |

---

## 11. Decisões em aberto (definir para fechar o escopo)

> Recomendações default marcadas; ajuste conforme a estratégia de negócio.

1. **Pagamentos na plataforma de cursos:** ✅ **RESOLVIDO** — grátis por **nível manual** + **um único membership que desbloqueia tudo** (estilo AUVP, sem venda por curso). **Processadoras:** ✅ **MercadoPago** (BR + MX, PIX/métodos locais) e **Stripe** (fallback global), via abstração de gateway escolhida por install. **Recorrência:** ✅ **anual recorrente**; **renovação dispensada enquanto o aluno for cliente ativo da GX**. **Corte ao cancelar (✅ definido):** dentro dos 12 meses pagos → honra até o fim; já fora dos 12 meses (só comp) → **30 dias** de carência; fim do prazo sem comp → corte imediato. **Integração (✅ definido):** **webhook do CRM** dispara os eventos de cliente (virou/cancelou) + sync de reconciliação; **chave de identidade = documento nacional** (CPF no BR), **não e-mail**. *Único a confirmar:* equivalente do CPF no México (**CURP vs RFC**).
2. **Profundidade da comunidade:** ✅ **RESOLVIDO** — **completa, estilo AUVP** (feed, espaços, perfis, gamificação/ranking/badges, notificações, moderação).
3. **Hospedagem de vídeo:** YouTube/Vimeo não-listado (barato, sem player próprio) vs. **Bunny/Mux** (player próprio, analytics, proteção leve). *Recomendação:* Bunny (bom custo-benefício com player e proteção).
4. **Modelo de negócio da plataforma de Educação:** ✅ **RESOLVIDO** — **acesso único desbloqueia tudo** (modelo AUVP), sem compras por curso. Simplifica a Fase 4b (um único produto/membership). Resta só recorrente vs. vitalício (item 1b).
5. **Idioma por install:** um idioma por install ou algum precisa ser bilíngue no mesmo site?
   - *Recomendação:* **um idioma por install** (mais simples, isola SEO). Bilíngue só se houver necessidade real.
6. **Nome da plataforma:** definir um nome de produto neutro para o repo/config (des-GX).
7. **Infra:** manter Apache + PHP 8.3 + MySQL por install ou padronizar em **Docker/compose** (recomendado a partir da Fase 5).

---

## 12. Próximos passos imediatos

1. **Aprovar este plano** e responder às decisões em aberto (§11).
2. **Iniciar a Fase 0** (espinha) em branch dedicada — sem impacto na GX.
3. Definir o **nome da plataforma** e criar o **repositório-alvo** (ou renomear/organizar este).
4. Rotacionar os segredos atuais do `.env` (higiene de segurança, independente da fase).

---

## Anexo A — Referências de arquivo (pontos de refatoração)

- **Marca cravada:** `app/Controllers/HomeController.php` (~49), `app/Views/common/_json_ld.php` (Org/fundador/imprensa/`areaServed`), `app/Views/marketing/playbook_*.php` (WhatsApp/e-mail/telefone), `app/Config/SeoFaq.php`, `app/Models/ContentAISettingsModel.php`.
- **Cores/design:** `colors_and_type.css`, `app/Views/marketing/_shared_styles.php`, `app/Views/wealth/_shared_styles.php`.
- **i18n / conteúdo:** `app/Common.php:426` (`trans()`), `app/Config/App.php:96-123`, `app/Models/CmsPageModel.php` (sem `lang_id`), `app/Libraries/LeadPhoneFormatter.php` (México já suportado).
- **Verticais/IA acoplados a IDs fixos:** `app/Models/ContentAISettingsModel.php` (mix de pauta / categorias 6,8,7,13).
- **Simuladores/Brasil-específico (modularizar):** `app/Views/simulators/consorcio.php`, `app/Libraries/QuotationEngine.php`, `app/Libraries/QuotationGate.php`.
- **Instalação/landmines:** `app/Database/Migrations/` (33 — só custom), `app/Database/Seeds/`, `.user.ini:1` (`open_basedir`), `.env` (`GSC_SERVICE_ACCOUNT_JSON`), sessão via DatabaseHandler (tabela `ci_sessions`).

## Anexo B — Fatos técnicos de base (do relatório)
- Base: Varient v2.4.2 + CI4 4.5.7 vendorizado; PHP 8.3; sem Composer (deps em `app/ThirdParty/`, 87 MB).
- Banco `portal`: 90 tabelas / ~60 MB. `uploads/` 197 MB (mídia local por padrão; S3 opcional por setting).
- Licença Varient: **liberada** pelo proprietário (não há mais bloqueio de novas licenças).
- `.env`, `writable/`, `uploads/` são gitignored (transferidos por fora do Git).
