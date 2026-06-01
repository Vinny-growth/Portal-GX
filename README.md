# GX Capital — Design System

A brutalist, technical financial brand system for **GX Capital**, a Brazilian wealth-management / multi-product fintech. The visual language pairs deep navy and antique champagne gold with sharp 0px corners, hard-offset shadows, and uppercase tracking-widest labels — built to feel **disruptive yet institutional**.

## Index (manifest)

```
.
├── README.md                  ← you are here
├── SKILL.md                   ← Agent Skill manifest (cross-compatible with Claude Code)
├── colors_and_type.css        ← all CSS vars (colors, type, shadows, motion, spacing)
├── assets/                    ← logos, brand marks, grid texture
├── preview/                   ← design-system preview cards (HTML, ~700×variable)
├── ui_kits/
│   └── nexus/                 ← UI kit reproducing the GX Capital Nexus AI app
│       ├── README.md
│       ├── index.html         ← interactive click-thru prototype
│       └── *.jsx              ← componentized UI primitives
└── uploads/                   ← original brand uploads (raw, do not edit)
```

## Sources of truth

| Resource | Where | Notes |
|---|---|---|
| **Brand logos** | `uploads/`, `assets/` | Provided by user; horizontal lockup, square app icon, gold/blue + reverse-on-dark |
| **Codebase** | `Vinny-growth/gx-capital-nexus-ai` (GitHub, private) | Vite + React + Tailwind + shadcn/ui + Supabase. Reference files: `src/index.css` (tokens), `tailwind.config.ts`, `src/components/ui/*`, `src/components/layout/AppSidebar.tsx`, `src/pages/Dashboard.tsx` |
| **Other related repos (not yet imported)** | `Vinny-growth/Portal-GX`, `Vinny-growth/GX-wealthmaneger-pilot`, `Vinny-growth/gx-capital-nexus-platform`, `Vinny-growth/gx-finance-insight`, `Vinny-growth/GX-crm`, `Vinny-growth/planejamento-gx` | Multiple GX-branded surfaces — partner portal, wealth manager, CRM, planning tool. The Nexus AI codebase is the canonical visual source for now. |

The reader is **not assumed** to have access to the GitHub repos — anything needed for design fidelity has been mirrored into this project.

## What is GX Capital?

A Brazilian capital / wealth-management firm building an internal **operations platform** ("Nexus") that bundles multiple workstreams into one product:

- **Dashboard / Executive KPIs** — AUM, MRR, performance vs. goal, FX radar
- **CRM** — leads, opportunities, marketing, growth
- **Wealth / Carteira de Clientes** — client portfolio management
- **Mesa de Operações** — trading desk
- **Comissionamento** — commission structures
- **ERP** — finance, banking integrations (Banco Inter OFX, etc.)
- **RH / Meu RH** — HR & self-service
- **Atendimento + Feed Interno** — internal comms
- **Agentes IA / Consultor Financeiro IA / Agente de Apresentações** — AI agents for advice, presentations, knowledge base
- **Public Landing + Partner Portal + Public Business Card** — outward-facing surfaces

The brand also appears across satellite repos (Portal-GX, Wealth-manager pilot, planning, CRM 2.1) — visually consistent.

## Content fundamentals

**Language.** Primary is **Brazilian Portuguese**. UI uses `pt-BR` formatters (`Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })`). English is acceptable in marketing or international contexts but the product itself is PT-BR.

**Tone.** Authoritative, technical, decisive. Not warm or playful. Reads like a Bloomberg terminal or a private-banking pitch deck — the user is a professional, not a consumer.

**Casing.**
- **UPPERCASE + tracking-widest** for eyebrows, section labels, KPI captions, tab triggers, badges. (E.g. `VOLUME TOTAL (AUM)`, `META DO MÊS`, `ACESSO DIRETO`.)
- **Title Case** for page titles and proper nouns ("Dashboard Executivo - Visão Geral", "Comandos Rápidos", "Mesa de Operações").
- **Sentence case** for body copy and descriptions.

**Person.** "Você" / second person for self-service ("Meu Dashboard", "Seus dados e métricas pessoais"). Third-person institutional for shared/admin views ("Visão consolidada de todos os usuários do sistema").

