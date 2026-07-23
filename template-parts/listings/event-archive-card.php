<?php

/**
 * Event archive card — follows platform listing template pattern.
 *
 * @var array $args
 */

$type_label      = $args['type_label'] ?? 'Event';
$type_badge      = $args['type_badge_class'] ?? 'bg-amber-400';
$type_text       = $args['type_text_class'] ?? 'text-neutral-800';
$status          = $args['status'] ?? 'Upcoming';
$date            = $args['date'] ?? '';
$time            = $args['time'] ?? '';
$title           = $args['title'] ?? '';
$excerpt         = $args['excerpt'] ?? '';
$author_name     = $args['author_name'] ?? '';
$author_url      = $args['author_url'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms    = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$link_url        = $args['link_url'] ?? '#';
$category        = $args['category'] ?? '';
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';

$is_past     = in_array( strtolower( $status ), [ 'past', 'ended' ], true );
$status_badge = $is_past ? 'bg-zinc-400 text-white' : 'bg-amber-400 text-neutral-800';
$type_key     = strtolower( trim( $type_label ) );
$type_archives = [
	'article' => get_post_type_archive_link( 'post' ) ?: home_url( '/articles/' ),
	'podcast' => get_post_type_archive_link( 'reci_podcast' ) ?: home_url( '/podcasts/' ),
	'video'   => get_post_type_archive_link( 'reci_video' ) ?: home_url( '/videos/' ),
];
$type_archive_url = $type_archives[ $type_key ] ?? '#';
?>

<div data-layer="Content" class="Content flex-1 self-stretch inline-flex flex-col justify-start items-start gap-5">
	<?php if ( $image_url ) : ?>
		<div class="self-stretch relative overflow-hidden rounded-lg bg-neutral-200 aspect-[16/9]">
			<img data-layer="Image" class="Image w-full h-full object-cover absolute inset-0" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
			<span class="absolute top-3 left-3 px-2 py-1 rounded text-xs font-medium z-10 <?php echo esc_attr( $status_badge ); ?>"><?php echo esc_html( $status ); ?></span>
		</div>
	<?php endif; ?>

	<div data-layer="Content" class="Content self-stretch flex-1 flex flex-col justify-between items-start">
		<div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-3">
			<div data-layer="Content" class="Content self-stretch reci-meta-row flex-wrap">
				<a href="<?php echo esc_url( $type_archive_url ); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr( $type_badge ); ?> rounded flex justify-center items-center gap-2.5 no-underline">
					<div data-layer="Event" class="Event justify-start <?php echo esc_attr( $type_text ); ?> text-sm font-normal leading-4"><?php echo esc_html( $type_label ); ?></div>
				</a>
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Date" class="Date justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html( $date ); ?></div>
				<?php if ( $time ) : ?>
					<div data-layer="Tag" class="Tag tag-dot"></div>
					<div data-layer="Time" class="Time justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html( $time ); ?></div>
				<?php endif; ?>
				<?php if ( $author_name !== '' ) : ?>
					<div data-layer="Tag" class="Tag tag-dot"></div>
					<div data-layer="Author" class="Author flex justify-start items-center gap-1">
						<?php if ( $author_url !== '' ) : ?>
							<a href="<?php echo esc_url( $author_url ); ?>" class="justify-start text-neutral-600 text-sm font-normal leading-4 hover:underline no-underline"><?php echo esc_html( $author_name ); ?></a>
						<?php else : ?>
							<div class="justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html( $author_name ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $link_url ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" data-layer="Title" class="Title self-stretch reci-side-listing-title line-clamp-2 no-underline"><?php echo esc_html( $title ); ?></a>
			<?php else : ?>
				<div data-layer="Title" class="Title self-stretch reci-side-listing-title line-clamp-2"><?php echo esc_html( $title ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( $category !== '' || ! empty( $sphere_terms ) ) : ?>
			<div class="self-stretch inline-flex justify-start items-center gap-2 flex-nowrap overflow-hidden">
				<?php if ( $category !== '' ) : ?>
					<a href="<?php echo esc_url( get_category_link( get_cat_ID( $category ) ) ); ?>" class="tag"><?php echo esc_html( $category ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $sphere_terms ) ) :
					$s = $sphere_terms[0]; ?>
					<a href="<?php echo esc_url( $s['url'] ); ?>" class="sphere" style="background-color: <?php echo esc_attr( $s['color'] ); ?>1a;">
						<span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr( $s['color'] ); ?>;"></span>
						<span class="font-medium" style="color: <?php echo esc_attr( $s['color'] ); ?>;"><?php echo esc_html( $s['name'] ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
