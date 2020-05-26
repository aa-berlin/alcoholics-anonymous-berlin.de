<?php

/**
 * Plugin Name: AA Berlin Addons
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Contains several optimizations and customizations to both WordPress itself and the 12-step-meeting-list plugin.
 * Text Domain: aa-berlin-addons
 * Domain Path: /languages/
 * Version: 1.6.0
 */

define('AA_BERLIN_ADDONS_VERSION', '1.6.0');

require __DIR__ . '/includes/options.php';

add_action('enqueue_block_editor_assets', 'aa_berlin_enqueue_block_editor_assets');
add_action('wp_enqueue_scripts', 'aa_berlin_wp_enqueue_scripts');
add_action('init', 'aa_berlin_addons_init');
add_action('widgets_init', 'aa_berlin_addons_widgets_init');
add_action('wp_footer', 'aa_berlin_addons_render_common_widgets');
add_action('wp_footer', 'aa_berlin_addons_render_dynamic_styles');
add_filter('body_class', 'aa_berlin_addons_body_class');
add_shortcode('timezone_info', 'aa_berlin_addons_shortcode_timezone_info');
add_filter('widget_text', 'do_shortcode');
add_filter('wp_mail_from', 'aa_berlin_addons_wp_mail_from');
add_filter('wp_mail_from_name', 'aa_berlin_addons_wp_mail_from_name');

function aa_berlin_addons_options($key = null) {
    static $options = null;

    if ($options === null) {
        $options = get_option('aa_berlin_addons_options', array());
    }

    // use these to initialize dates client side in relation to meeting times
    // to circumvent wrongly configured time zones of client and daylight savings time
    $options['server_time'] = date('c');
    $options['server_time_plus_one_week'] = date('c', strtotime('+1 week'));

    if (!$key) {
        return $options;
    }

    if (isset($options[$key])) {
        return $options[$key];
    }

    return null;
}

function aa_berlin_addons_init() {
    global $tsml_conference_providers;
    global $tsml_programs, $tsml_program;

    $existing_type_flags = array();

    if (!empty($tsml_programs[$tsml_program]['flags'])) {
        $existing_type_flags = $tsml_programs[$tsml_program]['flags'];
    }

    if (aa_berlin_addons_options('add_type_online') && function_exists('tsml_custom_types')) {
        tsml_custom_types(array(
            'ONL' => aa_berlin_addons_options('label_type_online'),
        ));
    }

    add_action('do_meta_boxes', 'aa_berlin_addons_add_passwordless_metabox', 9);
    add_action('save_post', 'aa_berlin_addons_save_passwordless_postdata');

    if (aa_berlin_addons_options('custom_type_flags_add') && function_exists('tsml_custom_flags')) {
        $custom_type_flags = aa_berlin_addons_options('custom_type_flags_add');
        $custom_type_flags = preg_split('#\s*,\s*#', $custom_type_flags);
        $custom_type_flags = array_merge($custom_type_flags, $existing_type_flags);
        $custom_type_flags = array_unique($custom_type_flags);

        $existing_type_flags = $custom_type_flags;
        tsml_custom_flags($custom_type_flags);
    }

    if (aa_berlin_addons_options('custom_type_flags_remove') && function_exists('tsml_custom_flags')) {
        $custom_type_flags = aa_berlin_addons_options('custom_type_flags_remove');
        $custom_type_flags = preg_split('#\s*,\s*#', $custom_type_flags);
        $custom_type_flags = array_diff($existing_type_flags, $custom_type_flags);

        tsml_custom_flags($custom_type_flags);
    }

    if (aa_berlin_addons_options('allow_type_online_without_link')) {
        // monkey patch the conference_url handling, so that the ONL type does not get removed for empty links
        $tsml_conference_providers = array();
        add_action('save_post', 'aa_berlin_addons_save_post_before_tsml', 9, 3);
    }
}

/**
 * Patches the post so that the the type ONL does not get removed for an empty conference_url.
 */
