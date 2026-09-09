<?php
/**
 * Dashboard editor for one submission.
 *
 * Args:
 * - post (WP_Post) The post being edited. Ownership is checked by the template.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edit_post = $args['post'] ?? null;
if ( ! $edit_post instanceof WP_Post ) {
	return;
}

$post_id      = (int) $edit_post->ID;
$is_published = 'publish' === $edit_post->post_status;
$can_trash    = reci_user_can_trash_submission( $post_id );
$content_link = (string) get_post_meta( $post_id, '_reci_submission_content_link', true );

$edit_errors = [
	'invalid_nonce' => __( 'Security check failed. Please try again.', 'reci-media-hub' ),
	'not_allowed'   => __( 'You cannot edit that item.', 'reci-media-hub' ),
	'missing_title' => __( 'A title is required.', 'reci-media-hub' ),
	'save_failed'   => __( 'We could not save your changes. Please try again.', 'reci-media-hub' ),
];
$error_code = isset( $_GET['edit_error'] ) ? sanitize_key( wp_unslash( $_GET['edit_error'] ) ) : '';

$input_classes    = 'w-full rounded-lg border border-zinc-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
$status_labels    = [ 'publish' => __( 'Published', 'reci-media-hub' ), 'pending' => __( 'Pending review', 'reci-media-hub' ), 'draft' => __( 'Draft', 'reci-media-hub' ) ];
$status_label     = $status_labels[ $edit_post->post_status ] ?? $edit_post->post_status;
?>

<?php if ( '' !== $error_code ) : ?>
	<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800" role="alert">
		<?php echo esc_html( $edit_errors[ $error_code ] ?? __( 'Something went wrong. Please try again.', 'reci-media-hub' ) ); ?>
	</div>
<?php endif; ?>

<?php if ( $is_published && ! current_user_can( 'publish_posts' ) ) : ?>
	<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900" role="status">
		<?php esc_html_e( 'This piece is live. Saving changes returns it to the review queue, so it will come off the public site until a member of staff approves the new version.', 'reci-media-hub' ); ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="max-w-4xl space-y-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
	<input type="hidden" name="action" value="reci_update_content" />
	<input type="hidden" name="post_id" value="<?php echo esc_attr( (string) $post_id ); ?>" />
	<?php wp_nonce_field( 'reci_edit_content_' . $post_id, 'reci_edit_nonce' ); ?>

	<div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 pb-5 text-sm">
		<span class="text-zinc-500"><?php esc_html_e( 'Status', 'reci-media-hub' ); ?></span>
		<span class="rounded-full px-2.5 py-1 text-xs font-medium <?php echo $is_published ? 'bg-green-100 text-green-700' : ( 'pending' === $edit_post->post_status ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-600' ); ?>">
			<?php echo esc_html( $status_label ); ?>
		</span>
		<span class="text-zinc-400">&middot;</span>
		<span class="text-zinc-500"><?php echo esc_html( get_post_type_object( $edit_post->post_type )->labels->singular_name ?? $edit_post->post_type ); ?></span>
	</div>

	<div>
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-edit-title"><?php esc_html_e( 'Title', 'reci-media-hub' ); ?></label>
		<input id="reci-edit-title" name="submission_title" type="text" required value="<?php echo esc_attr( $edit_post->post_title ); ?>" class="<?php echo esc_attr( $input_classes ); ?>" />
	</div>

	<div>
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-edit-summary"><?php esc_html_e( 'Summary', 'reci-media-hub' ); ?></label>
		<textarea id="reci-edit-summary" name="submission_summary" rows="3" class="<?php echo esc_attr( $input_classes ); ?>"><?php echo esc_textarea( $edit_post->post_excerpt ); ?></textarea>
		<p class="mt-2 text-xs text-zinc-500"><?php esc_html_e( 'The short description shown in listings.', 'reci-media-hub' ); ?></p>
	</div>

	<div class="reci-editor">
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="submission_details"><?php esc_html_e( 'Details', 'reci-media-hub' ); ?></label>
		<?php
		// Submissions made before this editor existed hold plain text with bare
		// newlines. TinyMCE would collapse those into a single paragraph, so give
		// unmarked-up content its paragraphs before handing it over. One-way: once
		// saved it is real HTML and this no longer applies.
		$editor_content = (string) $edit_post->post_content;
		if ( '' !== $editor_content && $editor_content === wp_strip_all_tags( $editor_content ) ) {
			$editor_content = wpautop( $editor_content );
		}

		wp_editor(
			$editor_content,
			'submission_details',
			[
				'textarea_name' => 'submission_details',
				'textarea_rows' => 18,
				// Subscribers hold no upload_files capability, so the media button
				// would open a modal that cannot do anything.
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => [ 'buttons' => 'strong,em,link,ul,ol,li,block,close' ],
				'tinymce'       => [
					'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,link,unlink,undo,redo',
					'toolbar2' => '',
				],
			]
		);
		?>
		<p class="mt-2 text-xs text-zinc-500"><?php esc_html_e( 'The full body of your submission, including the sections you filled in when you submitted it.', 'reci-media-hub' ); ?></p>
	</div>

	<div>
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-edit-link"><?php esc_html_e( 'Content Link', 'reci-media-hub' ); ?></label>
		<input id="reci-edit-link" name="submission_content_link" type="url" value="<?php echo esc_attr( $content_link ); ?>" class="<?php echo esc_attr( $input_classes ); ?>" placeholder="https://" />
	</div>

	<?php
	// The fields this content type has of its own — audio URL for a podcast,
	// video URL for a video, source and canonical URLs for written pieces. The
	// submission form collects them, so the editor has to be able to fix them.
	$type_key    = (string) get_post_meta( $post_id, '_reci_submission_content_type', true );
	$type_fields = ( '' !== $type_key && function_exists( 'reci_submission_type_fields' ) )
		? reci_submission_type_fields( $type_key )
		: [];
	?>
	<?php if ( ! empty( $type_fields ) ) : ?>
	<fieldset class="space-y-5 border-t border-zinc-200 pt-6">
		<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Media details', 'reci-media-hub' ); ?></legend>
		<?php foreach ( $type_fields as $field ) : ?>
			<?php
			$field_key   = (string) $field['key'];
			$field_value = (string) get_post_meta( $post_id, $field_key, true );
			$field_id    = 'reci-edit-' . sanitize_html_class( ltrim( $field_key, '_' ) );
			$input_type  = 'number' === $field['type'] ? 'number' : ( 'url' === $field['type'] ? 'url' : 'text' );
			?>
			<div>
				<label class="mb-2 block text-sm font-medium text-zinc-800" for="<?php echo esc_attr( $field_id ); ?>">
					<?php echo esc_html( (string) $field['label'] ); ?>
					<?php if ( ! empty( $field['required'] ) ) : ?><span class="text-red-600">*</span><?php endif; ?>
				</label>
				<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_key ); ?>" type="<?php echo esc_attr( $input_type ); ?>"
				       value="<?php echo esc_attr( $field_value ); ?>" placeholder="<?php echo esc_attr( (string) ( $field['placeholder'] ?? '' ) ); ?>"
				       class="<?php echo esc_attr( $input_classes ); ?>" <?php echo ! empty( $field['required'] ) ? 'required' : ''; ?> />
				<?php if ( ! empty( $field['help'] ) ) : ?>
					<p class="mt-2 text-xs text-zinc-500"><?php echo esc_html( (string) $field['help'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</fieldset>
	<?php endif; ?>

	<?php foreach ( reci_media_hub_submission_taxonomies() as $taxonomy ) : ?>
		<?php
		$tax_object = get_taxonomy( $taxonomy );
		if ( ! $tax_object ) {
			continue;
		}

		$terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}

		$selected = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
		$selected = is_wp_error( $selected ) ? [] : array_map( 'intval', $selected );
		?>
		<fieldset class="space-y-3 border-t border-zinc-200 pt-6">
			<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php echo esc_html( $tax_object->labels->name ); ?></legend>
			<div class="grid gap-2 sm:grid-cols-2">
				<?php foreach ( $terms as $term ) : ?>
					<label class="flex items-start gap-2 text-sm text-zinc-700">
						<input type="checkbox" name="<?php echo esc_attr( $taxonomy ); ?>_terms[]" value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php checked( in_array( (int) $term->term_id, $selected, true ) ); ?> class="mt-1 rounded border-zinc-300 text-amber-600 focus:ring-amber-500" />
						<span><?php echo esc_html( $term->name ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	<?php endforeach; ?>

	<?php
	// Level 3 and above publish their own work; level 2's edits queue for review.
	$can_publish = current_user_can( 'publish_posts' );
	?>
	<?php
	// A level 2 draft is work in progress: saving keeps it private, and a
	// separate action hands it to staff. Anything already published skips this,
	// since saving it already sends it back for review.
	$is_draft_in_progress = ! $can_publish && ! $is_published && 'draft' === $edit_post->post_status;
	?>
	<div class="flex flex-wrap items-center gap-4 border-t border-zinc-200 pt-6">
		<button type="submit" class="btn btn-primary btn-md">
			<?php
			if ( $is_draft_in_progress ) {
				esc_html_e( 'Save draft', 'reci-media-hub' );
			} elseif ( $is_published || $can_publish ) {
				esc_html_e( 'Save changes', 'reci-media-hub' );
			} else {
				esc_html_e( 'Save and resubmit for review', 'reci-media-hub' );
			}
			?>
		</button>
		<?php if ( $is_draft_in_progress ) : ?>
			<button type="submit" name="submission_submit" value="1" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-800 transition-colors hover:bg-amber-50">
				<?php esc_html_e( 'Submit for review', 'reci-media-hub' ); ?>
			</button>
		<?php endif; ?>
		<?php if ( $can_publish && ! $is_published ) : ?>
			<button type="submit" name="submission_publish" value="1" class="rounded-lg border border-green-300 px-4 py-2 text-sm font-medium text-green-800 transition-colors hover:bg-green-50">
				<?php esc_html_e( 'Publish now', 'reci-media-hub' ); ?>
			</button>
		<?php endif; ?>
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="text-sm font-medium text-zinc-600 hover:text-zinc-900"><?php esc_html_e( 'Preview', 'reci-media-hub' ); ?></a>
	</div>
</form>

<?php if ( $can_trash ) : ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-6 max-w-4xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm"
	      onsubmit="return confirm('<?php echo esc_js( __( 'Move this submission to the trash? Staff can restore it.', 'reci-media-hub' ) ); ?>');">
		<input type="hidden" name="action" value="reci_trash_content" />
		<input type="hidden" name="post_id" value="<?php echo esc_attr( (string) $post_id ); ?>" />
		<?php wp_nonce_field( 'reci_trash_content_' . $post_id, 'reci_trash_nonce' ); ?>

		<h2 class="text-sm font-semibold text-zinc-800"><?php esc_html_e( 'Delete this submission', 'reci-media-hub' ); ?></h2>
		<p class="mt-1 text-sm text-zinc-500"><?php esc_html_e( 'It moves to the trash, where staff can restore it. Published work cannot be deleted here.', 'reci-media-hub' ); ?></p>
		<button type="submit" class="mt-4 rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50"><?php esc_html_e( 'Move to Trash', 'reci-media-hub' ); ?></button>
	</form>
<?php endif; ?>
