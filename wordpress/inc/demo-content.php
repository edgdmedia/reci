<?php

/**
 * RECI Media Hub — Demo Content Installer.
 *
 * Provides a one-click demo content importer available in
 * Appearance → RECI Settings → Demo.
 *
 * - Idempotent: skips posts that already exist (matched by slug).
 * - Attaches theme-bundled images to posts as featured images.
 * - Creates taxonomy terms (topics, locations) before posts.
 * - Provides a reset handler that removes only demo-flagged posts.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Admin-post handlers
// ---------------------------------------------------------------------------

add_action( 'admin_post_reci_install_demo', 'reci_handle_install_demo' );
add_action( 'admin_post_reci_reset_demo',   'reci_handle_reset_demo' );

if ( ! function_exists( 'reci_demo_content_types' ) ) {
	function reci_demo_content_types(): array {
		return [
			'reci_article'   => [ 'label' => 'Articles',     'count' => 6 ],
			'reci_podcast'   => [ 'label' => 'Podcasts',     'count' => 3 ],
			'reci_video'     => [ 'label' => 'Videos',       'count' => 3 ],
			'reci_event'     => [ 'label' => 'Events',       'count' => 5 ],
			'reci_reflection'=> [ 'label' => 'Reflections',  'count' => 3 ],
			'reci_quote'     => [ 'label' => 'Quotes',       'count' => 3 ],
			'reci_assessment'=> [ 'label' => 'Quizzes',      'count' => 5 ],
			'reci_course'    => [ 'label' => 'Courses',      'count' => 2 ],
			'reci_testimonial' => [ 'label' => 'Testimonials','count' => 4 ],
			'reci_page'      => [ 'label' => 'Core Pages',   'count' => 10 ],
		];
	}
}

function reci_handle_install_demo(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}
	check_admin_referer( 'reci_demo_action' );

	$selected = isset( $_POST['reci_demo_types'] ) && is_array( $_POST['reci_demo_types'] )
		? array_map( 'sanitize_key', $_POST['reci_demo_types'] )
		: [];

	reci_install_demo_content( $selected );

	wp_safe_redirect( add_query_arg( [
		'page'         => 'reci-settings',
		'tab'          => 'demo',
		'demo_notice'  => 'installed',
	], admin_url( 'themes.php' ) ) );
	exit;
}

function reci_handle_reset_demo(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}
	check_admin_referer( 'reci_demo_action' );

	reci_reset_demo_content();

	wp_safe_redirect( add_query_arg( [
		'page'         => 'reci-settings',
		'tab'          => 'demo',
		'demo_notice'  => 'reset',
	], admin_url( 'themes.php' ) ) );
	exit;
}

// ---------------------------------------------------------------------------
// Core installer
// ---------------------------------------------------------------------------

function reci_install_demo_content( array $only_types = [] ): void {
	$all  = array_keys( reci_demo_content_types() );
	$want = $only_types ? array_intersect( $only_types, $all ) : $all;
	if ( empty( $want ) ) {
		return;
	}

	$do_all = $want === $all;

	// Taxonomy terms first.
	$topics = reci_demo_ensure_terms( 'reci_topic', [
		'Systemic Racism',
		'Intersectionality',
		'Cultural Identity',
		'Workplace Equity',
		'Community Action',
		'Education',
		'Health Disparities',
		'Criminal Justice',
		'Indigenous Rights',
		'Technology & Equity',
	] );

	$locations = reci_demo_ensure_terms( 'reci_location', [
		'Pittsburgh',
		'Allegheny County',
		'Pennsylvania',
		'National',
	] );

	// Image map: file basename (in assets/images/) => attachment ID (lazily sideloaded).
	$imgs = [];

	// Articles.
	$articles = [
		[
			'slug'    => 'reci-demo-understanding-systemic-racism',
			'title'   => 'Understanding Systemic Racism: A Comprehensive Overview',
			'excerpt' => 'An in-depth look at how systemic racism operates across institutions and how communities are working to dismantle it.',
			'content' => reci_demo_lorem( 4 ),
			'topics'  => [ 'Systemic Racism', 'Criminal Justice' ],
			'image'   => 'Image.png',
			'meta'    => [ '_reci_read_time' => '8 min read' ],
		],
		[
			'slug'    => 'reci-demo-equity-education-pipeline',
			'title'   => 'Building an Equity-Centered Education Pipeline',
			'excerpt' => 'How universities and K-12 schools are partnering to create pathways that address racial disparities in education.',
			'content' => reci_demo_lorem( 4 ),
			'topics'  => [ 'Education', 'Systemic Racism' ],
			'image'   => 'Image2.png',
			'meta'    => [ '_reci_read_time' => '6 min read' ],
		],
		[
			'slug'    => 'reci-demo-pittsburgh-community-pulse',
			'title'   => 'Community Pulse: Pittsburgh\'s Racial Justice Landscape in 2025',
			'excerpt' => 'A data-driven snapshot of where Pittsburgh stands on key racial equity indicators and what\'s changing.',
			'content' => reci_demo_lorem( 4 ),
			'topics'  => [ 'Community Action', 'Technology & Equity' ],
			'image'   => 'Image3.png',
			'meta'    => [ '_reci_read_time' => '5 min read' ],
		],
		[
			'slug'    => 'reci-demo-health-equity-research',
			'title'   => 'New Research Highlights Health Disparities Across Allegheny County',
			'excerpt' => 'The latest findings from the RECI Health Equity Lab reveal persistent gaps that demand immediate policy response.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Health Disparities', 'Technology & Equity' ],
			'image'   => 'Image4.png',
			'meta'    => [ '_reci_read_time' => '7 min read' ],
		],
		[
			'slug'    => 'reci-demo-arts-culture-healing',
			'title'   => 'Arts, Culture, and Community Healing After Racial Trauma',
			'excerpt' => 'How creative expression is becoming a key vehicle for healing, advocacy, and systemic change in Black communities.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Cultural Identity', 'Community Action' ],
			'image'   => 'Image5.png',
			'meta'    => [ '_reci_read_time' => '4 min read' ],
		],
		[
			'slug'    => 'reci-demo-leadership-equity',
			'title'   => 'Cultivating Equity-Minded Leaders at Every Level',
			'excerpt' => 'From classrooms to boardrooms, RECI\'s leadership programs are changing who gets to lead and how.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Workplace Equity', 'Education' ],
			'image'   => 'Section.png',
			'meta'    => [ '_reci_read_time' => '5 min read' ],
		],
	];

	if ( in_array( 'reci_article', $want, true ) ) {
		foreach ( $articles as $d ) {
			reci_demo_insert_post( 'reci_article', $d, $topics, $locations, $imgs );
		}
	}

	// Podcasts.
	$podcasts = [
		[
			'slug'    => 'reci-demo-podcast-voices-of-equity',
			'title'   => 'Voices of Equity — Episode 1: Why Race Still Matters',
			'excerpt' => 'Our inaugural episode tackles the foundational question of why race remains central to policy, education, and opportunity.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Systemic Racism', 'Criminal Justice' ],
			'image'   => 'connect-now.png',
			'meta'    => [
				'_reci_podcast_audio_url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
				'_reci_podcast_duration_label' => '42 min',
				'_reci_podcast_duration_secs'  => 2520,
			],
		],
		[
			'slug'    => 'reci-demo-podcast-education-roundtable',
			'title'   => 'Voices of Equity — Episode 2: Education Roundtable',
			'excerpt' => 'Three educators share what it looks like to build truly anti-racist curricula from the ground up.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Education', 'Workplace Equity' ],
			'image'   => 'connect-now2.png',
			'meta'    => [
				'_reci_podcast_audio_url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
				'_reci_podcast_duration_label' => '38 min',
				'_reci_podcast_duration_secs'  => 2280,
			],
		],
		[
			'slug'    => 'reci-demo-podcast-health-equity',
			'title'   => 'Voices of Equity — Episode 3: The Health Equity Crisis',
			'excerpt' => 'Dr. Simone Barnes joins us to discuss the lived experience behind the data on racial health disparities.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Health Disparities', 'Technology & Equity' ],
			'image'   => 'connect-now3.png',
			'meta'    => [
				'_reci_podcast_audio_url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
				'_reci_podcast_duration_label' => '51 min',
				'_reci_podcast_duration_secs'  => 3060,
			],
		],
	];

	if ( in_array( 'reci_podcast', $want, true ) ) {
		foreach ( $podcasts as $d ) {
			reci_demo_insert_post( 'reci_podcast', $d, $topics, $locations, $imgs );
		}
	}

	// Videos.
	$videos = [
		[
			'slug'    => 'reci-demo-video-racial-equity-explained',
			'title'   => 'Racial Equity Explained in 5 Minutes',
			'excerpt' => 'A concise animated explainer breaking down the difference between equality, equity, and justice.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Systemic Racism' ],
			'image'   => 'Image.png',
			'meta'    => [
				'_reci_video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'_reci_video_duration' => '5:12',
			],
		],
		[
			'slug'    => 'reci-demo-video-community-stories',
			'title'   => 'Community Stories: Voices from Pittsburgh\'s Hill District',
			'excerpt' => 'Residents share their experiences with racial inequity and the community-led solutions they\'re building.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Community Action', 'Cultural Identity' ],
			'image'   => 'Image2.png',
			'meta'    => [
				'_reci_video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'_reci_video_duration' => '12:34',
			],
		],
		[
			'slug'    => 'reci-demo-video-policy-change',
			'title'   => 'From Research to Policy: Making Equity Stick',
			'excerpt' => 'How RECI scholars are translating their research into concrete policy wins at the city and state level.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Criminal Justice', 'Technology & Equity' ],
			'image'   => 'Image3.png',
			'meta'    => [
				'_reci_video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'_reci_video_duration' => '18:05',
			],
		],
	];

	if ( in_array( 'reci_video', $want, true ) ) {
		foreach ( $videos as $d ) {
			reci_demo_insert_post( 'reci_video', $d, $topics, $locations, $imgs );
		}
	}

	// Events — real CRSP/RECI events from Pitt calendar with correct meta keys.
	$events = [
		[
			'slug'    => 'reci-demo-event-cohort-session-14',
			'title'   => 'RECI Spring Cohort — Session 14: Gauging Racial Inequities',
			'excerpt' => 'Explore frameworks for measuring racial inequities and championing racial justice in this penultimate session of the RECI Virtual Spring Cohort.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Systemic Racism', 'Workplace Equity' ],
			'image'   => 'site/events/national-day-racial-healing.jpg',
			'meta'    => [
				'_reci_event_start_date'       => '2026-06-04',
				'_reci_event_end_date'         => '2026-06-04',
				'_reci_event_start_time'       => '11:30 AM',
				'_reci_event_end_time'         => '1:00 PM',
				'_reci_event_timezone'         => 'EDT',
				'_reci_event_is_virtual'       => '1',
				'_reci_event_location_name'    => 'Virtual — Zoom',
				'_reci_event_registration_url' => '',
				'_reci_event_cta_label'        => 'Learn More',
			],
		],
		[
			'slug'    => 'reci-demo-event-cohort-closing',
			'title'   => 'RECI Spring Cohort — Closing Session',
			'excerpt' => 'Join us for the final session of the RECI Virtual Spring Cohort as we reflect on our journey and celebrate participant achievements.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Community Action', 'Intersectionality' ],
			'image'   => 'site/events/reci-cohort-closing.jpg',
			'meta'    => [
				'_reci_event_start_date'       => '2026-06-11',
				'_reci_event_end_date'         => '2026-06-11',
				'_reci_event_start_time'       => '11:30 AM',
				'_reci_event_end_time'         => '1:00 PM',
				'_reci_event_timezone'         => 'EDT',
				'_reci_event_is_virtual'       => '1',
				'_reci_event_location_name'    => 'Virtual — Zoom',
				'_reci_event_registration_url' => '',
				'_reci_event_cta_label'        => 'Learn More',
			],
		],
		[
			'slug'    => 'reci-demo-event-racial-healing',
			'title'   => 'National Day of Racial Healing',
			'excerpt' => 'A meaningful gathering in recognition of the National Day of Racial Healing, featuring collective reflection and dialogue.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Community Action', 'Cultural Identity' ],
			'image'   => 'site/events/national-day-racial-healing.jpg',
			'meta'    => [
				'_reci_event_start_date'       => '2026-01-20',
				'_reci_event_end_date'         => '2026-01-20',
				'_reci_event_start_time'       => '11:00 AM',
				'_reci_event_end_time'         => '1:00 PM',
				'_reci_event_timezone'         => 'EST',
				'_reci_event_is_virtual'       => '0',
				'_reci_event_location_name'    => "O'Hara Student Center, Ballroom",
				'_reci_event_location_address' => "3900 O'Hara Street, Pittsburgh, PA 15260",
			],
		],
		[
			'slug'    => 'reci-demo-event-human-flourishing',
			'title'   => 'Human Flourishing Symposium',
			'excerpt' => 'A transformative symposium exploring pathways to thriving — shifting from deficit to possibility in community well-being.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Health Disparities', 'Community Action' ],
			'image'   => 'site/events/human-flourishing-symposium.jpg',
			'meta'    => [
				'_reci_event_start_date'       => '2026-03-23',
				'_reci_event_end_date'         => '2026-03-23',
				'_reci_event_start_time'       => '9:00 AM',
				'_reci_event_end_time'         => '3:00 PM',
				'_reci_event_timezone'         => 'EDT',
				'_reci_event_is_virtual'       => '0',
				'_reci_event_location_name'    => 'Alumni Hall, Connolly Ballroom',
				'_reci_event_location_address' => '4227 Fifth Avenue, Pittsburgh, PA 15260',
			],
		],
		[
			'slug'    => 'reci-demo-event-refugee-strategies',
			'title'   => 'From Minneapolis to Pittsburgh: Supporting Refugee & Immigrant Neighbors',
			'excerpt' => 'A conversation with front-line supports and organizers on community strategies for supporting refugee and immigrant communities.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Community Action', 'Cultural Identity' ],
			'image'   => 'site/events/minneapolis-refugee.jpg',
			'meta'    => [
				'_reci_event_start_date'       => '2026-04-20',
				'_reci_event_end_date'         => '2026-04-20',
				'_reci_event_start_time'       => '5:00 PM',
				'_reci_event_end_time'         => '7:00 PM',
				'_reci_event_timezone'         => 'EDT',
				'_reci_event_is_virtual'       => '1',
				'_reci_event_location_name'    => 'Cathedral of Learning, Room 2017',
				'_reci_event_location_address' => 'Fifth Ave at Bigelow, Pittsburgh, PA 15213',
				'_reci_event_registration_url' => 'https://pitt.co1.qualtrics.com/jfe/form/SV_difa0J2zKNg3myW',
				'_reci_event_cta_label'        => 'Watch Recording',
			],
		],
	];

	if ( in_array( 'reci_event', $want, true ) ) {
		foreach ( $events as $d ) {
			reci_demo_insert_post( 'reci_event', $d, $topics, $locations, $imgs );
		}
	}

	// Reflections.
	$reflections = [
		[
			'slug'    => 'reci-demo-reflection-teaching-equity',
			'title'   => 'Teaching Equity in a Room Full of Doubt',
			'excerpt' => 'A personal reflection on the resistance, breakthroughs, and moments that make equity education transformative.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Education', 'Workplace Equity' ],
			'image'   => 'reflection.png',
			'meta'    => [],
		],
		[
			'slug'    => 'reci-demo-reflection-first-generation',
			'title'   => 'What It Means to Be First: A First-Generation Student\'s Story',
			'excerpt' => 'Growing up in a household where college was never mentioned, and navigating what it costs to be the first.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Community Action', 'Intersectionality' ],
			'image'   => 'reflection2.png',
			'meta'    => [],
		],
		[
			'slug'    => 'reci-demo-reflection-doctor-black-america',
			'title'   => 'Becoming a Doctor in Black America: A Journey Through Inequity',
			'excerpt' => 'From med school to residency, the invisible barriers that shaped one physician\'s commitment to health equity.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Health Disparities', 'Technology & Equity' ],
			'image'   => 'JohnDoe.png',
			'meta'    => [],
		],
	];

	if ( in_array( 'reci_reflection', $want, true ) ) {
		foreach ( $reflections as $d ) {
			reci_demo_insert_post( 'reci_reflection', $d, $topics, $locations, $imgs );
		}
	}

	// Quotes.
	$quotes = [
		[
			'slug'  => 'reci-demo-quote-racism-virus',
			'title' => 'Racism is a social virus',
			'text'  => 'Racism is a social virus. Racial equity consciousness is the vaccine.',
			'author_name' => 'Ron Idoko',
			'author_title' => 'Founding Director, RECI',
		],
		[
			'slug'  => 'reci-demo-quote-equity-journey',
			'title' => 'The work of racial equity',
			'text'  => 'The work of racial equity is not a destination; it is an ongoing process of reflection, education, and praxis.',
			'author_name' => 'RECI Framework',
			'author_title' => '',
		],
		[
			'slug'  => 'reci-demo-quote-consciousness-action',
			'title' => 'Consciousness is the first step',
			'text'  => 'Consciousness is the first step. But consciousness without action is complicity by another name.',
			'author_name' => 'RECI Cohort Participant',
			'author_title' => '',
		],
	];

	if ( in_array( 'reci_quote', $want, true ) ) {
		foreach ( $quotes as $d ) {
			reci_demo_insert_quote( $d );
		}
	}

	// Assessments.
	$assessments = [
		[
			'slug'    => 'reci-demo-assessment-racial-equity',
			'title'   => 'Racial Equity Foundation Check',
			'excerpt' => 'Evaluate your understanding of racial equity principles and systemic barriers.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Systemic Racism', 'Education' ],
			'image'   => 'Image4.png',
			'meta'    => [
				'_reci_assessment_type' => 'quiz',
				'_reci_assessment_duration' => '5 min',
				'_reci_assessment_questions' => wp_json_encode([
					['id'=>'q1','prompt'=>'What does racial equity primarily aim to achieve?','type'=>'single_choice','options'=>['Equal treatment regardless of outcomes','Fair outcomes by addressing systemic barriers','Preferential treatment for one group','Eliminating cultural differences'],'correct_answers'=>['Fair outcomes by addressing systemic barriers']],
					['id'=>'q2','prompt'=>'True or False: Racial equity and racial equality mean exactly the same thing.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['False']],
					['id'=>'q3','prompt'=>'Which of the following is an example of a racial equity initiative?','type'=>'single_choice','options'=>['Offering identical resources to everyone','Creating mentorship programs for underrepresented groups','Ignoring demographic data in hiring','Reducing workplace diversity training'],'correct_answers'=>['Creating mentorship programs for underrepresented groups']],
					['id'=>'q4','prompt'=>'What is one major barrier to racial equity?','type'=>'single_choice','options'=>['Systemic discrimination','Increased collaboration','Equal access to opportunities','Transparent hiring practices'],'correct_answers'=>['Systemic discrimination']],
					['id'=>'q5','prompt'=>'True or False: Data analysis can help organizations identify racial disparities.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
				]),
				'_reci_assessment_result_ranges' => wp_json_encode([
					['key'=>'needs_support','label'=>'Foundational','min_percent'=>0,'max_percent'=>39,'message'=>'You are beginning to explore the core concepts of racial equity. We recommend starting with our introductory modules to build a stronger baseline understanding of systemic barriers.','recommended_topics'=>['Systemic Racism','Education']],
					['key'=>'developing','label'=>'Developing','min_percent'=>40,'max_percent'=>69,'message'=>'You have a good grasp of basic equity concepts but may benefit from deeper engagement with how systemic discrimination operates in practice.','recommended_topics'=>['Systemic Racism','Community Action']],
					['key'=>'strong','label'=>'Strong','min_percent'=>70,'max_percent'=>100,'message'=>'Great job! You have a solid understanding of racial equity foundations and the importance of fair outcomes over equal treatment.','recommended_topics'=>['Policy and Practice','Leadership']],
				]),
			],
		],
		[
			'slug'    => 'reci-demo-assessment-racial-empathy',
			'title'   => 'Racial Empathy Self-Check',
			'excerpt' => 'A reflective check-in on understanding and valuing interracial experiences.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Cultural Identity', 'Intersectionality' ],
			'image'   => 'Image5.png',
			'meta'    => [
				'_reci_assessment_type' => 'checklist',
				'_reci_assessment_duration' => '5 min',
				'_reci_assessment_questions' => wp_json_encode([
					['id'=>'q1','prompt'=>'Racial empathy refers to:','type'=>'single_choice','options'=>['Avoiding conversations about race','Understanding and valuing experiences of people from different racial backgrounds','Treating everyone exactly the same','Assuming all experiences are identical'],'correct_answers'=>['Understanding and valuing experiences of people from different racial backgrounds']],
					['id'=>'q2','prompt'=>'True or False: Active listening is an important part of racial empathy.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
					['id'=>'q3','prompt'=>'Which action best demonstrates racial empathy in a workplace?','type'=>'single_choice','options'=>['Interrupting discussions about discrimination','Dismissing others’ experiences','Listening without defensiveness','Avoiding team discussions'],'correct_answers'=>['Listening without defensiveness']],
					['id'=>'q4','prompt'=>'Why is racial empathy important?','type'=>'single_choice','options'=>['It reduces communication','It helps build inclusive relationships','It increases stereotypes','It removes the need for policies'],'correct_answers'=>['It helps build inclusive relationships']],
					['id'=>'q5','prompt'=>'True or False: Racial empathy requires agreement with every perspective shared.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['False']],
				]),
				'_reci_assessment_result_ranges' => wp_json_encode([
					['key'=>'low','label'=>'Emerging Empathy','min_percent'=>0,'max_percent'=>39,'message'=>'Racial empathy involves active unlearning and listening. Take time to explore stories and perspectives from backgrounds different from your own.','recommended_topics'=>['Cultural Identity','Intersectionality']],
					['key'=>'mid','label'=>'Developing Empathy','min_percent'=>40,'max_percent'=>79,'message'=>'You are practicing the skills of listening and valuing diverse experiences. Continue focusing on non-defensive listening to further build your racial empathy.','recommended_topics'=>['Intersectionality','Community Action']],
					['key'=>'high','label'=>'High Empathy','min_percent'=>80,'max_percent'=>100,'message'=>'Your responses show a strong commitment to understanding and valuing interracial experiences through active listening and openness.','recommended_topics'=>['Racial Healing','Cultural Identity']],
				]),
			],
		],
		[
			'slug'    => 'reci-demo-assessment-racial-anxiety',
			'title'   => 'Racial Anxiety Awareness',
			'excerpt' => 'Identify discomfort or stress in interracial interactions to build better collaboration.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Workplace Equity', 'Community Action' ],
			'image'   => 'Image2.png',
			'meta'    => [
				'_reci_assessment_type' => 'quiz',
				'_reci_assessment_duration' => '5 min',
				'_reci_assessment_questions' => wp_json_encode([
					['id'=>'q1','prompt'=>'Racial anxiety can best be described as:','type'=>'single_choice','options'=>['Confidence in discussing race-related issues','Discomfort or stress during interracial interactions','A legal policy','A personality trait unrelated to race'],'correct_answers'=>['Discomfort or stress during interracial interactions']],
					['id'=>'q2','prompt'=>'True or False: Fear of saying the wrong thing can contribute to racial anxiety.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
					['id'=>'q3','prompt'=>'Which strategy may help reduce racial anxiety?','type'=>'single_choice','options'=>['Avoiding all conversations about race','Practicing open and respectful dialogue','Ignoring cultural differences','Making assumptions about others'],'correct_answers'=>['Practicing open and respectful dialogue']],
					['id'=>'q4','prompt'=>'Racial anxiety may negatively affect:','type'=>'single_choice','options'=>['Team collaboration','Communication','Workplace trust','All of the above'],'correct_answers'=>['All of the above']],
					['id'=>'q5','prompt'=>'True or False: Education and exposure can help reduce racial anxiety over time.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
				]),
				'_reci_assessment_result_ranges' => wp_json_encode([
					['key'=>'high','label'=>'High Anxiety','min_percent'=>0,'max_percent'=>39,'message'=>'It is common to feel stress in these interactions. Focused practice in open dialogue and exposure to new frameworks can help reduce this anxiety over time.','recommended_topics'=>['Workplace Equity','Racial Healing']],
					['key'=>'mid','label'=>'Developing Confidence','min_percent'=>40,'max_percent'=>79,'message'=>'You are building confidence in interracial settings. Keep leaning into respectful dialogue to continue reducing the fear of "saying the wrong thing."','recommended_topics'=>['Workplace Equity','Community Action']],
					['key'=>'low','label'=>'Interracial Confidence','min_percent'=>80,'max_percent'=>100,'message'=>'You demonstrate a high level of comfort and awareness in interracial interactions, which is key for effective collaboration and trust-building.','recommended_topics'=>['Leadership','Community Action']],
				]),
			],
		],
		[
			'slug'    => 'reci-demo-assessment-implicit-bias',
			'title'   => 'Implicit Bias Reflection',
			'excerpt' => 'Examine unconscious attitudes and stereotypes that affect decision making.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Systemic Racism', 'Workplace Equity' ],
			'image'   => 'Image3.png',
			'meta'    => [
				'_reci_assessment_type' => 'checklist',
				'_reci_assessment_duration' => '5 min',
				'_reci_assessment_questions' => wp_json_encode([
					['id'=>'q1','prompt'=>'Implicit bias refers to:','type'=>'single_choice','options'=>['Intentional discrimination only','Unconscious attitudes or stereotypes that affect decisions','Public policy changes','Formal workplace rules'],'correct_answers'=>['Unconscious attitudes or stereotypes that affect decisions']],
					['id'=>'q2','prompt'=>'True or False: Everyone can have implicit biases.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
					['id'=>'q3','prompt'=>'Which example may reflect implicit bias?','type'=>'single_choice','options'=>['Evaluating all resumes blindly','Assuming someone is less qualified based on their name','Using objective criteria in hiring','Encouraging equal participation'],'correct_answers'=>['Assuming someone is less qualified based on their name']],
					['id'=>'q4','prompt'=>'What can organizations do to reduce implicit bias?','type'=>'single_choice','options'=>['Ignore diversity data','Limit hiring transparency','Provide bias-awareness training','Avoid feedback systems'],'correct_answers'=>['Provide bias-awareness training']],
					['id'=>'q5','prompt'=>'True or False: Implicit bias is always intentional.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['False']],
				]),
				'_reci_assessment_result_ranges' => wp_json_encode([
					['key'=>'low','label'=>'Initial Awareness','min_percent'=>0,'max_percent'=>39,'message'=>'Implicit bias is often unconscious. Identifying these patterns is the first step toward reducing their impact on your decisions and interactions.','recommended_topics'=>['Systemic Racism','Workplace Equity']],
					['key'=>'mid','label'=>'Developing Awareness','min_percent'=>40,'max_percent'=>79,'message'=>'You are learning to spot common biases. Continue engaging with bias-awareness training to further strengthen your decision-making frameworks.','recommended_topics'=>['Workplace Equity','Intersectionality']],
					['key'=>'high','label'=>'Bias Aware','min_percent'=>80,'max_percent'=>100,'message'=>'You have a strong understanding of how unconscious stereotypes function and how to actively mitigate them in professional and personal contexts.','recommended_topics'=>['Intersectionality','Leadership']],
				]),
			],
		],
		[
			'slug'    => 'reci-demo-assessment-allyship',
			'title'   => 'Allyship and Inclusion Check',
			'excerpt' => 'Assess your advocacy and support for marginalized groups in the workplace.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Community Action', 'Workplace Equity' ],
			'image'   => 'Image1.png',
			'meta'    => [
				'_reci_assessment_type' => 'survey',
				'_reci_assessment_duration' => '5 min',
				'_reci_assessment_questions' => wp_json_encode([
					['id'=>'q1','prompt'=>'What is allyship in the context of diversity and inclusion?','type'=>'single_choice','options'=>['Remaining silent during discrimination','Supporting and advocating for marginalized groups','Avoiding workplace discussions','Treating inclusion as optional'],'correct_answers'=>['Supporting and advocating for marginalized groups']],
					['id'=>'q2','prompt'=>'True or False: Inclusive workplaces generally improve employee engagement.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
					['id'=>'q3','prompt'=>'Which behavior reflects effective allyship?','type'=>'single_choice','options'=>['Speaking over underrepresented voices','Challenging discriminatory behavior respectfully','Ignoring exclusionary comments','Avoiding accountability'],'correct_answers'=>['Challenging discriminatory behavior respectfully']],
					['id'=>'q4','prompt'=>'Why is inclusion important in organizations?','type'=>'single_choice','options'=>['It reduces collaboration','It limits innovation','It encourages diverse perspectives and participation','It removes the need for leadership'],'correct_answers'=>['It encourages diverse perspectives and participation']],
					['id'=>'q5','prompt'=>'True or False: Allyship involves continuous learning and action.','type'=>'single_choice','options'=>['True','False'],'correct_answers'=>['True']],
				]),
				'_reci_assessment_result_ranges' => wp_json_encode([
					['key'=>'developing','label'=>'Emerging Ally','min_percent'=>0,'max_percent'=>59,'message'=>'Allyship is a journey of continuous learning. Focus on finding respectful ways to challenge exclusionary behavior when you see it.','recommended_topics'=>['Community Action','Cultural Identity']],
					['key'=>'strong','label'=>'Active Ally','min_percent'=>60,'max_percent'=>100,'message'=>'You demonstrate a commitment to advocating for others and fostering inclusive environments through your actions and learning.','recommended_topics'=>['Leadership','Community Action']],
				]),
			],
		],
	];

	if ( in_array( 'reci_assessment', $want, true ) ) {
		foreach ( $assessments as $d ) {
			reci_demo_insert_post( 'reci_assessment', $d, $topics, $locations, $imgs );
		}
	}

	// Courses.
	$courses = [
		[
			'slug'    => 'reci-demo-course-intro-racial-equity',
			'title'   => 'Introduction to Racial Equity: Foundations and Frameworks',
			'excerpt' => 'A self-paced online course covering the history, theory, and practice of racial equity work. Ideal for beginners.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Systemic Racism', 'Education' ],
			'image'   => 'Section.png',
			'meta'    => [
				'_reci_course_duration' => '4 weeks',
				'_reci_course_level'    => 'Beginner',
			],
		],
		[
			'slug'    => 'reci-demo-course-equity-leadership',
			'title'   => 'Equity-Centered Leadership in Practice',
			'excerpt' => 'An advanced course for organizational leaders ready to embed racial equity into strategy, culture, and operations.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Workplace Equity', 'Criminal Justice' ],
			'image'   => 'donate-banner.png',
			'meta'    => [
				'_reci_course_duration' => '6 weeks',
				'_reci_course_level'    => 'Advanced',
			],
		],
	];

	if ( in_array( 'reci_course', $want, true ) ) {
		foreach ( $courses as $d ) {
			reci_demo_insert_post( 'reci_course', $d, $topics, $locations, $imgs );
		}
	}

	// Testimonials.
	$testimonials = [
		[
			'slug'      => 'reci-demo-testimonial-rasheed',
			'title'     => 'Rasheed — RECI transformed my approach',
			'text'      => 'RECI changed how I show up in equity work. Before, I thought I understood the issues — now I realize I was only scratching the surface. The cohort model pushed me to examine my own assumptions while building real strategies I can apply in my organization.',
			'name'      => 'Rasheed Thompson',
			'role'      => 'Program Director',
			'org'       => 'Allegheny County Health Department',
		],
		[
			'slug'      => 'reci-demo-testimonial-jasmine',
			'title'     => 'Jasmine — The cohort model created space',
			'text'      => 'The cohort model created space for honest conversations that rarely happen across departments. I came in expecting theory; I left with a network of equity practitioners who hold me accountable. That combination of knowledge and community is what makes RECI unique.',
			'name'      => 'Jasmine Okonkwo',
			'role'      => 'Community Engagement Specialist',
			'org'       => 'Pittsburgh Public Schools',
		],
		[
			'slug'      => 'reci-demo-testimonial-marcus',
			'title'     => 'Marcus — I came in as a skeptic',
			'text'      => 'I came in as a skeptic, left as a practitioner. The difference between RECI and other equity programs is that this one is grounded in research AND lived experience. It is not about checking a box — it is about genuinely changing how your institution operates day to day.',
			'name'      => 'Marcus Delgado',
			'role'      => 'VP of Programs',
			'org'       => 'Urban Impact Foundation',
		],
		[
			'slug'      => 'reci-demo-testimonial-keisha',
			'title'     => 'Keisha — This work gave me a language',
			'text'      => 'This work gave me a language for what I already knew but could not articulate. For years I navigated spaces where racial inequity was obvious but unspoken. RECI gave me the frameworks, the data, and the community to name it — and more importantly, to act on it.',
			'name'      => 'Keisha Williams',
			'role'      => 'Senior Policy Analyst',
			'org'       => 'City of Pittsburgh',
		],
	];

	if ( in_array( 'reci_testimonial', $want, true ) ) {
		foreach ( $testimonials as $d ) {
			reci_demo_insert_testimonial( $d );
		}
	}

	// Core pages.
	if ( in_array( 'reci_page', $want, true ) ) {
		$core_pages = [
			'about'              => ['title' => 'About',              'template' => 'template-about.php'],
			'learn'              => ['title' => 'Learn',              'template' => 'template-learn.php'],
			'framework'          => ['title' => 'The Six Spheres of RECI', 'template' => 'template-spheres.php'],
			'sponsorship'        => ['title' => 'Sponsorship',        'template' => 'template-sponsorship.php'],
			'community'          => ['title' => 'Community',          'template' => ''],
			'donate'             => ['title' => 'Donate',             'template' => 'template-donate.php'],
			'sign-in'            => ['title' => 'Sign In',            'template' => 'template-sign-in.php'],
			'sign-up'            => ['title' => 'Sign Up',            'template' => 'template-sign-up.php'],
			'forgot-password'    => ['title' => 'Forgot Password',    'template' => 'template-forgot-password.php'],
			'submit-content'     => ['title' => 'Submit Content',     'template' => 'template-submit-content.php'],
		];
		foreach ( $core_pages as $slug => $cfg ) {
			$page_id = reci_demo_ensure_page( $slug, $cfg['title'], $cfg['template'] );
			if ( $page_id ) {
				$slugs   = get_option( 'reci_demo_slugs', [] );
				$slugs[] = $slug;
				update_option( 'reci_demo_slugs', array_unique( $slugs ) );
			}
		}
	}

	update_option( 'reci_demo_installed', true );
}

// ---------------------------------------------------------------------------
// Reset handler
// ---------------------------------------------------------------------------

function reci_reset_demo_content(): void {
	$slugs = get_option( 'reci_demo_slugs', [] );
	foreach ( $slugs as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, [
			'reci_article', 'reci_podcast', 'reci_video', 'reci_event',
			'reci_reflection', 'reci_quote', 'reci_assessment', 'reci_course',
			'reci_testimonial', 'page',
		] );
		if ( $post ) {
			wp_delete_post( $post->ID, true );
		}
	}
	delete_option( 'reci_demo_installed' );
	delete_option( 'reci_demo_slugs' );
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Ensure a WordPress page exists with the given slug and template.
 *
 * Skips if the slug already exists. Flagged as demo content.
 *
 * @param string $slug     Page slug.
 * @param string $title    Page title.
 * @param string $template Template filename (without path).
 * @return int|false Page ID on success, false on failure.
 */
