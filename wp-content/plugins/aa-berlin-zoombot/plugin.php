<?php

/**
 * Plugin Name: AA Berlin Zoombot
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Allows for integration with Zoom online meetings providing e.g. coin ceremony features.
 * Text Domain: aa-berlin-zoombot
 * Domain Path: /languages/
 * Version: 0.1.0
 */

define('AA_BERLIN_ZOOMBOT_VERSION', '0.1.0');

register_activation_hook(__FILE__, 'aa_berlin_zoombot_activate');
add_action('init', 'aa_berlin_zoombot_init');

function aa_berlin_zoombot_activate() {
    if (!function_exists('aa_berlin_addons_init')) {
        deactivate_plugins(plugin_basename(__FILE__ ));
        wp_die(
            __('Please install and activate AA Berlin Addons before activating AA Berlin Zoombot.', 'aa-berlin-zoombot'),
            'Plugin dependency check',
            array('back_link' => true)
        );
    }
}

function aa_berlin_zoombot_init() {
    require __DIR__ . '/includes/options.php';
}
