<?php
/**
 * Voices of Resistance horizontal march stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-march',
	'items' => [],
	'continue_label' => 'Reflect',
]);
$items = (array) $args['items'];
?>
<section class="chapter-march" id="<?php echo esc_attr($args['id']); ?>">
	<div class="march-track" id="marchTrack">
		<?php foreach ($items as $index => $item) : ?>
			<div class="march-panel">
				<?php if (! empty($item['background_image'])) : ?>
					<div class="mp-bg" style="background-image:url('<?php echo esc_url($item['background_image']); ?>')"></div>
				<?php endif; ?>
				<div class="mp-content">
					<?php if (! empty($item['accent_title'])) : ?>
						<h2 class="mp-title mp-title--accent"><?php echo esc_html($item['accent_title']); ?></h2>
					<?php else : ?>
						<h2 class="mp-title"><?php echo esc_html((string) ($item['title'] ?? '')); ?></h2>
					<?php endif; ?>
					<?php if (! empty($item['body'])) : ?>
						<p class="mp-body"><?php echo esc_html($item['body']); ?></p>
					<?php endif; ?>
					<?php if ($index === count($items) - 1) : ?>
						<br>
						<button class="enter-btn" id="vorReflectBtn" type="button" data-stage-target="reflect"><?php echo esc_html($args['continue_label']); ?></button>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
