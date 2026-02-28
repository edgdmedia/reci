<?php
/**
 * Theme footer wrapper for PHP templates.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<?php get_template_part('template-parts/site', 'footer'); ?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
