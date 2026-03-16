<?php
/**
 * Reflection response block.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'id' => '',
		'eyebrow' => '',
		'title' => '',
		'intro' => '',
		'cards' => [],
		'prompt' => '',
	]
);
?>
<section class="px-5 py-14 sm:px-6 lg:px-10 lg:py-24" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto w-full max-w-[1260px]">
		<div class="grid gap-4">
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
			<h2 class="font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4rem]"><?php echo esc_html($args['title']); ?></h2>
			<p class="max-w-[74rem] text-base leading-8 text-[var(--reflection-soft-text)] sm:text-[1.05rem] sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<?php if (! empty($args['cards'])) : ?>
			<div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
				<?php foreach ($args['cards'] as $card) : ?>
					<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6"><h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($card['title']); ?></h3><p class="text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo esc_html($card['body']); ?></p></article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
			<div class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
				<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]">Save your reflection</h3>
				<p class="max-w-[74rem] text-base leading-8 text-[var(--reflection-soft-text)]">Prompt: <?php echo esc_html($args['prompt']); ?></p>
				<div id="responseGate" class="mt-4 hidden rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]">You must be logged in to save reflections. Once logged in, your responses will be attached to your account and shown here.</div>
				<div id="responseFormShell" class="mt-4">
					<label class="mb-2 block font-['Oswald'] text-sm uppercase tracking-[0.08em] text-[var(--reflection-accent)]" for="reflectionResponse">Your response</label>
					<textarea id="reflectionResponse" class="min-h-[220px] w-full rounded-[18px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-4 text-[var(--reflection-text)] outline-none" placeholder="Write your response here..."></textarea>
					<div class="mt-4 flex flex-wrap gap-4">
						<button class="inline-flex items-center justify-center rounded-full bg-[#d4a63f] px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] text-[var(--reflection-accent-contrast)]" type="button" id="saveResponseBtn">Save reflection</button>
					</div>
					<div id="responseStatus" class="mt-4 hidden rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]"></div>
				</div>
			</div>
			<div class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
				<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]">Your saved responses</h3>
				<p class="max-w-[74rem] text-base leading-8 text-[var(--reflection-soft-text)]">These responses are tied to your account and this reflection.</p>
				<div id="responseList" class="mt-4 grid gap-4"></div>
			</div>
		</div>
	</div>
</section>
