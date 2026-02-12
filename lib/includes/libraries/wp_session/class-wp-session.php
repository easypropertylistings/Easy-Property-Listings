<?php
/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data using database-backed options for storage.
 * for storing user session information.
 *
 * @package WordPress
 * @subpackage Session
 * @since   3.6.0
 */

/**
 * WordPress Session class for managing user session data.
 *
 * @package WordPress
 * @since   3.6.0
 */
class WP_Session implements ArrayAccess, Iterator, Countable {
	/**
	 * Internal data collection.
	 *
	 * @var array
	 */
	private $container;

	/**
	 * ID of the current session.
	 *
	 * @var string
	 */
	private $session_id;

	/**
	 * Unix timestamp when session expires.
	 *
	 * @var int
	 */
	private $expires;

	/**
	 * Singleton instance.
	 *
	 * @var bool|WP_Session
	 */
	private static $instance = false;

	/**
	 * Retrieve the current session instance.
	 *
	 * @return bool|WP_Session
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Default constructor.
	 * Will rebuild the session collection from the given session ID if it exists. Otherwise, will
	 * create a new session with that ID.
	 *
	 * @uses apply_filters Calls `wp_session_expiration` to determine how long until sessions expire.
	 *
	 * @since 3.5.3 Updated to return local timestamp.
	 */
	private function __construct() {
		if ( isset( $_COOKIE[ WP_SESSION_COOKIE ] ) ) {
			$this->session_id = stripslashes( $_COOKIE[ WP_SESSION_COOKIE ] );
		} else {
			$this->session_id = $this->generate_id();
		}

		$this->expires = epl_get_local_timestamp() + intval( apply_filters( 'wp_session_expiration', 24 * 60 ) );

		$this->read_data();

		setcookie( WP_SESSION_COOKIE, $this->session_id, $this->expires, ( defined( 'COOKIEPATH' ) ? COOKIEPATH : '/' ), ( defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '' ) );
	}

	/**
	 * Generate a cryptographically strong unique ID for the session token.
	 *
	 * @return string
	 */
	private function generate_id() {
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$hasher = new PasswordHash( 8, false );

		return md5( $hasher->get_random_bytes( 32 ) );
	}

	/**
	 * Read data from a transient for the current session.
	 *
	 * Automatically resets the expiration time for the session transient to some time in the future.
	 *
	 * @return array
	 */
	private function read_data() {
		$this->touch_session();
		$this->container = get_option( "_wp_session_{$this->session_id}", array() );
		return $this->container;
	}

	/**
	 * Write the data from the current session to the data storage system.
	 */
	public function write_data() {
		$session_list = get_option( '_wp_session_list', array() );

		$this->touch_session();

		update_option( "_wp_session_{$this->session_id}", $this->container );
	}

	/**
	 * Write the data from the current session to the data storage system.
	 *
	 * @since 3.5.3 Updated to return local timestamp.
	 */
	private function touch_session() {
		$session_list = get_option( '_wp_session_list', array() );

		$session_list[ $this->session_id ] = $this->expires;

		foreach ( $session_list as $id => $expires ) {
			if ( epl_get_local_timestamp() > $this->expires ) {
				delete_option( "_wp_session_{$id}" );
				unset( $session_list[ $id ] );
			}
		}

		update_option( '_wp_session_list', $session_list );
	}

	/**
	 * Output the current container contents as a JSON-encoded string.
	 *
	 * @return string
	 */
	public function json_out() {
		return wp_json_encode( $this->container );
	}

	/**
	 * Decodes a JSON string and, if the object is an array, overwrites the session container with its contents.
	 *
	 * @param string $data JSON-encoded string to decode.
	 *
	 * @return bool
	 */
	public function json_in( $data ) {
		$array = json_decode( $data );

		if ( is_array( $array ) ) {
			$this->container = $array;
			return true;
		}

		return false;
	}

	/**
	 * Regenerate the current session's ID.
	 *
	 * @param bool $delete_old Flag whether or not to delete the old session data from the server.
	 */
	public function regenerate_id( $delete_old = false ) {
		if ( $delete_old ) {
			delete_option( "_wp_session_{$this->session_id}" );

			$session_list = get_option( '_wp_session_list', array() );
			unset( $session_list[ $this->session_id ] );
			update_option( '_wp_session_list', $session_list );
		}

		$this->session_id = $this->generate_id();

		setcookie( WP_SESSION_COOKIE, $this->session_id, epl_get_local_timestamp() + $this->expires, ( defined( 'COOKIEPATH' ) ? COOKIEPATH : '/' ), ( defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '' ) );
	}

	/**
	 * Check if a session has been initialized.
	 *
	 * @return bool
	 */
	public function session_started() {
		return ! ! self::$instance;
	}

	/**
	 * Return the read-only cache expiration value.
	 *
	 * @return int
	 */
	public function cache_expiration() {
		return $this->expires;
	}

	/**
	 * Flushes all session variables.
	 */
	public function reset() {
		$this->container = array();
	}

	// ArrayAccess Implementation.

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function offsetExists( $offset ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return isset( $this->container[ $offset ] );
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function offsetGet( $offset ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return isset( $this->container[ $offset ] ) ? $this->container[ $offset ] : null;
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value  The value to set.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function offsetSet( $offset, $value ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		if ( is_null( $offset ) ) {
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function offsetUnset( $offset ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		unset( $this->container[ $offset ] );
	}

	// Iterator Implementation.

	/**
	 * Current position of the array.
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 *
	 * @return mixed
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function current() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return current( $this->container );
	}

	/**
	 * Key of the current element.
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 *
	 * @return mixed
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function key() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return key( $this->container );
	}

	/**
	 * Move the internal point of the container array to the next item
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function next() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		next( $this->container );
	}

	/**
	 * Rewind the internal point of the container array.
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function rewind() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		reset( $this->container );
	}

	/**
	 * Is the current key valid?
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 *
	 * @return bool
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function valid() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return $this->offsetExists( $this->key() );
	}

	// Countable Implementation.

	/**
	 * Get the count of elements in the container array.
	 *
	 * @link http://php.net/manual/en/countable.count.php
	 *
	 * @return int
	 */
	#[\ReturnTypeWillChange] // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
	public function count() { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
		return count( $this->container );
	}
}
