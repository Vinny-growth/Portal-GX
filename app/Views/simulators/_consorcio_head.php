<?= view('marketing/_shared_styles'); ?>
<style>
.gx-consorcio {
    position: relative;
    background:
        radial-gradient(circle at top right, rgba(201,169,106,0.14), transparent 34%),
        linear-gradient(180deg, #f5f3ee 0%, #ffffff 38%, #ebe6dc 100%);
}

.gx-consorcio-hero {
    position: relative;
    padding: 122px 0 56px;
    overflow: hidden;
    opacity: 1;
}

.gx-consorcio-hero::before {
    content: "";
    position: absolute;
    inset: 24px auto auto -140px;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(12,49,99,0.1), rgba(12,49,99,0));
    pointer-events: none;
}

.gx-consorcio-hero-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
    gap: 34px;
    align-items: start;
}

.gx-consorcio-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 34px;
    padding: 0 14px;
    border: 1px solid rgba(12,49,99,0.22);
    background: rgba(255,255,255,0.94);
    color: #0c3163;
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gx-consorcio-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--gx-gold);
    box-shadow: 0 0 0 6px rgba(201,169,106,0.16);
}

.gx-consorcio-title {
    margin: 18px 0 0;
    max-width: 14ch;
    font-family: var(--gx-font-heading);
    font-size: clamp(34px, 6vw, 58px);
    line-height: 1.02;
    letter-spacing: -0.03em;
    color: #0c3163;
    opacity: 1;
}

.gx-consorcio-copy {
    margin: 18px 0 0;
    max-width: 60ch;
    font-size: 17px;
    line-height: 1.8;
    color: #000d23;
}

.gx-consorcio-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 28px;
}

.gx-consorcio-hero .gx-btn-primary {
    background: #0c3163;
    color: #FFFFFF;
}

.gx-consorcio-hero .gx-btn-primary:hover {
    background: rgba(12,49,99,0.9);
    color: #FFFFFF;
}

.gx-consorcio-hero .gx-btn-ghost {
    border-color: #0c3163;
    color: #0c3163;
}

.gx-consorcio-hero .gx-btn-ghost:hover {
    border-color: #0c3163;
    background: rgba(12,49,99,0.06);
    color: #0c3163;
}

.gx-consorcio-reassurance {
    width: 100%;
    margin: 0;
    font-size: 14px;
    color: #5a6a80;
    line-height: 1.5;
}

.gx-consorcio-proof-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 28px;
}

.gx-consorcio-proof-item {
    padding: 18px 18px 16px;
    background: rgba(255,255,255,0.86);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 14px 34px rgba(12,49,99,0.04);
}

.gx-consorcio-proof-item strong {
    display: block;
    margin-bottom: 6px;
    font-family: var(--gx-font-heading);
    font-size: 15px;
    color: var(--gx-navy);
}

.gx-consorcio-proof-item span {
    display: block;
    font-size: 13px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

.gx-consorcio-hero-card {
    display: grid;
    gap: 22px;
    padding: 30px;
    background:
        linear-gradient(160deg, rgba(255,255,255,0.96) 0%, rgba(255,255,255,0.92) 42%, rgba(244,234,213,0.94) 100%);
    border: 1px solid rgba(12,49,99,0.1);
    box-shadow: 0 26px 64px rgba(12,49,99,0.12);
}

.gx-consorcio-hero-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}

.gx-consorcio-hero-card-top span:first-child {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    background: rgba(12,49,99,0.07);
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gx-consorcio-hero-card-top span:last-child {
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 600;
    color: var(--gx-gold-hover);
}

.gx-consorcio-hero-card h2 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 26px;
    line-height: 1.12;
    color: var(--gx-navy);
}

.gx-consorcio-hero-card p {
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-consorcio-stat-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.gx-consorcio-stat {
    padding: 18px 16px;
    background: rgba(255,255,255,0.84);
    border: 1px solid rgba(12,49,99,0.08);
}

.gx-consorcio-stat strong {
    display: block;
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 26px;
    letter-spacing: -0.02em;
    line-height: 1;
    color: var(--gx-navy);
}

.gx-consorcio-stat span {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    line-height: 1.5;
    color: var(--gx-text-secondary);
}

.gx-consorcio-signal-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-consorcio-signal-list li {
    position: relative;
    padding-left: 18px;
    font-size: 13px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

.gx-consorcio-signal-list li::before {
    content: "";
    position: absolute;
    top: 0.7em;
    left: 0;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--gx-gold);
}

.gx-consorcio-track-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.gx-consorcio-track-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 100%;
    padding: 26px 22px 22px;
    background: rgba(255,255,255,0.94);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 18px 44px rgba(12,49,99,0.05);
    transition: transform 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
}

.gx-consorcio-track-card.is-active,
.gx-consorcio-track-card:hover {
    transform: translateY(-2px);
    border-color: rgba(201,169,106,0.62);
    box-shadow: 0 24px 56px rgba(12,49,99,0.1);
}

