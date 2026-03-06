<?php
/**
 * Podcast compact row block (from herov3.php lines 707-896).
 *
 * @var array $args
 */

$items = is_array($args['items'] ?? null) ? $args['items'] : [];
?>

<div data-layer="Content" class="Content w-full flex flex-col lg:flex-row justify-start items-stretch">
    <?php foreach ($items as $index => $item) : ?>
        <?php get_template_part('template-parts/listings/podcast-compact-card', null, $item); ?>
        <?php if ($index < count($items) - 1) : ?>
            <div class="hidden lg:block w-px self-stretch flex-shrink-0 bg-zinc-400"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
