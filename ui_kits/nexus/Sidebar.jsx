// Sidebar.jsx — navy gradient sidebar with collapsible groups
const { useState } = React;

const NavItem = ({ icon, label, active, onClick, indent }) => (
  <div
    onClick={onClick}
    style={{
      display: 'flex', alignItems: 'center', gap: 10,
      padding: indent ? '7px 16px 7px 36px' : '8px 16px',
      fontSize: indent ? 12 : 13, color: '#fff', cursor: 'pointer',
      transition: 'all 0.2s cubic-bezier(0.16, 1, 0.3, 1)',
      background: active ? 'rgba(255,255,255,0.2)' : 'transparent',
      backdropFilter: active ? 'blur(4px)' : 'none',
      boxShadow: active ? 'inset 0 0 0 1px rgba(255,255,255,0.2)' : 'none',
      fontWeight: active ? 500 : 400,
      transform: active ? 'translateX(4px)' : 'translateX(0)',
    }}
    onMouseEnter={(e) => { if (!active) { e.currentTarget.style.background = 'rgba(255,255,255,0.1)'; e.currentTarget.style.transform = 'translateX(4px)'; }}}
    onMouseLeave={(e) => { if (!active) { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.transform = 'translateX(0)'; }}}
  >
    {icon ? <i data-lucide={icon} style={{ width: 14, height: 14, flexShrink: 0 }}></i> : <span style={{ width: 14 }}></span>}
    <span>{label}</span>
  </div>
);

const SidebarGroup = ({ label, children }) => (
  <div style={{ padding: '12px 0 4px' }}>
    <div style={{ padding: '0 16px 6px', fontSize: 10, fontWeight: 700, letterSpacing: '0.1em', textTransform: 'uppercase', color: 'rgba(255,255,255,0.9)' }}>{label}</div>
    {children}
  </div>
);

const Sidebar = ({ activeRoute, onNavigate }) => {
  const [crmOpen, setCrmOpen] = useState(true);

  return (
    <div style={{
      width: 220, height: '100%', flexShrink: 0,
      background: 'linear-gradient(to bottom, var(--gx-primary), var(--gx-primary), var(--gx-primary-dark))',
      color: '#fff', boxShadow: 'var(--shadow-elevated)', overflowY: 'auto',
    }}>
      <div style={{ padding: '16px 16px 18px', borderBottom: '1px solid rgba(255,255,255,0.2)', display: 'flex', justifyContent: 'center' }}>
        <img src="../../assets/logo-app-blue.png" alt="GX Capital" style={{ height: 36, filter: 'drop-shadow(0 2px 4px rgba(0,0,0,.3))' }} />
      </div>

      <SidebarGroup label="Módulos Principais">
        <NavItem icon="layout-dashboard" label="Dashboard" active={activeRoute === 'dashboard'} onClick={() => onNavigate('dashboard')} />
        <div onClick={() => setCrmOpen(v => !v)} style={{ display:'flex', alignItems:'center', gap:10, padding:'8px 16px', fontSize:13, color:'#fff', cursor:'pointer', justifyContent:'space-between' }}>
          <span style={{ display:'flex', alignItems:'center', gap:10 }}>
            <i data-lucide="users" style={{ width:14, height:14 }}></i> CRM
          </span>
          <i data-lucide="chevron-down" style={{ width:14, height:14, transform: crmOpen ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform .2s' }}></i>
        </div>
        {crmOpen && (
          <>
            <NavItem label="Comercial" indent active={activeRoute === 'crm'} onClick={() => onNavigate('crm')} />
            <NavItem label="Oportunidades" indent active={activeRoute === 'opps'} onClick={() => onNavigate('opps')} />
            <NavItem label="Reuniões" indent />
          </>
        )}
        <NavItem icon="trending-up" label="Estratégico" />
        <NavItem icon="briefcase" label="Mesa de Operações" active={activeRoute === 'mesa'} onClick={() => onNavigate('mesa')} />
        <NavItem icon="dollar-sign" label="Comissionamento" />
        <NavItem icon="sparkles" label="Consultor IA" />
        <NavItem icon="presentation" label="Apresentações" />
        <NavItem icon="handshake" label="Parceiros" />
        <NavItem icon="calculator" label="ERP" />
        <NavItem icon="user-cog" label="Recursos Humanos" />
      </SidebarGroup>

      <SidebarGroup label="Administração">
        <NavItem icon="user" label="Perfil" />
        <NavItem icon="settings" label="Configurações" />
      </SidebarGroup>
    </div>
  );
};

window.Sidebar = Sidebar;
