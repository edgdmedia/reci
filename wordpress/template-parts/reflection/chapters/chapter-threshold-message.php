<?php
/**
 * Immersive chapter: threshold message.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-threshold-message');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$button       = (string) ($content['button_label'] ?? 'Continue');
$bg_mode      = (string) ($content['background_mode'] ?? 'lightwash');
$theme        = (string) ($presentation['theme'] ?? 'light');
$accent       = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--threshold-message reci-threshold-message--<?php echo esc_attr(sanitize_html_class($bg_mode)); ?>" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="threshold_message" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--center">
        <div class="reci-threshold-message__box">
            <?php if ($title !== '') : ?><h2 class="reci-threshold-message__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
            <button type="button" class="reci-chapter-button reci-chapter-button--inverse" data-reflection-action="continue"><?php echo esc_html($button); ?></button>
        </div>
    </div>
</section>
