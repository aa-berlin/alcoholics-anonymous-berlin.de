<?php

require get_template_directory() . '/inc/template-tags.php';

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
add_action( 'wp_enqueue_scripts', 'zenzero_aa_scripts' );
add_action( 'widgets_init', 'zenzero_aa_widgets_init' );
add_action( 'wp_ajax_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0 );
add_action( 'wp_ajax_nopriv_tsml_pdf', 'zenzero_aa_tsml_ajax_pdf', 0 );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

function zenzero_aa_scripts() {
    wp_enqueue_script( 'zenzero-aa-main',  get_stylesheet_directory_uri() . '/js/jquery.zenzero-aa.js', ['zenzero-custom'] );
}

function zenzero_aa_widgets_init() {
    register_sidebar( [
        'name' => 'Header',
        'id' => 'header_teasers',
        'description' => 'Inside header, between page name and menu',
        'before_widget' => '<div class=”header-teasers”>',
        'after_widget' => '</div>',
        'before_title' => '<p>',
        'after_title' => '</p>',
    ] );
}

if (function_exists('tsml_custom_types')) {
    tsml_custom_types(array(
        'NDA' => 'No Dogs Allowed',
        'IX' => 'Inter',
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
