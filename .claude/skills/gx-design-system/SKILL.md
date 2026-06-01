---
name: gx-design-system
description: This skill should be used whenever building, editing, or restyling UI inside the GX Capital portal — including admin panel views (app/Views/admin/**), marketing/institutional pages (app/Views/marketing/**), simulators (app/Views/simulators/**), bio links, web stories, theme partials, or any HTML/CSS/JSX surface that the brand owns. It encodes the GX Capital "Nexus" visual identity (deep navy + champagne gold, brutalist financial, sharp 0px corners, uppercase tracking-widest labels), points to the canonical token sheet and component library bundled with the repo, AND covers AI image generation when a hero/feature surface needs custom imagery (uses the existing OpenAIImageHelper + gpt-image-1 family that already powers post covers and web stories — credentials read from .env). Triggers include phrases like "criar tela", "novo dashboard", "redesign", "página", "componente", "card", "botão", "kpi", "header", "sidebar", "imagem", "ilustração", "hero image", "capa", any work inside the admin panel UI, and any task that produces user-facing screens.
---

# GX Capital Nexus Design System

The portal already ships its own design system. Don't invent tokens — load them.

## Step 1 — Always read these first

Before writing any UI code, **read** the files below in the same turn (single Read call each). They are the source of truth and override anything you remember from training:

1. `colors_and_type.css` (repo root) — the full token sheet: CSS variables for color, type, spacing, shadows, radii, motion. Imported via `<link rel="stylesheet" href="/colors_and_type.css">` on every page that needs the system.
2. `ui_kits/nexus/README.md` — index of canonical components.
3. `ui_kits/nexus/index.html` — the live click-thru. Mimic its compositional patterns (full-bleed dark hero, KPI grid, gold quick-actions panel, table with bracketed cards).
4. The specific component file(s) closest to what you're building, e.g. `ui_kits/nexus/Button.jsx`, `Card.jsx`, `KpiTile.jsx`, `PageHeader.jsx`, `Sidebar.jsx`, `Tabs.jsx`, `QuickActions.jsx`, `ActivityList.jsx`, `LoginScreen.jsx`.

These are React/JSX prototypes but the styling rules transfer 1:1 to the PHP/CodeIgniter views and to admin AdminLTE templates — the tokens are in plain CSS variables.

## Step 2 — Aesthetic direction (non-negotiable)

The brand voice is **brutalist financial**: serious, precise, confident. Translate that to:

- **Sharp corners always.** `border-radius: 0`. Pills (`9999px`) only for badges/avatars.
- **Hard offset shadows**, not soft blurs. `--shadow-card`, `--shadow-card-hover`, `--shadow-elevated` (e.g. `4px 4px 0 0 rgba(12,49,99,0.2)`).
- **Two dominant colors** — deep navy (`--gx-primary` `#0c3163`, `--gx-primary-dark` `#000d23`) and champagne/gold (`--gx-secondary-light` `#dbc7a2`, `--gx-secondary-dark` `#87704a`, `--gx-gold` `#c9a96a`). Surfaces alternate between navy panels (white/champagne text) and white cards (navy text). Champagne accents on hover/active.
- **Type pairing**: Inter for everything (weights 400/500/600/700/900). JetBrains Mono **only for numbers** — KPI values, prices, monetary cells, code, ticker symbols. Tabular nums on monospaced numerics: `font-variant-numeric: tabular-nums`.
- **Uppercase tracking-widest labels.** Eyebrows, table headers, KPI labels, section titles → `text-transform: uppercase; letter-spacing: 0.1em–0.18em; font-weight: 700–900; font-size: 10–12px`. Never sentence-case for these.
- **Hero / headlines**: `font-weight: 900`, `font-size: 48–96px`, `letter-spacing: -0.04em`, `line-height: 0.92`, `text-transform: uppercase`. Pair with a 2px-tall, 36px-wide accent bar in `--gx-primary` to the left of the eyebrow.
- **Watermark behind dark heroes**: `GXC` lettermark at `font-size: 180–220px`, `opacity: 0.05`, positioned absolutely. See `PageHeader.jsx`.
- **Gold panels** for "command surfaces" (quick actions, premium CTAs) — `background: var(--gx-secondary-dark)` with white/gold-light text. See `QuickActions.jsx`.
- **Tech-bracket card hover**: 12×12px L-brackets in top-left and bottom-right corners that fade in on hover. See `Card.jsx`.
- **Tabular underline tabs** with 2px bottom border in active color. See `Tabs.jsx`.
- **Sidebar**: vertical navy gradient (`var(--gx-primary)` → `var(--gx-primary-dark)`), white text, active item gets `rgba(255,255,255,0.2)` background + 4px translateX. See `Sidebar.jsx`.

## Step 3 — Mandatory token usage

| Need | Use this token | Don't use |
|---|---|---|
| Primary navy | `var(--gx-primary)` | `#0c3163` literal |
| Champagne accent | `var(--gx-secondary-light)` / `var(--gx-secondary-dark)` | random gold values |
| Body bg | `var(--bg1)` (white) or `var(--bg2)` (champagne-tinted) | `#fafafa`, `#f8f9fa` |
| Body text | `var(--fg1)` (`#000d23`) | `#222`, `#333`, `#000` |
| Muted text | `var(--fg2)` (`#5a6a80`) | `#666`, `#999` |
| Borders | `var(--gx-border)` (`#d3d9e0`) | `#ddd`, `#e5e7eb` |
| Shadows | `var(--shadow-card)`, `var(--shadow-card-hover)`, `var(--shadow-elevated)` | hand-rolled `0 2px 8px rgba(...)` |
| Spacing | `var(--space-1..24)` (4px base) | arbitrary `px` |
| Motion | `var(--transition-smooth)` (`cubic-bezier(0.16,1,0.3,1)`) | `ease`, `ease-in-out` |
| Radii | `var(--radius)` (= 0) | `4px`, `8px`, `12px`, anything > 0 except `--radius-pill` for badges/avatars |

If a value isn't in `colors_and_type.css`, prefer to **extend the token sheet** rather than hard-code.

## Step 4 — Semantic helper classes

The token sheet ships ready-to-use typography classes. Prefer them over restyling from scratch:

- `.gx-display` — 96px brutalist hero
- `.gx-h1` — 64px page title
- `.gx-h2` — 32px section title
- `.gx-h3` — 24px card title
- `.gx-eyebrow` — 12px uppercase tracking-widest label, gold
- `.gx-label` — 10px uppercase muted micro-label
- `.gx-body` / `.gx-body-sm` — body text
- `.gx-mono`, `.gx-kpi-value`, `.gx-price` — JetBrains Mono with tabular-nums

## Step 5 — Component porting cheatsheet (PHP/AdminLTE → Nexus)

Most admin views in this repo were inherited from AdminLTE 2 and are not yet branded. When asked to redesign or restyle:

1. **Replace `.box` with the Nexus card pattern** from `Card.jsx`: 1px `var(--gx-border)`, 0 radius, `padding: 20–24px`, hover state shows tech-brackets + soft shadow `0 0 20px -10px rgba(12,49,99,0.3)`.
2. **Replace `.box-title` h3** with the eyebrow pattern: a 32px-wide × 2px navy bar followed by uppercase tracking-widest title in `--gx-secondary-dark`.
3. **KPI cards**: alternate `light` / `dark` variants on the same row (see `KpiTile.jsx`). Dark = `var(--gx-secondary-dark)` background with `border-bottom: 4px solid var(--gx-primary)`. Numbers always JetBrains Mono, weight 900.
4. **Tables**: 1px `var(--gx-border)` outer, `var(--bg2)` header row, uppercase tracking-widest `th`, hover row → `var(--bg2)` with `cursor: pointer`. See `.crm-table` in `ui_kits/nexus/index.html`.
5. **Pills/badges**: 0 radius, 10px font, weight 700, uppercase, `letter-spacing: 0.1em`. Color tokens: gold for neutral/in-progress, green for converted/positive, red for lost/negative.
6. **Buttons**: import patterns from `Button.jsx`. Variants: `primary` (navy bg, champagne text), `secondary` (champagne bg, navy text), `outline` (white bg + `--gx-border`), `ghost`, `dark`, `gold`, `destructive`. Hover lifts with `--shadow-card-hover`. Active scales to 0.95.

## Step 6 — Forbidden patterns (instant reject)

- ❌ Rounded corners on cards, buttons, inputs, KPI tiles. The brand is sharp. Period.
- ❌ Soft drop shadows like `box-shadow: 0 4px 6px rgba(0,0,0,0.1)`. Use hard offset shadows from the token sheet.
- ❌ Bootstrap blue (`#007bff`), Material teal, generic SaaS purple gradients. Stick to navy + gold.
- ❌ Sentence-case section titles or "Card Title". Make eyebrows uppercase tracking-widest.
- ❌ Sans-serif numbers in KPIs/prices/tables. Use `var(--font-mono)` with `tabular-nums`.
- ❌ Inventing new colors when there's a token. If something feels missing, extend `colors_and_type.css` first.
- ❌ Putting the `colors_and_type.css` rules inline in a view. Always link the canonical sheet.

## Step 7 — Self-review checklist before reporting "done"

Run through this list mentally before claiming the UI is finished:

- [ ] `colors_and_type.css` is linked (or its tokens are loaded into the admin theme).
- [ ] Every color, shadow, radius, spacing comes from a CSS variable, not a literal.
- [ ] All radii are 0 except for badges/avatars.
- [ ] Numbers (KPIs, currency, percentages, timestamps in tables) use `var(--font-mono)` + `tabular-nums`.
- [ ] Section headings use the eyebrow pattern (2px × 32px navy bar + uppercase tracking-widest title).
- [ ] At least one dark surface (navy panel or gold quick-actions block) anchors the screen.
- [ ] Hover states are present on cards, buttons, table rows, sidebar items.
- [ ] If admin: contrast vs the existing AdminLTE views — the new screen looks like it belongs to GX Capital, not generic AdminLTE.

## Step 8 — When the user asks for something genuinely new

If the design surface needs a pattern not yet in the kit (modal, drawer, stepper, calendar, etc.), do this in order:

1. Extend `ui_kits/nexus/` with the new component, named after the pattern (`Modal.jsx`, `Stepper.jsx`).
2. Use the existing tokens — never hardcode new colors/shadows.
3. Add a usage note to `ui_kits/nexus/README.md`.
4. Then use the new component in the actual view.

This keeps the system canonical and prevents drift across the portal.

## Step 9 — Generating custom images for a page

When a hero, feature card, illustration, or empty-state needs an original image and there isn't a good asset already in `uploads/`, **reuse the same image-generation pipeline that produces post covers and web stories**. Do not write a new client.

### What's already wired

| Piece | Path | Purpose |
|---|---|---|
| Helper class | `app/Helpers/OpenAIImageHelper.php` | Single entrypoint to OpenAI's `/v1/images/generations`. Handles model/size/quality validation and gpt-image-1 → dall-e-3 fallback on 403. |
| Credentials | `.env` → `OPENAI_API_KEY`, with fallback to `aiWriter()->apiKey` from `general_settings.ai_writer` (serialized) | Helper's constructor already reads both — don't pass keys around manually. |
| Defaults | `.env` → `OPENAI_DEFAULT_MODEL` (`gpt-image-1-mini`), `OPENAI_DEFAULT_QUALITY` (`high`), `OPENAI_DEFAULT_SIZE` (`1024x1536`), `OPENAI_BRAND_STYLE` | Read these instead of hardcoding. |
| Reference call site (cover) | `app/Controllers/PostController.php` lines ~880–1014 (action `generateAICover`) | Canonical pattern: build prompt, call helper, download, run through `UploadModel::uploadPostImage` for variants, convert to WebP, optionally push to S3. |
| Reference call site (story) | `app/Libraries/ContentAIService.php::generateCoverImage()` | Same flow, also handles `b64_json` response (gpt-image-1 returns base64 by default). |
| Image download | `PostController::downloadImage($url, $destPath)` (private) — reuse the same curl shape if you need it elsewhere | Ouputs to `uploads/tmp/`. |
| WebP conversion | `app/Helpers/WebPConverter.php` (constructor takes quality 1–100, default 85) | Always convert generated PNGs to `.webp` before persisting. |

### When to generate vs. when not to

Generate **only** when the screen materially benefits from custom imagery:
- Hero / above-the-fold backgrounds on marketing or admin landing pages.
- Feature cards or empty-states that would otherwise be plain-text boxes.
- Editorial illustrations for institutional pages or campaign blocks.

Don't burn tokens on:
- Icons → use `lucide` (already loaded in the kit) or inline SVG.
- Decorative dividers / dotted lines → CSS or SVG.
- Logos → already in `uploads/logo/` — use `getPwaLogo()` / existing brand assets.
- Anything < 200×200 px in final layout — generation cost is wasteful.

If unsure, ask the user before generating.

### Model + size rules

GPT-Image-1 family supports **only** these sizes:
- `1024x1024` square
- `1536x1024` landscape (3:2) — preferred for hero banners and admin headers
- `1024x1536` portrait (2:3) — preferred for web stories and mobile-first hero

For the GX Capital portal, defaults that match the brand grid:
- **Admin/marketing hero**: `gpt-image-1-mini`, `1536x1024`, `quality: high`
- **Editorial illustration / card hero**: `gpt-image-1-mini`, `1024x1024`, `quality: medium`
- **Mobile hero / story slide**: `gpt-image-1-mini`, `1024x1536`, `quality: high`
- **High-stakes landing page hero (rare)**: `gpt-image-1` (full, not mini), `1536x1024`, `quality: high`

Never pass `n > 1` for the gpt-image family — the helper already clamps it.

### Prompt language (must align with the brutalist aesthetic)

Build the prompt in PT-BR (the rest of the portal is PT-BR). Always include:

1. **Subject + scene** — concrete, cinematic, financial editorial.
2. **Brand palette anchor** — `harmonize com a paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e champagne/dourado (#dbc7a2, #87704a, #c9a96a)`.
3. **Aesthetic direction** — `estilo editorial sofisticado, premium, brutalismo financeiro, composição limpa, fotográfica, iluminação natural, profundidade de campo rasa`.
4. **Hard requirements** — `sem texto, sem logos, sem marca d'água, alta qualidade, foco centralizado, margens de segurança nas bordas, espaço negativo central preservado para overlay`.
5. **Format hint** — `formato paisagem 3:2` (or `retrato 2:3` / `quadrado 1:1`) — match the size you're requesting.

Example, for an admin dashboard hero illustration:

```
Imagem editorial para o topo do dashboard executivo de uma assessoria
financeira premium. Composição abstrata sugerindo dados, mercados e
liquidez — gráficos sutis de linhas, painéis de luz, geometria sólida.
Estilo editorial sofisticado, brutalismo financeiro, fotográfico,
iluminação natural fria, profundidade de campo rasa. Harmonize com a
paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e
champagne/dourado (#dbc7a2, #87704a, #c9a96a). Formato paisagem 3:2,
foco centralizado, margens de segurança, espaço negativo central
preservado para overlay de título. Sem texto, sem logos, sem marca
d'água, alta qualidade.
```

### End-to-end recipe (PHP)

```php
use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;

$helper = new OpenAIImageHelper();   // reads OPENAI_API_KEY automatically
$result = $helper->generateImage(
    $prompt,
    getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini',
    '1536x1024',
    'high',
    1
);

if (!$result || empty($result['data'][0])) {
    log_message('error', 'GX skill: image generation failed');
    return null;
}

// gpt-image-1* returns b64_json by default; dall-e-3 fallback returns url.
$row = $result['data'][0];
$tmpDir = FCPATH . 'uploads/tmp/';
if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0755, true); }
$tmpPath = $tmpDir . 'gx_' . uniqid('', true) . '.png';

if (!empty($row['b64_json'])) {
    file_put_contents($tmpPath, base64_decode($row['b64_json']));
} elseif (!empty($row['url'])) {
    // Reuse PostController::downloadImage shape if you need it in a new controller.
    file_put_contents($tmpPath, file_get_contents($row['url']));
}

// Convert to WebP for delivery.
$webp = (new WebPConverter(85))->convert($tmpPath, null, true);
$relativePath = str_replace(FCPATH, '', $webp);
// Persist $relativePath in the page model / settings table as needed.
```

### Persistence rules

- **Generated images for posts** → use `UploadModel::uploadPostImage()` to produce the discover/big/default/slider/mid/small variants (already done in `PostController::generateAICover`).
- **Generated images for marketing/admin pages** → save to a meaningful subfolder under `uploads/` (e.g. `uploads/marketing/hero_<slug>_<ts>.webp`). Reference via `base_url($relativePath)`.
- **If `general_settings.storage = 'aws_s3'`** → mirror to S3 via `\App\Models\AwsModel::uploadFile($relativePath)` after WebP conversion. The post-cover flow already does this; copy the same try/catch.
- **Always WebP** the final stored asset. PNG only stays in `uploads/tmp/` and should be deleted after conversion.

### Forbidden when generating images

- ❌ Don't paste API keys in code or commit them — read from `getenv('OPENAI_API_KEY')` or rely on the helper.
- ❌ Don't ship images **with text rendered into them** — the prompt must say "sem texto / no text". Headlines belong in HTML, layered over the image, so they stay localizable and accessible.
- ❌ Don't bypass the helper to call `https://api.openai.com/v1/images/generations` directly. The helper handles model validation, size validation, gpt-image-1 → dall-e-3 fallback on verification errors, and consistent logging.
- ❌ Don't generate, link the raw OpenAI signed URL into the page, and call it done. Those URLs expire — always download, convert, persist locally (or to S3).
- ❌ Don't loop generations to "pick the best". Generate once with a strong prompt; if the result is wrong, refine the prompt and regenerate, but never auto-batch.

### Self-review for image work

- [ ] Was generation actually necessary, or could SVG/CSS/an existing asset have done the job?
- [ ] Prompt is in PT-BR, includes the GX color anchor, the brutalist style direction, and the "sem texto / sem logo" guards.
- [ ] Model + size match the surface (3:2 hero, 2:3 mobile, 1:1 card).
- [ ] Image was downloaded, converted to WebP, persisted under `uploads/`, and (if S3 enabled) mirrored to S3.
- [ ] The view references the local/S3 path — never the raw OpenAI URL.
- [ ] The headline / overlay text lives in HTML, not baked into the image.
