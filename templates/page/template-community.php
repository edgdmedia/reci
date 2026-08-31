<?php
/**
 * Template Name: Community (Collaboratory)
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$is_collaborator = function_exists( 'reci_user_is_collaborator' ) && reci_user_is_collaborator( $current_user_id );

$member_cta_url = $is_logged_in ? home_url( '/dashboard/' ) : ( reci_get_auth_page_url( 'sign-up' ) ?: wp_registration_url() );
$collab_url     = function_exists( 'reci_get_collaborator_page_url' ) ? reci_get_collaborator_page_url() : home_url( '/become-a-collaborator/' );
?>

<main class="layout-page">

	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => 'Community',
		'subtitle' => 'Learn how RECI Community works, how to join, and how to contribute.',
	]); ?>

	<section class="reci-container pt-20 pb-16">
		<div class="max-w-6xl mx-auto flex flex-col gap-6 text-center">
			<div class="flex flex-col gap-5 text-neutral-600 text-lg font-normal leading-8 text-left sm:text-center">
				<p>
					<?php echo esc_html( 'RECI Community is the participation layer of the broader RECI platform. It is where readers become members, members become contributors, and collaborators build public publishing identities rooted in racial equity work, shared learning, and practical contribution.' ); ?>
				</p>
				<p>
					<?php echo esc_html( 'Members use Community to understand how the ecosystem works, how to join, and how to shape their private dashboard experience through the collaborators and interest areas they follow. Approved Collaborators use it as the gateway into contribution, publishing articles, videos, podcasts, events, reflections, and resources through RECI.' ); ?>
				</p>
				<p>
					<?php echo esc_html( 'Community is not a second homepage or a duplicate archive. The public site holds the content archives. The dashboard holds each member’s personalized feed, saved items, notifications, journal, and contribution tools.' ); ?>
				</p>
			</div>
		</div>
	</section>

	<section class="reci-container-full bg-neutral-100 py-20 border-t border-b border-zinc-200">
		<div class="reci-container flex flex-col gap-12">
			<div class="text-center">
				<h2 class="text-neutral-800 text-4xl font-bold font-heading">
					<?php echo esc_html( 'Explore Community' ); ?>
				</h2>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php
				$explore_links = [
					[
						'label' => $is_logged_in ? 'Go to Dashboard' : 'Join as a Member',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
						'url'   => $member_cta_url,
					],
					[
						'label' => 'Become a Collaborator',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
						'url'   => $collab_url,
					],
					[
						'label' => 'Find Collaborators',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',
						'url'   => get_post_type_archive_link( 'reci_author' ) ?: home_url( '/collaborators/' ),
					],
				];
				foreach ( $explore_links as $link ) :
				?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-lg border border-zinc-200 shadow-sm hover:border-amber-400 hover:shadow-md transition-all text-center group">
						<?php echo $link['icon']; ?>
						<span class="text-neutral-800 text-lg font-bold group-hover:text-amber-500 transition-colors"><?php echo esc_html( $link['label'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="reci-container py-24">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<?php
			$pillars = [
				[
					'title' => 'For Members',
					'desc'  => 'Members follow collaborators and interest areas, save work, manage notifications, and use the dashboard as the home of their personalized feed.',
				],
				[
					'title' => 'For Collaborators',
					'desc'  => 'Collaborators maintain public profiles and publish content and resources through RECI, making their work visible and followable across the platform.',
				],
				[
					'title' => 'How It Works',
					'desc'  => 'Join as a Member, follow the work that matters to you, and use your dashboard to discover and manage engagement. If you want to contribute as a Collaborator, use the dedicated Become a Collaborator flow.',
				],
			];
			foreach ( $pillars as $pillar ) :
			?>
				<div class="p-8 bg-neutral-800 rounded-xl flex flex-col gap-6 text-white border-b-4 border-amber-400 transform transition-transform hover:-translate-y-1">
					<div class="flex items-center gap-3">
						<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
						<h3 class="text-3xl font-bold text-white font-heading"><?php echo esc_html( $pillar['title'] ); ?></h3>
					</div>
					<p class="text-gray-300 text-lg font-normal leading-relaxed">
						<?php echo esc_html( $pillar['desc'] ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="reci-container pb-24">
		<div class="rounded-xl bg-white border border-zinc-200 p-10 shadow-sm flex flex-col gap-6">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
				<h2 class="text-3xl font-bold text-neutral-900 font-heading"><?php echo esc_html( 'Member Dashboard' ); ?></h2>
			</div>
			<p class="text-neutral-600 text-lg leading-8">
				<?php echo esc_html( 'Community explains how RECI participation works. Your dashboard is where your personalized feed, bookmarks, notifications, journal, and contribution tools live once you are signed in.' ); ?>
			</p>
			<div>
				<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="inline-flex items-center px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-white text-sm font-semibold rounded-lg transition-colors">
					<?php echo esc_html( 'Go to Dashboard' ); ?>
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