**Numbers.** Always tabular-nums, always JetBrains Mono for KPIs and prices. Currency = BRL (`R$`), formatted `R$ 1.234.567,89`. Percentages get a giant black numeral with a tiny `%` sibling (see `AUMGoalProgress`).

**No emoji.** No hand-drawn warmth. The product uses **Lucide React icons exclusively** — outline strokes, technical, monoline.

**Vibe in one sentence.** *Bloomberg terminal × brutalist editorial magazine × old-money private bank.*

**Examples lifted from the product:**
- Section eyebrow: `EVOLUÇÃO DO FATURAMENTO`
- KPI label: `Ativos sob gestão (Escopo atual)`
- Tab trigger: `NEXUS IA`, `VISÃO GERAL`, `KPIS EXECUTIVOS`
- Quick action: `Novo Lead` / `Cadastrar novo lead` (title + description, both short)
- Watermark: massive `GXC` at 12rem, opacity 5%, in dark headers

## Visual foundations

### Palette
- **Primary navy** `#0c3163` — the workhorse. Sidebar gradient, primary CTAs, accent strokes, KPI captions.
- **Dark navy** `#000d23` — page-header bg (`bg-[#0f172a]` near equivalent), sidebar gradient end, hero bg.
- **Champagne light** `#dbc7a2` — secondary surfaces, sidebar text, "secondary-foreground" pairing on navy.
- **Antique gold** `#87704a` — KPI mid-emphasis, eyebrow text, dark accent.
- **Logo gold** `#c9a96a` — used in the brand mark's diagonal stripes.
- **Neutrals** white, `#f5f3ee` (champagne-tinted muted), `#d3d9e0` borders.
- **Semantic** — emerald `#16a34a` for success/over-target, standard red/amber for danger/warning, but used sparingly. The brand prefers brand colors even for state.

### Typography
- **Inter** for everything sans-serif. Heavy use of weight **900 (black)** for headlines — hero `text-7xl font-black tracking-tighter` is the signature move.
- **JetBrains Mono** for numbers, codes, KPI values, prices (`.font-mono`, `.tabular-nums`, `.kpi-value`, `.price-tag`).
- Ranges from 10px (watermark labels) to 128px (mega watermarks). 12px uppercase eyebrows everywhere.

### Backgrounds
- **Solid color first**, gradients second. The sidebar uses a vertical navy gradient (`from-gx-primary via-gx-primary to-gx-primary-dark`); page headers use near-black `#0f172a` with subtle gradients.
- **Typographic watermarks**: massive `GXC` letters at opacity 5% absolute-positioned in headers — a signature.
- **Grid texture**: `assets/grid.svg` available as subtle background.
- **No photography by default.** When imagery is needed, lean cool / blue-tinted / corporate.
- **No hand-drawn illustrations.** No gradients with bluish-purple. No emoji cards.

### Geometry
- **`--radius: 0`** everywhere. The product is committed to **sharp corners**. Pills only on legacy badges and avatars.
- **Border widths** — 1px default, 2px for emphasis (top/bottom accent strokes), 4px for KPI accent borders.
- **Tech brackets** — Cards have CSS `::before` / `::after` pseudo-elements drawing 12px L-brackets in the top-left / bottom-right that activate on hover (transparent → `gx-primary`). Subtle but distinctive.

### Shadows
- **Hard-offset (no blur)** — `4px 4px 0 0 rgba(12,49,99,0.2)` is the signature `--shadow-gx`. Hover bumps to `6px 6px 0`. Elevated to `8px 8px 0`.
- A subtle ring shadow `0 0 0 1px hsl(var(--border))` is used as `--shadow-soft`.
- Glow shadows (`0 0 20px hsl(var(--gx-primary)/0.3)`) appear only on hover for `hover-glow`.

### Hover / press states
- **Hover**: most cards lift `translate-y(-4px)` + bigger hard-offset shadow + tech-brackets light up gold/navy. Buttons add `shadow-elevated`. Sidebar items get `bg-white/10` + `translate-x-1` slide.
- **Press**: `active:scale-95` on buttons. No color change.
- **Active tab**: bottom border (2px) in `gx-secondary-dark` (gold) for tab triggers. Sidebar active state: `bg-white/20 backdrop-blur-sm ring-1 ring-white/20 shadow-card-gx translate-x-1`.

