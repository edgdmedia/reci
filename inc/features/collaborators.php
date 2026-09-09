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
					// Short labels: these read inside the Collaborators menu, where the
					// surrounding context already says what they are applications for.
					'all_items'          => __( 'Applications', 'reci-media-hub' ),
					'menu_name'          => __( 'Applications', 'reci-media-hub' ),
					'filter_items_list'  => __( 'Filter collaborator applications', 'reci-media-hub' ),
					'items_list'         => __( 'Collaborator applications list', 'reci-media-hub' ),
				],
				'public'             => false,
				'show_ui'            => true,
				// Under Submissions, not Collaborators: an application is a thing
				// waiting to be reviewed, which is what that menu is for. The
				// Collaborators menu is the directory of people already approved.
				'show_in_menu'       => 'reci-submissions',
				'show_in_rest'       => true,
				'has_archive'        => false,
				'rewrite'            => false,
				'menu_icon'          => 'dashicons-id-alt',
				'menu_position'      => 34,
				// No editor: an application is a set of submitted fields, not a
				// document. The review metabox shows everything; a body field only
				// invited staff to edit the applicant's own words.
				'supports'           => [ 'title' ],
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

		// The role is the answer now. A collaborator is a Contributor (level 2)
		// or anything above it, all of which hold edit_posts; a Member does not.
		// The old _reci_collaborator_status meta stays readable so an account
		// that predates the migration is not locked out, but nothing writes it.
		if ( user_can( $user_id, 'edit_posts' ) ) {
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

		// Level 2 and above are collaborators by role. This has to agree with
		// reci_user_is_collaborator(); when it read edit_others_posts while that
		// read edit_posts, a Contributor was a collaborator everywhere except the
		// one gate that decides whether the submit form renders at all.
		if ( user_can( $user_id, 'edit_posts' ) ) {
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

if ( ! function_exists( 'reci_collaborator_profile_field_definitions' ) ) {
	/**
	 * Canonical contributor identity fields.
	 *
	 * Shared by collaborator onboarding, the canonical /submit/ flow, and the
	 * dashboard profile editor so all three surfaces stay in sync.
	 */
	function reci_collaborator_profile_field_definitions(): array {
		return [
			'reci_firstname'            => [ 'label' => __( 'First Name', 'reci-media-hub' ), 'type' => 'text', 'required' => true, 'width' => 'half', 'audience' => 'all' ],
			'reci_lastname'             => [ 'label' => __( 'Last Name', 'reci-media-hub' ), 'type' => 'text', 'required' => true, 'width' => 'half', 'audience' => 'all' ],
			'user_email'                => [ 'label' => __( 'Email', 'reci-media-hub' ), 'type' => 'email', 'required' => true, 'width' => 'half', 'audience' => 'all' ],
			'submission_bio'            => [ 'label' => __( 'Personal Bio (150 words or less)', 'reci-media-hub' ), 'type' => 'textarea', 'required' => true, 'width' => 'full', 'rows' => 6, 'audience' => 'all' ],
			'reci_affiliated_with_pitt' => [ 'label' => __( 'Affiliated with Pitt', 'reci-media-hub' ), 'type' => 'select', 'required' => true, 'width' => 'half', 'options' => [ 'Yes', 'No' ], 'audience' => 'collaborator' ],
			'reci_pitt_affiliation'     => [ 'label' => __( 'Pitt Affiliation', 'reci-media-hub' ), 'type' => 'text', 'required' => false, 'width' => 'half', 'audience' => 'collaborator' ],
			'submission_organization'   => [ 'label' => __( 'Affiliation / Organization', 'reci-media-hub' ), 'type' => 'text', 'required' => true, 'width' => 'half', 'audience' => 'collaborator' ],
			'reci_department'           => [ 'label' => __( 'Department (School / Organization)', 'reci-media-hub' ), 'type' => 'text', 'required' => true, 'width' => 'half', 'audience' => 'collaborator' ],
			'submission_role'           => [ 'label' => __( 'Role / Title', 'reci-media-hub' ), 'type' => 'text', 'required' => true, 'width' => 'half', 'audience' => 'collaborator' ],
			'submission_website'        => [ 'label' => __( 'Professional Website', 'reci-media-hub' ), 'type' => 'url', 'required' => false, 'width' => 'half', 'audience' => 'collaborator' ],
			'reci_social_handles'       => [ 'label' => __( 'Social Media Handles', 'reci-media-hub' ), 'type' => 'text', 'required' => false, 'width' => 'half', 'placeholder' => 'LinkedIn, X, Instagram, etc.', 'audience' => 'collaborator' ],
			// Multi-value fields; these become taxonomy terms on the public profile.
			'reci_affiliation_term'     => [ 'label' => __( 'Your Affiliation', 'reci-media-hub' ), 'type' => 'taxonomy_select', 'taxonomy' => 'reci_affiliation', 'required' => true, 'width' => 'half', 'audience' => 'collaborator' ],
			'reci_expertise_terms'      => [ 'label' => __( 'Subject Areas You Work In', 'reci-media-hub' ), 'type' => 'taxonomy_checkboxes', 'taxonomy' => 'reci_expertise', 'required' => false, 'width' => 'full', 'allow_other' => true, 'optional_hint' => false, 'choices' => function_exists( 'reci_media_hub_default_expertise_terms' ) ? reci_media_hub_default_expertise_terms() : [], 'audience' => 'collaborator' ],
		];
	}
}

if ( ! function_exists( 'reci_profile_fields_for_audience' ) ) {
	/**
	 * Filter the canonical fields down to one audience.
	 *
	 * Every account holds the 'all' fields. The 'collaborator' fields describe a
	 * professional identity a subscriber has no use for, so the dashboard profile
	 * only shows them to collaborators. The application form and the submit flow
	 * still ask for everything — that is where the full set is collected.
	 *
	 * @param string $audience 'all' for the shared subset, 'collaborator' for the full set.
	 * @return array<string,array<string,mixed>>
	 */
	function reci_profile_fields_for_audience( string $audience = 'all' ): array {
		$fields = reci_collaborator_profile_field_definitions();

		if ( 'collaborator' === $audience ) {
			return $fields;
		}

		return array_filter(
			$fields,
			static fn( array $field ): bool => 'all' === ( $field['audience'] ?? 'all' )
		);
	}
}

if ( ! function_exists( 'reci_collaborator_application_only_field_definitions' ) ) {
	/**
	 * Fields that belong to the collaborator application only.
	 *
	 * These never appear in the dashboard profile editor.
	 */
	function reci_collaborator_application_only_field_definitions(): array {
		return [
			'reci_profile_picture'      => [ 'label' => __( 'Profile Picture (Professional headshot)', 'reci-media-hub' ), 'type' => 'file', 'required' => true, 'width' => 'full', 'accept' => 'image/*' ],
			'reci_cv_upload'            => [ 'label' => __( 'Attach CV', 'reci-media-hub' ), 'type' => 'file', 'required' => false, 'width' => 'full', 'accept' => '.pdf,.doc,.docx' ],
			'reci_membership_objective' => [ 'label' => __( 'Main Objective for Membership', 'reci-media-hub' ), 'type' => 'textarea', 'required' => true, 'width' => 'full', 'rows' => 4 ],
		];
	}
}

if ( ! function_exists( 'reci_collaborator_account_field_definitions' ) ) {
	/**
	 * Account-creation fields, rendered only when the visitor is a guest.
	 */
	function reci_collaborator_account_field_definitions(): array {
		return [
			'user_pass'        => [ 'label' => __( 'Password', 'reci-media-hub' ), 'type' => 'password', 'required' => true, 'width' => 'half' ],
			'reci_pass_confirm' => [ 'label' => __( 'Confirm Password', 'reci-media-hub' ), 'type' => 'password', 'required' => true, 'width' => 'half' ],
		];
	}
}

if ( ! function_exists( 'reci_get_user_collaborator_profile_data' ) ) {
	/**
	 * Read the shared profile fields for a user, keyed by canonical field name.
	 */
	function reci_get_user_collaborator_profile_data( int $user_id ): array {
		$user = $user_id > 0 ? get_user_by( 'id', $user_id ) : null;
		if ( ! $user instanceof WP_User ) {
			return [];
		}

		return [
			'reci_firstname'            => (string) get_user_meta( $user_id, 'first_name', true ),
			'reci_lastname'             => (string) get_user_meta( $user_id, 'last_name', true ),
			'user_email'                => (string) $user->user_email,
			'reci_affiliated_with_pitt' => (string) get_user_meta( $user_id, 'reci_affiliated_with_pitt', true ),
			'reci_pitt_affiliation'     => (string) get_user_meta( $user_id, 'reci_pitt_affiliation', true ),
			'submission_organization'   => (string) get_user_meta( $user_id, 'organization', true ),
			'reci_department'           => (string) get_user_meta( $user_id, 'reci_department', true ),
			'submission_role'           => (string) get_user_meta( $user_id, 'user_title', true ),
			'submission_bio'            => (string) get_user_meta( $user_id, 'description', true ),
			'submission_website'        => (string) $user->user_url,
			'reci_social_handles'       => (string) get_user_meta( $user_id, 'reci_social_handles', true ),
			'reci_affiliation_term'     => (string) get_user_meta( $user_id, 'reci_affiliation_term', true ),
			'reci_expertise_terms'      => (array) ( get_user_meta( $user_id, 'reci_expertise_terms', true ) ?: [] ),
		];
	}
}

if ( ! function_exists( 'reci_save_user_collaborator_profile_data' ) ) {
	/**
	 * Persist the shared profile fields for a user.
	 *
	 * Only keys present in $data are written, so partial saves are safe.
	 */
	function reci_save_user_collaborator_profile_data( int $user_id, array $data ): void {
		if ( $user_id <= 0 ) {
			return;
		}

		$user_args = [ 'ID' => $user_id ];

		if ( array_key_exists( 'reci_firstname', $data ) ) {
			$user_args['first_name'] = (string) $data['reci_firstname'];
		}
		if ( array_key_exists( 'reci_lastname', $data ) ) {
			$user_args['last_name'] = (string) $data['reci_lastname'];
		}
		if ( array_key_exists( 'submission_website', $data ) ) {
			$user_args['user_url'] = (string) $data['submission_website'];
		}

		$full_name = trim( (string) ( $data['reci_firstname'] ?? '' ) . ' ' . (string) ( $data['reci_lastname'] ?? '' ) );
		if ( '' !== $full_name && ! isset( $data['display_name'] ) ) {
			$user_args['display_name'] = $full_name;
		} elseif ( isset( $data['display_name'] ) && '' !== trim( (string) $data['display_name'] ) ) {
			$user_args['display_name'] = (string) $data['display_name'];
		}

		if ( count( $user_args ) > 1 ) {
			wp_update_user( $user_args );
		}

		$meta_map = [
			'reci_affiliated_with_pitt' => 'reci_affiliated_with_pitt',
			'reci_pitt_affiliation'     => 'reci_pitt_affiliation',
			'submission_organization'   => 'organization',
			'reci_department'           => 'reci_department',
			'submission_role'           => 'user_title',
			'submission_bio'            => 'description',
			'reci_social_handles'       => 'reci_social_handles',
		];

		foreach ( $meta_map as $field => $meta_key ) {
			if ( array_key_exists( $field, $data ) ) {
				update_user_meta( $user_id, $meta_key, (string) $data[ $field ] );
			}
		}

		// Multi-value fields keep their shape; the sync turns them into terms.
		if ( array_key_exists( 'reci_affiliation_term', $data ) ) {
			update_user_meta( $user_id, 'reci_affiliation_term', (string) $data['reci_affiliation_term'] );
		}
		if ( array_key_exists( 'reci_expertise_terms', $data ) ) {
			$terms = array_values( array_unique( array_filter( array_map(
				static fn( $term ) => trim( wp_strip_all_tags( (string) $term ) ),
				(array) $data['reci_expertise_terms']
			) ) ) );
			update_user_meta( $user_id, 'reci_expertise_terms', $terms );
		}
	}
}

if ( ! function_exists( 'reci_render_collaborator_field' ) ) {
	/**
	 * Render one shared field from a field definition.
	 */
	function reci_render_collaborator_field( string $key, array $field, $value = '' ): void {
		$type     = (string) ( $field['type'] ?? 'text' );
		$required = ! empty( $field['required'] );
		$id       = 'reci-field-' . str_replace( '_', '-', $key );
		$classes  = 'w-full rounded-lg border border-zinc-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
		$span     = 'full' === ( $field['width'] ?? 'full' ) ? ' sm:col-span-2' : '';
		$selected = is_array( $value ) ? array_map( 'strval', $value ) : [];
		if ( ! is_array( $value ) ) {
			$value = (string) $value;
		}

		echo '<div class="' . esc_attr( trim( $span ) ) . '">';
		echo '<label class="mb-2 block text-sm font-medium text-zinc-800" for="' . esc_attr( $id ) . '">' . esc_html( (string) ( $field['label'] ?? $key ) );
		if ( ! $required && ( $field['optional_hint'] ?? true ) ) {
			echo ' <span class="font-normal text-zinc-400">' . esc_html__( '(optional)', 'reci-media-hub' ) . '</span>';
		}
		echo '</label>';

		if ( 'textarea' === $type ) {
			printf(
				'<textarea id="%1$s" name="%2$s" rows="%3$d" class="%4$s"%5$s>%6$s</textarea>',
				esc_attr( $id ),
				esc_attr( $key ),
				(int) ( $field['rows'] ?? 4 ),
				esc_attr( $classes ),
				$required ? ' required' : '',
				esc_textarea( $value )
			);
		} elseif ( 'select' === $type ) {
			printf(
				'<select id="%1$s" name="%2$s" class="%3$s"%4$s>',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( $classes ),
				$required ? ' required' : ''
			);
			echo '<option value="">' . esc_html__( 'Select one', 'reci-media-hub' ) . '</option>';
			foreach ( (array) ( $field['options'] ?? [] ) as $option_key => $option_label ) {
				$option_value = is_int( $option_key ) ? (string) $option_label : (string) $option_key;
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option_value ),
					selected( $value, $option_value, false ),
					esc_html( (string) $option_label )
				);
			}
			echo '</select>';
		} elseif ( 'taxonomy_checkboxes' === $type ) {
			// Offer the governed vocabulary, not every term in the taxonomy — the
			// imported directory carries a long tail of one-off subjects, and listing
			// them all invites more. Anything the person already holds stays checked
			// so editing a profile never silently drops their existing terms.
			$choices = (array) ( $field['choices'] ?? [] );
			if ( empty( $choices ) ) {
				$terms   = get_terms( [ 'taxonomy' => (string) ( $field['taxonomy'] ?? '' ), 'hide_empty' => false, 'orderby' => 'name' ] );
				$choices = is_wp_error( $terms ) ? [] : wp_list_pluck( $terms, 'name' );
			}
			$choices = array_values( array_unique( array_merge( $choices, $selected ) ) );
			sort( $choices );

			echo '<div class="grid gap-2 sm:grid-cols-2">';
			foreach ( $choices as $choice ) {
				printf(
					'<label class="flex items-start gap-2 text-sm text-zinc-700"><input type="checkbox" name="%1$s[]" value="%2$s"%3$s class="mt-1 rounded border-zinc-300 text-amber-600 focus:ring-amber-500" /><span>%4$s</span></label>',
					esc_attr( $key ),
					esc_attr( $choice ),
					checked( in_array( (string) $choice, $selected, true ), true, false ),
					esc_html( (string) $choice )
				);
			}
			echo '</div>';

			if ( ! empty( $field['allow_other'] ) ) {
				$other_key = $key . '_other';
				printf(
					'<label class="mt-3 mb-2 block text-sm font-medium text-zinc-800" for="%1$s">%2$s</label><input id="%1$s" name="%3$s" type="text" value="" placeholder="%4$s" class="%5$s" />',
					esc_attr( 'reci-field-' . str_replace( '_', '-', $other_key ) ),
					esc_html__( 'Something else? Add your own, separated by commas', 'reci-media-hub' ),
					esc_attr( $other_key ),
					esc_attr__( 'e.g. Housing Policy, Restorative Justice', 'reci-media-hub' ),
					esc_attr( $classes )
				);
			}
		} elseif ( 'taxonomy_select' === $type ) {
			$terms = get_terms( [ 'taxonomy' => (string) ( $field['taxonomy'] ?? '' ), 'hide_empty' => false, 'orderby' => 'name' ] );
			printf(
				'<select id="%1$s" name="%2$s" class="%3$s"%4$s>',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( $classes ),
				$required ? ' required' : ''
			);
			echo '<option value="">' . esc_html__( 'Select one', 'reci-media-hub' ) . '</option>';
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					printf(
						'<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $term->name ),
						selected( $value, $term->name, false ),
						esc_html( $term->name )
					);
				}
			}
			echo '</select>';
		} elseif ( 'file' === $type ) {
			printf(
				'<input id="%1$s" name="%2$s" type="file" accept="%3$s" class="%4$s"%5$s />',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( (string) ( $field['accept'] ?? '' ) ),
				esc_attr( $classes ),
				$required ? ' required' : ''
			);
		} else {
			printf(
				'<input id="%1$s" name="%2$s" type="%3$s" value="%4$s" placeholder="%5$s" class="%6$s"%7$s />',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( $type ),
				esc_attr( 'password' === $type ? '' : $value ),
				esc_attr( (string) ( $field['placeholder'] ?? '' ) ),
				esc_attr( $classes ),
				$required ? ' required' : ''
			);
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'reci_render_collaborator_fields' ) ) {
	/**
	 * Render a group of shared fields inside a two-column responsive grid.
	 */
	function reci_render_collaborator_fields( array $fields, array $values = [] ): void {
		echo '<div class="grid gap-5 sm:grid-cols-2">';
		foreach ( $fields as $key => $field ) {
			reci_render_collaborator_field( (string) $key, (array) $field, $values[ $key ] ?? '' );
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'reci_get_collaborator_application_notices' ) ) {
	/**
	 * Human-readable copy for the application_success / application_error query args.
	 */
	function reci_get_collaborator_application_notices(): array {
		return [
			'success' => [
				'already_approved'     => __( 'Your collaborator access is already active.', 'reci-media-hub' ),
				'pending'              => __( 'Your collaborator application is under review.', 'reci-media-hub' ),
				'pending_with_account' => __( 'Your member account has been created and your collaborator application is under review. Please verify your email address if prompted.', 'reci-media-hub' ),
			],
			'error'   => [
				'invalid_nonce'          => __( 'Security check failed. Please try again.', 'reci-media-hub' ),
				'missing_fields'         => __( 'Please complete the required fields.', 'reci-media-hub' ),
				'missing_account_fields' => __( 'Please complete the required account fields.', 'reci-media-hub' ),
				'password_mismatch'      => __( 'Passwords do not match.', 'reci-media-hub' ),
				'password_too_short'     => __( 'Password must be at least 8 characters.', 'reci-media-hub' ),
				'registration_disabled'  => __( 'Registration is currently disabled.', 'reci-media-hub' ),
				'existing_user_email'    => __( 'An account with this email address already exists.', 'reci-media-hub' ),
				'existing_user_login'    => __( 'That username already exists.', 'reci-media-hub' ),
				'save_failed'            => __( 'We could not save your application. Please try again.', 'reci-media-hub' ),
			],
		];
	}
}

if ( ! function_exists( 'reci_collaborator_application_just_submitted' ) ) {
	/**
	 * True right after an application was saved.
	 *
	 * A guest who just applied is not logged in yet, so their collaborator status
	 * still reads as `guest`. Callers use this to show the hold state instead of
	 * re-rendering an empty form under a success notice.
	 */
	function reci_collaborator_application_just_submitted(): bool {
		if ( ! isset( $_GET['application_success'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		$key = sanitize_key( wp_unslash( $_GET['application_success'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return in_array( $key, [ 'pending', 'pending_with_account' ], true );
	}
}

if ( ! function_exists( 'reci_render_collaborator_application_notices' ) ) {
	/**
	 * Render success/error notices for the collaborator application flow.
	 */
	function reci_render_collaborator_application_notices(): void {
		$notices = reci_get_collaborator_application_notices();

		if ( isset( $_GET['application_success'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$key = sanitize_key( wp_unslash( $_GET['application_success'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="mb-8 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">';
			echo esc_html( $notices['success'][ $key ] ?? __( 'Your collaborator application is under review.', 'reci-media-hub' ) );
			echo '</div>';
		}

		if ( isset( $_GET['application_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$key = sanitize_key( wp_unslash( $_GET['application_error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">';
			echo esc_html( $notices['error'][ $key ] ?? __( 'Something went wrong. Please try again.', 'reci-media-hub' ) );
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'reci_handle_collaborator_application' ) ) {
	function reci_handle_collaborator_application(): void {
		// The application form is rendered on both /become-a-collaborator/ and the
		// canonical /submit/ flow; return the user to whichever one they started on.
		$context    = sanitize_key( wp_unslash( $_POST['reci_application_context'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$target_url = 'submit' === $context ? home_url( '/submit/' ) : reci_get_collaborator_page_url();

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

			$password_error = function_exists( 'reci_password_error_code' )
				? reci_password_error_code( $password )
				: ( mb_strlen( $password ) < 8 ? 'password_too_short' : '' );
			if ( '' !== $password_error ) {
				wp_safe_redirect( add_query_arg( 'application_error', $password_error, $target_url ) );
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

			reci_send_verification_email( (int) $user_id, $email, $full_name, $token );
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
		$affiliation_term     = sanitize_text_field( wp_unslash( $_POST['reci_affiliation_term'] ?? '' ) );
		$expertise_terms      = array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['reci_expertise_terms'] ?? [] ) );
		// Free-text additions arrive comma separated and are flagged on creation.
		$expertise_other      = sanitize_text_field( wp_unslash( $_POST['reci_expertise_terms_other'] ?? '' ) );
		if ( '' !== $expertise_other ) {
			$expertise_terms = array_merge( $expertise_terms, array_map( 'trim', explode( ',', $expertise_other ) ) );
		}
		$expertise_terms = array_values( array_unique( array_filter( $expertise_terms ) ) );
		$full_name    = trim( $first_name . ' ' . $last_name );

		if ( '' === $full_name || ! is_email( $email ) || '' === $organization || '' === $department || '' === $role || '' === $bio || '' === $affiliated_with_pitt || '' === $membership_objective || '' === $affiliation_term ) {
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
		update_post_meta( $post_id, '_reci_collaborator_affiliation_term', $affiliation_term );
		update_post_meta( $post_id, '_reci_collaborator_expertise_terms', wp_json_encode( $expertise_terms ) );
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

		// Mirror the shared profile fields onto the user so /submit/ and the
		// dashboard profile editor can pre-fill from a single source of truth.
		reci_save_user_collaborator_profile_data(
			$user_id,
			[
				'reci_firstname'            => $first_name,
				'reci_lastname'             => $last_name,
				'reci_affiliated_with_pitt' => $affiliated_with_pitt,
				'reci_pitt_affiliation'     => $pitt_affiliation,
				'submission_organization'   => $organization,
				'reci_department'           => $department,
				'submission_role'           => $role,
				'submission_bio'            => $bio,
				'submission_website'        => $website,
				'reci_social_handles'       => $social_handles,
				'reci_affiliation_term'     => $affiliation_term,
				'reci_expertise_terms'      => $expertise_terms,
			]
		);

		if ( function_exists( 'reci_send_staff_submission_notification' ) ) {
			reci_send_staff_submission_notification( (int) $post_id );
		}

		$success_key = is_user_logged_in() ? 'pending' : 'pending_with_account';
		wp_safe_redirect( add_query_arg( 'application_success', $success_key, $target_url ) );
		exit;
	}
}

add_action( 'admin_post_reci_collaborator_application', 'reci_handle_collaborator_application' );
// The handler creates the account for guests, so it must also run unauthenticated —
// this is the entry point for the whole /submit/ contribution flow.
add_action( 'admin_post_nopriv_reci_collaborator_application', 'reci_handle_collaborator_application' );

if ( ! function_exists( 'reci_get_collaborator_profile_ids_for_user' ) ) {
	/**
	 * Public profile posts belonging to one account.
	 *
	 * @return array<int,int>
	 */
	function reci_get_collaborator_profile_ids_for_user( int $user_id ): array {
		if ( $user_id <= 0 ) {
			return [];
		}

		return array_map(
			'absint',
			get_posts(
				[
					'post_type'      => 'reci_author',
					'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_key'       => '_reci_author_profile_user_id',
					'meta_value'     => $user_id,
				]
			)
		);
	}
}

if ( ! function_exists( 'reci_set_collaborator_profile_status' ) ) {
	/**
	 * Publish or unpublish an account's public profile.
	 *
	 * Draft rather than trash or delete: rejection is reversible, and a profile
	 * carries imported biography and taxonomy work that should survive being
	 * taken off the site.
	 */
	function reci_set_collaborator_profile_status( int $user_id, string $status ): int {
		$changed = 0;

		foreach ( reci_get_collaborator_profile_ids_for_user( $user_id ) as $profile_id ) {
			if ( get_post_status( $profile_id ) === $status ) {
				continue;
			}

			wp_update_post( [ 'ID' => $profile_id, 'post_status' => $status ] );
			++$changed;
		}

		return $changed;
	}
}

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

			// Approval is a promotion: Member -> Collaborator. Anyone already
			// higher up the ladder keeps their level, so approving an Editor's
			// application never demotes them.
			$approved_user = get_user_by( 'id', $user_id );
			if ( $approved_user instanceof WP_User && ! user_can( $user_id, 'edit_posts' ) ) {
				$approved_user->set_role( 'contributor' );
			}
			if ( function_exists( 'reci_sync_collaborator_profile_from_application' ) ) {
				reci_sync_collaborator_profile_from_application( (int) $post->ID, $user_id );
			}
			if ( function_exists( 'reci_media_hub_create_author_profile_from_submission' ) ) {
				reci_media_hub_create_author_profile_from_submission( (int) $post->ID );
			}

			// Re-approving after a rejection has to put the profile back, or the
			// account would be a collaborator with no public page.
			reci_set_collaborator_profile_status( $user_id, 'publish' );
			if ( function_exists( 'reci_create_notification' ) ) {
				reci_create_notification( $user_id, 'collaborator_application_approved', __( 'Collaborator application approved', 'reci-media-hub' ), __( 'Your collaborator application has been approved. You can now submit content.', 'reci-media-hub' ), home_url( '/submit/' ), (int) $post->ID );
			}
			if ( '1' === get_user_meta( $user_id, 'reci_notify_collaborator_application_status', true ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user && ! empty( $user->user_email ) ) {
					reci_send_email(
						(string) $user->user_email,
						__( 'Your collaborator application was approved', 'reci-media-hub' ),
						__( 'You are now a RECI Collaborator', 'reci-media-hub' ),
						[
							[ 'type' => 'text', 'text' => sprintf( __( 'Congratulations %s — your collaborator application has been approved.', 'reci-media-hub' ), $user->display_name ) ],
							[ 'type' => 'text', 'text' => __( 'Your public collaborator profile is live, and content submission is now open to you.', 'reci-media-hub' ) ],
							[ 'type' => 'button', 'label' => __( 'Submit your first contribution', 'reci-media-hub' ), 'url' => home_url( '/submit/' ) ],
							[ 'type' => 'note', 'text' => __( 'Keep your profile current from your dashboard — it is what readers see beside your work.', 'reci-media-hub' ) ],
						],
						__( 'Your collaborator application has been approved.', 'reci-media-hub' )
					);
				}
			}
			return;
		}

		if ( 'trash' !== $new_status ) {
			update_post_meta( $post->ID, '_reci_collaborator_application_status', 'rejected' );
			update_user_meta( $user_id, '_reci_collaborator_status', 'rejected' );

			// Approval promotes, so rejection revokes. Only an account sitting at
			// Collaborator is demoted: anyone deliberately raised above that was
			// promoted by a human decision this one should not undo.
			$rejected_user = get_user_by( 'id', $user_id );
			if ( $rejected_user instanceof WP_User && [ 'contributor' ] === array_values( $rejected_user->roles ) ) {
				$rejected_user->set_role( 'subscriber' );
			}

			// Take the public profile down with the access. Draft, not deleted, so
			// re-approving restores it rather than rebuilding it.
			reci_set_collaborator_profile_status( $user_id, 'draft' );
			if ( function_exists( 'reci_create_notification' ) ) {
				reci_create_notification( $user_id, 'collaborator_application_rejected', __( 'Collaborator application updated', 'reci-media-hub' ), __( 'Your collaborator application was not approved at this time.', 'reci-media-hub' ), reci_get_collaborator_page_url(), (int) $post->ID );
			}
			if ( '1' === get_user_meta( $user_id, 'reci_notify_collaborator_application_status', true ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user && ! empty( $user->user_email ) ) {
					reci_send_email(
						(string) $user->user_email,
						__( 'Your collaborator application was updated', 'reci-media-hub' ),
						__( 'An update on your application', 'reci-media-hub' ),
						[
							[ 'type' => 'text', 'text' => sprintf( __( 'Hello %s, thank you for applying to contribute to RECI.', 'reci-media-hub' ), $user->display_name ) ],
							[ 'type' => 'text', 'text' => __( 'Your application was not approved at this time. This is not a closed door — you are welcome to update your details and apply again.', 'reci-media-hub' ) ],
							[ 'type' => 'button', 'label' => __( 'Update your details', 'reci-media-hub' ), 'url' => home_url( '/dashboard/profile/' ) ],
						],
						__( 'An update on your RECI collaborator application.', 'reci-media-hub' )
					);
				}
			}
		}
	}
}

add_action( 'transition_post_status', 'reci_sync_collaborator_application_status', 10, 3 );

if ( ! function_exists( 'reci_sync_collaborator_profile_from_application' ) ) {
	/**
	 * Publish a user's collaborator profile to their public `reci_author` post.
	 *
	 * The user account is the source of truth; this projects it outward. Fields
	 * that exist only to review an application — the membership objective above
	 * all — stay on the application post and are never published here.
	 */
	function reci_sync_collaborator_profile_from_application( int $application_id, int $user_id ): void {
		$profile = function_exists( 'reci_get_user_collaborator_profile_data' )
			? reci_get_user_collaborator_profile_data( $user_id )
			: [];

		$full_name = trim( (string) ( $profile['reci_firstname'] ?? '' ) . ' ' . (string) ( $profile['reci_lastname'] ?? '' ) );
		if ( '' === $full_name ) {
			$full_name = trim(
				(string) get_post_meta( $application_id, '_reci_submission_first_name', true ) . ' ' .
				(string) get_post_meta( $application_id, '_reci_submission_last_name', true )
			);
		}

		$bio          = (string) ( $profile['submission_bio'] ?? '' );
		$title        = (string) ( $profile['submission_role'] ?? '' );
		$website      = (string) ( $profile['submission_website'] ?? '' );
		$organization = (string) ( $profile['submission_organization'] ?? '' );
		$department   = (string) ( $profile['reci_department'] ?? '' );
		$social       = (string) ( $profile['reci_social_handles'] ?? '' );
		$email        = (string) ( $profile['user_email'] ?? '' );
		$pitt         = (string) ( $profile['reci_pitt_affiliation'] ?? '' );

		$profile_id = function_exists( 'reci_media_hub_get_author_profile_by_user_id' ) ? reci_media_hub_get_author_profile_by_user_id( $user_id ) : 0;

		// An imported profile may already exist for this person under the same
		// email — claim it rather than creating a duplicate.
		if ( $profile_id <= 0 && '' !== $email && function_exists( 'reci_media_hub_find_author_profile_by_email' ) ) {
			$profile_id = reci_media_hub_find_author_profile_by_email( $email );
		}

		if ( $profile_id <= 0 && function_exists( 'reci_media_hub_create_or_get_author_profile' ) ) {
			$profile_id = reci_media_hub_create_or_get_author_profile( $full_name, $title, $bio );
		}

		if ( $profile_id <= 0 ) {
			return;
		}

		wp_update_post([
			'ID'           => $profile_id,
			'post_title'   => $full_name !== '' ? $full_name : get_the_title( $profile_id ),
			'post_content' => $bio,
			'post_excerpt' => $bio,
			'post_status'  => 'publish',
		]);

		$meta = [
			'_reci_author_profile_user_id'  => $user_id,
			'_reci_author_profile_title'    => $title,
			'_reci_author_email'            => $email,
			'_reci_author_organization'     => $organization,
			'_reci_author_department'       => $department,
			'_reci_author_pitt_affiliation' => $pitt,
			'_reci_author_website'          => $website,
			'_reci_author_social_links'     => $social,
		];

		foreach ( $meta as $key => $value ) {
			update_post_meta( $profile_id, $key, $value );
		}

		$profile_image_id = absint( get_post_meta( $application_id, '_reci_collaborator_profile_image_id', true ) );
		if ( $profile_image_id > 0 ) {
			set_post_thumbnail( $profile_id, $profile_image_id );
		}

		$cv_id = absint( get_post_meta( $application_id, '_reci_collaborator_cv_attachment_id', true ) );
		if ( $cv_id > 0 ) {
			update_post_meta( $profile_id, '_reci_author_cv_id', $cv_id );
		}

		if ( function_exists( 'reci_assign_profile_terms' ) ) {
			$affiliation_term = (string) ( $profile['reci_affiliation_term'] ?? '' );
			if ( '' !== $affiliation_term ) {
				reci_assign_profile_terms( $profile_id, 'reci_affiliation', [ $affiliation_term ] );
			}

			$expertise_terms = (array) ( $profile['reci_expertise_terms'] ?? [] );
			if ( ! empty( $expertise_terms ) ) {
				// Terms the collaborator typed themselves are flagged for review.
				reci_assign_profile_terms( $profile_id, 'reci_expertise', $expertise_terms, true );
			}
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

// ── Follow intent carried through sign-in ────────────────────────────────────

if ( ! function_exists( 'reci_follow_after_login_url' ) ) {
	/**
	 * Sign-in URL that remembers the visitor meant to follow this collaborator.
	 *
	 * The intent rides on redirect_to, so after authenticating they land back on
	 * the profile with the follow already applied.
	 */
	function reci_follow_after_login_url( int $profile_id ): string {
		$target = add_query_arg( 'reci_follow', $profile_id, (string) get_permalink( $profile_id ) );
		$sign_in = function_exists( 'reci_get_auth_page_url' ) ? reci_get_auth_page_url( 'sign-in' ) : '';

		return '' !== $sign_in
			? add_query_arg( 'redirect_to', rawurlencode( $target ), $sign_in )
			: wp_login_url( $target );
	}
}

/**
 * Apply a pending follow at the moment of login.
 *
 * Hooked to wp_login rather than read from the URL on page load: this only runs
 * as part of an authentication the visitor just performed, so a crafted link
 * cannot make a signed-in user follow someone.
 */
add_action( 'wp_login', 'reci_apply_pending_follow', 10, 2 );
function reci_apply_pending_follow( string $user_login, WP_User $user ): void {
	$redirect = isset( $_REQUEST['redirect_to'] ) ? (string) wp_unslash( $_REQUEST['redirect_to'] ) : '';

	if ( '' === $redirect ) {
		return;
	}

	$query = (string) wp_parse_url( urldecode( $redirect ), PHP_URL_QUERY );

	if ( '' === $query ) {
		return;
	}

	parse_str( $query, $args );
	$profile_id = absint( $args['reci_follow'] ?? 0 );

	if ( $profile_id <= 0 || 'reci_author' !== get_post_type( $profile_id ) ) {
		return;
	}

	$following = reci_get_user_followed_collaborator_ids( $user->ID );

	if ( in_array( $profile_id, $following, true ) ) {
		return;
	}

	$following[] = $profile_id;
	update_user_meta( $user->ID, 'reci_followed_collaborators', array_values( array_unique( array_map( 'absint', $following ) ) ) );
}

/**
 * Drop the intent parameter once it has been acted on.
 *
 * Leaving it in the address bar would mean a reload or a shared link carrying an
 * instruction that has already been carried out.
 */
add_action( 'template_redirect', 'reci_clean_follow_intent_url' );
function reci_clean_follow_intent_url(): void {
	if ( empty( $_GET['reci_follow'] ) || ! is_singular( 'reci_author' ) ) {
		return;
	}

	wp_safe_redirect( remove_query_arg( 'reci_follow' ) );
	exit;
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
		$bio          = (string) get_post_meta( $post->ID, '_reci_submission_bio', true );
		$affiliated_with_pitt = (string) get_post_meta( $post->ID, '_reci_collaborator_affiliated_with_pitt', true );
		$pitt_affiliation     = (string) get_post_meta( $post->ID, '_reci_collaborator_pitt_affiliation', true );
		$department           = (string) get_post_meta( $post->ID, '_reci_collaborator_department', true );
		$social_handles       = (string) get_post_meta( $post->ID, '_reci_collaborator_social_handles', true );
		$membership_objective = (string) get_post_meta( $post->ID, '_reci_collaborator_membership_objective', true );
		$affiliation_term     = (string) get_post_meta( $post->ID, '_reci_collaborator_affiliation_term', true );
		$expertise_terms      = reci_collaborator_application_expertise( (int) $post->ID );
		$profile_image_id     = absint( get_post_meta( $post->ID, '_reci_collaborator_profile_image_id', true ) );
		$cv_attachment_id     = absint( get_post_meta( $post->ID, '_reci_collaborator_cv_attachment_id', true ) );

		$full_name = trim( $first_name . ' ' . $last_name ) ?: get_the_title( $post );
		?>
		<style>
			.reci-app-review { margin-top: 4px; }
			.reci-app-review__head { display: flex; gap: 18px; align-items: flex-start; padding-bottom: 18px; border-bottom: 1px solid #dcdcde; }
			.reci-app-review__photo img { display: block; width: 96px; height: 96px; object-fit: cover; border-radius: 6px; border: 1px solid #dcdcde; }
			.reci-app-review__photo--empty { width: 96px; height: 96px; border-radius: 6px; border: 1px dashed #c3c4c7; display: flex; align-items: center; justify-content: center; color: #8c8f94; font-size: 11px; text-align: center; line-height: 1.3; padding: 6px; box-sizing: border-box; }
			.reci-app-review__name { margin: 0 0 6px; font-size: 18px; line-height: 1.3; }
			.reci-app-review__pill { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
			.reci-app-review__pill--pending { background: #fcf3d8; color: #7a5b00; }
			.reci-app-review__pill--approved { background: #e3f2e1; color: #1c5c2e; }
			.reci-app-review__pill--rejected { background: #fbeaea; color: #8a2424; }
			/* Label column, value column. Long prose wraps in the value column
			   rather than breaking the alignment of everything above it. */
			.reci-app-review__rows { display: grid; grid-template-columns: 220px minmax(0, 1fr); }
			.reci-app-review__rows > dt,
			.reci-app-review__rows > dd { padding: 11px 0; border-bottom: 1px solid #f0f0f1; margin: 0; }
			.reci-app-review__rows > dt { font-weight: 600; color: #50575e; padding-right: 20px; }
			.reci-app-review__rows > dd { color: #1d2327; word-wrap: break-word; overflow-wrap: anywhere; }
			.reci-app-review__rows > dd p { margin: 0 0 8px; }
			.reci-app-review__rows > dd p:last-child { margin-bottom: 0; }
			.reci-app-review__empty { color: #8c8f94; }
			.reci-app-review__chips { display: flex; flex-wrap: wrap; gap: 6px; }
			.reci-app-review__chip { background: #f0f0f1; border-radius: 3px; padding: 3px 9px; font-size: 12px; }
			.reci-app-review__actions { padding-top: 18px; }
			@media screen and (max-width: 782px) {
				.reci-app-review__rows { grid-template-columns: minmax(0, 1fr); }
				.reci-app-review__rows > dt { padding-bottom: 0; border-bottom: 0; }
				.reci-app-review__rows > dd { padding-top: 4px; }
			}
		</style>

		<div class="reci-app-review">
			<div class="reci-app-review__head">
				<div class="reci-app-review__photo">
					<?php
					if ( $profile_image_id > 0 && wp_get_attachment_image( $profile_image_id, [ 96, 96 ] ) ) {
						$full = wp_get_attachment_image_url( $profile_image_id, 'full' );
						printf(
							'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
							esc_url( (string) $full ),
							wp_get_attachment_image( $profile_image_id, [ 96, 96 ] )
						);
					} else {
						echo '<div class="reci-app-review__photo--empty">' . esc_html__( 'No photo', 'reci-media-hub' ) . '</div>';
					}
					?>
				</div>
				<div>
					<h2 class="reci-app-review__name"><?php echo esc_html( $full_name ); ?></h2>
					<?php
					$state = 'publish' === $post->post_status ? 'approved' : ( 'draft' === $post->post_status ? 'rejected' : 'pending' );
					printf(
						'<span class="reci-app-review__pill reci-app-review__pill--%s">%s</span>',
						esc_attr( $state ),
						esc_html( ucfirst( $status ?: $state ) )
					);
					?>
					<p style="margin:8px 0 0;color:#50575e;">
						<?php
						echo $user instanceof WP_User
							? esc_html( sprintf( __( 'Linked account: %1$s (#%2$d)', 'reci-media-hub' ), $user->display_name, $user->ID ) )
							: esc_html__( 'No linked member account', 'reci-media-hub' );
						?>
					</p>
				</div>
			</div>

			<dl class="reci-app-review__rows">
				<?php
				reci_app_review_row( __( 'Email', 'reci-media-hub' ), $email ? sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) ) : '', true );
				reci_app_review_row( __( 'Personal Bio', 'reci-media-hub' ), $bio ? wpautop( esc_html( $bio ) ) : '', true );
				reci_app_review_row( __( 'Subject Areas', 'reci-media-hub' ), reci_app_review_chips( $expertise_terms ), true );
				reci_app_review_row( __( 'Affiliation', 'reci-media-hub' ), $affiliation_term );
				reci_app_review_row( __( 'Affiliated with Pitt', 'reci-media-hub' ), $affiliated_with_pitt );
				reci_app_review_row( __( 'Pitt Affiliation', 'reci-media-hub' ), $pitt_affiliation );
				reci_app_review_row( __( 'Organization', 'reci-media-hub' ), $organization );
				reci_app_review_row( __( 'Department', 'reci-media-hub' ), $department );
				reci_app_review_row( __( 'Role / Title', 'reci-media-hub' ), $role );
				reci_app_review_row(
					__( 'Website', 'reci-media-hub' ),
					$website ? sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>', esc_url( $website ), esc_html( $website ) ) : '',
					true
				);
				reci_app_review_row( __( 'Social Handles', 'reci-media-hub' ), $social_handles );
				reci_app_review_row( __( 'Main Objective for Membership', 'reci-media-hub' ), $membership_objective ? wpautop( esc_html( $membership_objective ) ) : '', true );
				reci_app_review_row( __( 'CV Upload', 'reci-media-hub' ), reci_app_review_attachment_link( $cv_attachment_id ), true );
				?>
			</dl>

			<div class="reci-app-review__actions">
		<?php
		// Keeps the original Review Actions presentation — a description above a
		// row of primary and secondary buttons. What changed is the wiring: the
		// old pair posted to action=reci_collaborator_decision, for which no
		// handler was ever registered, so neither button did anything. These use
		// the approve and reject endpoints, and each only appears when it applies.
		echo '<strong>' . esc_html__( 'Review Actions', 'reci-media-hub' ) . '</strong>';

		if ( current_user_can( 'reci_approve_collaborators' ) ) {
			if ( 'publish' === $post->post_status ) {
				$hint = __( 'Approved. Rejecting revokes access, unpublishes their profile and emails the applicant.', 'reci-media-hub' );
			} elseif ( 'draft' === $post->post_status ) {
				$hint = __( 'Rejected. Approving reinstates the collaborator and republishes their profile.', 'reci-media-hub' );
			} else {
				$hint = __( 'Approving publishes their profile, promotes the account and emails the applicant.', 'reci-media-hub' );
			}

			echo '<p class="description">' . esc_html( $hint ) . '</p>';
			echo '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">';

			if ( 'publish' !== $post->post_status ) {
				printf(
					'<a href="%s" class="button button-primary">%s</a>',
					esc_url( reci_collaborator_approve_url( (int) $post->ID ) ),
					esc_html__( 'Approve Collaborator', 'reci-media-hub' )
				);
			}

			if ( 'draft' !== $post->post_status ) {
				printf(
					'<a href="%s" class="button button-secondary">%s</a>',
					esc_url( reci_collaborator_reject_url( (int) $post->ID ) ),
					esc_html__( 'Reject Application', 'reci-media-hub' )
				);
			}

			echo '</div>';
		}
		?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'reci_app_review_row' ) ) {
	/**
	 * One label/value pair in the review list.
	 *
	 * @param string $value  Already-escaped markup when $is_html, plain text otherwise.
	 */
	function reci_app_review_row( string $label, string $value, bool $is_html = false ): void {
		$value = trim( $value );

		printf( '<dt>%s</dt>', esc_html( $label ) );

		if ( '' === $value ) {
			printf( '<dd><span class="reci-app-review__empty">%s</span></dd>', esc_html__( 'Not provided', 'reci-media-hub' ) );
			return;
		}

		printf( '<dd>%s</dd>', $is_html ? wp_kses_post( $value ) : esc_html( $value ) );
	}
}

if ( ! function_exists( 'reci_collaborator_application_expertise' ) ) {
	/**
	 * Subject areas as submitted.
	 *
	 * The application stores them as a JSON list on the post rather than as
	 * taxonomy terms — terms are only created once the application is approved.
	 *
	 * @return array<int,string>
	 */
	function reci_collaborator_application_expertise( int $post_id ): array {
		$raw = get_post_meta( $post_id, '_reci_collaborator_expertise_terms', true );

		if ( is_array( $raw ) ) {
			return array_values( array_filter( array_map( 'strval', $raw ) ) );
		}

		$raw = trim( (string) $raw );

		if ( '' === $raw ) {
			return [];
		}

		$decoded = json_decode( $raw, true );

		if ( is_array( $decoded ) ) {
			return array_values( array_filter( array_map( 'strval', $decoded ) ) );
		}

		// Older records stored a comma-separated string.
		return array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
	}
}

if ( ! function_exists( 'reci_app_review_chips' ) ) {
	/**
	 * @param array<int,string> $items
	 */
	function reci_app_review_chips( array $items ): string {
		if ( empty( $items ) ) {
			return '';
		}

		$chips = '';
		foreach ( $items as $item ) {
			$chips .= '<span class="reci-app-review__chip">' . esc_html( $item ) . '</span>';
		}

		return '<div class="reci-app-review__chips">' . $chips . '</div>';
	}
}

if ( ! function_exists( 'reci_app_review_attachment_link' ) ) {
	function reci_app_review_attachment_link( int $attachment_id ): string {
		if ( $attachment_id <= 0 ) {
			return '';
		}

		$url = wp_get_attachment_url( $attachment_id );

		if ( ! $url ) {
			return '';
		}

		return sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( $url ),
			esc_html( basename( (string) wp_parse_url( $url, PHP_URL_PATH ) ) )
		);
	}
}

// ── One-click approve ────────────────────────────────────────────────────────

if ( ! function_exists( 'reci_collaborator_approve_url' ) ) {
	/**
	 * Nonced URL that approves one application.
	 */
	function reci_collaborator_approve_url( int $post_id ): string {
		return wp_nonce_url(
			add_query_arg(
				[ 'action' => 'reci_approve_collaborator', 'post' => $post_id ],
				admin_url( 'admin-post.php' )
			),
			'reci_approve_collaborator_' . $post_id
		);
	}
}

if ( ! function_exists( 'reci_collaborator_reject_url' ) ) {
	/**
	 * Nonced URL that rejects one application.
	 */
	function reci_collaborator_reject_url( int $post_id ): string {
		return wp_nonce_url(
			add_query_arg(
				[ 'action' => 'reci_reject_collaborator', 'post' => $post_id ],
				admin_url( 'admin-post.php' )
			),
			'reci_reject_collaborator_' . $post_id
		);
	}
}

add_action( 'admin_post_reci_reject_collaborator', 'reci_handle_reject_collaborator' );

/**
 * Reject an application.
 *
 * Moving it out of 'publish' is what rejection means: the existing
 * transition_post_status branch marks it rejected, records the status on the
 * account and emails the applicant. Draft rather than trash, so the record of
 * who applied survives.
 */
function reci_handle_reject_collaborator(): void {
	$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
	$list    = admin_url( 'edit.php?post_type=' . reci_get_collaborator_application_post_type() );

	if ( ! current_user_can( 'reci_approve_collaborators' ) ) {
		wp_die( esc_html__( 'You do not have permission to review collaborators.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_reject_collaborator_' . $post_id );

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || reci_get_collaborator_application_post_type() !== $post->post_type ) {
		wp_safe_redirect( add_query_arg( 'reci_approved', 'invalid', $list ) );
		exit;
	}

	if ( 'draft' === $post->post_status ) {
		wp_safe_redirect( add_query_arg( 'reci_approved', 'already_rejected', $list ) );
		exit;
	}

	wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );

	wp_safe_redirect( add_query_arg( 'reci_approved', 'rejected', $list ) );
	exit;
}

add_action( 'admin_post_reci_approve_collaborator', 'reci_handle_approve_collaborator' );

/**
 * Approve an application and publish the collaborator.
 *
 * Publishing the application is what approval means: transition_post_status
 * promotes the account, syncs the profile and sends the notification. This just
 * gives staff a single button for it instead of the generic Publish control.
 */
function reci_handle_approve_collaborator(): void {
	$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
	$list    = admin_url( 'edit.php?post_type=' . reci_get_collaborator_application_post_type() );

	if ( ! current_user_can( 'reci_approve_collaborators' ) ) {
		wp_die( esc_html__( 'You do not have permission to approve collaborators.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_approve_collaborator_' . $post_id );

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || reci_get_collaborator_application_post_type() !== $post->post_type ) {
		wp_safe_redirect( add_query_arg( 'reci_approved', 'invalid', $list ) );
		exit;
	}

	if ( 'publish' === $post->post_status ) {
		wp_safe_redirect( add_query_arg( 'reci_approved', 'already', $list ) );
		exit;
	}

	wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ] );

	wp_safe_redirect( add_query_arg( 'reci_approved', '1', $list ) );
	exit;
}

/**
 * Approve link on each pending row.
 */
add_filter( 'post_row_actions', 'reci_collaborator_application_row_actions', 10, 2 );
function reci_collaborator_application_row_actions( array $actions, WP_Post $post ): array {
	if ( reci_get_collaborator_application_post_type() !== $post->post_type ) {
		return $actions;
	}

	if ( ! current_user_can( 'reci_approve_collaborators' ) ) {
		return $actions;
	}

	$review = [];

	if ( 'publish' !== $post->post_status ) {
		$review['reci_approve'] = sprintf(
			'<a href="%s" style="color:#1f7a5a;font-weight:600;">%s</a>',
			esc_url( reci_collaborator_approve_url( (int) $post->ID ) ),
			esc_html__( 'Approve', 'reci-media-hub' )
		);
	}

	if ( 'draft' !== $post->post_status ) {
		$review['reci_reject'] = sprintf(
			'<a href="%s" style="color:#9d2f45;">%s</a>',
			esc_url( reci_collaborator_reject_url( (int) $post->ID ) ),
			esc_html__( 'Reject', 'reci-media-hub' )
		);
	}

	return array_merge( $review, $actions );
}

/**
 * Result notice after approving.
 */
add_action( 'admin_notices', 'reci_collaborator_approval_notice' );
function reci_collaborator_approval_notice(): void {
	if ( ! isset( $_GET['reci_approved'] ) ) {
		return;
	}

	$code = sanitize_key( wp_unslash( $_GET['reci_approved'] ) );

	$messages = [
		'1'               => [ 'success', __( 'Collaborator approved and published.', 'reci-media-hub' ) ],
		'rejected'        => [ 'warning', __( 'Application rejected. The applicant has been notified.', 'reci-media-hub' ) ],
		'already_rejected' => [ 'info', __( 'That application was already rejected.', 'reci-media-hub' ) ],
		'already' => [ 'info', __( 'That application was already approved.', 'reci-media-hub' ) ],
		'invalid' => [ 'error', __( 'That application could not be found.', 'reci-media-hub' ) ],
	];

	if ( ! isset( $messages[ $code ] ) ) {
		return;
	}

	printf(
		'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
		esc_attr( $messages[ $code ][0] ),
		esc_html( $messages[ $code ][1] )
	);
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
