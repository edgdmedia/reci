<?php
/**
 * Dashboard sidebar navigation.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$current_page = get_query_var( 'dashboard_page', '' );
$is_author    = current_user_can( 'edit_posts' );
?>

<aside class="dashboard-sidebar w-full lg:w-60 shrink-0 bg-white border-r border-zinc-200 lg:min-h-screen <?php echo empty($args['mobile']) ? 'hidden lg:block' : ''; ?>">
	<nav class="py-6 px-4 flex flex-col h-full" aria-label="Dashboard navigation">
		<ul class="space-y-1 flex-1">
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === '' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
					Dashboard
				</a>
			</li>

			<?php if ( $is_author ) : ?>
			<li class="pt-3">
				<p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-400">Author</p>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/my-content/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'my-content' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
					My Content
				</a>
			</li>
			<?php endif; ?>

			<li class="pt-3">
				<p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-400">Content</p>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/bookmarks/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'bookmarks' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
					Bookmarks
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/notifications/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'notifications' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
					Notifications
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/journal/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'journal' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
					Journal
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/comments/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'comments' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
					Comments
				</a>
			</li>

			<li class="pt-3">
				<p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-400">Account</p>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/profile/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'profile' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
					Profile
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/dashboard/settings/' ) ); ?>"
				   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $current_page === 'settings' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
					Settings
				</a>
			</li>
		</ul>

		<div class="pt-4 mt-4 border-t border-zinc-200">
			<div class="flex items-center gap-3 px-3 py-2">
				<?php echo get_avatar( $current_user->ID, 32, '', $current_user->display_name, [ 'class' => 'rounded-full' ] ); ?>
				<div class="min-w-0">
					<p class="text-sm font-medium text-zinc-800 truncate"><?php echo esc_html( $current_user->display_name ); ?></p>
					<p class="text-xs text-zinc-500 truncate"><?php echo esc_html( $current_user->user_email ); ?></p>
				</div>
			</div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
			   class="flex items-center gap-2 px-3 py-2 mt-1 text-sm text-zinc-600 hover:text-zinc-900 transition-colors rounded-lg hover:bg-zinc-100">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
				Back to Site
			</a>
		</div>
	</nav>
</aside>
