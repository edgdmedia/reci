<?php
/**
 * Immersive chapter: reflection prompt.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-reflection-prompt');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$placeholder  = (string) ($content['placeholder'] ?? 'Share your thoughts...');
$button       = (string) ($content['button_label'] ?? 'Complete Journey');
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--reflection-prompt" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="reflection_prompt" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--narrow">
        <?php if ($title !== '') : ?><h2 class="reci-reflection-prompt__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <textarea class="reci-reflection-prompt__input" placeholder="<?php echo esc_attr($placeholder); ?>"></textarea>
        <button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($button); ?></button>
    </div>
</section>
