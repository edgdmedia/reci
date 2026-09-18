import fs from 'node:fs';
import path from 'node:path';
import { beforeEach, describe, expect, it } from 'vitest';

const runtimePaths = [
	'modules/reflection-system/assets/js/reflection-system-runtime.js',
	'wordpress/modules/reflection-system/assets/js/reflection-system-runtime.js',
];

function loadRuntime(runtimePath, { readyState = 'loading', dispatchDOMContentLoaded = true } = {}) {
	Object.defineProperty(document, 'readyState', {
		value: readyState,
		configurable: true,
	});
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
	if (dispatchDOMContentLoaded) {
		document.dispatchEvent(new Event('DOMContentLoaded'));
	}
}

function renderFixture() {
	const annotations = [
		{
			x: '39.6',
			y: '22.9',
			title: 'Physical differences',
			body: 'This text includes &quot;quoted text&quot; and <em>markup</em>.',
		},
	];
	const annotationsJson = JSON.stringify(annotations);
	const encodedAnnotations = Buffer.from(annotationsJson, 'utf8').toString('base64');

	document.body.innerHTML = `
		<img
			class="panel-image"
			src="/panel.webp"
			alt="Case I, Panel A"
			data-annotations='[{"x":"39.6","y":"22.9","title":"Physical differences","body":"This text includes "quoted text" and <em>markup</em>."}]'
			data-annotations-json="${encodedAnnotations}"
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
}

function expectAnnotationRender() {
	document.querySelector('.panel-image').click();

	expect(document.querySelectorAll('.panel-hotspot')).toHaveLength(1);
	expect(document.querySelectorAll('.annotation-chip')).toHaveLength(1);
	expect(document.querySelector('#annotationTitle').textContent).toBe('Physical differences');
	expect(document.querySelector('#annotationBody').innerHTML).toBe('This text includes "quoted text" and <em>markup</em>.');
}

describe.each(runtimePaths)('%s lightbox annotations', (runtimeFile) => {
	beforeEach(() => {
		document.body.innerHTML = '';
	});

	it('renders panel hotspots and annotation chips from data annotations', () => {
		const runtimePath = path.resolve(runtimeFile);
		renderFixture();

		loadRuntime(runtimePath);

		expectAnnotationRender();
	});

	it('initializes when the runtime loads after DOMContentLoaded', () => {
		const runtimePath = path.resolve(runtimeFile);
		renderFixture();

		loadRuntime(runtimePath, { readyState: 'complete', dispatchDOMContentLoaded: false });

		expectAnnotationRender();
	});
});
