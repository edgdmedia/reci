<?php
/**
 * Immersive chapter: step sequence.
 *
 * @package reci-media-hub
 */

$chapter = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id = (string) ($chapter['id'] ?? 'chapter-step-sequence');
$content = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$items = is_array($content['items'] ?? null) ? $content['items'] : [];
$title = (string) ($content['title'] ?? $chapter['title'] ?? '');
$subtitle = (string) ($content['subtitle'] ?? '');
$theme = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent = (string) ($presentation['accent'] ?? 'amber');
$button = (string) ($content['button_label'] ?? 'Take the step');
$continue = (string) ($content['continue_label'] ?? 'Continue');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--step-sequence" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="step_sequence" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--center">
        <div class="reci-step-sequence">
            <?php if ($title !== '') : ?><h2 class="reci-step-sequence__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
            <?php if ($subtitle !== '') : ?><p class="reci-step-sequence__subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
            <div class="reci-step-sequence__footprints" data-step-footprints>
                <?php foreach ($items as $index => $item) : ?>
                    <span class="reci-step-sequence__footprint" data-step-footprint><?php echo esc_html((string) ($item['label'] ?? 'Step')); ?></span>
                <?php endforeach; ?>
            </div>
            <button type="button" class="reci-chapter-button" data-step-sequence-button><?php echo esc_html($button); ?></button>
            <article class="reci-step-sequence__card" data-step-sequence-card hidden>
                <h3 data-step-sequence-title></h3>
                <p data-step-sequence-content></p>
                <button type="button" class="reci-chapter-button" data-step-sequence-continue hidden data-reflection-action="continue"><?php echo esc_html($continue); ?></button>
            </article>
            <script type="application/json" data-step-sequence-items><?php echo wp_json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
        </div>
    </div>
</section>
