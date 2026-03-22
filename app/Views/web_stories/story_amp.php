<!DOCTYPE html>
<html ⚡ lang="<?= $activeLang->short_form; ?>">
<head>
    <meta charset="utf-8">
    <title><?= esc($webStory->title); ?> - <?= esc($this->settings->site_title ?? 'Web Stories'); ?></title>
    <meta name="description" content="<?= esc($webStory->description); ?>">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    
    <!-- AMP Web Stories Required Scripts -->
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
    
    <!-- Social Media Meta Tags -->
    <meta property="og:title" content="<?= esc($webStory->title); ?>">
    <meta property="og:description" content="<?= esc($webStory->description); ?>">
    <meta property="og:image" content="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : base_url($webStory->image_url); ?>">
    <meta property="og:url" content="<?= current_url(); ?>">
    <meta property="og:type" content="article">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($webStory->title); ?>">
    <meta name="twitter:description" content="<?= esc($webStory->description); ?>">
    <meta name="twitter:image" content="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : base_url($webStory->image_url); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= current_url(); ?>">
    
    <!-- AMP Boilerplate -->
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    
    <!-- Custom Styles -->
    <style amp-custom>
        amp-story {
            font-family: 'Roboto', sans-serif;
        }
        
        .story-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .cover-page {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .content-page {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .cta-page {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .story-title {
            font-size: 3rem;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            line-height: 1.2;
            padding: 20px;
        }
        
        .story-subtitle {
            font-size: 1.5rem;
            color: white;
            opacity: 0.9;
            padding: 0 20px;
            margin-top: 20px;
        }
        
        .story-content {
            font-size: 1.2rem;
            color: white;
            line-height: 1.6;
            padding: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .story-highlight {
            background: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 15px;
            margin: 20px;
            backdrop-filter: blur(10px);
        }
        
        .story-cta {
            background: rgba(255,255,255,0.9);
            color: #333;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .ai-badge {
            background: linear-gradient(45deg, #6c5ce7, #a29bfe);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 10px 20px;
            display: inline-block;
        }
        
        .story-meta {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            padding: 0 20px;
            margin-top: 10px;
        }
    </style>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?= esc($webStory->title); ?>",
        "description": "<?= esc($webStory->description); ?>",
        "image": "<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : base_url($webStory->image_url); ?>",
        "url": "<?= current_url(); ?>",
        "datePublished": "<?= date('c', strtotime($webStory->created_at)); ?>",
        "author": {
            "@type": "Organization",
            "name": "<?= esc($this->settings->site_title ?? 'GX Capital'); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?= esc($this->settings->site_title ?? 'GX Capital'); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?= base_url('assets/img/logo.png'); ?>"
            }
        }
    }
    </script>
</head>

<body>
    <amp-story standalone
              title="<?= esc($webStory->title); ?>"
              publisher="<?= esc($this->settings->site_title ?? 'GX Capital'); ?>"
              publisher-logo-src="<?= base_url('assets/img/logo.png'); ?>"
              poster-portrait-src="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : base_url($webStory->image_url); ?>">
        
        <?php if (!empty($customPages)): ?>
            <!-- Render Custom Pages -->
            <?php foreach ($customPages as $page): ?>
                <?php 
                $pageClasses = [
                    'cover' => 'cover-page',
                    'content' => 'content-page', 
                    'image' => 'story-page',
                    'video' => 'story-page',
                    'cta' => 'cta-page',
                    'custom' => 'story-page'
                ];
                $pageClass = $pageClasses[$page->page_type] ?? 'story-page';
                
                $fontSizes = [
                    'small' => '1rem',
                    'medium' => '1.2rem', 
                    'large' => '1.5rem',
                    'xlarge' => '2rem'
                ];
                $fontSize = $fontSizes[$page->font_size] ?? '1.2rem';
                
                $textAlignments = [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end'
                ];
                $textAlign = $textAlignments[$page->text_position] ?? 'center';
                ?>
                
                <amp-story-page id="page-<?= $page->id; ?>" class="<?= $pageClass; ?>"
                               style="<?= $page->background_type === 'gradient' ? 'background: ' . esc($page->background_value) : ''; ?>
                                      <?= $page->background_type === 'color' ? 'background-color: ' . esc($page->background_value) : ''; ?>">
                    
                    <?php if ($page->background_type === 'image' && !empty($page->background_value)): ?>
                        <amp-story-grid-layer template="fill">
                            <amp-img src="<?= esc($page->background_value); ?>" 
                                     width="720" height="1280" 
                                     layout="responsive">
                            </amp-img>
                        </amp-story-grid-layer>
                    <?php endif; ?>
                    
                    <?php if (!empty($page->image_path) || !empty($page->image_url)): ?>
                        <amp-story-grid-layer template="fill">
                            <amp-img src="<?= !empty($page->image_path) ? base_url($page->image_path) : esc($page->image_url); ?>" 
                                     width="720" height="1280" 
                                     layout="responsive"
                                     alt="<?= esc($page->title); ?>">
                            </amp-img>
                        </amp-story-grid-layer>
                    <?php endif; ?>
                    
                    <amp-story-grid-layer template="vertical" style="align-items: <?= $textAlign; ?>; justify-content: <?= $textAlign; ?>;">
                        <div style="padding: 20px; text-align: center; color: <?= esc($page->text_color); ?>;">
                            <?php if (!empty($page->title)): ?>
                                <h2 style="margin: 0 0 20px 0; font-size: <?= $page->page_type === 'cover' ? '2.5rem' : '1.8rem'; ?>; color: <?= esc($page->text_color); ?>;">
                                    <?= esc($page->title); ?>
                                </h2>
                            <?php endif; ?>
                            
                            <?php if (!empty($page->content)): ?>
                                <div style="font-size: <?= $fontSize; ?>; line-height: 1.6; color: <?= esc($page->text_color); ?>;">
                                    <?= nl2br(esc($page->content)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($page->cta_text) && !empty($page->cta_url)): ?>
                                <a href="<?= esc($page->cta_url); ?>" 
                                   class="story-cta"
                                   target="_blank"
                                   style="margin-top: 20px; display: inline-block;">
                                    <?= esc($page->cta_text); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </amp-story-grid-layer>
                </amp-story-page>
            <?php endforeach; ?>
            
        <?php else: ?>
            <!-- Default Pages (Legacy) -->
            
            <!-- Page 1: Cover -->
            <amp-story-page id="cover" class="cover-page">
                <amp-story-grid-layer template="vertical">
                    <div class="story-title"><?= esc($webStory->title); ?></div>
                    <?php if (!empty($webStory->description)): ?>
                        <div class="story-subtitle"><?= esc($webStory->description); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($webStory->is_generated == 1): ?>
                        <div class="ai-badge">
                            🤖 Gerado por IA
                        </div>
                    <?php endif; ?>
                    
                    <div class="story-meta">
                        📅 <?= date('d/m/Y', strtotime($webStory->created_at)); ?> • 
                        👁️ <?= number_format($webStory->view_count); ?> visualizações
                    </div>
                </amp-story-grid-layer>
            </amp-story-page>

            <!-- Page 2: Main Image -->
            <amp-story-page id="main-image" class="content-page">
                <amp-story-grid-layer template="fill">
                    <amp-img src="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : base_url($webStory->image_url); ?>" 
                             width="720" height="1280" 
                             layout="responsive"
                             alt="<?= esc($webStory->title); ?>">
                    </amp-img>
                </amp-story-grid-layer>
                
                <amp-story-grid-layer template="vertical" class="bottom">
                    <div class="story-highlight">
                        <h2 style="margin: 0; color: white; font-size: 1.8rem;">
                            <?= esc($webStory->title); ?>
                        </h2>
                    </div>
                </amp-story-grid-layer>
            </amp-story-page>

            <!-- Page 3: Content -->
            <?php if (!empty($webStory->description)): ?>
            <amp-story-page id="content" class="story-page">
                <amp-story-grid-layer template="vertical">
                    <div class="story-content">
                        <h2 style="color: white; margin-bottom: 20px; font-size: 2rem;">
                            Detalhes da História
                        </h2>
                        <p><?= nl2br(esc($webStory->description)); ?></p>
                        
                        <?php if ($webStory->is_generated == 1 && !empty($webStory->generation_prompt)): ?>
                            <div class="story-highlight" style="margin-top: 30px;">
                                <h3 style="margin: 0 0 10px 0; color: white;">💡 Prompt usado para gerar a imagem:</h3>
                                <p style="margin: 0; font-style: italic;">"<?= esc($webStory->generation_prompt); ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </amp-story-grid-layer>
            </amp-story-page>
            <?php endif; ?>

            <!-- Page 4: Call to Action -->
            <?php if (!empty($webStory->link_url)): ?>
            <amp-story-page id="cta" class="cta-page">
                <amp-story-grid-layer template="vertical">
                    <div style="text-align: center; padding: 40px 20px;">
                        <h2 style="color: white; font-size: 2.5rem; margin-bottom: 30px;">
                            📈 Quer saber mais?
                        </h2>
                        <p style="color: white; font-size: 1.3rem; margin-bottom: 40px;">
                            Clique no link abaixo para acessar o conteúdo completo
                        </p>
                        <a href="<?= base_url('web-stories/click/' . $webStory->id); ?>" 
                           class="story-cta"
                           target="_blank">
                            🔗 Acessar Link
                        </a>
                        
                        <div style="margin-top: 30px; color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                            💭 <?= number_format($webStory->click_count); ?> pessoas já clicaram
                        </div>
                    </div>
                </amp-story-grid-layer>
            </amp-story-page>
            <?php endif; ?>

            <!-- Final Page: Share -->
            <amp-story-page id="share" class="story-page">
                <amp-story-grid-layer template="vertical">
                    <div style="text-align: center; padding: 40px 20px;">
                        <h2 style="color: white; font-size: 2.5rem; margin-bottom: 30px;">
                            📱 Compartilhe esta história
                        </h2>
                        <p style="color: white; font-size: 1.2rem; margin-bottom: 40px;">
                            Gostou do conteúdo? Compartilhe com seus amigos!
                        </p>
                        
                        <div style="margin-bottom: 30px;">
                            <a href="<?= base_url('web-stories'); ?>" 
                               style="background: rgba(255,255,255,0.2); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin: 10px; display: inline-block;">
                                ← Voltar para Web Stories
                            </a>
                        </div>
                        
                        <div style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                            💙 Obrigado por ler!
                        </div>
                    </div>
                </amp-story-grid-layer>
            </amp-story-page>
            
        <?php endif; ?>
    </amp-story>
</body>
</html>