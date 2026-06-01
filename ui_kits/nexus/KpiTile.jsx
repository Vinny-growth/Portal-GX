// KpiTile.jsx — dark / light KPI variants
const KpiTile = ({ variant = 'light', label, value, sub, icon, accent, progress }) => {
  if (variant === 'dark') {
    return (
      <div style={{
        position: 'relative', overflow: 'hidden',
        background: 'var(--gx-secondary-dark)', color: '#fff',
        borderBottom: '4px solid var(--gx-primary)',
        padding: 24, minHeight: 160, display: 'flex', flexDirection: 'column', justifyContent: 'space-between',
      }}>
        <div style={{ position: 'absolute', top: 0, right: 0, width: '50%', height: '100%', background: 'linear-gradient(to left, rgba(255,255,255,0.05), transparent)', pointerEvents: 'none' }}></div>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', position: 'relative' }}>
          <span style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', fontWeight: 700, color: 'rgba(255,255,255,0.5)' }}>{label}</span>
          {icon && <i data-lucide={icon} style={{ width: 18, height: 18, color: 'var(--gx-secondary-light)' }}></i>}
        </div>
        <div style={{ position: 'relative', minWidth: 0 }}>
          <div style={{ fontFamily: 'var(--font-mono)', fontWeight: 900, fontSize: 'clamp(20px, 2.4vw, 30px)', lineHeight: 1, letterSpacing: '-0.02em', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{value}</div>
          {sub && <div style={{ fontSize: 10, letterSpacing: '0.1em', textTransform: 'uppercase', fontWeight: 600, color: 'rgba(255,255,255,0.4)', marginTop: 8 }}>{sub}</div>}
        </div>
      </div>
    );
  }

  return (
    <div style={{ background: '#fff', border: '1px solid var(--gx-border)', padding: 24, minHeight: 160, display: 'flex', flexDirection: 'column', justifyContent: 'space-between' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
        <span style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', fontWeight: 700, color: 'var(--gx-secondary-dark)', opacity: 0.7 }}>{label}</span>
        {icon && <i data-lucide={icon} style={{ width: 18, height: 18, color: 'var(--gx-primary)' }}></i>}
      </div>
      <div>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
          <div style={{ minWidth: 0, flex: 1 }}>
            <div style={{ fontFamily: 'var(--font-mono)', fontWeight: 900, fontSize: 'clamp(18px, 2vw, 26px)', lineHeight: 1, letterSpacing: '-0.02em', color: 'var(--gx-primary-dark)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{value}</div>
            {sub && <div style={{ fontSize: 10, letterSpacing: '0.1em', textTransform: 'uppercase', fontWeight: 600, color: 'var(--fg-muted, #5a6a80)', marginTop: 6 }}>{sub}</div>}
          </div>
          {accent && <div style={{ display: 'flex', alignItems: 'baseline', gap: 2 }}>
            <span style={{ fontFamily: 'var(--font-mono)', fontWeight: 900, fontSize: 36, color: 'var(--gx-primary-dark)' }}>{accent.value}</span>
            <span style={{ fontWeight: 700, color: 'var(--fg-muted, #5a6a80)' }}>{accent.unit}</span>
          </div>}
        </div>
        {typeof progress === 'number' && (
          <div style={{ height: 4, background: 'rgba(0,0,0,0.08)', marginTop: 14 }}>
            <div style={{ height: '100%', background: progress >= 100 ? '#16a34a' : 'var(--gx-primary)', width: Math.min(100, progress) + '%', transition: 'width 1s' }}></div>
          </div>
        )}
      </div>
    </div>
  );
};

window.KpiTile = KpiTile;
