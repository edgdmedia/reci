<?php

/**
 * Homepage body wired with reusable listing components.
 *
 * Repeatable items are grouped in arrays at the top for maintainability.
 */

if (! defined('ABSPATH')) {
	exit;
}

$hero_feature_item = [
	'type_label'       => 'Article',
	'type_badge_class' => 'bg-amber-400',
	'type_text_class'  => 'text-neutral-800',
	'date'             => '08 Jan 2026',
	'meta_icon'        => 'timer',
	'meta_value'       => '3 mins',
	'title'            => 'Tracing Redlining: Pittsburgh’s Hidden Borders',
	'excerpt'          => 'Explore the historical practices of redlining in Pittsburgh and their lasting impact on communities.',
	'tags'             => ['Tagline 1', 'Tagline 2', 'Tagline 3'],
	'image_url'        => 'https://placehold.co/700x372',
	'image_alt'        => 'Tracing Redlining cover image',
];

$hero_sidebar_items = [
	[
		'type_label'       => 'Podcast',
		'type_badge_class' => 'bg-neutral-800',
		'type_text_class'  => 'text-white',
		'date'             => '08 Jan 2026',
		'meta_icon'        => 'audio',
		'meta_value'       => '07:27',
		'meta_value_size'  => 'text-sm',
		'title'            => 'The Digital Divide: Access and Equity',
		'tags'             => ['Technology', 'Equity', 'Access'],
		'image_url'        => 'https://placehold.co/180x150',
		'image_alt'        => 'Podcast thumbnail',
	],
	[
		'type_label'       => 'Video',
		'type_badge_class' => 'bg-blue-900',
		'type_text_class'  => 'text-white',
		'date'             => '08 Jan 2026',
		'meta_icon'        => 'video',
		'meta_value'       => '07:27',
		'meta_value_size'  => 'text-sm',
		'title'            => 'The Power of Collective Action',
		'tags'             => ['Community', 'Activism', 'Change'],
		'image_url'        => 'https://placehold.co/180x150',
		'image_alt'        => 'Video thumbnail',
	],
	[
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => '08 Jan 2026',
		'meta_icon'        => 'timer',
		'meta_value'       => '3 mins',
		'meta_value_size'  => 'text-base',
		'title'            => 'Unpacking Systemic Bias in Healthcare',
		'tags'             => ['Privilege', 'Inequality', 'Healthcare'],
		'image_url'        => 'https://placehold.co/180x150',
		'image_alt'        => 'Article thumbnail',
	],
	[
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => '08 Jan 2026',
		'meta_icon'        => 'timer',
		'meta_value'       => '3 mins',
		'meta_value_size'  => 'text-base',
		'title'            => 'Bridging Divides: Dialogue as Tool for Peace',
		'tags'             => ['Dialogue', 'Peace', 'Society'],
		'image_url'        => 'https://placehold.co/180x150',
		'image_alt'        => 'Article thumbnail',
	],
];

$today_items = [
	[
		'status'       => 'Upcoming',
		'date'         => '20 July, 2026',
		'time'         => '3PM EST',
		'title'        => 'Online Seminar on Decolonization Education',
		'excerpt'      => 'New Podcast Series : “ Intersectional Futures” Episode 1 Released - Available Now!',
		'button_label' => 'Register',
		'image_url'    => 'https://placehold.co/359x359',
		'image_alt'    => 'Event visual',
	],
];

$articles_feature_item = [
	'type_label'       => 'Article',
	'type_badge_class' => 'bg-amber-400',
	'type_text_class'  => 'text-neutral-800',
	'date'             => '08 Jan 2026',
	'meta_icon'        => 'timer',
	'meta_value'       => '3 mins',
	'title'            => 'Tracing Redlining: Pittsburgh’s Hidden Borders',
	'excerpt'          => 'Explore the historical practices of redlining in Pittsburgh and their lasting impact on communities.',
	'tags'             => ['Identity', 'Inclusion', 'Language'],
	'image_url'        => 'https://placehold.co/700x416',
	'image_alt'        => 'Featured article image',
	'title_classes'    => "self-stretch justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-10",
];

$articles_side_items = [
	[
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => '08 Jan 2026',
		'meta_value'       => '3 mins',
		'title'            => 'The Power of Collective Action',
		'tags'             => ['Identity', 'Inclusion', 'Language'],
		'image_url'        => 'https://placehold.co/460x232',
		'image_alt'        => 'Article card image',
	],
	[
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => '08 Jan 2026',
		'meta_value'       => '3 mins',
		'title'            => 'The Power of Collective Action',
		'tags'             => ['Identity', 'Inclusion', 'Language'],
		'image_url'        => 'https://placehold.co/460x232',
		'image_alt'        => 'Article card image',
	],
	[
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => '08 Jan 2026',
		'meta_value'       => '3 mins',
		'title'            => 'The Power of Collective Action',
		'tags'             => ['Identity', 'Inclusion', 'Language'],
		'image_url'        => 'https://placehold.co/460x232',
		'image_alt'        => 'Article card image',
	],
];

