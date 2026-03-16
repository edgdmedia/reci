<?php

/**
 * Template Name: Reflection Gallery - Racial Disparities
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

function reci_rd_asset(string $path): string
{
	return trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/' . ltrim($path, '/');
}

$controller_path = get_template_directory() . '/assets/js/reflection-stage-controller.js';
$controller_uri  = get_template_directory_uri() . '/assets/js/reflection-stage-controller.js';
$controller_ver  = file_exists($controller_path) ? (string) filemtime($controller_path) : wp_get_theme()->get('Version');
$script_path = get_template_directory() . '/assets/js/reflection-racial-disparities.js';
$script_uri  = get_template_directory_uri() . '/assets/js/reflection-racial-disparities.js';
$script_ver  = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');
wp_enqueue_style('reci-reflection-rd-fonts', 'https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500&family=Space+Grotesk:wght@300;400;600;700&display=swap', [], null);
wp_register_style('reci-reflection-rd-inline', false, [], $script_ver);
wp_enqueue_style('reci-reflection-rd-inline');
wp_add_inline_style('reci-reflection-rd-inline', <<<'CSS'
:root {
	--reflection-bg: #f4f4f4;
	--reflection-surface: #ffffff;
	--reflection-surface-alt: #f4f4f4;
	--reflection-card: #ffffff;
	--reflection-card-strong: #ffffff;
	--reflection-text: #111111;
	--reflection-white: #ffffff;
	--reflection-muted: #555555;
	--reflection-soft-text: #333333;
	--reflection-accent: #2A4494;
	--reflection-accent-contrast: #111111;
	--reflection-alert: #D93025;
}
body { overflow-x: hidden; }
.rd-stage[hidden] { display: none !important; }
.rd-data-card.active .rd-card-detail { display: block; }
.rd-data-card.active .rd-card-title { color: var(--reflection-accent); }
CSS);
wp_enqueue_script('reci-reflection-stage-controller', $controller_uri, [], $controller_ver, true);
wp_enqueue_script('reci-reflection-rd', $script_uri, ['reci-reflection-stage-controller'], $script_ver, true);
$cards = [
	[
		'icon' => '💰',
		'eyebrow' => 'Economic Inequality',
		'stat' => '10x',
		'unit' => 'Wealth Gap',
		'summary' => 'Median white family wealth vs. Black family wealth.',
		'detail' => 'The racial wealth gap has widened in recent decades, reflecting centuries of discriminatory policies.',
		'toggle' => 'View Solution',
		'solution' => 'Reparative policies such as baby bonds, down payment assistance, and strict enforcement of fair lending laws are evidenced-based ways to close the gap.',
		'bars' => [['label' => 'White', 'width' => '100%'], ['label' => 'Black', 'width' => '10%', 'alert' => true]],
	],
	[
		'icon' => '⚖️',
		'eyebrow' => 'Criminal Justice',
		'stat' => '5x',
		'unit' => 'Incarceration Rate',
		'summary' => 'Black Americans are incarcerated at 5x the rate of whites.',
		'detail' => 'Despite similar rates of drug use, the justice system disproportionately targets communities of color.',
		'bars' => [['label' => 'White', 'width' => '20%'], ['label' => 'Black', 'width' => '100%', 'alert' => true]],
	],
	[
		'icon' => '🏥',
		'eyebrow' => 'Healthcare Access',
		'stat' => '40%',
		'unit' => 'Higher Mortality',
		'summary' => 'Maternal mortality rates for Black women vs. white women.',
		'detail' => 'These differences are not explained by genetics, but by systemic factors and bias in medical treatment.',
	],
	[
		'icon' => '🎓',
		'eyebrow' => 'Education',
		'stat' => '$23B',
		'unit' => 'Funding Gap',
		'summary' => 'Annual funding difference between white and non-white districts.',
		'detail' => 'Students of color are more likely to attend underfunded schools with fewer resources and advanced courses.',
	],
];
get_header('reflection'); ?>
<nav class="sticky top-0 z-50 flex items-center justify-between border-b border-black/10 bg-[color:rgba(244,244,244,0.9)] px-5 py-5 sm:px-6 lg:px-8">
	<div class="flex items-center gap-3">
		<a class="font-['Space_Grotesk'] text-sm font-semibold uppercase tracking-[0.12em] text-black no-underline" href="<?php echo esc_url( home_url('/reflections/') ); ?>">← Gallery Index</a>
		<button id="rdStageBack" class="hidden border-none bg-transparent font-['Space_Grotesk'] text-sm font-semibold uppercase tracking-[0.12em] text-black/60" type="button">← Back</button>
	</div>
	<div class="mini-player flex items-center gap-3 rounded-full border border-black/10 bg-white px-4 py-2">
		<button id="rdPlayBtn" onclick="reciToggleRacialAudio()" class="border-none bg-transparent text-sm" type="button">▶</button>
		<span class="font-['Space_Grotesk'] text-xs font-semibold uppercase tracking-[0.1em]">Full Analysis</span>
	</div>
</nav>
<audio id="rdAudio" src="<?php echo esc_url(reci_rd_asset('audio/racial-disparities.m4a')); ?>"></audio>
<section class="rd-stage min-h-[calc(100vh-81px)] px-5 py-16 sm:px-6 sm:py-20 lg:px-8" id="rd-hero" data-stage-id="rd-hero">
	<div class="mx-auto flex min-h-[calc(100vh-209px)] w-full max-w-[900px] flex-col items-center justify-center text-center">
		<div class="font-['Roboto_Mono'] text-sm uppercase tracking-[0.18em] text-[var(--reflection-accent)]">Data Reflection</div>
		<h1 class="mt-6 font-['Space_Grotesk'] text-5xl font-bold leading-none text-[var(--reflection-accent-contrast)] sm:text-6xl lg:text-7xl">The Data Gap</h1>
		<p class="mx-auto mt-6 max-w-[620px] text-lg leading-8 text-[var(--reflection-muted)] sm:text-xl">Racial disparities are not just numbers. They are structural realities that affect lives. Tap a domain to examine the evidence.</p>
		<div class="mt-10 flex flex-wrap items-center justify-center gap-4">
			<button id="rdEnterStory" class="inline-flex items-center justify-center border border-[var(--reflection-accent)] bg-[var(--reflection-accent)] px-8 py-3 font-['Roboto_Mono'] text-sm uppercase tracking-[0.16em] text-white" type="button">Enter Analysis</button>
			<a class="inline-flex items-center justify-center border border-black px-8 py-3 font-['Roboto_Mono'] text-sm uppercase tracking-[0.16em] text-black no-underline" href="<?php echo esc_url( home_url('/reflections/') ); ?>">Return to Gallery</a>
		</div>
	</div>
</section>
<section class="rd-stage" id="rd-analysis" data-stage-id="rd-analysis" hidden>
	<?php get_template_part('template-parts/reflections/chapters/chapter-data-cards', null, [
		'variant' => 'analytical',
		'id' => 'racial-disparities',
		'title' => 'The Data Gap',
		'intro' => 'Racial disparities are not just numbers. They are structural realities that affect lives. Tap a domain to examine the evidence.',
		'cards' => $cards,
		'footer_text' => 'Understanding data is the first step to dismantling systems.',
		'footer_href' => home_url('/reflections/'),
		'footer_label' => 'Return to Gallery',
	]); ?>
</section>
<?php get_footer('reflection');