.gx-consorcio-track-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gx-gold) 0%, rgba(201,169,106,0.1) 100%);
}

.gx-consorcio-track-card ul {
    display: grid;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-consorcio-track-card li {
    position: relative;
    padding-left: 16px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-consorcio-track-card li::before {
    content: "";
    position: absolute;
    top: 0.65em;
    left: 0;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(12,49,99,0.6);
}

.gx-consorcio-card-action {
    margin-top: auto;
}

.gx-consorcio-workbench {
    display: grid;
    grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
    gap: 28px;
    align-items: start;
}

.gx-consorcio-main-card,
.gx-consorcio-results-card {
    padding: 28px;
    background: rgba(255,255,255,0.96);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 22px 56px rgba(12,49,99,0.07);
}

.gx-consorcio-main-card {
    display: grid;
    gap: 24px;
}

.gx-consorcio-switcher {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.gx-consorcio-switch {
    display: grid;
    gap: 6px;
    padding: 16px 16px 14px;
    border: 1px solid rgba(12,49,99,0.08);
    background: var(--gx-bg);
    color: var(--gx-text-secondary);
    text-align: left;
    cursor: pointer;
    transition: border-color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
}

.gx-consorcio-switch:hover,
.gx-consorcio-switch.is-active {
    border-color: rgba(201,169,106,0.65);
    background: rgba(201,169,106,0.08);
    color: var(--gx-navy);
    transform: translateY(-1px);
}

.gx-consorcio-switch strong {
    font-family: var(--gx-font-heading);
    font-size: 15px;
    line-height: 1.2;
}

.gx-consorcio-switch span {
    font-size: 12px;
    line-height: 1.5;
}

.gx-consorcio-info-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 16px;
    padding: 14px 16px;
    background: rgba(12,49,99,0.04);
    border: 1px solid rgba(12,49,99,0.08);
    font-size: 13px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-consorcio-form-grid {
    display: grid;
    gap: 20px;
}

.gx-consorcio-fieldset {
    display: grid;
    gap: 16px;
    padding: 22px 20px;
    border: 1px solid rgba(12,49,99,0.08);
    background: rgba(250,250,248,0.74);
}

.gx-consorcio-fieldset-head {
    display: grid;
    gap: 6px;
}

.gx-consorcio-fieldset-head h3 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 18px;
    color: var(--gx-navy);
}

.gx-consorcio-fieldset-head p {
    margin: 0;
    font-size: 13px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-consorcio-field-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.gx-consorcio-field-grid.is-wide {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.gx-consorcio-field {
    display: grid;
    gap: 6px;
}

.gx-consorcio-field-label {
    display: block;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: var(--gx-text-secondary);
}

.gx-consorcio-field small {
    display: block;
    font-size: 12px;
    line-height: 1.55;
    color: var(--gx-text-tertiary);
}

.gx-consorcio-input-shell {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    height: 46px;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.gx-consorcio-input-shell:focus-within {
    border-color: var(--gx-navy-50);
    box-shadow: 0 0 0 3px var(--gx-navy-08);
}

.gx-consorcio-input-shell input {
    width: 100%;
    height: 44px;
    padding: 10px 14px;
    border: 0;
    background: transparent;
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    font-size: 15px;
    outline: 0;
}

.gx-consorcio-input-prefix,
.gx-consorcio-input-suffix {
    display: inline-flex;
    align-items: center;
    height: 100%;
    padding: 0 14px;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 700;
    color: var(--gx-text-tertiary);
}

.gx-consorcio-input-prefix {
    border-right: 1px solid rgba(12,49,99,0.06);
}

.gx-consorcio-input-prefix.is-empty {
    width: 0;
    min-width: 0;
    padding: 0;
    border-right: 0;
}

.gx-consorcio-input-suffix {
    border-left: 1px solid rgba(12,49,99,0.06);
}

.gx-consorcio-card-action .gx-text-link {
    padding: 0;
    border: 0;
    background: transparent;
    cursor: pointer;
}

.gx-consorcio-side {
    position: sticky;
    top: 92px;
    display: grid;
    gap: 18px;
}

.gx-consorcio-results-card {
    display: grid;
    gap: 18px;
}

.gx-consorcio-results-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
}

.gx-consorcio-status-chip {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 0 10px;
    background: rgba(12,49,99,0.06);
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.gx-consorcio-results-head h3 {
    margin: 6px 0 0;
    font-family: var(--gx-font-heading);
    font-size: 24px;
    line-height: 1.15;
    color: var(--gx-navy);
}

.gx-consorcio-results-head p {
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-consorcio-kpi-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.gx-consorcio-kpi {
    display: grid;
    gap: 8px;
    padding: 18px 16px;
    background: rgba(250,250,248,0.82);
    border: 1px solid rgba(12,49,99,0.08);
}

.gx-consorcio-kpi span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-text-tertiary);
}

.gx-consorcio-kpi strong {
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 24px;
    letter-spacing: -0.02em;
    line-height: 1.05;
    color: var(--gx-navy);
}

.gx-consorcio-kpi p {
    margin: 0;
    font-size: 12px;
    line-height: 1.55;
    color: var(--gx-text-secondary);
}

.gx-consorcio-band {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.gx-consorcio-band-card {
    display: grid;
    gap: 6px;
    padding: 18px 16px;
    background: linear-gradient(180deg, rgba(12,49,99,0.05), rgba(12,49,99,0.02));
    border: 1px solid rgba(12,49,99,0.08);
}

.gx-consorcio-band-card span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-text-tertiary);
}

.gx-consorcio-band-card strong {
    font-family: var(--gx-font-heading);
    font-size: 19px;
    line-height: 1.2;
    color: var(--gx-navy);
}

.gx-consorcio-band-card p {
    margin: 0;
    font-size: 12px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-consorcio-insights {
    display: grid;
    gap: 10px;
}

.gx-consorcio-insights h4 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 15px;
    color: var(--gx-navy);
}

.gx-consorcio-insight-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-consorcio-insight-list li {
    position: relative;
    padding: 14px 14px 14px 34px;
    border: 1px solid rgba(12,49,99,0.08);
    background: rgba(255,255,255,0.94);
    font-size: 13px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

.gx-consorcio-insight-list li::before {
    content: "";
    position: absolute;
    top: 19px;
    left: 14px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--gx-gold);
    box-shadow: 0 0 0 6px rgba(201,169,106,0.15);
}

.gx-consorcio-disclaimer {
    font-size: 12px;
    line-height: 1.6;
    color: var(--gx-text-tertiary);
}

.gx-consorcio-lead-card {
    display: grid;
    gap: 18px;
    padding: 30px 28px;
}

.gx-consorcio-context-box {
    display: grid;
    gap: 6px;
    padding: 16px;
    background: rgba(201,169,106,0.1);
    border: 1px solid rgba(201,169,106,0.25);
}

.gx-consorcio-context-box span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-consorcio-context-box strong {
    font-family: var(--gx-font-heading);
    font-size: 17px;
    line-height: 1.3;
    color: var(--gx-navy);
}

.gx-consorcio-context-box p {
    margin: 0;
    font-size: 13px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

.gx-consorcio-promise-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-consorcio-promise-list li {
    position: relative;
    padding-left: 18px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-consorcio-promise-list li::before {
    content: "";
    position: absolute;
    top: 0.7em;
    left: 0;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(12,49,99,0.72);
}

.gx-consorcio-lead-form {
    display: grid;
    gap: 16px;
}

.gx-consorcio-lead-status[hidden] {
    display: none;
}

.gx-consorcio-schedule-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 12px;
    padding: 36px 24px;
    border-radius: 0;
    background: linear-gradient(135deg, rgba(201,169,106,0.06) 0%, rgba(12,49,99,0.04) 100%);
    border: 1px solid rgba(201,169,106,0.18);
}
.gx-consorcio-schedule-step[hidden] {
    display: none;
}
.gx-schedule-icon {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(201,169,106,0.10);
}
.gx-schedule-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #c9a96a;
    background: rgba(201,169,106,0.10);
    padding: 4px 12px;
    border-radius: 0;
}
.gx-schedule-title {
    font-size: 20px;
    font-weight: 700;
    color: #0c3163;
    line-height: 1.3;
    margin: 4px 0 0;
}
.gx-schedule-desc {
    font-size: 14px;
    line-height: 1.55;
    color: #475569;
    max-width: 380px;
    margin: 0;
}
.gx-schedule-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}
.gx-schedule-alt {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}
.gx-schedule-alt p {
    font-size: 13px;
    color: #94a3b8;
    margin: 0;
}
.gx-schedule-alt-links {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}
.gx-schedule-note {
    font-size: 12px;
    color: #94a3b8;
    margin: 4px 0 0;
    font-style: italic;
}

.gx-consorcio-inline-links {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.gx-consorcio-inline-link {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 12px;
    border: 1px solid rgba(12,49,99,0.12);
    background: rgba(12,49,99,0.03);
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 600;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

.gx-consorcio-inline-link:hover {
    background: rgba(12,49,99,0.06);
    border-color: rgba(12,49,99,0.18);
}

.gx-consorcio-ai-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.gx-consorcio-ai-card {
    display: grid;
    gap: 14px;
    padding: 26px 22px;
    background: rgba(255,255,255,0.95);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 18px 44px rgba(12,49,99,0.05);
}

.gx-consorcio-ai-step {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: rgba(12,49,99,0.08);
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 15px;
    font-weight: 700;
}

.gx-consorcio-ai-card h3 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 18px;
    line-height: 1.3;
    color: var(--gx-navy);
}

.gx-consorcio-ai-card p {
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-consorcio-ai-card ul {
    display: grid;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-consorcio-ai-card li {
    position: relative;
    padding-left: 16px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-consorcio-ai-card li::before {
    content: "";
    position: absolute;
    top: 0.68em;
    left: 0;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--gx-gold);
}

.gx-consorcio-callout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 18px;
    align-items: center;
    padding: 28px 30px;
    background: linear-gradient(135deg, rgba(12,49,99,0.96), rgba(6,33,61,0.92));
    color: rgba(255,255,255,0.84);
    box-shadow: 0 28px 68px rgba(12,49,99,0.18);
}

.gx-consorcio-callout h2 {
    margin: 10px 0 0;
    max-width: 18ch;
    font-family: var(--gx-font-heading);
    font-size: clamp(28px, 4vw, 40px);
    line-height: 1.04;
    color: #FFFFFF;
}

.gx-consorcio-callout p {
    margin: 14px 0 0;
    max-width: 62ch;
    font-size: 15px;
    line-height: 1.75;
}

.gx-consorcio-callout .gx-label {
    color: rgba(255,255,255,0.7);
}

.gx-consorcio-callout-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: flex-end;
}

.gx-consorcio-callout .gx-btn-ghost {
    border-color: rgba(255,255,255,0.18);
    color: #FFFFFF;
}

.gx-consorcio-callout .gx-btn-ghost:hover {
    border-color: rgba(255,255,255,0.32);
    background: rgba(255,255,255,0.06);
}

@media (max-width: 1100px) {
    .gx-consorcio-hero-grid,
    .gx-consorcio-workbench,
    .gx-consorcio-callout {
        grid-template-columns: 1fr;
    }

    .gx-consorcio-side {
        position: static;
    }

    .gx-consorcio-proof-grid,
    .gx-consorcio-track-grid,
    .gx-consorcio-ai-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-consorcio-callout-actions {
        justify-content: flex-start;
    }
}

@media (max-width: 780px) {
    .gx-consorcio-hero {
        padding-top: 108px;
    }

    .gx-consorcio-proof-grid,
    .gx-consorcio-track-grid,
    .gx-consorcio-field-grid,
    .gx-consorcio-kpi-grid,
    .gx-consorcio-band,
    .gx-consorcio-ai-grid,
    .gx-consorcio-stat-grid,
    .gx-consorcio-switcher {
        grid-template-columns: 1fr;
    }

    .gx-consorcio-main-card,
    .gx-consorcio-results-card,
    .gx-consorcio-lead-card,
    .gx-consorcio-callout {
        padding: 24px 20px;
    }

    .gx-consorcio-title {
        max-width: none;
    }
}

@media (max-width: 640px) {
    .gx-consorcio-copy {
        font-size: 15px;
    }

    .gx-consorcio-actions,
    .gx-consorcio-inline-links {
        flex-direction: column;
        align-items: stretch;
    }

    .gx-consorcio-actions .gx-btn,
    .gx-consorcio-callout-actions .gx-btn {
        width: 100%;
    }
}

/* ── CRO: reduce section spacing (max ~80px between sections) ── */
.gx-consorcio .gx-section {
    padding: 48px 0;
}

.gx-consorcio .gx-section-header {
    margin-bottom: 36px;
}

.gx-consorcio .gx-strip {
    padding: 14px 0;
}

.gx-consorcio .gx-divider {
    margin: 0 auto;
}

@media (max-width: 780px) {
    .gx-consorcio .gx-section {
        padding: 36px 0;
    }

    .gx-consorcio .gx-section-header {
        margin-bottom: 28px;
    }
}

/* ── Priority 5: Quick lead form after hero ── */
.gx-consorcio-quick-lead {
    padding: 36px 0;
    background: var(--gx-bg-cool);
}

.gx-quick-lead-card {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
    align-items: center;
    padding: 28px 30px;
    background: #FFFFFF;
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 14px 34px rgba(12,49,99,0.05);
}

.gx-quick-lead-text h2 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 22px;
    line-height: 1.2;
    color: #0c3163;
}

.gx-quick-lead-text p {
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.6;
    color: #555555;
}

.gx-quick-lead-fields {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 10px;
    align-items: end;
}

.gx-quick-lead-input {
    width: 100%;
    height: 46px;
    padding: 10px 14px;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    font-size: 15px;
    border-radius: var(--gx-radius);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.gx-quick-lead-input:focus {
    outline: 0;
    border-color: var(--gx-navy-50);
    box-shadow: 0 0 0 3px var(--gx-navy-08);
}

.gx-quick-lead-btn {
    white-space: nowrap;
}

.gx-quick-lead-trust {
    grid-column: 1 / -1;
    margin: 4px 0 0;
    font-size: 12px;
    color: var(--gx-text-tertiary);
    text-align: right;
}

@media (max-width: 780px) {
    .gx-quick-lead-card {
        grid-template-columns: 1fr;
        padding: 24px 20px;
    }

    .gx-quick-lead-fields {
        grid-template-columns: 1fr;
    }

    .gx-quick-lead-trust {
        text-align: center;
    }
}

/* ── Priority 6: Mini simulator in hero ── */
.gx-mini-sim-body {
    display: grid;
    gap: 18px;
}

.gx-mini-sim-field {
    display: grid;
    gap: 6px;
}

.gx-mini-sim-field label {
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: var(--gx-text-secondary);
}

.gx-mini-sim-output {
    font-family: var(--gx-font-heading);
    font-size: 22px;
    font-weight: 700;
    color: #0c3163;
    line-height: 1;
}

.gx-mini-sim-range {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    background: rgba(12,49,99,0.1);
    border-radius: 0;
    outline: none;
    cursor: pointer;
}

.gx-mini-sim-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #0c3163;
    border: 3px solid #FFFFFF;
    box-shadow: 0 2px 8px rgba(12,49,99,0.25);
    cursor: pointer;
}

.gx-mini-sim-range::-moz-range-thumb {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #0c3163;
    border: 3px solid #FFFFFF;
    box-shadow: 0 2px 8px rgba(12,49,99,0.25);
    cursor: pointer;
}

.gx-mini-sim-range-labels {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: var(--gx-text-tertiary);
}

.gx-mini-sim-btn {
    width: 100%;
    margin-top: 4px;
}

.gx-mini-sim-result {
    padding: 18px;
    background: rgba(12,49,99,0.05);
    border: 1px solid rgba(12,49,99,0.1);
    text-align: center;
    animation: gxSlideDown 0.4s var(--gx-ease);
}

.gx-mini-sim-result strong {
    display: block;
    font-family: var(--gx-font-heading);
    font-size: 20px;
    color: #0c3163;
}

.gx-mini-sim-result p {
    margin: 6px 0 0;
    font-size: 13px;
    color: var(--gx-text-secondary);
}

.gx-mini-sim-result .gx-text-link {
    display: inline-block;
    margin-top: 10px;
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 600;
    color: var(--gx-gold-hover);
}

@keyframes gxSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Priority 8: Testimonials ── */
.gx-testimonial-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.gx-testimonial-card {
    display: grid;
    gap: 12px;
    padding: 26px 22px;
    background: #FFFFFF;
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: 0 14px 34px rgba(12,49,99,0.04);
}

.gx-testimonial-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: var(--gx-bg-cool);
    border: 2px solid var(--gx-gold);
}

.gx-testimonial-avatar-photo {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.gx-testimonial-stars {
    font-size: 16px;
    color: var(--gx-gold);
    letter-spacing: 2px;
}

.gx-testimonial-card blockquote {
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-testimonial-card footer {
    font-size: 13px;
    color: var(--gx-text-tertiary);
}

.gx-testimonial-card footer strong {
    color: #0c3163;
    font-family: var(--gx-font-heading);
    font-weight: 600;
}

@media (max-width: 780px) {
    .gx-testimonial-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
}

/* ── Priority 10: Floating WhatsApp button ── */
.gx-fab-whatsapp {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #25D366;
    color: #FFFFFF;
    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    animation: gxFabPulse 5s ease-in-out infinite;
}

.gx-fab-whatsapp:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 24px rgba(0,0,0,0.24);
    color: #FFFFFF;
}

.gx-fab-whatsapp svg {
    width: 28px;
    height: 28px;
}

@keyframes gxFabPulse {
    0%, 85%, 100% { box-shadow: 0 4px 16px rgba(0,0,0,0.18); }
    90% { box-shadow: 0 4px 16px rgba(0,0,0,0.18), 0 0 0 8px rgba(37,211,102,0.2); }
    95% { box-shadow: 0 4px 16px rgba(0,0,0,0.18), 0 0 0 16px rgba(37,211,102,0); }
}

@media (max-width: 991px) {
    .gx-fab-whatsapp {
        display: none;
    }
}

/* ── Priority 11: Exit intent popup ── */
.gx-exit-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.55);
    animation: gxFadeIn 0.3s ease;
}

