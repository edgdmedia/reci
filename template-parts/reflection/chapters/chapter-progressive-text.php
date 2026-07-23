<?php
/**
 * Immersive chapter: progressive text.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-progressive-text');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title        = (string) ($content['title'] ?? $chapter['title'] ?? '');
$items        = is_array($content['paragraphs'] ?? null) ? $content['paragraphs'] : (is_array($content['items'] ?? null) ? $content['items'] : []);
$prompt       = (string) ($content['prompt'] ?? 'Reveal the next passage');
$button       = (string) ($content['button_label'] ?? 'Reveal');
$continue     = (string) ($content['continue_label'] ?? 'Continue');
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--progressive-text" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="progressive_text" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--narrow">
        <?php if ($title !== '') : ?><h2 class="reci-progressive-text__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <div class="reci-progressive-text__body" data-progressive-body>
            <?php foreach ($items as $index => $item) :
                $text = is_array($item) ? (string) ($item['content'] ?? $item['text'] ?? '') : (string) $item;
                if ($text === '') {
                    continue;
                }
                ?>
                <p class="reci-progressive-text__paragraph" data-progressive-item hidden><?php echo esc_html($text); ?></p>
            <?php endforeach; ?>
        </div>
        <div class="reci-progressive-text__controls">
            <?php if ($prompt !== '') : ?><div class="reci-progressive-text__prompt"><?php echo esc_html($prompt); ?></div><?php endif; ?>
            <button type="button" class="reci-progressive-text__next" data-progressive-next><?php echo esc_html($button); ?></button>
            <button type="button" class="reci-chapter-button" data-progressive-continue data-reflection-action="continue" hidden><?php echo esc_html($continue); ?></button>
        </div>
    </div>
</section>
