<?php

/**
 * Plugin Name: AA Berlin Addons
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Contains several optimizations and customizations to both WordPress itself and the 12-step-meeting-list plugin.
 * Text Domain: aa-berlin-addons
 * Domain Path: /languages/
 * Version: 0.9.0
 */

define('AA_BERLIN_ADDONS_VERSION', '0.9.0');

require __DIR__ . '/includes/options.php';

add_action('enqueue_block_editor_assets', 'aa_berlin_enqueue_block_editor_assets');
add_action('wp_enqueue_scripts', 'aa_berlin_wp_enqueue_scripts');
add_action('init', 'aa_berlin_addons_init');
add_action('widgets_init', 'aa_berlin_addons_widgets_init');
add_action('wp_footer', 'aa_berlin_addons_render_common_widgets');
add_action('wp_footer', 'aa_berlin_addons_render_dynamic_styles');
add_filter('body_class', 'aa_berlin_addons_body_class');

function aa_berlin_addons_options($key = null) {
    $options = get_option('aa_berlin_addons_options', array());

    if (!$key) {
        return $options;
    }

    if (isset($options[$key])) {
        return $options[$key];
    }

    return null;
}

function aa_berlin_addons_init() {
    if (aa_berlin_addons_options('add_type_online') && function_exists('tsml_custom_types')) {
        tsml_custom_types(array(
            'ONLINE' => aa_berlin_addons_options('label_type_online'),
        ));
    }
}

function aa_berlin_enqueue_block_editor_assets() {
    wp_enqueue_style(
        'aa-berlin-blocks-frontend',
        plugins_url('assets/blocks.css', __FILE__),
        array(),
        AA_BERLIN_ADDONS_VERSION
    );

    wp_enqueue_script(
        'aa-berlin-blocks-editor',
        plugins_url('assets/blocks-editor.js', __FILE__),
        array(
            'wp-blocks',
            'wp-element',
        ),
        AA_BERLIN_ADDONS_VERSION
    );

    wp_enqueue_style(
        'aa-berlin-blocks-editor',
        plugins_url('assets/blocks-editor.css', __FILE__),
        array(),
        AA_BERLIN_ADDONS_VERSION
    );
}

function aa_berlin_wp_enqueue_scripts() {
    wp_enqueue_style(
        'aa-berlin-blocks-frontend',
        plugins_url('assets/blocks.css', __FILE__),
        array(),
        AA_BERLIN_ADDONS_VERSION
    );

    wp_enqueue_script(
        'aa-berlin-auto-augment-page',
        plugins_url('assets/auto-augment-page.js', __FILE__),
        array(
            'jquery',
            'wp-i18n',
        ),
        AA_BERLIN_ADDONS_VERSION
    );

    // todo: implement translations (line otherwise fails with Zlib error -2 deflating data; missing files? endless loops?)
    // wp_set_script_translations('aa-berlin-auto-augment-page', 'aa-berlin-addons');

    wp_localize_script(
        'aa-berlin-auto-augment-page',
        'aa_berlin_addons_options',
        aa_berlin_addons_options()
    );

    wp_enqueue_style(
        'aa-berlin-auto-augment-page',
        plugins_url('assets/auto-augment-page.css', __FILE__),
        array(),
        AA_BERLIN_ADDONS_VERSION
    );
}

function aa_berlin_addons_widgets_init() {
    register_sidebar(array(
        'id' => 'aa_berlin_addons_hint_for_augmented_links',
        'name' => __('Hint for Augmented Links', 'aa-berlin-addons'),
        'description' => __('This displays next to automatically transformed links. E.g. in meeting details.', 'aa-berlin-addons'),
        'before_widget' => '<div class="aa-berlin-addons-hint-for-augmented-links-widget">',
        'after_widget' => '</div>',
        'before_title' => '<p class="aa-berlin-addons-widget-headline">',
        'after_title' => '</p>',
    ));
}

function aa_berlin_addons_body_class($classes) {
    if (aa_berlin_addons_options('disable_map_if_tc')) {
        $classes[] = 'aa-berlin-addons-disable-map-if-tc';
    }

    if (aa_berlin_addons_options('disable_map_if_online')) {
        $classes[] = 'aa-berlin-addons-disable-map-if-online';
    }

    return $classes;
}

/**
 * @see aa_berlin_addons_widgets_init()
 */
function aa_berlin_addons_render_common_widgets() {
    $hint = aa_berlin_addons_options('stream_link_hint');
    $type = aa_berlin_addons_options('stream_link_hint_type');

    $domains = aa_berlin_addons_options('stream_domains_pattern');
    $domains = preg_split('#\s*,\s*#', $domains);

    ?>
    <template id="aa-berlin-addons-hint-for-augmented-links" class="aa-berlin-addons-template">
        <?php
        if (is_active_sidebar('aa_berlin_addons_hint_for_augmented_links')):
            dynamic_sidebar('aa_berlin_addons_hint_for_augmented_links');
        endif;
        ?>

        <?php if ($type): ?>
            <?php foreach ($domains as $domain): ?>
                <div class="aa-berlin-addons-online-meeting-hint">
                    <div data-if-link-domain-is="<?php echo esc_attr($domain); ?>" class="aa-berlin-addons-auto-highlight-notice type-<?php echo $type; ?>">
                        <?php echo $hint; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </template>
    <?php
}

function aa_berlin_addons_render_dynamic_styles() {
    $textOnline = aa_berlin_addons_options('disable_map_text_online');
    $textOnline = str_replace('"', '', $textOnline);

    $textTc = aa_berlin_addons_options('disable_map_text_tc');
    $textTc = str_replace('"', '', $textTc);

    ?>
    <style type="text/css" id="aa-berlin-addons-dynamic-styles">
        <?php if (aa_berlin_addons_options('disable_map_if_tc') && $textTc): ?>
            .aa-berlin-addons-disable-map-if-tc.tsml-type-tc #tsml #meeting #map > div::after {
                content: "<?php echo $textTc ?>";
            }
        <?php endif; ?>

        <?php if (aa_berlin_addons_options('disable_map_if_online') && $textOnline): ?>
            .aa-berlin-addons-disable-map-if-online.tsml-type-online #tsml #meeting #map > div::after {
                content: "<?php echo $textOnline ?>";
            }
        <?php endif; ?>
    </style>
    <?php
}
