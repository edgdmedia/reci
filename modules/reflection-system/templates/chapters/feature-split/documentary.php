<?php

/**
 * Reflection chapter feature split variant: documentary.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'feature-split',
	'eyebrow' => '',
	'title' => '',
	'body' => '',
	'image' => '',
	'image_alt' => '',
	'caption' => '',
	'note' => '',
	'media_side' => 'left',
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
$media_first = ($args['media_side'] ?? 'left') !== 'right';
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] lg:items-center">
				<?php if ($media_first) : ?>
					<div class="overflow-hidden rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] shadow-[0_24px_60px_rgba(0,0,0,0.18)]">
						<img class="block h-[320px] w-full object-cover sm:h-[420px] lg:h-[72vh]" src="<?php echo esc_url($args['image']); ?>" alt="<?php echo esc_attr($args['image_alt']); ?>">
						<?php if ($args['caption']) : ?><div class="px-5 py-4 text-xs leading-7 reci-reflection-muted"><?php echo esc_html($args['caption']); ?></div><?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="max-h-[70vh] overflow-y-auto rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6 sm:p-8 lg:p-10 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[color:var(--reflection-border)]">
					<?php if ($args['eyebrow']) : ?><div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div><?php endif; ?>
					<?php if ($args['title']) : ?><h2 class="mt-3 font-['Playfair_Display'] text-3xl/snug font-semibold  reci-reflection-text sm:text-4xl/snug lg:text-[3.25rem]/snug"><?php echo esc_html($args['title']); ?></h2><?php endif; ?>
					<p class="mt-5 text-base leading-8 reci-reflection-soft-text sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
					<?php if ($args['note']) : ?><div class="mt-6 rounded-[1.25rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-panel)] px-5 py-4 text-sm leading-7 reci-reflection-soft-text"><?php echo esc_html($args['note']); ?></div><?php endif; ?>
					<div class="mt-8 flex flex-wrap gap-4">
						<?php if (!empty($args['actions']) && is_array($args['actions'])) : foreach ($args['actions'] as $action) : ?>
							<?php $tag = !empty($action['href']) && $action['href'] !== '#' ? 'a' : 'button'; ?>
							<<?php echo $tag; ?> <?php if ($tag === 'a') echo 'href="' . esc_url($action['href']) . '"'; else echo 'type="button"'; ?> class="reci-continue <?php echo esc_attr($action['class'] ?? ''); ?>" <?php foreach ($action['attributes'] ?? [] as $k => $v) { echo esc_attr($k) . '="' . esc_attr($v) . '" '; } ?>><?php echo esc_html($action['label']); ?></<?php echo $tag; ?>>
						<?php endforeach; elseif (($args['transition_mode'] ?? 'button') === 'button') : ?>
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
						<?php endif; ?>
					</div>
				</div>
				<?php if (! $media_first) : ?>
					<div class="overflow-hidden rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] shadow-[0_24px_60px_rgba(0,0,0,0.18)]">
						<img class="block h-[320px] w-full object-cover sm:h-[420px] lg:h-[72vh]" src="<?php echo esc_url($args['image']); ?>" alt="<?php echo esc_attr($args['image_alt']); ?>">
						<?php if ($args['caption']) : ?><div class="px-5 py-4 text-sm leading-7 reci-reflection-muted"><?php echo esc_html($args['caption']); ?></div><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>