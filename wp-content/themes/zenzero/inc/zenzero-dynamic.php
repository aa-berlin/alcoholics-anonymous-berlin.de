<?php 
/**
 * zenzero functions and dynamic template
 *
 * @package zenzero
 */

/**
 * Replace more Excerpt
 */
if ( ! function_exists( 'zenzero_new_excerpt_more' ) ) {
	function zenzero_new_excerpt_more($more) {
		if ( is_admin() ) {
			return $more;
		}
		return '&hellip;';
	}
}
add_filter('excerpt_more', 'zenzero_new_excerpt_more');

/**
 * Delete font size style from tag cloud widget
 */
if ( ! function_exists( 'zenzero_fix_tag_cloud' ) ) {
	function zenzero_fix_tag_cloud($tag_string){
	   return preg_replace('/ style=("|\')(.*?)("|\')/','',$tag_string);
	}
}
add_filter('wp_generate_tag_cloud', 'zenzero_fix_tag_cloud',10,1);

/**
 * Social Buttons
 */
if ( ! function_exists( 'zenzero_social_button' ) ) {
	function zenzero_social_button(){
		$hideRss = get_theme_mod('zenzero_theme_options_rss', '1');
		$facebookURL = get_theme_mod('zenzero_theme_options_facebookurl', '#');
		$twitterURL = get_theme_mod('zenzero_theme_options_twitterurl', '#');
		$googleplusURL = get_theme_mod('zenzero_theme_options_googleplusurl', '#');
		$linkedinURL = get_theme_mod('zenzero_theme_options_linkedinurl', '#');
		$instagramURL = get_theme_mod('zenzero_theme_options_instagramurl', '#');
		$youtubeURL = get_theme_mod('zenzero_theme_options_youtubeurl', '#');
		$pinterestURL = get_theme_mod('zenzero_theme_options_pinteresturl', '#');
		$tumblrURL = get_theme_mod('zenzero_theme_options_tumblrurl', '#');
		$vkURL = get_theme_mod('zenzero_theme_options_vkurl', '#');
		$xingURL = get_theme_mod('zenzero_theme_options_xingurl', '');
		$vimeoURL = get_theme_mod('zenzero_theme_options_vimeourl', '');
		$imdbURL = get_theme_mod('zenzero_theme_options_imdburl', '');
		$twitchURL = get_theme_mod('zenzero_theme_options_twitchurl', '');
		$spotifyURL = get_theme_mod('zenzero_theme_options_spotifyurl', '');
		$whatsappURL = get_theme_mod('zenzero_theme_options_whatsappurl', '');
		?>
		<div class="site-social smallPart">
			<?php if (!empty($facebookURL)) : ?>
				<a href="<?php echo esc_url($facebookURL); ?>" title="<?php esc_attr_e( 'Facebook', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-facebook"><span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($twitterURL)) : ?>
				<a href="<?php echo esc_url($twitterURL); ?>" title="<?php esc_attr_e( 'Twitter', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-twitter"><span class="screen-reader-text"><?php esc_html_e( 'Twitter', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($googleplusURL)) : ?>
				<a href="<?php echo esc_url($googleplusURL); ?>" title="<?php esc_attr_e( 'Google Plus', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-google-plus"><span class="screen-reader-text"><?php esc_html_e( 'Google Plus', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($linkedinURL)) : ?>
				<a href="<?php echo esc_url($linkedinURL); ?>" title="<?php esc_attr_e( 'Linkedin', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-linkedin"><span class="screen-reader-text"><?php esc_html_e( 'Linkedin', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($instagramURL)) : ?>
				<a href="<?php echo esc_url($instagramURL); ?>" title="<?php esc_attr_e( 'Instagram', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-instagram"><span class="screen-reader-text"><?php esc_html_e( 'Instagram', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($youtubeURL)) : ?>
				<a href="<?php echo esc_url($youtubeURL); ?>" title="<?php esc_attr_e( 'YouTube', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-youtube"><span class="screen-reader-text"><?php esc_html_e( 'YouTube', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($pinterestURL)) : ?>
				<a href="<?php echo esc_url($pinterestURL); ?>" title="<?php esc_attr_e( 'Pinterest', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-pinterest"><span class="screen-reader-text"><?php esc_html_e( 'Pinterest', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($tumblrURL)) : ?>
				<a href="<?php echo esc_url($tumblrURL); ?>" title="<?php esc_attr_e( 'Tumblr', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-tumblr"><span class="screen-reader-text"><?php esc_html_e( 'Tumblr', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($vkURL)) : ?>
				<a href="<?php echo esc_url($vkURL); ?>" title="<?php esc_attr_e( 'VK', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-vk"><span class="screen-reader-text"><?php esc_html_e( 'VK', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($xingURL)) : ?>
				<a href="<?php echo esc_url($xingURL); ?>" title="<?php esc_attr_e( 'Xing', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-xing"><span class="screen-reader-text"><?php esc_html_e( 'Xing', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($vimeoURL)) : ?>
				<a href="<?php echo esc_url($vimeoURL); ?>" title="<?php esc_attr_e( 'Vimeo', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-vimeo"><span class="screen-reader-text"><?php esc_html_e( 'Vimeo', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($imdbURL)) : ?>
				<a href="<?php echo esc_url($imdbURL); ?>" title="<?php esc_attr_e( 'Imdb', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-imdb"><span class="screen-reader-text"><?php esc_html_e( 'Imdb', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($twitchURL)) : ?>
				<a href="<?php echo esc_url($twitchURL); ?>" title="<?php esc_attr_e( 'Twitch', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-twitch"><span class="screen-reader-text"><?php esc_html_e( 'Twitch', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($spotifyURL)) : ?>
				<a href="<?php echo esc_url($spotifyURL); ?>" title="<?php esc_attr_e( 'Spotify', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-spotify"><span class="screen-reader-text"><?php esc_html_e( 'Spotify', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if (!empty($whatsappURL)) : ?>
				<a href="<?php echo esc_url($whatsappURL); ?>" title="<?php esc_attr_e( 'WhatsApp', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-whatsapp"><span class="screen-reader-text"><?php esc_html_e( 'WhatsApp', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
			<?php if ($hideRss == 1 ) : ?>
				<a href="<?php esc_url(bloginfo( 'rss2_url' )); ?>" title="<?php esc_attr_e( 'RSS', 'zenzero' ); ?>"><i class="fa spaceLeftRight fa-rss"><span class="screen-reader-text"><?php esc_html_e( 'RSS', 'zenzero' ); ?></span></i></a>
			<?php endif; ?>
		</div>
		<?php
	}
}

 /**
 * Register All Colors and Section
 */
function zenzero_color_primary_register( $wp_customize ) {
	$colors = array();
	
	$colors[] = array(
	'slug'=>'text_color_first', 
	'default' => '#919191',
	'label' => __('Box Text Color', 'zenzero')
	);
	
	$colors[] = array(
	'slug'=>'box_color_second', 
	'default' => '#ffffff',
	'label' => __('Box Background Color', 'zenzero')
	);
	
	$colors[] = array(
	'slug'=>'special_color_third', 
	'default' => '#292929',
	'label' => __('Header / Footer / Sidebar Background Color', 'zenzero')
	);
	
	$colors[] = array(
	'slug'=>'special_color_fourth', 
	'default' => '#727272',
	'label' => __('Header / Footer / Sidebar Text Color', 'zenzero')
	);
	
	foreach( $colors as $color ) {
	// SETTINGS
	$wp_customize->add_setting(
		$color['slug'], array(
			'default' => $color['default'],
			'type' => 'option', 
			'sanitize_callback' => 'sanitize_hex_color',
			'capability' => 'edit_theme_options'
		)
	);
	// CONTROLS
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			$color['slug'], 
			array('label' => $color['label'], 
			'section' => 'colors',
			'settings' => $color['slug'])
		)
	);
	}
	
	/*
	Start Zenzero Options
	=====================================================
	*/
	$wp_customize->add_section( 'cresta_zenzero_options', array(
	     'title'    => esc_html__( 'Zenzero Theme Options', 'zenzero' ),
	     'priority' => 50,
	) );
	
	/*
	Social Icons
	=====================================================
	*/
	$socialmedia = array();
	
	$socialmedia[] = array(
	'slug'=>'facebookurl', 
	'default' => '#',
	'label' => __('Facebook URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'twitterurl', 
	'default' => '#',
	'label' => __('Twitter URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'googleplusurl', 
	'default' => '#',
	'label' => __('Google Plus URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'linkedinurl', 
	'default' => '#',
	'label' => __('Linkedin URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'instagramurl', 
	'default' => '#',
	'label' => __('Instagram URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'youtubeurl', 
	'default' => '#',
	'label' => __('YouTube URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'pinteresturl', 
	'default' => '#',
	'label' => __('Pinterest URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'tumblrurl', 
	'default' => '#',
	'label' => __('Tumblr URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'vkurl', 
	'default' => '#',
	'label' => __('VK URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'xingurl', 
	'default' => '',
	'label' => __('Xing URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'vimeourl', 
	'default' => '',
	'label' => __('Vimeo URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'imdburl', 
	'default' => '',
	'label' => __('Imdb URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'twitchurl', 
	'default' => '',
	'label' => __('Twitch URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'spotifyurl', 
	'default' => '',
	'label' => __('Spotify URL', 'zenzero')
	);
	$socialmedia[] = array(
	'slug'=>'whatsappurl', 
	'default' => '',
	'label' => __('WhatsApp URL', 'zenzero')
	);
	
	foreach( $socialmedia as $zenzero_theme_options ) {
		// SETTINGS
		$wp_customize->add_setting(
			'zenzero_theme_options_' . $zenzero_theme_options['slug'], array(
				'default' => $zenzero_theme_options['default'],
				'capability'     => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
				'type'     => 'theme_mod',
			)
		);
		// CONTROLS
		$wp_customize->add_control(
			$zenzero_theme_options['slug'], 
			array('label' => $zenzero_theme_options['label'], 
			'section'    => 'cresta_zenzero_options',
			'settings' =>'zenzero_theme_options_' . $zenzero_theme_options['slug'],
			)
		);
	}
	
	/*
	RSS Button
	=====================================================
	*/
	$wp_customize->add_setting('zenzero_theme_options_rss', array(
        'default'    => '1',
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
		'sanitize_callback' => 'zenzero_sanitize_checkbox'
    ) );
	
	$wp_customize->add_control('zenzero_theme_options_rss', array(
        'label'      => __( 'Show RSS Button', 'zenzero' ),
        'section'    => 'cresta_zenzero_options',
        'settings'   => 'zenzero_theme_options_rss',
        'type'       => 'checkbox',
    ) );
	
	/*
	Search Button
	=====================================================
	*/
	$wp_customize->add_setting('zenzero_theme_options_hidesearch', array(
        'default'    => '1',
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
		'sanitize_callback' => 'zenzero_sanitize_checkbox'
    ) );
	
	$wp_customize->add_control('zenzero_theme_options_hidesearch', array(
        'label'      => __( 'Show Search Button', 'zenzero' ),
        'section'    => 'cresta_zenzero_options',
        'settings'   => 'zenzero_theme_options_hidesearch',
        'type'       => 'checkbox',
    ) );
	
	/*
	Search Button
	=====================================================
	*/
	$wp_customize->add_setting('zenzero_theme_options_socialheader', array(
        'default'    => '',
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
		'sanitize_callback' => 'zenzero_sanitize_checkbox'
    ) );
	
	$wp_customize->add_control('zenzero_theme_options_socialheader', array(
        'label'      => __( 'Show Social Button in the header', 'zenzero' ),
        'section'    => 'cresta_zenzero_options',
        'settings'   => 'zenzero_theme_options_socialheader',
        'type'       => 'checkbox',
    ) );
	
	/*
	Smooth Scroll
	=====================================================
	*/
	$wp_customize->add_setting('zenzero_theme_options_smoothscroll', array(
        'default'    => '1',
        'type'       => 'theme_mod',
        'capability' => 'edit_theme_options',
		'sanitize_callback' => 'zenzero_sanitize_checkbox'
    ) );
	
	$wp_customize->add_control('zenzero_theme_options_smoothscroll', array(
        'label'      => __( 'Enable Smooth Scroll', 'zenzero' ),
        'section'    => 'cresta_zenzero_options',
        'settings'   => 'zenzero_theme_options_smoothscroll',
        'type'       => 'checkbox',
    ) );
	
	/*
	Custom Copyright text
	=====================================================
	*/
	$wp_customize->add_setting('zenzero_theme_options_copyright', array(
		'sanitize_callback' => 'zenzero_sanitize_text',
		'default'    => '&copy; '.date('Y').' '. get_bloginfo('name'),
		'type'       => 'theme_mod',
		'capability' => 'edit_theme_options',
	) );
	$wp_customize->add_control('zenzero_theme_options_copyright', array(
		'label'      => __( 'Copyright Text', 'zenzero' ),
		'description'	=> __( 'Get the PRO version to remove CrestaProject credits', 'zenzero' ),
		'section'    => 'cresta_zenzero_options',
		'settings'   => 'zenzero_theme_options_copyright',
		'type'       => 'text',
	) );
	
	/*
	Upgrade to PRO
	=====================================================
	*/
    class Zenzero_Customize_Upgrade_Control extends WP_Customize_Control {
        public function render_content() {  ?>
        	<p class="zenzero-upgrade-title">
        		<span class="customize-control-title">
					<h3 style="text-align:center;"><div class="dashicons dashicons-megaphone"></div> <?php esc_html_e('Get Zenzero PRO WP Theme for only', 'zenzero'); ?> 19,90&euro;</h3>
        		</span>
        	</p>
			<p style="text-align:center;" class="zenzero-upgrade-button">
				<a style="margin: 10px;" target="_blank" href="https://crestaproject.com/demo/zenzero-pro/" class="button button-secondary">
					<?php esc_html_e('Watch the demo', 'zenzero'); ?>
				</a>
				<a style="margin: 10px;" target="_blank" href="https://crestaproject.com/downloads/zenzero/" class="button button-secondary">
					<?php esc_html_e('Get Zenzero PRO Theme', 'zenzero'); ?>
				</a>
			</p>
			<ul>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Advanced Theme Options', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Logo Upload', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Loading Page', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Font Switcher', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Unlimited Colors and Skin', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Post views counter', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Breadcrumb', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Post format', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( '7 Shortcodes', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( '5 Exclusive Widgets', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Related Posts Box', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'Information About Author Box', 'zenzero' ); ?></b></li>
				<li><div class="dashicons dashicons-yes" style="color: #1fa67a;"></div><b><?php esc_html_e( 'And much more...', 'zenzero' ); ?></b></li>
			<ul><?php
        }
    }
	
	$wp_customize->add_section( 'cresta_upgrade_pro', array(
	     'title'    => esc_html__( 'More features? Upgrade to PRO', 'zenzero' ),
	     'priority' => 999,
	));
	
	$wp_customize->add_setting('zenzero_section_upgrade_pro', array(
		'default' => '',
		'type' => 'option',
		'sanitize_callback' => 'esc_attr'
	));
	
	$wp_customize->add_control(new Zenzero_Customize_Upgrade_Control($wp_customize, 'zenzero_section_upgrade_pro', array(
		'section' => 'cresta_upgrade_pro',
		'settings' => 'zenzero_section_upgrade_pro',
	)));
}
add_action( 'customize_register', 'zenzero_color_primary_register' );

function zenzero_sanitize_checkbox( $input ) {
	if ( $input == 1 ) {
		return 1;
	} else {
		return '';
	}
}

function zenzero_sanitize_text( $input ) {
	return wp_kses($input, zenzero_allowed_html());
}

if( ! function_exists('zenzero_allowed_html')){
	function zenzero_allowed_html() {
		$allowed_tags = array(
			'a' => array(
				'class' => array(),
				'id'    => array(),
				'href'  => array(),
				'rel'   => array(),
				'title' => array(),
				'target' => array(),
			),
			'abbr' => array(
				'title' => array(),
			),
			'b' => array(),
			'blockquote' => array(
				'cite'  => array(),
			),
			'cite' => array(
				'title' => array(),
			),
			'code' => array(),
			'del' => array(
				'datetime' => array(),
				'title' => array(),
			),
			'dd' => array(),
			'div' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'dl' => array(),
			'dt' => array(),
			'em' => array(),
			'h1' => array(
				'class' => array(),
			),
			'h2' => array(
				'class' => array(),
			),
			'h3' => array(
				'class' => array(),
			),
			'h4' => array(
				'class' => array(),
			),
			'h5' => array(
				'class' => array(),
			),
			'h6' => array(
				'class' => array(),
			),
			'i' => array(
				'class' => array(),
			),
			'br' => array(),
			'img' => array(
				'alt'    => array(),
				'class'  => array(),
				'height' => array(),
				'src'    => array(),
				'width'  => array(),
			),
			'li' => array(
				'class' => array(),
			),
			'ol' => array(
				'class' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'q' => array(
				'cite' => array(),
				'title' => array(),
			),
			'span' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'strike' => array(),
			'strong' => array(),
			'ul' => array(
				'class' => array(),
			),
			'iframe' => array(
				'width' => array(),
				'height' => array(),
				'src' => array(),
				'frameborder' => array(),
				'allow' => array(),
				'style' => array(),
				'name' => array(),
				'id' => array(),
				'class' => array(),
			),
		);
		return $allowed_tags;
	}
}

/**
 * Add Custom CSS to Header 
 */
function zenzero_custom_css_styles() { 
	$text_color_first = get_option('text_color_first');
	$box_color_second = get_option('box_color_second');
	$special_color_third = get_option('special_color_third');
	$special_box_color_fourth = get_option('special_color_fourth');
?>

<style id="zenzero-custom-css">
	<?php if (!empty($text_color_first) && $text_color_first != '#919191' ) : ?>
	body,
	button,
	input,
	select,
	textarea {
		color: <?php echo esc_html($text_color_first); ?>;
	}
	<?php endif; ?>
	
	<?php if (!empty($box_color_second) && $box_color_second != '#ffffff' ) : ?>
	<?php list($r, $g, $b) = sscanf($box_color_second, '#%02x%02x%02x'); ?>
	#search-full {
		background: rgba(<?php echo esc_html($r).', '.esc_html($g).', '.esc_html($b); ?>, 0.9);
	}
	
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.main-navigation ul:not(.sub-menu) > li > a:hover::before,
	.main-navigation ul:not(.sub-menu) > li > a:focus::before,
	.main-navigation ul li:hover > a, 
	.main-navigation li.current-menu-item > a, 
	.main-navigation li.current-menu-parent > a, 
	.main-navigation li.current-page-ancestor > a, 
	.main-navigation .current_page_item > a, 
	.main-navigation ul > li:hover .indicator,
	.main-navigation li.current-menu-parent .indicator, 
	.main-navigation li.current-menu-item .indicator,
	.paging-navigation .nav-links a, 
	.comment-navigation a,
	#toTop:hover, 
	.showSide:hover, 
	.showSearch:hover,
	.site-social i.fa-rss:hover,
	.page-links span a,
	.entry-footer a,
	.widget-title	{
		color: <?php echo esc_html($box_color_second); ?>;
	}
	.site-branding a, 
	.site-branding a:hover,
	.menu-toggle, 
	.menu-toggle:hover {
		color: <?php echo esc_html($box_color_second); ?> !important;
	}
	.paging-navigation .nav-links a:hover,
	.comment-navigation a:hover,
	#page	{
		background: <?php echo esc_html($box_color_second); ?>;
	}
	.main-navigation ul:not(.sub-menu) > li > a:hover::before,
	.main-navigation ul:not(.sub-menu) > li > a:focus::before {
		text-shadow: 8px 0 <?php echo esc_html($box_color_second); ?>, -8px 0px <?php echo esc_html($box_color_second); ?>;
	}
	<?php endif; ?>
	
	<?php if (!empty($special_color_third) && $special_color_third != '#292929' ) : ?>
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.main-navigation ul ul,
	.paging-navigation .nav-links a,
	.comment-navigation a,
	.site-header,
	.site-footer,
	#secondary,
	.showSide, 
	.showSearch,
	#toTop,
	.page-links span a,
	.entry-footer a	{
		background: <?php echo esc_html($special_color_third); ?>;
	}
	button:hover,
	input[type="button"]:hover,
	input[type="reset"]:hover,
	input[type="submit"]:hover,
	button:focus,
	input[type="button"]:focus,
	input[type="reset"]:focus,
	input[type="submit"]:focus,
	button:active,
	input[type="button"]:active,
	input[type="reset"]:active,
	input[type="submit"]:active,
	a,
	a:hover,
	a:focus,
	a:active,
	.paging-navigation .nav-links a:hover, 
	.comment-navigation a:hover,
	.entry-meta,
	.entry-footer a:hover,
	.sticky .entry-header:before {
		color: <?php echo esc_html($special_color_third); ?>;
	}
	.tagcloud a {	
		color: <?php echo esc_html($special_color_third); ?> !important;
	}
	button:hover,
	input[type="button"]:hover,
	input[type="reset"]:hover,
	input[type="submit"]:hover,
	.paging-navigation .nav-links a:hover, 
	.comment-navigation a:hover,
	.entry-footer a:hover {
		border: 1px solid <?php echo esc_html($special_color_third); ?>;
	}
	<?php endif; ?>
	
	<?php if (!empty($special_box_color_fourth) && $special_box_color_fourth != '#727272' ) : ?>
	<?php list($r, $g, $b) = sscanf($special_box_color_fourth, '#%02x%02x%02x'); ?>
	.site-header a, 
	.site-footer a, 
	#secondary a, 
	.site-footer a:hover,
	.main-navigation ul li .indicator,
	.site-header, .site-footer,
	#secondary,
	.showSide, 
	.showSearch,
	#toTop {
		color: <?php echo esc_html($special_box_color_fourth); ?>;
	}
	.tagcloud a:hover {
		color: <?php echo esc_html($special_box_color_fourth); ?> !important;
	}
	.tagcloud a {
		background: <?php echo esc_html($special_box_color_fourth); ?>;
	}
	#wp-calendar tbody td#today,
	.tagcloud a:hover {
		border: 1px solid <?php echo esc_html($special_box_color_fourth); ?>;
	}
	.nano > .nano-pane > .nano-slider {
		background-color: <?php echo esc_html($special_box_color_fourth); ?>;
	}
	.nano > .nano-pane {
		background: rgba(<?php echo esc_html($r).', '.esc_html($g).', '.esc_html($b); ?>, 0.15);
	}
	.nano > .nano-pane > .nano-slider {
		background: rgba(<?php echo esc_html($r).', '.esc_html($g).', '.esc_html($b); ?>, 0.3);
	}
	@media screen and (max-width: 1025px) {
		.main-navigation ul li .indicator {
			color: <?php echo esc_html($special_box_color_fourth); ?>;
		}
	}
	<?php endif; ?>
	
</style>
    <?php
}
add_action('wp_head', 'zenzero_custom_css_styles');