.gx-exit-overlay[hidden] {
    display: none;
}

.gx-exit-card {
    position: relative;
    width: 90%;
    max-width: 420px;
    padding: 36px 30px;
    background: #FFFFFF;
    box-shadow: 0 32px 72px rgba(0,0,0,0.2);
    text-align: center;
    animation: gxSlideDown 0.4s var(--gx-ease);
}

.gx-exit-close {
    position: absolute;
    top: 12px;
    right: 16px;
    background: transparent;
    border: 0;
    font-size: 28px;
    color: var(--gx-text-tertiary);
    cursor: pointer;
    line-height: 1;
}

.gx-exit-card h2 {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 22px;
    line-height: 1.2;
    color: #0c3163;
}

.gx-exit-card > p {
    margin: 10px 0 0;
    font-size: 15px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-exit-form {
    display: grid;
    gap: 12px;
    margin-top: 20px;
}

.gx-exit-input {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    border: 1px solid var(--gx-border);
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    font-size: 16px;
    text-align: center;
}

.gx-exit-input:focus {
    outline: 0;
    border-color: var(--gx-navy-50);
    box-shadow: 0 0 0 3px var(--gx-navy-08);
}

.gx-exit-submit {
    width: 100%;
}

.gx-exit-dismiss {
    display: inline-block;
    margin-top: 14px;
    padding: 0;
    background: transparent;
    border: 0;
    font-size: 13px;
    color: var(--gx-text-tertiary);
    cursor: pointer;
    text-decoration: underline;
}

@keyframes gxFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ══════════════════════════════════════════════════════════ */
/* Vault Modernism — Consórcio Simulator                       */
/* ══════════════════════════════════════════════════════════ */

.gx-consorcio {
    font-family: var(--gx-font-sans-refined);
    background:
        radial-gradient(ellipse 900px 420px at 85% 12%, rgba(201,169,106,0.1) 0%, transparent 65%),
        radial-gradient(ellipse 700px 500px at 10% 100%, rgba(12,49,99,0.04) 0%, transparent 70%),
        linear-gradient(180deg, #FFFFFF 0%, var(--gx-vellum) 100%);
}

.gx-consorcio .gx-hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(201,169,106,0.35) 50%, transparent 100%);
    z-index: 2;
}

