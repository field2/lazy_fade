<?php
/**
 * Plugin Name: Lazy Fade Images
 * Description: Lazy-load images below the fold and fade them in on scroll.
 * Version: 1.0
 * Author: Ben Dunkle
 */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('lazy-fade-images', plugin_dir_url(__FILE__) . 'lazy-fade-images.js', [], null, true);
    wp_enqueue_style('lazy-fade-images-style', plugin_dir_url(__FILE__) . 'lazy-fade-images.css');
});

add_filter('the_content', function ($content) {
    // Replace <img> with lazy version
    return preg_replace_callback(
        '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i',
        function ($matches) {
            $attrs = $matches[1] . $matches[3];
            $src = $matches[2];

            // Add class and move src to data-src
            return '<img class="lazy-fade" data-src="' . esc_attr($src) . '" ' . $attrs . '>';
        },
        $content
    );
});
add_filter('post_thumbnail_html', function ($html) {
    return preg_replace_callback(
        '/<img(.*?)src=["\'](.*?)["\'](.*?)>/i',
        function ($matches) {
            $attrs = $matches[1] . $matches[3];
            $src = $matches[2];

            // Add class and move src to data-src
            return '<img class="lazy-fade" data-src="' . esc_attr($src) . '" ' . $attrs . '>';
        },
        $html
    );
});
