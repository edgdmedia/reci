<?php
/**
 * Dashboard settings form.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user_id = get_current_user_id();
$message         = '';
$settings_tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'interests';

$available_tabs = [
	'interests'     => __( 'Interests', 'reci-media-hub' ),
	'notifications' => __( 'Notifications', 'reci-media-hub' ),
	'journal'       => __( 'Journal', 'reci-media-hub' ),
];

if ( ! isset( $available_tabs[ $settings_tab ] ) ) {
	$settings_tab = 'interests';
}

$topic_options    = reci_media_hub_get_taxonomy_terms_for_submission( 'reci_topic' );
$sphere_options   = reci_media_hub_get_taxonomy_terms_for_submission( 'reci_sphere' );
$practice_options = reci_media_hub_get_taxonomy_terms_for_submission( 'reci_practice_focus' );
$audience_options = reci_media_hub_get_taxonomy_terms_for_submission( 'reci_target_audience' );
$collaborator_options = function_exists( 'reci_media_hub_get_author_profile_options' ) ? reci_media_hub_get_author_profile_options( false ) : [];

$saved_topics   = reci_get_user_followed_term_ids( $current_user_id, 'reci_followed_topics' );
$saved_spheres  = reci_get_user_followed_term_ids( $current_user_id, 'reci_followed_spheres' );
$saved_practice = reci_get_user_followed_term_ids( $current_user_id, 'reci_followed_practice_focus' );
$saved_audience = reci_get_user_followed_term_ids( $current_user_id, 'reci_followed_target_audience' );
$saved_collaborators = function_exists( 'reci_get_user_followed_collaborator_ids' ) ? reci_get_user_followed_collaborator_ids( $current_user_id ) : [];

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['reci_settings_nonce'] ) && wp_verify_nonce( $_POST['reci_settings_nonce'], 'reci_update_settings' ) ) {
	$journal_privacy = sanitize_text_field( $_POST['journal_default_privacy'] ?? 'private' );
	$notify_approved = isset( $_POST['notify_submission_approved'] ) ? '1' : '0';
	$notify_rejected = isset( $_POST['notify_submission_rejected'] ) ? '1' : '0';
	$notify_reply    = isset( $_POST['notify_comment_reply'] ) ? '1' : '0';
	$notify_digest   = isset( $_POST['notify_weekly_digest'] ) ? '1' : '0';
	$followed_topics = array_values( array_filter( array_map( 'absint', (array) ( $_POST['reci_followed_topics'] ?? [] ) ) ) );
	$followed_spheres = array_values( array_filter( array_map( 'absint', (array) ( $_POST['reci_followed_spheres'] ?? [] ) ) ) );
	$followed_practice_focus = array_values( array_filter( array_map( 'absint', (array) ( $_POST['reci_followed_practice_focus'] ?? [] ) ) ) );
	$followed_target_audience = array_values( array_filter( array_map( 'absint', (array) ( $_POST['reci_followed_target_audience'] ?? [] ) ) ) );
	$followed_collaborators = array_values( array_filter( array_map( 'absint', (array) ( $_POST['reci_followed_collaborators'] ?? [] ) ) ) );
	$notify_personalized = isset( $_POST['notify_personalized_content'] ) ? '1' : '0';
	$notify_followed_collaborators = isset( $_POST['notify_followed_collaborators'] ) ? '1' : '0';
	$notify_collaborator_application_status = isset( $_POST['notify_collaborator_application_status'] ) ? '1' : '0';

	update_user_meta( $current_user_id, 'reci_journal_default_privacy', $journal_privacy );
	update_user_meta( $current_user_id, 'reci_notify_submission_approved', $notify_approved );
	update_user_meta( $current_user_id, 'reci_notify_submission_rejected', $notify_rejected );
	update_user_meta( $current_user_id, 'reci_notify_comment_reply', $notify_reply );
	update_user_meta( $current_user_id, 'reci_notify_weekly_digest', $notify_digest );
	update_user_meta( $current_user_id, 'reci_followed_topics', $followed_topics );
	update_user_meta( $current_user_id, 'reci_followed_spheres', $followed_spheres );
	update_user_meta( $current_user_id, 'reci_followed_practice_focus', $followed_practice_focus );
	update_user_meta( $current_user_id, 'reci_followed_target_audience', $followed_target_audience );
	update_user_meta( $current_user_id, 'reci_followed_collaborators', $followed_collaborators );
	update_user_meta( $current_user_id, 'reci_notify_personalized_content', $notify_personalized );
	update_user_meta( $current_user_id, 'reci_notify_followed_collaborators', $notify_followed_collaborators );
	update_user_meta( $current_user_id, 'reci_notify_collaborator_application_status', $notify_collaborator_application_status );

	$saved_topics   = $followed_topics;
	$saved_spheres  = $followed_spheres;
	$saved_practice = $followed_practice_focus;
	$saved_audience = $followed_target_audience;
	$saved_collaborators = $followed_collaborators;

	$message = 'Settings saved.';
}

if ( $message ) : ?>
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-6"><?php echo esc_html( $message ); ?></div>
<?php endif; ?>

<form method="post" class="space-y-6">
	<?php wp_nonce_field( 'reci_update_settings', 'reci_settings_nonce' ); ?>

	<div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-4">
		<?php foreach ( $available_tabs as $tab_key => $tab_label ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'tab', $tab_key, home_url( '/dashboard/settings/' ) ) ); ?>" class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-colors <?php echo $settings_tab === $tab_key ? 'bg-amber-100 text-amber-800' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'; ?>"><?php echo esc_html( $tab_label ); ?></a>
		<?php endforeach; ?>
	</div>

	<?php if ( 'journal' === $settings_tab ) : ?>
	<fieldset>
		<legend class="text-lg font-semibold text-zinc-800 mb-4">Journal Default Privacy</legend>
		<div class="space-y-2">
			<label class="flex items-center gap-3">
				<input type="radio" name="journal_default_privacy" value="private" <?php checked( get_user_meta( $current_user_id, 'reci_journal_default_privacy', true ) ?: 'private', 'private' ); ?> class="text-amber-600 accent-amber-600">
				<span class="text-sm text-zinc-700">Private — journal entries are only visible to you</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="radio" name="journal_default_privacy" value="public" <?php checked( get_user_meta( $current_user_id, 'reci_journal_default_privacy', true ), 'public' ); ?> class="text-amber-600 accent-amber-600">
				<span class="text-sm text-zinc-700">Public — new journal entries are shared by default</span>
			</label>
		</div>
	</fieldset>
	<?php endif; ?>

	<?php if ( 'interests' === $settings_tab ) : ?>
	<fieldset>
		<legend class="text-lg font-semibold text-zinc-800 mb-4">Content Interests</legend>
		<p class="text-sm text-zinc-600 mb-4">Choose the topics, lenses, and collaborators you want shaping your dashboard feed.</p>
		<div class="space-y-5">
			<?php
			$interest_groups = [
				[ 'name' => 'reci_followed_topics', 'label' => 'Topics', 'options' => $topic_options, 'saved' => $saved_topics ],
				[ 'name' => 'reci_followed_spheres', 'label' => 'Spheres', 'options' => $sphere_options, 'saved' => $saved_spheres ],
				[ 'name' => 'reci_followed_practice_focus', 'label' => 'Practice Focus', 'options' => $practice_options, 'saved' => $saved_practice ],
				[ 'name' => 'reci_followed_target_audience', 'label' => 'Target Audience', 'options' => $audience_options, 'saved' => $saved_audience ],
			];
			foreach ( $interest_groups as $group ) :
			?>
			<div>
				<p class="text-sm font-medium text-zinc-700 mb-2"><?php echo esc_html( $group['label'] ); ?></p>
				<div class="flex flex-wrap gap-2">
					<?php foreach ( $group['options'] as $option ) : ?>
					<label class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700">
						<input type="checkbox" name="<?php echo esc_attr( $group['name'] ); ?>[]" value="<?php echo esc_attr( (string) $option['id'] ); ?>" <?php checked( in_array( (int) $option['id'], $group['saved'], true ) ); ?> class="text-amber-600 accent-amber-600 rounded">
						<span><?php echo esc_html( (string) $option['name'] ); ?></span>
					</label>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<fieldset>
		<legend class="text-lg font-semibold text-zinc-800 mb-4">Followed Collaborators</legend>
		<div class="flex flex-wrap gap-2">
			<?php foreach ( $collaborator_options as $option ) : ?>
			<label class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700">
				<input type="checkbox" name="reci_followed_collaborators[]" value="<?php echo esc_attr( (string) $option['ID'] ); ?>" <?php checked( in_array( (int) $option['ID'], $saved_collaborators, true ) ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span><?php echo esc_html( (string) $option['display_name'] ); ?></span>
			</label>
			<?php endforeach; ?>
		</div>
	</fieldset>
	<?php endif; ?>

	<?php if ( 'notifications' === $settings_tab ) : ?>
	<fieldset>
		<legend class="text-lg font-semibold text-zinc-800 mb-4">Email Notifications</legend>
		<div class="space-y-3">
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_submission_approved" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_submission_approved', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">When my submission is approved</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_submission_rejected" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_submission_rejected', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">When my submission is rejected</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_comment_reply" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_comment_reply', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">When someone replies to my comment</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_weekly_digest" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_weekly_digest', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">Weekly digest of new content</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_personalized_content" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_personalized_content', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">Email me when new content matches my interests</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_followed_collaborators" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_followed_collaborators', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">Email me when collaborators I follow publish new content or resources</span>
			</label>
			<label class="flex items-center gap-3">
				<input type="checkbox" name="notify_collaborator_application_status" value="1" <?php checked( get_user_meta( $current_user_id, 'reci_notify_collaborator_application_status', true ), '1' ); ?> class="text-amber-600 accent-amber-600 rounded">
				<span class="text-sm text-zinc-700">Email me when my collaborator application status changes</span>
			</label>
		</div>
	</fieldset>
	<?php endif; ?>

	<div>
		<button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">Save Settings</button>
	</div>
</form>
