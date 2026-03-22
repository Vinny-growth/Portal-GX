<!-- Web Stories Section -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="web-stories-container">
                <div class="section-header">
                    <h1 class="page-title"><?= trans('web_stories'); ?></h1>
                    <p class="page-description"><?= trans('explore_our_visual_stories'); ?></p>
                </div>

                <?php if (!empty($webStories)): ?>
                    <div class="web-stories-grid" id="web-stories-grid">
                        <?php foreach ($webStories as $story): ?>
                            <div class="web-story-card" data-story-id="<?= $story->id; ?>">
                                <div class="story-image-container">
                                    <img src="<?= !empty($story->image_path) ? base_url($story->image_path) : ($story->image_url ?: base_url('assets/img/no-image.png')); ?>" 
                                         alt="<?= esc($story->title); ?>" 
                                         class="story-image lazyload"
                                         loading="lazy">
                                    
                                    <!-- Title always visible -->
                                    <div class="story-title-bar">
                                        <h3 class="story-title-main"><?= esc($story->title); ?></h3>
                                    </div>
                                    
                                    <!-- Hover overlay with actions -->
                                    <div class="story-overlay">
                                        <div class="story-content">
                                            <?php if (!empty($story->description)): ?>
                                                <p class="story-description"><?= esc(character_limiter($story->description, 100)); ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="story-actions">
                                                <a href="<?= base_url('web-stories/story/' . $story->id); ?>" 
                                                   class="btn btn-primary btn-view-story">
                                                    <i class="fa fa-play"></i> Ver Story
                                                </a>
                                                
                                                <?php if (!empty($story->link_url)): ?>
                                                    <a href="<?= base_url('web-stories/click/' . $story->id); ?>" 
                                                       class="btn btn-outline-light btn-story-link" 
                                                       target="_blank" rel="noopener">
                                                        <i class="fa fa-external-link"></i> <?= trans('visit_link'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Generation badge -->
                                    <?php if ($story->is_generated == 1): ?>
                                        <div class="ai-badge">
                                            <i class="fa fa-magic"></i> AI
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Story stats -->
                                <div class="story-stats">
                                    <span class="stat-item">
                                        <i class="fa fa-eye"></i> <?= number_format($story->view_count); ?>
                                    </span>
                                    <?php if (!empty($story->link_url)): ?>
                                        <span class="stat-item">
                                            <i class="fa fa-mouse-pointer"></i> <?= number_format($story->click_count); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa fa-images fa-4x"></i>
                        </div>
                        <h3><?= trans('no_stories_available'); ?></h3>
                        <p><?= trans('check_back_later_for_new_stories'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Simplified: No modal needed - direct navigation to story page -->

<style>
.web-stories-container {
    padding: 40px 0;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.page-description {
    font-size: 1.1rem;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

.web-stories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.web-story-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    background: #fff;
}

.web-story-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.story-image-container {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.story-title-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    padding: 20px;
    z-index: 3;
}

.story-title-main {
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

.story-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.web-story-card:hover .story-image {
    transform: scale(1.05);
}

.story-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 4;
}

.web-story-card:hover .story-overlay {
    opacity: 1;
}

.web-story-card:hover .story-title-bar {
    opacity: 0.8;
}

.story-content {
    color: white;
    width: 100%;
    text-align: center;
    position: relative;
    z-index: 2;
}

.story-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 10px;
    line-height: 1.3;
}

.story-description {
    font-size: 0.95rem;
    margin-bottom: 20px;
    opacity: 0.9;
    line-height: 1.4;
}

.story-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 20px;
}

.btn-view-story,
.btn-story-link {
    border-radius: 30px;
    padding: 12px 25px;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-view-story {
    background: #007bff;
    border: none;
    color: white;
}

.btn-view-story:hover {
    background: #0056b3;
    transform: scale(1.05);
}

.btn-story-link {
    background: transparent;
    border: 2px solid rgba(255,255,255,0.7);
    color: white;
    text-decoration: none;
}

.btn-story-link:hover {
    background: rgba(255,255,255,0.1);
    border-color: white;
    color: white;
    text-decoration: none;
}

.ai-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(45deg, #6c5ce7, #a29bfe);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.story-stats {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    color: #666;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.stat-item i {
    color: #007bff;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.empty-icon {
    margin-bottom: 30px;
    color: #ddd;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #333;
}

/* Modal styles */
.modal-story {
    max-width: 800px;
}

.modal-story .modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.modal-story .modal-header {
    background: linear-gradient(45deg, #007bff, #6c5ce7);
    color: white;
    border: none;
}

.modal-story .modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
}

.modal-story .modal-header .close:hover {
    opacity: 1;
}

/* Responsive design */
@media (max-width: 768px) {
    .web-stories-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .story-image-container {
        height: 300px;
    }
    
    .story-overlay {
        padding: 20px;
    }
    
    .story-title-main {
        font-size: 1.1rem;
    }
    
    .story-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-view-story,
    .btn-story-link {
        width: 100%;
        text-align: center;
        padding: 10px 20px;
    }
}

@media (max-width: 480px) {
    .web-stories-container {
        padding: 20px 0;
    }
    
    .section-header {
        margin-bottom: 30px;
    }
    
    .story-stats {
        padding: 10px 15px;
        font-size: 0.8rem;
    }
}
</style>

<script>
// Smooth hover effects and better UX
document.addEventListener('DOMContentLoaded', function() {
    // Initialize lazyload for images if available
    if (typeof lazyload !== 'undefined') {
        lazyload();
    }
    
    // Add smooth scroll to top when clicking story links
    const storyCards = document.querySelectorAll('.web-story-card');
    storyCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Only for the main card area, not buttons
            if (e.target.closest('.story-actions') || e.target.closest('.story-stats')) {
                return;
            }
            
            // Get the story ID and navigate to AMP story format
            const storyId = this.dataset.storyId;
            if (storyId) {
                window.location.href = '<?= base_url('web-stories/story/'); ?>' + storyId;
            }
        });
        
        // Add cursor pointer for clickable areas
        card.style.cursor = 'pointer';
    });
    
    // Prevent card click when clicking on action buttons
    const actionButtons = document.querySelectorAll('.story-actions a, .story-actions button');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>