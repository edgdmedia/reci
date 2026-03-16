<?php
/**
 * Immersive chapter: generic content stage.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-content');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$body         = (string) ($content['content'] ?? $content['text'] ?? '');
$button       = (string) ($content['button_label'] ?? 'Continue');
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--content-stage" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="content_stage" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--narrow">
        <?php if ($title !== '') : ?><h2 class="reci-content-stage__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($body !== '') : ?><div class="reci-content-stage__body"><?php echo wp_kses_post(wpautop($body)); ?></div><?php endif; ?>
        <?php if (! empty($chapter['state']['completion'])) : ?>
            <button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($button); ?></button>
        <?php endif; ?>
    </div>
</section>
