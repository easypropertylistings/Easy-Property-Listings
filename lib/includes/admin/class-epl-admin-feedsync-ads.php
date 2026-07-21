<?php
/**
 * Contextual FeedSync advertisements for WP All Import installations.
 *
 * @package     EPL
 * @subpackage  Admin
 * @since       3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display FeedSync migration advertisements when WP All Import is installed.
 *
 * Only one creative is displayed per screen:
 * - A on EPL settings and extension screens.
 * - C on WP All Import screens.
 * - B throughout the remainder of the WordPress admin.
 *
 * Dismissal is stored per user and per creative.
 *
 * @since 3.6.0
 */
class EPL_Admin_Feedsync_Ads {

	/**
	 * User meta key used for dismissed advertisements.
	 *
	 * @var string
	 */
	const DISMISSED_META_KEY = 'epl_feedsync_dismissed_ads';

	/**
	 * FeedSync destination.
	 *
	 * @var string
	 */
	const FEEDSYNC_URL = 'https://feedsync.com.au/';

	/**
	 * Whether a supported WP All Import plugin is installed.
	 *
	 * @var bool|null
	 */
	private static $wp_all_import_installed = null;

	/**
	 * Installed WP All Import edition label.
	 *
	 * @var string|null
	 */
	private static $wp_all_import_label = null;

