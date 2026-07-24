<?php
require_once('../../../../wp-load.php');
$args = array(
    'post_type' => 'reci_reflection',
    's' => 'We Humans',
    'posts_per_page' => 1
);
$query = new WP_Query($args);
if ($query->have_posts()) {
    $post = $query->posts[0];
    echo "Post ID: " . $post->ID . "\n";
    echo "Title: " . $post->post_title . "\n";
    $payload = RECI_Reflection_Content_Service::get_payload($post->ID);
    echo "Blueprint:\n";
    echo json_encode($payload['blueprint'], JSON_PRETTY_PRINT);
} else {
    echo "No post found.";
}
