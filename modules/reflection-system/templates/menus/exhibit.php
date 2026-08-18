<?php
/**
 * Reflection menu overlay variant: exhibit.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'back_url' => '#',
	'items' => [],
	'subtitle' => '',
]);
?>
<style>
	.wh-menu.active { display: flex; }
</style>
<!-- Floating Menu Button -->
<div class="fixed top-6 right-6 z-50">
	<button class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface,#222)] shadow-xl transition hover:scale-105" type="button" id="menuToggle" aria-label="Open Menu">
		<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
		</svg>
	</button>
</div>

<!-- Menu Modal Overlay -->
<div class="wh-menu fixed inset-0 z-[60] hidden items-center justify-center bg-black/95 backdrop-blur-md px-5 py-8" id="menuOverlay" aria-hidden="true">
	<div class="relative w-full max-w-[880px] max-h-[90vh] overflow-y-auto rounded-[28px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface-alt)] p-8 sm:p-10">
		<!-- Header / Close button -->
		<div class="flex items-center justify-between mb-8 border-b border-[color:var(--reflection-border-soft)] pb-6">
			<a class="inline-flex items-center rounded-full border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] px-5 py-2 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white no-underline transition hover:bg-[var(--reflection-card)]" href="<?php echo esc_url($args['back_url']); ?>">
				← <?php echo esc_html($args['back_label'] ?? 'Back to Gallery'); ?>
			</a>
			
			<button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] text-white hover:bg-[var(--reflection-card)] transition" id="menuCloseBtn" aria-label="Close menu">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<h2 class="font-['Playfair_Display'] text-4xl font-semibold reci-reflection-text sm:text-5xl">Navigate the Exhibit</h2>
		<?php if (! empty($args['subtitle'])) : ?>
			<p class="mt-3 max-w-[48rem] text-base leading-8 reci-reflection-soft-text"><?php echo esc_html($args['subtitle']); ?></p>
		<?php endif; ?>
		
		<div class="mt-8 grid gap-4 sm:grid-cols-2">
			<?php foreach ((array) $args['items'] as $item) : ?>
				<?php
				$item_attributes = '';
				foreach ((array) ($item['attributes'] ?? []) as $attr_key => $attr_value) {
					$item_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
				}
				?>
				<a class="block rounded-[20px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] px-5 py-5 no-underline transition hover:bg-[var(--reflection-card)]" href="<?php echo esc_url($item['href'] ?? '#'); ?>"<?php echo $item_attributes; ?>>
					<strong class="mb-1 block font-['Oswald'] text-sm uppercase tracking-[0.08em] reci-reflection-accent"><?php echo esc_html($item['title']); ?></strong>
					<span class="text-sm leading-7 reci-reflection-soft-text"><?php echo esc_html($item['description']); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<script>
	(function() {
		const toggleBtn = document.getElementById('menuToggle');
		const closeBtn = document.getElementById('menuCloseBtn');
		const overlay = document.getElementById('menuOverlay');
		
		if (!toggleBtn || !overlay) return;
		
		// The legacy reflection-system-runtime.js might also bind to menuToggle, 
		// but we add this here to ensure closeBtn also works.
		if (closeBtn) {
			closeBtn.addEventListener('click', () => {
				overlay.classList.remove('active');
			});
		}
	})();
</script>
