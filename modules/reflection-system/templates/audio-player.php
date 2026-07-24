<?php
/**
 * Global floating audio player for Reflection System.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$audio_url = (string) ($args['audio_url'] ?? '');
$audio_label = (string) ($args['audio_label'] ?? 'Audio track');

if ($audio_url === '') {
	return;
}
?>
<div id="reci-floating-audio" class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 bg-[var(--reflection-surface,#222)] px-4 py-3 shadow-2xl transition-all duration-300 rounded-full border border-white/10 hover:border-white/30 backdrop-blur-md opacity-80 hover:opacity-100 group">
	<button id="reci-audio-toggle" type="button" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-[var(--reflection-accent,#FFB81C)] text-black shadow-lg transition-transform hover:scale-110 active:scale-95" aria-label="Toggle Audio">
		<!-- Play Icon -->
		<svg id="reci-audio-icon-play" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
			<path d="M8 5v14l11-7z" />
		</svg>
		<!-- Pause Icon (hidden by default) -->
		<svg id="reci-audio-icon-pause" class="h-4 w-4 hidden" fill="currentColor" viewBox="0 0 24 24">
			<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
		</svg>
	</button>
	<div class="flex flex-col max-w-0 overflow-hidden opacity-0 group-hover:max-w-[200px] group-hover:opacity-100 transition-all duration-500 whitespace-nowrap">
		<span class="text-xs font-bold uppercase tracking-wider text-[var(--reflection-heading,#fff)]"><?php echo esc_html($audio_label); ?></span>
		<span id="reci-audio-status" class="text-[10px] text-[var(--reflection-muted,#aaa)] uppercase tracking-widest">Paused</span>
	</div>
	
	<audio id="reci-global-audio" loop preload="metadata">
		<source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
	</audio>
</div>

<script>
(function() {
	const audio = document.getElementById('reci-global-audio');
	const btn = document.getElementById('reci-audio-toggle');
	const playIcon = document.getElementById('reci-audio-icon-play');
	const pauseIcon = document.getElementById('reci-audio-icon-pause');
	const statusText = document.getElementById('reci-audio-status');
	
	if (!audio || !btn) return;
	
	let hasInteracted = false;
	let isIntentionallyPaused = false;
	
	function updateUI() {
		if (audio.paused) {
			playIcon.classList.remove('hidden');
			pauseIcon.classList.add('hidden');
			if (statusText) statusText.innerText = 'Paused';
		} else {
			playIcon.classList.add('hidden');
			pauseIcon.classList.remove('hidden');
			if (statusText) statusText.innerText = 'Playing';
		}
	}
	
	function toggleAudio() {
		if (audio.paused) {
			audio.play().catch(e => console.warn('Audio play failed:', e));
			isIntentionallyPaused = false;
		} else {
			audio.pause();
			isIntentionallyPaused = true;
		}
		updateUI();
	}
	
	btn.addEventListener('click', (e) => {
		e.preventDefault();
		hasInteracted = true;
		toggleAudio();
	});
	
	// Attempt autoplay on first document interaction if not intentionally paused
	const startAudio = () => {
		if (hasInteracted || isIntentionallyPaused) return;
		hasInteracted = true;
		audio.play().then(updateUI).catch(e => {
			console.warn('Autoplay prevented by browser.');
			updateUI();
		});
		
		['click', 'touchstart', 'scroll', 'wheel'].forEach(evt => {
			document.removeEventListener(evt, startAudio);
		});
	};
	
	['click', 'touchstart', 'scroll', 'wheel'].forEach(evt => {
		document.addEventListener(evt, startAudio, { passive: true, once: true });
	});
	
	audio.addEventListener('play', updateUI);
	audio.addEventListener('pause', updateUI);
})();
</script>
