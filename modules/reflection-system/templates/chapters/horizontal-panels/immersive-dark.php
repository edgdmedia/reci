<?php
/**
 * Voices of Resistance horizontal march stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-march',
	'items' => [],
	'continue_label' => 'Reflect',
]);
$items = (array) $args['items'];
?>
<?php
$track_id = 'track-' . uniqid();
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim($args['continue_target'] ?? '', '#');
?>
<section class="reci-stage chapter-march" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($transition_mode); ?>" data-continue-target="<?php echo esc_attr($continue_target); ?>">
	<div class="flex h-screen w-full snap-x snap-mandatory overflow-x-auto overflow-y-hidden scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" id="<?php echo esc_attr($track_id); ?>">
		<?php foreach ($items as $index => $item) : ?>
			<div class="relative flex h-screen w-screen flex-shrink-0 snap-center snap-always items-center justify-center overflow-hidden">
				<?php if (! empty($item['background_image'])) : ?>
					<div class="absolute inset-0 bg-cover bg-center opacity-30 grayscale" style="background-image:url('<?php echo esc_url($item['background_image']); ?>')"></div>
				<?php endif; ?>
				<div class="relative z-10 max-w-[640px] px-6 text-center">
					<?php if (! empty($item['accent_title'])) : ?>
						<h2 class="font-['Oswald'] text-4xl uppercase tracking-[0.25em] text-[var(--reflection-accent)] sm:text-5xl"><?php echo esc_html($item['accent_title']); ?></h2>
					<?php else : ?>
						<h2 class="font-['Playfair_Display'] text-4xl leading-tight text-white sm:text-5xl"><?php echo esc_html((string) ($item['title'] ?? '')); ?></h2>
					<?php endif; ?>
					<?php if (! empty($item['body'])) : ?>
						<p class="mt-5 text-lg leading-8 text-white/85"><?php echo esc_html($item['body']); ?></p>
					<?php endif; ?>
					
					<div class="mt-12 flex items-center justify-center gap-6">
						<?php if ($index > 0) : ?>
							<button type="button" class="border border-white/20 px-6 py-2 text-xs uppercase tracking-widest text-white/70 hover:bg-white hover:text-black transition" onclick="this.closest('.reci-stage').querySelector('#<?php echo esc_js($track_id); ?>').scrollBy({left: -window.innerWidth, behavior: 'smooth'})">Back</button>
						<?php endif; ?>
						
						<span class="text-xs text-white/50"><?php echo ($index + 1) . ' / ' . count($items); ?></span>
						
						<?php if ($index < count($items) - 1) : ?>
							<button type="button" class="border border-white/20 px-6 py-2 text-xs uppercase tracking-widest text-white/70 hover:bg-white hover:text-black transition" onclick="this.closest('.reci-stage').querySelector('#<?php echo esc_js($track_id); ?>').scrollBy({left: window.innerWidth, behavior: 'smooth'})">Next</button>
						<?php else : ?>
							<?php if ($transition_mode === 'button') : ?>
							<button class="bg-white px-6 py-2 font-['Oswald'] text-xs uppercase tracking-widest text-black transition hover:bg-white/80" type="button" data-stage-target="<?php echo esc_attr($continue_target); ?>"><?php echo esc_html($args['continue_label'] ?? 'Continue'); ?></button>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<script>
		(function() {
			const track = document.getElementById('<?php echo esc_js($track_id); ?>');
			if (!track) return;
			let isScrolling = false;
			track.addEventListener('wheel', (e) => {
				if (e.deltaY === 0 || Math.abs(e.deltaX) > Math.abs(e.deltaY)) return; // Allow native horizontal scroll
				e.preventDefault();
				if (isScrolling) return;
				
				const isAtEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 10;
				if (e.deltaY > 0 && isAtEnd) {
					const stage = track.closest('.reci-stage');
					if (stage && stage.dataset.transitionMode === 'scroll' && stage.dataset.continueTarget) {
						if (window.RECIReflectionController) window.RECIReflectionController.goTo(stage.dataset.continueTarget);
					}
					return;
				}
				
				isScrolling = true;
				track.scrollBy({ left: e.deltaY > 0 ? window.innerWidth : -window.innerWidth, behavior: 'smooth' });
				setTimeout(() => isScrolling = false, 600);
			}, { passive: false });
		})();
	</script>
</section>
