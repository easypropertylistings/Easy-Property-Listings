<?php
/**
 * EPL Session
 *
 * This is a wrapper class for WP_Session / PHP $_SESSION
 *
 * @package     EPL
 * @subpackage  Classes/Session
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.4.15
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Session Class
 *
 * @since 3.4.15
 */
class EPL_Session {

	/**
	 * Holds our session data
	 *
	 * @var array
	 * @access private
	 * @since 3.4.15
	 */
	private $session;


	/**
	 * Whether to use PHP $_SESSION or WP_Session
	 *
	 * @var bool
	 * @access private
	 * @since 3.4.15
	 */
	private $use_php_sessions = false;

	/**
	 * Session index prefix
	 *
	 * @var string
	 * @access private
	 * @since 3.4.15
	 */
	private $prefix = '';

	/**
	 * Whether the session has been started for this request
	 *
	 * @var bool
	 * @access private
	 * @since 3.6
	 */
	private $started = false;

	/**
	 * Whether EPL loaded the bundled WP_Session library
	 *
	 * @var bool
	 * @access private
	 * @since 3.6
	 */
	private $bundled_wp_session = false;

	/**
	 * Get things started
	 *
	 * Defines our WP_Session constants, includes the necessary libraries and
	 * retrieves the WP Session instance
	 *
	 * @since 3.4.15
	 * @since 3.6 The bundled WP_Session library no longer starts a session on every request.
	 */
	public function __construct() {

		$this->use_php_sessions = $this->use_php_sessions();

		if ( $this->use_php_sessions ) {

			if ( is_multisite() ) {

				$this->prefix = '_' . get_current_blog_id();

			}

			// Use PHP SESSION (must be enabled via the EPL_USE_PHP_SESSIONS constant).
			add_action( 'init', array( $this, 'maybe_start_session' ), -2 );

		} else {

			if ( ! $this->should_start_session() ) {
				return;
			}

			// Use WP_Session (default).
			if ( ! defined( 'WP_SESSION_COOKIE' ) ) {
				define( 'WP_SESSION_COOKIE', 'epl_wp_session' );
			}

			if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {

				require_once EPL_PATH_LIB . 'includes/libraries/class-recursive-arrayaccess.php';
			}

			if ( ! class_exists( 'WP_Session' ) ) {
				require_once EPL_PATH_LIB . 'includes/libraries/class-wp-session.php';
				require_once EPL_PATH_LIB . 'includes/libraries/wp-session.php';

				// The library starts a session, and sends its cookie, on every request. EPL starts it only when needed.
				remove_action( 'plugins_loaded', 'wp_session_start' );
				remove_action( 'shutdown', 'wp_session_write_close' );
				$this->bundled_wp_session = true;
			}

			add_filter( 'wp_session_expiration_variant', array( $this, 'set_expiration_variant_time' ), 99999 );
			add_filter( 'wp_session_expiration', array( $this, 'set_expiration_time' ), 99999 );

		}

		if ( empty( $this->session ) && ! $this->use_php_sessions ) {
			add_action( 'plugins_loaded', array( $this, 'init' ), -1 );
		} else {
			add_action( 'init', array( $this, 'init' ), -1 );
		}
	}

	/**
	 * Setup the WP_Session instance
	 *
	 * Loads the visitor's existing session. A new session is only started when something
	 * is stored in it (see set()), so pages for visitors without a session send no session
	 * cookie and can be cached.
	 *
	 * @since 3.4.15
	 * @since 3.6 Starts a session only when the visitor already has one, or when lazy sessions are off.
	 * @return array|WP_Session
	 */
	public function init() {

		if ( $this->use_php_sessions ) {
			if ( session_id() ) {
				$this->started = true;
				$this->session = $this->get_php_session_data();
			} else {
				$this->session = array();
			}
		} elseif ( ! $this->lazy_session() || $this->has_session_cookie() ) {
			$this->start_session();
		} else {
			$this->session = array();
		}

		return $this->session;
	}

	/**
	 * EPL's data in the PHP session
	 *
	 * @since 3.6
	 * @return array
	 */
	private function get_php_session_data() {
		return isset( $_SESSION[ 'epl' . $this->prefix ] ) && is_array( $_SESSION[ 'epl' . $this->prefix ] ) ? $_SESSION[ 'epl' . $this->prefix ] : array();
	}

