<?php

/**
 * Podcast compact row block.
 *
 * @var array $args
 */

$items = is_array($args['items'] ?? null) ? $args['items'] : [];
?>

<div data-layer="Content" class="Content w-full grid grid-cols-1 md:grid-cols-3  gap-6 lg:gap-1">
    <?php foreach ($items as $index => $item) : ?>
        <div class="w-full">
            <?php get_template_part('template-parts/listings/podcast-compact-card', null, $item); ?>
        </div>
    <?php endforeach; ?>
</div>
