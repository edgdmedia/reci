<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'word-shift',
	'title' => '',
	'html' => '',
]);
?>
<section class="min-h-screen px-5 py-16 text-center text-[var(--reflection-text)]" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto flex min-h-[70vh] max-w-[760px] flex-col items-center justify-center">
		<h2 class="font-['Cinzel'] text-4xl uppercase tracking-[0.18em] text-[var(--reflection-text)] sm:text-5xl"><?php echo esc_html($args['title']); ?></h2>
		<div class="mt-8 text-2xl leading-10"><?php echo wp_kses_post($args['html']); ?></div>
	</div>
</section>
