import fs from 'node:fs';
import path from 'node:path';
import { describe, expect, it } from 'vitest';

const runtimePaths = [
	'modules/reflection-system/assets/js/reflection-system-runtime.js',
	'wordpress/modules/reflection-system/assets/js/reflection-system-runtime.js',
];

function loadRuntime(runtimePath) {
	window.RECIReflection = {
		createStageController: () => ({
			init() {},
			goTo() {},
			back() {},
			current() {
				return null;
			},
		}),
	};
	window.eval(fs.readFileSync(runtimePath, 'utf8'));
	document.dispatchEvent(new Event('DOMContentLoaded'));
}

describe.each(runtimePaths)('%s lightbox annotations', (runtimeFile) => {
	it('renders panel hotspots and annotation chips from data annotations', () => {
		const runtimePath = path.resolve(runtimeFile);
		document.body.innerHTML = `
			<img
				class="panel-image"
				src="/panel.webp"
				alt="Case I, Panel A"
				data-annotations='[{"x":"39.6","y":"22.9","title":"Physical differences","body":"A guided note."}]'
			>
			<div class="lightbox" id="lightbox" aria-hidden="true">
				<img id="lightboxImage" src="" alt="">
				<button id="lightboxClose" type="button"></button>
				<div id="hotspotLayer"></div>
				<h3 id="lightboxTitle"></h3>
				<p id="lightboxIntro"></p>
				<h4 id="annotationTitle"></h4>
				<p id="annotationBody"></p>
				<div id="annotationList"></div>
			</div>
			<div id="responseList"></div>
		`;

		loadRuntime(runtimePath);

		document.querySelector('.panel-image').click();

		expect(document.querySelectorAll('.panel-hotspot')).toHaveLength(1);
		expect(document.querySelectorAll('.annotation-chip')).toHaveLength(1);
		expect(document.querySelector('#annotationTitle').textContent).toBe('Physical differences');
		expect(document.querySelector('#annotationBody').innerHTML).toBe('A guided note.');
	});
});
