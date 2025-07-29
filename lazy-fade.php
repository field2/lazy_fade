<?php
/**
 * Plugin Name: Lazy Fade
 * Plugin URI: https://github.com/YOUR-USERNAME/lazy_fade
 * Description: Lazy-load images and fade-in wp-block-group elements on scroll.
 * Version: 1.0.1
 * Author: Ben Dunkle
 * Author URI: https://bendunkle.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lazy-fade
 * Requires at least: 5.0
 * Tested up to: 6.8.1
 * Requires PHP: 7.0
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

// Add lazy-fade class to images in the_content only
add_filter('the_content', function ($content) {
    // Only apply in main content area
    if (!is_admin() && in_the_loop() && is_main_query()) {
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
    }
    return $content;
});

// Add fade-in class to wp-block-group divs only in main content
add_filter('render_block', function ($block_content, $block) {
    // Only apply to group blocks in main content area
    if ($block['blockName'] === 'core/group' &&
        !is_admin() &&
        in_the_loop() &&
        is_main_query()) {
        $block_content = preg_replace(
            '/<div class="wp-block-group/',
            '<div class="wp-block-group group-fade-in',
            $block_content,
            1
        );
    }
    return $block_content;
}, 10, 2);

// Add fallback script to handle elements that might be missed
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only target elements within main content area
        const mainContent = document.querySelector('main, .content, #content, .site-content, .entry-content');
        if (!mainContent) return;

        setTimeout(function() {
            const hiddenElements = mainContent.querySelectorAll('.group-fade-in[style*="opacity: 0"], .group-fade-in:not(.fade-in-visible)');
            hiddenElements.forEach(function(element) {
                const rect = element.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                if (isVisible) {
                    element.classList.add('fade-in-visible');
                    element.style.opacity = '1';
                }
            });
        }, 1000);
    });
    </script>
    <?php
});
