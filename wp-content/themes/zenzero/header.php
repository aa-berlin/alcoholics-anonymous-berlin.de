<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package zenzero
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php 
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}
?>
<div id="page" class="hfeed site">
	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
		<header id="masthead" class="site-header">
			<div class="site-branding">
				<?php
				if ( is_front_page() && is_home() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
				endif;
				$zenzero_description = get_bloginfo( 'description', 'display' );
				if ( $zenzero_description || is_customize_preview() ) : ?>
					<p class="site-description"><?php echo $zenzero_description; /* // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></p>
				<?php
				endif; ?>
			</div>
			
			<?php $zenzero_theme_options_socialheader = get_theme_mod('zenzero_theme_options_socialheader', '');  
			if ($zenzero_theme_options_socialheader == 1) : ?>
			<?php zenzero_social_button(); ?>
			<?php endif; ?>

			<nav id="site-navigation" class="main-navigation smallPart">
				<button class="menu-toggle" aria-label="<?php esc_attr_e( 'Main Menu', 'zenzero' ); ?>"><?php esc_html_e( 'Main Menu', 'zenzero' ); ?><i class="fa fa-align-justify"></i></button>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #site-navigation -->
		</header><!-- #masthead -->
	<?php endif; ?>
	<div id="content" class="site-content">
