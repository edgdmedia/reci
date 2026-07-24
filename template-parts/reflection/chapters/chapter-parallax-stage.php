<?php
/**
 * Immersive chapter: parallax stage.
 *
 * @package reci-media-hub
 */

$chapter = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id = (string) ($chapter['id'] ?? 'chapter-parallax-stage');
$content = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$items = is_array($content['items'] ?? null) ? $content['items'] : [];
$title = (string) ($content['title'] ?? $chapter['title'] ?? '');
$body = (string) ($content['content'] ?? $content['subtitle'] ?? '');
$button = (string) ($content['button_label'] ?? 'Continue');
$theme = (string) ($presentation['theme'] ?? 'immersive-dark');
$accent = (string) ($presentation['accent'] ?? 'amber');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--parallax-stage" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="parallax_stage" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-parallax-stage" data-parallax-stage>
        <?php foreach ($items as $index => $item) :
            if (! is_array($item)) {
                continue;
            }
            $image = (string) ($item['image_url'] ?? '');
            $style = $image !== '' ? "background-image:url('" . esc_url($image) . "');" : '';
            $speed = isset($item['x']) ? (float) $item['x'] : (0.03 * ($index + 1));
            ?>
            <div class="reci-parallax-stage__layer" data-parallax-layer data-speed="<?php echo esc_attr((string) $speed); ?>" <?php if ($style) : ?>style="<?php echo esc_attr($style); ?>"<?php endif; ?>></div>
        <?php endforeach; ?>
        <div class="reci-parallax-stage__overlay"></div>
        <div class="reci-parallax-stage__content">
            <?php if ($title !== '') : ?><h2 class="reci-parallax-stage__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
            <?php if ($body !== '') : ?><p class="reci-parallax-stage__body"><?php echo esc_html($body); ?></p><?php endif; ?>
            <button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($button); ?></button>
        </div>
    </div>
</section>
