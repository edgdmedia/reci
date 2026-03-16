<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'chain-break',
	'text' => '',
	'instruction' => 'Drag Down to Break',
]);
?>
<section class="min-h-screen px-5 py-16 text-center text-[var(--reflection-text)]" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto flex min-h-[70vh] max-w-[720px] flex-col items-center justify-center">
		<p class="max-w-[620px] text-2xl leading-10"><?php echo esc_html($args['text']); ?></p>
		<div class="relative mt-12 h-[400px] w-[100px]" id="chainInteract">
			<div class="absolute left-[10px] top-0 h-[120px] w-[80px] rounded-[40px] border-[15px] border-[var(--reflection-metal)] shadow-[inset_0_0_20px_black,0_10px_20px_rgba(0,0,0,0.5)]"></div>
			<div class="absolute left-[10px] top-[80px] h-[120px] w-[80px] rounded-[40px] border-[15px] border-[var(--reflection-metal)] shadow-[inset_0_0_20px_black,0_10px_20px_rgba(0,0,0,0.5)] transition-transform" id="breakableLink"></div>
		</div>
		<p id="chainInstruction" class="mt-5 font-['Oswald'] text-sm uppercase tracking-[0.18em] text-[var(--reflection-accent)]"><?php echo esc_html($args['instruction']); ?></p>
	</div>
</section>