	/**
	 * Start the session only when something is stored in it?
	 *
	 * @since 3.6
	 * @return bool
	 */
	public function lazy_session() {
		/**
		 * Filters whether EPL starts the session only when something is stored in it.
		 *
		 * Return false to start a session, and send its cookie, on every request as before 3.6.
		 *
		 * @since 3.6
		 *
		 * @param bool $lazy Default true.
		 */
		return (bool) apply_filters( 'epl_lazy_session', true );
	}

	/**
	 * Does the request carry a session cookie?
	 *
	 * @since 3.6
	 * @return bool
	 */
	public function has_session_cookie() {
		if ( $this->use_php_sessions ) {
			$name = session_name();
		} else {
			$name = defined( 'WP_SESSION_COOKIE' ) ? WP_SESSION_COOKIE : 'epl_wp_session';
		}
		return ! empty( $_COOKIE[ $name ] );
	}

	/**
	 * Start the session and send its cookie
	 *
	 * Values stored before the session started are kept.
	 *
	 * @since 3.6
	 * @return bool True when the session is started.
	 */
	public function start_session() {

		if ( $this->started ) {
			return true;
		}

		if ( ! $this->should_start_session() ) {
			return false;
		}

		$pending = is_array( $this->session ) ? $this->session : array();

		if ( $this->use_php_sessions ) {

			if ( ! session_id() ) {
				if ( headers_sent() ) {
					return false;
				}
				session_start();
			}

			$this->session = array_merge( $this->get_php_session_data(), $pending );

			$_SESSION[ 'epl' . $this->prefix ] = $this->session;

		} else {

			// The cookie can't be sent once output has started.
			if ( ! class_exists( 'WP_Session' ) || headers_sent() ) {
				return false;
			}

			if ( $this->bundled_wp_session ) {
				wp_session_start();
			}

			$this->session = WP_Session::get_instance();

			foreach ( $pending as $key => $value ) {
				$this->session[ $key ] = $value;
			}

			if ( $this->bundled_wp_session && ! has_action( 'shutdown', 'wp_session_write_close' ) ) {
				add_action( 'shutdown', 'wp_session_write_close' );
			}
		}

		$this->started = true;

		return true;
	}


	/**
	 * Retrieve session ID
	 *
	 * @since 1.6
	 * @since 3.6 Returns an empty string when no session is started.
	 * @return string Session ID
	 */
	public function get_id() {
		if ( $this->use_php_sessions ) {
			return session_id();
		}
		return is_object( $this->session ) && isset( $this->session->session_id ) ? $this->session->session_id : '';
	}


	/**
	 * Retrieve a session variable
	 *
	 * @since 3.4.15
	 * @param string $key Session key.
	 * @return mixed Session variable
	 */
	public function get( $key ) {

		$key    = sanitize_key( $key );
		$return = false;

		if ( isset( $this->session[ $key ] ) && ! empty( $this->session[ $key ] ) ) {

			preg_match( '/[oO]\s*:\s*\d+\s*:\s*"\s*(?!(?i)(stdClass))/', $this->session[ $key ], $matches );
			if ( ! empty( $matches ) ) {
				$this->set( $key, null );
				return false;
			}

			if ( is_numeric( $this->session[ $key ] ) ) {
				$return = $this->session[ $key ];
			} else {

				$maybe_json = json_decode( $this->session[ $key ] );

				// Since json_last_error is PHP 5.3+, we have to rely on a `null` value for failing to parse JSON.
				if ( is_null( $maybe_json ) ) {
					$is_serialized = is_serialized( $this->session[ $key ] );
					if ( $is_serialized ) {
						$value = @unserialize( $this->session[ $key ] );
						$this->set( $key, (array) $value );
						$return = $value;
					} else {
						$return = $this->session[ $key ];
					}
				} else {
					$return = json_decode( $this->session[ $key ], true );
				}
			}
		}

		return $return;
	}

	/**
	 * Set a session variable
	 *
	 * @since 3.4.15
	 * @since 3.6 Starts the session, and sends its cookie, the first time a value is stored.
	 *
	 * @param string           $key Session key.
	 * @param int|string|array $value Session variable.
	 * @return mixed Session variable
	 */
	public function set( $key, $value ) {

		$key = sanitize_key( $key );

		// Clearing a value that was never stored needs no session; storing one starts it.
		$clearing = null === $value || '' === $value || array() === $value;
		if ( ! $this->started && ! ( $clearing && empty( $this->session[ $key ] ) ) ) {
			$this->start_session();
		}

		if ( is_array( $value ) ) {
			$this->session[ $key ] = wp_json_encode( $value );
		} else {
			$this->session[ $key ] = esc_attr( $value );
		}

		if ( $this->use_php_sessions && $this->started ) {

			$_SESSION[ 'epl' . $this->prefix ] = $this->session;
		}

		return $this->session[ $key ];
	}

