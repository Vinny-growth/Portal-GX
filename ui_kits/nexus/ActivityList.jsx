// ActivityList.jsx — recent activity rows
const ActivityList = ({ items }) => (
  <div style={{ background: '#fff', border: '1px solid var(--gx-border)', padding: 24 }}>
    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 18 }}>
      <span style={{ height: 2, width: 32, background: 'var(--gx-primary)' }}></span>
      <h3 style={{ fontSize: 12, fontWeight: 900, letterSpacing: '0.15em', textTransform: 'uppercase', color: 'var(--gx-secondary-dark)', margin: 0 }}>Atividades Recentes</h3>
    </div>
    <div style={{ display: 'flex', flexDirection: 'column' }}>
      {items.map((it, i) => (
        <div key={i} style={{
          display: 'flex', alignItems: 'center', gap: 14,
          padding: '12px 0', borderBottom: i === items.length - 1 ? 0 : '1px solid var(--gx-border)',
        }}>
          <div style={{
            width: 32, height: 32, background: it.tone === 'gold' ? 'var(--gx-secondary-light)' : 'var(--gx-primary)',
            color: it.tone === 'gold' ? 'var(--gx-primary)' : 'var(--gx-secondary-light)',
            display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
          }}>
            <i data-lucide={it.icon} style={{ width: 14, height: 14 }}></i>
          </div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: 'var(--gx-primary-dark)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{it.title}</div>
            <div style={{ fontSize: 11, color: 'var(--fg-muted, #5a6a80)' }}>{it.desc}</div>
          </div>
          <div style={{ fontFamily: 'var(--font-mono)', fontSize: 11, color: 'var(--fg-muted, #5a6a80)', flexShrink: 0 }}>{it.time}</div>
        </div>
      ))}
    </div>
  </div>
);

window.ActivityList = ActivityList;
