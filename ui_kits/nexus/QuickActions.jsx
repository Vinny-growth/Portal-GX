// QuickActions.jsx — gold panel of 4 actions
const QuickActions = ({ actions }) => (
  <div style={{
    background: 'var(--gx-secondary-dark)', color: '#fff', padding: 28,
    position: 'relative', overflow: 'hidden',
  }}>
    <div style={{ position: 'absolute', right: 0, top: 0, width: '50%', height: '100%', background: 'linear-gradient(to left, rgba(255,255,255,0.05), transparent)', pointerEvents: 'none' }}></div>

    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 28, position: 'relative' }}>
      <div>
        <h3 style={{ fontSize: 22, fontWeight: 900, letterSpacing: '-0.02em', display: 'flex', alignItems: 'center', gap: 8, margin: 0 }}>
          <i data-lucide="zap" style={{ width: 18, height: 18, color: 'var(--gx-primary)' }}></i>
          Comandos Rápidos
        </h3>
        <p style={{ fontSize: 10, textTransform: 'uppercase', letterSpacing: '0.12em', color: 'rgba(255,255,255,0.5)', fontWeight: 700, marginTop: 6 }}>Acesso Direto</p>
      </div>
    </div>

    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 1, background: 'rgba(255,255,255,0.1)', position: 'relative' }}>
      {actions.map((a, i) => {
        const isPrimary = i % 2 === 0;
        return (
          <button
            key={i}
            onClick={a.onClick}
            style={{
              padding: 22, background: 'var(--gx-secondary-dark)', textAlign: 'left',
              display: 'flex', flexDirection: 'column', gap: 6, border: 0, color: '#fff', cursor: 'pointer',
              transition: 'all 0.2s',
            }}
            onMouseEnter={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.05)'}
            onMouseLeave={(e) => e.currentTarget.style.background = 'var(--gx-secondary-dark)'}
          >
            <span style={{ fontSize: 13, fontWeight: 900, letterSpacing: '0.06em', textTransform: 'uppercase', color: isPrimary ? 'var(--gx-primary)' : '#fff' }}>{a.title}</span>
            <span style={{ fontSize: 10, color: 'rgba(255,255,255,0.6)', fontWeight: 500, letterSpacing: '0.04em', textTransform: 'uppercase' }}>{a.desc}</span>
          </button>
        );
      })}
    </div>
  </div>
);

window.QuickActions = QuickActions;
