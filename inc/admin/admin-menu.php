<?php
/**
 * Admin menu order.
 *
 * Twenty-two top-level entries listed the database rather than the work. This
 * groups them into four bands, separated by rules:
 *
 *   review    Submissions (content, applications, journals), Collaborators
 *   publish   Content, Media, Comments
 *   configure RECI Settings
 *   system    Pages, Appearance, Plugins, Users, Tools, Settings
 *
 * Pages sits with the system band on purpose: editing About or Contact is site
 * administration, not the recurring editorial work the top of the menu is for.
 *
 * Only menu placement changes here. Every URL stays what it was, so bookmarks
 * keep working and the whole thing is reversible.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rename Articles to Content, since it now parents every content type.
 */
add_action( 'admin_menu', 'reci_rename_content_menu', 11 );
function reci_rename_content_menu(): void {
	global $menu, $submenu;

	foreach ( $menu as $position => $item ) {
		if ( isset( $item[2] ) && 'edit.php' === $item[2] ) {
			$menu[ $position ][0] = __( 'Content', 'reci-media-hub' );
			$menu[ $position ][6] = 'dashicons-portfolio';
			break;
		}
	}

	// The parent is now called Content, so its own list needs to say Articles or
	// nothing in the submenu identifies it.
	if ( isset( $submenu['edit.php'][5][0] ) ) {
		$submenu['edit.php'][5][0] = __( 'Articles', 'reci-media-hub' );
	}
	if ( isset( $submenu['edit.php'][10][0] ) ) {
		$submenu['edit.php'][10][0] = __( 'Add New Article', 'reci-media-hub' );
	}
}

/**
 * Order the Submissions submenu.
 *
 * Its three children arrive from three different places — this file, the post
 * type registration and the journals table — so registration order is arbitrary.
 * The queue itself has to come first: it is what the menu is named after.
 */
add_action( 'admin_menu', 'reci_order_submissions_submenu', 998 );
function reci_order_submissions_submenu(): void {
	global $submenu;

	if ( empty( $submenu['reci-submissions'] ) ) {
		return;
	}

	$wanted = [ 'reci-submissions', 'edit.php?post_type=reci_collab_app', 'reci-journals' ];

	usort(
		$submenu['reci-submissions'],
		static function ( array $a, array $b ) use ( $wanted ): int {
			$rank = static function ( array $item ) use ( $wanted ): int {
				$index = array_search( $item[2] ?? '', $wanted, true );

				return false === $index ? count( $wanted ) : (int) $index;
			};

			return $rank( $a ) <=> $rank( $b );
		}
	);
}

/**
 * Reorder the top level and insert separators.
 *
 * Runs at 999 so every post type, settings page and plugin menu has registered.
 */
add_action( 'admin_menu', 'reci_reorder_admin_menu', 999 );
function reci_reorder_admin_menu(): void {
	global $menu;

	// Desired order, top to bottom. 'SEP' inserts a divider.
	$order = [
		'index.php',                    // Dashboard
		'SEP',
		'reci-submissions',             // + applications, journals
		'edit.php?post_type=reci_author', // Collaborators
		'SEP',
		'edit.php',                     // Content
		'upload.php',                   // Media
		'edit-comments.php',            // Comments
		'SEP',
		'reci-settings',                // RECI Settings + Email Log
		'SEP',
		'edit.php?post_type=page',      // Pages
		'themes.php',
		'plugins.php',
		'users.php',
		'tools.php',
		'options-general.php',
	];

	$by_slug = [];
	foreach ( $menu as $item ) {
		if ( isset( $item[2] ) ) {
			$by_slug[ $item[2] ] = $item;
		}
	}

	$rebuilt  = [];
	$position = 1;
	$sep      = 0;

	foreach ( $order as $slug ) {
		if ( 'SEP' === $slug ) {
			$rebuilt[ $position ] = [ '', 'read', 'reci-separator-' . $sep++, '', 'wp-menu-separator' ];
			$position            += 2;
			continue;
		}

		if ( isset( $by_slug[ $slug ] ) ) {
			$rebuilt[ $position ] = $by_slug[ $slug ];
			$position            += 2;
			unset( $by_slug[ $slug ] );
		}
	}

	// Anything not named above — a plugin's menu, or a post type added later —
	// keeps its entry and lands after the ordered set rather than disappearing.
	foreach ( $by_slug as $item ) {
		if ( isset( $item[4] ) && str_contains( (string) $item[4], 'wp-menu-separator' ) ) {
			continue;
		}

		$rebuilt[ $position ] = $item;
		$position            += 2;
	}

	$menu = $rebuilt;
}
