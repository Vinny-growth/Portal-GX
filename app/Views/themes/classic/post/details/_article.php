<?php $coverAlt = !empty($post->image_alt) ? $post->image_alt : $post->title; ?>
<div class="post-image">
    <div class="post-image-inner">
        <?php if (!empty($postImages) && countItems($postImages) > 0) : ?>
            <div class="show-on-page-load">
                <div id="post-detail-slider" class="post-detail-slider">
                    <div class="post-detail-slider-item">
                        <img src="<?= getPostImage($post, 'default'); ?>" class="img-responsive center-image" alt="<?= esc($coverAlt); ?>"/>
                        <?php if (!empty($post->image_description)): ?>
                            <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                        <?php endif; ?>
                    </div>
                    <?php foreach ($postImages as $image):
                        $imgBaseURL = getBaseURLByStorage($image->storage); ?>
                        <div class="post-detail-slider-item">
                            <img src="<?= $imgBaseURL . esc($image->image_default); ?>" class="img-responsive center-image" alt="<?= esc($coverAlt); ?>"/>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="post-detail-slider-nav" class="slider-nav post-detail-slider-nav">
                    <button class="prev"><i class="icon-arrow-left"></i></button>
                    <button class="next"><i class="icon-arrow-right"></i></button>
                </div>
            </div>
        <?php else:
            if (!empty($post->image_id) || !empty($post->image_url)):?>
                <img src="<?= getPostImage($post, 'default'); ?>" class="img-responsive center-image" alt="<?= esc($coverAlt); ?>"/>
                <?php if (!empty($post->image_description)): ?>
                    <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                <?php endif; ?>
            <?php endif;
        endif; ?>
    </div>
</div>
