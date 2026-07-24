<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'word-shift',
	'title' => '',
	'html' => '',
]);
?>
<section class="reci-stage min-h-screen px-5 py-16 text-center reci-reflection-text" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<div class="mx-auto flex min-h-[70vh] max-w-[760px] flex-col items-center justify-center">
		<h2 class="font-['Cinzel'] text-4xl uppercase tracking-[0.18em] reci-reflection-text sm:text-5xl"><?php echo esc_html($args['title']); ?></h2>
		<div class="mt-8 text-2xl leading-10"><?php echo wp_kses_post($args['html']); ?></div>
		<?php if (! empty($args['continue_target']) && ! empty($args['continue_label']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
			<div class="mt-16">
				<button class="inline-flex items-center justify-center border border-white px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.18em] text-white no-underline transition-colors hover:bg-white hover:text-black" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>">
					<?php echo esc_html($args['continue_label']); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>