$videos_featured_item = [
	'type_label'   => 'Video',
	'date'         => '08 Jan 2026',
	'meta_value'   => '07:27',
	'title'        => 'The Power of Collective Action',
	'excerpt'      => 'Exploring the transformative potential of intergroup dialogue in fostering understanding and resolving conflicts',
	'tags'         => ['Dialogue', 'Peace', 'Society'],
	'bg_image_url' => 'https://placehold.co/710x700',
];

$quote_items = [
	[
		'quote'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in',
		'author' => '– By John Doe',
	],
];

$podcast_feature_item = [
	'type_label'       => 'Podcast',
	'date'             => '08 Jan 2026',
	'duration'         => '07:27',
	'title'            => 'Decolonizing the Curriculum: A Conversation',
	'tags'             => ['Tag 1', 'Tag 2', 'Tag 3'],
	'excerpt'          => 'Listen to educators and scholars discuss the vital process of decolonizing academic curricula to include diverse perspectives.',
	'image_url'        => 'https://placehold.co/670x450',
	'image_alt'        => 'Podcast feature image',
	'progress_percent' => 35,
];

$podcast_compact_items = [
	[
		'topic_tags'       => ['Technology', 'Equity', 'Access'],
		'type_label'       => 'Podcast',
		'date'             => '08 Jan 2026',
		'duration'         => '07:27',
		'title'            => 'The Digital Divide: Access and Equity',
		'image_url'        => 'https://placehold.co/185x186',
		'image_alt'        => 'Podcast card image',
		'progress_percent' => 35,
	],
	[
		'topic_tags'       => ['Technology', 'Equity', 'Access'],
		'type_label'       => 'Podcast',
		'date'             => '08 Jan 2026',
		'duration'         => '07:27',
		'title'            => 'The Digital Divide: Access and Equity',
		'image_url'        => 'https://placehold.co/185x186',
		'image_alt'        => 'Podcast card image',
		'progress_percent' => 35,
	],
	[
		'topic_tags'       => ['Technology', 'Equity', 'Access'],
		'type_label'       => 'Podcast',
		'date'             => '08 Jan 2026',
		'duration'         => '07:27',
		'title'            => 'The Digital Divide: Access and Equity',
		'image_url'        => 'https://placehold.co/185x186',
		'image_alt'        => 'Podcast card image',
		'progress_percent' => 35,
	],
	[
		'topic_tags'       => ['Technology', 'Equity', 'Access'],
		'type_label'       => 'Podcast',
		'date'             => '08 Jan 2026',
		'duration'         => '07:27',
		'title'            => 'The Digital Divide: Access and Equity',
		'image_url'        => 'https://placehold.co/185x186',
		'image_alt'        => 'Podcast card image',
		'progress_percent' => 35,
	],
];

$lens_cards = [
	[
		'title'        => 'Racial anxiety quiz',
		'description'  => 'Assess your comfort levels in cross-racial interactions.',
		'button_label' => 'Start quiz',
	],
	[
		'title'        => 'Privilege checklist',
		'description'  => 'Explore unearned advantages based on social identities',
		'button_label' => 'Start quiz',
	],
	[
		'title'        => 'Implicit bias survey',
		'description'  => 'Uncover subconscious associations affecting perceptions',
		'button_label' => 'Start quiz',
	],
];

$community_slides = [
	[
		'quote'       => 'RECI has truly transformed how we approach market analysis. The insights are unparalleled!.',
		'author_name' => 'Jane Doe',
		'author_role' => 'Financial Analyst',
		'author_image' => 'https://placehold.co/60x60',
		'author_alt'  => 'Jane Doe',
	],
];
?>

