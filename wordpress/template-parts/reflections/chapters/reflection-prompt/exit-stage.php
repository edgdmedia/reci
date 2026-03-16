<?php
/**
 * Reflection chapter prompt variant: exit stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'reflection',
	'eyebrow' => '',
	'title' => '',
	'intro' => '',
	'cards' => [],
	'prompt' => '',
	'continue_label' => 'Return',
	'continue_target' => 'top',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body justify-center">
			<div class="mx-auto flex w-full max-w-[1040px] flex-col items-center rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] px-6 py-10 text-center sm:px-8 lg:px-12 lg:py-14">
				<?php if ($args['eyebrow']) : ?>
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.14em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>
				<h2 class="mt-4 max-w-[16ch] font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4.5rem]"><?php echo esc_html($args['title']); ?></h2>
				<p class="mt-5 max-w-[44rem] text-base leading-8 text-[var(--reflection-soft-text)] sm:text-lg sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
				<?php if ($args['cards']) : ?>
					<div class="mt-8 grid w-full gap-4 md:grid-cols-2">
						<?php foreach ((array) $args['cards'] as $card) : ?>
							<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)] p-5 text-left">
								<h3 class="mb-3 font-['Playfair_Display'] text-2xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($card['title']); ?></h3>
								<p class="text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo esc_html($card['body']); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="mt-8 w-full max-w-[42rem] rounded-[24px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-6 text-left">
					<label class="mb-3 block font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]" for="reflectionResponse"><?php echo esc_html($args['prompt'] ?: 'Your reflection'); ?></label>
					<div id="responseGate" class="mb-4 hidden rounded-[18px] bg-[var(--reflection-card-strong)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]">You must be logged in to save reflections. Once logged in, your responses will be attached to your account and shown here.</div>
					<div id="responseFormShell">
						<textarea id="reflectionResponse" class="min-h-[180px] w-full rounded-[18px] border border-[color:var(--reflection-border)] bg-transparent p-4 text-[var(--reflection-text)] outline-none" placeholder="Write your response here..."></textarea>
						<div class="mt-4 flex flex-wrap gap-4">
							<button class="inline-flex items-center justify-center rounded-full bg-[var(--reflection-accent)] px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] text-[var(--reflection-accent-contrast)]" type="button" id="saveResponseBtn">Save reflection</button>
							<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
						</div>
						<div id="responseStatus" class="mt-4 hidden rounded-[18px] bg-[var(--reflection-card-strong)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
