<?php

/**
 * Template Name: Reflection Gallery - Voices of Resistance
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

function reci_vor_asset(string $path): string
{
	return trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/' . ltrim($path, '/');
}

$controller_path = get_template_directory() . '/assets/js/reflection-stage-controller.js';
$controller_uri  = get_template_directory_uri() . '/assets/js/reflection-stage-controller.js';
$controller_ver  = file_exists($controller_path) ? (string) filemtime($controller_path) : wp_get_theme()->get('Version');
$script_path = get_template_directory() . '/assets/js/reflection-voices-of-resistance.js';
$script_uri  = get_template_directory_uri() . '/assets/js/reflection-voices-of-resistance.js';
$script_ver  = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');

wp_enqueue_style(
	'reci-reflection-vor-fonts',
	'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&family=Oswald:wght@300;400;500;700&display=swap',
	[],
	null
);
wp_register_style('reci-reflection-vor-inline', false, [], $script_ver);
wp_enqueue_style('reci-reflection-vor-inline');
wp_add_inline_style('reci-reflection-vor-inline', <<<'CSS'
:root {
	--vor-primary: #8B4513;
	--vor-bg: #111;
	--vor-text: #e0e0e0;
	--vor-accent: #FFB81C;
	--reflection-bg: #111;
	--reflection-text: #fff;
	--reflection-soft-text: #e0e0e0;
	--reflection-accent: #FFB81C;
}
body {
	background-color: var(--vor-bg);
	color: var(--vor-text);
	overflow-x: hidden;
	margin: 0;
	padding: 0;
	font-family: 'Inter', sans-serif;
	min-height: 100vh;
	position: relative;
}
.chapter-intro,
.chapter-interact,
.chapter-hold,
.chapter-threshold,
.chapter-march,
.chapter-reflection {
	position: fixed;
	inset: 0;
	width: 100%;
}
.vor-nav {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	padding: 30px;
	z-index: 1000;
	display: flex;
	justify-content: space-between;
	pointer-events: none;
}
.vor-nav a,
.vor-nav button { pointer-events: auto; }
.back-link,
.stage-back {
	color: white;
	text-decoration: none;
	font-family: 'Oswald', sans-serif;
	font-size: 1rem;
	text-transform: uppercase;
	letter-spacing: 2px;
	mix-blend-mode: difference;
	background: transparent;
	border: 0;
	padding: 0;
	cursor: pointer;
}
.stage-back { display: none; }
.chapter-intro {
	height: 100vh;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	position: relative;
	background: black;
	z-index: 100;
	transition: transform 1s cubic-bezier(0.77, 0, 0.175, 1);
}
.intro-bg {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-size: cover;
	background-position: center;
	opacity: 0.4;
	filter: grayscale(100%);
}
.intro-content { z-index: 2; text-align: center; }
.intro-title {
	font-family: 'Playfair Display', serif;
	font-size: clamp(3rem, 10vw, 8rem);
	color: white;
	line-height: 0.9;
	margin: 0;
}
.intro-subtitle {
	font-family: 'Inter', sans-serif;
	font-size: 1.2rem;
	letter-spacing: 2px;
	color: var(--vor-accent);
	margin-top: 20px;
	font-style: italic;
}
.enter-btn {
	margin-top: 50px;
	padding: 15px 40px;
	background: transparent;
	border: 1px solid rgba(255, 255, 255, 0.5);
	color: white;
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: 2px;
	cursor: pointer;
	transition: all 0.3s;
	text-decoration: none;
	display: inline-flex;
	align-items: center;
	justify-content: center;
}
.enter-btn:hover { background: white; color: black; }
.chapter-interact {
	height: 100vh;
	width: 100%;
	position: relative;
	overflow: hidden;
	display: none;
	opacity: 0;
	transition: opacity 1s ease;
}
.interact-stage {
	height: 100%;
	width: 100%;
	background-repeat: no-repeat;
	background-position: center center;
	background-size: cover;
	position: relative;
}
.instruction-overlay {
	position: absolute;
	top: 100px;
	width: 100%;
	text-align: center;
	pointer-events: none;
	z-index: 10;
}
.instruction-overlay h3 {
	font-family: 'Oswald', sans-serif;
	font-weight: 300;
	letter-spacing: 4px;
	color: rgba(255, 255, 255, 0.8);
	text-transform: uppercase;
	text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
}
.hotspot {
	position: absolute;
	width: 50px;
	height: 50px;
	background: rgba(255, 184, 28, 0.2);
	border: 2px solid var(--vor-accent);
	border-radius: 50%;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: transform 0.3s;
	animation: pulse-ring 2s infinite;
	color: white;
}
.hotspot:hover { transform: scale(1.2); background: rgba(255, 184, 28, 0.4); }
@keyframes pulse-ring {
	0% { box-shadow: 0 0 0 0 rgba(255, 184, 28, 0.4); }
	70% { box-shadow: 0 0 0 15px rgba(255, 184, 28, 0); }
	100% { box-shadow: 0 0 0 0 rgba(255, 184, 28, 0); }
}
.hotspot-detail-panel {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 80%;
	max-width: 400px;
	background: rgba(10, 10, 10, 0.95);
	border: 1px solid var(--vor-accent);
	padding: 40px;
	text-align: center;
	opacity: 0;
	pointer-events: none;
	transition: opacity 0.3s;
	z-index: 20;
}
.hotspot-detail-panel.visible { opacity: 1; pointer-events: auto; }
.detail-title {
	font-family: 'Oswald';
	color: var(--vor-accent);
	font-size: 1.5rem;
	margin-bottom: 10px;
}
.detail-text {
	font-family: 'Playfair Display';
	font-size: 1.2rem;
	line-height: 1.5;
}
.detail-close {
	margin-top: 20px;
	padding: 10px 20px;
	background: #333;
	color: white;
	border: none;
	cursor: pointer;
}
.continue-hint {
	position: absolute;
	bottom: 40px;
	left: 50%;
	transform: translateX(-50%);
	font-family: 'Oswald';
	text-transform: uppercase;
	letter-spacing: 2px;
	font-size: 0.9rem;
	opacity: 0;
	pointer-events: none;
	cursor: pointer;
	animation: bounce 2s infinite;
	transition: opacity 1s;
	color: white;
}
.continue-hint.visible { opacity: 1; pointer-events: auto; }
.chapter-hold {
	min-height: 100vh;
	background: #1a1a1a;
	display: none;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	opacity: 0;
	transition: opacity 1s;
	padding: 40px;
	box-sizing: border-box;
}
.hold-title {
	font-family: 'Playfair Display', serif;
	font-size: 3rem;
	margin-bottom: 40px;
	color: var(--vor-accent);
}
.hold-text-container {
	max-width: 700px;
	text-align: center;
	font-family: 'Playfair Display', serif;
	font-size: 1.4rem;
	line-height: 1.6;
	min-height: 200px;
}
.hold-paragraph {
	margin-bottom: 20px;
	opacity: 0;
	transform: translateY(20px);
	transition: all 0.5s;
	display: none;
}
.hold-paragraph.visible { opacity: 1; transform: translateY(0); display: block; }
.hold-controls { text-align: center; margin-top: 40px; }
.hold-prompt {
	font-family: 'Oswald';
	text-transform: uppercase;
	letter-spacing: 2px;
	margin-bottom: 10px;
	opacity: 0.7;
}
.hold-btn {
	width: 80px;
	height: 80px;
	border-radius: 50%;
	border: 2px solid #555;
	background: transparent;
	color: white;
	margin-top: 40px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.2s;
	font-size: 2rem;
}
.hold-btn:active { transform: scale(0.9); border-color: var(--vor-accent); }
.chapter-threshold {
	height: 100vh;
	background: #111;
	display: none;
	align-items: center;
	justify-content: center;
	text-align: center;
	opacity: 0;
	transition: opacity 1s;
	position: relative;
}
.threshold-content { z-index: 2; max-width: 800px; }
.threshold-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: white; opacity: 0.1; }
.thresh-text {
	font-family: 'Oswald';
	font-size: 2.5rem;
	text-transform: uppercase;
	letter-spacing: 5px;
	color: #333;
	transition: color 2s;
}
.chapter-threshold.active .thresh-text { color: white; }
.threshold-btn { border-color: #333; color: #333; }
.chapter-march {
	height: 100vh;
	overflow: hidden;
	display: none;
	opacity: 0;
	transition: opacity 1s;
	position: relative;
}
.march-track { display: flex; height: 100%; width: 300vw; will-change: transform; }
.march-panel {
	width: 100vw;
	height: 100%;
	flex-shrink: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
}
.mp-content { z-index: 2; max-width: 600px; text-align: center; }
.mp-bg {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-size: cover;
	background-position: center;
	opacity: 0.3;
	filter: grayscale(100%);
}
.mp-title { font-family: 'Playfair Display'; font-size: 3rem; color: white; }
.mp-title--accent { font-family: 'Oswald'; font-size: 3rem; color: var(--vor-accent); }
.mp-body { margin-top: 1rem; color: white; font-size: 1.2rem; }
.chapter-reflection {
	height: 100vh;
	background: #0a0a0a;
	display: none;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	opacity: 0;
	transition: opacity 1s;
	padding: 20px;
}
.reflect-prompt {
	font-family: 'Playfair Display', serif;
	font-size: 2rem;
	margin-bottom: 30px;
	text-align: center;
	max-width: 800px;
	color: white;
}
.reflect-input {
	width: 100%;
	max-width: 600px;
	height: 150px;
	background: #222;
	border: 1px solid #444;
	color: white;
	padding: 20px;
	font-family: 'Inter';
	font-size: 1.1rem;
}
@keyframes bounce {
	0%, 100% { transform: translateX(-50%) translateY(0); }
	50% { transform: translateX(-50%) translateY(10px); }
}
CSS);
wp_enqueue_script('reci-reflection-stage-controller', $controller_uri, [], $controller_ver, true);
wp_enqueue_script('reci-reflection-vor', $script_uri, ['reci-reflection-stage-controller'], $script_ver, true);

get_header('reflection');

get_template_part('template-parts/reflections/menu-overlay', null, [
	'variant' => 'voices-of-resistance',
	'back_url' => '/reflections',
]);

get_template_part('template-parts/reflections/hero', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-intro',
	'subtitle' => '“I remember the day we decided enough was enough...”',
	'background_image' => reci_vor_asset('images/community-gathering.png'),
	'section_attributes' => ['data-stage' => 'top'],
	'actions' => [
		[
			'href' => '#',
			'label' => 'Enter the Story',
			'attributes' => ['data-stage-target' => 'explore'],
		],
	],
]);

get_template_part('template-parts/reflections/chapters/chapter-hotspot-stage', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-explore',
	'background_image' => reci_vor_asset('images/community-gathering.png'),
	'instruction' => 'Tap the Icons to Uncover the Story',
	'hotspots' => [
		['key' => 'strategy', 'top' => '40%', 'left' => '30%'],
		['key' => 'resolve', 'top' => '60%', 'left' => '60%'],
		['key' => 'legacy', 'top' => '30%', 'left' => '70%'],
	],
]);

get_template_part('template-parts/reflections/chapters/chapter-progressive-text', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-hold',
	'title' => 'The Decision',
	'paragraphs' => [
		'"The march was scheduled for dawn. We knew the risks."',
		'"But staying silent had become more dangerous than speaking out."',
		'"As we walked down those streets, arm in arm, I felt the weight of generations."',
	],
	'prompt' => 'Click to Reveal',
	'button_label' => '▼',
	'continue_label' => 'Face the Moment →',
]);

get_template_part('template-parts/reflections/chapters/chapter-threshold-message', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-threshold',
	'title' => 'We stood at the edge of history.',
	'button_label' => 'March Forward',
]);

get_template_part('template-parts/reflections/chapters/chapter-horizontal-panels', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-march',
	'items' => [
		['title' => '"Every step was an act of defiance."', 'background_image' => reci_vor_asset('images/protest-march.png')],
		['title' => '"They could beat us, but they could not break our spirit."', 'background_image' => reci_vor_asset('images/voting-rights.png')],
		['accent_title' => 'The Struggle Continues'],
	],
	'continue_label' => 'Reflect',
]);

get_template_part('template-parts/reflections/chapters/chapter-reflection-prompt', null, [
	'variant' => 'voices-of-resistance',
	'id' => 's-reflect',
	'prompt' => 'What does courage mean to you in the context of social justice?',
	'button_label' => 'Complete Journey',
	'button_href' => get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/'),
]);

get_footer('reflection');