<main data-layer="Hero V3" class="HeroV3 w-[1440px] bg-slate-100 inline-flex flex-col justify-start items-center overflow-hidden">
	<div class=" w-[1440px] px-24 pt-10 pb-24 inline-flex justify-start items-start gap-10">
		<?php get_template_part('template-parts/listings/post-item-half-row', null, $hero_feature_item); ?>

		<div data-layer="Content" class="Content flex-1 h-[700px] pl-10 pb-5 border-l-[0.50px] border-zinc-400 inline-flex flex-col justify-start items-start gap-5 overflow-hidden">
			<?php foreach ($hero_sidebar_items as $index => $item) : ?>
				<?php get_template_part('template-parts/listings/post-item-compact', null, $item); ?>
				<?php if ($index < count($hero_sidebar_items) - 1) : ?>
					<div data-layer="Vector" class="self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-stone-300"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<div class=" w-[1440px] h-[750px] px-24 pt-24 pb-14 relative flex flex-col justify-start items-start gap-10" style="background-image: url('https://placehold.co/1440x750'); background-size: cover; background-position: center;">
		<div data-layer="Overlay" class="Overlay w-[1440px] h-[750px] left-0 top-0 absolute bg-gradient-to-l from-neutral-800 to-white/0"></div>
		<div class="Container p-10 bg-white rounded-lg flex flex-col justify-start items-start gap-5">
			<div data-layer="Today at RECI" class="TodayAtReci self-stretch justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Today at RECI</div>
			<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
			<?php get_template_part('template-parts/listings/event-carousel-card', null, $today_items[0]); ?>
			<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
				<?php for ($i = 0; $i < 4; $i++) : ?>
					<div data-layer="Pagination" class="Pagination flex-1 h-0.5 relative <?php echo $i === 0 ? 'bg-amber-400' : 'bg-zinc-400'; ?>"></div>
				<?php endfor; ?>
			</div>
		</div>
	</div>

	<div class=" w-[1440px] px-24 py-10 flex flex-col justify-center items-start gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch inline-flex justify-between items-center">
			<div data-layer="Content" class="Content w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></div>
				<div data-layer="Articles" class="Articles justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Articles</div>
			</div>
			<div data-layer="Button" data-property-1="Secondary" class="Button min-w-28 px-7 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-800 flex justify-center items-center gap-2 overflow-hidden">
				<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
					<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">View all</div>
				</div>
			</div>
		</div>
		<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
		<div data-layer="Content" class="Content self-stretch inline-flex justify-start items-start gap-10">
			<?php get_template_part('template-parts/listings/post-item-half-row', null, $articles_feature_item); ?>
			<?php get_template_part('template-parts/listings/articles-side-rail', null, ['items' => $articles_side_items]); ?>
		</div>
		<div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
	</div>

	<div class=" w-[1440px] h-[750px] px-24 pt-24 pb-14 bg-neutral-800/60 flex flex-col justify-end items-start gap-10" style="background-image: url('https://placehold.co/1440x750'); background-size: cover; background-position: center;">
		<div data-layer="Frame 1" class="Frame1 self-stretch inline-flex justify-between items-center">
			<div data-layer="Reflection Gallery" class="ReflectionGallery justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Reflection Gallery</div>
			<div data-layer="Button" data-property-1="Hover" class="Button min-w-28 px-7 py-3.5 bg-amber-400 rounded-lg inline-flex justify-center items-center gap-2 overflow-hidden">
				<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
					<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">Reflection gallery</div>
				</div>
			</div>
		</div>
		<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-white"></div>
		<div data-layer="Description" class="self-stretch justify-start text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight">Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery – a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists.</div>
	</div>

	<div class=" w-[1440px] px-24 py-10 flex flex-col justify-start items-start gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch inline-flex justify-between items-center">
			<div data-layer="Content" class="Content w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></div>
				<div data-layer="Videos" class="Videos justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Videos</div>
			</div>
			<div data-layer="Button" data-property-1="Secondary" class="Button min-w-28 px-7 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-800 flex justify-center items-center gap-2 overflow-hidden">
				<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
					<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">View all</div>
				</div>
			</div>
		</div>
		<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
		<div data-layer="Content" class="Content self-stretch inline-flex justify-start items-start gap-10">
			<div data-layer="Content" class="Content flex-1 h-[701px] pr-10 overflow-hidden border-r-[0.50px] border-zinc-400 inline-flex flex-col justify-start items-start gap-5">
				<?php foreach ($articles_side_items as $index => $item) : ?>
					<?php get_template_part('template-parts/listings/articles-side-card', null, $item); ?>
					<?php if ($index < count($articles_side_items) - 1) : ?>
						<div data-layer="Vector" class="self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-stone-300"></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php get_template_part('template-parts/listings/videos-featured-overlay-card', null, $videos_featured_item); ?>
		</div>
		<div data-layer="Vector 5" class="Vector5 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
	</div>

	<div class=" w-[1440px] px-36 py-24 bg-blue-900 flex flex-col justify-start items-start gap-10">
		<div class="Container self-stretch p-10 bg-blue-800 rounded-lg flex flex-col justify-start items-start gap-5">
			<div data-layer="Quotes of the day" class="QuotesOfTheDay self-stretch text-center justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Quotes of the day</div>
			<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-slate-200"></div>
			<?php get_template_part('template-parts/listings/quote-carousel-item', null, $quote_items[0]); ?>
			<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
				<?php for ($i = 0; $i < 4; $i++) : ?>
					<div data-layer="Pagination" class="Pagination flex-1 h-0.5 relative <?php echo $i === 0 ? 'bg-amber-400' : 'bg-stone-300'; ?>"></div>
				<?php endfor; ?>
			</div>
		</div>
	</div>

	<div class=" w-[1440px] px-24 py-10 flex flex-col justify-start items-center gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch inline-flex justify-between items-center">
			<div data-layer="Content" class="Content w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></div>
				<div data-layer="Podcasts" class="Podcasts justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Podcasts</div>
			</div>
			<div data-layer="Button" data-property-1="Secondary" class="Button min-w-28 px-7 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-800 flex justify-center items-center gap-2 overflow-hidden">
				<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
					<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">View all</div>
				</div>
			</div>
		</div>
		<div data-layer="Vector 3" class=" self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
		<?php get_template_part('template-parts/listings/podcast-feature-card', null, $podcast_feature_item); ?>
		<div data-layer="Vector 5" class="Vector5 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
		<?php get_template_part('template-parts/listings/podcast-compact-row', null, ['items' => $podcast_compact_items]); ?>
		<div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
	</div>

	<div class=" w-[1440px] p-24 bg-white flex flex-col justify-start items-start gap-10">
		<div data-layer="Content" class="Content self-stretch py-14 bg-slate-100 rounded-lg flex flex-col justify-start items-start gap-10">
			<div data-layer="content" class="Content self-stretch px-14 inline-flex justify-between items-center">
				<div data-layer="Check your lens" class="CheckYourLens justify-start text-neutral-800 text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">Check your lens</div>
				<div data-layer="Button" data-property-1="Secondary" class="Button min-w-28 px-7 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-800 flex justify-center items-center gap-2 overflow-hidden">
					<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
						<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">View all</div>
					</div>
				</div>
			</div>
			<?php get_template_part('template-parts/listings/lens-quiz-row', null, ['cards' => $lens_cards]); ?>
		</div>
	</div>

	<div class=" w-[1440px] p-24 bg-neutral-800 flex flex-col justify-start items-start gap-24">
		<div data-layer="Content" class="Content self-stretch inline-flex justify-start items-start gap-10">
			<img data-layer="Image" class="Image flex-1 self-stretch p-2.5 rounded-lg border-b-[11px] border-amber-400" src="https://placehold.co/415x347" alt="Connect elements" />
			<div data-layer="Content" class="Content inline-flex flex-col justify-start items-start gap-10">
				<div data-layer="Tag" class="Tag flex flex-col justify-start items-start gap-2.5">
					<div data-layer="Tag" class="Tag px-2 py-1 bg-amber-400 rounded inline-flex justify-center items-center gap-2.5">
						<div data-layer="Connect Elements" class="ConnectElements justify-start text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4">Connect Elements</div>
					</div>
					<div data-layer="Title" class="w-[785px] justify-start text-white text-6xl font-semibold font-['EB_Garamond'] leading-[74.40px]">Connect your interests to improve your feed and discover more relevant content</div>
				</div>
				<div data-layer="Button" data-property-1="Hover" class="Button min-w-28 px-7 py-3.5 bg-amber-400 rounded-lg inline-flex justify-center items-center gap-2 overflow-hidden">
					<div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
						<div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6">Connect Now</div>
					</div>
				</div>
			</div>
		</div>
		<div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
		<div data-layer="Content" class="Content self-stretch p-14 relative bg-neutral-600 rounded-lg inline-flex justify-start items-start gap-28">
			<div data-layer="Content" class="Content inline-flex flex-col justify-start items-start gap-2.5">
				<div data-layer="Content" class="Content w-24 h-6"></div>
				<div data-layer="Community Pulse" class="CommunityPulse w-56 justify-start text-white text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">Community Pulse</div>
			</div>
			<div data-layer="format-quote-open" class="FormatQuoteOpen w-20 h-20 left-[307px] top-[87px] absolute overflow-hidden">
				<div data-layer="Vector" class="Vector w-12 h-8 left-[16.67px] top-[23.33px] absolute bg-amber-400"></div>
			</div>
			<?php get_template_part('template-parts/listings/community-pulse-slide', null, $community_slides[0]); ?>
		</div>
	</div>
</main>