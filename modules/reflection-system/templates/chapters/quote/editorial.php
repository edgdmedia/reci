<?php
/**
 * Reflection chapter quote variant: editorial.
 *
 * A sleek, magazine-style layout with elegant serif typography and strong borders.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id'               => 'quote-editorial',
	'eyebrow'          => '',
	'quote'            => '',
	'attribution'      => '',
	'background_image' => '',
	'button_label'     => 'Continue',
	'continue_target'  => '#',
	'align_horizontal' => 'center',
	'align_vertical'   => 'center',
]);

$quote = is_array($args['quote'] ?? '') ? implode("\n\n", $args['quote']) : (string) ($args['quote'] ?? '');
$bg    = is_array($args['background_image'] ?? '')
	? ($args['background_image']['url'] ?? $args['background_image'][0] ?? '')
	: (string) ($args['background_image'] ?? '');
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim((string) ($args['continue_target'] ?? ''), '#');

$h_align = $args['align_horizontal'] ?? 'center';
$v_align = $args['align_vertical'] ?? 'center';

$text_align_class = $h_align === 'left' ? 'text-left items-start' : ($h_align === 'right' ? 'text-right items-end' : 'text-center items-center');
$justify_class = $v_align === 'top' ? 'justify-start' : ($v_align === 'bottom' ? 'justify-end' : 'justify-center');
?>
<section
	class="reci-stage min-h-screen relative overflow-hidden bg-[#faf8f5] text-[#222]"
	id="<?php echo esc_attr($args['id']); ?>"
	data-stage="<?php echo esc_attr($args['id']); ?>"
	data-transition-mode="<?php echo esc_attr($transition_mode); ?>"
	data-continue-target="<?php echo esc_attr($continue_target); ?>"
>
	<?php if ($bg !== '') : ?>
		<div class="absolute inset-0 z-0">
			<img src="<?php echo esc_url($bg); ?>" class="w-full h-full object-cover opacity-20 grayscale mix-blend-multiply" alt="">
		</div>
	<?php endif; ?>
	
	<div class="relative z-10 min-h-screen w-full flex flex-col p-8 md:p-16 <?php echo $justify_class; ?>">
		<div class="max-w-[900px] w-full mx-auto flex flex-col <?php echo $text_align_class; ?>">
			
			<?php if ($args['eyebrow'] !== '') : ?>
				<div class="mb-8 border-t-2 border-black pt-4 font-['Oswald'] text-sm uppercase tracking-[0.2em] font-semibold">
					<?php echo esc_html($args['eyebrow']); ?>
				</div>
			<?php endif; ?>
			
			<div class="relative w-full">
				<?php if ($h_align === 'left'): ?>
					<span class="absolute -left-8 top-[-0.5em] font-['Playfair_Display'] text-[6rem] leading-none text-black/10">&ldquo;</span>
				<?php endif; ?>
				<?php 
					$text_size_pref = $args['text_size'] ?? 'normal';
					if ($text_size_pref === 'large') {
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1.1;";
					} elseif ($text_size_pref === 'small') {
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(1.25rem, 3vw, 2rem); line-height: 1.5;";
					} else {
						// Normal
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(1.8rem, 4vw, 3rem); line-height: 1.3;";
					}
				?>
				<blockquote class="font-['Playfair_Display'] font-medium tracking-tight" style="<?php echo esc_attr($dynamic_style); ?>">
					<?php echo wp_kses_post($quote); ?>
				</blockquote>
			</div>
			
			<?php if ($args['attribution'] !== '') : ?>
				<cite class="mt-8 block font-['Merriweather'] text-lg md:text-xl italic text-black/70">
					&mdash; <?php echo esc_html($args['attribution']); ?>
				</cite>
			<?php endif; ?>
			
			<?php if ($transition_mode === 'button' && $continue_target !== '') : ?>
				<div class="mt-16 border-t border-black/10 pt-8 w-full <?php echo $h_align === 'center' ? 'flex justify-center' : ''; ?>">
					<button type="button" class="inline-flex items-center justify-center border border-black px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.18em] text-black no-underline transition-colors hover:bg-black hover:text-white" data-stage-target="<?php echo esc_attr($continue_target); ?>">
						<?php echo esc_html($args['button_label']); ?>
					</button>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
</section>
