<!-- Single Web Story View -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="story-container">
                <!-- Story Header -->
                <div class="story-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(); ?>"><?= trans('home'); ?></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('web-stories'); ?>"><?= trans('web_stories'); ?></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= esc($webStory->title); ?>
                            </li>
                        </ol>
                    </nav>

                    <h1 class="story-title"><?= esc($webStory->title); ?></h1>
                    
                    <?php if (!empty($webStory->description)): ?>
                        <p class="story-description"><?= esc($webStory->description); ?></p>
                    <?php endif; ?>

                    <div class="story-meta">
                        <div class="meta-item">
                            <i class="fa fa-eye"></i>
                            <span><?= number_format($webStory->view_count); ?> <?= trans('views'); ?></span>
                        </div>
                        
                        <?php if (!empty($webStory->link_url)): ?>
                            <div class="meta-item">
                                <i class="fa fa-mouse-pointer"></i>
                                <span><?= number_format($webStory->click_count); ?> <?= trans('clicks'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="fa fa-calendar"></i>
                            <span><?= date('M j, Y', strtotime($webStory->created_at)); ?></span>
                        </div>

                        <?php if ($webStory->is_generated == 1): ?>
                            <div class="meta-item ai-generated">
                                <i class="fa fa-magic"></i>
                                <span><?= trans('ai_generated'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Story Content -->
                <div class="story-content">
                    <div class="story-image-wrapper">
                        <img src="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : ($webStory->image_url ?: base_url('assets/img/no-image.png')); ?>" 
                             alt="<?= esc($webStory->title); ?>" 
                             class="story-main-image">
                        
                        <?php if ($webStory->is_generated == 1): ?>
                            <div class="ai-badge">
                                <i class="fa fa-magic"></i> AI Generated
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($webStory->generation_prompt) && $webStory->is_generated == 1): ?>
                        <div class="ai-prompt-section">
                            <h5><i class="fa fa-lightbulb-o"></i> <?= trans('ai_prompt_used'); ?></h5>
                            <blockquote class="ai-prompt">
                                "<?= esc($webStory->generation_prompt); ?>"
                            </blockquote>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($webStory->link_url)): ?>
                        <div class="story-action">
                            <a href="<?= base_url('web-stories/click/' . $webStory->id); ?>" 
                               class="btn btn-primary btn-lg btn-action" 
                               target="_blank" 
                               rel="noopener">
                                <i class="fa fa-external-link"></i>
                                <?= trans('visit_link'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Social Sharing -->
                <div class="story-sharing">
                    <h5><?= trans('share_this_story'); ?></h5>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()); ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="share-btn facebook">
                            <i class="fa fa-facebook"></i>
                            Facebook
                        </a>
                        
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()); ?>&text=<?= urlencode($webStory->title); ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="share-btn twitter">
                            <i class="fa fa-twitter"></i>
                            Twitter
                        </a>
                        
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(current_url()); ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="share-btn linkedin">
                            <i class="fa fa-linkedin"></i>
                            LinkedIn
                        </a>
                        
                        <a href="https://wa.me/?text=<?= urlencode($webStory->title . ' - ' . current_url()); ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="share-btn whatsapp">
                            <i class="fa fa-whatsapp"></i>
                            WhatsApp
                        </a>
                        
                        <button class="share-btn copy-link" onclick="copyToClipboard('<?= current_url(); ?>')">
                            <i class="fa fa-copy"></i>
                            <?= trans('copy_link'); ?>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="story-navigation">
                    <a href="<?= base_url('web-stories'); ?>" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left"></i>
                        <?= trans('back_to_stories'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Content for AJAX Loading -->
<div class="story-modal-content" style="display: none;">
    <div class="modal-story-image text-center">
        <img src="<?= !empty($webStory->image_path) ? base_url($webStory->image_path) : ($webStory->image_url ?: base_url('assets/img/no-image.png')); ?>" 
             alt="<?= esc($webStory->title); ?>" 
             class="img-fluid"
             style="max-height: 400px; border-radius: 10px;">
    </div>
    
    <?php if (!empty($webStory->description)): ?>
        <div class="modal-story-description mt-3">
            <p><?= esc($webStory->description); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="modal-story-meta mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="story-stats">
                <span class="badge badge-primary me-2">
                    <i class="fa fa-eye"></i> <?= number_format($webStory->view_count); ?>
                </span>
                <?php if (!empty($webStory->link_url)): ?>
                    <span class="badge badge-info">
                        <i class="fa fa-mouse-pointer"></i> <?= number_format($webStory->click_count); ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($webStory->link_url)): ?>
                <a href="<?= base_url('web-stories/click/' . $webStory->id); ?>" 
                   class="btn btn-primary" 
                   target="_blank" 
                   rel="noopener">
                    <i class="fa fa-external-link"></i> <?= trans('visit_link'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.story-container {
    padding: 40px 0;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 30px;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

.story-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.3;
}

.story-description {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}

.story-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 0.95rem;
}