	/**
	 * Force the cookie expiration variant time to 23 hours
	 *
	 * @since 3.4.15
	 * @param int $exp Default expiration (1 hour).
	 * @return int
	 */
	public function set_expiration_variant_time( $exp ) {
		return ( 30 * 60 * 23 );
	}

	/**
	 * Force the cookie expiration time to 24 hours
	 *
	 * @since 3.4.15
	 * @param int $exp Default expiration (1 hour).
	 * @return int Cookie expiration time
	 */
	public function set_expiration_time( $exp ) {
		return ( 30 * 60 * 24 );
	}

	/**
	 * Starts a new session if one hasn't started yet.
	 *
	 * Checks to see if the server supports PHP sessions
	 * or if the EPL_USE_PHP_SESSIONS constant is defined
	 *
	 * @since 3.4.15
	 * @author Daniel J Griffiths
	 * @author Daniel J Griffiths
	 * @return boolean $ret True if we are using PHP sessions, false otherwise.
	 */
	public function use_php_sessions() {

		$ret = false;

		// If the database variable is already set, no need to run autodetection.
		$epl_use_php_sessions = (bool) get_option( 'epl_use_php_sessions' );

		if ( ! $epl_use_php_sessions ) {

			// Attempt to detect if the server supports PHP sessions. Doesn't store anything, so no session is started.
			if ( function_exists( 'session_start' ) ) {

				$ret = true;

				// Set the database option.
				update_option( 'epl_use_php_sessions', true );
			}
		} else {
			$ret = $epl_use_php_sessions;
		}

		// Enable or disable PHP Sessions based on the EPL_USE_PHP_SESSIONS constant.
		if ( defined( 'EPL_USE_PHP_SESSIONS' ) && EPL_USE_PHP_SESSIONS ) {
			$ret = true;
		} elseif ( defined( 'EPL_USE_PHP_SESSIONS' ) && ! EPL_USE_PHP_SESSIONS ) {
			$ret = false;
		}

		return (bool) apply_filters( 'epl_use_php_sessions', $ret );
	}

	/**
	 * Determines if we should start sessions
	 *
	 * @since  2.5.11
	 * @return bool
	 */
	public function should_start_session() {

		$start_session = true;

		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {

			$blacklist = $this->get_blacklist();
			$uri       = ltrim( $_SERVER['REQUEST_URI'], '/' );
			$uri       = untrailingslashit( $uri );

			if ( in_array( $uri, $blacklist, true ) ) {
				$start_session = false;
			}

			if ( false !== strpos( $uri, 'feed=' ) ) {
				$start_session = false;
			}

			if ( false !== strpos( $uri, 'wp_scrape_key' ) ) {
				// Starting sessions while saving the file editor can break the save process, so don't start.
				$start_session = false;
			}
		}

		return apply_filters( 'epl_start_session', $start_session );
	}

	/**
	 * Retrieve the URI blacklist
	 *
	 * These are the URIs where we never start sessions
	 *
	 * @since  2.5.11
	 * @return array
	 */
	public function get_blacklist() {

		$blacklist = apply_filters(
			'epl_session_start_uri_blacklist',
			array(
				'feed',
				'feed/rss',
				'feed/rss2',
				'feed/rdf',
				'feed/atom',
				'comments/feed',
			)
		);

		// Look to see if WordPress is in a sub folder or this is a network site that uses sub folders.
		$folder = str_replace( network_home_url(), '', get_site_url() );

		if ( ! empty( $folder ) ) {
			foreach ( $blacklist as $path ) {
				$blacklist[] = $folder . '/' . $path;
			}
		}

		return $blacklist;
	}

	/**
	 * Starts a new session if one hasn't started yet.
	 *
	 * @since 3.6 Only resumes an existing session; a new one starts when something is stored.
	 */
	public function maybe_start_session() {

		if ( ! $this->should_start_session() ) {
			return;
		}

		if ( $this->lazy_session() && ! $this->has_session_cookie() ) {
			return;
		}

		if ( ! session_id() && ! headers_sent() ) {
			session_start();
		}
	}
}
