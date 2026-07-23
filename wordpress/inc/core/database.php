<?php
/**
 * Custom tables setup.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_media_hub_create_custom_tables() {
	global $wpdb;
	
	$installed_ver = get_option( 'reci_db_version' );
	$current_ver   = '1.3.0';

	if ( $installed_ver !== $current_ver ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		// Journals Table
		$table_journals = $wpdb->prefix . 'reci_journals';
		$sql_journals = "CREATE TABLE $table_journals (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			reflection_id bigint(20) unsigned NOT NULL DEFAULT 0,
			prompt text NOT NULL,
			response longtext NOT NULL,
			is_shared tinyint(1) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY reflection_id (reflection_id)
		) $charset_collate;";

		// Assessment Submissions Table
		$table_assessments = $wpdb->prefix . 'reci_assessment_submissions';
		$sql_assessments = "CREATE TABLE $table_assessments (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			assessment_id bigint(20) unsigned NOT NULL DEFAULT 0,
			respondent varchar(255) NOT NULL DEFAULT '',
			score int(11) NOT NULL DEFAULT 0,
			max_score int(11) NOT NULL DEFAULT 0,
			percentage int(11) NOT NULL DEFAULT 0,
			answers_json longtext NOT NULL,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY assessment_id (assessment_id)
		) $charset_collate;";

		// Notifications Table
		$table_notifications = $wpdb->prefix . 'reci_notifications';
		$sql_notifications = "CREATE TABLE $table_notifications (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			type varchar(100) NOT NULL DEFAULT '',
			title varchar(255) NOT NULL DEFAULT '',
			message text NOT NULL,
			target_url text NOT NULL,
			related_post_id bigint(20) unsigned NOT NULL DEFAULT 0,
			is_read tinyint(1) NOT NULL DEFAULT 0,
			email_sent tinyint(1) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY type (type),
			KEY is_read (is_read),
			KEY related_post_id (related_post_id)
		) $charset_collate;";

		dbDelta( $sql_journals );
		dbDelta( $sql_assessments );
		dbDelta( $sql_notifications );

		if ( version_compare( $installed_ver, '1.1.0', '<' ) ) {
			// Migrate reci_article to standard post
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = 'post' WHERE post_type = %s", 'reci_article' ) );
		}

		if ( version_compare( $installed_ver, '1.2.0', '<' ) ) {
			// Update page template paths to reflect the new nested directory structure
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, 'page-templates/', 'templates/page/') WHERE meta_key = '_wp_page_template' AND meta_value LIKE 'page-templates/%'" );
		}

		update_option( 'reci_db_version', $current_ver );
	}
}

add_action( 'admin_init', 'reci_media_hub_create_custom_tables' );
add_action( 'after_switch_theme', 'reci_media_hub_create_custom_tables' );
