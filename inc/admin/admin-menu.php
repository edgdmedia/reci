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

}

/**
 * Drop "All" from menu labels WordPress owns.
 *
 * Our post types set all_items themselves, but Pages is core's and the
 * Taxonomies parent needs the opposite treatment — there "All Taxonomies" is
 * accurate, because that entry really does list every one of them.
 */
add_action( 'admin_menu', 'reci_simplify_submenu_labels', 998 );
function reci_simplify_submenu_labels(): void {
	global $submenu;

	$rename = [
		'edit.php?post_type=page' => [ 'edit.php?post_type=page' => __( 'Pages', 'reci-media-hub' ) ],
		'users.php'               => [ 'users.php' => __( 'Users', 'reci-media-hub' ) ],
		'reci-taxonomies'         => [ 'reci-taxonomies' => __( 'All Taxonomies', 'reci-media-hub' ) ],
	];

	foreach ( $rename as $parent => $slugs ) {
		if ( empty( $submenu[ $parent ] ) ) {
			continue;
		}

		foreach ( $submenu[ $parent ] as $key => $item ) {
			if ( isset( $item[2], $slugs[ $item[2] ] ) ) {
				$submenu[ $parent ][ $key ][0] = $slugs[ $item[2] ];
			}
		}
	}
}

/**
 * Point taxonomy screens at the Taxonomies menu.
 *
 * WordPress works out the active menu from the post type a taxonomy is
 * registered against, so opening Categories lit up Content and opening
 * Affiliations lit up Collaborators — the menu the term actually lives in was
 * never the one highlighted.
 */
add_filter( 'parent_file', 'reci_taxonomy_parent_file' );
function reci_taxonomy_parent_file( string $parent_file ): string {
	$screen = get_current_screen();

	if ( $screen && in_array( $screen->base, [ 'edit-tags', 'term' ], true ) && ! empty( $screen->taxonomy ) ) {
		$names = wp_list_pluck( reci_listable_taxonomies(), 'name' );

		if ( in_array( $screen->taxonomy, $names, true ) ) {
			return 'reci-taxonomies';
		}
	}

	return $parent_file;
}

add_filter( 'submenu_file', 'reci_taxonomy_submenu_file' );
function reci_taxonomy_submenu_file( $submenu_file ) {
	$screen = get_current_screen();

	if ( $screen && in_array( $screen->base, [ 'edit-tags', 'term' ], true ) && ! empty( $screen->taxonomy ) ) {
		$names = wp_list_pluck( reci_listable_taxonomies(), 'name' );

		if ( in_array( $screen->taxonomy, $names, true ) ) {
			// Must match the slug the submenu was registered with, which carries
			// no post_type argument — WordPress's default value does, so it never
			// matched and no child was highlighted either.
			return 'edit-tags.php?taxonomy=' . $screen->taxonomy;
		}
	}

	return $submenu_file;
}

/**
 * Drop "Add New Article" from the Content menu.
 *
 * Content is a library to browse; the admin bar's + New covers the occasional
 * article written in wp-admin, and collaborators write through the dashboard.
 */
add_action( 'admin_menu', 'reci_trim_content_submenu', 12 );
function reci_trim_content_submenu(): void {
	remove_submenu_page( 'edit.php', 'post-new.php' );
}

/**
 * Collect every taxonomy into one menu.
 *
 * WordPress files a taxonomy under whichever post type owns it, so Categories
 * and Tags sat inside Content while Affiliations and Subject Areas sat inside
 * Collaborators, and Spheres appeared in both. Mixing ten post types with six
 * taxonomies in one list makes both harder to scan, and a taxonomy that applies
 * to several post types has no natural home among them.
 *
 * Runs before the reorder so the new parent is in place when the order is built.
 */
