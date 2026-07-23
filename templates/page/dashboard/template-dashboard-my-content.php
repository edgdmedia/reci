<?php
/**
 * Template Name: Dashboard — My Content
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user  = wp_get_current_user();
$paged         = max( 1, absint( get_query_var( 'paged', 1 ) ) );
$filter_type   = sanitize_text_field( $_GET['type'] ?? '' );
$filter_status = sanitize_text_field( $_GET['status'] ?? '' );
$search        = sanitize_text_field( $_GET['s'] ?? '' );

$args = [
	'author'         => $current_user->ID,
	'post_type'      => [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_quote', 'reci_course', 'reci_testimonial', 'reci_glossary_term' ],
	'post_status'    => [ 'publish', 'pending', 'draft' ],
	'posts_per_page' => 20,
	'paged'          => $paged,
];

if ( $filter_type && in_array( $filter_type, (array) $args['post_type'], true ) ) {
	$args['post_type'] = $filter_type;
}
if ( $filter_status && in_array( $filter_status, [ 'publish', 'pending', 'draft' ], true ) ) {
	$args['post_status'] = $filter_status;
}
if ( $search ) {
	$args['s'] = $search;
}

$content_query = new WP_Query( $args );

get_header('dashboard');
?>
<main class="layout-page">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<div class="flex items-center justify-between mb-8">
				<h1 class="text-2xl font-bold font-heading text-zinc-800">My Content</h1>
				<a href="<?php echo esc_url( home_url( '/dashboard/submit/' ) ); ?>" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
					+ Submit New
				</a>
			</div>

			<form method="get" class="flex flex-wrap gap-3 mb-6">
				<select name="type" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm">
					<option value="">All Types</option>
					<?php foreach ( [ 'post' => 'Articles', 'reci_podcast' => 'Podcasts', 'reci_video' => 'Videos', 'reci_event' => 'Events', 'reci_quote' => 'Quotes', 'reci_course' => 'Courses', 'reci_testimonial' => 'Testimonials', 'reci_glossary_term' => 'Glossary' ] as $val => $label ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $filter_type, $val ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<select name="status" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm">
					<option value="">All Statuses</option>
					<option value="publish" <?php selected( $filter_status, 'publish' ); ?>>Published</option>
					<option value="pending" <?php selected( $filter_status, 'pending' ); ?>>Pending Review</option>
					<option value="draft" <?php selected( $filter_status, 'draft' ); ?>>Draft</option>
				</select>
				<input type="search" name="s" placeholder="Search titles..." value="<?php echo esc_attr( $search ); ?>" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm flex-1 min-w-[200px]">
				<button type="submit" class="px-4 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-sm font-medium rounded-lg transition-colors">Filter</button>
			</form>

			<?php if ( ! $content_query->have_posts() ) : ?>
			<p class="text-zinc-500">No content found. Submit your first piece to get started.</p>
			<?php else : ?>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead>
						<tr class="border-b border-zinc-200 text-left text-zinc-500 text-xs uppercase tracking-wider">
							<th class="pb-3 pr-4 font-medium">Title</th>
							<th class="pb-3 pr-4 font-medium">Type</th>
							<th class="pb-3 pr-4 font-medium">Status</th>
							<th class="pb-3 pr-4 font-medium">Date</th>
							<th class="pb-3 font-medium">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php while ( $content_query->have_posts() ) : $content_query->the_post(); ?>
						<tr class="border-b border-zinc-100 hover:bg-zinc-50">
							<td class="py-3 pr-4">
								<a href="<?php echo esc_url( get_edit_post_link() ?: get_permalink() ); ?>" class="font-medium text-zinc-800 hover:text-amber-700">
									<?php echo esc_html( get_the_title() ?: '(untitled)' ); ?>
								</a>
							</td>
							<td class="py-3 pr-4 text-zinc-600"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ?? get_post_type() ); ?></td>
							<td class="py-3 pr-4">
								<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
									<?php echo get_post_status() === 'publish' ? 'bg-green-100 text-green-700' : ( get_post_status() === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-600' ); ?>">
									<?php echo get_post_status() === 'publish' ? 'Published' : ( get_post_status() === 'pending' ? 'Pending' : 'Draft' ); ?>
								</span>
							</td>
							<td class="py-3 pr-4 text-zinc-500"><?php echo esc_html( get_the_modified_date() ); ?></td>
							<td class="py-3">
								<a href="<?php echo esc_url( get_edit_post_link() ?: get_permalink() ); ?>" class="text-amber-600 hover:text-amber-700 text-xs font-medium"><?php echo get_edit_post_link() ? 'Edit' : 'View'; ?></a>
							</td>
						</tr>
						<?php endwhile; wp_reset_postdata(); ?>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
