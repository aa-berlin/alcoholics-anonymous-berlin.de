<?php

require get_template_directory() . '/inc/template-tags.php';

add_action('wp_enqueue_scripts', 'zenzero_aa_enqueue_assets');
add_action('widgets_init', 'zenzero_aa_widgets_init');
add_action('wp_ajax_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0);
add_action('wp_ajax_nopriv_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0);
add_filter('rewrite_rules_array', 'zenzero_aa_filter_rewrite_rules_array');
add_filter('wp_mail_from', 'zenzero_aa_sender_email');
add_filter('wp_mail_from_name', 'zenzero_aa_sender_name');
add_action('after_setup_theme', 'zenzero_aa_register_nav_menus', 0);

function zenzero_aa_enqueue_assets() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'zenzero-aa-main-js',
        get_stylesheet_directory_uri() . '/js/jquery.zenzero-aa.js',
        array(
            'zenzero-custom',
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
            'flo-forms-public',
        ),
        wp_get_theme()->get('Version')
    );
}

function zenzero_aa_widgets_init() {
    register_sidebar(array(
        'name' => 'Header',
        'id' => 'header_teasers',
        'description' => 'Inside header, between page name and menu',
        'before_widget' => '<div class=”header-teasers”>',
        'after_widget' => '</div>',
        'before_title' => '<p>',
        'after_title' => '</p>',
    ));
}

if (function_exists('tsml_custom_types')) {
    tsml_custom_types(array(
        'DF' => 'Dog Friendly',
        'IX' => 'Inter',
        'LGBTQ' => 'LGBTQI+',
        'NOX' => 'No Wheelchair Access',
        'NODF' => 'No Dogs Allowed',
        'TOP' => 'Topic',
    ));
}

// This replaces the builtin meeting-schedule with our own styling.
// This function should be kept up-to-date with tsml_ajax_pdf().
function zenzero_aa_tsml_ajax_pdf() {

    //include the file, which includes TCPDF
    require(TSML_PATH . '/includes/pdf.php');
    require dirname(__FILE__) . '/inc/BerlinPdf.php';

    //create new PDF document
    $pdf = new BerlinPdf(array(
        'margin' => !empty($_GET['margin']) ? floatval($_GET['margin']) : .25,
        'width' => !empty($_GET['width']) ? floatval($_GET['width']) : 4.25,
        'height' => !empty($_GET['height']) ? floatval($_GET['height']) : 11,
    ));

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

function zenzero_aa_sender_email($original_email_address) {
    return $original_email_address;
}

function zenzero_aa_sender_name($original_email_from) {
    return $original_email_from;
}

function zenzero_aa_register_nav_menus() {
    register_nav_menus(array(
        'zenzero_aa_private_menu' => __('Private Pages Navigation', 'zenzero-aa'),
    ));
}
