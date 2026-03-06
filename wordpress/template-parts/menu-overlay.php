<?php

/**
 * Full-screen navigation overlay (hamburger menu).
 *
 * Toggled open/closed by assets/js/theme.js.
 * Layout mirrors figma/menu-overlay.php — 4 column grid on large screens.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$icons_url  = get_template_directory_uri() . '/figma/assets/';
$assets_url = get_template_directory_uri() . '/assets/images/';

// ── Dynamic data ─────────────────────────────────────────────────────────────

$categories = get_categories(['hide_empty' => false]);

$content_type_links = [
	'Articles' => get_post_type_archive_link('reci_article') ?: home_url('/articles/'),
	'Videos'   => get_post_type_archive_link('reci_video')   ?: home_url('/videos/'),
	'Podcasts'  => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
];

$authors = get_users(
	[
		'who'    => 'authors',
		'number' => 10,
		'fields' => ['ID', 'display_name'],
	]
);

$locations = get_terms(
	[
		'taxonomy'   => 'reci_location',
		'hide_empty' => false,
		'number'     => 8,
	]
);

$quizzes = get_posts(
	[
		'post_type'      => 'reci_assessment',
		'numberposts'    => 5,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

$overlay_address = get_option(
	'reci_address',
	"4200 Fifth Avenue\nPittsburgh, PA 15260"
);
?>

<div
	id="menu-overlay"
	class="hidden fixed inset-0 z-50 bg-neutral-800 overflow-y-auto"
	role="dialog"
	aria-modal="true"
	aria-label="Navigation menu"
	aria-hidden="true">

	<!-- ── Overlay navbar ──────────────────────────────────────── -->
	<div class="w-full border-b border-neutral-500">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20 h-24 flex justify-between items-center">

			<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-5">
				<img
					src="<?php echo esc_url($assets_url . rawurlencode('RECI Logo - Version 2 1.png')); ?>"
					alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
					class="h-9 w-auto" />
				<div class="w-px h-7 bg-zinc-400/50"></div>
				<span class="text-white text-lg font-medium font-['SF_Pro_Display']">RECI Media Hub</span>
			</a>

			<button
				id="menu-close"
				aria-label="Close navigation menu"
				class="w-8 h-8 flex items-center justify-center cursor-pointer flex-shrink-0">
				<img
					src="<?php echo esc_url($icons_url . 'close.svg'); ?>"
					alt=""
					class="w-8 h-8"
					aria-hidden="true" />
			</button>

		</div>
	</div>

	<!-- ── Overlay content: 4-column grid ─────────────────────── -->
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20 grid grid-cols-2 md:grid-cols-4 max-h-[100vh]">

		<!-- ── Column 1: Categories ──────────────────────────── -->
		<div class="col-span-1 lg:flex-1 lg:border-r border-neutral-500 flex flex-col py-0 lg:py-0">

			<div class="px-4 lg:px-6 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Categories</span>
			</div>

			<nav aria-label="Content categories" class="px-4 lg:px-6 pb-10">
				<!-- WordPress categories -->
				<?php foreach ($categories as $cat) : ?>
					<a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="block pr-5 py-1.5 rounded-xl group">
						<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors"><?php echo esc_html($cat->name); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>


			<?php if (! empty($quizzes)) : ?>
				<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
					<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
					<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Quiz</span>
				</div>

				<nav aria-label="Quizzes" class="pl-4 lg:pl-5 pb-5">
					<?php foreach ($quizzes as $quiz) : ?>
						<a href="<?php echo esc_url(get_permalink($quiz->ID)); ?>" class="block pr-5 py-1.5 rounded-xl group">
							<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors"><?php echo esc_html($quiz->post_title); ?></span>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

		</div>

		<!-- ── Column 2: Authors + Locations ─────────────────── -->
		<div class="col-span-1 lg:border-r border-neutral-500 flex flex-col">

			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Quicklinks</span>
			</div>

			<nav aria-label="Quick links" class="pl-4 lg:pl-5 pb-5">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">Home</span>
				</a>
				<a href="<?php echo esc_url(home_url('/learn/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">Learn</span>
				</a>
				<a href="<?php echo esc_url(home_url('/reflection-gallery/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">Reflect</span>
				</a>
				<a href="<?php echo esc_url(home_url('/community/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">Community</span>
				</a>
			</nav>

			<div class="px-4 lg:px-6 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Authors</span>
			</div>

			<nav aria-label="Authors" class="px-4 lg:px-6 pb-5">
				<?php if (! empty($authors)) : ?>
					<?php foreach ($authors as $author) : ?>
						<a href="<?php echo esc_url(get_author_posts_url($author->ID)); ?>" class="block pr-5 py-1.5 rounded-xl group">
							<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors"><?php echo esc_html($author->display_name); ?></span>
						</a>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="py-3 text-zinc-500 text-base font-['SF_Pro_Display']">No authors yet</p>
				<?php endif; ?>
			</nav>

			<?php
			$has_locations = ! is_wp_error($locations) && ! empty($locations);
			if ($has_locations) :
			?>
				<div class="px-4 lg:px-6 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
					<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
					<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Location</span>
				</div>

				<nav aria-label="Locations" class="px-4 lg:px-6 pb-5">
					<?php foreach ($locations as $location) : ?>
						<a href="<?php echo esc_url(get_term_link($location)); ?>" class="block pr-5 py-1.5 rounded-xl group">
							<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors"><?php echo esc_html($location->name); ?></span>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

		</div>

		<!-- ── Column 3: Quicklinks + Quiz + Address ─────────── -->
		<div class="col-span-1 lg:border-r border-neutral-500 flex flex-col">

			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Address</span>
			</div>

			<div class="pl-4 lg:pl-5 pr-5 pb-10 pt-3">
				<p class="text-white text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
					<?php echo nl2br(esc_html($overlay_address)); ?>
				</p>
			</div>

			<!-- Newsletter -->
			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Newsletter</span>
			</div>

			<div class="pl-4 lg:pl-5 pr-5 pt-5 pb-10">
				<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="flex flex-col gap-3">
					<?php wp_nonce_field('reci_newsletter_subscribe', 'reci_newsletter_nonce'); ?>
					<input type="hidden" name="action" value="reci_newsletter_subscribe" />

					<div class="py-2 border-b border-zinc-400/50 flex items-center gap-2">
						<input
							type="email"
							name="email"
							placeholder="Your email"
							required
							class="flex-1 bg-transparent w-2/3 text-neutral-400 text-base font-normal font-['SF_Pro_Display'] leading-6 outline-none" />
						<button
							type="submit"
							class="flex items-center gap-1 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
								<path d="M1 8h14M9 2l6 6-6 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<span class="text-white text-base font-normal font-['SF_Pro_Display'] leading-6 hidden md:flex">Submit</span>
						</button>
					</div>

					<p class="text-white text-xs font-normal font-['SF_Pro_Display'] leading-5">
						Subscribe to our newsletter for weekly insights, updates, and exclusive contents
					</p>
				</form>
			</div>

			<!-- Donate -->
			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-zinc-400/50 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-neutral-400 text-sm font-medium font-['SF_Pro_Display']">Donate</span>
			</div>

			<div class="pl-4 lg:pl-5 pr-5 pb-5 pt-3 flex flex-col gap-5">
				<p class="text-white text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
					Support RECI's mission toward racial equity consciousness.
				</p>
				<a
					href="<?php echo esc_url(home_url('/donate/')); ?>"
					class="self-start px-5 py-2.5 bg-amber-400 rounded-lg flex items-center gap-2 hover:bg-amber-300 transition-colors">
					<span class="text-zinc-800 text-sm font-medium font-['SF_Pro_Display'] leading-5">Donate</span>
				</a>
			</div>

		</div>

		<!-- ── Column 4: RECI links + Social + Donate + Newsletter -->
		<div class="col-span-1 flex flex-col">

			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">RECI</span>
			</div>

			<nav aria-label="RECI pages" class="pl-4 lg:pl-5 pb-5 h-[30vh] md:h-[50vh] overflow-auto">
				<a href="<?php echo esc_url(home_url('/about/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">About us</span>
				</a>
				<a href="<?php echo esc_url(home_url('/sponsorship/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">Sponsorship</span>
				</a>
				<?php foreach ($content_type_links as $label => $url) : ?>
					<a href="<?php echo esc_url($url); ?>" class="block pr-5 py-1.5 rounded-xl group">
						<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors"><?php echo esc_html($label); ?></span>
					</a>
				<?php endforeach; ?>
				<a href="<?php echo esc_url(home_url('/general-post/')); ?>" class="block pr-5 py-1.5 rounded-xl group">
					<span class="py-2 text-white text-lg font-medium font-['SF_Pro_Display'] group-hover:text-amber-400 transition-colors">General Post</span>
				</a>
			</nav>

			<!-- Social icons -->
			<div class="pl-4 lg:pl-5 pt-10 pb-2 border-b border-neutral-500 flex items-center gap-2">
				<img src="<?php echo esc_url($icons_url . 'Tag.svg'); ?>" alt="" class="w-2 h-2 flex-shrink-0" aria-hidden="true" />
				<span class="text-zinc-400 text-sm font-medium font-['SF_Pro_Display']">Follow RECI</span>
			</div>

			<div class="pl-4 lg:pl-5 py-5">
				<div class="flex items-center gap-2">
					<a href="#" aria-label="LinkedIn" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<img src="<?php echo esc_url($icons_url . rawurlencode('linkedin (1).svg')); ?>" alt="" class="w-5 h-5" aria-hidden="true" />
					</a>
					<a href="#" aria-label="Facebook" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<img src="<?php echo esc_url($icons_url . rawurlencode('facebook (1).svg')); ?>" alt="" class="w-5 h-5" aria-hidden="true" />
					</a>
					<a href="#" aria-label="Twitter / X" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<img src="<?php echo esc_url($icons_url . 'twitter.png'); ?>" alt="" class="w-5 h-5" aria-hidden="true" />
					</a>
					<a href="#" aria-label="Instagram" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<img src="<?php echo esc_url($icons_url . rawurlencode('instagram (1).png')); ?>" alt="" class="w-5 h-5" aria-hidden="true" />
					</a>
				</div>
			</div>





		</div>

	</div><!-- .content -->

</div><!-- #menu-overlay -->