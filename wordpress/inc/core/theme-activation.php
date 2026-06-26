<?php

/**
 * Theme activation setup.
 *
 * Runs once when the theme is activated (after_switch_theme).
 * Creates all required pages with their templates if they don't already exist,
 * then stores a slug → ID map in the 'reci_pages' option for fast lookups.
 *
 * Safe to re-run: existing pages are never deleted or overwritten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	add_action( 'after_switch_theme', 'reci_theme_activation_setup' );
	
function reci_theme_activation_setup(): void {
	// Pages to create: slug => [title, template path].
	$pages = [
		// Auth
		'sign-in'                  => [ 'Sign In',             'page-templates/template-sign-in.php' ],
		'sign-up'                  => [ 'Sign Up',             'page-templates/template-sign-up.php' ],
		'forgot-password'          => [ 'Forgot Password',     'page-templates/template-forgot-password.php' ],
		'forgot-password-response' => [ 'Password Reset Sent', 'page-templates/template-forgot-password-response.php' ],
		'verify-email'             => [ 'Verify Email',        'page-templates/template-verify-email.php' ],

		// Content archives
		'podcasts'           => [ 'Podcasts',           'page-templates/template-podcast-archive.php' ],
		'videos'             => [ 'Videos',             'page-templates/template-video-archive.php' ],
		'reflections'        => [ 'Reflections',        'page-templates/template-reflection-archive.php' ],
		'events'             => [ 'Events',             'page-templates/template-event-archive.php' ],
		'assessments'        => [ 'Assessments',        'page-templates/template-assessment-archive.php' ],
		'quotes'             => [ 'Quotes',             'page-templates/template-quote-archive.php' ],
		'quiz'               => [ 'Quiz',               'page-templates/template-quiz.php' ],
		'quiz-result'        => [ 'Quiz Result',        'page-templates/template-quiz-single-result.php' ],
		'submit'             => [ 'Submit Content',     'page-templates/template-submit-content.php' ],

		// Community / organisation
		'locations'          => [ 'Locations',   'page-templates/template-location-archive.php' ],
		'about'              => [ 'About',        'page-templates/template-about.php' ],

		// Support / fundraising
		'donate'             => [ 'Donate',              'page-templates/template-donate.php' ],
		'donate-faq'         => [ 'Donate – FAQ',        'page-templates/template-donate-faq.php' ],
		'donate-other-ways'  => [ 'Other Ways to Give',  'page-templates/template-donate-other-ways.php' ],
		'donate-benefits'    => [ 'Donor Benefits',      'page-templates/template-donate-benefits.php' ],
		'sponsorship'        => [ 'Sponsorship',         'page-templates/template-sponsorship.php' ],

		// Dashboard
		'dashboard'          => [ 'Dashboard',           'page-templates/dashboard/template-dashboard.php' ],
	];

	// Homepage — create and set as the static front page.
	$home_id = reci_ensure_page( 'home', 'Home', 'page-templates/template-homepage.php' );
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	// Create all other pages and collect their IDs.
	$page_ids = [];
	foreach ( $pages as $slug => [ $title, $template ] ) {
		$id = reci_ensure_page( $slug, $title, $template );
		if ( $id ) {
			$page_ids[ $slug ] = $id;
		}
	}

	// Persist the map so auth helpers can look up page IDs quickly.
	update_option( 'reci_pages', $page_ids );

	// Schedule rewrite rule flush.
	set_transient( 'reci_flush_rewrite_rules', true );
}

add_action( 'init', function() {
	if ( get_transient( 'reci_flush_rewrite_rules' ) ) {
		flush_rewrite_rules();
		delete_transient( 'reci_flush_rewrite_rules' );
	}
} );

/**
 * Ensure a page exists for the given slug with the given template.
 *
 * - If the page already exists: updates the template if it has changed.
 * - If it doesn't exist: creates it as a published page.
 *
 * @return int  Page ID, or 0 on failure.
 */
function reci_ensure_page( string $slug, string $title, string $template ): int {
	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		$current_template = get_post_meta( $existing->ID, '_wp_page_template', true );
		if ( $current_template !== $template ) {
			update_post_meta( $existing->ID, '_wp_page_template', $template );
		}
		return (int) $existing->ID;
	}

	$page_id = wp_insert_post( [
		'post_title'     => $title,
		'post_name'      => $slug,
		'post_status'    => 'publish',
		'post_type'      => 'page',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	] );

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	update_post_meta( $page_id, '_wp_page_template', $template );
	return $page_id;
}
