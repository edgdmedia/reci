<?php
/**
 * Site-wide flash notice.
 *
 * Driven by query args the auth handlers set on redirect, so an action that
 * ends in a redirect can still tell the user it worked. Rendered directly
 * below the header on every page.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notices = [
	'verified' => [
		'tone' => 'success',
		'text' => __( 'Your email is verified and you are signed in. Welcome to the Collaboratory.', 'reci-media-hub' ),
	],
	'signed_out' => [
		'tone' => 'info',
		'text' => __( 'You have been signed out.', 'reci-media-hub' ),
	],
];

$notice = null;
foreach ( $notices as $key => $config ) {
	if ( ! empty( $_GET[ $key ] ) ) {
		$notice = $config;
		break;
	}
}

if ( null === $notice ) {
	return;
}

$tones = [
	'success' => 'border-green-200 bg-green-50 text-green-800',
	'info'    => 'border-blue-200 bg-blue-50 text-blue-800',
];
$classes = $tones[ $notice['tone'] ] ?? $tones['info'];
?>

<div class="w-full px-4 pt-4 sm:px-6 lg:px-8">
	<div class="mx-auto flex max-w-7xl items-start gap-3 rounded-lg border px-4 py-3 text-sm <?php echo esc_attr( $classes ); ?>" role="status">
		<svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
		</svg>
		<p class="leading-6"><?php echo esc_html( $notice['text'] ); ?></p>
	</div>
</div>