.meta-item i {
    color: #007bff;
}

.meta-item.ai-generated {
    background: linear-gradient(45deg, #6c5ce7, #a29bfe);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.meta-item.ai-generated i {
    color: white;
}

.story-content {
    margin-bottom: 50px;
}

.story-image-wrapper {
    position: relative;
    margin-bottom: 30px;
    text-align: center;
}

.story-main-image {
    width: 100%;
    max-width: 600px;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.ai-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: linear-gradient(45deg, #6c5ce7, #a29bfe);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
}

.ai-prompt-section {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    border-left: 4px solid #6c5ce7;
}

.ai-prompt-section h5 {
    color: #6c5ce7;
    margin-bottom: 15px;
    font-weight: 600;
}

.ai-prompt {
    font-style: italic;
    color: #495057;
    margin: 0;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border-left: 3px solid #6c5ce7;
}

.story-action {
    text-align: center;
    margin-bottom: 40px;
}

.btn-action {
    padding: 15px 40px;
    font-size: 1.1rem;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,123,255,0.3);
    text-decoration: none;
}

.story-sharing {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 40px;
}

.story-sharing h5 {
    margin-bottom: 20px;
    color: #333;
    font-weight: 600;
}

.share-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.share-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.share-btn:hover {
    text-decoration: none;
    transform: translateY(-2px);
}

.share-btn.facebook {
    background: #1877f2;
    color: white;
}

.share-btn.facebook:hover {
    background: #166fe5;
    color: white;
}

.share-btn.twitter {
    background: #1da1f2;
    color: white;
}

.share-btn.twitter:hover {
    background: #1a91da;
    color: white;
}

.share-btn.linkedin {
    background: #0077b5;
    color: white;
}

.share-btn.linkedin:hover {
    background: #006396;
    color: white;
}

.share-btn.whatsapp {
    background: #25d366;
    color: white;
}

.share-btn.whatsapp:hover {
    background: #20ba5a;
    color: white;
}

.share-btn.copy-link {
    background: #6c757d;
    color: white;
}

.share-btn.copy-link:hover {
    background: #5a6268;
    color: white;
}

.story-navigation {
    text-align: center;
}

/* Responsive design */
@media (max-width: 768px) {
    .story-title {
        font-size: 2rem;
    }
    
    .story-description {
        font-size: 1.1rem;
    }
    
    .story-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .share-buttons {
        justify-content: center;
    }
    
    .share-btn {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }
    
    .ai-badge {
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .story-container {
        padding: 20px 0;
    }
    
    .story-title {
        font-size: 1.75rem;
    }
    
    .story-sharing {
        padding: 20px;
    }
    
    .share-buttons {
        flex-direction: column;
    }
    
    .share-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('<?= trans('link_copied_to_clipboard'); ?>');
        }, function(err) {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            showNotification('<?= trans('link_copied_to_clipboard'); ?>');
        } else {
            showNotification('<?= trans('copy_failed'); ?>');
        }
    } catch (err) {
        showNotification('<?= trans('copy_not_supported'); ?>');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message) {
    // Simple notification - you can integrate with your notification system
    alert(message);
}
</script>