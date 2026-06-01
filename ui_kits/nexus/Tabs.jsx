// Tabs.jsx — brutalist underline
const Tabs = ({ tabs, value, onChange }) => (
  <div style={{ display: 'flex', gap: 32, borderBottom: '1px solid var(--gx-border)' }}>
    {tabs.map(t => {
      const active = value === t.id;
      const ai = t.id === 'ai';
      const accent = ai ? 'var(--gx-primary)' : 'var(--gx-secondary-dark)';
      return (
        <span
          key={t.id}
          onClick={() => onChange(t.id)}
          style={{
            padding: '8px 0', fontSize: 12, fontWeight: 900,
            letterSpacing: '0.15em', textTransform: 'uppercase',
            color: active ? accent : 'rgba(135,112,74,0.5)',
            borderBottom: '2px solid', borderColor: active ? accent : 'transparent',
            cursor: 'pointer', transition: 'all .2s',
            display: 'inline-flex', alignItems: 'center', gap: 8,
          }}
        >
          {t.icon && <i data-lucide={t.icon} style={{ width: 14, height: 14 }}></i>}
          {t.label}
        </span>
      );
    })}
  </div>
);

window.Tabs = Tabs;