	/**
	 * Register hooks.
	 *
	 * @since 3.6.0
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_ad' ), 20 );
		add_action( 'admin_post_epl_dismiss_feedsync_ad', array( __CLASS__, 'dismiss_ad' ) );
	}

	/**
	 * Check the installed plugin list for WP All Import or WP All Import Pro.
	 *
	 * This intentionally checks installation rather than activation: the migration
	 * message remains relevant when an import plugin has been temporarily disabled.
	 *
	 * @return bool
	 * @since 3.6.0
	 */
	public static function is_wp_all_import_installed() {
		if ( null !== self::$wp_all_import_installed ) {
			return self::$wp_all_import_installed;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins      = get_plugins();
		$plugin_files = array_keys( $plugins );
		$pro_files    = array(
			'wp-all-import-pro/wp-all-import-pro.php',
			'wp-all-import-pro/wp-all-import.php',
		);
		$free_files   = array(
			'wp-all-import/wp-all-import.php',
			'wp-all-import/plugin.php',
		);

		foreach ( $pro_files as $plugin_file ) {
			if ( in_array( $plugin_file, $plugin_files, true ) ) {
				self::$wp_all_import_installed = true;
				self::$wp_all_import_label     = __( 'WP All Import Pro', 'easy-property-listings' );
				return true;
			}
		}

		foreach ( $free_files as $plugin_file ) {
			if ( in_array( $plugin_file, $plugin_files, true ) ) {
				self::$wp_all_import_installed = true;
				self::$wp_all_import_label     = __( 'WP All Import', 'easy-property-listings' );
				return true;
			}
		}

		// Accommodate renamed plugin directories while still requiring the real plugin metadata.
		foreach ( $plugins as $plugin ) {
			$name = isset( $plugin['Name'] ) ? $plugin['Name'] : '';
			if ( false !== stripos( $name, 'WP All Import Pro' ) ) {
				self::$wp_all_import_installed = true;
				self::$wp_all_import_label     = __( 'WP All Import Pro', 'easy-property-listings' );
				return true;
			}
			if ( 'WP All Import' === trim( $name ) ) {
				self::$wp_all_import_installed = true;
				self::$wp_all_import_label     = __( 'WP All Import', 'easy-property-listings' );
				return true;
			}
		}

		self::$wp_all_import_installed = false;
		return false;
	}

	/**
	 * Enqueue the advertisement stylesheet only on targeted installations.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	public static function enqueue_styles() {
		if ( ! current_user_can( 'edit_posts' ) || ! self::is_wp_all_import_installed() ) {
			return;
		}

		wp_enqueue_style(
			'epl-feedsync-admin-ads',
			EPL_PLUGIN_URL . 'lib/assets/css/feedsync-admin-ads.css',
			array(),
			EPL_PROPERTY_VER
		);
	}

	/**
	 * Render the appropriate advertisement for the current screen.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	public static function render_ad() {
		if ( ! current_user_can( 'edit_posts' ) || ! self::is_wp_all_import_installed() ) {
			return;
		}

		$creative = self::get_current_creative();
		if ( self::is_dismissed( $creative ) ) {
			return;
		}

		switch ( $creative ) {
			case 'a':
				self::render_creative_a();
				break;
			case 'c':
				self::render_creative_c();
				break;
			default:
				self::render_creative_b();
				break;
		}
	}

	/**
	 * Determine which creative belongs on the current screen.
	 *
	 * @return string
	 * @since 3.6.0
	 */
	private static function get_current_creative() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $page, array( 'epl-settings', 'epl-extensions', 'epl-addons' ), true ) ) {
			return 'a';
		}

		if ( 0 === strpos( $page, 'pmxi-admin-' ) ) {
			return 'c';
		}

		$screen = get_current_screen();
		if ( $screen && false !== strpos( $screen->id, 'pmxi' ) ) {
			return 'c';
		}

		return 'b';
	}

	/**
	 * Get the detected product label.
	 *
	 * @return string
	 * @since 3.6.0
	 */
	private static function get_product_label() {
		return self::$wp_all_import_label ? self::$wp_all_import_label : __( 'WP All Import', 'easy-property-listings' );
	}

	/**
	 * Determine whether a creative has been dismissed by the current user.
	 *
	 * @param string $creative Creative identifier.
	 * @return bool
	 * @since 3.6.0
	 */
	private static function is_dismissed( $creative ) {
		$dismissed = get_user_meta( get_current_user_id(), self::DISMISSED_META_KEY, true );
		$dismissed = is_array( $dismissed ) ? $dismissed : array();
		return in_array( $creative, $dismissed, true );
	}

	/**
	 * Build the dismiss URL for a creative.
	 *
	 * @param string $creative Creative identifier.
	 * @return string
	 * @since 3.6.0
	 */
	private static function get_dismiss_url( $creative ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'epl_dismiss_feedsync_ad',
					'ad'     => $creative,
				),
				admin_url( 'admin-post.php' )
			),
			'epl_dismiss_feedsync_ad_' . $creative
		);
	}

	/**
	 * Store a per-user dismissal and return to the originating screen.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	public static function dismiss_ad() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to dismiss this notice.', 'easy-property-listings' ) );
		}

		$creative = isset( $_GET['ad'] ) ? sanitize_key( wp_unslash( $_GET['ad'] ) ) : '';
		if ( ! in_array( $creative, array( 'a', 'b', 'c' ), true ) ) {
			wp_die( esc_html__( 'Invalid advertisement.', 'easy-property-listings' ) );
		}

		check_admin_referer( 'epl_dismiss_feedsync_ad_' . $creative );

		$dismissed   = get_user_meta( get_current_user_id(), self::DISMISSED_META_KEY, true );
		$dismissed   = is_array( $dismissed ) ? $dismissed : array();
		$dismissed[] = $creative;
		update_user_meta( get_current_user_id(), self::DISMISSED_META_KEY, array_values( array_unique( $dismissed ) ) );

		$redirect = wp_get_referer();
		wp_safe_redirect( $redirect ? $redirect : admin_url() );
		exit;
	}

	/**
	 * Output shared animated wire markup.
	 *
	 * @param int $packets Number of packets.
	 * @return void
	 * @since 3.6.0
	 */
	private static function render_wire( $packets = 3 ) {
		echo '<span class="epl-fs-ad__wire" aria-hidden="true"></span>';
		echo '<span class="epl-fs-ad__packets" aria-hidden="true">';
		for ( $i = 0; $i < $packets; $i++ ) {
			echo '<i class="epl-fs-ad__packet"></i>';
		}
		echo '</span>';
	}

	/**
	 * Output shared dismiss control.
	 *
	 * @param string $creative Creative identifier.
	 * @return void
	 * @since 3.6.0
	 */
	private static function render_dismiss( $creative ) {
		printf(
			'<a class="epl-fs-ad__dismiss" href="%1$s" aria-label="%2$s">&#10005;</a>',
			esc_url( self::get_dismiss_url( $creative ) ),
			esc_attr__( 'Dismiss FeedSync message', 'easy-property-listings' )
		);
	}

	/**
	 * Render flagship creative A.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	private static function render_creative_a() {
		?>
		<div class="epl-fs-ad-wrap">
			<div class="epl-fs-ad epl-fs-ad--a">
				<?php self::render_wire( 4 ); ?>
				<div class="epl-fs-ad__mark" aria-label="FeedSync">
					<svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true"><path d="M9 3H5a2 2 0 0 0-2 2v4M3 19v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="15" cy="7" r="1.4" fill="#4c7bff"/><circle cx="20.5" cy="7" r="1.4" fill="#4c7bff" opacity=".55"/><circle cx="15" cy="14" r="1.4" fill="#4c7bff"/><circle cx="20.5" cy="14" r="1.4" fill="#4c7bff" opacity=".55"/><circle cx="15" cy="21" r="1.4" fill="#4c7bff" opacity=".55"/><circle cx="20.5" cy="21" r="1.4" fill="#4c7bff" opacity=".3"/><path d="M9 14h1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
					<strong>FeedSync</strong>
				</div>
				<div class="epl-fs-ad__copy">
					<span class="epl-fs-ad__detected"><span></span><?php echo esc_html( self::get_product_label() ); ?> <?php esc_html_e( 'detected on this site', 'easy-property-listings' ); ?></span>
					<h2><?php esc_html_e( 'Listings taking hours to show up?', 'easy-property-listings' ); ?></h2>
					<p><?php esc_html_e( 'Move to FeedSync. Your listings go live the second your CRM sends them — no imports, no schedule, nothing to manage.', 'easy-property-listings' ); ?></p>
				</div>
				<div class="epl-fs-ad__cta">
					<a class="epl-fs-ad__button" href="<?php echo esc_url( self::FEEDSYNC_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Move to FeedSync →', 'easy-property-listings' ); ?></a>
					<span><?php esc_html_e( 'Built for real estate feeds · since 2014', 'easy-property-listings' ); ?></span>
				</div>
				<?php self::render_dismiss( 'a' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render global strip creative B.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	private static function render_creative_b() {
		?>
		<div class="epl-fs-ad-wrap">
			<div class="epl-fs-ad epl-fs-ad--b">
				<?php self::render_wire( 3 ); ?>
				<span class="epl-fs-ad__badge"><?php esc_html_e( 'Slow site?', 'easy-property-listings' ); ?></span>
				<div class="epl-fs-ad__text"><strong><?php esc_html_e( 'Website slow when your imports run?', 'easy-property-listings' ); ?></strong> <?php esc_html_e( 'Move to FeedSync — your CRM syncs straight to your site. No imports at all.', 'easy-property-listings' ); ?></div>
				<a class="epl-fs-ad__button" href="<?php echo esc_url( self::FEEDSYNC_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Move to FeedSync', 'easy-property-listings' ); ?></a>
				<?php self::render_dismiss( 'b' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render WP All Import screen creative C.
	 *
	 * @return void
	 * @since 3.6.0
	 */
	private static function render_creative_c() {
		?>
		<div class="epl-fs-ad-wrap">
			<div class="epl-fs-ad epl-fs-ad--c">
				<div class="epl-fs-ad__top">
					<div>
						<span class="epl-fs-ad__detected"><span></span><?php echo esc_html( self::get_product_label() ); ?> <?php esc_html_e( 'detected', 'easy-property-listings' ); ?></span>
						<h2><?php esc_html_e( 'Sold this morning.', 'easy-property-listings' ); ?> <em><?php esc_html_e( 'Still on your site tonight?', 'easy-property-listings' ); ?></em></h2>
						<p><?php echo esc_html( self::get_product_label() ); ?> <?php esc_html_e( 'only updates your website when its next import runs. FeedSync updates it the second your CRM changes something.', 'easy-property-listings' ); ?></p>
					</div>
					<a class="epl-fs-ad__button" href="<?php echo esc_url( self::FEEDSYNC_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Move to FeedSync →', 'easy-property-listings' ); ?></a>
				</div>
				<div class="epl-fs-ad__lanes" aria-hidden="true">
					<div class="epl-fs-ad__lane">
						<div class="epl-fs-ad__who"><strong><?php esc_html_e( 'Your site now', 'easy-property-listings' ); ?></strong><span><?php echo esc_html( self::get_product_label() ); ?></span></div>
						<div class="epl-fs-ad__track"><i class="epl-fs-ad__batch"></i></div>
						<div class="epl-fs-ad__end"><?php esc_html_e( 'updates every few hours', 'easy-property-listings' ); ?></div>
					</div>
					<div class="epl-fs-ad__lane">
						<div class="epl-fs-ad__who"><strong><?php esc_html_e( 'Your site on FeedSync', 'easy-property-listings' ); ?></strong><span><?php esc_html_e( 'direct from your CRM', 'easy-property-listings' ); ?></span></div>
						<div class="epl-fs-ad__track"><i class="epl-fs-ad__stream"></i><i class="epl-fs-ad__stream"></i><i class="epl-fs-ad__stream"></i><i class="epl-fs-ad__stream"></i><i class="epl-fs-ad__stream"></i></div>
						<div class="epl-fs-ad__end"><?php esc_html_e( 'updates instantly', 'easy-property-listings' ); ?></div>
					</div>
				</div>
				<?php self::render_dismiss( 'c' ); ?>
			</div>
		</div>
		<?php
	}
}

EPL_Admin_Feedsync_Ads::init();
