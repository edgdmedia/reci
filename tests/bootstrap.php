<?php
/**
 * Minimal WordPress stubs so pure theme functions load outside WordPress.
 *
 * Only declare what the functions under test actually call. Each stub is
 * guarded, so if this ever runs inside a real WordPress the real one wins.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['reci_test_options'] = [];

/**
 * Seed the fake option store used by get_option()/update_option() stubs.
 *
 * @param array<string,mixed> $options Option name => value.
 */
function reci_test_stub_options( array $options ): void {
	$GLOBALS['reci_test_options'] = $options;
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( string $name, $default = false ) {
		return $GLOBALS['reci_test_options'][ $name ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( string $name, $value ): bool {
		$GLOBALS['reci_test_options'][ $name ] = $value;
		return true;
	}
}

if ( ! function_exists( '__' ) ) {
	function __( string $text, string $domain = '' ): string {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( string $text, string $domain = '' ): string {
		return $text;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ): int {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( string $value ): string {
		return trim( strip_tags( $value ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( string $value ): string {
		return trim( strip_tags( $value ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( string $type, $gmt = 0 ): string {
		return gmdate( 'Y-m-d H:i:s' );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, int $options = 0, int $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( string $hook_name, $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( string $hook_name, $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return true;
	}
}
