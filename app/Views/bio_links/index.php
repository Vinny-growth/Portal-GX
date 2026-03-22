<!DOCTYPE html>
<html lang="<?= $activeLang->short_form ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= escMeta($title); ?></title>
    <meta name="description" content="<?= escMeta($description); ?>"/>
    <meta name="keywords" content="<?= escMeta($keywords); ?>"/>
    <meta name="author" content="<?= escMeta($baseSettings->application_name); ?>"/>
    <meta name="robots" content="all">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="<?= escMeta($title); ?>"/>
    <meta property="og:description" content="<?= escMeta($description); ?>"/>
    <meta property="og:url" content="<?= base_url('bio'); ?>"/>
    <meta property="og:image" content="<?= getLogo(); ?>"/>
    <meta property="og:site_name" content="<?= escMeta($baseSettings->application_name); ?>"/>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/admin/plugins/font-awesome/css/font-awesome.min.css'); ?>">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 10px;
            color: #fff;
            line-height: 1.6;
        }
        
        .bio-container {
            max-width: 500px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            color: #333;
            text-align: center;
        }
        
        .profile-section {
            margin-bottom: 40px;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        
        .profile-description {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .profile-website {
            font-size: 14px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        
        .profile-website:hover {
            text-decoration: underline;
        }
        
        .links-section {
            margin-top: 30px;
        }
        
        .bio-link {
            display: block;
            width: 100%;
            margin-bottom: 16px;
            padding: 16px 24px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .bio-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .bio-link:hover::before {
            left: 100%;
        }
        
        .bio-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .bio-link:active {
            transform: translateY(0);
        }
        
        .bio-link i {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .footer-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #ecf0f1;
        }
        
        .powered-by {
            font-size: 12px;
            color: #95a5a6;
            margin-bottom: 10px;
        }
        
        .powered-by a {
            color: #3498db;
            text-decoration: none;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #ecf0f1;
            color: #7f8c8d;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 18px;
        }
        
        .social-link:hover {
            background: #3498db;
            color: #fff;
            transform: translateY(-2px);
        }
        
        .no-links {
            text-align: center;
            color: #7f8c8d;
            font-size: 16px;
            margin: 40px 0;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 15px;
            border: 2px dashed #dee2e6;
        }
        
        .no-links i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
        
        /* Mobile optimizations */
        @media (max-width: 480px) {
            body {
                padding: 10px 5px;
            }
            
            .bio-container {
                padding: 30px 20px;
                margin: 10px auto;
                border-radius: 15px;
            }
            
            .profile-image {
                width: 100px;
                height: 100px;
            }
            
            .profile-name {
                font-size: 24px;
            }
            
            .bio-link {
                padding: 14px 20px;
                font-size: 15px;
                margin-bottom: 12px;
            }
            
            .bio-link i {
                margin-right: 10px;
                font-size: 16px;
            }
        }
        
        /* Animation for page load */
        .bio-container {
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .bio-link {
            animation: slideInLeft 0.6s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Stagger animation for links */
        .bio-link:nth-child(1) { animation-delay: 0.1s; }
        .bio-link:nth-child(2) { animation-delay: 0.2s; }
        .bio-link:nth-child(3) { animation-delay: 0.3s; }
        .bio-link:nth-child(4) { animation-delay: 0.4s; }
        .bio-link:nth-child(5) { animation-delay: 0.5s; }
        .bio-link:nth-child(6) { animation-delay: 0.6s; }
        .bio-link:nth-child(7) { animation-delay: 0.7s; }
        .bio-link:nth-child(8) { animation-delay: 0.8s; }
    </style>
</head>
<body>
    <div class="bio-container">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?= getLogo(); ?>" alt="<?= esc($baseSettings->application_name); ?>">
            </div>
            <h1 class="profile-name"><?= esc($baseSettings->application_name); ?></h1>
            <p class="profile-description"><?= esc($baseSettings->site_description ?: 'Todos os nossos links importantes em um só lugar'); ?></p>
            <a href="<?= base_url(); ?>" class="profile-website" target="_blank">
                <i class="fa fa-globe"></i> <?= str_replace(['http://', 'https://'], '', base_url()); ?>
            </a>
        </div>
        
        <!-- Links Section -->
        <div class="links-section">
            <?php if (!empty($bioLinks)): ?>
                <?php foreach ($bioLinks as $link): ?>
                    <a href="<?= base_url('bio/click/' . $link['id']); ?>" 
                       class="bio-link" 
                       target="_blank"
                       style="background-color: <?= esc($link['button_color']); ?>; color: <?= esc($link['text_color']); ?>;">
                        <?php if (!empty($link['icon'])): ?>
                            <i class="<?= esc($link['icon']); ?>"></i>
                        <?php endif; ?>
                        <?= esc($link['title']); ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-links">
                    <i class="fa fa-link"></i>
                    <h3>Em breve...</h3>
                    <p>Nossos links estarão disponíveis aqui em breve!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <div class="social-links">
                <?php if (!empty($baseSettings->facebook_url)): ?>
                    <a href="<?= esc($baseSettings->facebook_url); ?>" class="social-link" target="_blank">
                        <i class="fa fa-facebook"></i>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($baseSettings->twitter_url)): ?>
                    <a href="<?= esc($baseSettings->twitter_url); ?>" class="social-link" target="_blank">
                        <i class="fa fa-twitter"></i>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($baseSettings->instagram_url)): ?>
                    <a href="<?= esc($baseSettings->instagram_url); ?>" class="social-link" target="_blank">
                        <i class="fa fa-instagram"></i>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($baseSettings->youtube_url)): ?>
                    <a href="<?= esc($baseSettings->youtube_url); ?>" class="social-link" target="_blank">
                        <i class="fa fa-youtube"></i>
                    </a>
                <?php endif; ?>
            </div>
            
            <p class="powered-by">
                Powered by <a href="<?= base_url(); ?>"><?= esc($baseSettings->application_name); ?></a>
            </p>
        </div>
    </div>

    <script>
        // Track clicks for analytics
        document.querySelectorAll('.bio-link').forEach(function(link) {
            link.addEventListener('click', function() {
                // Optional: Send analytics event
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'bio_link_click', {
                        'link_text': this.textContent.trim(),
                        'link_url': this.href
                    });
                }
            });
        });
        
        // Add touch feedback for mobile
        document.querySelectorAll('.bio-link, .social-link').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    </script>
</body>
</html>