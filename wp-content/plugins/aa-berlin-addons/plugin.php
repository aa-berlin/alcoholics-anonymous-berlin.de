<?php

/**
 * Plugin Name: AA Berlin Addons
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Contains several optimizations and customizations to both WordPress itself and the 12-step-meeting-list plugin.
 * Text Domain: aa-berlin-addons
 * Domain Path: /languages/
 * Version: 1.9.1
 */

define('AA_BERLIN_ADDONS_VERSION', '1.9.1');

require __DIR__ . '/includes/options.php';
require_once ABSPATH . WPINC . '/class-phpass.php';

add_action('init', 'aa_berlin_addons_init');

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

    if (aa_berlin_addons_options('add_type_f2f') && function_exists('tsml_custom_types')) {
        tsml_custom_types(array(
            'F2F' => aa_berlin_addons_options('label_type_f2f'),
        ));
    }

    add_action('do_meta_boxes', 'aa_berlin_addons_add_metaboxes', 9);
    add_action('save_post', 'aa_berlin_addons_save_metaboxes_postdata');

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

    add_action('save_post', 'aa_berlin_addons_save_post_before_tsml', 9, 3);

    add_action('enqueue_block_editor_assets', 'aa_berlin_enqueue_block_editor_assets');
    add_action('wp_enqueue_scripts', 'aa_berlin_wp_enqueue_scripts');
    add_action('widgets_init', 'aa_berlin_addons_widgets_init');
    add_action('wp_footer', 'aa_berlin_addons_render_common_widgets');
    add_action('wp_footer', 'aa_berlin_addons_render_dynamic_styles');

    add_filter('body_class', 'aa_berlin_addons_body_class');

    add_shortcode('timezone_info', 'aa_berlin_addons_shortcode_timezone_info');
    add_filter('widget_text', 'do_shortcode');

    add_filter('wp_mail_from', 'aa_berlin_addons_wp_mail_from');
    add_filter('wp_mail_from_name', 'aa_berlin_addons_wp_mail_from_name');

    add_filter('the_password_form', 'aa_berlin_addons_the_password_form');
    add_action('login_form_postpass', 'aa_berlin_addons_login_form_postpass');
    add_action('check_passwords', 'aa_berlin_addons_wp_mail_from_name');
    add_filter('post_password_expires', 'aa_berlin_addons_password_expires');
    add_filter('post_password_required', 'aa_berlin_addons_password_required');
}

/**
 * Runs before save handler of tsml.
 * Manipulate post data here to patch tsml behaviour.
 */
