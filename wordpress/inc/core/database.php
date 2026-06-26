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
	$current_ver   = '1.1.0';

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

		dbDelta( $sql_journals );
		dbDelta( $sql_assessments );

		if ( version_compare( $installed_ver, '1.1.0', '<' ) ) {
			// Migrate reci_article to standard post
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = 'post' WHERE post_type = %s", 'reci_article' ) );
		}

		update_option( 'reci_db_version', $current_ver );
	}
}

add_action( 'admin_init', 'reci_media_hub_create_custom_tables' );
add_action( 'after_switch_theme', 'reci_media_hub_create_custom_tables' );
