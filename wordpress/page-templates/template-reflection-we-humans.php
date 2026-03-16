<?php

/**
 * Template Name: Reflection Gallery - We Humans
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

function reci_we_humans_asset(string $path): string
{
	return trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/we-humans/' . ltrim($path, '/');
}

$controller_path = get_template_directory() . '/assets/js/reflection-stage-controller.js';
$controller_uri  = get_template_directory_uri() . '/assets/js/reflection-stage-controller.js';
$controller_ver  = file_exists($controller_path) ? (string) filemtime($controller_path) : wp_get_theme()->get('Version');
$script_path = get_template_directory() . '/assets/js/reflection-we-humans.js';
$script_uri  = get_template_directory_uri() . '/assets/js/reflection-we-humans.js';
$script_ver  = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');

wp_enqueue_style(
	'reci-reflection-we-humans-fonts',
	'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600&family=Oswald:wght@300;400;500;700&display=swap',
	[],
	null
);
wp_register_style('reci-reflection-we-humans-inline', false, [], $script_ver);
wp_enqueue_style('reci-reflection-we-humans-inline');
wp_add_inline_style('reci-reflection-we-humans-inline', <<<'CSS'
:root {
	--reflection-bg: #66676D;
	--reflection-surface: rgba(197, 198, 199, 0.18);
	--reflection-surface-alt: rgba(102, 103, 109, 0.82);
	--reflection-panel: rgba(210, 231, 202, 0.16);
	--reflection-card: rgba(255, 255, 255, 0.08);
	--reflection-card-strong: rgba(255, 255, 255, 0.14);
	--reflection-white: #FFFFFF;
	--reflection-text: var(--reflection-white);
	--reflection-muted: #C5C6C7;
	--reflection-soft-text: #D2E7CA;
	--reflection-accent: #A7C796;
	--reflection-accent-contrast: #66676D;
	--reflection-border: rgba(255, 255, 255, 0.22);
	--reflection-border-soft: rgba(255, 255, 255, 0.12);
	--reflection-overlay: rgba(102, 103, 109, 0.94);
	--reflection-hotspot-ring: rgba(167, 199, 150, 0.22);
}
html { scroll-behavior: auto; }
body { overflow: hidden; background: var(--reflection-bg); }
.reci-stage {
	position: fixed;
	inset: 0;
	display: none;
	opacity: 0;
	transition: opacity 0.7s ease;
	background: var(--reflection-bg);
}
.reci-stage-shell {
	width: 100%;
	min-height: 100vh;
	display: flex;
	align-items: stretch;
}
.reci-stage-body {
	width: 100%;
	max-width: 1440px;
	margin: 0 auto;
	padding: 6rem 1.25rem 4.75rem;
	display: flex;
	flex-direction: column;
	justify-content: center;
}
.reci-continue {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	padding: 0.95rem 1.35rem;
	border-radius: 999px;
	border: 1px solid var(--reflection-border);
	background: var(--reflection-card);
	color: var(--reflection-text);
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: 0.1em;
	cursor: pointer;
}
.reci-stage-grid {
	display: grid;
	gap: 1.5rem;
}
.reci-scroll-panel {
	max-height: min(72vh, 820px);
	overflow-y: auto;
	padding-right: 0.5rem;
}
.reci-scroll-panel::-webkit-scrollbar { width: 8px; }
.reci-scroll-panel::-webkit-scrollbar-thumb { background: var(--reflection-border); border-radius: 999px; }
.reci-timeline-world {
	height: 100vh;
	width: 300vw;
	display: flex;
	transform: translateX(0);
	transition: transform 0.85s cubic-bezier(0.23, 1, 0.32, 1);
	will-change: transform;
}
.reci-timeline-panel {
	width: 100vw;
	height: 100vh;
	flex-shrink: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 6rem 1.25rem 4.75rem;
	position: relative;
	border-right: 1px solid var(--reflection-border-soft);
}
.reci-timeline-card {
	width: min(1120px, 100%);
	background: linear-gradient(180deg, var(--reflection-card-strong), var(--reflection-card));
	border: 1px solid var(--reflection-border-soft);
	border-radius: 2rem;
	padding: 2rem;
	box-shadow: 0 24px 60px rgba(0,0,0,0.18);
}
.reci-timeline-controls {
	position: absolute;
	left: 50%;
	bottom: 3.25rem;
	transform: translateX(-50%);
	display: flex;
	gap: 0.85rem;
	z-index: 10;
}
.reci-icon-btn {
	width: 3.1rem;
	height: 3.1rem;
	border-radius: 999px;
	border: 1px solid var(--reflection-border);
	background: var(--reflection-card);
	color: var(--reflection-text);
	cursor: pointer;
}
.reci-stage-panels {
	display: grid;
	grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
	gap: 1.5rem;
	align-items: stretch;
}
.reci-panel-scroll {
	max-height: min(72vh, 820px);
	overflow-y: auto;
	padding-right: 0.5rem;
}
.reci-panel-scroll::-webkit-scrollbar { width: 8px; }
.reci-panel-scroll::-webkit-scrollbar-thumb { background: var(--reflection-border); border-radius: 999px; }
.reci-stage-menu { display: grid; gap: 1rem; }
.reci-stage-menu button.active { background: var(--reflection-accent); color: var(--reflection-accent-contrast); border-color: transparent; }
.panel-hotspot {
	position: absolute;
	width: 22px;
	height: 22px;
	border-radius: 999px;
	border: 2px solid rgba(255,255,255,0.9);
	background: rgba(167, 199, 150, 0.95);
	color: #1a1713;
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
.panel-hotspot.active { background: white; box-shadow: 0 0 0 10px rgba(255,255,255,0.08); }
.annotation-chip.active { border-color: var(--reflection-accent) !important; background: rgba(167, 199, 150, 0.18) !important; }
@media (max-width: 1024px) {
	.reci-stage-body,
	.reci-timeline-panel { padding-top: 5.5rem; }
	.reci-stage-panels,
	.reci-stage-grid { grid-template-columns: 1fr; }
	.reci-timeline-card { padding: 1.5rem; }
}
CSS);

wp_enqueue_script('reci-reflection-stage-controller', $controller_uri, [], $controller_ver, true);
wp_enqueue_script('reci-reflection-we-humans', $script_uri, ['reci-reflection-stage-controller'], $script_ver, true);
wp_add_inline_script(
	'reci-reflection-we-humans',
	'window.RECIReflectionConfig = ' . wp_json_encode([
		'isLoggedIn' => is_user_logged_in(),
		'restUrl' => esc_url_raw(rest_url('reci/v1/reflection-responses')),
		'nonce' => wp_create_nonce('wp_rest'),
		'reflectionId' => is_singular('reci_reflection') ? get_queried_object_id() : 0,
		'currentUser' => is_user_logged_in() ? [
			'id' => get_current_user_id(),
			'name' => wp_get_current_user()->display_name,
		] : null,
	]) . ';',
	'before'
);

$menu_items = [
	['title' => 'Origins', 'description' => 'How “We Humans” came to be.', 'stage' => 'wh-origins'],
	['title' => 'Display + Reception', 'description' => 'Where it was seen and how people responded.', 'stage' => 'wh-timeline'],
	['title' => 'The Panels', 'description' => 'Read the eight original “We Humans” panels.', 'stage' => 'wh-panels'],
	['title' => 'Reflection Today', 'description' => 'Consider the exhibit’s lessons and limits.', 'stage' => 'wh-reflection'],
	['title' => 'About', 'description' => 'Credits, acknowledgements, and sources.', 'stage' => 'wh-about'],
];

$setup_one = '“We Humans” was an exhibit on race and racism developed by two curators of anthropology employed at the Carnegie Museum (now Carnegie Museum of Natural History), James Swauger and Don Dragoo. Through punchy rhetoric informed by current science, Swauger and Dragoo encouraged workers, students, and citizens to question their assumptions about race and to value the lives and contributions of all people. The exhibit debuted in downtown Pittsburgh in 1955 and later reached a national audience through portable versions and publications. The exhibit was a collaborative effort, jointly planned and sponsored by the museum, the labor union the United Steelworkers of America, Mayor David L. Lawrence’s Civic Unity Council, and Pittsburgh Public Schools.';
$setup_two = '“We Humans” demonstrates the extent to which a version of anti-racism was being made an urgent public priority across the United States in the 1950s, but also shows the pitfalls of the institutional and scientific tactics employed in such efforts at this time. As you learn more about the story of “We Humans,” ask yourself what its ambitions and shortcomings might have to teach people today. What feels familiar and unfamiliar about this story?';
$setup_note = 'Please note that this exhibit includes racial terminology and imagery that are outdated and offensive.';

$origins_sections = [
	[
		'title' => 'United Steelworkers of America',
		'paragraphs' => [
			'The Civil Rights Committee of the United Steelworkers of America (USW) was established in 1948 to address racial and ethnic discrimination in employment. In its early years, under the leadership of brothers Thomas and Francis Shane, the core activities of the Committee included organizing a series of seminars where leaders in organized labor, formal and informal education, religion, and professors of Anthropology, Psychology and related subjects gathered to debate and strategize about how to mitigate prejudice in both the workplace and in society, more broadly.',
			'Carnegie Museum Curator of Anthropology James Swauger was invited to a USW “Seminar on Human Relations” in 1951 at The Pennsylvania State College to speak about how museums and Anthropology collections could help in the Committee’s efforts. In a move he later repeated in “We Humans,” he brought materials from the Museum’s Anthropology collections to the seminar that had been made by diverse cultural groups. Presenting them to the seminar participants without any identifying information, he challenged people to racially label or judge the materials. Swauger’s participation in this seminar led to further collaborations between United Steelworkers and the Museum that eventually created the opportunity for “We Humans.”',
		],
		'links' => [
			['href' => reci_we_humans_asset('Seminar on Human Relations (1951) - Front matter and list of participants.pdf'), 'label' => 'Read the opening remarks and participant list from the October 1951 seminar'],
		],
	],
	[
		'title' => 'UNESCO Statements on Race',
		'paragraphs' => [
			'In December 1949, a group of mostly anthropologists gathered in Paris to discuss and draft what became the first UNESCO Statement on Race of 1950. Similar to the work being done in the same years by United Steelworkers on a more local scale, the United Nations Educational, Scientific and Cultural Organization brought these scholars together toward the aim of creating an official scientific statement on race with the aim of eliminating racial prejudice through knowledge.',
			'The status of racial categorization in Anthropology at this moment was shifting. Franz Boas and his students, for example, were challenging race science and Anthropology’s role in supporting it by attempting to disprove notions of racial superiority or inferiority. However, many of these same anthropologists still believed in biological, racial categorization based on morphological comparison, skull measuring and other practices of physical anthropology that are now broadly dismissed by the discipline as part of a scientifically inaccurate and socially harmful history.',
			'Some of the core messages of the UNESCO Statement, and later of “We Humans,” were that humans were one species, that races were identifiable groups of humans, and that race did not correspond to intelligence or to national, religious, or cultural categories. In turn, racism was cast as a dangerous social myth that could be given less power through scientific knowledge. Thus, the 1950 UNESCO statement, and later “We Humans,” combined messages about human equality that were progressive and controversial in their own time alongside ideas about the biological basis of racial categories that today are out of date and offensive.',
		],
		'links' => [
			['href' => reci_we_humans_asset('Klineberg to Swauger.pdf'), 'label' => 'Open Klineberg to Swauger'],
			['href' => reci_we_humans_asset('Roosevelt to Swauger.pdf'), 'label' => 'Open Roosevelt to Swauger'],
		],
	],
];

$timeline_items = [
	[
		'title' => 'February 1955',
		'body' => '“We Humans” goes on view in the City-County Building in downtown Pittsburgh. Throughout that year, a total of four display cases arrived on a staggered schedule to keep new visitors to the exhibit interested.',
	],
	[
		'title' => 'July 1955',
		'body' => 'An exchange of ideas in the Pittsburgh Sun-Telegraph from the first summer when the exhibit was on display downtown demonstrates the friction it caused. “Are You Ethnocentric?” praised the exhibit for encouraging audiences to question the belief that one’s own identity group is better than all others. Multiple readers published responses in which they doubled down on their sense of superiority, and some suggested that the anti-ethnocentric message of “We Humans” would lead to Communism.',
		'media' => [
			['src' => reci_we_humans_asset('Are you ethnocentric - Pittsburgh_Sun_Telegraph_1955_07_08_16.jpg'), 'alt' => 'Are You Ethnocentric? editorial', 'caption' => '“Are you Ethnocentric?” Pittsburgh Sun-Telegraph, July 8, 1955. From newspapers.com.'],
			['src' => reci_we_humans_asset('Ethnocentric - Pittsburgh_Sun_Telegraph_1955_07_15_14.jpg'), 'alt' => 'Ethnocentric response', 'caption' => '“Ethnocentric.” Pittsburgh Sun-Telegraph, July 15, 1955. From newspapers.com.'],
		],
	],
	[
		'title' => 'February 1956',
		'body' => '“We Humans” begins a tour through Pittsburgh Public Schools, as well as parochial and regional schools. A modified, portable version of the exhibit is created for this purpose, which would be hosted by junior and high schools and taught as a module in social studies classes. A booklet was also produced, paid for by United Steelworkers, to circulate the exhibit’s content more widely.',
		'media' => [
			['src' => reci_we_humans_asset('We Humans - Case II, Panel A, portable version.jpg'), 'alt' => 'Portable We Humans panel', 'caption' => 'A portable “We Humans” panel. Courtesy of Carnegie Museum of Natural History Library and Archives.'],
		],
		'link' => ['href' => reci_we_humans_asset('Comments from teachers.pdf'), 'label' => 'Read responses from teachers and students at Schenley High School'],
	],
];

$panels = [
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case I, Panel A.jpg'), 'alt' => 'We Humans - Case I, Panel A', 'title' => 'Case I, Panel A', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case I, Panel B.jpg'), 'alt' => 'We Humans - Case I, Panel B', 'title' => 'Case I, Panel B', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case II, Panel A.jpg'), 'alt' => 'We Humans - Case II, Panel A', 'title' => 'Case II, Panel A', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case II, Panel B.jpg'), 'alt' => 'We Humans - Case II, Panel B', 'title' => 'Case II, Panel B', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case III, Panel A.jpg'), 'alt' => 'We Humans - Case III, Panel A', 'title' => 'Case III, Panel A', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case III, Panel B.jpg'), 'alt' => 'We Humans - Case III, Panel B', 'title' => 'Case III, Panel B', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case IV, Panel A.jpg'), 'alt' => 'We Humans - Case IV, Panel A', 'title' => 'Case IV, Panel A', 'description' => 'Click to enlarge and read the panel more closely.'],
	['src' => reci_we_humans_asset('Eight original panels/We Humans - Case IV, Panel B.jpg'), 'alt' => 'We Humans - Case IV, Panel B', 'title' => 'Case IV, Panel B', 'description' => 'Click to enlarge and read the panel more closely.'],
];

$analysis_cards = [
	['title' => 'Why have we forgotten this moment?', 'body' => 'What does it mean that this public educational effort is not better remembered, even though it once carried institutional force and civic urgency?'],
	['title' => 'Intent versus impact', 'body' => 'How should we evaluate a project that challenged prejudice while also relying on harmful concepts of race?'],
	['title' => 'Race is not just a dangerous idea', 'body' => 'The story asks visitors to think beyond myth and language to the structural realities that shape everyday life.'],
	['title' => 'How the science has changed', 'body' => 'What does later anthropology make visible about the exhibit’s limitations, and what does that reveal about our own present?'],
];

$credits_items = [
	['title' => 'About this exhibit', 'paragraphs' => ['This exhibit was developed in 2025-2026 by Deirdre Madeleine Smith, a Teaching Assistant Professor in the History of Art and Architecture Department at University of Pittsburgh and Curator of Museum Studies and Art at Carnegie Museum of Natural History. Deirdre is a historian of modern and contemporary art and visual culture with an interest in how art and exhibitions distinctively mediate philosophical and ethical discussions.', 'An earlier version of this exhibit was hosted in the Hyland Gallery at Hillman Library on the University of Pittsburgh campus, co-curated by student intern Lindsey Kenny and co-organized with University of Pittsburgh Library System staff Megan Massanelli, Archives & Special Collections Engagement and Outreach Librarian, and Madeleine Chesek-Welch, Preservation Program Manager.']],
	['title' => 'Thanks to the following individuals', 'paragraphs' => ['Jenise Brown, Marie Corrado, Amy Covell-Murthy, Sarah Crawford, Sydney Dominick, Christopher Fleisher, Kristina Gaugler, Laurie Giarratani, Ron Idoko, Morgan Riggenbach, Keirstin Rotharmel, Ellen Sanin, Rachel Thomas-Beckel, Breann Thompson, Annick Vuissoz, Ginger White, and Gina Winstead.']],
	['title' => 'Archives', 'paragraphs' => ['Carnegie Museum of Natural History Section of Anthropology Archives; Carnegie Museum of Natural History Library & Archives; Heinz History Center - Pittsburgh Public Schools Records and Photographs; University of Pittsburgh Library System Archives & Special Collections - Francis C. Shane Papers, 1942-1969.']],
	['title' => 'Articles & Books', 'paragraphs' => ['Anthony Hazard, <em>Postwar Anti-racism: The U.S., Unesco and “Race,” 1945-1968</em>; James B. Stewart, “Civil Rights and Organized Labor: The Case of the United Steelworkers of America”; Tracy Teslow, <em>Constructing Race: The Science of Bodies and Cultures in American Anthropology</em>; Joe William Trotter Jr. and Jared N. Day, <em>Race and Renaissance: African Americans in Pittsburgh since World War II</em>.']],
];

get_header('reflection');

get_template_part(
	'template-parts/reflections/menu-overlay',
	null,
	[
		'back_url' => home_url('/reflections/'),
		'variant' => 'exhibit',
		'items' => array_map(
			static fn($item) => [
				'title' => $item['title'],
				'description' => $item['description'],
				'href' => '#',
				'attributes' => ['data-stage-target' => $item['stage']],
			],
			$menu_items
		),
	]
);

get_template_part(
	'template-parts/reflections/hero',
	null,
	[
		'id' => 'wh-hero',
		'eyebrow' => 'Digital Exhibit',
		'title' => '“We Humans”',
		'subtitle' => 'Educating Pittsburgh on Race in the 1950s',
		'body' => 'In the years following World War II, global shock over the events of the Holocaust, growing demands to end legal and social race-based discrimination, and shifting ideas in science combined to create a charged and consequential environment for social change. This digital exhibit shares one story of how civic, labor, and education leaders in Pittsburgh, Pennsylvania responded to that moment.',
		'caption' => 'Students hearing the “We Humans” curriculum, Monongahela High School, Monongahela City, 1959. Photo by Michel Chalufour, photographer. Courtesy of Carnegie Museum of Natural History, Library and Archives.',
		'background_image' => reci_we_humans_asset('We Humans - Students hearing the curriculum, 1959.jpg'),
		'variant' => 'documentary',
		'section_class' => 'reci-stage',
		'section_attributes' => ['data-stage' => 'wh-hero'],
		'actions' => [
			['href' => '#', 'label' => 'Enter Exhibit', 'class' => 'bg-[var(--reflection-accent)] text-[var(--reflection-accent-contrast)]', 'attributes' => ['data-stage-target' => 'wh-setup-1']],
			['href' => '#', 'label' => 'View the Panels', 'class' => 'border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] text-[var(--reflection-text)]', 'attributes' => ['data-stage-target' => 'wh-panels']],
		],
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-feature-split',
	null,
	[
		'variant' => 'documentary',
		'id' => 'wh-setup-1',
		'eyebrow' => 'Introduction',
		'title' => 'A public educational intervention',
		'body' => $setup_one,
		'image' => reci_we_humans_asset('We Humans - Students hearing the curriculum, 1959.jpg'),
		'image_alt' => 'Students hearing the We Humans curriculum in 1959',
		'caption' => 'Students hearing the “We Humans” curriculum, Monongahela High School, Monongahela City, 1959. Courtesy of Carnegie Museum of Natural History, Library and Archives.',
		'media_side' => 'left',
		'continue_label' => 'Continue',
		'continue_target' => 'wh-setup-2',
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-feature-split',
	null,
	[
		'variant' => 'documentary',
		'id' => 'wh-setup-2',
		'eyebrow' => 'Framing the Exhibit',
		'title' => 'Ambition and contradiction',
		'body' => $setup_two,
		'note' => $setup_note,
		'image' => reci_we_humans_asset('We Humans - Case II, Panel A, portable version.jpg'),
		'image_alt' => 'Portable We Humans panel',
		'caption' => 'A portable “We Humans” panel. Courtesy of Carnegie Museum of Natural History Library and Archives.',
		'media_side' => 'right',
		'continue_label' => 'Choose a Path Through the Exhibit',
		'continue_target' => 'wh-menu',
	]
);
?>
<section class="reci-stage" id="wh-menu" data-stage="wh-menu">
	<div class="reci-stage-shell">
		<div class="reci-stage-body items-center justify-center text-center">
			<div class="mx-auto max-h-[calc(100vh-8.5rem)] w-full max-w-[880px] overflow-y-auto rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6 sm:p-8 lg:p-10">
				<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]">Navigate the Exhibit</div>
				<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-[0.95] text-[var(--reflection-text)] sm:text-5xl lg:text-[3.4rem]">Choose a chapter to begin</h2>
				<p class="mx-auto mt-4 max-w-[44rem] text-[0.95rem] leading-7 text-[var(--reflection-soft-text)] sm:text-base sm:leading-8">Move through the exhibit in the order laid out in the curatorial narrative, or jump to a section to study a particular part of the story.</p>
				<div class="reci-stage-menu mt-6 sm:grid-cols-2">
					<?php foreach ($menu_items as $item) : ?>
						<button class="min-h-[8.5rem] rounded-[1.5rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] px-5 py-5 text-left transition hover:bg-[var(--reflection-card)] hover:shadow-[0_18px_40px_rgba(0,0,0,0.14)]" type="button" data-stage-target="<?php echo esc_attr($item['stage']); ?>">
							<strong class="mb-2 block font-['Oswald'] text-sm uppercase tracking-[0.08em] text-[var(--reflection-accent)]"><?php echo esc_html($item['title']); ?></strong>
							<span class="block text-sm leading-7 text-[var(--reflection-soft-text)] sm:text-[0.95rem]"><?php echo esc_html($item['description']); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_template_part(
	'template-parts/reflections/chapters/chapter-documentary-dossier',
	null,
	[
		'variant' => 'archival',
		'id' => 'wh-origins',
		'eyebrow' => 'How did “We Humans” come to be?',
		'title' => 'From postwar urgency to a public exhibit on race',
		'intro' => [
			'“We Humans” emerged as one of many responses to local, national and global conversations about race, racism and discrimination taking place in the 1950s. Civic, labor, religious and community leaders, as well as academics collaborated to counter anti-semitism and Nazi race science, as well as anti-Black racism and segregation.',
			'Of particular relevance to the development of “We Humans” were the discussions being convened by the Committee on Civil Rights of the United Steelworkers of America around the country, and the first UNESCO Statement on Race from 1950.',
		],
		'sections' => $origins_sections,
		'continue_label' => 'Move to Display + Reception',
		'continue_target' => 'wh-timeline',
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-timeline-world',
	null,
	[
		'id' => 'wh-timeline',
		'eyebrow' => 'Where was “We Humans” displayed and what did people say about it?',
		'items' => $timeline_items,
		'continue_label' => 'Go to The Panels',
		'continue_target' => 'wh-panels',
		'variant' => 'horizontal',
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-panel-explorer',
	null,
	[
		'id' => 'wh-panels',
		'eyebrow' => 'What was actually in “We Humans”?',
		'title' => 'The eight original panels',
		'intro' => 'This chapter centers the eight original “We Humans” panels. Enlarge each one and use the annotations to examine how the exhibit framed race, equality, and public education.',
		'items' => $panels,
		'continue_label' => 'Continue to Reflection',
		'continue_target' => 'wh-reflection',
		'variant' => 'documentary',
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-reflection-prompt',
	null,
	[
		'id' => 'wh-reflection',
		'eyebrow' => 'How should we think and feel about “We Humans” today?',
		'title' => 'Ambition, limitation, and historical memory',
		'intro' => 'As you learn more about the story of “We Humans,” ask what its ambitions and shortcomings might have to teach people today. What feels familiar and unfamiliar about this story? What does it mean to revisit a form of anti-racism that was urgent in its time but tethered to institutional and scientific assumptions we now reject?',
		'cards' => $analysis_cards,
		'prompt' => 'What will people in 70 years say about the anti-racist education and initiatives of your moment?',
		'continue_label' => 'Continue to About',
		'continue_target' => 'wh-about',
		'variant' => 'journal',
	]
);

get_template_part(
	'template-parts/reflections/chapters/chapter-about',
	null,
	[
		'id' => 'wh-about',
		'eyebrow' => 'About this exhibit',
		'title' => 'Credits, thanks, and sources consulted',
		'intro' => 'This closing chapter follows the source document directly and keeps the curatorial context visible inside the exhibit itself.',
		'items' => $credits_items,
		'continue_label' => 'Return to Hero',
		'continue_target' => 'wh-hero',
		'variant' => 'documentary',
	]
);

get_template_part('template-parts/reflections/annotated-lightbox', null, ['variant' => 'annotated']);
get_footer('reflection');
