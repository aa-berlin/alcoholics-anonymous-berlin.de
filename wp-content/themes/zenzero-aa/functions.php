<?php

require get_template_directory() . '/inc/template-tags.php';

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
add_action( 'wp_enqueue_scripts', 'zenzero_aa_scripts' );
add_action( 'widgets_init', 'zenzero_aa_widgets_init' );

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
