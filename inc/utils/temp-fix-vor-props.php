<?php
add_action('init', function() {
    if (!isset($_GET['fix_vor_props'])) return;

    $post = get_page_by_title('Voices of Resistance (V2 Builder)', OBJECT, 'reci_reflection');
    if (!$post) {
        echo "Post not found!";
        exit;
    }

    $raw = get_post_meta($post->ID, '_reci_reflection_blueprint', true);
    $decoded = json_decode($raw, true);

    if (is_array($decoded) && isset($decoded['chapters'])) {
        foreach ($decoded['chapters'] as &$chapter) {
            if ($chapter['family'] === 'hotspot-stage') {
                $details = [
                    'strategy' => ['title' => 'The Strategy', 'text' => 'We gathered in the church basement not just to pray, but to plan. Every route was mapped, every risk calculated.'],
                    'resolve' => ['title' => 'The Resolve', 'text' => "Standing tall wasn't just physical. It was a reclaiming of space we were told we couldn't occupy."],
                    'legacy' => ['title' => 'The Community', 'text' => 'Young and old, doctors and janitors. In this room, titles dissolved. We were simply one people.'],
                ];
                if (isset($chapter['props']['hotspots']) && is_array($chapter['props']['hotspots'])) {
                    foreach ($chapter['props']['hotspots'] as &$hotspot) {
                        $key = $hotspot['key'] ?? '';
                        if (isset($details[$key])) {
                            $hotspot['title'] = $details[$key]['title'];
                            $hotspot['body'] = $details[$key]['text'];
                        }
                    }
                }
            }
            if ($chapter['family'] === 'progressive-text') {
                if (isset($chapter['props']['paragraphs']) && is_array($chapter['props']['paragraphs'])) {
                    $new_paras = [];
                    foreach ($chapter['props']['paragraphs'] as $p) {
                        if (is_string($p)) {
                            $new_paras[] = ['text' => $p];
                        } else {
                            $new_paras[] = $p;
                        }
                    }
                    $chapter['props']['paragraphs'] = $new_paras;
                }
            }
        }
    }

    $json = wp_json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    update_post_meta($post->ID, '_reci_reflection_blueprint', wp_slash($json));
    
    echo "SUCCESS fixed post props";
    exit;
});
