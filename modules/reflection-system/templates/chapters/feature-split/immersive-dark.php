<?php

/**
 * Reflection chapter feature split variant: immersive dark.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'feature-split',
	'eyebrow' => '',
	'title' => '',
	'body' => '',
	'image' => '',
	'image_alt' => '',
	'caption' => '',
	'note' => '',
	'media_side' => 'left',
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
$media_first = ($args['media_side'] ?? 'left') !== 'right';
?>
<section class="reci-stage chapter-feature-split min-h-screen px-5 py-16 text-center reci-reflection-text" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<div class="mx-auto flex min-h-[70vh] max-w-[1200px] flex-col items-center justify-center">
		<div class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:items-center w-full">
			<?php if ($media_first && ! empty($args['image'])) : ?>
				<div class="overflow-hidden border border-white/20 bg-black/40 shadow-[0_24px_60px_rgba(0,0,0,0.5)] relative">
					<img class="block h-[320px] w-full object-cover sm:h-[420px] lg:h-[60vh] opacity-80" src="<?php echo esc_url($args['image']); ?>" alt="<?php echo esc_attr($args['image_alt']); ?>">
					<div class="absolute inset-0 bg-black/20 mix-blend-multiply pointer-events-none"></div>
					<?php if ($args['caption']) : ?><div class="absolute bottom-0 w-full bg-black/80 px-5 py-4 text-xs tracking-wider text-white/70 uppercase font-['Oswald']"><?php echo esc_html($args['caption']); ?></div><?php endif; ?>
				</div>
			<?php endif; ?>
			
			<div class="text-left max-h-[60vh] overflow-y-auto pr-4 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-white/20 <?php echo empty($args['image']) ? 'col-span-full text-center max-w-[760px] mx-auto' : ''; ?>">
				<?php if ($args['eyebrow']) : ?><div class="font-['Oswald'] text-sm uppercase tracking-[0.18em] reci-reflection-accent mb-6"><?php echo esc_html($args['eyebrow']); ?></div><?php endif; ?>
				<?php if ($args['title']) : ?><h2 class="font-['Cinzel'] text-3xl/snug uppercase tracking-[0.18em] reci-reflection-text sm:text-4xl/snug mb-6"><?php echo esc_html($args['title']); ?></h2><?php endif; ?>
				
				<div class="mt-6 text-lg leading-8 text-white/90">
					<?php echo wp_kses_post($args['body']); ?>
				</div>
				
				<?php if ($args['note']) : ?>
					<div class="mt-8 border-l-2 border-[var(--reflection-accent)] pl-6 py-2 text-base italic text-white/70">
						<?php echo esc_html($args['note']); ?>
					</div>
				<?php endif; ?>
				
				<?php if (! empty($args['continue_target']) && ! empty($args['continue_label']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
					<div class="mt-12 mb-4 <?php echo empty($args['image']) ? 'text-center' : 'text-left'; ?>">
						<button class="inline-flex items-center justify-center border border-white px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.18em] text-white no-underline transition-colors hover:bg-white hover:text-black" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>">
							<?php echo esc_html($args['continue_label']); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
			
			<?php if (! $media_first && ! empty($args['image'])) : ?>
				<div class="overflow-hidden border border-white/20 bg-black/40 shadow-[0_24px_60px_rgba(0,0,0,0.5)] relative">
					<img class="block h-[320px] w-full object-cover sm:h-[420px] lg:h-[60vh] opacity-80" src="<?php echo esc_url($args['image']); ?>" alt="<?php echo esc_attr($args['image_alt']); ?>">
					<div class="absolute inset-0 bg-black/20 mix-blend-multiply pointer-events-none"></div>
					<?php if ($args['caption']) : ?><div class="absolute bottom-0 w-full bg-black/80 px-5 py-4 text-xs tracking-wider text-white/70 uppercase font-['Oswald']"><?php echo esc_html($args['caption']); ?></div><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
