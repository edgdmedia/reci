<?php
/**
 * Cross-post-type card for See Also / Read More grids.
 * Delegates to the appropriate archive card based on post_type.
 */

if (!defined('ABSPATH')) {
    exit;
}

$item = $args;
if (empty($item) || !is_array($item)) {
    return;
}

$post_type = $item['post_type'] ?? '';

// All post types use the same generic listing card layout
get_template_part('template-parts/listings/articles-side-card', null, $item);
