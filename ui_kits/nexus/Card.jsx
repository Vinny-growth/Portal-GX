// Card.jsx — sharp card with tech-bracket hover
const { useState: useStateCard } = React;

const Card = ({ children, style }) => {
  const [hover, setHover] = useStateCard(false);
  return (
    <div
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
      style={{
        position: 'relative', background: '#fff',
        border: '1px solid', borderColor: hover ? 'rgba(12,49,99,0.5)' : 'var(--gx-border)',
        boxShadow: hover ? '0 0 20px -10px rgba(12,49,99,0.3)' : 'none',
        transition: 'all 0.3s', padding: 20, ...style,
      }}
    >
      <span style={{ position: 'absolute', top: 0, left: 0, width: 12, height: 12, borderTop: '2px solid', borderLeft: '2px solid', borderColor: hover ? 'var(--gx-primary)' : 'transparent', transition: 'border-color 0.3s' }}></span>
      <span style={{ position: 'absolute', bottom: 0, right: 0, width: 12, height: 12, borderBottom: '2px solid', borderRight: '2px solid', borderColor: hover ? 'var(--gx-primary)' : 'transparent', transition: 'border-color 0.3s' }}></span>
      {children}
    </div>
  );
};

window.Card = Card;
