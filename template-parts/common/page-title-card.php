<?php

/**
 * Reusable page title card.
 *
 * Args:
 * - title: string
 * - subtitle: string
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$title = isset($args["title"]) ? (string) $args["title"] : "";
$subtitle = isset($args["subtitle"]) ? (string) $args["subtitle"] : "";
?>
<div class="reci-container-full border-b border-zinc-400">
	<div class="reci-container py-14">
		<div class="flex flex-col md:flex-row justify-start md:justify-between items-center gap-6">
			<div class="flex items-center gap-3 w-full md:w-2/5 lg:w-1/2">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-neutral-800 text-5xl font-bold font-heading leading-[1.05]"><?php echo esc_html(
        $title,
    ); ?></h1>
			</div>
			<?php if ($subtitle !== ""): ?>
				<div class="lg:pl-10 lg:border-l lg:border-zinc-400 w-full md:w-3/5 lg:w-1/2">
					<p class="text-neutral-700 text-xl font-normal leading-7 "><?php echo esc_html(
         $subtitle,
     ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
