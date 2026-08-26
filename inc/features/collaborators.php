<?php
/**
 * Collaborator status, applications, and follow helpers.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_get_collaborator_application_post_type' ) ) {
	function reci_get_collaborator_application_post_type(): string {
		return 'reci_collab_app';
	}
}

if ( ! function_exists( 'reci_register_collaborator_application_post_type' ) ) {
	function reci_register_collaborator_application_post_type(): void {
		register_post_type(
			reci_get_collaborator_application_post_type(),
			[
				'labels' => [
					'name'               => __( 'Collaborator Applications', 'reci-media-hub' ),
					'singular_name'      => __( 'Collaborator Application', 'reci-media-hub' ),
					'add_new_item'       => __( 'Add New Collaborator Application', 'reci-media-hub' ),
					'edit_item'          => __( 'Edit Collaborator Application', 'reci-media-hub' ),
					'new_item'           => __( 'New Collaborator Application', 'reci-media-hub' ),
					'view_item'          => __( 'View Collaborator Application', 'reci-media-hub' ),
					'search_items'       => __( 'Search Collaborator Applications', 'reci-media-hub' ),
					'not_found'          => __( 'No collaborator applications found', 'reci-media-hub' ),
					'not_found_in_trash' => __( 'No collaborator applications found in Trash', 'reci-media-hub' ),
					'all_items'          => __( 'Collaborator Applications', 'reci-media-hub' ),
					'menu_name'          => __( 'Collaborator Applications', 'reci-media-hub' ),
					'filter_items_list'  => __( 'Filter collaborator applications', 'reci-media-hub' ),
					'items_list'         => __( 'Collaborator applications list', 'reci-media-hub' ),
				],
				'public'             => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'has_archive'        => false,
				'rewrite'            => false,
				'menu_icon'          => 'dashicons-id-alt',
				'menu_position'      => 34,
				'supports'           => [ 'title', 'editor', 'revisions' ],
				'capability_type'    => 'post',
				'publicly_queryable' => false,
			]
		);
	}
}

add_action( 'init', 'reci_register_collaborator_application_post_type' );

if ( ! function_exists( 'reci_get_collaborator_page_url' ) ) {
	function reci_get_collaborator_page_url(): string {
		$page = get_page_by_path( 'become-a-collaborator' );
		return $page ? ( get_permalink( $page ) ?: '' ) : home_url( '/become-a-collaborator/' );
	}
}

if ( ! function_exists( 'reci_user_is_collaborator' ) ) {
	function reci_user_is_collaborator( int $user_id = 0 ): bool {
		$user_id = $user_id > 0 ? $user_id : get_current_user_id();
		if ( $user_id <= 0 ) {
			return false;
		}

		if ( user_can( $user_id, 'edit_others_posts' ) ) {
			return true;
		}

		return 'approved' === (string) get_user_meta( $user_id, '_reci_collaborator_status', true );
	}
}

if ( ! function_exists( 'reci_get_collaborator_status' ) ) {
	function reci_get_collaborator_status( int $user_id = 0 ): string {
		$user_id = $user_id > 0 ? $user_id : get_current_user_id();
		if ( $user_id <= 0 ) {
			return 'guest';
		}

		if ( user_can( $user_id, 'edit_others_posts' ) ) {
			return 'approved';
		}

		$status = (string) get_user_meta( $user_id, '_reci_collaborator_status', true );
		return in_array( $status, [ 'approved', 'pending', 'rejected' ], true ) ? $status : 'member';
	}
}

if ( ! function_exists( 'reci_get_user_followed_collaborator_ids' ) ) {
	function reci_get_user_followed_collaborator_ids( int $user_id ): array {
		$values = get_user_meta( $user_id, 'reci_followed_collaborators', true );
		if ( ! is_array( $values ) ) {
			return [];
		}

		return array_values( array_filter( array_map( 'absint', $values ) ) );
	}
}

if ( ! function_exists( 'reci_get_user_collaborator_application' ) ) {
	function reci_get_user_collaborator_application( int $user_id ): ?WP_Post {
		if ( $user_id <= 0 ) {
			return null;
		}

		$posts = get_posts(
			[
				'post_type'      => reci_get_collaborator_application_post_type(),
				'post_status'    => [ 'pending', 'draft', 'publish', 'private' ],
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => '_reci_collaborator_user_id',
				'meta_value'     => $user_id,
			]
		);

		return ! empty( $posts ) && $posts[0] instanceof WP_Post ? $posts[0] : null;
	}
}

if ( ! function_exists( 'reci_handle_collaborator_application' ) ) {
	function reci_handle_collaborator_application(): void {
		$target_url = reci_get_collaborator_page_url();

		if ( empty( $_POST['reci_collaborator_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reci_collaborator_nonce'] ) ), 'reci_collaborator_application' ) ) {
			wp_safe_redirect( add_query_arg( 'application_error', 'invalid_nonce', $target_url ) );
			exit;
		}

		$user_id = get_current_user_id();
		if ( ! is_user_logged_in() ) {
			if ( ! get_option( 'users_can_register' ) ) {
				wp_safe_redirect( add_query_arg( 'application_error', 'registration_disabled', $target_url ) );
				exit;
			}

			$first_name   = sanitize_text_field( wp_unslash( $_POST['reci_firstname'] ?? '' ) );
			$last_name    = sanitize_text_field( wp_unslash( $_POST['reci_lastname'] ?? '' ) );
			$email        = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
			$password     = (string) wp_unslash( $_POST['user_pass'] ?? '' );
			$pass_confirm = (string) wp_unslash( $_POST['reci_pass_confirm'] ?? '' );
			$full_name    = trim( $first_name . ' ' . $last_name );

			if ( '' === $full_name || ! is_email( $email ) || '' === $password ) {
				wp_safe_redirect( add_query_arg( 'application_error', 'missing_account_fields', $target_url ) );
				exit;
			}

			if ( $password !== $pass_confirm ) {
				wp_safe_redirect( add_query_arg( 'application_error', 'password_mismatch', $target_url ) );
				exit;
			}

			if ( strlen( $password ) < 8 ) {
				wp_safe_redirect( add_query_arg( 'application_error', 'password_too_short', $target_url ) );
				exit;
			}

			$base     = sanitize_user( strstr( $email, '@', true ), true );
			$username = $base;
			$suffix   = 1;
			while ( username_exists( $username ) ) {
				$username = $base . $suffix++;
			}

			$user_id = wp_create_user( $username, $password, $email );
			if ( is_wp_error( $user_id ) ) {
				wp_safe_redirect( add_query_arg( 'application_error', $user_id->get_error_code(), $target_url ) );
				exit;
			}

			wp_update_user([
				'ID'           => $user_id,
				'display_name' => $full_name,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
			]);

			$token = wp_generate_password( 24, false );
			update_user_meta( $user_id, '_reci_verify_token', $token );
			update_user_meta( $user_id, '_reci_is_verified', '0' );

			$verify_url = add_query_arg([
				'action' => 'reci_verify_email',
				'u'      => $user_id,
				't'      => $token,
			], admin_url( 'admin-post.php' ) );

			wp_mail(
				$email,
				__( 'Verify your email address', 'reci-media-hub' ),
				sprintf(
					"Hello %s,\n\nPlease verify your email address by clicking the link below:\n\n%s\n\nIf you did not request this, please ignore this email.",
					$full_name,
					$verify_url
				)
			);
		}

		if ( reci_user_is_collaborator( $user_id ) ) {
			wp_safe_redirect( add_query_arg( 'application_success', 'already_approved', $target_url ) );
			exit;
		}

		$existing = reci_get_user_collaborator_application( $user_id );
		if ( $existing instanceof WP_Post && 'pending' === $existing->post_status ) {
			wp_safe_redirect( add_query_arg( 'application_success', 'pending', $target_url ) );
			exit;
		}

		$first_name   = sanitize_text_field( wp_unslash( $_POST['reci_firstname'] ?? '' ) );
		$last_name    = sanitize_text_field( wp_unslash( $_POST['reci_lastname'] ?? '' ) );
		$email        = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
		$affiliated_with_pitt = sanitize_text_field( wp_unslash( $_POST['reci_affiliated_with_pitt'] ?? '' ) );
		$pitt_affiliation     = sanitize_text_field( wp_unslash( $_POST['reci_pitt_affiliation'] ?? '' ) );
		$department           = sanitize_text_field( wp_unslash( $_POST['reci_department'] ?? '' ) );
		$organization = sanitize_text_field( wp_unslash( $_POST['submission_organization'] ?? '' ) );
		$role         = sanitize_text_field( wp_unslash( $_POST['submission_role'] ?? '' ) );
		$bio          = sanitize_textarea_field( wp_unslash( $_POST['submission_bio'] ?? '' ) );
		$website      = esc_url_raw( wp_unslash( $_POST['submission_website'] ?? '' ) );
		$social_handles = sanitize_text_field( wp_unslash( $_POST['reci_social_handles'] ?? '' ) );
		$membership_objective = sanitize_textarea_field( wp_unslash( $_POST['reci_membership_objective'] ?? '' ) );
		$full_name    = trim( $first_name . ' ' . $last_name );

		if ( '' === $full_name || ! is_email( $email ) || '' === $organization || '' === $department || '' === $role || '' === $bio || '' === $affiliated_with_pitt || '' === $membership_objective ) {
			wp_safe_redirect( add_query_arg( 'application_error', 'missing_fields', $target_url ) );
			exit;
		}

		$post_id = wp_insert_post(
			[
				'post_type'    => reci_get_collaborator_application_post_type(),
				'post_status'  => 'pending',
				'post_title'   => $full_name,
				'post_content' => $bio,
				'post_author'  => $user_id,
			],
			true
		);

		if ( is_wp_error( $post_id ) || $post_id <= 0 ) {
			wp_safe_redirect( add_query_arg( 'application_error', 'save_failed', $target_url ) );
			exit;
		}

		update_post_meta( $post_id, '_reci_collaborator_user_id', $user_id );
		update_post_meta( $post_id, '_reci_submission_first_name', $first_name );
		update_post_meta( $post_id, '_reci_submission_last_name', $last_name );
		update_post_meta( $post_id, '_reci_submission_email', $email );
		update_post_meta( $post_id, '_reci_collaborator_affiliated_with_pitt', $affiliated_with_pitt );
		update_post_meta( $post_id, '_reci_collaborator_pitt_affiliation', $pitt_affiliation );
		update_post_meta( $post_id, '_reci_collaborator_department', $department );
		update_post_meta( $post_id, '_reci_submission_organization', $organization );
		update_post_meta( $post_id, '_reci_submission_role', $role );
		update_post_meta( $post_id, '_reci_submission_bio', $bio );
		update_post_meta( $post_id, '_reci_submission_website', $website );
		update_post_meta( $post_id, '_reci_collaborator_social_handles', $social_handles );
		update_post_meta( $post_id, '_reci_collaborator_membership_objective', $membership_objective );
		update_post_meta( $post_id, '_reci_collaborator_application_status', 'pending' );

		$profile_image_id = 0;
		$cv_attachment_id = 0;
		if ( isset( $_FILES['reci_profile_picture'] ) && is_array( $_FILES['reci_profile_picture'] ) && ! empty( $_FILES['reci_profile_picture']['name'] ) && ( (int) ( $_FILES['reci_profile_picture']['error'] ?? 0 ) ) === UPLOAD_ERR_OK ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			$attachment_id = media_handle_upload( 'reci_profile_picture', $post_id );
			if ( ! is_wp_error( $attachment_id ) && $attachment_id > 0 ) {
				$profile_image_id = (int) $attachment_id;
				set_post_thumbnail( $post_id, $profile_image_id );
			}
		}
		if ( isset( $_FILES['reci_cv_upload'] ) && is_array( $_FILES['reci_cv_upload'] ) && ! empty( $_FILES['reci_cv_upload']['name'] ) && ( (int) ( $_FILES['reci_cv_upload']['error'] ?? 0 ) ) === UPLOAD_ERR_OK ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			$attachment_id = media_handle_upload( 'reci_cv_upload', $post_id );
			if ( ! is_wp_error( $attachment_id ) && $attachment_id > 0 ) {
				$cv_attachment_id = (int) $attachment_id;
			}
		}
		if ( $profile_image_id > 0 ) {
			update_post_meta( $post_id, '_reci_collaborator_profile_image_id', $profile_image_id );
		}
		if ( $cv_attachment_id > 0 ) {
			update_post_meta( $post_id, '_reci_collaborator_cv_attachment_id', $cv_attachment_id );
		}
		update_user_meta( $user_id, '_reci_collaborator_status', 'pending' );

		if ( function_exists( 'reci_send_staff_submission_notification' ) ) {
			reci_send_staff_submission_notification( (int) $post_id );
		}

		$success_key = is_user_logged_in() ? 'pending' : 'pending_with_account';
		wp_safe_redirect( add_query_arg( 'application_success', $success_key, $target_url ) );
		exit;
	}
}

add_action( 'admin_post_reci_collaborator_application', 'reci_handle_collaborator_application' );

if ( ! function_exists( 'reci_sync_collaborator_application_status' ) ) {
	function reci_sync_collaborator_application_status( string $new_status, string $old_status, WP_Post $post ): void {
		if ( reci_get_collaborator_application_post_type() !== $post->post_type || $new_status === $old_status ) {
			return;
		}

		$user_id = absint( get_post_meta( $post->ID, '_reci_collaborator_user_id', true ) );
		if ( $user_id <= 0 ) {
			return;
		}

		if ( 'publish' === $new_status ) {
			update_post_meta( $post->ID, '_reci_collaborator_application_status', 'approved' );
			update_user_meta( $user_id, '_reci_collaborator_status', 'approved' );
			if ( function_exists( 'reci_sync_collaborator_profile_from_application' ) ) {
				reci_sync_collaborator_profile_from_application( (int) $post->ID, $user_id );
			}
			if ( function_exists( 'reci_media_hub_create_author_profile_from_submission' ) ) {
				reci_media_hub_create_author_profile_from_submission( (int) $post->ID );
			}
			if ( function_exists( 'reci_create_notification' ) ) {
				reci_create_notification( $user_id, 'collaborator_application_approved', __( 'Collaborator application approved', 'reci-media-hub' ), __( 'Your collaborator application has been approved. You can now submit content.', 'reci-media-hub' ), home_url( '/submit/' ), (int) $post->ID );
			}
			if ( '1' === get_user_meta( $user_id, 'reci_notify_collaborator_application_status', true ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user && ! empty( $user->user_email ) ) {
					wp_mail( $user->user_email, __( 'Your collaborator application was approved', 'reci-media-hub' ), __( 'Your collaborator application has been approved. You can now sign in and submit content on RECI.', 'reci-media-hub' ) );
				}
			}
			return;
		}

		if ( 'trash' !== $new_status ) {
			update_post_meta( $post->ID, '_reci_collaborator_application_status', 'rejected' );
			update_user_meta( $user_id, '_reci_collaborator_status', 'rejected' );
			if ( function_exists( 'reci_create_notification' ) ) {
				reci_create_notification( $user_id, 'collaborator_application_rejected', __( 'Collaborator application updated', 'reci-media-hub' ), __( 'Your collaborator application was not approved at this time.', 'reci-media-hub' ), reci_get_collaborator_page_url(), (int) $post->ID );
			}
			if ( '1' === get_user_meta( $user_id, 'reci_notify_collaborator_application_status', true ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user && ! empty( $user->user_email ) ) {
					wp_mail( $user->user_email, __( 'Your collaborator application was updated', 'reci-media-hub' ), __( 'Your collaborator application was not approved at this time. You can update your information and apply again later.', 'reci-media-hub' ) );
				}
			}
		}
	}
}

add_action( 'transition_post_status', 'reci_sync_collaborator_application_status', 10, 3 );

if ( ! function_exists( 'reci_sync_collaborator_profile_from_application' ) ) {
	function reci_sync_collaborator_profile_from_application( int $application_id, int $user_id ): void {
		$profile_id = function_exists( 'reci_media_hub_get_author_profile_by_user_id' ) ? reci_media_hub_get_author_profile_by_user_id( $user_id ) : 0;
		$full_name  = trim(
			(string) get_post_meta( $application_id, '_reci_submission_first_name', true ) . ' ' .
			(string) get_post_meta( $application_id, '_reci_submission_last_name', true )
		);
		$bio        = (string) get_post_meta( $application_id, '_reci_submission_bio', true );
		$title      = (string) get_post_meta( $application_id, '_reci_submission_role', true );
		$website    = (string) get_post_meta( $application_id, '_reci_submission_website', true );
		$organization = (string) get_post_meta( $application_id, '_reci_submission_organization', true );
		$department   = (string) get_post_meta( $application_id, '_reci_collaborator_department', true );
		$social       = (string) get_post_meta( $application_id, '_reci_collaborator_social_handles', true );
		$objective    = (string) get_post_meta( $application_id, '_reci_collaborator_membership_objective', true );

		if ( $profile_id <= 0 && function_exists( 'reci_media_hub_create_or_get_author_profile' ) ) {
			$profile_id = reci_media_hub_create_or_get_author_profile( $full_name, $title, $bio );
		}

		if ( $profile_id <= 0 ) {
			return;
		}

		wp_update_post([
			'ID'           => $profile_id,
			'post_title'   => $full_name !== '' ? $full_name : get_the_title( $profile_id ),
			'post_content' => trim( implode( "\n\n", array_filter( [ $bio, $organization !== '' ? 'Organization: ' . $organization : '', $department !== '' ? 'Department: ' . $department : '', $social !== '' ? 'Social: ' . $social : '', $objective !== '' ? 'Main objective: ' . $objective : '' ] ) ) ),
			'post_excerpt' => $bio,
			'post_status'  => 'publish',
		]);

		update_post_meta( $profile_id, '_reci_author_profile_user_id', $user_id );
		update_post_meta( $profile_id, '_reci_author_profile_title', $title );
		if ( $website !== '' ) {
			update_post_meta( $profile_id, '_reci_submission_website', $website );
		}

		$profile_image_id = absint( get_post_meta( $application_id, '_reci_collaborator_profile_image_id', true ) );
		if ( $profile_image_id > 0 ) {
			set_post_thumbnail( $profile_id, $profile_image_id );
		}
	}
}

if ( ! function_exists( 'reci_render_submit_gate' ) ) {
	function reci_render_submit_gate( string $context = 'public' ): void {
		$status           = reci_get_collaborator_status();
		$sign_in_url      = reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url( home_url( '/submit/' ) );
		$sign_up_url      = function_exists( 'reci_get_sign_up_url' ) ? reci_get_sign_up_url() : ( reci_get_auth_page_url( 'sign-up' ) ?: wp_registration_url() );
		$collaborator_url = reci_get_collaborator_page_url();
		$dashboard_url    = home_url( '/dashboard/' );
		$title            = __( 'Submit to RECI', 'reci-media-hub' );
		$message          = __( 'Approved Collaborators can submit content to the RECI Media Hub. Members can still follow interests, save content, and apply to contribute.', 'reci-media-hub' );
		$primary_label    = __( 'Become a Collaborator', 'reci-media-hub' );
		$primary_url      = $collaborator_url;
		$secondary_links  = '';

		if ( 'guest' === $status ) {
			$secondary_links = sprintf(
				'<a href="%1$s" class="btn btn-outline-primary btn-md">%2$s</a><a href="%3$s" class="btn btn-outline-primary btn-md">%4$s</a>',
				esc_url( $sign_in_url ),
				esc_html__( 'Log In', 'reci-media-hub' ),
				esc_url( $sign_up_url ),
				esc_html__( 'Sign Up', 'reci-media-hub' )
			);
		} elseif ( 'pending' === $status ) {
			$title         = __( 'Your Collaborator Application Is Under Review', 'reci-media-hub' );
			$message       = __( 'Your account is active as a Member. Once your Collaborator application is approved, you will be able to submit content here.', 'reci-media-hub' );
			$primary_label = __( 'Go to Dashboard', 'reci-media-hub' );
			$primary_url   = $dashboard_url;
		} elseif ( in_array( $status, [ 'member', 'rejected' ], true ) ) {
			$message = __( 'You are signed in as a Member. Apply to become a Collaborator to unlock content submission.', 'reci-media-hub' );
		}

		$max_width = 'dashboard' === $context ? 'max-w-3xl' : 'max-w-4xl';
		echo '<div class="' . esc_attr( $max_width ) . ' rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">';
		echo '<span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800">' . esc_html__( 'Contributor Access', 'reci-media-hub' ) . '</span>';
		echo '<h2 class="mt-4 text-2xl font-bold text-zinc-900">' . esc_html( $title ) . '</h2>';
		echo '<p class="mt-3 text-base leading-7 text-zinc-600">' . esc_html( $message ) . '</p>';
		echo '<div class="mt-6 flex flex-wrap gap-3">';
		echo '<a href="' . esc_url( $primary_url ) . '" class="btn btn-primary btn-md">' . esc_html( $primary_label ) . '</a>';
		echo $secondary_links;
		echo '</div></div>';
	}
}

if ( ! function_exists( 'reci_toggle_follow_collaborator' ) ) {
	function reci_toggle_follow_collaborator(): void {
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url() );
			exit;
		}

		$collaborator_id = absint( $_POST['collaborator_id'] ?? 0 );
		$redirect_to     = esc_url_raw( wp_unslash( $_POST['redirect_to'] ?? get_permalink( $collaborator_id ) ?: home_url( '/' ) ) );
		$nonce           = sanitize_text_field( wp_unslash( $_POST['reci_follow_collaborator_nonce'] ?? '' ) );

		if ( $collaborator_id <= 0 || ! wp_verify_nonce( $nonce, 'reci_toggle_follow_collaborator_' . $collaborator_id ) ) {
			wp_safe_redirect( $redirect_to );
			exit;
		}

		$user_id  = get_current_user_id();
		$current  = reci_get_user_followed_collaborator_ids( $user_id );
		$position = array_search( $collaborator_id, $current, true );

		if ( false === $position ) {
			$current[] = $collaborator_id;
		} else {
			unset( $current[ $position ] );
		}

		update_user_meta( $user_id, 'reci_followed_collaborators', array_values( array_unique( array_map( 'absint', $current ) ) ) );
		wp_safe_redirect( $redirect_to );
		exit;
	}
}

add_action( 'admin_post_reci_toggle_follow_collaborator', 'reci_toggle_follow_collaborator' );

if ( ! function_exists( 'reci_render_collaborator_application_metabox' ) ) {
	function reci_render_collaborator_application_metabox( WP_Post $post ): void {
		$user_id      = absint( get_post_meta( $post->ID, '_reci_collaborator_user_id', true ) );
		$user         = $user_id > 0 ? get_user_by( 'id', $user_id ) : null;
		$status       = (string) get_post_meta( $post->ID, '_reci_collaborator_application_status', true );
		$first_name   = (string) get_post_meta( $post->ID, '_reci_submission_first_name', true );
		$last_name    = (string) get_post_meta( $post->ID, '_reci_submission_last_name', true );
		$email        = (string) get_post_meta( $post->ID, '_reci_submission_email', true );
		$organization = (string) get_post_meta( $post->ID, '_reci_submission_organization', true );
		$role         = (string) get_post_meta( $post->ID, '_reci_submission_role', true );
		$website      = (string) get_post_meta( $post->ID, '_reci_submission_website', true );
		$affiliated_with_pitt = (string) get_post_meta( $post->ID, '_reci_collaborator_affiliated_with_pitt', true );
		$pitt_affiliation     = (string) get_post_meta( $post->ID, '_reci_collaborator_pitt_affiliation', true );
		$department           = (string) get_post_meta( $post->ID, '_reci_collaborator_department', true );
		$social_handles       = (string) get_post_meta( $post->ID, '_reci_collaborator_social_handles', true );
		$membership_objective = (string) get_post_meta( $post->ID, '_reci_collaborator_membership_objective', true );
		$profile_image_id     = absint( get_post_meta( $post->ID, '_reci_collaborator_profile_image_id', true ) );
		$cv_attachment_id     = absint( get_post_meta( $post->ID, '_reci_collaborator_cv_attachment_id', true ) );

		echo '<div class="reci-meta-grid">';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Application Status', 'reci-media-hub' ) . '</strong><span>' . esc_html( ucfirst( $status ?: 'pending' ) ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Linked Member Account', 'reci-media-hub' ) . '</strong><span>' . esc_html( $user instanceof WP_User ? $user->display_name . ' (#' . $user->ID . ')' : __( 'Not linked', 'reci-media-hub' ) ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Full Name', 'reci-media-hub' ) . '</strong><span>' . esc_html( trim( $first_name . ' ' . $last_name ) ?: get_the_title( $post ) ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Email', 'reci-media-hub' ) . '</strong><span>' . esc_html( $email ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Organization', 'reci-media-hub' ) . '</strong><span>' . esc_html( $organization ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Affiliated with Pitt', 'reci-media-hub' ) . '</strong><span>' . esc_html( $affiliated_with_pitt ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Pitt Affiliation', 'reci-media-hub' ) . '</strong><span>' . esc_html( $pitt_affiliation ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Department', 'reci-media-hub' ) . '</strong><span>' . esc_html( $department ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Role / Title', 'reci-media-hub' ) . '</strong><span>' . esc_html( $role ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row reci-meta-row--full"><strong>' . esc_html__( 'Website', 'reci-media-hub' ) . '</strong><span>';
		if ( '' !== $website ) {
			echo '<a href="' . esc_url( $website ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $website ) . '</a>';
		} else {
			echo '—';
		}
		echo '</span></div>';
		echo '<div class="reci-meta-row reci-meta-row--full"><strong>' . esc_html__( 'Social Handles', 'reci-media-hub' ) . '</strong><span>' . esc_html( $social_handles ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row reci-meta-row--full"><strong>' . esc_html__( 'Main Objective for Membership', 'reci-media-hub' ) . '</strong><span>' . esc_html( $membership_objective ?: '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'Profile Picture', 'reci-media-hub' ) . '</strong><span>' . esc_html( $profile_image_id > 0 ? __( 'Uploaded', 'reci-media-hub' ) : '—' ) . '</span></div>';
		echo '<div class="reci-meta-row"><strong>' . esc_html__( 'CV Upload', 'reci-media-hub' ) . '</strong><span>' . esc_html( $cv_attachment_id > 0 ? __( 'Uploaded', 'reci-media-hub' ) : '—' ) . '</span></div>';
		echo '<div class="reci-meta-row reci-meta-row--full"><strong>' . esc_html__( 'Review Actions', 'reci-media-hub' ) . '</strong>';
		echo '<p class="description">' . esc_html__( 'Use the buttons below for a clear approve or reject workflow.', 'reci-media-hub' ) . '</p>';
		echo '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		echo '<input type="hidden" name="action" value="reci_collaborator_decision" />';
		echo '<input type="hidden" name="application_id" value="' . esc_attr( (string) $post->ID ) . '" />';
		echo '<input type="hidden" name="decision" value="approve" />';
		wp_nonce_field( 'reci_collaborator_decision_' . $post->ID, 'reci_collaborator_decision_nonce' );
		submit_button( __( 'Approve Collaborator', 'reci-media-hub' ), 'primary', 'submit', false );
		echo '</form>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		echo '<input type="hidden" name="action" value="reci_collaborator_decision" />';
		echo '<input type="hidden" name="application_id" value="' . esc_attr( (string) $post->ID ) . '" />';
		echo '<input type="hidden" name="decision" value="reject" />';
		wp_nonce_field( 'reci_collaborator_decision_' . $post->ID, 'reci_collaborator_decision_nonce' );
		submit_button( __( 'Reject Application', 'reci-media-hub' ), 'secondary', 'submit', false );
		echo '</form>';
		echo '</div></div>';
		echo '</div>';
	}
}

if ( ! function_exists( 'reci_add_collaborator_application_metaboxes' ) ) {
	function reci_add_collaborator_application_metaboxes(): void {
		add_meta_box(
			'reci-collaborator-application-details',
			__( 'Collaborator Application Review', 'reci-media-hub' ),
			'reci_render_collaborator_application_metabox',
			reci_get_collaborator_application_post_type(),
			'normal',
			'high'
		);
	}
}

add_action( 'add_meta_boxes', 'reci_add_collaborator_application_metaboxes' );

if ( ! function_exists( 'reci_collaborator_application_columns' ) ) {
	function reci_collaborator_application_columns( array $columns ): array {
		return [
			'cb'           => $columns['cb'] ?? '<input type="checkbox" />',
			'title'        => __( 'Applicant', 'reci-media-hub' ),
			'organization' => __( 'Organization', 'reci-media-hub' ),
			'email'        => __( 'Email', 'reci-media-hub' ),
			'status'       => __( 'Status', 'reci-media-hub' ),
			'date'         => __( 'Submitted', 'reci-media-hub' ),
		];
	}
}

add_filter( 'manage_' . reci_get_collaborator_application_post_type() . '_posts_columns', 'reci_collaborator_application_columns' );

if ( ! function_exists( 'reci_render_collaborator_application_column' ) ) {
	function reci_render_collaborator_application_column( string $column, int $post_id ): void {
		if ( 'organization' === $column ) {
			echo esc_html( (string) get_post_meta( $post_id, '_reci_submission_organization', true ) ?: '—' );
			return;
		}

		if ( 'email' === $column ) {
			echo esc_html( (string) get_post_meta( $post_id, '_reci_submission_email', true ) ?: '—' );
			return;
		}

		if ( 'status' === $column ) {
			$status = (string) get_post_meta( $post_id, '_reci_collaborator_application_status', true );
			echo esc_html( ucfirst( $status ?: 'pending' ) );
		}
	}
}

add_action( 'manage_' . reci_get_collaborator_application_post_type() . '_posts_custom_column', 'reci_render_collaborator_application_column', 10, 2 );

if ( ! function_exists( 'reci_collaborator_application_updated_messages' ) ) {
	function reci_collaborator_application_updated_messages( array $messages ): array {
		$post_type = reci_get_collaborator_application_post_type();
		$messages[ $post_type ] = [
			0  => '',
			1  => __( 'Collaborator application updated.', 'reci-media-hub' ),
			6  => __( 'Collaborator application approved.', 'reci-media-hub' ),
			7  => __( 'Collaborator application saved.', 'reci-media-hub' ),
			10 => __( 'Collaborator application draft updated.', 'reci-media-hub' ),
		];
		return $messages;
	}
}

add_filter( 'post_updated_messages', 'reci_collaborator_application_updated_messages' );
