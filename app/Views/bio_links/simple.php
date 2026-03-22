<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title); ?></title>
    <meta name="description" content="<?= esc($description); ?>">
    <meta name="keywords" content="<?= esc($keywords); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url(); ?>">
    <meta property="og:title" content="<?= esc($title); ?>">
    <meta property="og:description" content="<?= esc($description); ?>">
    <meta property="og:image" content="<?= base_url('uploads/logo/logo_681f2f8a9e7759-77206881.svg'); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= current_url(); ?>">
    <meta property="twitter:title" content="<?= esc($title); ?>">
    <meta property="twitter:description" content="<?= esc($description); ?>">
    <meta property="twitter:image" content="<?= base_url('uploads/logo/logo_681f2f8a9e7759-77206881.svg'); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png'); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #c7a053;
            --secondary-color: #002a55;
            --background-color: #f8fafc;
            --text-color: #1a202c;
            --text-muted: #718096;
            --border-radius: 16px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-color);
        }

        .bio-container {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 480px;
            width: 100%;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .bio-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .profile-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 200px;
            height: auto;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }


        .profile-bio {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .links-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .bio-link {
            display: flex;
            align-items: center;
            padding: 18px 24px;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            font-weight: 500;
            font-size: 16px;
            border: 2px solid transparent;
            background: var(--background-color);
            color: var(--text-color);
        }

        .bio-link:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            text-decoration: none;
            color: var(--text-color);
            border-color: var(--primary-color);
        }

        .bio-link i {
            font-size: 20px;
            margin-right: 16px;
            width: 24px;
            text-align: center;
            color: var(--primary-color);
            display: inline-block;
            font-weight: 900;
        }
        
        /* Força a cor do ícone quando personalizada */
        .bio-link i[style*="color"] {
            color: inherit !important;
        }
        
        .bio-link i.fa-youtube-play:before {
            content: "\f04b";
        }
        
        .bio-link i.fa-youtube:before {
            content: "\f167";
        }
        
        .bio-link i.fa-instagram:before {
            content: "\f16d";
        }
        
        .bio-link i.fa-facebook:before {
            content: "\f09a";
        }
        
        .bio-link i.fa-twitter:before {
            content: "\f099";
        }
        
        .bio-link i.fa-linkedin:before {
            content: "\f0e1";
        }
        
        .bio-link i.fa-globe:before {
            content: "\f0ac";
        }
        
        .bio-link i.fa-whatsapp:before {
            content: "\f232";
        }
        
        .bio-link i.fa-telegram:before {
            content: "\f2c6";
        }

        .bio-link-title {
            flex: 1;
            font-weight: 600;
        }

        .bio-link-arrow {
            font-size: 18px;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .bio-link:hover .bio-link-arrow {
            transform: translateX(4px);
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 14px;
            color: var(--text-muted);
        }

        .powered-by {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .powered-by a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .bio-container {
                padding: 24px;
                border-radius: 16px;
            }
            
            .logo {
                width: 150px;
            }
            
            .bio-link {
                padding: 16px 20px;
                font-size: 15px;
            }
            
            .bio-link i {
                font-size: 18px;
                margin-right: 12px;
            }
        }

        @media (max-width: 360px) {
            .bio-container {
                padding: 20px;
            }
            
            .logo {
                width: 120px;
            }
            
            .bio-link {
                padding: 14px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="bio-container">
        <div class="profile-section">
            <div class="logo">
                <img src="<?= base_url('uploads/logo/logo_681f2f8a9e7759-77206881.svg'); ?>" alt="GX Capital">
            </div>
            
            <p class="profile-bio">
                <?= esc($bioDescription); ?>
            </p>
        </div>

        <div class="links-section">
            <?php if (!empty($bioLinks)): ?>
                <?php foreach ($bioLinks as $link): ?>
                    <a href="<?= base_url('bio/click/' . $link['id']); ?>" 
                       class="bio-link" 
                       target="_blank"
                       style="<?= !empty($link['button_color']) ? 'background-color: ' . esc($link['button_color']) . ';' : ''; ?><?= !empty($link['text_color']) ? 'color: ' . esc($link['text_color']) . ';' : ''; ?>">
                        
                        <?php if (!empty($link['icon'])): ?>
                            <?php 
                            // Converter ícones FA4 para FA6 se necessário
                            $iconClass = esc($link['icon']);
                            $iconClass = str_replace('fa-youtube-play', 'fa-youtube', $iconClass);
                            
                            // Se não tem prefixo fa, fas, far, fab, adicionar fab para redes sociais
                            if (!preg_match('/^(fa|fas|far|fab|fal|fad|fat)\s/', $iconClass)) {
                                if (strpos($iconClass, 'youtube') !== false || 
                                    strpos($iconClass, 'instagram') !== false || 
                                    strpos($iconClass, 'facebook') !== false || 
                                    strpos($iconClass, 'twitter') !== false || 
                                    strpos($iconClass, 'linkedin') !== false ||
                                    strpos($iconClass, 'whatsapp') !== false ||
                                    strpos($iconClass, 'telegram') !== false) {
                                    $iconClass = 'fab ' . str_replace('fa ', '', $iconClass);
                                } elseif (!preg_match('/^fa\s/', $iconClass)) {
                                    $iconClass = 'fas ' . $iconClass;
                                }
                            }
                            ?>
                            <i class="<?= $iconClass; ?>" style="<?= !empty($link['text_color']) ? 'color: ' . esc($link['text_color']) . ';' : ''; ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-link"></i>
                        <?php endif; ?>
                        
                        <span class="bio-link-title"><?= esc($link['title']); ?></span>
                        <i class="fas fa-chevron-right bio-link-arrow" style="<?= !empty($link['text_color']) ? 'color: ' . esc($link['text_color']) . ';' : ''; ?>"></i>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-link"></i>
                    <p>Nenhum link disponível no momento.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p class="footer-text">Conecte-se conosco através dos links acima</p>
            <p class="powered-by">
                Desenvolvido com ♥ por <a href="<?= base_url(); ?>" target="_blank">GX Capital</a>
            </p>
        </div>
    </div>

    <script>
        // Adicionar efeitos de hover suaves
        document.querySelectorAll('.bio-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Analytics de clique (opcional)
        document.querySelectorAll('.bio-link').forEach(link => {
            link.addEventListener('click', function() {
                // Aqui você pode adicionar tracking de analytics
                console.log('Link clicado:', this.querySelector('.bio-link-title').textContent);
            });
        });
    </script>
</body>
</html>