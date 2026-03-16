<?php
/**
 * Immersive chapter: data cards.
 *
 * @package reci-media-hub
 */

$chapter = is_array($args['chapter'] ?? null) ? $args['chapter'] : [];
$id = (string) ($chapter['id'] ?? 'chapter-data-cards');
$content = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
$presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
$items = is_array($content['items'] ?? null) ? $content['items'] : [];
$title = (string) ($content['title'] ?? $chapter['title'] ?? '');
$subtitle = (string) ($content['subtitle'] ?? '');
$continue = (string) ($content['continue_label'] ?? 'Continue');
$theme = (string) ($presentation['theme'] ?? 'light');
$accent = (string) ($presentation['accent'] ?? 'blue');
?>
<section id="<?php echo esc_attr($id); ?>" class="reci-reflection-chapter reci-chapter reci-chapter--data-cards" data-reflection-chapter data-chapter-id="<?php echo esc_attr($id); ?>" data-chapter-type="data_cards" data-theme="<?php echo esc_attr($theme); ?>" data-accent="<?php echo esc_attr($accent); ?>">
    <div class="reci-chapter__inner">
        <div class="reci-data-cards">
            <header class="reci-data-cards__header">
                <?php if ($title !== '') : ?><h2 class="reci-data-cards__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
                <?php if ($subtitle !== '') : ?><p class="reci-data-cards__subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
            </header>
            <div class="reci-data-cards__grid" data-data-cards-grid>
                <?php foreach ($items as $index => $item) : if (! is_array($item)) { continue; } ?>
                    <article class="reci-data-card" data-data-card>
                        <div class="reci-data-card__icon"><?php echo esc_html((string) ($item['icon'] ?? '•')); ?></div>
                        <div class="reci-data-card__label"><?php echo esc_html((string) ($item['label'] ?? '')); ?></div>
                        <div class="reci-data-card__stat"><?php echo esc_html((string) ($item['stat'] ?? '')); ?> <span><?php echo esc_html((string) ($item['unit'] ?? '')); ?></span></div>
                        <p class="reci-data-card__summary"><?php echo esc_html((string) ($item['content'] ?? '')); ?></p>
                        <div class="reci-data-card__detail">
                            <?php if (! empty($item['problem'])) : ?><p class="reci-data-card__problem"><?php echo esc_html((string) $item['problem']); ?></p><?php endif; ?>
                            <?php if (! empty($item['solution'])) : ?><button type="button" class="reci-data-card__toggle" data-data-card-toggle><?php esc_html_e('View Solution', 'reci-media-hub'); ?></button><p class="reci-data-card__solution" data-data-card-solution hidden><?php echo esc_html((string) $item['solution']); ?></p><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="reci-chapter__footer reci-chapter__footer--static"><button type="button" class="reci-chapter-button" data-reflection-action="continue"><?php echo esc_html($continue); ?></button></div>
        </div>
    </div>
</section>