/* Hero — tipografia editorial */
.gx-consorcio-badge {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-navy-deep);
    border: 1px solid rgba(201,169,106,0.4);
    background: rgba(255,255,255,0.78);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.gx-consorcio-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(42px, 6vw, 72px);
    line-height: 1;
    letter-spacing: -0.02em;
    color: var(--gx-navy-deep);
    max-width: 14ch;
}

.gx-consorcio-title em,
.gx-consorcio-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-consorcio-copy {
    font-family: var(--gx-font-sans-refined);
    font-size: 17px;
    line-height: 1.65;
    color: var(--gx-navy-70);
    letter-spacing: -0.003em;
    max-width: 54ch;
}

/* Hero buttons — vault treatment */
.gx-consorcio-hero .gx-btn {
    font-family: var(--gx-font-heading);
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-size: 12.5px;
    white-space: normal;
    word-break: break-word;
    max-width: 100%;
}

.gx-consorcio-hero .gx-btn-primary {
    background: var(--gx-gold-etched);
    color: #FFFFFF;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.25),
        inset 0 0 0 1px rgba(255,255,255,0.08),
        0 14px 30px rgba(201,169,106,0.22);
}

.gx-consorcio-hero .gx-btn-primary:hover {
    background: var(--gx-navy-deep);
    color: #FFFFFF;
}

