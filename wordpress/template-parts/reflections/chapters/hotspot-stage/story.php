<?php
/**
 * Reflection chapter hotspot stage variant: story.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'explore',
	'instruction' => 'Tap the Icons to Uncover the Story',
	'background_image' => '',
	'hotspots' => [],
	'continue_label' => 'Scroll Down to Continue ↓',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="relative min-h-screen w-full overflow-hidden">
		<div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo esc_url($args['background_image']); ?>');"></div>
		<div class="absolute inset-0 bg-black/35"></div>
		<div class="absolute inset-x-0 top-24 z-10 text-center pointer-events-none">
			<h3 class="font-['Oswald'] text-lg font-light uppercase tracking-[0.3em] text-white/85 drop-shadow"><?php echo esc_html($args['instruction']); ?></h3>
		</div>
		<div class="relative z-10 min-h-screen">
			<?php foreach ((array) $args['hotspots'] as $index => $hotspot) : ?>
				<button
					type="button"
					class="vor-hotspot absolute flex h-[50px] w-[50px] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-[var(--reflection-accent)] bg-[color:rgba(255,184,28,0.2)] text-white transition hover:scale-110 hover:bg-[color:rgba(255,184,28,0.4)]"
					style="top: <?php echo esc_attr((string) ($hotspot['top'] ?? '50%')); ?>; left: <?php echo esc_attr((string) ($hotspot['left'] ?? '50%')); ?>;"
					data-detail-key="<?php echo esc_attr($hotspot['key'] ?? ''); ?>"
				>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="8" x2="12" y2="16"></line>
						<line x1="8" y1="12" x2="16" y2="12"></line>
					</svg>
				</button>
			<?php endforeach; ?>

			<div class="pointer-events-none absolute inset-0 flex items-center justify-center px-5">
				<div id="vorDetailPanel" class="pointer-events-auto invisible w-full max-w-[420px] rounded-[24px] border border-[var(--reflection-accent)] bg-black/95 p-8 text-center opacity-0 transition">
					<h4 id="vorDetailTitle" class="font-['Oswald'] text-2xl uppercase tracking-[0.08em] text-[var(--reflection-accent)]">Title</h4>
					<p id="vorDetailText" class="mt-3 font-['Playfair_Display'] text-xl leading-8 text-white/90">Text</p>
					<button type="button" id="vorDetailClose" class="mt-6 inline-flex items-center justify-center border border-white/30 px-5 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white">Close</button>
				</div>
			</div>

			<button type="button" id="vorExploreContinue" class="invisible absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white/80 opacity-0 transition" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
		</div>
	</div>
</section>
