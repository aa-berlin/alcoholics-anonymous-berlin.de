<?php
/**
 * Zenzero Admin Class.
 *
 * @author  CrestaProject
 * @package Zenzero
 * @since   1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Zenzero_Admin' ) ) :

/**
 * Zenzero_Admin Class.
 */
class Zenzero_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );
		global $zenzero_adminpage;
		$zenzero_adminpage = add_theme_page( esc_html__( 'About', 'zenzero' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'zenzero' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'zenzero-welcome', array( $this, 'welcome_screen' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_admin_scripts() {
		global $zenzero_adminpage;
		$screen = get_current_screen();
		if ( $screen->id != $zenzero_adminpage ) {
			return;
		}
		wp_enqueue_style( 'zenzero-welcome', get_template_directory_uri() . '/inc/admin/welcome.css', array(), '1.0' );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $pagenow;

		wp_enqueue_style( 'zenzero-message', get_template_directory_uri() . '/inc/admin/message.css', array(), '1.0' );

		// Let's bail on theme activation.
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			update_option( 'zenzero_admin_notice_welcome', 1 );

		// No option? Let run the notice wizard again..
		} elseif( ! get_option( 'zenzero_admin_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['zenzero-hide-notice'] ) && isset( $_GET['_zenzero_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key($_GET['_zenzero_notice_nonce'] ), 'zenzero_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'zenzero' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Cheatin&#8217; huh?', 'zenzero' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash($_GET['zenzero-hide-notice'] ));
			update_option( 'zenzero_admin_notice_' . $hide_notice, 1 );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div id="message" class="updated cresta-message">
			<a class="cresta-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'zenzero-hide-notice', 'welcome' ) ), 'zenzero_hide_notices_nonce', '_zenzero_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'zenzero' ); ?></a>
			<p><?php
			/* translators: 1: start option panel link, 2: end option panel link */
			printf( esc_html__( 'Welcome! Thank you for choosing Zenzero! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'zenzero' ), '<a href="' . esc_url( admin_url( 'themes.php?page=zenzero-welcome' ) ) . '">', '</a>' );
			?>
			</p>
			<p class="submit">
				<a class="button-secondary" href="<?php echo esc_url( admin_url( 'themes.php?page=zenzero-welcome' ) ); ?>"><?php esc_html_e( 'Get started with Zenzero', 'zenzero' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="cresta-theme-info">
				<h1>
					<?php esc_html_e('About', 'zenzero'); ?>
					<?php echo esc_html($theme->get( 'Name' )) ." ". esc_html($theme->get( 'Version' )); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo esc_html($theme->display( 'Description' )); ?>
				<p class="cresta-actions">
					<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://crestaproject.com/downloads/zenzero/' ) ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'zenzero' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://crestaproject.com/demo/zenzero/' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Demo', 'zenzero' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://crestaproject.com/demo/zenzero-pro/' ) ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'View PRO version Demo', 'zenzero' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://wordpress.org/support/theme/zenzero/reviews/' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Rate this theme', 'zenzero' ); ?></a>
				</p>
				</div>

				<div class="cresta-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
				</div>
			</div>
		</div>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && isset( $_GET['page'] ) && $_GET['page'] == 'zenzero-welcome' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'zenzero-welcome' ), 'themes.php' ) ) ); ?>">
				<?php echo esc_html($theme->display( 'Name' )); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'zenzero-welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs PRO', 'zenzero' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'zenzero-welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'zenzero' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
		$current_tab = empty( $_GET['tab'] ) ? 'about' : sanitize_title( wp_unslash($_GET['tab']) );

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases">
				<div class="under-the-hood two-col">
					<div class="col">
						<h3><?php esc_html_e( 'Theme Customizer', 'zenzero' ); ?></h3>
						<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'zenzero' ) ?></p>
						<p><a href="<?php echo esc_url(admin_url( 'customize.php' )); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'zenzero' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Got theme support question?', 'zenzero' ); ?></h3>
						<p><?php esc_html_e( 'Please put it in our support forum.', 'zenzero' ) ?></p>
						<p><a target="_blank" href="<?php echo esc_url( 'https://wordpress.org/support/theme/zenzero/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support', 'zenzero' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php esc_html_e( 'Need more features?', 'zenzero' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'zenzero' ) ?></p>
						<p><a target="_blank" href="<?php echo esc_url( 'https://crestaproject.com/downloads/zenzero/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Info about PRO version', 'zenzero' ); ?></a></p>
					</div>

					<div class="col">
						<h3>
							<?php
							esc_html_e( 'Translate', 'zenzero' );
							echo ' ' . esc_html($theme->display( 'Name' ));
							?>
						</h3>
						<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'zenzero' ) ?></p>
						<p>
							<a target="_blank" href="<?php echo esc_url( 'https://translate.wordpress.org/projects/wp-themes/zenzero/' ); ?>" class="button button-secondary">
								<?php
								esc_html_e( 'Translate', 'zenzero' );
								echo ' ' . esc_html($theme->display( 'Name' ));
								?>
							</a>
						</p>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard cresta">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates', 'zenzero' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'zenzero' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'zenzero' ) : esc_html_e( 'Go to Dashboard', 'zenzero' ); ?></a>
			</div>
		</div>
		<?php
	}

		/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'zenzero' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'zenzero_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 * @param  string $content
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}

	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'zenzero' ); ?></p>

			<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e('Features', 'zenzero'); ?></h3></th>
						<th><h3><?php esc_html_e('Zenzero', 'zenzero'); ?></h3></th>
						<th><h3><?php esc_html_e('Zenzero PRO', 'zenzero'); ?></h3></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><h3><?php esc_html_e('Responsive Design', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Change Background', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Unlimited Text Color', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Choose Social Icons', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('RTL Support', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Powerful theme options', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Loading Page', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Font switcher', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Breadcrumb', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Post views counter', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('7 Shortcodes', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('5 Exclusive Widgets', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Related Posts Box', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Information About Author Box', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('6 Post format', 'zenzero'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://crestaproject.com/demo/zenzero-pro/' ) ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'View PRO version demo', 'zenzero' ); ?></a>
							<a href="<?php echo esc_url( apply_filters( 'zenzero_pro_theme_url', 'https://crestaproject.com/downloads/zenzero/' ) ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'More Information', 'zenzero' ); ?></a>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
		<?php
	}
}

endif;

return new Zenzero_Admin();
