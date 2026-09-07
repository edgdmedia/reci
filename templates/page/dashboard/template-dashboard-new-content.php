<?php
/**
 * Template Name: Dashboard — New Content
 *
 * Level 3 and above add content here. Picking a type creates the draft and hands
 * off to the existing editor, so there is one editing surface rather than a
 * create form and an edit form drifting apart.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Level 2 submits through /submit/ for review; this route is for people who
// publish their own work.
if ( ! current_user_can( 'publish_posts' ) ) {
	wp_safe_redirect( home_url( '/submit/' ) );
	exit;
}

$types = function_exists( 'reci_media_hub_submission_type_definitions' )
	? reci_media_hub_submission_type_definitions()
	: [];

get_header( 'dashboard' );
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>

		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[
					'title'    => 'Add Content',
					'subtitle' => 'Choose a format to start. You can publish it yourself when it is ready.',
					'action'   => '<a href="' . esc_url( home_url( '/dashboard/my-content/' ) ) . '" class="text-sm font-semibold text-amber-700 hover:text-amber-800">&larr; Back to My Content</a>',
				]
			);
			?>

			<?php if ( isset( $_GET['create_error'] ) ) : ?>
				<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800" role="alert">
					<?php esc_html_e( 'We could not start that draft. Please try again.', 'reci-media-hub' ); ?>
				</div>
			<?php endif; ?>

			<div class="grid max-w-4xl gap-4 sm:grid-cols-2">
				<?php foreach ( $types as $type ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="reci_create_content" />
						<input type="hidden" name="content_type" value="<?php echo esc_attr( (string) $type['id'] ); ?>" />
						<?php wp_nonce_field( 'reci_create_content', 'reci_create_nonce' ); ?>
						<button type="submit" class="h-full w-full rounded-2xl border border-zinc-200 bg-white p-5 text-left shadow-sm transition-colors hover:border-amber-400 hover:bg-amber-50">
							<span class="text-2xl" aria-hidden="true"><?php echo esc_html( (string) ( $type['icon'] ?? '' ) ); ?></span>
							<span class="mt-2 block font-subhead text-base font-bold text-neutral-800"><?php echo esc_html( (string) $type['label'] ); ?></span>
							<span class="mt-1 block text-sm leading-6 text-zinc-500"><?php echo esc_html( wp_trim_words( (string) ( $type['desc'] ?? '' ), 18 ) ); ?></span>
						</button>
					</form>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
