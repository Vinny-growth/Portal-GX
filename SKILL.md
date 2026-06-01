---
name: gx-capital-design
description: Use this skill to generate well-branded interfaces and assets for GX Capital (Brazilian wealth/capital firm), either for production or throwaway prototypes/mocks. Contains essential design guidelines, colors (deep navy + antique champagne gold), type (Inter + JetBrains Mono), assets, and UI kit components for prototyping the Nexus platform.
user-invocable: true
---

Read the README.md file within this skill, and explore the other available files.

If creating visual artifacts (slides, mocks, throwaway prototypes, etc), copy assets out of `assets/` and create static HTML files for the user to view, importing `colors_and_type.css` for tokens. If working on production code, you can copy assets and read the rules here to become an expert in designing with this brand.

If the user invokes this skill without any other guidance, ask them what they want to build or design, ask some questions, and act as an expert designer who outputs HTML artifacts _or_ production code, depending on the need.

Key reference points:
- `colors_and_type.css` — all tokens (CSS vars).
- `README.md` — content fundamentals + visual foundations + iconography.
- `ui_kits/nexus/` — interactive UI kit recreating the GX Capital Nexus AI app (sidebar, KPIs, CRM, mesa de operações). Components are JSX with `window.*` exports for inter-script use; copy patterns liberally.
- `assets/logo-*.png` — brand marks. Use square app icon on navy backgrounds, horizontal lockup on white.
- `preview/` — design-system specimen cards that document each token visually.

Brand essence: **brutalist financial — sharp 0px corners, hard offset shadows, uppercase tracking-widest labels, deep navy + champagne gold, JetBrains Mono numerals**. Never use rounded corners, gradients with bluish-purple, emoji, or hand-drawn SVG illustration.
