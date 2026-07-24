<?php
/**
 * Reflection chapter quote variant: default (light fallback).
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id'          => 'quote',
	'quote'       => '',
	'attribution' => '',
]);

$quote = is_array($args['quote'] ?? '') ? implode("\n\n", $args['quote']) : (string) ($args['quote'] ?? '');
?>
<section class="reci-stage chapter-quote chapter-quote--default" id="<?php echo esc_attr($args['id']); ?>">
	<div class="quote-inner">
		<blockquote class="quote-text"><?php echo esc_html($quote); ?></blockquote>
		<?php if ($args['attribution'] !== '') : ?>
			<cite class="quote-attribution">&mdash; <?php echo esc_html($args['attribution']); ?></cite>
		<?php endif; ?>
	</div>
</section>
