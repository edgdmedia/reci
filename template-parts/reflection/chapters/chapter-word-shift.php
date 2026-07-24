<?php
/**
 * Immersive chapter: word shift.
 *
 * Content uses token format {{word|SHIFT}}.
 *
 * @package reci-media-hub
 */

$chapter = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id = (string) ($chapter['id'] ?? 'chapter-word-shift');
$content = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title = (string) ($content['title'] ?? $chapter['title'] ?? '');
$body = (string) ($content['content'] ?? '');
$continue = (string) ($content['continue_label'] ?? 'Continue');
$theme = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent = (string) ($presentation['accent'] ?? 'amber');
$body = preg_replace_callback('/\{\{([^\|\}]+)\|([^\}]+)\}\}/', static function ($matches) {
    return '<span class="reci-word-shift__word" data-shift-word data-default="' . esc_attr($matches[1]) . '" data-shift="' . esc_attr($matches[2]) . '">' . esc_html($matches[1]) . '</span>';
}, $body ?: '');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--word-shift" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="word_shift" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--narrow">
        <?php if ($title !== '') : ?><h2 class="reci-word-shift__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <div class="reci-word-shift__body"><?php echo wp_kses_post(wpautop($body)); ?></div>
        <button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($continue); ?></button>
    </div>
</section>
