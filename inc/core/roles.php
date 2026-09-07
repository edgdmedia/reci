<?php
/**
 * RECI role ladder.
 *
 * Six levels, mapped onto WordPress roles wherever a stock role already means
 * the right thing:
 *
 *   1 subscriber        Member                  read, comment, journal
 *   2 contributor       Collaborator            writes own work, cannot publish
 *   3 author            Publishing Collaborator publishes own work
 *   4 editor            Editor                  publishes others', approves collaborators
 *   5 reci_site_manager Site Manager            + plugin updates, user roles
 *   6 administrator     Administrator           everything
 *
 * Level 2 is Contributor rather than Author on purpose. "Writes own work but
 * cannot publish" is exactly what Contributor means in WordPress, so the review
 * queue is enforced by the role itself instead of by our code remembering to set
 * a status. Author (level 3) is the same person promoted to self-publish.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bump when the definitions below change, so installs re-apply them.
 */
const RECI_ROLES_VERSION = '1.0.0';

/**
 * Custom capabilities, so screens gate on intent rather than on a borrowed cap.
 *
 * @return array<int,string>
 */
function reci_custom_capabilities(): array {
	return [
		// Approve or reject collaborator applications.
		'reci_approve_collaborators',
		// Confirm a registration that has not verified by email.
		'reci_confirm_registrations',
		// Reach the wp-admin side of the site at all.
		'reci_access_admin',
	];
}

/**
 * Display names, so the admin reads in RECI's language rather than WordPress's.
 *
 * @return array<string,string>
 */
function reci_role_display_names(): array {
	return [
		'subscriber'  => __( 'Member', 'reci-media-hub' ),
		'contributor' => __( 'Collaborator', 'reci-media-hub' ),
		'author'      => __( 'Publishing Collaborator', 'reci-media-hub' ),
	];
}

add_action( 'init', 'reci_maybe_install_roles', 5 );

/**
 * Install or refresh the role ladder.
 *
 * Roles live in the database, so this runs once per version rather than on every
 * request.
 */
function reci_maybe_install_roles(): void {
	if ( get_option( 'reci_roles_version' ) === RECI_ROLES_VERSION ) {
		return;
	}

	reci_install_roles();
	reci_migrate_collaborators_to_roles();
	update_option( 'reci_roles_version', RECI_ROLES_VERSION );
}

/**
 * Promote accounts approved under the old meta flag.
 *
 * Before the ladder, an approved collaborator was a Subscriber carrying
 * _reci_collaborator_status=approved. Those accounts must become Collaborators
 * or they lose the ability to submit. Anyone already higher up keeps their
 * level, and the meta is left in place as a record of how they arrived.
 *
 * @return int Accounts promoted.
 */
function reci_migrate_collaborators_to_roles(): int {
	$users = get_users(
		[
			'meta_key'   => '_reci_collaborator_status',
			'meta_value' => 'approved',
			'fields'     => 'ID',
		]
	);

	$promoted = 0;

	foreach ( $users as $user_id ) {
		// user_can() covers levels 2-6, so this only touches accounts that would
		// otherwise be stranded.
		if ( user_can( (int) $user_id, 'edit_posts' ) ) {
			continue;
		}

		$user = get_user_by( 'id', (int) $user_id );
		if ( ! $user instanceof WP_User ) {
			continue;
		}

		$user->set_role( 'contributor' );
		++$promoted;
	}

	if ( $promoted > 0 ) {
		update_option( 'reci_roles_migrated_count', $promoted );
	}

	return $promoted;
}

/**
 * Write the ladder into the database.
 */
