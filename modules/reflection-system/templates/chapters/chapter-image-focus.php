<?php
if (! defined('ABSPATH')) { exit; }

$variant = $args['variant'] ?? 'default';

get_template_part('modules/reflection-system/templates/chapters/image-focus/' . $variant, null, $args);