### Motion
- Easings used: `cubic-bezier(0.16, 1, 0.3, 1)` (smooth/snappy, dominant), `cubic-bezier(0.34, 1.56, 0.64, 1)` (bounce, occasional), `cubic-bezier(0.175, 0.885, 0.32, 1.1)` (spring).
- Durations: 150–400ms. Default 200ms.
- Animations available: `fade-in`, `slide-in-right`, `slide-in-left`, `scale-in`, `shimmer` (skeleton loader), `pulse-glow`, `gradient-shift` (rare).
- Global animations toggle: `[data-animations="false"] *` disables all. Respect this attribute.

### Transparency & blur
- **Glassmorphism is sparingly used** — `glass` and `glass-dark` utilities exist but the brand prefers solid surfaces. When used, it's on overlays/popovers over hero gradients.
- White-on-navy: `text-white/50` for muted, `text-white/70` for medium emphasis, `text-white` for primary.
- Backgrounds: `bg-white/5`, `bg-white/10`, `bg-white/20` for layered glass on dark.

### Cards
- Default card: **white bg, 1px border, 0px radius, no shadow at rest**. On hover: navy border at 50% + faint navy glow + tech-brackets activate.
- Dark cards (KPI tiles): **navy or `gx-secondary-dark` solid bg, white text, 4px bottom border in `gx-primary` for accent**. Same 0px radius. Decorative `bg-gradient-to-l from-white/5` overlay on right side.

### Layout rules
- **Container**: `container` centered, 2rem padding, max `1400px` at 2xl.
- **Sidebar**: 240px expanded, 56px collapsed (icon-only). Always navy gradient.
- **Page padding**: `p-4 md:p-10` standard. Hero headers `p-6 md:p-12`.
- **Grid gaps**: usually `gap-3 md:gap-4 lg:gap-6` — getting more generous on larger screens.

### Iconography (see also: ICONOGRAPHY below)
- **Lucide React** is the only icon system. Strokes only, no fills.

## Iconography

The Nexus AI codebase imports icons exclusively from **`lucide-react`** (v0.462.0). It is the canonical icon set.

- **Style**: monoline outlined, 1.5–2px stroke. Standard rendered size `h-4 w-4` (16px) inline, `h-5 w-5` (20px) in headers.
- **Common imports** (from `AppSidebar.tsx`, `Dashboard.tsx`): `BarChart3`, `Users`, `Handshake`, `Briefcase`, `DollarSign`, `MessageSquare`, `MessageCircle`, `Calculator`, `UserCog`, `Workflow`, `Bot`, `Brain`, `Settings`, `TrendingUp`, `Calendar`, `User`, `Sparkles`, `Target`, `Presentation`, `Zap`, `Activity`, `Clock`, `AlertTriangle`, `PieChart`, `ChevronDown`.
- **Color**: inherit text color. On navy sidebar: white. On white cards: `gx-primary` for primary accents, `gx-secondary-dark` (gold) for emphasis.
- **No emoji.** Never. Even in casual contexts.
- **No PNG icons.** Lucide SVG only.
- **No unicode glyphs as icons.** Use a Lucide component.
- **Logo treatments**: see `assets/`. Use the white version on navy/dark, the blue+gold version on white. Square app-icon version for favicons / app launchers.

When prototyping in an HTML artifact (no React build), include Lucide via CDN:

```html
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<i data-lucide="bar-chart-3" class="w-4 h-4"></i>
<script>lucide.createIcons();</script>
```

This is a **substitute** for the React-component flow; the visual output is identical (same SVG paths). Documented for completeness — flag this swap to consumers of HTML artifacts.

## Caveats / open questions

- The **WhatsApp-style CRM** repos and **Portal-GX** were not pulled in (the Nexus codebase already covers the brand language). If you need WhatsApp-CRM-Extension visual fidelity, import that separately.
- **Slide deck templates** were not provided; sample slides are not included in this system.
- **Lucide icons** are CDN-linked rather than bundled — if you need offline operation, `npm i lucide` and bundle.
- The `index.css` declared `--radius: 0px` (BRUTALIST). Don't reintroduce rounded corners except on avatars/legacy pill badges.
- **Fonts** are loaded from Google Fonts (Inter + JetBrains Mono). The codebase declares them as `var(--font-sans)` / `var(--font-mono)` but never ships the font files. If you need true offline fidelity, host them; otherwise the Google Fonts CDN matches the codebase's runtime behavior exactly.