function aa_berlin_addons_save_post_before_tsml($post_id, $post, $update = null) {
    // scrounged from original impl in tsml_save_post()
    if (!isset($_POST['post_type']) || ($_POST['post_type'] != 'tsml_meeting')) {
        return;
    }
    if (empty($_POST['types']) || !is_array($_POST['types'])) {
        $_POST['types'] = array();
    }

    if (in_array('ONL', $_POST['types'], true) && empty($_POST['conference_url'])) {
        $_POST['conference_url'] = ' ';
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
    global $meeting;

    if (aa_berlin_addons_options('disable_map_if_tc')) {
        $classes[] = 'aa-berlin-addons-disable-map-if-tc';
    }

    if (aa_berlin_addons_options('disable_map_if_online')) {
        $classes[] = 'aa-berlin-addons-disable-map-if-online';
    }

    if (aa_berlin_addons_options('muted_address_in_listing_if_tc_and_onl')) {
        $classes[] = 'aa-berlin-addons-muted-address-if-tc-and-onl';
    }

    return $classes;
}

/**
 * @see aa_berlin_addons_widgets_init()
 */
function aa_berlin_addons_render_common_widgets() {
    $hint = aa_berlin_addons_options('stream_link_hint');
    $hint = do_shortcode($hint);
    $type = aa_berlin_addons_options('stream_link_hint_type');

    if (!$type) {
        $hint = '';
    }

    $current_meeting_detail_is_passwordless = false;
    $current_post = get_post();
    if ($current_post && $current_post->post_type == 'tsml_meeting') {
        $meeting = tsml_get_meeting();
        $current_meeting_detail_is_passwordless = property_exists($meeting, 'aa_berlin_addons_passwordless') && $meeting->aa_berlin_addons_passwordless;
    }

    if ($current_meeting_detail_is_passwordless) {
        $passwordlessHint = aa_berlin_addons_options('passwordless_stream_link_hint');
        $passwordlessHint = do_shortcode($passwordlessHint);
        $passwordlessType = aa_berlin_addons_options('passwordless_stream_link_hint_type');

        if ($passwordlessType == 'neither') {
            $hint = '';
        } elseif ($passwordlessType) {
            $hint = $passwordlessHint;
            $type = $passwordlessType;
        }
    }

    $domains = aa_berlin_addons_options('stream_domains_pattern');
    $domains = preg_split('#\s*,\s*#', $domains);

    ?>
    <template id="aa-berlin-addons-hint-for-augmented-links" class="aa-berlin-addons-template">
        <?php
        if (is_active_sidebar('aa_berlin_addons_hint_for_augmented_links')):
            dynamic_sidebar('aa_berlin_addons_hint_for_augmented_links');
        endif;
        ?>

        <?php if ($hint): ?>
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
            .aa-berlin-addons-disable-map-if-online.tsml-type-onl #tsml #meeting #map > div::after {
                content: "<?php echo $textOnline ?>";
            }
        <?php endif; ?>
    </style>
    <?php
}

function aa_berlin_addons_shortcode_timezone_info() {
    $timezoneString = get_option('timezone_string');
    $timezoneOffset = get_option('gmt_offset');
    $timezoneSign = $timezoneOffset > 0 ? '+' : '-';

    return '<span class="aa-berlin-addons-shortcode-timezone">' . $timezoneString . ' (UTC ' . $timezoneSign . $timezoneOffset . 'h)</span>';
}

function aa_berlin_addons_add_passwordless_metabox() {
    add_meta_box('passwordlessmetabox', __('Password-less', 'aa-berlin-addons'), 'aa_berlin_addons_extended_post_submit_meta_box', 'tsml_meeting', 'side');
}

function aa_berlin_addons_extended_post_submit_meta_box(WP_Post $post, $args = array()) {
    $is_checked = get_post_meta($post->ID, 'aa_berlin_addons_passwordless', true);

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_passwordless" name="aa_berlin_addons_passwordless" type="checkbox" value="passwordless" <?php checked($is_checked); ?> />
        <label for="aa_berlin_addons_passwordless" class="selectit"><?php echo __('If online meeting, show alternate hint next to online meeting links without password prompt', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php
}

function aa_berlin_addons_save_passwordless_postdata($post_id, $a=1, $b=2) {

    if (!isset($_POST['post_type'])) {
        return;
    }

    $post_type = $_POST['post_type'];

    if ($post_type != 'tsml_meeting' && $post_type != 'tsml_group') {
        return;
    }

    $is_passwordless = '0';

    if (array_key_exists('aa_berlin_addons_passwordless', $_POST)) {
        $is_passwordless = '1';
    }

    update_post_meta(
        $post_id,
        'aa_berlin_addons_passwordless',
        $is_passwordless
    );
}

function aa_berlin_addons_wp_mail_from($original_from_address) {
    if (!aa_berlin_addons_options('change_default_email_address')) {
        return $original_from_address;
    }

    return aa_berlin_addons_options('default_from_email_address');
}

function aa_berlin_addons_wp_mail_from_name($original_from_name) {
    if (!aa_berlin_addons_options('change_default_email_address')) {
        return $original_from_name;
    }

    return aa_berlin_addons_options('default_from_email_name');
}