.gx-consorcio-hero .gx-btn-ghost {
    border: 1px solid rgba(12,49,99,0.22);
    color: var(--gx-navy-deep);
    box-shadow: inset 0 0 0 1px rgba(201,169,106,0.1);
}

.gx-consorcio-hero .gx-btn-ghost:hover {
    border-color: var(--gx-gold);
    background: rgba(251,247,238,0.8);
    box-shadow: inset 0 0 0 1px rgba(201,169,106,0.3);
    color: var(--gx-navy-deep);
}

/* Proof items com oldstyle nums */
.gx-consorcio-proof-item {
    padding: 20px 22px;
    background: rgba(255,255,255,0.88);
    border: 1px solid rgba(12,49,99,0.08);
    border-left: 2px solid var(--gx-gold);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.7),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
}

.gx-consorcio-proof-item strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    font-size: 22px;
    color: var(--gx-gold-etched);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    text-transform: none;
    letter-spacing: -0.01em;
}

.gx-consorcio-proof-item span {
    font-family: var(--gx-font-sans-refined);
    font-size: 12.5px;
    color: var(--gx-navy-70);
}

/* Hero card — vault frame */
.gx-consorcio-hero-card {
    padding: 32px;
    background: linear-gradient(160deg, #FFFFFF 0%, var(--gx-vellum) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.85),
        0 0 0 1px rgba(201,169,106,0.18),
        0 40px 80px rgba(5,25,52,0.1);
}

.gx-consorcio-hero-card h2 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 30px;
    line-height: 1.05;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-hero-card p {
    font-family: var(--gx-font-sans-refined);
}

/* Hero card top badges */
.gx-consorcio-hero-card-top span:first-child {
    font-family: var(--gx-font-sans-refined);
    letter-spacing: 0.22em;
    background: rgba(201,169,106,0.12);
    color: var(--gx-gold-etched);
    border: 1px solid rgba(201,169,106,0.25);
    padding: 4px 10px;
    font-size: 10px;
}

.gx-consorcio-hero-card-top span:last-child {
    font-family: var(--gx-font-sans-refined);
    letter-spacing: 0.04em;
}

/* Stats no hero card — oldstyle figures */
.gx-consorcio-stat {
    padding: 20px 18px;
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.7);
}

