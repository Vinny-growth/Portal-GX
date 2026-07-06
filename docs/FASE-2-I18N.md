# Fase 2 — i18n da camada de marketing/módulos (detalhamento)

**Documento pai:** [`PLANO-WHITELABEL.md`](./PLANO-WHITELABEL.md) · **Depende de:** Fase 0 (brand config) + Fase 1 (identidade).
**Objetivo:** rodar a **camada GX (marketing, simuladores, wealth, newsletter, módulos)** no **idioma do install** — hoje 100% hardcoded pt-BR. Habilita o deploy do México em espanhol.
**Estratégia:** **um idioma por install** (deploy monolíngue). A camada de marketing/módulos usa **arquivos de idioma CI4 (`lang()`)**; o admin/blog clássico continuam no sistema DB (`trans()` / `language_translations`) — os dois convivem.

---

## Mecanismo (decisão)
- **Locale do install** = `brandLocale()` (subtag primária de `brand('locale')`; `pt-BR`→`pt`, `es-MX`→`es`).
- `BaseController::initController` faz `service('language')->setLocale(brandLocale())` → todo `lang()` na request resolve o idioma do install (fallback CI4 para `defaultLocale='en'` quando falta chave — seguro).
- Strings ficam em `app/Language/<locale>/<Arquivo>.php`. Para o México: `app/Language/es/*.php` + `brand('locale')='es-MX'`.
- Views passam a consumir `lang('Marketing.<chave>')` (ou `Simuladores.*`, `Newsletter.*`, etc.).

## Espinha (feita e verificada — 06/jul/2026)
- `brandLocale()` em `app/Helpers/brand_helper.php`.
- `service('language')->setLocale(brandLocale())` em `BaseController`.
- `app/Language/pt/Marketing.php` (catálogo iniciado: `__proof`, CTAs).
- **Verificado:** `lang('Marketing.__proof','pt')='i18n-ok-pt'`; site 200; **nenhuma string trocada** (sem mudança visível).
- **Risco avaliado:** só existia **1** `lang()` no app (título de erro) — muda locale sem efeito prático (fallback en).

## Próximos passos (migração de strings — em lotes)
Cada lote: extrair strings pt-BR de um grupo de views → `app/Language/pt/<Arquivo>.php` → trocar por `lang(...)` → **diff de HTML antes/depois byte-idêntico**.

1. **CTAs/labels compartilhados** (`_specialist_form.php`, botões, headers de marketing).
2. **HomeController — as ~40 strings de prosa** adiadas da Fase 1 (WhatsApp/FAQ/descriptions) → `Home.php` de idioma; o nome da marca entra via `brand()` interpolado.
3. **Simuladores** (`consorcio.php`, `seguro_resgatavel.php`, `simulators_fx_hub.php`).
4. **Wealth** (`landing/results/pdf_template`) e **Newsletter** (`_layout`, `landing`, `thank_you`).
5. **Libraries de defaults** (`MarketingHomeDefaults`/`SimulatorsDefaults`/`ConsorcioDefaults`).

## Formatação / moeda (locale)
- Moeda BRL/`R$` e `number_format(...,',','.')` / `Intl.NumberFormat('pt-BR')` cravados em simuladores/wealth → derivar de `brand('currency')`/`brandLocale()`. Tratar junto de cada lote de simulador.

## Estrutural
- **`cms_pages` sem `lang_id`:** adicionar coluna (migration aditiva) para a config de home/marketing ganhar dimensão de idioma quando necessário. Fazer quando um install precisar de conteúdo de marketing multilíngue.

## Adiado (já previsto)
- **Cores/tokens:** bloqueado pelo WIP de `_shared_styles.php` (aplicar quando commitado).
- **Persona da IA / SeoFaq:** conteúdo de mercado → Fase 3.
