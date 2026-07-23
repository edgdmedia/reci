<?php
require_once('../wp-load.php');
$args = array(
    'post_type' => 'reci_reflection',
    's' => 'Racial Disparities',
    'posts_per_page' => 1
);
$query = new WP_Query($args);
if ($query->have_posts()) {
    $post = $query->posts[0];
    echo "Post ID: " . $post->ID . "\n";
    $payload = RECI_Reflection_Content_Service::get_payload($post->ID);
    
    $updated = false;
    foreach ($payload['blueprint']['chapters'] as &$chapter) {
        if ($chapter['family'] === 'hero' && $chapter['props']['id'] === 'rd-hero') {
            $chapter['props']['use_background_image'] = '1';
            $chapter['props']['background_image'] = reci_reflection_placeholder_image();
            $chapter['props']['overlay_rgb'] = '255,255,255';
            $chapter['props']['overlay_opacity'] = '0.70';
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        RECI_Reflection_Content_Service::save_payload($post->ID, $payload);
        echo "Updated post.\n";
    } else {
        echo "Hero section not found.\n";
    }
} else {
    echo "No post found.\n";
}
