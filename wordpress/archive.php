<?php
/**
 * Archive template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>
<main class="reci-wrap" style="padding-top:40px;padding-bottom:40px;">
	<header style="margin-bottom:32px;">
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description('<div>', '</div>'); ?>
	</header>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom:32px;">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e('No posts found.', 'reci-media-hub'); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();

