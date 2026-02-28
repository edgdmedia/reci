<?php
/**
 * 404 template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>
<main class="reci-wrap" style="padding-top:60px;padding-bottom:60px;">
	<h1><?php esc_html_e('Page not found', 'reci-media-hub'); ?></h1>
	<p><?php esc_html_e('The page you are looking for does not exist.', 'reci-media-hub'); ?></p>
	<p><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to home', 'reci-media-hub'); ?></a></p>
</main>
<?php
get_footer();

