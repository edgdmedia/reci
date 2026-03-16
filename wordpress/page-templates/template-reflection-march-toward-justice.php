<?php

/**
 * Template Name: Reflection Gallery - March Toward Justice
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

function reci_mtj_asset(string $path): string
{
	return trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/' . ltrim($path, '/');
}
$controller_path = get_template_directory() . '/assets/js/reflection-stage-controller.js';
$controller_uri  = get_template_directory_uri() . '/assets/js/reflection-stage-controller.js';
$controller_ver  = file_exists($controller_path) ? (string) filemtime($controller_path) : wp_get_theme()->get('Version');
$script_path = get_template_directory() . '/assets/js/reflection-march-toward-justice.js';
$script_uri  = get_template_directory_uri() . '/assets/js/reflection-march-toward-justice.js';
$script_ver  = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');
$title_background = esc_url(reci_mtj_asset('images/protest-march.png'));
wp_enqueue_style('reci-reflection-mtj-fonts', 'https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;700&family=Merriweather:ital,wght@0,300;0,400;1,300&display=swap', [], null);
wp_register_style('reci-reflection-mtj-inline', false, [], $script_ver);
wp_enqueue_style('reci-reflection-mtj-inline');
wp_add_inline_style('reci-reflection-mtj-inline', str_replace('__TITLE_BG__', $title_background, <<<'CSS'
:root {
	--reflection-bg: #e6e6e6;
	--reflection-surface: #ffffff;
	--reflection-card: rgba(255,255,255,0.96);
	--reflection-card-strong: rgba(255,255,255,0.96);
	--reflection-text: #111111;
	--reflection-white: #ffffff;
	--reflection-muted: #444444;
	--reflection-soft-text: #222222;
	--reflection-accent: #8a0e0e;
	--reflection-accent-contrast: #ffffff;
}
body { margin:0; padding:0; background-color:var(--reflection-bg); color:var(--reflection-text); font-family:'Merriweather',serif; overflow:hidden; }
#marchWorld { height:100vh; width:700vw; display:flex; position:relative; transform:translateX(0); transition:transform 1s cubic-bezier(0.23, 1, 0.32, 1); }
[data-march-card].visible { visibility: visible; }
.march-nav {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	padding: 30px;
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	z-index: 1000;
	pointer-events: none;
}
.march-nav-group {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 10px;
	pointer-events: auto;
}
.back-link,
.stage-back {
	text-decoration: none;
	color: white;
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: 2px;
	font-weight: 500;
	mix-blend-mode: difference;
	pointer-events: auto;
	font-size: 1.1rem;
	background: transparent;
	border: 0;
	padding: 0;
	cursor: pointer;
	line-height: 1;
}
.stage-back {
	display:none;
	position: fixed;
	left: 30px;
	bottom: 30px;
	z-index: 1000;
	padding: 10px 16px;
	border: 1px solid rgba(255,255,255,0.18);
	border-radius: 999px;
	background: rgba(0,0,0,0.28);
	backdrop-filter: blur(8px);
}
.audio-dock {
	background: white;
	color: black;
	padding: 10px 20px;
	border-radius: 40px;
	display: flex;
	align-items: center;
	gap: 15px;
	box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
	z-index: 1000;
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	font-size: 0.8rem;
	pointer-events: auto;
}
.play-btn {
	background: black;
	color: white;
	width: 30px;
	height: 30px;
	border-radius: 50%;
	border: none;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
}
.section {
	width: 100vw;
	height: 100vh;
	position: relative;
	flex-shrink: 0;
	border-right: 1px solid rgba(0, 0, 0, 0.1);
	overflow: hidden;
}
.title-section {
	background-image: url("__TITLE_BG__");
	background-size: cover;
	background-position: center;
}
.title-overlay {
	width: 100%;
	height: 100%;
	background: rgba(230, 230, 230, 0.9);
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	text-align: center;
	-webkit-mask-image: linear-gradient(to right, black 50%, transparent 100%);
}
h1 {
	font-family: 'Oswald', sans-serif;
	font-size: 8vw;
	text-transform: uppercase;
	line-height: 0.9;
	margin: 0;
	color: #111;
}
.start-btn {
	margin-top: 50px;
	padding: 20px 50px;
	background: var(--reflection-accent);
	color: white;
	border: none;
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: 2px;
	font-size: 1.2rem;
	cursor: pointer;
	transition: transform 0.2s;
}
.start-btn:hover { transform: scale(1.05); }
.context-section {
	background: #111;
	color: #ddd;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 0 2vw;
	text-align: center;
}
.history-section {
	background: #222;
	color: #f0f0f0;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	position: relative;
}
.year-huge {
	font-family: 'Oswald', sans-serif;
	font-size: 30vw;
	opacity: 0.05;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	white-space: nowrap;
	z-index: 1;
	pointer-events: none;
}
.march-interaction-layer {
	z-index: 10;
	display: flex;
	flex-direction: column;
	align-items: center;
	transition: opacity 0.5s;
	position: absolute;
}
.march-interaction-layer.hidden {
	opacity: 0;
	pointer-events: none;
}
.history-footprints {
	display: flex;
	gap: 20px;
	margin-bottom: 20px;
}
.mini-fp {
	font-size: 2rem;
	opacity: 0.2;
	transform: rotate(90deg);
	transition: opacity 0.3s;
}
.mini-fp.active {
	opacity: 1;
	color: var(--reflection-accent);
}
.mini-step-btn {
	width: 80px;
	height: 80px;
	border-radius: 50%;
	background: transparent;
	border: 2px solid var(--reflection-accent);
	color: white;
	font-family: 'Oswald', sans-serif;
	font-size: 1rem;
	cursor: pointer;
	margin-top: 20px;
	transition: all 0.1s;
	display: flex;
	align-items: center;
	justify-content: center;
}
.mini-step-btn:active {
	transform: scale(0.95);
	background: rgba(138, 14, 14, 0.2);
}
.mini-step-btn:hover {
	background: rgba(138, 14, 14, 0.1);
}
.history-card {
	width: 400px;
	max-width: calc(100vw - 48px);
	background: white;
	color: black;
	padding: 40px;
	z-index: 20;
	box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
	opacity: 0;
	top: 50%;
	left: 50%;
	transform: translate(-50%, calc(-50% + 20px)) scale(0.95);
	transition: all 0.5s ease;
	position: absolute;
	pointer-events: none;
}
.history-card.visible {
	opacity: 1;
	transform: translate(-50%, -50%) scale(1);
	pointer-events: auto;
}
.sec-1968 {
	background: #1a1a1a;
	color: #eee;
}
.sec-2020 {
	background: #f0f0f0;
	color: #111;
}
.crowd-section {
	background: #8aa;
	overflow: hidden;
}
.crowd-layer {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-size: cover;
	background-position: center;
}
.cl-1 {
	opacity: 0.4;
	transform: scale(1.1);
}
.cl-2 {
	opacity: 0.6;
	mix-blend-mode: multiply;
	transform: translateX(100px);
}
.crowd-content {
	position: absolute;
	left: clamp(2rem, 7vw, 6rem);
	bottom: clamp(2.5rem, 9vh, 6rem);
	max-width: min(560px, calc(100vw - 4rem));
	z-index: 20;
	display: grid;
	gap: 1.5rem;
}
.crowd-kicker {
	font-family: 'Oswald', sans-serif;
	font-size: 0.95rem;
	letter-spacing: 0.16em;
	text-transform: uppercase;
	color: rgba(255,255,255,0.86);
}
.crowd-text {
	font-size: clamp(1.75rem, 3.4vw, 2.6rem);
	line-height: 1.18;
	background: rgba(255,255,255,0.94);
	padding: clamp(1.5rem, 3vw, 2.2rem);
	color: #111;
	box-shadow: 0 24px 60px rgba(0,0,0,0.2);
}
.crowd-cta {
	justify-self: start;
}
.reflection-section {
	background: #111;
	color: white;
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	padding: 2rem;
}
.march-reflection-card {
	width: min(840px, 100%);
	padding: clamp(2rem, 4vw, 3.25rem);
	border: 1px solid rgba(255,255,255,0.12);
	background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
	box-shadow: 0 24px 60px rgba(0,0,0,0.2);
	text-align: center;
}
.march-reflection-title {
	font-family:'Oswald';
	font-size: clamp(2.8rem, 5vw, 4.2rem);
	margin-bottom: 1.75rem;
	color: var(--reflection-accent);
	text-transform: uppercase;
	letter-spacing: 0.04em;
}
.march-reflection-copy {
	font-size: clamp(1.15rem, 2vw, 1.6rem);
	line-height: 1.65;
	max-width: 38rem;
	margin: 0 auto 2rem;
	color: rgba(255,255,255,0.92);
}
.march-reflection-input {
	width: 100%;
	min-height: 160px;
	background: rgba(255,255,255,0.08);
	border: 1px solid rgba(255,255,255,0.12);
	color: white;
	padding: 18px 20px;
	font-family: 'Merriweather';
	margin-bottom: 2rem;
	border-radius: 10px;
}
@media (max-width: 900px) {
	.title-overlay {
		-webkit-mask-image: none;
		padding: 0 24px;
	}
	h1 {
		font-size: 20vw;
	}
	.history-card {
		padding: 28px;
	}
	.march-nav {
		padding: 22px;
	}
	.stage-back {
		left: 22px;
		bottom: 22px;
	}
	.crowd-content {
		left: 24px;
		right: 24px;
		bottom: 32px;
		max-width: none;
	}
	.crowd-text {
		font-size: 1.35rem;
	}
	.crowd-cta {
		justify-self: stretch;
	}
}
CSS
));
wp_enqueue_script('reci-reflection-stage-controller', $controller_uri, [], $controller_ver, true);
wp_enqueue_script('reci-reflection-mtj', $script_uri, ['reci-reflection-stage-controller'], $script_ver, true);
get_header('reflection'); ?>
<nav class="march-nav">
	<div class="march-nav-group">
		<a class="back-link" href="<?php echo esc_url( home_url('/reflections/') ); ?>">← Gallery</a>
	</div>
	<div class="audio-dock"><button id="marchPlayBtn" class="play-btn" type="button">▶</button><span>History in Motion</span></div>
</nav>
<button type="button" class="stage-back" id="marchStageBack">← Back</button>
<audio id="marchAudio" src="<?php echo esc_url(reci_mtj_asset('audio/march-toward-justice.m4a')); ?>" loop></audio>
<div class="timeline-world" id="marchWorld">
	<section class="section title-section" id="march-title">
		<div class="title-overlay">
			<div style="font-family: 'Oswald'; font-size: 2rem; color: var(--reflection-accent);">1965</div>
			<h1>The March<br>Toward<br>Justice</h1>
			<button class="start-btn" id="marchStartBtn" type="button" data-stage-target="march-context">Take the First Step</button>
		</div>
	</section>
	<section class="section context-section" id="march-context">
		<div style="max-width: 900px; padding: 0 28px;">
			<h2 style="font-family: 'Oswald'; font-size: clamp(2.4rem, 4vw, 3.5rem); margin-bottom: 24px; color: var(--reflection-accent); text-transform: uppercase; letter-spacing: 0.04em;">The Weight of History</h2>
			<p style="font-size: 1.15rem; line-height: 1.85; max-width: 760px; margin: 0 auto 44px; color: rgba(255,255,255,0.9);">The fight for justice didn't start on a bridge in Selma. It began in the fields, in the courtrooms, and in the quiet resolve of millions who refused to accept inequality as their fate.<br><br>By 1965, the Civil Rights Act had passed, but the ballot box remained locked to Black Americans in the South. The march you are about to witness was not just a protest—it was a demand for the soul of democracy.</p>
			<button class="start-btn" id="marchContextBtn" style="background: transparent; border: 2px solid rgba(255,255,255,0.82); color: white;" type="button" data-stage-target="march-selma">Begin the March →</button>
		</div>
	</section>
	<?php get_template_part('template-parts/reflections/chapters/chapter-step-sequence', null, ['variant' => 'march-history', 'id' => 'march-selma', 'backdrop' => 'SELMA', 'title' => 'The Bridge', 'body' => 'We stood at the edge of the bridge, looking down at the water below and the line of troopers ahead. The air was cold, but our resolve was burning.', 'button_label' => 'Forward to 1968 →', 'continue_target' => 'march-1968']); ?>
	<?php get_template_part('template-parts/reflections/chapters/chapter-step-sequence', null, ['variant' => 'march-history', 'id' => 'march-1968', 'backdrop' => '1968', 'title' => 'The Mourning', 'body' => 'When the news broke, the world stopped. But silence did not last long. The grief turned into a new kind of fire, one that burned across cities and decades.', 'button_label' => 'Forward to 2020 →', 'continue_target' => 'march-2020', 'dark' => true]); ?>
	<?php get_template_part('template-parts/reflections/chapters/chapter-step-sequence', null, ['variant' => 'march-history', 'id' => 'march-2020', 'backdrop' => '2020', 'title' => 'The Awakening', 'body' => 'A global chorus rose up. “I can’t breathe” became a rallying cry heard in every language. The march had not ended; it had just found new feet.', 'button_label' => 'Join the Crowd →', 'continue_target' => 'march-crowd']); ?>
	<div class="section" id="march-crowd"><?php get_template_part('template-parts/reflections/chapters/chapter-parallax-stage', null, ['variant' => 'crowd', 'id' => 'march-crowd-stage', 'layers' => [['src' => reci_mtj_asset('images/hands-unity.png'), 'opacity' => '0.4'], ['src' => reci_mtj_asset('images/voting-rights.png'), 'opacity' => '0.6', 'blend' => 'multiply']], 'text' => 'We are not just marching for ourselves. We are marching for the soul of this nation.', 'button_label' => 'Reflect on the Journey →', 'continue_target' => 'march-reflect']); ?></div>
	<div class="section reflection-section" id="march-reflect">
		<div class="march-reflection-card">
			<h2 class="march-reflection-title">Your Reflection</h2>
			<p class="march-reflection-copy">Which moment in this timeline resonates most with you, and why?</p>
			<textarea class="march-reflection-input" placeholder="Share your thoughts..."></textarea>
			<button class="start-btn" type="button" id="marchCompleteBtn" data-complete-href="<?php echo esc_url( home_url('/reflections/') ); ?>">Complete & Return</button>
		</div>
	</div>
</div>
<?php get_footer('reflection');