.gx-consorcio-stat strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 28px;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

.gx-consorcio-stat span {
    font-family: var(--gx-font-sans-refined);
    font-size: 11.5px;
    letter-spacing: 0.04em;
}

/* Signal list gold refinado */
.gx-consorcio-signal-list li {
    font-family: var(--gx-font-sans-refined);
    font-size: 13.5px;
    line-height: 1.65;
    padding-left: 20px;
}

.gx-consorcio-signal-list li::before {
    width: 6px;
    height: 6px;
    background: var(--gx-gold);
    box-shadow: 0 0 0 3px rgba(201,169,106,0.2);
    top: 0.6em;
}

/* Section titles (herdados de .gx-home não aplicam, reforço aqui) */
.gx-consorcio .gx-section-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(30px, 4.2vw, 52px);
    line-height: 1.03;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-consorcio .gx-section-title em,
.gx-consorcio .gx-section-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-consorcio .gx-section-desc {
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    line-height: 1.65;
    color: var(--gx-navy-70);
}

.gx-consorcio .gx-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-consorcio .gx-label::before {
    width: 28px;
    background: var(--gx-gold);
}

/* Track cards — vault hairline frame */
.gx-consorcio-track-card {
    padding: 30px 26px 26px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    border-radius: 0;
    transition: transform 0.35s var(--gx-ease), border-color 0.35s ease, box-shadow 0.35s ease;
}

