<?php
/**
 * Articles section right rail block (from herov3.php lines 325-439).
 *
 * @var array $args
 */

$items = is_array($args['items'] ?? null) ? $args['items'] : [];
?>

<div data-layer="Content" class="Content w-full lg:flex-1 lg:min-h-[700px] lg:pl-10 pt-6 lg:pt-0 border-t-[0.50px] lg:border-t-0 lg:border-l-[0.50px] border-zinc-400 inline-flex flex-col justify-start items-start gap-5 overflow-hidden">
    <?php foreach ($items as $index => $item) : ?>
        <?php get_template_part('template-parts/listings/articles-side-card', null, $item); ?>
        <?php if ($index < count($items) - 1) : ?>
            <div data-layer="Vector" class="divider divider-stone"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