function aa_berlin_addons_save_post_before_tsml($post_id, $post, $update = null) {
    // fires for other posts as well
    if (!isset($_POST['post_type']) || ($_POST['post_type'] != 'tsml_meeting')) {
        return;
    }

    // copy of builtin code
    if (empty($_POST['types']) || !is_array($_POST['types'])) {
        $_POST['types'] = array();
    }

    // add your fixes here
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

    $show_chat = aa_berlin_addons_options('show_gso_gb_chat');

    if ($show_chat) {
        wp_enqueue_script(
            'aa-berlin-gso-gb-chat',
            plugins_url('assets/gso-gb-chat.js', __FILE__),
            array(
                'jquery',
            ),
            AA_BERLIN_ADDONS_VERSION
        );
    }
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

function aa_berlin_addons_add_metaboxes() {
    add_meta_box(
            'aaberlinaddonspasswordlessmetabox',
            __('AA B. Addons', 'aa-berlin-addons'),
            'aa_berlin_addons_meetings_post_submit_meta_box',
            'tsml_meeting',
            'side'
    );

    add_meta_box(
            'aaberlinaddonsextrasmetabox',
            __('AA B. Addons', 'aa-berlin-addons'),
            'aa_berlin_addons_posts_post_submit_meta_box',
            ['page', 'post'],
            'side'
    );
}

function aa_berlin_addons_meetings_post_submit_meta_box(WP_Post $post, $args = array()) {
    $is_checked = get_post_meta($post->ID, 'aa_berlin_addons_passwordless', true);

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_passwordless" name="aa_berlin_addons_passwordless" type="checkbox" value="passwordless" <?php checked($is_checked); ?> />
        <label for="aa_berlin_addons_passwordless" class="selectit"><?php echo __('If online meeting, show alternate hint next to online meeting links without password prompt', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php
}

function aa_berlin_addons_posts_post_submit_meta_box(WP_Post $post, $args = array()) {
    $is_checked = get_post_meta($post->ID, 'aa_berlin_addons_allow_global_passwords', true);

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_allow_global_passwords" name="aa_berlin_addons_allow_global_passwords" type="checkbox" value="allow_global_passwords" <?php checked($is_checked); ?> />
        <label for="aa_berlin_addons_allow_global_passwords" class="selectit"><?php echo __('If password protected, also allow for use of global passwords', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php
}

function aa_berlin_addons_save_metaboxes_postdata($post_id, $a=1, $b=2) {

    if (!isset($_POST['post_type'])) {
        return;
    }

    $post_type = $_POST['post_type'];

    if ($post_type == 'tsml_meeting' || $post_type == 'tsml_group') {
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

    if ($post_type == 'post' || $post_type == 'page') {
        $allow_global = '0';

        if (array_key_exists('aa_berlin_addons_allow_global_passwords', $_POST)) {
            $allow_global = '1';
        }

        update_post_meta(
            $post_id,
            'aa_berlin_addons_allow_global_passwords',
            $allow_global
        );
    }
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

function aa_berlin_addons_password_expires($expires) {
    $override = (int) aa_berlin_addons_options('post_password_expires');

    return $override ? time() + $override * DAY_IN_SECONDS : $expires;
}

function aa_berlin_addons_login_form_postpass() {
    if (empty($_POST['post_password'])) {
        return;
    }

    if (empty($_POST['aa_berlin_addons_post_id'])) {
        return;
    }

    if (empty($_POST['aa_berlin_addons_post_id_hash'])) {
        return;
    }

    $id = $_POST['aa_berlin_addons_post_id'];
    $hash = $_POST['aa_berlin_addons_post_id_hash'];

    if ($hash != aa_berlin_addons_get_post_id_hash($id)) {
        return;
    }

    if (!get_post_meta($id, 'aa_berlin_addons_allow_global_passwords', true)) {
        return;
    }

    $input_password = $_POST['post_password'];
    $global_passwords = aa_berlin_addons_get_global_passwords();

    $is_match = false;

    // go through our global passwords and see if one matches, maybe allowing for some typos
    foreach ($global_passwords as $global_password) {
        $distance = levenshtein($global_password, $input_password);
        $password_length = strlen($global_password);
        $required_chars = $password_length;

        if (aa_berlin_addons_options('post_global_passwords_may_be_inaccurate')) {
            $required_chars = ceil($required_chars * 0.8);
        }

        $allowed_distance = $password_length - $required_chars;

        if ($distance <= $allowed_distance) {
            $is_match = true;

            break;
        }
    }

    if (!$is_match) {
        return;
    }

    // user had a good enough match, override his input with the exact global password to then be hashed and put
    // into a cookie by wp-login.php
    $_POST['post_password'] = $global_password;
}

function aa_berlin_addons_get_global_passwords() {
    $global_passwords = aa_berlin_addons_options('post_global_passwords');
    $global_passwords = preg_split('#[\r\n,]+#', $global_passwords);
    $global_passwords = array_map('trim', $global_passwords);
    $global_passwords = array_filter($global_passwords);

    return $global_passwords;
}

function aa_berlin_addons_password_required($required) {
    global $post;

    if (!$required || !$post) {
        return $required;
    }

    if (!get_post_meta($post->ID, 'aa_berlin_addons_allow_global_passwords', true)) {
        return $required;
    }

    $cookie_name = 'wp-postpass_' . COOKIEHASH;

    if (!isset($_COOKIE[$cookie_name])) {
        return $required;
    }

    $global_passwords = aa_berlin_addons_get_global_passwords();

    if (!$global_passwords) {
        return $required;
    }

    $hasher = aa_berlin_addons_get_hasher();

    $hash = wp_unslash($_COOKIE[$cookie_name]);

    if (0 !== strpos($hash, '$P$B')) {
        return true;
    }

    foreach ($global_passwords as $global_password) {
        if ($hasher->CheckPassword($global_password, $hash)) {
            return false;
        }
    }

    return true;
}

function aa_berlin_addons_get_hasher() {
    // copied from post_password_required() – initialization needs to match!
    return new PasswordHash(8, true);
}

function aa_berlin_addons_the_password_form($form_html) {
    global $post;

    if (!$post) {
        return $form_html;
    }

    $id = $post->ID;

    if (!get_post_meta($id, 'aa_berlin_addons_allow_global_passwords', true)) {
        return $form_html;
    }

    $hash = aa_berlin_addons_get_post_id_hash($id);
    $append = "<input name='aa_berlin_addons_post_id_hash' id='aaberlinpostidhash-$id' type='hidden' value='$hash'/>";
    $append .= "<input name='aa_berlin_addons_post_id' id='aaberlinpostid-$id' type='hidden' value='$id'/>";
    $form_html = preg_replace('#<form\b[^>]+>#', '$0 ' . $append, $form_html);

    return $form_html;
}

function aa_berlin_addons_get_post_id_hash($id) {
    return sha1(
        SECURE_AUTH_SALT
        . AUTH_SALT
        . 'aa_berlin_addons_use_global_passwords for post id '
        . $id
    );
}
