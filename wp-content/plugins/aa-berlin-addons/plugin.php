<?php

/**
 * Plugin Name: AA Berlin Addons
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Contains several optimizations and customizations to both WordPress itself and the 12-step-meeting-list plugin.
 * Text Domain: aa-berlin-addons
 * Domain Path: /languages/
 * Version: 1.17.3
 */

define('AA_BERLIN_ADDONS_VERSION', '1.17.3');

define(
    'AA_BERLIN_ADDONS_SMTP_CONSTANTS',
    'SMTP_USER,SMTP_PASS,SMTP_HOST,SMTP_FROM,SMTP_NAME,SMTP_PORT,SMTP_SECURE,SMTP_AUTH,SMTP_DEBUG'
);

require __DIR__ . '/includes/options.php';
require_once ABSPATH . WPINC . '/class-phpass.php';

$aa_berlin_addons_last_widget_options = [];

register_activation_hook(__FILE__, 'aa_berlin_addons_activate');
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
    global $tsml_programs, $tsml_program, $tsml_conference_providers;

    $existing_type_flags = array();

    if (!empty($tsml_programs[$tsml_program]['flags'])) {
        $existing_type_flags = $tsml_programs[$tsml_program]['flags'];
    }

    if (aa_berlin_addons_options('add_type_online') && function_exists('tsml_custom_types')) {
        tsml_custom_types(array(
            'ONL' => aa_berlin_addons_options('label_type_online'),
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

    // ensure that we can always enter our own domain as meeting url
    $host = parse_url(get_option('siteurl'))['host'];
    $providers = [
        $host,
        preg_replace('#^www\.#', '', $host),
    ];
    $providers = array_unique($providers);
    foreach ($providers as $provider) {
        $tsml_conference_providers[$provider] = __('the details from this page', 'aa-berlin-addons');
    }

    add_action('save_post', 'aa_berlin_addons_save_post_before_tsml', 9, 3);

    add_action('wp_dashboard_setup', 'aa_berlin_addons_wp_dashboard_setup');
    add_action('enqueue_block_editor_assets', 'aa_berlin_enqueue_block_editor_assets');

    add_action('wp_enqueue_scripts', 'aa_berlin_wp_enqueue_scripts');
    add_action('widgets_init', 'aa_berlin_addons_widgets_init');
    add_action('wp_footer', 'aa_berlin_addons_render_common_widgets');
    add_action('wp_footer', 'aa_berlin_addons_render_dynamic_styles');

    add_filter('body_class', 'aa_berlin_addons_body_class');

    add_shortcode('timezone_info', 'aa_berlin_addons_shortcode_timezone_info');
    add_filter('widget_text', 'do_shortcode');
    add_filter('widget_title', 'aa_berlin_addons_widget_title');
    add_filter('widget_posts_args', 'aa_berlin_addons_widget_posts_args');

    add_action('phpmailer_init', 'aa_berlin_addons_send_smtp_email');
    add_filter('wp_mail_from', 'aa_berlin_addons_wp_mail_from');
    add_filter('wp_mail_from_name', 'aa_berlin_addons_wp_mail_from_name');

    add_filter('the_password_form', 'aa_berlin_addons_the_password_form');
    add_action('login_form_postpass', 'aa_berlin_addons_login_form_postpass');
    add_action('check_passwords', 'aa_berlin_addons_wp_mail_from_name');
    add_filter('post_password_expires', 'aa_berlin_addons_password_expires');
    add_filter('post_password_required', 'aa_berlin_addons_password_required');

    add_filter('site_url', 'aa_berlin_addons_authenticate_cron_url');
    add_action('crontrol/tab-header', 'aa_berlin_addons_print_cron_url', 20);

    add_action('wp_router_generate_routes', 'aa_berlin_addons_generate_routes');

    add_action('admin_init', 'aa_berlin_addons_admin_init');
    add_action('admin_menu', 'aa_berlin_addons_admin_menu');
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

    // if we want an online meeting but do not have a url, we can override that url with our own, so the user at least
    // returns to the meeting details with the relevant info
    if (!empty($_POST['aa_berlin_addons_force_online'])) {
        $url = get_permalink($post_id);
        $_POST['conference_url'] = $url;
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

    if (aa_berlin_addons_options('enable_sharing')) {
        wp_enqueue_script(
            'qrcode-js',
            'https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.js'
        );
    }

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
        wp_enqueue_style(
            'aa-berlin-gso-gb-chat',
            plugins_url('assets/gso-gb-chat.css', __FILE__),
            array(),
            AA_BERLIN_ADDONS_VERSION
        );

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

    if (aa_berlin_addons_options('enable_sharing')):
        ?>
        <template id="aa-berlin-addons-share-template" class="aa-berlin-addons-template">
            <dl class="aa-berlin-addons-share panel" data-copy-label="<?php echo esc_attr(__('Copy to Clipboard', 'aa-berlin-addons')) ?>">
                <dt class="aa-berlin-addons-share-header"><?php echo __('Share this Meeting', 'aa-berlin-addons') ?></dt>
            </dl>
        </template>
        <?php
    endif;

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
    $force_online = get_post_meta($post->ID, 'aa_berlin_addons_force_online', true);
    $is_passwordless = get_post_meta($post->ID, 'aa_berlin_addons_passwordless', true);

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_force_online" name="aa_berlin_addons_force_online" type="checkbox" value="force" <?php checked($force_online); ?> />
        <label for="aa_berlin_addons_force_online" class="selectit"><?php echo __('Save this meeting as an online meeting, even if I do not have connection details to enter. This OVERWRITES the online meeting url with the link to this meeting.', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_passwordless" name="aa_berlin_addons_passwordless" type="checkbox" value="passwordless" <?php checked($is_passwordless); ?> />
        <label for="aa_berlin_addons_passwordless" class="selectit"><?php echo __('If online meeting, show alternate hint next to online meeting links without password prompt', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php
}

function aa_berlin_addons_posts_post_submit_meta_box(WP_Post $post, $args = array()) {
    $is_checked = get_post_meta($post->ID, 'aa_berlin_addons_allow_global_passwords', true);
    $password_html = get_post_meta($post->ID, 'aa_berlin_addons_password_page_html', true);

    ?>
    <div class="misc-pub-section">
        <input id="aa_berlin_addons_allow_global_passwords" name="aa_berlin_addons_allow_global_passwords" type="checkbox" value="allow_global_passwords" <?php checked($is_checked); ?> />
        <label for="aa_berlin_addons_allow_global_passwords" class="selectit"><?php echo __('If password protected, also allow for use of global passwords', 'aa-berlin-addons'); ?></label>
        <br />
    </div>
    <?php

    ?>
    <div class="misc-pub-section">
        <label for="aa_berlin_addons_password_page_html" class="selectit"><?php echo __('Show this text above the password form, if password protected', 'aa-berlin-addons'); ?></label>
        <textarea placeholder="<?php echo __('Help text for password form', 'aa-berlin-addons') ?>" style="width:100%; min-height: 150px;" id="aa_berlin_addons_password_page_html" name="aa_berlin_addons_password_page_html"><?php echo esc_html($password_html) ?></textarea>
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
        $force_online_meeting = '0';

        if (array_key_exists('aa_berlin_addons_passwordless', $_POST)) {
            $is_passwordless = '1';
        }

        if (array_key_exists('aa_berlin_addons_force_online', $_POST)) {
            $force_online_meeting = '1';
        }

        update_post_meta(
            $post_id,
            'aa_berlin_addons_passwordless',
            $is_passwordless
        );

        update_post_meta(
            $post_id,
            'aa_berlin_addons_force_online',
            $force_online_meeting
        );
    }

    if ($post_type == 'post' || $post_type == 'page') {
        $allow_global = '0';
        $password_html = '';

        if (array_key_exists('aa_berlin_addons_allow_global_passwords', $_POST)) {
            $allow_global = '1';
        }

        if (array_key_exists('aa_berlin_addons_password_page_html', $_POST)) {
            $password_html = $_POST['aa_berlin_addons_password_page_html'];
            $password_html = trim(preg_replace('#<script.*script\s*?>#s', '', $password_html));
        }

        update_post_meta(
            $post_id,
            'aa_berlin_addons_allow_global_passwords',
            $allow_global
        );

        update_post_meta(
            $post_id,
            'aa_berlin_addons_password_page_html',
            $password_html
        );
    }
}

function aa_berlin_addons_wp_mail_from($original_from_address) {
    if (!aa_berlin_addons_options('change_default_email_address')) {
        return $original_from_address;
    }

    if (aa_berlin_addons_has_smtp_settings()) {
        return SMTP_FROM;
    }

    return aa_berlin_addons_options('default_from_email_address');
}

function aa_berlin_addons_has_smtp_settings() {
    $constants = explode(',', AA_BERLIN_ADDONS_SMTP_CONSTANTS);

    foreach ($constants as $constant) {
        if (!defined($constant)) {
            return false;
        }
    }

    return true;
}

function aa_berlin_addons_wp_mail_from_name($original_from_name) {
    if (!aa_berlin_addons_options('change_default_email_address')) {
        return $original_from_name;
    }

    if (aa_berlin_addons_has_smtp_settings()) {
        return SMTP_NAME;
    }

    return aa_berlin_addons_options('default_from_email_name');
}

/**
 * @param \PHPMailer\PHPMailer\PHPMailer $phpMailer
 */
function aa_berlin_addons_send_smtp_email($phpMailer) {
    if (!$phpMailer instanceof \PHPMailer\PHPMailer\PHPMailer) {
        return;
    }

    if (!aa_berlin_addons_has_smtp_settings()) {
        return;
    }

    $phpMailer->isSMTP();
    $phpMailer->Host = SMTP_HOST;
    $phpMailer->SMTPAuth = SMTP_AUTH;
    $phpMailer->Port = SMTP_PORT;
    $phpMailer->Username = SMTP_USER;
    $phpMailer->Password = SMTP_PASS;
    $phpMailer->SMTPSecure = SMTP_SECURE;
    $phpMailer->From = SMTP_FROM;
    $phpMailer->FromName = SMTP_NAME;
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
    $global_password = null;

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

    return array_filter($global_passwords);
}

function aa_berlin_addons_password_required($required) {
    global $post;

    if (aa_berlin_addons_options('no_pw_if_logged_in') && is_user_logged_in()) {
        return false;
    }

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

    $password_content = get_post_meta($id, 'aa_berlin_addons_password_page_html', true);

    if ($password_content) {
        $password_content = '<div class="aa-berlin-addons-password-intro">' . $password_content . '</div>';
    }

    $hash = aa_berlin_addons_get_post_id_hash($id);
    $append = "<input name='aa_berlin_addons_post_id_hash' id='aaberlinpostidhash-$id' type='hidden' value='$hash'/>";
    $append .= "<input name='aa_berlin_addons_post_id' id='aaberlinpostid-$id' type='hidden' value='$id'/>";

    return $password_content . preg_replace('#<form\b[^>]+>#', '$0 ' . $append, $form_html);
}

function aa_berlin_addons_get_post_id_hash($id) {
    return sha1(
        SECURE_AUTH_SALT
        . AUTH_SALT
        . 'aa_berlin_addons_use_global_passwords for post id '
        . $id
    );
}

function aa_berlin_addons_widget_title($title) {
    global $aa_berlin_addons_last_widget_options;

    $options = [];

    $title = preg_replace_callback('#\[aa-berlin-filter-([\w_-]+)=(.*?)]#', function ($match) use (&$options) {
        $options[$match[1]] = $match[2];

        return '';
    }, $title);

    $aa_berlin_addons_last_widget_options = $options;

    return $title;
}

function aa_berlin_addons_widget_posts_args($options) {
    global $aa_berlin_addons_last_widget_options;

    return array_merge($aa_berlin_addons_last_widget_options, $options);
}

function aa_berlin_addons_wp_dashboard_setup() {
    wp_add_dashboard_widget('aa_berlin_addons_phpinfo_dashboard_widget', 'phpinfo()', 'aa_berlin_addons_phpinfo_dashboard_widget');
}

function aa_berlin_addons_phpinfo_dashboard_widget() {
    $isAllowed = current_user_can('manage_options');

    ob_start();
    if ($isAllowed) {
        phpinfo();
    } else {
        echo 'You must be an Administrator to view this info.';
    }
    $infoHtml = ob_get_clean();

    $infoHtml = preg_replace_callback('#<style.*?</style>#s', function ($styles) {
        return preg_replace_callback('#.*{#', function ($line) {
            $selectors = preg_split('#\s*,\s*#', $line[0]);

            return '.aa-berlin-addons-phpinfo ' . implode(', .aa-berlin-addons-phpinfo ', $selectors);
        }, $styles[0]);
    }, $infoHtml);

    ?><div class="aa-berlin-addons-phpinfo-widget" style="overflow: hidden; height: 400px; max-width: 100%; position: relative;">
        <button class="aa-berlin-addons-toggle-phpinfo button button-primary" onclick="this.parentNode.getElementsByClassName('aa-berlin-addons-phpinfo')[0].requestFullscreen();">
            Show All
        </button>

        <div class="aa-berlin-addons-phpinfo" style="height: 100%; width: 100%; position: absolute; top: 40px; left: 0; overflow: scroll; background: #fff; font-size: 16px;">
            <?php echo $infoHtml ?>
        </div>
    </div><?php
}

function aa_berlin_addons_to_data_uri($file) {
    static $cached = [];

    if (isset($cached[$file])) {
        return $cached[$file];
    }

    if (!file_exists($file)) {
        return $cached[$file] = false;
    }

    $content = file_get_contents($file);

    if (!$content) {
        return $cached[$file] = false;
    }

    $content = base64_encode($content);
    $type = mime_content_type($file);

    return $cached[$file] = "data:$type;base64,$content";
}

function aa_berlin_addons_authenticate_cron_url($url) {
    if (preg_match('#cron\.php$#', $url)) {
        return $url . '?' . WP_CRON_AUTH_PARAM;
    }

    return $url;
}

function aa_berlin_addons_print_cron_url($tab = null) {
    if (!defined('WP_CRON_AUTH_PARAM')) {
        return;
    }

    $link = site_url() . '/wp-cron.php?' . WP_CRON_AUTH_PARAM;

    ?>
    <div class="notice notice-info aa-berlin-addons-cron-info">
        <p>
            Use the following link to setup a cron job via http or trigger a cron job manually:
            <a href="<?php echo esc_attr($link) ?>"><?php echo esc_html($link) ?></a>
        </p>
    </div>
    <?php
}

function aa_berlin_addons_activate() {
    $plugin_tests = [
        'WP_Router_load' => 'WP Router',
    ];

    foreach ($plugin_tests as $fn => $plugin) {
        if (!function_exists($fn)) {
            deactivate_plugins(plugin_basename(__FILE__ ));

            wp_die(
            // translators: %s is the readable plugin name
                sprintf(__('Please install and activate %s before activating AA Berlin Addons.', 'aa-berlin-addons'), $plugin),
                'Plugin dependency check',
                array('back_link' => true)
            );
        }
    }
}

function aa_berlin_addons_generate_routes(WP_Router $router) {
    $router->add_route(
        'aa-berlin-addons-short-url',
        [
            'path' => '^([mlpn])/(\d+)',
            'query_vars' => [
                'aa_berlin_addons_post_type' => 1,
                'aa_berlin_addons_post_id' => 2,
            ],
            'access_callback' => true,
            'template' => false,
            'page_arguments' => ['aa_berlin_addons_post_type', 'aa_berlin_addons_post_id'],
            'page_callback' => 'aa_berlin_addons_route_short_url',
        ]
    );
}

function aa_berlin_addons_route_short_url($post_type = null, $post_id = null) {
    global $wpdb;

    $post_id = (int)$post_id;
    $url = '/';
    $prefix = '/';

    switch ($post_type) {
        case 'm':
            $post_type = 'tsml_meeting';
            $prefix = '/meetings/';
            break;
        case 'l':
            $post_type = 'tsml_location';
            $prefix = '/locations/';
            break;
        case 'p':
            $post_type = 'page';
            break;
        case 'n':
            $post_type = 'post';
            break;
        default:
            $post_type = null;
            break;
    }

    if ($post_type && $post_id) {
        $slug = $wpdb->get_var(
                "SELECT post_name FROM wp_posts WHERE ID = $post_id AND post_status = 'publish' AND post_type = '$post_type'",
                0
        );
        $url = $prefix . $slug;
    }

    header('Location: ' . $url, true, 302);

    exit;
}

function aa_berlin_addons_admin_init() {
    ob_start();
}

function aa_berlin_addons_admin_menu() {
    if (!aa_berlin_addons_options('activate_adminer')) {
        return;
    }

    add_management_page( 'Administer Database (Adminer)', 'Administer Database (Adminer)', 'manage_options', 'aa-berlin-addons-adminer', 'aa_berlin_addons_adminer' );
    // TODO: fix, editor behaves weirdly
    // add_management_page( 'Edit Data in Database (Adminer Editor)', 'Edit Database (Adminer Editor)', 'manage_options', 'aa-berlin-addons-adminer-editor', 'aa_berlin_addons_adminer' );
}

function aa_berlin_addons_adminer() {
    if (!current_user_can('manage_options')) {
        echo 'You must be an administrator to view this content!';
    }

    $adminerVersion = '4.8.1';
    // from the menu item name extract either "adminer" or "editor" to be used as the php-file of adminer to include
    $adminerMode = preg_replace('#^.+-(\w+)$#', '$1', $GLOBALS['plugin_page']);
    $adminerRoot = __DIR__ . '/includes/adminer_v' . $adminerVersion . '_';
    $adminerDir = glob($adminerRoot . '*')[0] ?? null;

    $rawRepoUrl = 'https://raw.githubusercontent.com/vrana/adminer/v' . $adminerVersion;
    $releasesUrl = 'https://github.com/vrana/adminer/releases/download/v' . $adminerVersion;

    $pematonRawRepoUrl = 'https://raw.githubusercontent.com/pematon/adminer-plugins/v1.7.1';

    $filesToFetch = [
        'adminer.php' => "$releasesUrl/adminer-$adminerVersion-en.php",
        'editor.php' => "$releasesUrl/editor-$adminerVersion-en.php",
        'adminer.css' => 'https://raw.githubusercontent.com/pepa-linha/Adminer-Design-Dark/master/adminer.css',
        'plugins/plugin.php' => "$rawRepoUrl/plugins/plugin.php",
        'plugins/AdminerJsonPreview.php' => "$pematonRawRepoUrl/AdminerJsonPreview.php",
        'plugins/AdminerSimpleMenu.php' => "$pematonRawRepoUrl/AdminerSimpleMenu.php",
        'plugins/AdminerCollations.php' => "$pematonRawRepoUrl/AdminerCollations.php",
    ];

    if (!$adminerDir) {
        $randomSuffix = str_replace('=', rand(0, 9), base64_encode(random_bytes(20)));
        $adminerDir = $adminerRoot . $randomSuffix;

        if (!mkdir($adminerDir, 0755)) {
            die('Could not create adminer base dir!');
        }

        $codeHeader = <<<'EOFCODE'
<?php
// Prepended by aa-berlin-addons WordPress plugin (begin)
// This copy of Adminer has been refactored automatically to make it work in WordPress' runtime.
// The Adminer license agreement remains otherwise unchanged, we believe :)
if (!defined('AA_BERLIN_ADDONS_ADMINER_DIR')) {
    // do not allow for direct access
    die('Access denied.');
}
// Prepended by aa-berlin-addons WordPress plugin (end)
?>
EOFCODE;

        foreach ($filesToFetch as $file => $url) {
            $content = file_get_contents($url);
            $subDir = dirname($file);
            if (!$subDir || $subDir == '.') {
                $targetDir = $adminerDir;
            } else {
                $targetDir = $adminerDir . '/' . $subDir;
            }
            $file = basename($file);
            if (!$content) {
                die("Could not fetch $file!");
            }
            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                die("Could not create adminer sub directory $subDir!");
            }
            if (preg_match('#\.php$#', $file)) {
                // rename some functions conflicting with wp
                $content = preg_replace('#(js_escape|get_temp_dir)#', 'adminer_$1', $content);
                // adminer uses exit a lot to exit early; we need to rewrite its output though, so wrap the call to exit in our own function
                $content = preg_replace('#exit\s*(?:\(\s*(\d*)\s*\))?\s*;#', 'aa_berlin_addons_adminer_exit($1);', $content);
                $content = $codeHeader . $content;
            }
            $targetFile = "$targetDir/$file";
            if (!file_put_contents($targetFile, $content)) {
                die("Could not write $file!");
            }
        }
    }

    @error_reporting(E_ERROR);
    @ini_set('display_errors', 0);

    // buffer was started by aa_berlin_addons_admin_init()
    ob_end_clean();

    // variable number of output buffers is being used by adminer, so init a few to brute force capture them later
    ob_start();
    ob_start();
    ob_start();

    chdir($adminerDir);

    $file = $_GET['file'] ?? null;

    if ($file == 'adminer.css') {
        header('Content-Type: text/css');
        echo file_get_contents("$adminerDir/adminer.css");
        exit;
    }

    define('AA_BERLIN_ADDONS_ADMINER_DIR', $adminerDir);

    function adminer_object() {
        return aa_berlin_addons_adminer_object();
    }

    require "$adminerDir/$adminerMode.php";

    // call exit handler in case it wasnt called by adminer
    aa_berlin_addons_adminer_exit();
}

function aa_berlin_addons_adminer_object() {
    $adminerDir = AA_BERLIN_ADDONS_ADMINER_DIR;

    require "$adminerDir/plugins/plugin.php";
    require "$adminerDir/plugins/AdminerJsonPreview.php";
    require "$adminerDir/plugins/AdminerSimpleMenu.php";
    require "$adminerDir/plugins/AdminerCollations.php";

    class AdminerWordpressConnection {
        public function serverName($server = null) {
            return 'WordPress DB';
        }
        public function database() {
            return DB_NAME;
        }
        public function databases() {
            return [DB_NAME];
        }
        public function credentials() {
            return [DB_HOST, DB_USER, DB_PASSWORD];
        }
        public function loginForm() {
            $parentImpl = new Adminer();

            ?><div class="patched-login-form"><?php
                $parentImpl->loginForm();
            ?></div>
            <style>
                .patched-login-form table {
                    display: none;
                }
            </style>
            <?php

            return false;
        }
        public function login($login, $password) {
            set_password(DRIVER, DB_HOST, DB_USER, DB_PASSWORD);
            return true;
        }
    }

    return new AdminerPlugin([
            new AdminerWordpressConnection(),
        new AdminerCollations([
            'utf8mb4_general_ci',
            'utf8mb4_unicode_ci',
        ]),
        new AdminerJsonPreview(),
        new AdminerSimpleMenu(),
    ]);
}

function aa_berlin_addons_adminer_exit($exitCode = 0) {
    // these buffers were started by aa_berlin_addons_adminer() to defo catch anything rendered by adminer
    $html = ob_get_clean();
    $html = ob_get_clean() . $html;
    $html = ob_get_clean() . $html;

    $page = $_GET['page'] ?? null;
    $page = preg_replace('#[^a-zA-Z0-9_-]#', '', $page);

    $baseLink = $GLOBALS['pagenow'] ?? null;

    $html = preg_replace("#$baseLink\?[^\"'`\s]+#", '$0&page=' . $page, $html);

    // insert the wordpress page setting into each form
    $driverField = '';
    if (!strpos($html, 'name="auth[driver]"')) {
        // as well as the auth[driver] field, as it sometimes is referenced by js but not rendered
        // $driverField = '<input type=text style="display:none" name="auth[driver]" onchange="console.log(`authdriverchange`);" value=mysql>';
    }
    $html = preg_replace('#<form[^>{}]*?>#s', "$0$driverField<input type=hidden name=page value='$page'>", $html);

    // replace physical link to adminer.css with one that passes through here
    $html = preg_replace('#adminer\.css\??#', "$baseLink?page=$page&file=adminer.css&", $html);

    // replace inline-event handlers with script tags to appease csp
    $html = preg_replace_callback('#<(?:input|textarea|form|select|body)[^>{}]*?>#s', function ($match) {
        if (!preg_match_all('#\s((on\w+)=([\'"])([^><]+)\3)#s', $match[0], $allHandlers, PREG_SET_ORDER)) {
            return $match[0];
        }

        $patched = preg_replace('#\s(on\w+=)#s', 'data-disabled-$1', $match[0]);

        $assignments = [];
        foreach ($allHandlers as $handler) {
            list($match, $attribute, $name, $quote, $code) = $handler;
            $assignments[] = "tag.$name=function(){{$code}};";
        }
        $assignments = implode('', $assignments);

        return $patched . "<script>(function(){let scripts=document.getElementsByTagName('script');const tag=scripts[scripts.length-1].previousSibling;$assignments})();</script>";
    }, $html);

    // add CSP nonces to scripts where still missing
    $html = preg_replace_callback('#(<script[^>{}]*?)>#s', function ($match) {
        if (!strpos($match[0], 'nonce=')) {
            return $match[1] . ' nonce="' . get_nonce() . '">';
        }

        return $match[0];
    }, $html);

    foreach (headers_list() as $header) {
        $header = preg_replace("#$baseLink\?[^\"'`\s]+#", '$0&page=' . $page, $header);
        header($header);
    }

    echo $html;

    exit($exitCode);
}
