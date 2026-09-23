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
<?php if ( is_user_logged_in() ) : ?>
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
