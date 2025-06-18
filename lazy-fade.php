<?php
/**
 * Plugin Name: Lazy Fade
 * Description: Lazy-load images and fade-in wp-block-group elements on scroll.
 * Version: 1.0
 * Author: Ben Dunkle
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'lazy-fade-script',
        plugin_dir_url(__FILE__) . 'lazy-fade.js',
        [],
        '1.1',
        true
    );
    wp_enqueue_style(
        'lazy-fade-style',
        plugin_dir_url(__FILE__) . 'lazy-fade.css'
    );
});

// Add lazy-fade class to images in the_content
add_filter('the_content', function ($content) {
    return preg_replace_callback(
        '/<img([^>]*?)src=["\'](.*?)["\']([^>]*)>/i',
        function ($matches) {
            $before = $matches[1];
            $src = $matches[2];
            $after = $matches[3];

            // Check for class attribute
            if (preg_match('/class=["\']([^"\']*)["\']/', $before . $after, $classMatch)) {
                // Append lazy-fade to existing class
                $newClass = trim($classMatch[1] . ' lazy-fade');
                $newAttrs = preg_replace(
                    '/class=["\']([^"\']*)["\']/',
                    'class="' . esc_attr($newClass) . '"',
                    $before . $after
                );
            } else {
                // Add new class attribute
                $newAttrs = trim($before . $after) . ' class="lazy-fade"';
            }

            // Replace src with data-src
            $newAttrs = preg_replace(
                '/\s*src=["\'](.*?)["\']/',
                '',
                $newAttrs
            );

            return '<img data-src="' . esc_attr($src) . '" ' . trim($newAttrs) . '>';
        },
        $content
    );
});

// Add lazy-fade class to post thumbnails (featured images)
add_filter('post_thumbnail_html', function ($html) {
    return preg_replace_callback(
        '/<img([^>]*?)src=["\'](.*?)["\']([^>]*)>/i',
        function ($matches) {
            $before = $matches[1];
            $src = $matches[2];
            $after = $matches[3];

            // Check for class attribute
            if (preg_match('/class=["\']([^"\']*)["\']/', $before . $after, $classMatch)) {
                // Append lazy-fade to existing class
                $newClass = trim($classMatch[1] . ' lazy-fade');
                $newAttrs = preg_replace(
                    '/class=["\']([^"\']*)["\']/',
                    'class="' . esc_attr($newClass) . '"',
                    $before . $after
                );
            } else {
                // Add new class attribute
                $newAttrs = trim($before . $after) . ' class="lazy-fade"';
            }

            // Replace src with data-src
            $newAttrs = preg_replace(
                '/\s*src=["\'](.*?)["\']/',
                '',
                $newAttrs
            );

            return '<img data-src="' . esc_attr($src) . '" ' . trim($newAttrs) . '>';
        },
        $html
    );
});

// Add fade-in class to wp-block-group divs
add_filter('render_block', function ($block_content, $block) {
    if ($block['blockName'] === 'core/group') {
        $block_content = preg_replace(
            '/<div class="wp-block-group/',
            '<div class="wp-block-group group-fade-in',
            $block_content,
            1
        );
    }
    return $block_content;
}, 10, 2);
