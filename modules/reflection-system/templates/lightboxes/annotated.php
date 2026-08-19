<?php
/**
 * Reflection lightbox variant: annotated.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}
?>
<style>
	.lightbox.active { display: grid; }
	.lightbox--plain { grid-template-columns: minmax(0, 1fr) !important; }
	.lightbox--plain .lightbox-aside { display: none; }
	.panel-hotspot {
		position: absolute;
		width: 22px;
		height: 22px;
		border-radius: 999px;
		border: 2px solid var(--reflection-body);
		background: var(--reflection-accent);
		color: var(--reflection-accent-contrast);
		font-size: 0.72rem;
		font-weight: 700;
		display: flex;
		align-items: center;
		justify-content: center;
		transform: translate(-50%, -50%);
		cursor: pointer;
		pointer-events: auto;
		box-shadow: 0 0 0 8px var(--reflection-hotspot-ring);
	}
	.panel-hotspot.active {
		background: white;
		box-shadow: 0 0 0 10px rgba(210, 231, 202, 0.18);
	}
	.annotation-chip.active {
		border-color: var(--reflection-accent) !important;
		background: rgba(167, 199, 150, 0.18) !important;
	}
</style>
<div class="lightbox fixed inset-0 z-[80] hidden place-items-center bg-[color:var(--reflection-overlay)] p-4 sm:p-6" id="lightbox" aria-hidden="true">
	<div class="relative grid max-h-[92vh] w-full max-w-[1500px] overflow-hidden rounded-3xl border border-[color:var(--reflection-border)] bg-[var(--reflection-surface-alt)] lg:grid-cols-[minmax(0,1.5fr)_minmax(320px,420px)]">
		<button class="absolute right-4 top-4 z-[2] h-11 w-11 rounded-full bg-[var(--reflection-card)] text-xl reci-reflection-text" type="button" id="lightboxClose">×</button>
		<div class="relative flex min-h-[70vh] items-center justify-center bg-[var(--reflection-bg)] p-6 lg:min-h-[70vh]">
			<img class="block max-h-[calc(92vh-3rem)] max-w-full object-contain" src="" alt="" id="lightboxImage">
			<div class="absolute inset-6" id="hotspotLayer"></div>
		</div>
		<aside class="lightbox-aside min-h-0 overflow-y-auto border-t border-[color:var(--reflection-border)] bg-[var(--reflection-surface)] p-6 lg:border-l lg:border-t-0">
			<span class="block font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent">Panel annotations</span>
			<h3 class="mt-2 font-['Playfair_Display'] text-3xl font-semibold reci-reflection-text" id="lightboxTitle">Panel reader</h3>
			<p class="mt-2 text-base leading-8 reci-reflection-soft-text" id="lightboxIntro">Select an annotation point to read a guided note for this panel.</p>
			<div class="mt-5 rounded-[18px] border border-[color:var(--reflection-border)] bg-[rgba(255,255,255,0.12)] p-4">
				<h4 class="font-['Oswald'] text-sm uppercase tracking-[0.06em] reci-reflection-accent" id="annotationTitle">Panel note</h4>
				<p class="mt-2 text-sm leading-7 reci-reflection-soft-text" id="annotationBody">Annotated notes will appear here.</p>
			</div>
			<div class="mt-4 grid gap-3" id="annotationList"></div>
		</aside>
	</div>
</div>
