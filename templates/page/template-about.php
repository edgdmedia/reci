<?php

/**
 * Template Name: About
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$page_title = "About Us";
$page_subtitle = "Learn more about the people, purpose, and story behind RECI.";

$about_cards = [
    [
        "title" => reci_setting('about_c1_title', 'Our Mission'),
        "copy"  => reci_setting('about_c1_copy', 'The Racial Equity Consciousness Institute (RECI) advances racial equity through evidence-based education, research, and community engagement. Housed at the University of Pittsburgh\'s Center on Race and Social Problems, RECI develops and delivers innovative tools for consciousness development across six spheres of influence — Self, Family, Community, Organization, Society, and Global — through Structured Cognitive Behavioral Training (SCBT).'),
        "icon"  => reci_setting('about_c1_icon', 'assets/icons/rocket-launch-outline.svg'),
    ],
    [
        "title" => reci_setting('about_c2_title', 'Our Vision'),
        "copy"  => reci_setting('about_c2_copy', 'We envision a world where racial equity consciousness is a universal competency — where individuals, communities, and institutions possess the awareness, knowledge, and commitment to dismantle racism and build systems that serve everyone equitably.'),
        "icon"  => reci_setting('about_c2_icon', 'assets/icons/lightbulb-on-outline.svg'),
    ],
    [
        "title" => reci_setting('about_c3_title', 'Our Approach'),
        "copy"  => reci_setting('about_c3_copy', 'RECI\'s work is grounded in the metaphor that racism operates as a social virus — and that racial equity consciousness is the vaccine. Through our Structured Cognitive Behavioral Training (SCBT) framework, we guide individuals and organizations through a process-oriented journey of consciousness development across six bilateral spheres. Each sphere represents both an awareness dimension and an action dimension, reflecting the journey from recognition to transformation. This framework has been developed through years of research, tested across multiple cohorts, and is currently supported by NIH-funded research.'),
        "icon"  => reci_setting('about_c3_icon', 'assets/icons/target.svg'),
    ],
];

$template_dir = get_template_directory_uri();

$team_items = [];
$team_query = new WP_Query([
    'post_type'      => 'reci_team',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);

if ($team_query->have_posts()) {
    while ($team_query->have_posts()) {
        $team_query->the_post();
        
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if (!$image_url) {
            $image_url = get_template_directory_uri() . '/demo-content/images/site/team/Ron_Idoko.webp'; // Safe fallback
        }
        
        $team_items[] = [
            'id'        => get_the_ID(),
            'name'      => get_the_title(),
            'role'      => get_post_meta(get_the_ID(), '_reci_team_role', true),
            'image_url' => $image_url,
            'image_alt' => get_the_title(),
            'bio_html'  => apply_filters('the_content', get_the_content()),
        ];
    }
    wp_reset_postdata();
}

$story_cards = [
    'RECI was founded in 2021 by Ron Idoko at the University of Pittsburgh\'s Center on Race and Social Problems. What began as a focused educational initiative has grown into a comprehensive platform engaging 2,500+ participants through cohort-based training, community partnerships, and rigorous research.',
    'Our work bridges scholarship and practice — from NIH-funded research evaluating SCBT\'s effectiveness to collaborative community initiatives like Envisioning a Just Pittsburgh with Carnegie Museums and Carnegie Library.',
    "The RECMH extends this mission by making evidence-based racial equity resources accessible to everyone.",
];

$about_community_slides = [];
$testimonial_query = new WP_Query([
    'post_type'      => 'reci_testimonial',
    'posts_per_page' => 5,
    'orderby'        => 'rand',
]);

if ($testimonial_query->have_posts()) {
    while ($testimonial_query->have_posts()) {
        $testimonial_query->the_post();
        
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
        if (!$image_url) {
            $image_url = 'https://placehold.co/60x60';
        }
        
        $about_community_slides[] = [
            'quote'        => get_post_meta(get_the_ID(), '_reci_testimonial_text', true),
            'author_name'  => get_post_meta(get_the_ID(), '_reci_testimonial_full_name', true),
            'author_role'  => get_post_meta(get_the_ID(), '_reci_testimonial_role', true),
            'author_image' => $image_url,
            'author_alt'   => get_post_meta(get_the_ID(), '_reci_testimonial_full_name', true),
        ];
    }
    wp_reset_postdata();
}

get_header();
?>
<main class="layout-page">
	<?php get_template_part("template-parts/common/page-title-card", null, [
     "title" => $page_title,
     "subtitle" => $page_subtitle,
 ]); ?>

	<section class="reci-container py-14">
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
			<?php foreach (array_slice($about_cards, 0, 2) as $card): ?>
				<div class="p-10 bg-reci-blue rounded-lg flex flex-col gap-14">
					<div class="w-20 h-20 rounded-full bg-amber-400 flex items-center justify-center">
						<?php if ( is_numeric( $card["icon"] ) && $card["icon"] > 0 ) : ?>
							<?php echo wp_get_attachment_image( $card["icon"], 'thumbnail', false, ['class' => 'w-10 h-10 object-contain'] ); ?>
						<?php else : ?>
							<?php echo reci_inline_svg($card["icon"], "w-10 h-10 text-neutral-800", [
								"aria-hidden" => "true",
							]); ?>
						<?php endif; ?>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-white text-3xl font-bold font-heading leading-10"><?php echo esc_html(
          $card["title"],
      ); ?></h2>
						<p class="text-gray-200 text-xl font-normal leading-relaxed"><?php echo esc_html(
          $card["copy"],
      ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
			<?php if (isset($about_cards[2])): $card = $about_cards[2]; ?>
				<div class="p-10 bg-reci-blue rounded-lg flex flex-col gap-14 lg:col-span-2">
					<div class="w-20 h-20 rounded-full bg-amber-400 flex items-center justify-center">
						<?php if ( is_numeric( $card["icon"] ) && $card["icon"] > 0 ) : ?>
							<?php echo wp_get_attachment_image( $card["icon"], 'thumbnail', false, ['class' => 'w-10 h-10 object-contain'] ); ?>
						<?php else : ?>
							<?php echo reci_inline_svg($card["icon"], "w-10 h-10 text-neutral-800", [
								"aria-hidden" => "true",
							]); ?>
						<?php endif; ?>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-white text-3xl font-bold font-heading leading-10"><?php echo esc_html(
          $card["title"],
      ); ?></h2>
						<p class="text-gray-200 text-xl font-normal leading-relaxed"><?php echo esc_html(
          $card["copy"],
      ); ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="reci-container-full min-h-[500px] relative flex items-center" style="background-image: url('<?php echo esc_url(
     get_template_directory_uri() . "/demo-content/images/site/about/our-story.webp",
 ); ?>'); background-size: cover; background-position: center top;">
		<div class="absolute inset-0 bg-gradient-to-r from-neutral-900/90 to-neutral-900/80"></div>
		<div class="reci-container relative z-10 py-20 flex flex-col gap-8">
			<div class="flex items-center gap-2">
				<span class="w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></span>
				<h2 class="text-white text-5xl font-bold font-heading leading-[1.05]">Our Story</h2>
			</div>
			<div class="max-w-4xl flex flex-col gap-6">
				<?php foreach ($story_cards as $story_card): ?>
					<p class="text-gray-200 text-2xl font-normal leading-7"><?php echo esc_html(
         $story_card,
     ); ?></p>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url(
       is_user_logged_in() ? home_url("/my-account/") : home_url("/sign-up/"),
   ); ?>" class="btn btn-primary btn-md self-start ">
				<?php esc_html_e("Join RECI", "reci-media-hub"); ?>
			</a>
		</div>
	</section>

	<section class="reci-container py-10 flex flex-col gap-10">
		<div class="flex items-center gap-2">
			<span class="w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></span>
			<h2 class="text-neutral-800 text-5xl font-bold font-heading leading-[1.05]">Meet the Team</h2>
		</div>
		<div class="self-stretch h-px bg-zinc-400"></div>
		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
			<?php foreach ($team_items as $team_item): ?>
				<button
					type="button"
					class="flex flex-col gap-5 no-underline group text-left"
					data-team-modal-open="<?php echo esc_attr((string) $team_item["id"]); ?>">
					<div class="rounded-lg overflow-hidden bg-zinc-200">
						<img class="w-full h-96 object-cover object-center transition-transform duration-300 group-hover:scale-[1.02]" src="<?php echo esc_url($team_item["image_url"]); ?>" alt="<?php echo esc_attr($team_item["image_alt"]); ?>" />
					</div>
					<div class="flex flex-col gap-2">
						<h3 class="text-neutral-800 text-3xl font-bold font-heading leading-8"><?php echo esc_html($team_item["name"]); ?></h3>
						<p class="text-neutral-600 text-lg font-normal leading-7"><?php echo esc_html($team_item["role"]); ?></p>
					</div>
				</button>
			<?php endforeach; ?>
		</div>
	</section>

	<div id="about-team-modal" class="hidden fixed inset-0 z-[80] bg-neutral-900/70 p-4 lg:p-10 overflow-y-auto" aria-hidden="true">
		<div class="min-h-full flex items-center justify-center">
			<div class="relative w-full max-w-[1000px]">
				<button
					type="button"
					class="absolute right-4 top-4 z-10 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-white text-neutral-800 hover:bg-zinc-50"
					data-team-modal-close
					aria-label="<?php esc_attr_e("Close team profile", "reci-media-hub"); ?>">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>

				<?php foreach ($team_items as $team_item): ?>
					<div class="hidden bg-white rounded-lg overflow-hidden" data-team-modal-panel="<?php echo esc_attr((string) $team_item["id"]); ?>">
						<div class="grid grid-cols-1 lg:grid-cols-[400px_1fr]">
							<div class="bg-zinc-100">
								<img src="<?php echo esc_url($team_item["image_url"]); ?>" alt="<?php echo esc_attr($team_item["image_alt"]); ?>" class="w-full h-full min-h-[360px] object-cover object-center" />
							</div>
							<div class="p-8 lg:p-10 flex flex-col gap-6 overflow-y-auto max-h-[80vh]">
								<div class="flex flex-col gap-3">
									<h3 class="text-neutral-800 text-3xl lg:text-4xl font-bold font-heading leading-tight"><?php echo esc_html($team_item["name"]); ?></h3>
									<p class="text-neutral-600 text-lg font-bold font-subhead leading-7"><?php echo esc_html($team_item["role"]); ?></p>
								</div>
								<div class="h-px bg-zinc-300"></div>
								<div class="text-neutral-700 text-xl font-normal leading-relaxed prose prose-lg max-w-none">
									<?php echo wp_kses_post($team_item["bio_html"]); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const modal = document.getElementById('about-team-modal');
		if (!modal) return;

		const panels = Array.from(modal.querySelectorAll('[data-team-modal-panel]'));
		const openButtons = Array.from(document.querySelectorAll('[data-team-modal-open]'));
		const closeButtons = Array.from(modal.querySelectorAll('[data-team-modal-close]'));

		const hidePanels = function() {
			panels.forEach(function(panel) {
				panel.classList.add('hidden');
			});
		};

		const closeModal = function() {
			modal.classList.add('hidden');
			modal.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('overflow-hidden');
			hidePanels();
		};

		const openModal = function(id) {
			const panel = modal.querySelector('[data-team-modal-panel="' + id + '"]');
			if (!panel) return;
			hidePanels();
			panel.classList.remove('hidden');
			modal.classList.remove('hidden');
			modal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('overflow-hidden');
		};

		openButtons.forEach(function(button) {
			button.addEventListener('click', function() {
				openModal(button.getAttribute('data-team-modal-open'));
			});
		});

		closeButtons.forEach(function(button) {
			button.addEventListener('click', closeModal);
		});

		modal.addEventListener('click', function(event) {
			if (event.target === modal) {
				closeModal();
			}
		});

		document.addEventListener('keydown', function(event) {
			if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
				closeModal();
			}
		});
	});
	</script>

	<section class="reci-container py-14">
		<div class="flex items-center gap-2 mb-8">
			<span class="w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></span>
			<h2 class="text-neutral-800 text-5xl font-bold font-heading leading-[1.05]">Partners &amp; Affiliations</h2>
		</div>
		<div class="self-stretch h-px bg-zinc-400 mb-10"></div>
		<?php get_template_part("template-parts/common/partners-carousel"); ?>
	</section>
</main>

<?php get_footer(); ?>
