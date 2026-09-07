<?php
/**
 * Template Name: Dashboard — Overview
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$is_author    = function_exists( 'reci_user_is_collaborator' ) && reci_user_is_collaborator( $current_user->ID );

// Dashboard home is overview-only — the full feed lives at /dashboard/feed/.
$personalized_posts = reci_get_personalized_dashboard_posts( $current_user->ID, 3 );
$recent_notifications = function_exists( 'reci_get_user_notifications' )
	? reci_get_user_notifications( $current_user->ID, 5, false )
	: [];

$bookmark_ids    = reci_get_user_bookmarks( $current_user->ID );
$recent_bookmarks = ! empty( $bookmark_ids )
	? get_posts( [ 'post__in' => array_column( $bookmark_ids, 'post_id' ), 'posts_per_page' => 5, 'post_type' => 'any' ] )
	: [];

global $wpdb;
$table_name = $wpdb->prefix . 'reci_journals';
$recent_journals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 3", $current_user->ID ) );

$recent_comments = get_comments( [ 'user_id' => $current_user->ID, 'number' => 5 ] );

$pending_count = $is_author
	? count( get_posts( [
		'author'         => $current_user->ID,
		'post_type'      => [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection', 'reci_course', 'reci_document' ],
		'post_status'    => 'pending',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	] ) )
	: 0;

get_header('dashboard');
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>

		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[ 'title' => sprintf( 'Welcome back, %s', $current_user->display_name ), 'subtitle' => 'A quick overview of your RECI activity. Your feed has the full reading list.' ]
			);
			?>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<?php if ( $is_author && $pending_count > 0 ) : ?>
				<div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm col-span-full">
					<p class="text-amber-800 font-medium">
						You have <?php echo $pending_count; ?> pending submission<?php echo $pending_count !== 1 ? 's' : ''; ?>.
						<a href="<?php echo esc_url( home_url( '/dashboard/my-content/' ) ); ?>" class="underline underline-offset-2 text-amber-900 hover:text-amber-700">Review</a>
					</p>
				</div>
				<?php endif; ?>

				<div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm col-span-full">
					<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
						<h2 class="text-neutral-800 text-xl font-bold font-subhead leading-7">Your Feed</h2>
						<a href="<?php echo esc_url( home_url( '/dashboard/feed/' ) ); ?>" class="text-sm font-semibold text-amber-700 hover:text-amber-800">Open your feed &rarr;</a>
					</div>
					<?php if ( ! empty( $personalized_posts ) ) : ?>
					<ul class="space-y-3">
						<?php foreach ( $personalized_posts as $post ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="text-sm font-medium text-zinc-700 hover:text-amber-700 transition-colors">
								<?php echo esc_html( get_the_title( $post ) ); ?>
							</a>
							<p class="text-xs text-zinc-500"><?php echo esc_html( get_post_type_object( get_post_type( $post ) )->labels->singular_name ?? get_post_type( $post ) ); ?></p>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
					<p class="text-sm text-zinc-500">Choose interests and collaborators in <a href="<?php echo esc_url( home_url( '/dashboard/settings/' ) ); ?>" class="text-amber-700 underline">Settings</a> to build your private RECI feed.</p>
					<?php endif; ?>
				</div>

				<div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-neutral-800 text-xl font-bold font-subhead leading-7">Notifications</h2>
						<a href="<?php echo esc_url( home_url( '/dashboard/notifications/' ) ); ?>" class="text-sm font-semibold text-amber-700 hover:text-amber-800">View all</a>
					</div>
					<?php get_template_part( 'template-parts/dashboard/notifications-list', null, [ 'items' => $recent_notifications, 'empty_message' => 'No notifications yet.' ] ); ?>
				</div>

				<div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-neutral-800 text-xl font-bold font-subhead leading-7">Recent Bookmarks</h2>
						<a href="<?php echo esc_url( home_url( '/dashboard/bookmarks/' ) ); ?>" class="text-sm font-semibold text-amber-700 hover:text-amber-800">View all</a>
					</div>
					<?php if ( ! empty( $recent_bookmarks ) ) : ?>
					<ul class="space-y-3">
						<?php foreach ( $recent_bookmarks as $post ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="text-sm font-medium text-zinc-700 hover:text-amber-700 transition-colors">
								<?php echo esc_html( get_the_title( $post ) ); ?>
							</a>
							<p class="text-xs text-zinc-500"><?php echo esc_html( get_post_type_object( get_post_type( $post ) )->labels->singular_name ?? get_post_type( $post ) ); ?></p>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
					<p class="text-sm text-zinc-500">No bookmarks yet. Browse content and click the bookmark icon to save posts.</p>
					<?php endif; ?>
				</div>

				<div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-neutral-800 text-xl font-bold font-subhead leading-7">Recent Journal</h2>
						<a href="<?php echo esc_url( home_url( '/dashboard/journal/' ) ); ?>" class="text-sm font-semibold text-amber-700 hover:text-amber-800">View all</a>
					</div>
					<?php if ( ! empty( $recent_journals ) ) : ?>
					<ul class="space-y-3">
						<?php foreach ( $recent_journals as $entry ) : ?>
						<li>
							<p class="text-sm font-medium text-zinc-700"><?php echo esc_html( wp_trim_words( $entry->prompt, 10, '...' ) ); ?></p>
							<p class="text-xs text-zinc-500"><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $entry->created_at ) ) ); ?></p>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
					<p class="text-sm text-zinc-500">No journal entries yet. Write a reflection in any reflection gallery to create one.</p>
					<?php endif; ?>
				</div>

				<div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-neutral-800 text-xl font-bold font-subhead leading-7">Recent Comments</h2>
						<a href="<?php echo esc_url( home_url( '/dashboard/comments/' ) ); ?>" class="text-sm font-semibold text-amber-700 hover:text-amber-800">View all</a>
					</div>
					<?php if ( ! empty( $recent_comments ) ) : ?>
					<ul class="space-y-3">
						<?php foreach ( $recent_comments as $comment ) : ?>
						<li>
							<p class="text-sm text-zinc-700 line-clamp-2"><?php echo esc_html( $comment->comment_content ); ?></p>
							<p class="text-xs text-zinc-500 mt-1">
								on <a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>" class="text-amber-600 hover:text-amber-700">
									<?php echo esc_html( get_the_title( $comment->comment_post_ID ) ); ?>
								</a>
							</p>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
					<p class="text-sm text-zinc-500">No comments yet.</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
