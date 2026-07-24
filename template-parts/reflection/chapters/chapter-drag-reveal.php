<?php
/**
 * Immersive chapter: drag reveal.
 *
 * @package reci-media-hub
 */

$chapter = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id = (string) ($chapter['id'] ?? 'chapter-drag-reveal');
$content = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$title = (string) ($content['title'] ?? $chapter['title'] ?? '');
$subtitle = (string) ($content['subtitle'] ?? $content['content'] ?? '');
$instruction = (string) ($content['instruction'] ?? 'Drag down to break');
$success = (string) ($content['success_label'] ?? 'Broken');
$continue = (string) ($content['continue_label'] ?? 'Continue');
$theme = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--drag-reveal" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="drag_reveal" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner reci-chapter__inner--center">
        <div class="reci-drag-reveal">
            <?php if ($title !== '') : ?><h2 class="reci-drag-reveal__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
            <?php if ($subtitle !== '') : ?><p class="reci-drag-reveal__subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
            <div class="reci-drag-reveal__chain" data-drag-reveal-chain>
                <div class="reci-drag-reveal__link reci-drag-reveal__link--fixed"></div>
                <div class="reci-drag-reveal__link reci-drag-reveal__link--movable" data-drag-reveal-link></div>
            </div>
            <div class="reci-drag-reveal__instruction" data-drag-reveal-status><?php echo esc_html($instruction); ?></div>
            <button type="button" class="reci-chapter-button" data-drag-reveal-continue data-reflection-action="continue" hidden><?php echo esc_html($continue); ?></button>
            <script type="application/json" data-drag-reveal-config><?php echo wp_json_encode(['success' => $success], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
        </div>
    </div>
</section>
