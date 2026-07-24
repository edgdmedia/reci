<?php
/**
 * Dashboard notifications list.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items         = is_array( $args['items'] ?? null ) ? $args['items'] : [];
$empty_message = (string) ( $args['empty_message'] ?? 'No notifications.' );

if ( empty( $items ) ) :
	?>
	<p class="text-sm text-zinc-500"><?php echo esc_html( $empty_message ); ?></p>
	<?php
	return;
endif;
?>

<ul class="space-y-3">
	<?php foreach ( $items as $item ) : ?>
	<li class="border border-zinc-200 rounded-lg p-3 <?php echo ! empty( $item['is_read'] ) ? 'bg-white' : 'bg-amber-50'; ?>">
		<div class="flex items-start justify-between gap-4">
			<div>
				<p class="text-sm font-semibold text-zinc-800"><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></p>
				<p class="text-sm text-zinc-600 mt-1"><?php echo esc_html( (string) ( $item['message'] ?? '' ) ); ?></p>
				<?php if ( ! empty( $item['created_at'] ) ) : ?>
				<p class="text-xs text-zinc-400 mt-2"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( (string) $item['created_at'] ) ) ); ?></p>
				<?php endif; ?>
			</div>
			<div class="flex items-center gap-2 shrink-0">
				<?php if ( ! empty( $item['target_url'] ) ) : ?>
				<a href="<?php echo esc_url( (string) $item['target_url'] ); ?>" class="text-xs text-amber-600 hover:text-amber-700">Open</a>
				<?php endif; ?>
				<?php if ( empty( $item['is_read'] ) ) : ?>
				<button type="button" class="reci-mark-notification-read text-xs text-zinc-500 hover:text-zinc-700" data-notification-id="<?php echo esc_attr( (string) ( $item['id'] ?? '' ) ); ?>">Mark read</button>
				<?php endif; ?>
			</div>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
