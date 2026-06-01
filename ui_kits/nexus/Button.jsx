// Button.jsx — variants matching shadcn config
const Button = ({ variant = 'primary', size = 'default', children, onClick, icon, style }) => {
  const base = {
    display: 'inline-flex', alignItems: 'center', justifyContent: 'center', gap: 8,
    fontFamily: 'var(--font-sans)', fontWeight: 600, cursor: 'pointer',
    transition: 'all 0.2s cubic-bezier(0.16, 1, 0.3, 1)',
    border: '1px solid transparent', borderRadius: 0, whiteSpace: 'nowrap',
  };
  const sizes = {
    sm: { padding: '8px 12px', fontSize: 12, height: 36 },
    default: { padding: '10px 16px', fontSize: 13, height: 40 },
    lg: { padding: '14px 28px', fontSize: 14, height: 44 },
    icon: { padding: 10, height: 40, width: 40 },
  };
  const variants = {
    primary: { background: 'var(--gx-primary)', color: 'var(--gx-secondary-light)' },
    secondary: { background: 'var(--gx-secondary-light)', color: 'var(--gx-primary)' },
    outline: { background: '#fff', color: 'var(--gx-primary)', borderColor: 'var(--gx-border)' },
    ghost: { background: 'transparent', color: 'var(--gx-primary)' },
    dark: { background: 'var(--gx-primary-dark)', color: '#fff' },
    gold: { background: 'var(--gx-secondary-dark)', color: '#fff' },
    destructive: { background: '#dc2626', color: '#fff' },
  };
  const [hover, setHover] = React.useState(false);
  const [active, setActive] = React.useState(false);

  return (
    <button
      onClick={onClick}
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => { setHover(false); setActive(false); }}
      onMouseDown={() => setActive(true)}
      onMouseUp={() => setActive(false)}
      style={{
        ...base, ...sizes[size], ...variants[variant],
        boxShadow: hover ? 'var(--shadow-card-hover)' : 'none',
        transform: active ? 'scale(.95)' : 'scale(1)',
        ...style,
      }}
    >
      {icon && <i data-lucide={icon} style={{ width: 14, height: 14 }}></i>}
      {children}
    </button>
  );
};

window.Button = Button;
