<?php
$file = '/Users/olalekan/Projects/reci/media-hub/wordpress/modules/reflection-system/inc/reflection-system-registry.php';
$content = file_get_contents($file);

// Replace the colors array definitions
// Example:
// 'text' => '#e0e0e0',
// to:
// 'heading' => '#e0e0e0',
// 'body' => '#a0a0a0',

$content = preg_replace("/'text'\s*=>\s*'([^']+)'/", "'heading' => '$1',\n\t\t\t\t\t'body' => '#a0a0a0'", $content);

file_put_contents($file, $content);
echo "Updated registry colors.\n";