function reci_demo_ensure_page( string $slug, string $title, string $template ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing ) {
		if ( $template !== '' ) {
			update_post_meta( $existing->ID, '_wp_page_template', $template );
		}
		update_post_meta( $existing->ID, '_reci_demo', '1' );
		return $existing->ID;
	}

	$page_id = wp_insert_post( [
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => '',
	] );

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return false;
	}

	if ( $template !== '' ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}
	update_post_meta( $page_id, '_reci_demo', '1' );

	return $page_id;
}

/**
 * Insert a demo testimonial post.
 */
function reci_demo_insert_testimonial( array $data ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_testimonial' );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_testimonial',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_content' => '',
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_testimonial_text', $data['text'] );
	update_post_meta( $post_id, '_reci_testimonial_full_name', $data['name'] );
	update_post_meta( $post_id, '_reci_testimonial_role', $data['role'] );
	update_post_meta( $post_id, '_reci_testimonial_organization', $data['org'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Insert a demo quote post.
 */
function reci_demo_insert_quote( array $data ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_quote' );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_quote',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_content' => '',
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_quote_text', $data['text'] );
	update_post_meta( $post_id, '_reci_quote_author_name', $data['author_name'] );
	update_post_meta( $post_id, '_reci_quote_author_title', $data['author_title'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Ensure taxonomy terms exist; returns slug → term_id map.
 */
function reci_demo_ensure_terms( string $taxonomy, array $names ): array {
	$map = [];
	foreach ( $names as $name ) {
		$term = term_exists( $name, $taxonomy );
		if ( ! $term ) {
			$term = wp_insert_term( $name, $taxonomy );
		}
		if ( ! is_wp_error( $term ) ) {
			$map[ $name ] = (int) ( $term['term_id'] ?? $term );
		}
	}
	return $map;
}

/**
 * Insert a demo post if its slug doesn't already exist.
 */
function reci_demo_insert_post(
	string $post_type,
	array $data,
	array $topics,
	array $locations,
	array &$imgs
): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, $post_type );
	if ( $existing ) {
		return;
	}

	$post_args = [
		'post_type'    => $post_type,
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_excerpt' => $data['excerpt'],
		'post_content' => $data['content'],
	];

	if ( 'reci_event' === $post_type && ! empty( $data['meta']['_reci_event_start_date'] ) ) {
		$event_date = $data['meta']['_reci_event_start_date'];
		$post_args['post_date']     = $event_date . ' 12:00:00';
		$post_args['post_date_gmt'] = $event_date . ' 12:00:00';
	}

	$post_id = wp_insert_post( $post_args );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	foreach ( ( $data['meta'] ?? [] ) as $key => $val ) {
		update_post_meta( $post_id, $key, $val );
	}
	update_post_meta( $post_id, '_reci_demo', '1' );

	$topic_ids = [];
	foreach ( ( $data['topics'] ?? [] ) as $topic_name ) {
		if ( isset( $topics[ $topic_name ] ) ) {
			$topic_ids[] = $topics[ $topic_name ];
		}
	}
	if ( $topic_ids ) {
		wp_set_object_terms( $post_id, $topic_ids, 'reci_topic', false );
	}

	if ( isset( $locations['Pittsburgh'] ) ) {
		wp_set_object_terms( $post_id, [ $locations['Pittsburgh'] ], 'reci_location', false );
	}

	if ( ! empty( $data['image'] ) ) {
		$attachment_id = reci_demo_sideload_image( $data['image'], $post_id, $imgs );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Sideload an image from theme's assets/images/ directory into the media library.
 */
function reci_demo_sideload_image( string $filename, int $post_id, array &$imgs ): int {
	if ( isset( $imgs[ $filename ] ) ) {
		return $imgs[ $filename ];
	}

	$file_path = get_template_directory() . '/assets/images/' . $filename;
	if ( ! file_exists( $file_path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$upload = wp_upload_bits( $filename, null, file_get_contents( $file_path ) );
	if ( $upload['error'] ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment( [
		'post_mime_type' => mime_content_type( $file_path ) ?: 'image/png',
		'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
		'post_parent'    => $post_id,
	], $upload['file'], $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	$imgs[ $filename ] = $attachment_id;
	return $attachment_id;
}

/**
 * Generate placeholder body copy.
 */
function reci_demo_lorem( int $paragraphs = 2 ): string {
	$p   = 'Racial equity work requires sustained commitment, community partnership, and an unflinching willingness to examine systems — not just individual behaviors. Over decades of research, scholars and practitioners have demonstrated that disparities in education, health, housing, and economic opportunity are not the result of individual failings, but of policies and structures designed to produce unequal outcomes. Understanding this is the first step toward changing it.';
	$p2  = 'The path forward demands both urgency and patience. Urgency because lives and livelihoods hang in the balance every day that inequitable systems remain in place. Patience because lasting systemic change is never quick, and meaningful progress requires building trust across communities, institutions, and generations. RECI is committed to both, bridging rigorous scholarship with community-driven action.';
	$p3  = 'Communities most impacted by racial inequity are not simply problems to be solved — they are sources of expertise, resilience, and vision. Centering the voices and leadership of those who have navigated unjust systems firsthand is not charity; it is a prerequisite for effective and lasting change. When solutions emerge from within communities, they are more likely to endure.';
	$p4  = 'Data matters, but data without context can mislead. Numbers alone cannot capture the lived experience of systemic exclusion. Effective equity work weaves together quantitative research, qualitative stories, and community knowledge to create a fuller picture of both the problem and the possibility. RECI\'s approach integrates all three.';

	$all = [ $p, $p2, $p3, $p4 ];
	return implode( "\n\n", array_slice( $all, 0, min( $paragraphs, 4 ) ) );
}
