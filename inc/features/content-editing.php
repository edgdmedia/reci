<?php
/**
 * Front-end editing of a collaborator's own submissions.
 *
 * Collaborators are Subscribers carrying `_reci_collaborator_status = approved`;
 * they hold no `edit_posts` capability and are redirected out of wp-admin. So
 * ownership is checked explicitly here rather than delegated to `current_user_can`,
 * and no capability is granted — wp-admin stays closed by construction.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types a collaborator may edit through the dashboard.
 *
 * Mirrors the my-content listing: if it is not listed there, it is not editable
 * here either.
 *
 * @return array<int,string>
 */
function reci_editable_submission_post_types(): array {
	return [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_course', 'reci_document' ];
}

/**
 * May this user edit this post from the dashboard?
 *
 * Four conditions, all required: the post exists, it is one of ours, the user
 * authored it, and it is not already in the trash.
 */
function reci_user_can_edit_submission( int $post_id, int $user_id = 0 ): bool {
	$user_id = $user_id > 0 ? $user_id : get_current_user_id();
	if ( $user_id <= 0 || $post_id <= 0 ) {
		return false;
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	if ( ! in_array( $post->post_type, reci_editable_submission_post_types(), true ) ) {
		return false;
	}

	if ( (int) $post->post_author !== $user_id ) {
		return false;
	}

	return in_array( $post->post_status, [ 'publish', 'pending', 'draft' ], true );
}

/**
 * May this user trash this post?
 *
 * Only work that was never published. A published piece may already be cited or
 * linked, so removing it is a staff decision.
 */
function reci_user_can_trash_submission( int $post_id, int $user_id = 0 ): bool {
	if ( ! reci_user_can_edit_submission( $post_id, $user_id ) ) {
		return false;
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	// Anything that has ever been published stays staff-only, even though editing
	// it demoted it back to pending. Without this the status rule is trivially
	// sidestepped: edit a live post to send it to pending, then trash it.
	if ( '' !== (string) get_post_meta( $post_id, '_reci_submission_was_published', true ) ) {
		return false;
	}

	return in_array( $post->post_status, [ 'pending', 'draft' ], true );
}

/**
 * URL of the dashboard editor for one post.
 */
function reci_submission_edit_url( int $post_id ): string {
	return home_url( '/dashboard/my-content/edit/' . $post_id . '/' );
}

// ── Create handler ───────────────────────────────────────────────────────────

add_action( 'admin_post_reci_create_content', 'reci_handle_content_create' );

/**
 * Start a draft and hand off to the editor.
 *
 * The draft exists before the editor opens, so the create and edit paths are the
 * same screen and the same save handler — there is no second form to drift.
 */
function reci_handle_content_create(): void {
	$chooser = home_url( '/dashboard/my-content/new/' );

	$nonce = isset( $_POST['reci_create_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['reci_create_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'reci_create_content' ) ) {
		wp_safe_redirect( add_query_arg( 'create_error', 'invalid_nonce', $chooser ) );
		exit;
	}

	// Level 2 submits for review through /submit/; this route belongs to anyone
	// who publishes their own work.
	if ( ! current_user_can( 'publish_posts' ) ) {
		wp_safe_redirect( home_url( '/submit/' ) );
		exit;
	}

	$content_type = isset( $_POST['content_type'] ) ? sanitize_key( wp_unslash( $_POST['content_type'] ) ) : '';
	$type_map     = function_exists( 'reci_media_hub_submission_type_map' ) ? reci_media_hub_submission_type_map() : [];

	if ( ! isset( $type_map[ $content_type ] ) ) {
		wp_safe_redirect( add_query_arg( 'create_error', 'invalid_type', $chooser ) );
		exit;
	}

	$post_id = wp_insert_post(
		[
			'post_type'   => $type_map[ $content_type ],
			'post_status' => 'draft',
			'post_title'  => __( 'Untitled', 'reci-media-hub' ),
			'post_author' => get_current_user_id(),
		],
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_safe_redirect( add_query_arg( 'create_error', 'save_failed', $chooser ) );
		exit;
	}

	// The editor reads this to decide which type-specific fields to show.
	update_post_meta( $post_id, '_reci_submission_content_type', $content_type );
	update_post_meta( $post_id, '_reci_submission_submitter_user_id', get_current_user_id() );

	wp_safe_redirect( reci_submission_edit_url( (int) $post_id ) );
	exit;
}

// ── Update handler ───────────────────────────────────────────────────────────

add_action( 'admin_post_reci_update_content', 'reci_handle_content_update' );

function reci_handle_content_update(): void {
	$post_id  = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$listing  = home_url( '/dashboard/my-content/' );
	$edit_url = reci_submission_edit_url( $post_id );

	$nonce = isset( $_POST['reci_edit_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['reci_edit_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'reci_edit_content_' . $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'invalid_nonce', $edit_url ) );
		exit;
	}

	if ( ! reci_user_can_edit_submission( $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'not_allowed', $listing ) );
		exit;
	}

	$post  = get_post( $post_id );
	$title = sanitize_text_field( wp_unslash( $_POST['submission_title'] ?? '' ) );

	if ( '' === $title ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'missing_title', $edit_url ) );
		exit;
	}

	$update = [
		'ID'           => $post_id,
		'post_title'   => $title,
		'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['submission_summary'] ?? '' ) ),
		'post_content' => wp_kses_post( wp_unslash( $_POST['submission_details'] ?? '' ) ),
	];

	// An edit to live content goes back into the review queue, so nothing
	// reaches the public site unreviewed. Remember where it came from: staff
	// need to know this was published before, not a first-time submission.
	// Level 3 and above may publish their own work, so the editor offers it. For
	// level 2 the button does not render and this check refuses the POST anyway.
	$wants_publish = ! empty( $_POST['submission_publish'] ) && current_user_can( 'publish_posts' );
	if ( $wants_publish && 'publish' !== $post->post_status ) {
		$update['post_status'] = 'publish';
	}

	$was_published = 'publish' === $post->post_status;
	if ( $was_published && ! $wants_publish && ! current_user_can( 'publish_posts' ) ) {
		$update['post_status'] = 'pending';

		// Set before the update, not after: wp_update_post() fires
		// transition_post_status synchronously, and the staff notification reads
		// this flag to tell a revision from a first-time submission. Written
		// afterwards it would always arrive too late.
		update_post_meta( $post_id, '_reci_submission_was_published', '1' );
		update_post_meta( $post_id, '_reci_submission_revised_at', current_time( 'mysql' ) );
	}

	$result = wp_update_post( $update, true );
	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'save_failed', $edit_url ) );
		exit;
	}

	$link = esc_url_raw( wp_unslash( $_POST['submission_content_link'] ?? '' ) );
	if ( '' !== $link ) {
		update_post_meta( $post_id, '_reci_submission_content_link', $link );
	} else {
		delete_post_meta( $post_id, '_reci_submission_content_link' );
	}

	// Type-specific fields, through the same writer the submission uses — it only
	// accepts keys declared for that content type, so a crafted POST cannot set
	// arbitrary meta here either.
	$content_type = (string) get_post_meta( $post_id, '_reci_submission_content_type', true );
	if ( '' !== $content_type && function_exists( 'reci_save_submission_type_fields' ) ) {
		reci_save_submission_type_fields( $post_id, $content_type );
	}

	// Taxonomies: only the ones the submission flow owns, and only when the form
	// actually posted the field — an absent key means "not on this form", which
	// is different from "cleared".
	foreach ( reci_media_hub_submission_taxonomies() as $taxonomy ) {
		$key = $taxonomy . '_terms';
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$term_ids = array_filter( array_map( 'absint', (array) wp_unslash( $_POST[ $key ] ) ) );
		wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
	}

	wp_safe_redirect( add_query_arg( 'edited', $was_published ? 'resubmitted' : '1', $listing ) );
	exit;
}

// ── Trash handler ────────────────────────────────────────────────────────────

add_action( 'admin_post_reci_trash_content', 'reci_handle_content_trash' );

function reci_handle_content_trash(): void {
	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$listing = home_url( '/dashboard/my-content/' );

	$nonce = isset( $_POST['reci_trash_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['reci_trash_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'reci_trash_content_' . $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'invalid_nonce', $listing ) );
		exit;
	}

	if ( ! reci_user_can_trash_submission( $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'edit_error', 'not_allowed', $listing ) );
		exit;
	}

	// Trash, never delete: an admin can restore it.
	wp_trash_post( $post_id );

	wp_safe_redirect( add_query_arg( 'edited', 'trashed', $listing ) );
	exit;
}
