<?php
/**
 * Reflection chapter quote variant: cinematic.
 *
 * A dramatic, full-viewport variant with a dark vignette and large, oversized typography.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id'               => 'quote-cinematic',
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
	class="reci-stage min-h-screen relative overflow-hidden bg-black text-white"
	id="<?php echo esc_attr($args['id']); ?>"
	data-stage="<?php echo esc_attr($args['id']); ?>"
	data-transition-mode="<?php echo esc_attr($transition_mode); ?>"
	data-continue-target="<?php echo esc_attr($continue_target); ?>"
>
	<?php if ($bg !== '') : ?>
		<div class="absolute inset-0 z-0">
			<img src="<?php echo esc_url($bg); ?>" class="w-full h-full object-cover scale-105 transform opacity-60" alt="">
			<div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-black/20"></div>
			<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-transparent via-black/40 to-black"></div>
		</div>
	<?php endif; ?>
	
	<div class="relative z-10 min-h-screen w-full flex flex-col p-8 md:p-16 lg:p-24 <?php echo $justify_class; ?>">
		<div class="max-w-[1200px] w-full mx-auto flex flex-col <?php echo $text_align_class; ?>">
			
			<?php if ($args['eyebrow'] !== '') : ?>
				<div class="mb-6 font-['Oswald'] text-xs md:text-sm uppercase tracking-[0.3em] text-[var(--reflection-accent,#ff4444)]">
					<?php echo esc_html($args['eyebrow']); ?>
				</div>
			<?php endif; ?>
			
			<div class="relative w-full">
				<?php 
					$text_size_pref = $args['text_size'] ?? 'normal';
					if ($text_size_pref === 'large') {
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(3rem, 8vw, 6rem); line-height: 1.0;";
					} elseif ($text_size_pref === 'small') {
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(1.5rem, 4vw, 2.5rem); line-height: 1.4;";
					} else {
						// Normal
						$dynamic_style = "text-align: {$h_align}; font-size: clamp(2rem, 5vw, 4rem); line-height: 1.1;";
					}
				?>
				<blockquote class="font-['Oswald'] uppercase tracking-tight text-white mix-blend-screen drop-shadow-2xl" style="<?php echo esc_attr($dynamic_style); ?>">
					&ldquo;<?php echo wp_kses_post($quote); ?>&rdquo;
				</blockquote>
			</div>
			
			<?php if ($args['attribution'] !== '') : ?>
				<cite class="mt-8 block font-['Cinzel'] text-xl md:text-2xl text-white/80 tracking-widest">
					&mdash; <?php echo esc_html($args['attribution']); ?>
				</cite>
			<?php endif; ?>
			
			<?php if ($transition_mode === 'button' && $continue_target !== '') : ?>
				<div class="mt-20 w-full <?php echo $h_align === 'center' ? 'flex justify-center' : ''; ?>">
					<button type="button" class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md border border-white/30 px-12 py-5 font-['Oswald'] text-sm uppercase tracking-[0.2em] text-white no-underline transition-all hover:bg-white hover:text-black" data-stage-target="<?php echo esc_attr($continue_target); ?>">
						<?php echo esc_html($args['button_label']); ?>
					</button>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
</section>
