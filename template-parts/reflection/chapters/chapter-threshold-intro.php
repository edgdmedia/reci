<?php
/**
 * Immersive chapter: threshold intro.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-intro');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$bg_url       = (string) ($content['background_image_url'] ?? '');
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$subtitle     = (string) ($content['subtitle'] ?? $content['quote'] ?? '');
$button       = (string) ($content['button_label'] ?? 'Enter');
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
$style        = $bg_url !== '' ? "background-image:url('" . esc_url($bg_url) . "');" : '';
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--threshold-intro" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="threshold_intro" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__bg" <?php if ($style) : ?>style="<?php echo esc_attr($style); ?>"<?php endif; ?>></div>
    <div class="reci-chapter__overlay"></div>
    <div class="reci-chapter__inner reci-chapter__inner--center">
        <div class="reci-threshold-intro__content">
            <?php if ($title !== '') : ?>
                <h1 class="reci-threshold-intro__title"><?php echo wp_kses_post(nl2br(esc_html($title))); ?></h1>
            <?php endif; ?>
            <?php if ($subtitle !== '') : ?>
                <p class="reci-threshold-intro__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
            <button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($button); ?></button>
        </div>
    </div>
</section>
