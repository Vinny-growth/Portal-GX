# Fase 1 — De-hardcode de marca (detalhamento executável)

**Documento pai:** [`PLANO-WHITELABEL.md`](./PLANO-WHITELABEL.md) · **Depende de:** [`FASE-0-ESPINHA.md`](./FASE-0-ESPINHA.md) (brand config já existe).
**Objetivo:** os pontos hardcoded da marca passam a **consumir `brand()`**. Como os valores em `brand_settings` foram seedados com os da GX, o resultado renderizado é **idêntico** — mas agora trocável por install.
**Princípio:** behavior-preserving. Verificação = **diff de HTML/JSON-LD antes/depois** (deve ser byte-idêntico onde a mudança é 1:1).

---

## Escopo desta fase (e o que foi adiado)

**Nesta fase (identidade textual — verificável byte-a-byte):**
- **1A — `app/Views/common/_json_ld.php`** (global, toda página): Org `name`/`legalName`/`description`, `founder` (Person), `areaServed`, `press mentions` → `brand()`.
- **1B — `app/Controllers/HomeController.php`** (~49 pontos): titles, meta descriptions, mensagens de WhatsApp, respostas de FAQ que citam a marca/contatos → `brand()`.
- **1C — playbooks** (`app/Views/marketing/playbook_*.php`): nome, WhatsApp, e-mail, telefone hardcoded → `brand()`.

**ADIADO (com motivo):**
- **Cores / tokenização (`colors_and_type.css`, `_shared_styles.php` x2):** os dois `_shared_styles.php` têm **WIP não-commitado** do geo-seo; mexer entrelaçaria mudanças. Fazer **depois** que esse WIP for commitado. O `brandCssVars()` já existe (Fase 0) pronto para injetar.
- **`robots.txt` / `llms.txt`:** arquivos **estáticos** servidos pelo webserver (não passam por PHP). Vão virar **gerados por install no wizard (Fase 5)** ou template — não convém torná-los dinâmicos agora (complica o serving).
- **`SeoFaq.php` / `ContentAISettingsModel.php` (persona):** é **conteúdo/mercado** (Copom, Selic, BNDES…), não "chrome" de marca. Pertence mais à Fase 3 (pipeline de IA) do que à identidade. Fica lá.

---

## 1A — `_json_ld.php` (implementado primeiro)

Substituições (todas com default = valor GX atual, garantindo byte-idêntico):

| Ponto | Antes | Depois |
|---|---|---|
| Person `@id` (2x) | `base_url().'/#person-vinicius-teixeira'` | `base_url().'/'.brand('founder_schema_id','#person-vinicius-teixeira')` |
| Person `name` | `"Vinicius Teixeira"` | `brand('founder_name','Vinicius Teixeira')` |
| Person `jobTitle` | `"CEO & Founder"` | `brand('founder_title','CEO & Founder')` |
| Org `name` | `"GX Capital"` | `brand('display_name','GX Capital')` |
| Org `legalName` | `"GX Capital"` | `brand('legal_name','GX Capital')` |
| Org `description` | string fixa | `brand('org_description', <fixa>)` |
| Org `areaServed.name` | `"Brazil"` | `brand('area_served','Brazil')` |
| Press mentions | `$gxPressMentions ?? [fixo]` | `$gxPressMentions ?? (brandPressMentions() ?: [fixo])` |

**Não alterado nesta fase (topical, não identidade):** listas `knowsAbout`, `areaServed.sameAs` (link Wikipedia BR) — anotados para revisão por install.

**DoD 1A:** JSON-LD da home **byte-idêntico** ao baseline (`/tmp/fase1/jsonld_home_BEFORE.txt`).

---

## Verificação (todas as sub-fases)
1. Capturar HTML/JSON-LD **antes** das páginas afetadas.
2. Aplicar edições + `php -l`.
3. Capturar **depois** e **diff** → idêntico (onde a troca é 1:1).
4. Smoke test: páginas-chave seguem **200**.

## Resultado da execução (06/jul/2026)

**Feito e verificado (byte-idêntico / site 200):**
- **1A — `_json_ld.php`:** Org name/legalName/description/founder/areaServed + press → `brand()`. Home JSON-LD **byte-idêntico** (6723 bytes).
- **1B — HomeController (identidade estruturada):** `:720` `$pageName` e `:750` FinancialService `name` → `brand('display_name')`. Home JSON-LD segue idêntico.
- **1C — playbooks:** `email` + `whatsapp` (fallbacks) → `brand()`; **telefone deixado** (middot `·` é apresentação; `settings.contact_phone` é NULL).

**Decisão de escopo (importante):** as **~40 ocorrências restantes de "GX Capital" no HomeController** estão dentro de **strings de conteúdo em português** (mensagens de WhatsApp, respostas de FAQ, meta descriptions sobre câmbio/consórcio/4131). Isso é **conteúdo/i18n**, não "chrome" de marca — a **Fase 2** externaliza cada string para arquivo de idioma, e lá o nome da marca entra via `brand()` naturalmente. Forçar `brand()` dentro dessas frases agora seria churn prematuro e arriscado. **Adiado para a Fase 2, de propósito.**

**Também adiado (já previsto):** cores/tokens (WIP de `_shared_styles.php`), `robots/llms` (estáticos → wizard Fase 5), `SeoFaq`/persona da IA (conteúdo → Fase 3).

## Saída → Fase 2
Com a marca lendo de `brand_settings`, a **Fase 2** (i18n) passa a traduzir a camada de marketing/módulos; e a tokenização de cores entra assim que o WIP de `_shared_styles.php` for commitado.
