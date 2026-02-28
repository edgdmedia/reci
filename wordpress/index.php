<?php
/**
 * Fallback template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>
<main class="reci-wrap" style="padding-top:40px;padding-bottom:40px;">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom:40px;">
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e('No content found.', 'reci-media-hub'); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();

