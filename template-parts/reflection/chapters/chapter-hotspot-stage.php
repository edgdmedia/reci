<?php
/**
 * Immersive chapter: hotspot stage.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-hotspots');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$bg_url       = (string) ($content['background_image_url'] ?? '');
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$subtitle     = (string) ($content['subtitle'] ?? 'Tap the icons to uncover the story');
$continue     = (string) ($content['continue_label'] ?? 'Continue');
$items        = is_array($content['items'] ?? null) ? $content['items'] : [];
$min_seen     = max(1, (int) (($chapter['state']['completion']['min_required'] ?? count($items))));
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
$style        = $bg_url !== '' ? "background-image:url('" . esc_url($bg_url) . "');" : '';
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--hotspot-stage" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="hotspot_stage" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>" data-hotspot-min="<?php echo esc_attr((string) $min_seen); ?>">
    <div class="reci-chapter__bg reci-chapter__bg--cover" <?php if ($style) : ?>style="<?php echo esc_attr($style); ?>"<?php endif; ?>></div>
    <div class="reci-chapter__overlay reci-chapter__overlay--soft"></div>
    <div class="reci-hotspot-stage__header">
        <?php if ($title !== '') : ?><h2 class="reci-hotspot-stage__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($subtitle !== '') : ?><p class="reci-hotspot-stage__subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
    </div>
    <div class="reci-hotspot-stage__surface">
        <?php foreach ($items as $index => $item) :
            if (! is_array($item)) {
                continue;
            }
            $x = isset($item['x']) ? (float) $item['x'] : 50;
            $y = isset($item['y']) ? (float) $item['y'] : 50;
            ?>
            <button type="button" class="reci-hotspot" style="left: <?php echo esc_attr((string) $x); ?>%; top: <?php echo esc_attr((string) $y); ?>%;" data-hotspot data-hotspot-index="<?php echo esc_attr((string) $index); ?>" data-title="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" data-content="<?php echo esc_attr((string) ($item['content'] ?? '')); ?>" data-label="<?php echo esc_attr((string) ($item['label'] ?? ($index + 1))); ?>">
                <span><?php echo esc_html((string) ($item['label'] ?? '+')); ?></span>
            </button>
        <?php endforeach; ?>
        <aside class="reci-hotspot-detail" data-hotspot-detail>
            <div class="reci-hotspot-detail__eyebrow"><?php esc_html_e('Detail', 'reci-media-hub'); ?></div>
            <h3 class="reci-hotspot-detail__title" data-hotspot-title><?php esc_html_e('Select a point', 'reci-media-hub'); ?></h3>
            <p class="reci-hotspot-detail__content" data-hotspot-content><?php esc_html_e('Reveal each hotspot to move through this chapter.', 'reci-media-hub'); ?></p>
            <button type="button" class="reci-hotspot-detail__close" data-hotspot-close><?php esc_html_e('Close', 'reci-media-hub'); ?></button>
        </aside>
    </div>
    <div class="reci-chapter__footer reci-chapter__footer--center">
        <button type="button" class="reci-chapter-button" data-hotspot-continue hidden data-reflection-action="continue"><?php echo esc_html($continue); ?></button>
    </div>
</section>
