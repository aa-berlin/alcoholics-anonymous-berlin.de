<?php

/**
 * Plugin Name: AA Berlin Addons
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Contains several optimizations and customizations to both WordPress itself and the 12-step-meeting-list plugin.
 * Text Domain: aa-berlin-addons
 * Domain Path: /languages/
 * Version: 0.0.1
 */

add_action('enqueue_block_editor_assets', 'aa_berlin_enqueue_block_editor_assets');
add_action('wp_enqueue_scripts', 'aa_berlin_wp_enqueue_scripts');

function aa_berlin_enqueue_block_editor_assets() {
    wp_enqueue_style(
        'aa-berlin-blocks-frontend',
        plugins_url('assets/blocks.css', __FILE__),
        array()
    );

    wp_enqueue_script(
        'aa-berlin-blocks-editor',
        plugins_url('assets/blocks.js', __FILE__),
        array(
            'wp-blocks',
            'wp-element',
        )
    );

    wp_enqueue_style(
        'aa-berlin-blocks-editor',
        plugins_url('assets/blocks-editor.css', __FILE__),
        array()
    );
}

function aa_berlin_wp_enqueue_scripts() {
    wp_enqueue_style(
        'aa-berlin-blocks-frontend',
        plugins_url('assets/blocks.css', __FILE__),
        array()
    );

    wp_enqueue_script(
        'aa-berlin-auto-highlight-notices',
        plugins_url('assets/auto-highlight-notices.js', __FILE__),
        array()
    );

    wp_enqueue_style(
        'aa-berlin-auto-highlight-notices',
        plugins_url('assets/auto-highlight-notices.css', __FILE__),
        array()
    );
}
