<?php
/**
 * Partners & Affiliations carousel.
 *
 * Queries reci_partner posts and displays them as
 * a horizontal carousel with logo and name.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit();
}

$partner_posts = get_posts([
    'post_type'      => 'reci_partner',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);

if (empty($partner_posts)) {
    return;
}
?>

<div class="relative" data-partner-carousel>
	<div class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-4 -mb-4 scrollbar-hide cursor-grab active:cursor-grabbing select-none" style="scrollbar-width:none;-ms-overflow-style:none;" data-carousel-track>
		<?php foreach ($partner_posts as $partner): ?>
			<?php
			$partner_url  = get_post_meta($partner->ID, '_reci_partner_url', true);
			$logo_id      = get_post_thumbnail_id($partner->ID);
			$logo_url     = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
			$partner_name = esc_html(get_the_title($partner->ID));
			?>
			<div class="flex-shrink-0 w-[85%] sm:w-1/2 lg:w-1/3 xl:w-1/4 snap-start pointer-events-auto" data-carousel-item>
				<a
					href="<?php echo esc_url($partner_url ?: '#'); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="p-6 bg-neutral-50 rounded-lg border border-zinc-200 flex flex-col items-center justify-center gap-4 text-center hover:border-amber-400 hover:shadow-md transition-all h-full"
				>
					<?php if ($logo_url): ?>
						<img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($partner_name); ?>" class="h-16 w-auto max-w-[180px] object-contain rounded" loading="lazy">
					<?php else: ?>
						<div class="h-16 w-[180px] bg-zinc-100 rounded flex items-center justify-center text-zinc-400 text-xs font-medium">Logo</div>
					<?php endif; ?>
					<span class="text-neutral-700 text-base font-medium"><?php echo $partner_name; ?></span>
				</a>
			</div>
		<?php endforeach; ?>
	</div>

	<button data-carousel-prev class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-10 h-10 bg-white rounded-full shadow-md border border-zinc-200 flex items-center justify-center hover:bg-amber-400 hover:border-amber-400 transition-all z-10 cursor-pointer" aria-label="Previous partners">
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
	</button>
	<button data-carousel-next class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-10 h-10 bg-white rounded-full shadow-md border border-zinc-200 flex items-center justify-center hover:bg-amber-400 hover:border-amber-400 transition-all z-10 cursor-pointer" aria-label="Next partners">
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
	</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var carousel = document.querySelector('[data-partner-carousel]');
	if (!carousel) return;

	var track = carousel.querySelector('[data-carousel-track]');
	if (!track) return;

	// ── Arrow scroll ──────────────────────────────────────────────
	var prevBtn = carousel.querySelector('[data-carousel-prev]');
	var nextBtn = carousel.querySelector('[data-carousel-next]');

	function scrollBy(dir) {
		var w = track.querySelector('[data-carousel-item]');
		if (!w) return;
		var step = w.offsetWidth + 24;
		track.scrollBy({ left: dir * step, behavior: 'smooth' });
	}

	if (prevBtn) prevBtn.addEventListener('click', function() { scrollBy(-1); });
	if (nextBtn) nextBtn.addEventListener('click', function() { scrollBy(1); });

	// ── Mouse drag ─────────────────────────────────────────────────
	var isDown = false, startX, scrollLeft;

	track.addEventListener('mousedown', function(e) {
		isDown = true;
		track.classList.remove('cursor-grab');
		track.classList.add('cursor-grabbing');
		startX = e.pageX - track.offsetLeft;
		scrollLeft = track.scrollLeft;
	});

	track.addEventListener('mouseleave', function() {
		isDown = false;
		track.classList.add('cursor-grab');
		track.classList.remove('cursor-grabbing');
	});

	track.addEventListener('mouseup', function() {
		isDown = false;
		track.classList.add('cursor-grab');
		track.classList.remove('cursor-grabbing');
	});

	track.addEventListener('mousemove', function(e) {
		if (!isDown) return;
		e.preventDefault();
		var x = e.pageX - track.offsetLeft;
		var walk = (x - startX) * 1.5;
		track.scrollLeft = scrollLeft - walk;
	});

	// ── Auto-scroll (pause on hover / drag) ───────────────────────
	var autoInterval, isPaused = false;

	function startAuto() {
		stopAuto();
		autoInterval = setInterval(function() {
			if (isPaused) return;
			var w = track.querySelector('[data-carousel-item]');
			if (!w) return;
			var step = w.offsetWidth + 24;
			var maxScroll = track.scrollWidth - track.clientWidth;
			if (track.scrollLeft + step >= maxScroll - 4) {
				track.scrollTo({ left: 0, behavior: 'smooth' });
			} else {
				track.scrollBy({ left: step, behavior: 'smooth' });
			}
		}, 4000);
	}

	function stopAuto() { if (autoInterval) clearInterval(autoInterval); autoInterval = null; }

	carousel.addEventListener('mouseenter', function() { isPaused = true; });
	carousel.addEventListener('mouseleave', function() { isPaused = false; });
	track.addEventListener('mousedown', function() { isPaused = true; });
	track.addEventListener('mouseup', function() { isPaused = false; });

	startAuto();
});
</script>