.gx-consorcio-track-card:hover,
.gx-consorcio-track-card.is-active {
    transform: translateY(-4px);
    border-color: rgba(201,169,106,0.4);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.9),
        0 0 0 1px rgba(201,169,106,0.3),
        var(--gx-shadow-md);
}

.gx-consorcio-track-card::before {
    height: 3px;
    background: linear-gradient(90deg, var(--gx-gold-etched) 0%, var(--gx-gold) 100%);
}

.gx-consorcio-track-card h3,
.gx-consorcio-track-card .gx-card-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 24px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-track-card p {
    font-family: var(--gx-font-sans-refined);
    font-size: 14.5px;
    line-height: 1.6;
}

.gx-consorcio-track-card li {
    font-family: var(--gx-font-sans-refined);
}

/* Workbench cards — mesmo frame */
.gx-consorcio-main-card,
.gx-consorcio-results-card,
.gx-consorcio-lead-card {
    padding: 32px 30px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.85),
        0 0 0 1px rgba(201,169,106,0.15),
        0 22px 56px rgba(5,25,52,0.08);
    border-radius: 0;
}

/* Switcher tabs — vault */
.gx-consorcio-switch {
    padding: 18px 18px 16px;
    background: #FFFFFF;
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
    transition: all 0.25s var(--gx-ease);
    border-radius: 0;
}

.gx-consorcio-switch strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 17px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-switch span {
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
}

.gx-consorcio-switch:hover,
.gx-consorcio-switch.is-active {
    border-color: rgba(201,169,106,0.42);
    background: rgba(251,247,238,0.85);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.15),
        0 0 0 1px rgba(201,169,106,0.25);
}

/* Fieldset — editorial */
.gx-consorcio-fieldset {
    padding: 26px 24px;
    background: rgba(251,247,238,0.4);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.5);
    border-radius: 0;
}

.gx-consorcio-fieldset-head h3 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 20px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-fieldset-head p {
    font-family: var(--gx-font-sans-refined);
}

.gx-consorcio-field-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-navy-70);
}

/* Input shells — hairline underline */
.gx-consorcio-input-shell {
    border: none;
    border-bottom: 1px solid rgba(12,49,99,0.18);
    background: transparent;
    border-radius: 0;
    height: 44px;
}

.gx-consorcio-input-shell:focus-within {
    border-bottom-color: var(--gx-gold);
    box-shadow: 0 1px 0 0 var(--gx-gold);
}

.gx-consorcio-input-shell input {
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    color: var(--gx-navy-deep);
    padding: 8px 10px;
    height: 42px;
}

.gx-consorcio-input-prefix,
.gx-consorcio-input-suffix {
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
    letter-spacing: 0.06em;
    color: var(--gx-gold-etched);
    border: none;
    padding: 0 10px;
}

/* Results — oldstyle figures nos KPIs */
.gx-consorcio-results-head h3 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 28px;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-status-chip {
    font-family: var(--gx-font-sans-refined);
    font-size: 10.5px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
    background: rgba(201,169,106,0.1);
    border: 1px solid rgba(201,169,106,0.25);
}

.gx-consorcio-kpi {
    padding: 20px 18px;
    background: #FFFFFF;
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
}

