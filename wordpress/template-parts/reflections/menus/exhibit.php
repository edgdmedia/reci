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
]);
?>
<style>
	.wh-menu.active { display: flex; }
</style>
<nav class="pointer-events-none fixed inset-x-0 top-0 z-50 flex items-center justify-between px-5 py-5 sm:px-6 lg:px-8">
	<a class="pointer-events-auto inline-flex items-center rounded-full border border-[color:var(--reflection-border-soft)] bg-black/35 px-4 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white no-underline" href="<?php echo esc_url($args['back_url']); ?>">Back to Gallery</a>
	<button class="pointer-events-auto inline-flex items-center rounded-full border border-[color:var(--reflection-border-soft)] bg-black/35 px-4 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white" type="button" id="menuToggle">Exhibit Menu</button>
</nav>
<div class="wh-menu fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 px-5 py-8" id="menuOverlay" aria-hidden="true">
	<div class="w-full max-w-[880px] rounded-[28px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface-alt)] p-8 sm:p-10">
		<h2 class="font-['Playfair_Display'] text-4xl font-semibold text-[var(--reflection-text)] sm:text-5xl">Navigate the Exhibit</h2>
		<p class="mt-3 max-w-[48rem] text-base leading-8 text-[var(--reflection-soft-text)]">Move through the exhibit in the order laid out in the curatorial narrative.</p>
		<div class="mt-8 grid gap-4 sm:grid-cols-2">
			<?php foreach ((array) $args['items'] as $item) : ?>
				<?php
				$item_attributes = '';
				foreach ((array) ($item['attributes'] ?? []) as $attr_key => $attr_value) {
					$item_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
				}
				?>
				<a class="block rounded-[20px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] px-5 py-5 no-underline transition hover:bg-[var(--reflection-card)]" href="<?php echo esc_url($item['href'] ?? '#'); ?>"<?php echo $item_attributes; ?>>
					<strong class="mb-1 block font-['Oswald'] text-sm uppercase tracking-[0.08em] text-[var(--reflection-accent)]"><?php echo esc_html($item['title']); ?></strong>
					<span class="text-sm leading-7 text-[var(--reflection-soft-text)]"><?php echo esc_html($item['description']); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</div>
