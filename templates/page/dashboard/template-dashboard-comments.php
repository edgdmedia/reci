<?php
/**
 * Template Name: Dashboard — Comments
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_comments = get_comments( [
	'user_id' => get_current_user_id(),
	'number'  => 50,
	'orderby' => 'comment_date',
	'order'   => 'DESC',
] );

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
				[ 'title' => 'Comments', 'subtitle' => 'Your contributions to conversations across the hub.' ]
			);
			?>

			<?php if ( empty( $user_comments ) ) : ?>
			<p class="text-zinc-500">No comments yet. Start a conversation on any post or reflection.</p>
			<?php else : ?>
			<div class="space-y-4">
				<?php foreach ( $user_comments as $comment ) : ?>
				<div class="bg-white border border-zinc-200 rounded-xl p-5">
					<div class="flex items-start justify-between gap-4">
						<div class="min-w-0 flex-1">
							<p class="text-sm text-zinc-700 line-clamp-3"><?php echo esc_html( $comment->comment_content ); ?></p>
							<div class="flex items-center gap-3 mt-2">
								<a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>" class="text-xs text-amber-600 hover:text-amber-700 font-medium">
									<?php echo esc_html( get_the_title( $comment->comment_post_ID ) ); ?>
								</a>
								<span class="text-xs text-zinc-400"><?php echo esc_html( get_comment_date( 'M j, Y', $comment ) ); ?></span>
							</div>
						</div>
						<span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-medium <?php echo $comment->comment_approved === '1' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
							<?php echo $comment->comment_approved === '1' ? 'Approved' : 'Pending'; ?>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
