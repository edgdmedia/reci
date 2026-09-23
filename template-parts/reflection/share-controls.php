<?php
/**
 * Share controls for a reflection prompt.
 *
 * Included by every live `reflection-prompt` variant. There are two families —
 * the panel style (journal, exit-stage) and the full-screen stage style
 * (minimal, immersive-dark, protest-march) — so `style` picks the skin while
 * the markup and hooks stay identical.
 *
 * Deliberately no `id` attributes: a reflection may contain several prompt
 * chapters, and duplicate ids would leave every control after the first inert.
 * Everything is found by data attribute, scoped to the nearest `.reci-stage`.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reci_share = wp_parse_args(
	$args ?? [],
	[
		'style' => 'panel',
		// 'all', 'toggles' or 'read'. Variants that want the read button on the
		// same row as their own continue button render the two parts apart.
		'show'  => 'all',
	]
);

$reci_is_stage = 'stage' === $reci_share['style'];

$reci_wrap_class = $reci_is_stage
	? 'reci-share-controls mt-6 grid w-full max-w-[600px] gap-3 text-left mx-auto'
	: 'reci-share-controls mt-4 grid gap-3';

$reci_label_class = $reci_is_stage
	? 'flex items-start gap-3 text-sm text-white/80'
	: 'flex items-start gap-3 text-sm reci-reflection-soft-text';

$reci_hint_class = $reci_is_stage
	? 'block text-xs text-white/55'
	: 'block text-xs opacity-80';
?>
<?php
// Sharing needs an account, so these are for members only - the variants
// already tell a signed-out visitor to log in. Reading what others shared
// needs no account, and now happens in its own chapter.
?>
<?php if ( 'read' !== $reci_share['show'] && is_user_logged_in() ) : ?>
<div class="<?php echo esc_attr( $reci_wrap_class ); ?>" data-reci-share-controls>
	<label class="<?php echo esc_attr( $reci_label_class ); ?>">
		<input type="checkbox" class="mt-1" data-reci-share />
		<span>
			<?php esc_html_e( 'Share this reflection with others', 'reci-media-hub' ); ?>
			<span class="<?php echo esc_attr( $reci_hint_class ); ?>"><?php esc_html_e( 'A moderator reads it before it appears. You can withdraw it at any time.', 'reci-media-hub' ); ?></span>
		</span>
	</label>
	<label class="<?php echo esc_attr( $reci_label_class ); ?>" data-reci-anon-wrap hidden>
		<input type="checkbox" class="mt-1" data-reci-anonymous />
		<span>
			<?php esc_html_e( 'Share anonymously', 'reci-media-hub' ); ?>
			<span class="<?php echo esc_attr( $reci_hint_class ); ?>"><?php esc_html_e( 'Your name is hidden from other readers. The RECI team can still see it, so that abuse can be acted on.', 'reci-media-hub' ); ?></span>
		</span>
	</label>
</div>
<?php endif; ?>

<?php
if ( 'toggles' === $reci_share['show'] ) {
	return;
}

// The shared reflections chapter is a stage like any other, and stages are
// hidden until the controller activates one. Without a link into it the
// chapter is reachable only from the menu, which is how it went unnoticed.
$reci_shared_count = function_exists( 'reci_get_shared_journal_count' )
	? reci_get_shared_journal_count( (int) get_the_ID() )
	: 0;

if ( $reci_shared_count < 1 ) {
	return;
}

$reci_is_stage = 'stage' === $reci_share['style'];

$reci_button_class = $reci_is_stage
	? 'reci-open-shared inline-flex items-center justify-center border border-white/60 px-8 py-3 font-[\'Oswald\'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white hover:text-black transition-colors'
	: 'reci-open-shared inline-flex items-center justify-center rounded-full border border-[color:var(--reflection-border)] px-6 py-3 font-[\'Oswald\'] text-xs uppercase tracking-[0.1em] reci-reflection-text';
?>
<button
	type="button"
	class="<?php echo esc_attr( $reci_button_class ); ?>"
	data-stage-target="reci-shared-journals"
>
	<?php
	printf(
		/* translators: %d: number of shared reflections */
		esc_html( _n( 'Read %d shared reflection', 'Read %d shared reflections', $reci_shared_count, 'reci-media-hub' ) ),
		(int) $reci_shared_count
	);
	?>
</button>
