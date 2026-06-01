// PageHeader.jsx — brutalist dark header with GXC watermark
const PageHeader = ({ eyebrow, title, watermark = 'GXC', right }) => (
  <div style={{
    background: '#0f172a', color: '#fff',
    borderBottom: '1px solid rgba(255,255,255,0.1)',
    padding: '36px 32px 28px', position: 'relative', overflow: 'hidden',
  }}>
    <div style={{
      position: 'absolute', top: -30, right: -10, fontSize: 180,
      fontWeight: 900, letterSpacing: '-0.06em', lineHeight: 0.8,
      opacity: 0.05, pointerEvents: 'none', userSelect: 'none', whiteSpace: 'nowrap',
    }}>{watermark}</div>

    <div style={{ position: 'relative', zIndex: 1, display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 24, flexWrap: 'wrap' }}>
      <div>
        <h1 style={{ fontSize: 56, fontWeight: 900, lineHeight: 0.92, letterSpacing: '-0.04em', textTransform: 'uppercase', margin: 0 }}>{title}</h1>
        <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginTop: 14 }}>
          <span style={{ height: 2, width: 36, background: 'var(--gx-primary)' }}></span>
          <span style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'var(--gx-primary)' }}>{eyebrow}</span>
        </div>
      </div>
      {right && <div>{right}</div>}
    </div>
  </div>
);

window.PageHeader = PageHeader;
