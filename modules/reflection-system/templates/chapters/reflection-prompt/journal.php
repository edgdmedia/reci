<?php
/**
 * Reflection chapter prompt variant: journal.
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
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="reci-stage-grid lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)] lg:items-start">
				<div class="flex max-h-[70vh] flex-col rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<div class="reci-scroll-panel !justify-start">
						<div class="font-['Oswald'] text-xs uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
						<h2 class="mt-3 font-['Playfair_Display'] text-3xl font-semibold leading-tight reci-reflection-text sm:text-4xl"><?php echo esc_html($args['title']); ?></h2>
						<p class="mt-4 text-sm leading-7 reci-reflection-soft-text sm:text-base sm:leading-8"><?php echo esc_html($args['intro']); ?></p>
						<div class="mt-6 grid gap-4 md:grid-cols-2">
							<?php foreach ((array) $args['cards'] as $card) : ?>
								<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)] p-5"><h3 class="mb-2 font-['Playfair_Display'] text-xl font-semibold reci-reflection-text"><?php echo esc_html($card['title']); ?></h3><p class="text-sm leading-7 reci-reflection-soft-text"><?php echo esc_html($card['body']); ?></p></article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="reci-scroll-panel !justify-start rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<h3 class="mb-2 font-['Playfair_Display'] text-2xl font-semibold reci-reflection-text">Save your reflection</h3>
					<p class="text-sm leading-7 reci-reflection-soft-text">Prompt: <?php echo esc_html($args['prompt']); ?></p>
					<div id="responseGate" class="mt-4 hidden rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-xs reci-reflection-soft-text">You must be logged in to submit reflections. Once logged in, your responses will be attached to your account and shown here.</div>
					<div id="responseFormShell" class="mt-4">
						<label class="mb-2 block font-['Oswald'] text-xs uppercase tracking-[0.08em] reci-reflection-accent" for="reflectionResponse">Your response</label>
						<textarea id="reflectionResponse" class="min-h-[180px] w-full rounded-[18px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-4 text-sm reci-reflection-text outline-none" placeholder="Write your response here..."></textarea>
						<div class="mt-4 flex flex-wrap gap-4">
							<button class="inline-flex items-center justify-center rounded-full bg-[var(--reflection-accent)] px-5 py-3 font-['Oswald'] text-xs uppercase tracking-[0.1em] text-[var(--reflection-accent-contrast)]" type="button" id="saveResponseBtn">Save reflection</button>
						</div>
						<div id="responseStatus" class="mt-4 hidden rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-xs reci-reflection-soft-text"></div>
					</div>
					<div class="mt-8">
						<h3 class="mb-2 font-['Playfair_Display'] text-2xl font-semibold reci-reflection-text">Your saved responses</h3>
						<p class="text-sm leading-7 reci-reflection-soft-text">These responses are tied to your account and this reflection.</p>
						<div id="responseList" class="mt-4 grid gap-4"></div>
					</div>
					<div class="mt-6 flex flex-wrap gap-4">
						<?php if (($args['transition_mode'] ?? 'button') === 'button' && !empty($args['continue_target']) && $args['continue_target'] !== '#') : ?>
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