add_action( 'admin_menu', 'reci_group_taxonomy_menus', 997 );
function reci_group_taxonomy_menus(): void {
	global $submenu;

	add_menu_page(
		__( 'Taxonomies', 'reci-media-hub' ),
		__( 'Taxonomies', 'reci-media-hub' ),
		'manage_categories',
		'reci-taxonomies',
		'reci_render_taxonomies_index',
		'dashicons-tag',
		31
	);

	// Strip taxonomy entries from wherever WordPress filed them. The same
	// taxonomy appears under every post type that uses it, each with a different
	// post_type query arg, so this cannot dedupe by slug.
	foreach ( $submenu as $parent => $items ) {
		if ( 'reci-taxonomies' === $parent ) {
			continue;
		}

		foreach ( $items as $key => $item ) {
			if ( isset( $item[2] ) && str_starts_with( (string) $item[2], 'edit-tags.php?taxonomy=' ) ) {
				unset( $submenu[ $parent ][ $key ] );
			}
		}

		if ( isset( $submenu[ $parent ] ) ) {
			$submenu[ $parent ] = array_values( $submenu[ $parent ] );
		}
	}

	// Build from the registry rather than from what was in the menu. Scavenging
	// missed any taxonomy whose post type is itself a submenu now — Shows and
	// Target Audiences had no top-level parent left to be filed under.
	foreach ( reci_listable_taxonomies() as $taxonomy ) {
		add_submenu_page(
			'reci-taxonomies',
			$taxonomy->labels->name,
			$taxonomy->labels->menu_name ?? $taxonomy->labels->name,
			$taxonomy->cap->manage_terms,
			'edit-tags.php?taxonomy=' . $taxonomy->name
		);
	}
}

if ( ! function_exists( 'reci_listable_taxonomies' ) ) {
	/**
	 * Taxonomies worth showing in the menu.
	 *
	 * Excludes WordPress's own plumbing: link categories are a legacy feature
	 * this site does not use, and pattern categories belong to the block editor
	 * rather than to RECI's vocabulary.
	 *
	 * @return array<int,WP_Taxonomy>
	 */
	function reci_listable_taxonomies(): array {
		$hidden = [ 'link_category', 'wp_pattern_category', 'nav_menu', 'post_format' ];

		$taxonomies = array_filter(
			get_taxonomies( [ 'show_ui' => true ], 'objects' ),
			static fn( WP_Taxonomy $taxonomy ): bool => ! in_array( $taxonomy->name, $hidden, true )
		);

		uasort(
			$taxonomies,
			static fn( WP_Taxonomy $a, WP_Taxonomy $b ): int => strcasecmp( $a->labels->name, $b->labels->name )
		);

		return array_values( $taxonomies );
	}
}

/**
 * Landing page for the Taxonomies menu.
 *
 * The parent needs somewhere to go, and a list with term counts is more use
 * than redirecting to whichever taxonomy happens to be first.
 */
function reci_render_taxonomies_index(): void {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	echo '<div class="wrap"><h1>' . esc_html__( 'Taxonomies', 'reci-media-hub' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'The vocabularies used to classify content and collaborators.', 'reci-media-hub' ) . '</p>';
	echo '<table class="widefat striped" style="max-width:900px;"><thead><tr>';
	echo '<th>' . esc_html__( 'Taxonomy', 'reci-media-hub' ) . '</th>';
	echo '<th>' . esc_html__( 'Terms', 'reci-media-hub' ) . '</th>';
	echo '<th>' . esc_html__( 'Applies to', 'reci-media-hub' ) . '</th>';
	echo '</tr></thead><tbody>';

	foreach ( reci_listable_taxonomies() as $taxonomy ) {
		$types = array_map(
			static function ( string $type ): string {
				$object = get_post_type_object( $type );

				return $object->labels->name ?? $type;
			},
			(array) $taxonomy->object_type
		);

		printf(
			'<tr><td><a href="%s"><strong>%s</strong></a></td><td>%s</td><td>%s</td></tr>',
			esc_url( admin_url( 'edit-tags.php?taxonomy=' . $taxonomy->name ) ),
			esc_html( $taxonomy->labels->name ),
			esc_html( (string) wp_count_terms( [ 'taxonomy' => $taxonomy->name, 'hide_empty' => false ] ) ),
			esc_html( implode( ', ', $types ) )
		);
	}

	echo '</tbody></table></div>';
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
		'reci-taxonomies',              // every taxonomy, gathered
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