.gx-consorcio-kpi span {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-consorcio-kpi strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 30px;
    line-height: 1.05;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

.gx-consorcio-kpi p {
    font-family: var(--gx-font-sans-refined);
}

.gx-consorcio-band-card {
    padding: 20px 18px;
    background: linear-gradient(180deg, var(--gx-ivory) 0%, rgba(201,169,106,0.06) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
}

.gx-consorcio-band-card span {
    font-family: var(--gx-font-sans-refined);
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-consorcio-band-card strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 22px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
}

/* AI cards */
.gx-consorcio-ai-card {
    padding: 30px 26px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    border-radius: 0;
    transition: transform 0.35s var(--gx-ease), border-color 0.35s ease, box-shadow 0.35s ease;
}

.gx-consorcio-ai-card:hover {
    transform: translateY(-4px);
    border-color: rgba(201,169,106,0.4);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.9),
        0 0 0 1px rgba(201,169,106,0.3),
        var(--gx-shadow-md);
}

.gx-consorcio-ai-step {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    background: rgba(201,169,106,0.12);
    color: var(--gx-gold-etched);
    border: 1px solid rgba(201,169,106,0.25);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    width: 48px;
    height: 48px;
    font-size: 22px;
}

.gx-consorcio-ai-card h3 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 22px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-consorcio-ai-card p,
.gx-consorcio-ai-card li {
    font-family: var(--gx-font-sans-refined);
}

/* Callout navy com frame gold duplo */
.gx-consorcio-callout {
    padding: 80px 60px;
    background:
        radial-gradient(ellipse 600px 300px at 50% 0%, rgba(201,169,106,0.18) 0%, transparent 70%),
        linear-gradient(160deg, var(--gx-navy) 0%, var(--gx-navy-deep) 100%);
    border: 1px solid rgba(201,169,106,0.3);
    border-bottom: 4px solid var(--gx-gold);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.12),
        var(--gx-shadow-lg);
    position: relative;
    overflow: hidden;
    border-radius: 0;
}

.gx-consorcio-callout::before {
    content: "";
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    bottom: 14px;
    border: 1px solid rgba(201,169,106,0.18);
    pointer-events: none;
}

.gx-consorcio-callout::after {
    content: "";
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0 2px, transparent 2px 24px);
    pointer-events: none;
}

.gx-consorcio-callout > * {
    position: relative;
    z-index: 1;
}

.gx-consorcio-callout h2 {
    font-family: var(--gx-font-heading);
    font-weight: 900;
    font-size: clamp(28px, 4vw, 44px);
    letter-spacing: -0.03em;
    line-height: 1.05;
    text-transform: uppercase;
}

.gx-consorcio-callout h2 em,
.gx-consorcio-callout h2 i {
    font-style: normal;
    font-weight: 900;
    color: var(--gx-gold);
}

.gx-consorcio-callout p {
    font-family: var(--gx-font-sans-refined);
}

.gx-consorcio-callout .gx-btn {
    font-family: var(--gx-font-heading);
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-size: 12.5px;
    /* Long labels wrap instead of overflowing the action row. */
    white-space: normal;
    word-break: break-word;
    max-width: 100%;
}

/* Context box */
.gx-consorcio-context-box {
    padding: 20px 22px;
    background: rgba(201,169,106,0.08);
    border: 1px solid rgba(201,169,106,0.22);
    border-left: 2px solid var(--gx-gold);
    border-radius: 0;
}

.gx-consorcio-context-box span {
    font-family: var(--gx-font-sans-refined);
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-consorcio-context-box strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 18px;
    letter-spacing: -0.01em;
}

.gx-consorcio-context-box p {
    font-family: var(--gx-font-sans-refined);
}

/* Mini sim (hero quick simulator) */
.gx-mini-sim-output {
    font-family: var(--gx-font-mono);
    font-weight: 900;
    font-style: normal;
    font-size: 26px;
    color: var(--gx-navy-deep);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.02em;
}

.gx-mini-sim-field label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-navy-70);
}

.gx-mini-sim-result {
    background: rgba(201,169,106,0.08);
    border: 1px solid rgba(201,169,106,0.25);
    border-left: 2px solid var(--gx-gold);
    padding: 20px;
}

.gx-mini-sim-result strong {
    font-family: var(--gx-font-mono);
    font-weight: 900;
    font-size: 22px;
    color: var(--gx-navy-deep);
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.02em;
}

/* Testimonials — vault */
.gx-testimonial-card {
    padding: 30px 26px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    border-radius: 0;
}

.gx-testimonial-card blockquote {
    font-family: var(--gx-font-display);
    font-style: italic;
    font-size: 15.5px;
    line-height: 1.65;
    color: var(--gx-navy-70);
}

.gx-testimonial-card footer strong {
    font-family: var(--gx-font-sans-refined);
    font-weight: 700;
    color: var(--gx-navy-deep);
}

/* Quick lead card */
.gx-quick-lead-card {
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 0 0 1px rgba(201,169,106,0.15),
        var(--gx-shadow-sm);
}

.gx-quick-lead-text h2 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 26px;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-quick-lead-text p {
    font-family: var(--gx-font-sans-refined);
}

.gx-quick-lead-input {
    border: none;
    border-bottom: 1px solid rgba(12,49,99,0.18);
    border-radius: 0;
    background: transparent;
    padding: 10px 0 8px;
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    color: var(--gx-navy-deep);
    height: auto;
    min-height: 42px;
}

.gx-quick-lead-input:focus {
    outline: none;
    border-bottom-color: var(--gx-gold);
    box-shadow: 0 1px 0 0 var(--gx-gold);
}
</style>
