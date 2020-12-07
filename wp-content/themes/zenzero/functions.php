<?php
/**
 * zenzero functions and definitions
 *
 * @package zenzero
 */

if ( ! function_exists( 'zenzero_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function zenzero_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on zenzero, use a find and replace
	 * to change 'zenzero' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'zenzero', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	
	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'zenzero-normal-post' , 980, 9999);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'zenzero' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'zenzero_custom_background_args', array(
		'default-color' => 'f0f0f0',
		'default-image' => '',
	) ) );
	
	/* Support for wide images on Gutenberg */
	add_theme_support( 'align-wide' );
	
	// Adds support for editor font sizes.
	add_theme_support( 'editor-font-sizes', array(
		array(
			'name'      => __( 'Small', 'zenzero' ),
			'shortName' => __( 'S', 'zenzero' ),
			'size'      => 14,
			'slug'      => 'small'
		),
		array(
			'name'      => __( 'Regular', 'zenzero' ),
			'shortName' => __( 'M', 'zenzero' ),
			'size'      => 16,
			'slug'      => 'regular'
		),
		array(
			'name'      => __( 'Large', 'zenzero' ),
			'shortName' => __( 'L', 'zenzero' ),
			'size'      => 18,
			'slug'      => 'large'
		),
		array(
			'name'      => __( 'Larger', 'zenzero' ),
			'shortName' => __( 'XL', 'zenzero' ),
			'size'      => 22,
			'slug'      => 'larger'
		)
	) );
}
endif; // zenzero_setup
add_action( 'after_setup_theme', 'zenzero_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function zenzero_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'zenzero_content_width', 770 );
}
add_action( 'after_setup_theme', 'zenzero_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function zenzero_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'zenzero' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'zenzero_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function zenzero_scripts() {
	wp_enqueue_style( 'zenzero-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version') );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() .'/css/font-awesome.min.css', array(), '4.7.0');
	$query_args = array(
		'family' => 'Open+Sans:300,400,700',
		'display' => 'swap'
	);
	wp_enqueue_style( 'zenzero-googlefonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );

	wp_enqueue_script( 'zenzero-navigation', get_template_directory_uri() . '/js/navigation.min.js', array(), '20151215', true );
	wp_enqueue_script( 'zenzero-custom', get_template_directory_uri() . '/js/jquery.zenzero.min.js', array('jquery'), wp_get_theme()->get('Version'), true );
	if (get_theme_mod('zenzero_theme_options_smoothscroll', '1')) {
		wp_enqueue_script( 'zenzero-smoothScroll', get_template_directory_uri() . '/js/SmoothScroll.min.js', array('jquery'), '1.4.9', true );
	}
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		wp_enqueue_script( 'zenzero-nanoScroll', get_template_directory_uri() . '/js/jquery.nanoscroller.min.js', array('jquery'), '0.8.7', true );
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zenzero_scripts' );

/**
* Fix skip link focus in IE11.
*
* This does not enqueue the script because it is tiny and because it is only for IE11,
* thus it does not warrant having an entire dedicated blocking script being loaded.
*
* @link https://git.io/vWdr2
*/
function zenzero_skip_link_focus_fix() {
    // The unminified version of this code is in /js/skip-link-focus-fix.js
	?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'zenzero_skip_link_focus_fix' );

function zenzero_gutenberg_scripts() {
	wp_enqueue_style( 'zenzero-gutenberg-css', get_theme_file_uri( '/css/gutenberg-editor-style.css' ), array(), wp_get_theme()->get('Version') );
}
add_action( 'enqueue_block_editor_assets', 'zenzero_gutenberg_scripts' );

/**
 * Register all Elementor locations
 */
function zenzero_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'zenzero_register_elementor_locations' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load Zenzero Dynamic.
 */
require get_template_directory() . '/inc/zenzero-dynamic.php';

/* Calling in the admin area for the Welcome Page */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/zenzero-admin-page.php';
}

/**
 * Load PRO Button in the customizer
 */
require get_template_directory() . '/inc/pro-button/class-customize.php';
