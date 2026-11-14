<?php
/**
 * FeedSync Service banners for sites importing listings with the EPL WP All Import add-on.
 *
 * @package     EPL
 * @subpackage  Admin
 * @since       3.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FeedSync Service banners
 *
 * Shown to users who can edit posts when the Easy Property Listings Import CSV, XML WP All
 * Import add-on is active (the legacy FeedSync setup) and the FeedSync Service companion
 * plugin isn't (the site hasn't moved yet). Filter epl_feedsync_ads_show to turn them off.
 *
 * Only one creative is displayed per screen:
 * - A on EPL settings and extension screens.
 * - C on WP All Import screens.
 * - B throughout the remainder of the WordPress admin.
 *
 * Dismissal is stored per user and per creative.
 *
 * @since 3.6
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
	 * Last day legacy FeedSync is supported (Y-m-d). The deadline line shows until the end of this day.
	 *
	 * @var string
	 */
	const LEGACY_RETIRES = '2026-12-05';

	/**
	 * Whether the banners show on this site (cached for the request).
	 *
	 * @var bool|null
	 */
	private static $show = null;

	/**
	 * Register hooks.
	 *
	 * @since 3.6
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_ad' ), 20 );
		add_action( 'admin_post_epl_dismiss_feedsync_ad', array( __CLASS__, 'dismiss_ad' ) );
	}

	/**
	 * Is a plugin with this main file name active?
	 *
	 * Matches on the file name only, so a renamed plugin folder still counts. Includes
	 * network-activated plugins on multisite.
	 *
	 * @param string $basename Plugin main file name, for example epl-wp-all-import.php.
	 * @return bool
	 * @since 3.6
	 */
	private static function is_plugin_file_active( $basename ) {
		$active = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active = array_merge( $active, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		foreach ( $active as $plugin_file ) {
			if ( basename( (string) $plugin_file ) === $basename ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Should the banners show on this site?
	 *
	 * Yes when the EPL WP All Import add-on is active and the FeedSync Service companion
	 * plugin isn't.
	 *
	 * @return bool
	 * @since 3.6
	 */
	public static function should_show() {
		if ( null !== self::$show ) {
			return self::$show;
		}

		$show = self::is_plugin_file_active( 'epl-wp-all-import.php' ) && ! self::is_plugin_file_active( 'feedsync-companion.php' );

		/**
		 * Filters whether the FeedSync Service banners show.
		 *
		 * @since 3.6
		 *
		 * @param bool $show True when the EPL WP All Import add-on is active and the FeedSync companion isn't.
		 */
		self::$show = (bool) apply_filters( 'epl_feedsync_ads_show', $show );

		return self::$show;
	}

	/**
	 * Enqueue the advertisement stylesheet only on targeted installations.
	 *
	 * @return void
	 * @since 3.6
	 */
	public static function enqueue_styles() {
		if ( ! current_user_can( 'edit_posts' ) || ! self::should_show() ) {
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
	 * @since 3.6
	 */
	public static function render_ad() {
		if ( ! current_user_can( 'edit_posts' ) || ! self::should_show() ) {
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
	 * @since 3.6
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
	 * Name of the active WP All Import edition, for the banner copy.
	 *
	 * @return string
	 * @since 3.6
	 */
	private static function get_product_label() {
		return self::is_plugin_file_active( 'wp-all-import-pro.php' ) ? __( 'WP All Import Pro', 'easy-property-listings' ) : __( 'WP All Import', 'easy-property-listings' );
	}

	/**
	 * Output the "detected" chip naming the EPL import add-on.
	 *
	 * @param bool $on_this_site Add "on this site".
	 * @return void
	 * @since 3.6
	 */
	private static function render_detected( $on_this_site = true ) {
		$text = $on_this_site
			? __( 'Easy Property Listings Import add-on detected on this site', 'easy-property-listings' )
			: __( 'Easy Property Listings Import add-on detected', 'easy-property-listings' );

		printf( '<span class="epl-fs-ad__detected"><span></span>%s</span>', esc_html( $text ) );
	}

	/**
	 * Output the label saying which plugin shows the banner.
	 *
	 * @return void
	 * @since 3.6
	 */
	private static function render_from() {
		printf( '<span class="epl-fs-ad__from">%s</span>', esc_html__( 'Easy Property Listings', 'easy-property-listings' ) );
	}

	/**
	 * Determine whether a creative has been dismissed by the current user.
	 *
	 * @param string $creative Creative identifier.
	 * @return bool
	 * @since 3.6
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
	 * @since 3.6
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
	 * @since 3.6
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
	 * Legacy FeedSync retirement line, shown until the retirement date.
	 *
	 * @return string The line, or '' after the retirement date.
	 * @since 3.6
	 */
	private static function get_deadline_text() {
		if ( current_time( 'Y-m-d' ) > self::LEGACY_RETIRES ) {
			return '';
		}
		return __( 'Using legacy FeedSync? It retires on 5 December 2026.', 'easy-property-listings' );
	}

	/**
	 * Output the legacy FeedSync retirement line, when it still applies.
	 *
	 * @return void
	 * @since 3.6
	 */
	private static function render_deadline() {
		$text = self::get_deadline_text();
		if ( '' === $text ) {
			return;
		}
		printf( '<span class="epl-fs-ad__deadline">%s</span>', esc_html( $text ) );
	}

	/**
	 * Output shared animated wire markup.
	 *
	 * @param int $packets Number of packets.
	 * @return void
	 * @since 3.6
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
	 * @since 3.6
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
	 * @since 3.6
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
					<?php self::render_from(); ?>
					<?php self::render_detected(); ?>
					<h2><?php esc_html_e( 'Listings taking hours to show up?', 'easy-property-listings' ); ?></h2>
					<p><?php esc_html_e( 'Move to FeedSync. Your listings go live the second your CRM sends them — no imports, no schedule, nothing to manage.', 'easy-property-listings' ); ?></p>
					<?php self::render_deadline(); ?>
				</div>
				<div class="epl-fs-ad__cta">
					<a class="epl-fs-ad__button" href="<?php echo esc_url( self::FEEDSYNC_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Move to FeedSync →', 'easy-property-listings' ); ?></a>
					<span><?php esc_html_e( 'Built for real estate feeds · since 2011', 'easy-property-listings' ); ?></span>
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
	 * @since 3.6
	 */
	private static function render_creative_b() {
		?>
		<div class="epl-fs-ad-wrap">
			<div class="epl-fs-ad epl-fs-ad--b">
				<?php self::render_wire( 3 ); ?>
				<span class="epl-fs-ad__badge"><?php esc_html_e( 'Slow site?', 'easy-property-listings' ); ?></span>
				<div class="epl-fs-ad__text"><?php self::render_from(); ?><strong><?php esc_html_e( 'Website slow when your imports run?', 'easy-property-listings' ); ?></strong> <?php esc_html_e( 'Move to FeedSync — your CRM syncs straight to your site. No imports at all.', 'easy-property-listings' ); ?><?php self::render_deadline(); ?></div>
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
	 * @since 3.6
	 */
	private static function render_creative_c() {
		?>
		<div class="epl-fs-ad-wrap">
			<div class="epl-fs-ad epl-fs-ad--c">
				<div class="epl-fs-ad__top">
					<div>
						<?php self::render_from(); ?>
						<?php self::render_detected( false ); ?>
						<h2><?php esc_html_e( 'Sold this morning.', 'easy-property-listings' ); ?> <em><?php esc_html_e( 'Still on your site tonight?', 'easy-property-listings' ); ?></em></h2>
						<p><?php echo esc_html( self::get_product_label() ); ?> <?php esc_html_e( 'only updates your website when its next import runs. FeedSync updates it the second your CRM changes something.', 'easy-property-listings' ); ?></p>
						<?php self::render_deadline(); ?>
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
