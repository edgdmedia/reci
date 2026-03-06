<?php

/**
 * Lens quiz row block (from herov3.php lines 907-976).
 *
 * @var array $args
 */

$cards = is_array($args['cards'] ?? null) ? $args['cards'] : [];
?>

<div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start">
    <div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
    <div data-layer="Content" class="Content self-stretch px-4 sm:px-6 lg:px-14 flex flex-col lg:flex-row justify-start items-stretch gap-5">
        <div data-layer="Vector 5" class="Vector5 hidden lg:block w-0 self-stretch outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
        <?php foreach ($cards as $index => $card) : ?>
            <?php get_template_part('template-parts/listings/lens-quiz-card', null, $card); ?>
            <?php if ($index < count($cards) - 1) : ?>
                <div data-layer="Vector" class="hidden lg:block w-0 self-stretch outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
            <?php endif; ?>
        <?php endforeach; ?>
        <div data-layer="Vector 2" class="Vector2 hidden lg:block w-0 self-stretch outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
    </div>
    <div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
</div>