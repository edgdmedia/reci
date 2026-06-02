<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('reci_get_post_spheres')) {
    function reci_get_post_spheres(int $post_id): array {
        $terms = wp_get_post_terms($post_id, 'reci_sphere');
        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        $spheres = [];
        foreach ($terms as $term) {
            $default = reci_media_hub_get_sphere_default_by_slug($term->slug) ?? [];
            $color   = (string) get_term_meta($term->term_id, 'reci_sphere_color', true);
            $name    = (string) get_term_meta($term->term_id, 'reci_sphere_awareness', true);
            $num     = (string) get_term_meta($term->term_id, 'reci_sphere_num', true);
            $action  = (string) get_term_meta($term->term_id, 'reci_sphere_action', true);

            if ($color === '') {
                $color = (string) ($default['color'] ?? '#9B4D3A');
            }
            if ($name === '') {
                $name = $term->name;
            }
            if ($num === '') {
                $num = (string) ($default['num'] ?? '');
            }
            if ($action === '') {
                $action = (string) ($default['action'] ?? '');
            }

            $link = get_term_link($term);

            $image_id = get_term_meta($term->term_id, 'reci_sphere_content_image_id', true);
            $image_url = '';
            if ($image_id) {
                $image_url = wp_get_attachment_url($image_id);
            } else {
                $filename = $default['image_file'] ?? '';
                if ($filename) {
                    $image_url = get_template_directory_uri() . '/assets/images/site/reci-spheres/' . $filename;
                }
            }

            $spheres[] = [
                'term_id'           => $term->term_id,
                'slug'              => $term->slug,
                'name'              => $name,
                'num'               => $num,
                'action'            => $action,
                'color'             => $color,
                'url'               => is_string($link) ? $link : '',
                'content_image_url' => $image_url,
            ];
        }

        return $spheres;
    }
}

if (! function_exists('reci_render_sphere_badges')) {
    function reci_render_sphere_badges(array $spheres, array $args = []): string {
        if (empty($spheres)) {
            return '';
        }

        $class     = $args['class'] ?? '';
        $size      = $args['size'] ?? 'sm';
        $dot_size  = $size === 'sm' ? 'w-2 h-2' : 'w-3 h-3';
        $text_size = $size === 'sm' ? 'text-xs' : 'text-sm';

        $html = '<div class="inline-flex items-center gap-1.5 flex-wrap ' . esc_attr($class) . '">';
        foreach ($spheres as $sphere) {
            $color = $sphere['color'] ?? '#9B4D3A';
            $html .= '<a href="' . esc_url($sphere['url'] ?? '#') . '" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded no-underline hover:opacity-80 transition-opacity ' . esc_attr($text_size) . '" style="background-color: ' . esc_attr($color) . '1a;">';
            $html .= '<span class="rounded-full ' . esc_attr($dot_size) . '" style="background-color: ' . esc_attr($color) . ';"></span>';
            $html .= '<span class="font-medium" style="color: ' . esc_attr($color) . ';">' . esc_html($sphere['name'] ?? '') . '</span>';
            $html .= '</a>';
        }
        $html .= '</div>';
        return $html;
    }
}

if (! function_exists('reci_get_all_spheres')) {
    function reci_get_all_spheres(): array {
        return reci_media_hub_get_submission_spheres();
    }
}

if (! function_exists('reci_get_sphere_hex_color')) {
    function reci_get_sphere_hex_color(string $slug): string {
        $default = reci_media_hub_get_sphere_default_by_slug($slug);
        return (string) ($default['color'] ?? '#9B4D3A');
    }
}
