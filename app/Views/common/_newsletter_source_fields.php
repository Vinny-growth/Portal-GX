<?php
$srcPostId = (isset($post) && !empty($post->id)) ? (int) $post->id : '';
$srcCategoryId = '';
if (isset($post) && !empty($post->category_id)) {
    $srcCategoryId = (int) $post->category_id;
} elseif (isset($category) && !empty($category->id)) {
    $srcCategoryId = (int) $category->id;
} elseif (isset($activeCategoryId) && !empty($activeCategoryId)) {
    $srcCategoryId = (int) $activeCategoryId;
}
$srcUrl = function_exists('current_url') ? current_url() : '';
?>
<input type="hidden" name="source_post_id" value="<?= esc($srcPostId); ?>">
<input type="hidden" name="source_category_id" value="<?= esc($srcCategoryId); ?>">
<input type="hidden" name="source_url" value="<?= esc($srcUrl); ?>">
