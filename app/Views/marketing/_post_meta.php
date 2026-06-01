<?php
$postItem = $postItem ?? $post ?? null;
if (empty($postItem)) {
    return;
}

$metaItems = [];
if ($generalSettings->show_post_author == 1 && !empty($postItem->author_slug)) {
    $metaItems[] = '<a href="' . esc(generateProfileURL($postItem->author_slug)) . '" class="gx-post-meta-link">' . esc(characterLimiter($postItem->author_username, 24, '...')) . '</a>';
}
if ($generalSettings->show_post_date == 1 && !empty($postItem->created_at)) {
    $metaItems[] = '<span>' . esc(formatDateFront($postItem->created_at)) . '</span>';
}
if ($generalSettings->comment_system == 1) {
    $metaItems[] = '<span>' . esc((string)$postItem->comment_count) . ' comentários</span>';
}
if ($generalSettings->show_hits) {
    $views = isset($postItem->pageviews_count) ? $postItem->pageviews_count : $postItem->pageviews;
    $metaItems[] = '<span>' . esc((string)numberFormatShort($views)) . ' visualizações</span>';
}
?>
<?php if (!empty($metaItems)): ?>
    <span class="gx-post-meta-list"><?= implode('<span class="gx-post-meta-sep" aria-hidden="true">•</span>', $metaItems); ?></span>
<?php endif; ?>
