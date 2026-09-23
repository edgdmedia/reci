<?php
/**
 * Shared reflections chapter.
 *
 * Reads as any other chapter in the piece: a full stage the reader scrolls
 * into, in the reflection's own palette. The render service appends it when a
 * reflection has approved shared entries, so it is never an empty section.
 *
 * The cards are filled over REST on load rather than printed here, so the
 * chapter does not grow with the number of entries.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'reci-shared-journals',
	'eyebrow' => 'From others',
	'title' => 'Shared reflections',
	'intro' => 'What other people chose to share on this reflection. Some are anonymous.',
	'reflection_id' => 0,
	'count' => 0,
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body justify-center">
			<div
				class="mx-auto flex w-full max-w-[1040px] flex-col rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] px-6 py-10 sm:px-8 lg:px-12 lg:py-14"
				data-reci-shared-chapter
				data-reflection-id="<?php echo esc_attr((string) $args['reflection_id']); ?>"
			>
				<?php if ($args['eyebrow']) : ?>
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.14em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>

				<h2 class="mt-4 font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl">
					<?php echo esc_html($args['title']); ?>
				</h2>

				<p class="mt-5 max-w-[44rem] text-base leading-8 reci-reflection-soft-text sm:text-lg sm:leading-9">
					<?php echo esc_html($args['intro']); ?>
				</p>

				<div data-reci-shared-list class="mt-8 grid gap-4 md:grid-cols-2"></div>

				<p data-reci-shared-status class="mt-6 text-sm reci-reflection-muted"></p>

				<?php
				// Appended last, so there is no next chapter to continue to. The
				// reader needs a deliberate way out or they are stranded here.
				?>
				<div class="mt-8 flex flex-wrap items-center gap-4">
					<a
						href="<?php echo esc_url( home_url( '/reflections/' ) ); ?>"
						class="inline-flex items-center justify-center rounded-full border border-[color:var(--reflection-border)] px-6 py-3 font-['Oswald'] text-xs uppercase tracking-[0.1em] reci-reflection-text no-underline"
					><?php esc_html_e( 'Return to Gallery', 'reci-media-hub' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>
