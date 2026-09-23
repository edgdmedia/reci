<?php
/**
 * Template Name: Dashboard — Journal
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$paged        = max( 1, absint( get_query_var( 'paged', 1 ) ) );
$per_page     = 20;
$offset       = ( $paged - 1 ) * $per_page;

global $wpdb;
$table_name = $wpdb->prefix . 'reci_journals';

$total_journals = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name WHERE user_id = %d", $current_user->ID ) );
$max_num_pages  = ceil( $total_journals / $per_page );

$journals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d", $current_user->ID, $per_page, $offset ) );

get_header('dashboard');
?>
<main class="layout-page">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<h1 class="text-2xl font-bold font-heading text-zinc-800 mb-8">Journal</h1>

			<?php if ( empty( $journals ) ) : ?>
			<p class="text-zinc-500">No journal entries yet. Write a reflection in any reflection gallery to create one.</p>
			<?php else : ?>
			<div class="space-y-4">
				<?php foreach ( $journals as $journal ) :
					$reflection_id = (int) $journal->reflection_id;
					$prompt        = $journal->prompt;
					$shared        = (bool) $journal->is_shared;
					$content       = $journal->response;
					$date          = wp_date( get_option( 'date_format' ), strtotime( $journal->created_at ) );
				?>
				<div class="bg-white border border-zinc-200 rounded-xl p-5">
					<div class="flex items-start justify-between gap-4">
						<div class="min-w-0 flex-1">
							<p class="text-xs text-zinc-500 mb-1">
								<?php if ( $reflection_id && get_post( $reflection_id ) ) : ?>
								From: <a href="<?php echo esc_url( get_permalink( $reflection_id ) ); ?>" class="text-amber-600 hover:text-amber-700"><?php echo esc_html( get_the_title( $reflection_id ) ); ?></a>
								<?php elseif ( $reflection_id ) : ?>
								From: [Reflection removed]
								<?php endif; ?>
							</p>
							<?php if ( $prompt ) : ?>
							<p class="text-sm font-medium text-zinc-700 italic mb-2">"<?php echo esc_html( $prompt ); ?>"</p>
							<?php endif; ?>
							<p class="text-sm text-zinc-600 line-clamp-3"><?php echo esc_html( wp_strip_all_tags( $content ) ); ?></p>
						</div>
						<div class="flex flex-col items-end gap-2 shrink-0">
							<span class="text-xs px-2 py-0.5 rounded-full <?php echo $shared ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600'; ?>">
								<?php echo $shared ? 'Shared' : 'Private'; ?>
							</span>
							<span class="text-xs text-zinc-400"><?php echo esc_html( $date ); ?></span>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $max_num_pages > 1 ) : ?>
			<div class="mt-8 flex justify-center gap-2">
				<?php
				echo paginate_links( [
					'total'     => $max_num_pages,
					'current'   => $paged,
					'type'      => 'list',
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
				] );
				?>
			</div>
			<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
