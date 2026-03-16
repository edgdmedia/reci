<?php

/**
 * Template Name: Reflection Gallery - Breaking Chains
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

function reci_bc_asset(string $path): string
{
	return trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/' . ltrim($path, '/');
}
$script_path = get_template_directory() . '/assets/js/reflection-breaking-chains.js';
$script_uri  = get_template_directory_uri() . '/assets/js/reflection-breaking-chains.js';
$script_ver  = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');
wp_enqueue_style('reci-reflection-bc-fonts', 'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&display=swap', [], null);
wp_register_style('reci-reflection-bc-inline', false, [], $script_ver);
wp_enqueue_style('reci-reflection-bc-inline');
wp_add_inline_style('reci-reflection-bc-inline', <<<'CSS'
:root {
	--reflection-bg: #0a0a0a;
	--reflection-surface: rgba(20,20,20,0.8);
	--reflection-card: rgba(255,255,255,0.04);
	--reflection-card-strong: rgba(255,255,255,0.08);
	--reflection-text: #e0e0e0;
	--reflection-white: #ffffff;
	--reflection-muted: #b8b8b8;
	--reflection-soft-text: #e0e0e0;
	--reflection-accent: #D4AF37;
	--reflection-accent-contrast: #111111;
	--reflection-metal: #5a5a5a;
}
body { margin:0; padding:0; background-color:var(--reflection-bg); color:var(--reflection-text); font-family:'Lato',sans-serif; overflow-x:hidden; }
.bc-nav {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	padding: 30px;
	display: flex;
	justify-content: space-between;
	z-index: 100;
	mix-blend-mode: difference;
	pointer-events: none;
}
.back-link,
.stage-back {
	text-decoration: none;
	color: white;
	text-transform: uppercase;
	letter-spacing: 2px;
	background: transparent;
	border: 0;
	padding: 0;
	cursor: pointer;
	pointer-events: auto;
}
.audio-dock {
	position: fixed;
	bottom: 30px;
	right: 30px;
	background: rgba(20, 20, 20, 0.8);
	border: 1px solid #333;
	padding: 10px 20px;
	border-radius: 4px;
	display: flex;
	align-items: center;
	gap: 15px;
	z-index: 100;
}
.play-btn {
	background: none;
	border: 1px solid white;
	color: white;
	width: 30px;
	height: 30px;
	border-radius: 50%;
	cursor: pointer;
}
.bc-stage {
	min-height: 100vh;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	position: relative;
	text-align: center;
	opacity: 0;
	transition: opacity 1s;
}
.bc-stage.active { opacity:1; }
.title-huge {
	font-family: 'Cinzel', serif;
	font-size: 5rem;
	color: var(--reflection-text);
	text-transform: uppercase;
	letter-spacing: 10px;
	margin-bottom: 20px;
}
.verse-text {
	max-width: 600px;
	font-size: 1.4rem;
	line-height: 1.6;
	margin: 0 auto;
}
#bcProgressLine { transition: height 0.5s; }
.link-broken { border-color: #333 !important; transform-origin: top center; }
@keyframes breakAndFall { 0% { transform: rotate(0deg); } 20% { transform: rotate(15deg); border-color: var(--reflection-accent); } 100% { transform: rotate(45deg) translateY(500px); opacity: 0; } }
.shift-word { color: var(--reflection-accent); cursor: pointer; border-bottom: 1px dashed var(--reflection-accent); transition: all 0.3s; }
.shift-word:hover, .shift-word.shifted { background: rgba(212, 175, 55, 0.2); color: white; }
CSS);
wp_enqueue_script('reci-reflection-bc', $script_uri, [], $script_ver, true);
get_header('reflection'); ?>
<nav class="bc-nav">
	<div class="flex items-center gap-6">
		<a class="back-link" href="<?php echo esc_url( home_url('/reflections/') ); ?>">← Gallery</a>
	</div>
</nav>
<div class="audio-dock">
	<button id="bcPlayBtn" class="play-btn" type="button">▶</button>
	<span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Breaking Chains</span>
</div>
<div id="bcProgressLine" class="fixed left-1/2 top-0 z-40 h-0 w-[2px] -translate-x-1/2 bg-[var(--reflection-accent)] shadow-[0_0_10px_var(--reflection-accent)]"></div>
<div id="bcFreedomBg" class="pointer-events-none fixed inset-0 -z-10 bg-[linear-gradient(to_top,#1a0b00,#000)] opacity-0 transition-opacity duration-[2000ms]"></div>
<audio id="bcAudio" src="<?php echo esc_url(reci_bc_asset('audio/breaking-chains.m4a')); ?>"></audio>
<section class="bc-stage active flex-col items-center justify-center px-5 py-16 text-center" id="s1">
	<h1 class="title-huge">Breaking<br>Chains</h1>
	<p class="verse-text">Liberation is not merely the absence of physical chains. It is the transformation of consciousness.</p>
	<div class="mt-12 text-xs uppercase tracking-[0.18em] text-white/60">Scroll Down to Begin</div>
</section>
<section class="bc-stage flex-col items-center justify-center px-5 py-16 text-center" id="s2">
	<?php get_template_part('template-parts/reflections/chapters/chapter-drag-reveal', null, ['variant' => 'chain', 'id' => 'bc-chain-stage', 'text' => 'Historically, systems of oppression have relied on more than physical force...', 'instruction' => 'Drag Down to Break']); ?>
</section>
<section class="bc-stage flex-col items-center justify-center px-5 py-16 text-center" id="s3">
	<?php get_template_part('template-parts/reflections/chapters/chapter-word-shift', null, ['variant' => 'liberation', 'id' => 'word-shift', 'title' => 'Internal Narratives', 'html' => "The process of liberation requires <span class=\"shift-word\" data-shift=\"CONSCIENTIZATION\">awakening</span>... a shift from feeling <span class=\"shift-word\" data-shift=\"POWERFUL\">powerless</span> to recognizing one's own <span class=\"shift-word\" data-shift=\"AGENCY\">fate</span>."]); ?>
</section>
<section class="bc-stage flex-col items-center justify-center px-5 py-16 text-center" id="s4">
	<div class="mx-auto flex min-h-[70vh] max-w-[720px] flex-col items-center justify-center"><img src="<?php echo esc_url(reci_bc_asset('images/hands-unity.png')); ?>" class="mb-8 w-[300px] rounded-full opacity-80 sepia-[50%]" alt="Hands in unity">
		<p class="verse-text">When people come together, they build the collective power necessary for change.</p>
	</div>
</section>
<section class="bc-stage flex-col items-center justify-center px-5 py-16 text-center" id="s5">
	<div class="mx-auto flex min-h-[70vh] max-w-[720px] flex-col items-center justify-center">
		<h1 class="title-huge" style="color: var(--reflection-accent);">Freedom</h1>
		<p class="verse-text">A world where all people can live fully, freely, and with dignity.</p><button class="mt-10 inline-flex items-center justify-center border border-white px-10 py-4 text-sm uppercase tracking-[0.18em] text-white no-underline" type="button" id="bcCompleteBtn" data-complete-href="<?php echo esc_url( home_url('/reflections/') ); ?>">Return to Gallery</button>
	</div>
</section>
<?php get_footer('reflection');
