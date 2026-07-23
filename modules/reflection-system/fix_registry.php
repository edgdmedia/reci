<?php
$file = '/Users/olalekan/Projects/reci/media-hub/wordpress/modules/reflection-system/inc/reflection-system-registry.php';
$content = file_get_contents($file);

// Fix the single quote escaping issue
// Before:
// 'heading' => 'Standing tall wasn\',
// 					'body' => '#a0a0a0't just physical.
$content = str_replace(
	"'heading' => 'Standing tall wasn\',\n\t\t\t\t\t'body' => '#a0a0a0't just physical",
	"'text' => 'Standing tall wasn\'t just physical",
	$content
);

// Fix general texts
// We want to revert things that are not hex colors.
// Replace 'heading' => '...', 'body' => '#a0a0a0' back to 'text' => '...'
$content = preg_replace_callback("/'heading'\s*=>\s*'([^']*)',\s*'body'\s*=>\s*'#a0a0a0'/", function($m) {
	if (strpos($m[1], '#') === 0) {
		return $m[0]; // Keep it if it starts with #
	}
	return "'text' => '" . $m[1] . "'";
}, $content);

file_put_contents($file, $content);
echo "Fixed.\n";
