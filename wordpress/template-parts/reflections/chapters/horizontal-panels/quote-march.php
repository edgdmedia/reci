<?php
/**
 * Reflection chapter horizontal panels variant: quote-march.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'march',
	'items' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="relative min-h-screen w-full overflow-hidden bg-black" id="vorMarchStage">
		<div id="vorMarchTrack" class="flex h-screen w-[300vw] will-change-transform transition-transform duration-700 ease-out">
			<?php foreach ((array) $args['items'] as $index => $item) : ?>
				<div class="relative flex h-screen w-screen flex-shrink-0 items-center justify-center overflow-hidden">
					<?php if (! empty($item['background_image'])) : ?>
						<div class="absolute inset-0 bg-cover bg-center opacity-30 grayscale" style="background-image:url('<?php echo esc_url($item['background_image']); ?>');"></div>
					<?php endif; ?>
					<div class="relative z-10 max-w-[640px] px-6 text-center">
						<h2 class="font-['Playfair_Display'] text-4xl leading-tight text-white sm:text-5xl"><?php echo esc_html($item['title'] ?? ''); ?></h2>
						<?php if (! empty($item['body'])) : ?>
							<p class="mt-5 text-lg leading-8 text-white/85"><?php echo esc_html($item['body']); ?></p>
						<?php endif; ?>
						<?php if ($index === count((array) $args['items']) - 1) : ?>
							<button type="button" class="mt-8 inline-flex items-center justify-center border border-white/40 px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
