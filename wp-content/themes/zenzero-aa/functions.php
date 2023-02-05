<?php

require __DIR__ . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-tags.php';

const ZENZERO_AA_SIDEBAR_DELIMITER = '<!--zenzero_aa_sidebar-->';

add_action('wp_enqueue_scripts', 'zenzero_aa_enqueue_assets', 10);
add_action('wp_enqueue_scripts', 'zenzero_aa_patch_parent_styles', 11);

add_action('widgets_init', 'zenzero_aa_widgets_init');
add_action('wp_ajax_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0);
add_action('wp_ajax_nopriv_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0);
add_filter('rewrite_rules_array', 'zenzero_aa_filter_rewrite_rules_array');
add_filter('wp_mail_from', 'zenzero_aa_sender_email');
add_filter('wp_mail_from_name', 'zenzero_aa_sender_name');
add_action('after_setup_theme', 'zenzero_aa_register_nav_menus', 0);
add_action('get_sidebar', 'zenzero_aa_get_sidebar', -1);
add_filter('gettext', 'zenzero_aa_filter_gettext');

add_filter('theme_mod_zenzero_theme_options_copyright', 'zenzero_aa_theme_mod_copyright', -1);

function zenzero_aa_enqueue_assets() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'feather-icons',
        'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js'
    );

    wp_enqueue_script(
        'zenzero-aa-main-js',
        get_stylesheet_directory_uri() . '/js/jquery.zenzero-aa.js',
        array(
            'zenzero-custom',
            'feather-icons',
        ),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'zenzero-aa-forms-style',
        get_stylesheet_directory_uri() . '/css/flo-forms.css',
        array(
            'zenzero-style',
            'flo-forms-public',
        ),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'zenzero-aa-meeting-list-style',
        get_stylesheet_directory_uri() . '/css/12-step-meeting-list.css',
        array(
            'zenzero-style',
            'tsml_public',
        ),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'zenzero-aa-meeting-list-upcoming-widget-style',
        get_stylesheet_directory_uri() . '/css/12-step-meeting-list-upcoming-widget.css',
        array(
            'zenzero-style',
        ),
        wp_get_theme()->get('Version')
    );
}

function zenzero_aa_patch_parent_styles() {
    $wp_styles = wp_styles();

    /** @var _WP_Dependency $rule */
    $rule = $wp_styles->registered['zenzero-googlefonts'] ?? null;

    if (!$rule) {
        return;
    }

    // also load semi-bold, as open sans bold is kinda ugly
    $rule->src = preg_replace('#([@:]\d+(?:[,;]\d+)*);700#', '$1;600', $rule->src);
}

function zenzero_aa_widgets_init() {
    register_sidebar(array(
        'name' => 'Header',
        'id' => 'header_teasers',
        'description' => 'Inside header, between page name and menu',
        'before_widget' => '<div class="header-teasers">',
        'after_widget' => '</div>',
        'before_title' => '<p>',
        'after_title' => '</p>',
    ));
}

if (function_exists('tsml_custom_types')) {
    tsml_custom_types([
        // exists in other programme configurations already
        'DE' => 'German',
        // exists in other programme configurations already
        'DF' => 'Dog Friendly',
        // exists in other programme configurations already
        'TOP' => 'Topic',
        // specific to us
        'IX' => 'Inter',
        // renaming only
        'LGBTQ' => 'LGBTQI+',
        // antonym of official X
        'NOX' => 'No Wheelchair Access',
        // antonym of official DF
        'NODF' => 'No Dogs Allowed',
    ]);
}

if (function_exists('tsml_custom_addresses')) {
    tsml_custom_addresses([
        'Neukölln, Berlin, Germany' => [
            'formatted_address' => 'Neukölln, Berlin, Germany',
            'city' => 'Berlin',
            'latitude' => 52.4407709,
            'longitude' => 13.4445071,
            'approximate' => 'no',
        ],
    ]);
}

// This replaces the builtin meeting-schedule with our own styling.
// This function should be kept up-to-date with tsml_ajax_pdf().
function zenzero_aa_tsml_ajax_pdf() {

    //include the file, which includes TCPDF
    require(TSML_PATH . '/includes/pdf.php');
    require dirname(__FILE__) . '/inc/BerlinPdf.php';

    //create new PDF document
    $pdf = new BerlinPdf([
        'margin' => !empty($_GET['margin']) ? floatval($_GET['margin']) : .25,
        'width' => !empty($_GET['width']) ? floatval($_GET['width']) : 4.25,
        'height' => !empty($_GET['height']) ? floatval($_GET['height']) : 11,
    ]);

    //send to browser
    if (!headers_sent()) {
        $pdf->Output('aa-berlin-meeting-schedule.pdf', 'I');
    }

    exit;
}

function zenzero_aa_filter_rewrite_rules_array($rules) {
    foreach ($rules as $rule => $rewrite) {
//        example deleting author permalinks:
//        if ( preg_match( '#/author/#', $rule ) ) {
//            unset( $rules[$rule] );
//        }
    }

    return $rules;
}

function zenzero_aa_get_sidebar($sidebar = null) {
    global $zenzero_aa_sidebar_render_count;

    if ($sidebar === null || $sidebar === 'sidebar-1') {
        $zenzero_aa_sidebar_render_count++;
    }
}

function zenzero_aa_sender_email($original_email_address) {
    return $original_email_address;
}

function zenzero_aa_sender_name($original_email_from) {
    return $original_email_from;
}

function zenzero_aa_register_nav_menus() {
    register_nav_menus(array(
        'zenzero_aa_private_menu' => __('Private Pages Navigation', 'zenzero-aa'),
        'zenzero_aa_service_menu' => __('Service Navigation', 'zenzero-aa'),
    ));
}

function zenzero_aa_filter_gettext($translated, $original = null, $domain = null) {
    switch ($translated) {
        case 'Request a change to this listing':
            return 'Update this Meeting';
        case 'Use this form to submit a change to the meeting information above.':
            return 'Use this form to let us know about updates to or problems with the meeting details on this page.';
        default:
            return $translated;
    }
}

function zenzero_aa_theme_mod_copyright($text) {
    if (!is_string($text)) {
        return $text;
    }

    return preg_replace('#\d+#', '2007 - ' . date('Y'), $text, 1);
}
