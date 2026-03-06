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

function reci_handle_install_demo(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}
	check_admin_referer( 'reci_demo_action' );

	reci_install_demo_content();

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

function reci_install_demo_content(): void {
	// Taxonomy terms first.
	$topics = reci_demo_ensure_terms( 'reci_topic', [
		'Racial Equity',
		'Education',
		'Community',
		'Leadership',
		'Health Equity',
		'Policy',
		'Research',
		'Arts & Culture',
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
			'topics'  => [ 'Racial Equity', 'Policy' ],
			'image'   => 'Image.png',
			'meta'    => [ '_reci_read_time' => '8 min read' ],
		],
		[
			'slug'    => 'reci-demo-equity-education-pipeline',
			'title'   => 'Building an Equity-Centered Education Pipeline',
			'excerpt' => 'How universities and K-12 schools are partnering to create pathways that address racial disparities in education.',
			'content' => reci_demo_lorem( 4 ),
			'topics'  => [ 'Education', 'Racial Equity' ],
			'image'   => 'Image2.png',
			'meta'    => [ '_reci_read_time' => '6 min read' ],
		],
		[
			'slug'    => 'reci-demo-pittsburgh-community-pulse',
			'title'   => 'Community Pulse: Pittsburgh's Racial Justice Landscape in 2025',
			'excerpt' => 'A data-driven snapshot of where Pittsburgh stands on key racial equity indicators and what's changing.',
			'content' => reci_demo_lorem( 4 ),
			'topics'  => [ 'Community', 'Research' ],
			'image'   => 'Image3.png',
			'meta'    => [ '_reci_read_time' => '5 min read' ],
		],
		[
			'slug'    => 'reci-demo-health-equity-research',
			'title'   => 'New Research Highlights Health Disparities Across Allegheny County',
			'excerpt' => 'The latest findings from the RECI Health Equity Lab reveal persistent gaps that demand immediate policy response.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Health Equity', 'Research' ],
			'image'   => 'Image4.png',
			'meta'    => [ '_reci_read_time' => '7 min read' ],
		],
		[
			'slug'    => 'reci-demo-arts-culture-healing',
			'title'   => 'Arts, Culture, and Community Healing After Racial Trauma',
			'excerpt' => 'How creative expression is becoming a key vehicle for healing, advocacy, and systemic change in Black communities.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Arts & Culture', 'Community' ],
			'image'   => 'Image5.png',
			'meta'    => [ '_reci_read_time' => '4 min read' ],
		],
		[
			'slug'    => 'reci-demo-leadership-equity',
			'title'   => 'Cultivating Equity-Minded Leaders at Every Level',
			'excerpt' => 'From classrooms to boardrooms, RECI's leadership programs are changing who gets to lead and how.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Leadership', 'Education' ],
			'image'   => 'Section.png',
			'meta'    => [ '_reci_read_time' => '5 min read' ],
		],
	];

	foreach ( $articles as $d ) {
		reci_demo_insert_post( 'reci_article', $d, $topics, $locations, $imgs );
	}

	// Podcasts.
	$podcasts = [
		[
			'slug'    => 'reci-demo-podcast-voices-of-equity',
			'title'   => 'Voices of Equity — Episode 1: Why Race Still Matters',
			'excerpt' => 'Our inaugural episode tackles the foundational question of why race remains central to policy, education, and opportunity.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Racial Equity', 'Policy' ],
			'image'   => 'connect-now.png',
			'meta'    => [
				'_reci_podcast_audio_url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
				'_reci_podcast_duration_label' => '42 min',
				'_reci_podcast_duration_secs'  => 2520,
				'_reci_podcast_spotify_url'    => '',
				'_reci_podcast_apple_url'      => '',
			],
		],
		[
			'slug'    => 'reci-demo-podcast-education-roundtable',
			'title'   => 'Voices of Equity — Episode 2: Education Roundtable',
			'excerpt' => 'Three educators share what it looks like to build truly anti-racist curricula from the ground up.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Education', 'Leadership' ],
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
			'topics'  => [ 'Health Equity', 'Research' ],
			'image'   => 'connect-now3.png',
			'meta'    => [
				'_reci_podcast_audio_url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
				'_reci_podcast_duration_label' => '51 min',
				'_reci_podcast_duration_secs'  => 3060,
			],
		],
	];

	foreach ( $podcasts as $d ) {
		reci_demo_insert_post( 'reci_podcast', $d, $topics, $locations, $imgs );
	}

	// Videos.
	$videos = [
		[
			'slug'    => 'reci-demo-video-racial-equity-explained',
			'title'   => 'Racial Equity Explained in 5 Minutes',
			'excerpt' => 'A concise animated explainer breaking down the difference between equality, equity, and justice.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Racial Equity' ],
			'image'   => 'Image.png',
			'meta'    => [
				'_reci_video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'_reci_video_duration' => '5:12',
			],
		],
		[
			'slug'    => 'reci-demo-video-community-stories',
			'title'   => 'Community Stories: Voices from Pittsburgh's Hill District',
			'excerpt' => 'Residents share their experiences with racial inequity and the community-led solutions they're building.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Community', 'Arts & Culture' ],
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
			'topics'  => [ 'Policy', 'Research' ],
			'image'   => 'Image3.png',
			'meta'    => [
				'_reci_video_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'_reci_video_duration' => '18:05',
			],
		],
	];

	foreach ( $videos as $d ) {
		reci_demo_insert_post( 'reci_video', $d, $topics, $locations, $imgs );
	}

	// Events.
	$events = [
		[
			'slug'    => 'reci-demo-event-equity-symposium',
			'title'   => 'RECI Annual Racial Equity Symposium 2025',
			'excerpt' => 'Join scholars, practitioners, and community members for a full day of panels, workshops, and conversation.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Racial Equity', 'Community' ],
			'image'   => 'donate-banner.png',
			'meta'    => [
				'_reci_event_date'     => date( 'Y-m-d', strtotime( '+30 days' ) ),
				'_reci_event_time'     => '9:00 AM – 5:00 PM',
				'_reci_event_location' => 'University of Pittsburgh, William Pitt Union',
				'_reci_event_url'      => '',
			],
		],
		[
			'slug'    => 'reci-demo-event-film-screening',
			'title'   => 'Documentary Screening: "Unequal Grounds"',
			'excerpt' => 'A free community screening of the award-winning documentary on housing segregation, followed by a panel discussion.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Arts & Culture', 'Community' ],
			'image'   => 'sponsorship.png',
			'meta'    => [
				'_reci_event_date'     => date( 'Y-m-d', strtotime( '+45 days' ) ),
				'_reci_event_time'     => '6:00 PM – 9:00 PM',
				'_reci_event_location' => 'Carnegie Library of Pittsburgh, Main Branch',
				'_reci_event_url'      => '',
			],
		],
		[
			'slug'    => 'reci-demo-event-leadership-workshop',
			'title'   => 'Equity Leadership Workshop Series — Session 1',
			'excerpt' => 'An immersive workshop for educators and administrators building the skills to lead equity-centered organizations.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Leadership', 'Education' ],
			'image'   => 'sponsorship2.png',
			'meta'    => [
				'_reci_event_date'     => date( 'Y-m-d', strtotime( '+14 days' ) ),
				'_reci_event_time'     => '10:00 AM – 2:00 PM',
				'_reci_event_location' => 'RECI Center, Posvar Hall',
				'_reci_event_url'      => '',
			],
		],
	];

	foreach ( $events as $d ) {
		reci_demo_insert_post( 'reci_event', $d, $topics, $locations, $imgs );
	}

	// Reflections.
	$reflections = [
		[
			'slug'    => 'reci-demo-reflection-teaching-equity',
			'title'   => 'Teaching Equity in a Room Full of Doubt',
			'excerpt' => 'A personal reflection on the resistance, breakthroughs, and moments that make equity education transformative.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Education', 'Leadership' ],
			'image'   => 'reflection.png',
			'meta'    => [],
		],
		[
			'slug'    => 'reci-demo-reflection-first-generation',
			'title'   => 'What It Means to Be First: A First-Generation Student's Story',
			'excerpt' => 'Growing up in a household where college was never mentioned, and navigating what it costs to be the first.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Community', 'Racial Equity' ],
			'image'   => 'reflection2.png',
			'meta'    => [],
		],
		[
			'slug'    => 'reci-demo-reflection-doctor-black-america',
			'title'   => 'Becoming a Doctor in Black America: A Journey Through Inequity',
			'excerpt' => 'From med school to residency, the invisible barriers that shaped one physician's commitment to health equity.',
			'content' => reci_demo_lorem( 3 ),
			'topics'  => [ 'Health Equity', 'Research' ],
			'image'   => 'JohnDoe.png',
			'meta'    => [],
		],
	];

	foreach ( $reflections as $d ) {
		reci_demo_insert_post( 'reci_reflection', $d, $topics, $locations, $imgs );
	}

	// Quotes.
	$quotes = [
		[
			'slug'    => 'reci-demo-quote-equity-not-equality',
			'title'   => 'On Equity vs. Equality',
			'excerpt' => '"Equality is giving everyone the same pair of shoes. Equity is giving everyone a pair of shoes that fits."',
			'content' => '"Equality is giving everyone the same pair of shoes. Equity is giving everyone a pair of shoes that fits." — Dr. Angela Davis (paraphrased)',
			'topics'  => [ 'Racial Equity' ],
			'image'   => 'JohnDoe.png',
			'meta'    => [
				'_reci_quote_author'      => 'Dr. Angela Davis',
				'_reci_quote_attribution' => 'Scholar, Author, and Activist',
			],
		],
		[
			'slug'    => 'reci-demo-quote-injustice-anywhere',
			'title'   => 'Injustice Anywhere',
			'excerpt' => '"Injustice anywhere is a threat to justice everywhere."',
			'content' => '"Injustice anywhere is a threat to justice everywhere." — Dr. Martin Luther King Jr.',
			'topics'  => [ 'Policy', 'Community' ],
			'image'   => 'JohnDoe.png',
			'meta'    => [
				'_reci_quote_author'      => 'Dr. Martin Luther King Jr.',
				'_reci_quote_attribution' => 'Letter from Birmingham Jail, 1963',
			],
		],
		[
			'slug'    => 'reci-demo-quote-silence-is-not-neutral',
			'title'   => 'Silence Is Not Neutral',
			'excerpt' => '"In a racist society, it is not enough to be non-racist; we must be anti-racist."',
			'content' => '"In a racist society, it is not enough to be non-racist; we must be anti-racist." — Angela Y. Davis',
			'topics'  => [ 'Racial Equity', 'Policy' ],
			'image'   => 'JohnDoe.png',
			'meta'    => [
				'_reci_quote_author'      => 'Angela Y. Davis',
				'_reci_quote_attribution' => 'Author and Activist',
			],
		],
	];

	foreach ( $quotes as $d ) {
		reci_demo_insert_post( 'reci_quote', $d, $topics, $locations, $imgs );
	}

	// Assessments.
	$assessments = [
		[
			'slug'    => 'reci-demo-assessment-racial-equity-iq',
			'title'   => 'Racial Equity Knowledge Check',
			'excerpt' => 'Test your foundational knowledge of racial equity concepts, history, and frameworks with this 10-question assessment.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Racial Equity', 'Education' ],
			'image'   => 'Image4.png',
			'meta'    => [ '_reci_assessment_duration' => '10 min' ],
		],
		[
			'slug'    => 'reci-demo-assessment-implicit-bias',
			'title'   => 'Understanding Your Implicit Biases',
			'excerpt' => 'A reflective self-assessment to help you identify and examine the biases you may carry without knowing it.',
			'content' => reci_demo_lorem( 1 ),
			'topics'  => [ 'Leadership', 'Racial Equity' ],
			'image'   => 'Image5.png',
			'meta'    => [ '_reci_assessment_duration' => '15 min' ],
		],
	];

	foreach ( $assessments as $d ) {
		reci_demo_insert_post( 'reci_assessment', $d, $topics, $locations, $imgs );
	}

	// Courses.
	$courses = [
		[
			'slug'    => 'reci-demo-course-intro-racial-equity',
			'title'   => 'Introduction to Racial Equity: Foundations and Frameworks',
			'excerpt' => 'A self-paced online course covering the history, theory, and practice of racial equity work. Ideal for beginners.',
			'content' => reci_demo_lorem( 2 ),
			'topics'  => [ 'Racial Equity', 'Education' ],
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
			'topics'  => [ 'Leadership', 'Policy' ],
			'image'   => 'donate-banner.png',
			'meta'    => [
				'_reci_course_duration' => '6 weeks',
				'_reci_course_level'    => 'Advanced',
			],
		],
	];

	foreach ( $courses as $d ) {
		reci_demo_insert_post( 'reci_course', $d, $topics, $locations, $imgs );
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
 * Ensure taxonomy terms exist; returns slug → term_id map.
 *
 * @param string   $taxonomy
 * @param string[] $names
 * @return array<string,int>
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
 *
 * @param string                $post_type
 * @param array                 $data
 * @param array<string,int>     $topics
 * @param array<string,int>     $locations
 * @param array<string,int>     &$imgs  Running attachment ID cache.
 */
function reci_demo_insert_post(
	string $post_type,
	array $data,
	array $topics,
	array $locations,
	array &$imgs
): void {
	// Skip if slug already taken.
	$existing = get_page_by_path( $data['slug'], OBJECT, $post_type );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => $post_type,
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_excerpt' => $data['excerpt'],
		'post_content' => $data['content'],
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	// Meta.
	foreach ( ( $data['meta'] ?? [] ) as $key => $val ) {
		update_post_meta( $post_id, $key, $val );
	}
	update_post_meta( $post_id, '_reci_demo', '1' );

	// Topics taxonomy.
	$topic_ids = [];
	foreach ( ( $data['topics'] ?? [] ) as $topic_name ) {
		if ( isset( $topics[ $topic_name ] ) ) {
			$topic_ids[] = $topics[ $topic_name ];
		}
	}
	if ( $topic_ids ) {
		wp_set_object_terms( $post_id, $topic_ids, 'reci_topic', false );
	}

	// Default location: Pittsburgh.
	if ( isset( $locations['Pittsburgh'] ) ) {
		wp_set_object_terms( $post_id, [ $locations['Pittsburgh'] ], 'reci_location', false );
	}

	// Featured image — sideload from theme assets.
	if ( ! empty( $data['image'] ) ) {
		$attachment_id = reci_demo_sideload_image( $data['image'], $post_id, $imgs );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	// Track slug for reset.
	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Sideload an image from theme's assets/images/ directory into the media library.
 * Results are cached in $imgs to avoid re-importing duplicates within one run.
 *
 * @param string            $filename  Basename, e.g. "Image.png".
 * @param int               $post_id   Post to attach to.
 * @param array<string,int> &$imgs     Running cache.
 * @return int  Attachment ID, or 0 on failure.
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
 *
 * @param int $paragraphs
 */
function reci_demo_lorem( int $paragraphs = 2 ): string {
	$p = 'Racial equity work requires sustained commitment, community partnership, and an unflinching willingness to examine systems — not just individual behaviors. Over decades of research, scholars and practitioners have demonstrated that disparities in education, health, housing, and economic opportunity are not the result of individual failings, but of policies and structures designed to produce unequal outcomes. Understanding this is the first step toward changing it.';
	$p2 = 'The path forward demands both urgency and patience. Urgency because lives and livelihoods hang in the balance every day that inequitable systems remain in place. Patience because lasting systemic change is never quick, and meaningful progress requires building trust across communities, institutions, and generations. RECI is committed to both, bridging rigorous scholarship with community-driven action.';
	$p3 = 'Communities most impacted by racial inequity are not simply problems to be solved — they are sources of expertise, resilience, and vision. Centering the voices and leadership of those who have navigated unjust systems firsthand is not charity; it is a prerequisite for effective and lasting change. When solutions emerge from within communities, they are more likely to endure.';
	$p4 = 'Data matters, but data without context can mislead. Numbers alone cannot capture the lived experience of systemic exclusion. Effective equity work weaves together quantitative research, qualitative stories, and community knowledge to create a fuller picture of both the problem and the possibility. RECI's approach integrates all three.';

	$all = [ $p, $p2, $p3, $p4 ];
	return implode( "\n\n", array_slice( $all, 0, min( $paragraphs, 4 ) ) );
}
