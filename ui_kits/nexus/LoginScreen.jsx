// LoginScreen.jsx — split auth surface
const LoginScreen = ({ onSubmit }) => {
  const [email, setEmail] = React.useState('lucas@gxcapital.com.br');
  const [pw, setPw] = React.useState('••••••••••');

  return (
    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', minHeight: '100vh', height: '100%' }}>
      <div style={{
        background: 'linear-gradient(180deg, var(--gx-primary) 0%, var(--gx-primary-dark) 100%)',
        color: '#fff', padding: 56, position: 'relative', overflow: 'hidden',
        display: 'flex', flexDirection: 'column', justifyContent: 'space-between',
      }}>
        <div style={{ position: 'absolute', top: -40, right: -20, fontSize: 220, fontWeight: 900, letterSpacing: '-0.06em', lineHeight: 0.8, opacity: 0.05, userSelect: 'none' }}>GXC</div>
        <div style={{ position: 'relative' }}>
          <img src="../../assets/logo-app-blue.png" alt="GX Capital" style={{ height: 44 }} />
        </div>
        <div style={{ position: 'relative' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
            <span style={{ height: 2, width: 36, background: 'var(--gx-secondary-dark)' }}></span>
            <span style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'var(--gx-secondary-dark)' }}>Nexus Platform</span>
          </div>
          <h1 style={{ fontSize: 56, fontWeight: 900, lineHeight: 0.92, letterSpacing: '-0.04em', textTransform: 'uppercase', marginTop: 14 }}>Wealth.<br/>Capital.<br/>Comando.</h1>
          <p style={{ fontSize: 14, color: 'rgba(255,255,255,0.6)', maxWidth: 340, marginTop: 16 }}>Visão consolidada de toda operação. CRM, mesa, comissionamento e IA em uma só plataforma.</p>
        </div>
        <div style={{ position: 'relative', fontSize: 10, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'rgba(255,255,255,0.4)', fontWeight: 700 }}>v 2.4.0 · 2026 GX Capital</div>
      </div>

      <div style={{ background: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: 56 }}>
        <form
          onSubmit={(e) => { e.preventDefault(); onSubmit && onSubmit(); }}
          style={{ width: '100%', maxWidth: 360, display: 'flex', flexDirection: 'column', gap: 18 }}
        >
          <div>
            <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase', color: 'var(--gx-secondary-dark)' }}>Acesso restrito</div>
            <h2 style={{ fontSize: 36, fontWeight: 900, lineHeight: 1, letterSpacing: '-0.03em', textTransform: 'uppercase', marginTop: 8 }}>Entrar</h2>
          </div>

          <label style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
            <span style={{ fontSize: 10, fontWeight: 700, letterSpacing: '0.1em', textTransform: 'uppercase', color: 'var(--gx-secondary-dark)' }}>E-mail corporativo</span>
            <input value={email} onChange={(e) => setEmail(e.target.value)} style={{ height: 42, padding: '0 12px', fontSize: 14, border: '1px solid var(--gx-border)', borderRadius: 0, fontFamily: 'var(--font-sans)', outline: 'none' }} onFocus={(e) => e.target.style.borderColor = 'var(--gx-primary)'} onBlur={(e) => e.target.style.borderColor = 'var(--gx-border)'} />
          </label>
          <label style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
            <span style={{ fontSize: 10, fontWeight: 700, letterSpacing: '0.1em', textTransform: 'uppercase', color: 'var(--gx-secondary-dark)' }}>Senha</span>
            <input type="password" value={pw} onChange={(e) => setPw(e.target.value)} style={{ height: 42, padding: '0 12px', fontSize: 14, border: '1px solid var(--gx-border)', borderRadius: 0, fontFamily: 'var(--font-sans)', outline: 'none' }} />
          </label>

          <Button>Entrar no Nexus →</Button>

          <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 11, fontWeight: 600, color: 'var(--fg-muted, #5a6a80)', marginTop: 4 }}>
            <a href="#" style={{ color: 'var(--gx-primary)', textDecoration: 'none' }}>Esqueci minha senha</a>
            <a href="#" style={{ color: 'var(--gx-primary)', textDecoration: 'none' }}>Solicitar acesso</a>
          </div>
        </form>
      </div>
    </div>
  );
};

window.LoginScreen = LoginScreen;
