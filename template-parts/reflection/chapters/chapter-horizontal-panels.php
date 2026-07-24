<?php
/**
 * Immersive chapter: horizontal panels.
 *
 * @package reci-media-hub
 */

$chapter      = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id           = (string) ($chapter['id'] ?? 'chapter-horizontal-panels');
$content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$items        = is_array($content['items'] ?? null) ? $content['items'] : [];
$theme        = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent       = (string) ($presentation['accent'] ?? 'amber');
$continue     = (string) ($content['continue_label'] ?? 'Continue');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--horizontal-panels" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="horizontal_panels" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-horizontal-panels" data-horizontal-panels>
        <div class="reci-horizontal-panels__track" data-horizontal-track>
            <?php foreach ($items as $index => $item) :
                if (! is_array($item)) {
                    continue;
                }
                $bg_url = (string) ($item['background_image_url'] ?? $item['image_url'] ?? '');
                $style  = $bg_url !== '' ? "background-image:url('" . esc_url($bg_url) . "');" : '';
                ?>
                <article class="reci-horizontal-panels__panel" data-horizontal-panel>
                    <div class="reci-horizontal-panels__panel-bg" <?php if ($style) : ?>style="<?php echo esc_attr($style); ?>"<?php endif; ?>></div>
                    <div class="reci-horizontal-panels__panel-overlay"></div>
                    <div class="reci-horizontal-panels__panel-content">
                        <?php if (! empty($item['title'])) : ?><h2 class="reci-horizontal-panels__title"><?php echo esc_html((string) $item['title']); ?></h2><?php endif; ?>
                        <?php if (! empty($item['content'])) : ?><p class="reci-horizontal-panels__text"><?php echo esc_html((string) $item['content']); ?></p><?php endif; ?>
                        <?php if ($index === array_key_last($items)) : ?>
                            <button type="button" class="reci-chapter-button" data-horizontal-continue data-reflection-action="continue" hidden><?php echo esc_html($continue); ?></button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="reci-horizontal-panels__controls">
            <button type="button" class="reci-horizontal-panels__nav" data-horizontal-prev><?php esc_html_e('Previous', 'reci-media-hub'); ?></button>
            <button type="button" class="reci-horizontal-panels__nav" data-horizontal-next><?php esc_html_e('Next', 'reci-media-hub'); ?></button>
        </div>
    </div>
</section>