function reci_install_roles(): void {
	// ── Level 5: Site Manager ────────────────────────────────────────────
	// Everything an Editor can do, plus plugin and theme updates and user
	// management. Deliberately without manage_options and install_plugins:
	// those stay with the Administrator.
	$editor = get_role( 'editor' );
	$caps   = $editor ? $editor->capabilities : [];

	$caps = array_merge(
		$caps,
		[
			'update_plugins'             => true,
			'update_themes'              => true,
			'update_core'                => false,
			'list_users'                 => true,
			'edit_users'                 => true,
			'promote_users'              => true,
			'create_users'               => true,
			'remove_users'               => true,
			'delete_users'               => false,
			'reci_approve_collaborators' => true,
			'reci_confirm_registrations' => true,
			'reci_access_admin'          => true,
		]
	);

	remove_role( 'reci_site_manager' );
	add_role( 'reci_site_manager', __( 'Site Manager', 'reci-media-hub' ), $caps );

	// ── Levels 1–4 and 6: stock roles, adjusted ─────────────────────────
	$grants = [
		'administrator' => [ 'reci_approve_collaborators', 'reci_confirm_registrations', 'reci_access_admin' ],
		'editor'        => [ 'reci_approve_collaborators', 'reci_confirm_registrations', 'reci_access_admin' ],
	];

	foreach ( $grants as $role_name => $capabilities ) {
		$role = get_role( $role_name );
		if ( ! $role ) {
			continue;
		}
		foreach ( $capabilities as $cap ) {
			$role->add_cap( $cap );
		}
	}

	// Levels 1–3 never reach wp-admin; the dashboard is their whole surface.
	foreach ( [ 'author', 'contributor', 'subscriber' ] as $role_name ) {
		$role = get_role( $role_name );
		if ( ! $role ) {
			continue;
		}
		foreach ( reci_custom_capabilities() as $cap ) {
			$role->remove_cap( $cap );
		}
	}

	// Contributors submit files through our own handler, which runs the upload
	// itself — but the dashboard editor checks this cap before offering media.
	$contributor = get_role( 'contributor' );
	if ( $contributor ) {
		$contributor->add_cap( 'upload_files' );
	}
}

/**
 * Rename stock roles for display.
 *
 * Only the label changes; the slugs stay standard so plugins and core continue
 * to behave normally.
 */
// WP_Roles is constructed during wp-settings.php, long before a theme loads, so
// its own wp_roles_init hook has already fired by the time this file exists.
// Mutate the built object on init instead.
add_action( 'init', 'reci_rename_roles', 6 );
function reci_rename_roles(): void {
	$roles = wp_roles();

	foreach ( reci_role_display_names() as $slug => $label ) {
		if ( isset( $roles->role_names[ $slug ] ) ) {
			$roles->role_names[ $slug ] = $label;
		}
		if ( isset( $roles->roles[ $slug ] ) ) {
			$roles->roles[ $slug ]['name'] = $label;
		}
	}
}

/**
 * Stop anyone below Administrator handing out Administrator.
 *
 * Level 5 can change user roles, which in stock WordPress means it could promote
 * itself and become an Administrator — the ladder would have no top. Editing an
 * existing administrator is blocked for the same reason.
 */
add_filter( 'editable_roles', 'reci_restrict_editable_roles' );
function reci_restrict_editable_roles( array $roles ): array {
	if ( current_user_can( 'manage_options' ) ) {
		return $roles;
	}

	unset( $roles['administrator'] );

	return $roles;
}

add_filter( 'map_meta_cap', 'reci_protect_administrators', 10, 4 );
function reci_protect_administrators( array $caps, string $cap, int $user_id, array $args ): array {
	if ( ! in_array( $cap, [ 'edit_user', 'delete_user', 'promote_user', 'remove_user' ], true ) ) {
		return $caps;
	}

	$target_id = isset( $args[0] ) ? (int) $args[0] : 0;
	if ( $target_id <= 0 || $target_id === $user_id ) {
		return $caps;
	}

	if ( user_can( $target_id, 'manage_options' ) && ! user_can( $user_id, 'manage_options' ) ) {
		$caps[] = 'do_not_allow';
	}

	return $caps;
}
