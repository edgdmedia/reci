<?php

/**
 * Template Name: Glossary
 *
 * @package reci-media-hub
 */

if (!defined('ABSPATH')) {
	exit;
}

$page_title    = 'Glossary';
$page_subtitle  = 'Shared language is the foundation of meaningful dialogue. This glossary provides clear, accessible definitions for key terms used across the RECI platform.';
$template_dir   = get_template_directory_uri();

$intro = '<p>Words and their multiple uses reflect the tremendous diversity that characterizes our society. Indeed, universally agreed upon language on issues relating to racism is nonexistent. Even the most frequently used words in any discussion on race can easily cause confusion, which leads to controversy and hostility. It is essential to achieve some degree of shared understanding, particularly when using the most common terms. In this way, the quality of dialogue and discourse on race can be enhanced.</p>
<p>Language can be used deliberately to engage and support community anti-racism coalitions and initiatives, or to inflame and divide them. Discussing definitions can engage and support coalitions. However, it is important for groups to decide the extent to which they must have consensus and where it is okay for people to disagree.</p>
<p>Many of these terms have evolved over time and may carry different meanings for different people. This glossary is a living document — updated as our understanding deepens and language evolves.</p>';

$glossary_posts = get_posts([
	'post_type'      => 'reci_glossary_term',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
]);

$glossary = [];
foreach ($glossary_posts as $post) {
	$first_letter = strtoupper(mb_substr($post->post_title, 0, 1));
	if (! isset($glossary[$first_letter])) {
		$glossary[$first_letter] = [];
	}
	$glossary[$first_letter][] = [
		'term'        => $post->post_title,
		'definitions' => [$post->post_content],
	];
}

get_header();
?>
<main class="layout-page">

	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<!-- Intro -->
	<section class="reci-container py-14">
		<div class=" prose prose-lg prose-neutral">
			<?php echo wp_kses_post($intro); ?>
		</div>
	</section>

	<!-- Glossary -->
	<section class="reci-container pb-20">
		<div class="flex items-center justify-between mb-8">
			<div class="flex items-center gap-2">
				<span class="w-2 h-2 px-2 py-1 bg-amber-400 rounded-sm"></span>
				<h2 class="text-neutral-800 text-3xl font-bold font-heading">Terms</h2>
			</div>
			<button
				type="button"
				id="glossaryToggleAll"
				class="text-sm font-medium text-amber-600 hover:text-amber-700 underline underline-offset-2"
				data-expanded="false"
			>Expand All</button>
		</div>

		<?php foreach ($glossary as $letter => $terms): ?>
		<div class="mb-10">
			<h3 class="text-2xl font-bold font-heading text-neutral-800 mb-4 pb-2 border-b border-zinc-300"><?php echo esc_html($letter); ?></h3>
			<div class="flex flex-col divide-y divide-zinc-200">
				<?php foreach ($terms as $entry): ?>
				<?php
				$item_id = 'glossary-term-' . sanitize_title($entry['term']);
				?>
				<div class="glossary-term py-4">
					<button
						type="button"
						class="glossary-term-trigger flex items-center justify-between w-full text-left group"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr($item_id); ?>"
					>
						<span class="text-xl font-semibold text-neutral-800 group-hover:text-amber-700 transition-colors"><?php echo esc_html($entry['term']); ?></span>
						<svg class="glossary-term-icon w-5 h-5 text-neutral-500 shrink-0 ml-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
						</svg>
					</button>
					<div
						id="<?php echo esc_attr($item_id); ?>"
						class="glossary-term-content mt-3 hidden"
						role="region"
					>
						<?php foreach ($entry['definitions'] as $def): ?>
						<p class="text-neutral-600 text-lg leading-relaxed"><?php echo esc_html($def); ?></p>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</section>

</main>

<script>
(function() {
	'use strict';

	var toggleBtn = document.getElementById('glossaryToggleAll');
	var terms = document.querySelectorAll('.glossary-term');

	function updateExpandAllLabel() {
		var anyCollapsed = Array.from(terms).some(function(t) {
			var trigger = t.querySelector('.glossary-term-trigger');
			return trigger && trigger.getAttribute('aria-expanded') === 'false';
		});
		if (toggleBtn) {
			toggleBtn.textContent = anyCollapsed ? 'Expand All' : 'Collapse All';
			toggleBtn.setAttribute('data-expanded', anyCollapsed ? 'false' : 'true');
		}
	}

	function toggleTerm(trigger) {
		var content = document.getElementById(trigger.getAttribute('aria-controls'));
		if (!content) return;
		var isOpen = trigger.getAttribute('aria-expanded') === 'true';
		trigger.setAttribute('aria-expanded', !isOpen);
		content.classList.toggle('hidden');
		var icon = trigger.querySelector('.glossary-term-icon');
		if (icon) {
			icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
		}
		updateExpandAllLabel();
	}

	terms.forEach(function(term) {
		var trigger = term.querySelector('.glossary-term-trigger');
		if (trigger) {
			trigger.addEventListener('click', function() {
				toggleTerm(trigger);
			});
		}
	});

	if (toggleBtn) {
		toggleBtn.addEventListener('click', function() {
			var expandAll = toggleBtn.getAttribute('data-expanded') === 'false';
			terms.forEach(function(term) {
				var trigger = term.querySelector('.glossary-term-trigger');
				if (!trigger) return;
				var isOpen = trigger.getAttribute('aria-expanded') === 'true';
				if (expandAll && !isOpen) {
					toggleTerm(trigger);
				} else if (!expandAll && isOpen) {
					toggleTerm(trigger);
				}
			});
		});
	}

	function openTermFromHash() {
		var hash = window.location.hash.slice(1);
		if (!hash) return;
		var term = document.getElementById(hash);
		if (!term) return;
		var trigger = term.closest('.glossary-term')?.querySelector('.glossary-term-trigger');
		if (trigger && trigger.getAttribute('aria-expanded') === 'false') {
			toggleTerm(trigger);
			setTimeout(function() {
				trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}, 100);
		}
	}
	openTermFromHash();
	window.addEventListener('hashchange', openTermFromHash);
})();
</script>

<?php get_footer(); ?>
