<?php
/**
 * EPL License
 *
 * @package     EPL
 * @subpackage  Classes/License
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EPL_License' ) ) :

	/**
	 * License handler for Easy Property Listings
	 *
	 * This class should simplify the process of adding license information to new EPL extensions.
	 *
	 * @since	1.0
	 * @version	1.1
	 */
	class EPL_License {
		private $file;
		private $license;
		private $item_name;
		private $item_shortname;
		private $version;
		private $author;
		private $api_url = 'https://easypropertylistings.com.au/edd-sl-api/';

		/**
		 * Class constructor
		 *
		 * @global  array $epl_options
		 * @param string  $_file
		 * @param string  $_item_name
		 * @param string  $_version
		 * @param string  $_author
		 * @param string  $_optname
		 * @param string  $_api_url
		 */
		function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null ) {
			global $epl_options;

			$this->file           = $_file;
			$this->item_name      = $_item_name;

			$this->item_shortname_without_prefix = preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->item_shortname = 'epl_' . $this->item_shortname_without_prefix;

			$this->version        = $_version;

			$this->license        = isset( $epl_options[ $this->item_shortname . '_license_key' ] ) ? trim( $epl_options[ $this->item_shortname . '_license_key' ] ) : '';
			if(empty($this->license)) {
				$epl_license = get_option('epl_license');
				if(!empty($epl_license) && isset($epl_license[$this->item_shortname_without_prefix])) {
					$this->license = $epl_license[$this->item_shortname_without_prefix];
				}
			}

			$this->author         = $_author;
			$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;

			/**
			 * Allows for backwards compatibility with old license options,
			 * i.e. if the plugins had license key fields previously, the license
			 * handler will automatically pick these up and use those in lieu of the
			 * user having to reactive their license.
			 */
			if ( ! empty( $_optname ) && isset( $epl_options[ $_optname ] ) && empty( $this->license ) ) {
				$this->license = trim( $epl_options[ $_optname ] );
			}

			// Setup hooks
			$this->includes();
			$this->hooks();
			//$this->auto_updater();
		}

		/**
		 * Include the updater class
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if ( ! class_exists( 'EPL_SL_Plugin_Updater' ) )
				require_once 'EPL_SL_Plugin_Updater.php';
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		private function hooks() {
			// Register settings
			add_filter( 'epl_settings_licenses', array( $this, 'settings' ), 1 );

			// Activate license key on settings save
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Updater
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			add_action( 'admin_notices', array( $this, 'notices' ) );
		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @global  array $epl_options
		 * @return  void
		 */
		public function auto_updater() {
			// Setup the updater
			$epl_updater = new EPL_SL_Plugin_Updater(
				$this->api_url,
				$this->file,
				array(
					'version'   => $this->version,
					'license'   => $this->license,
					'item_name' => $this->item_name,
					'author'    => $this->author
				)
			);
		}


		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array   $settings
		 * @return  array
		 */
		public function settings( $settings ) {
			$epl_license_settings = array(
				array(
					'id'      => $this->item_shortname . '_license_key',
					'name'    => sprintf( __( '%1$s License Key', 'epl' ), $this->item_name ),
					'desc'    => '',
					'type'    => 'license_key',
					'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
					'size'    => 'regular'
				)
			);

			return array_merge( $settings, $epl_license_settings );
		}


		/**
		 * Activate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function activate_license() {
			if( !isset($_REQUEST['action']) || $_REQUEST['action'] != 'epl_settings' )
				return;

			if ( ! isset( $_POST['epl_license'] ) )
				return;

			if ( ! isset( $_POST['epl_license'][ $this->item_shortname ] ) )
				return;

			foreach( $_POST as $key => $value ) {
				if( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key
					return;
				}
			}

			$license = sanitize_text_field( $_POST['epl_license'][ $this->item_shortname ] );

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);

			// Call the API
			$response = wp_remote_get(
				add_query_arg( $api_params, $this->api_url ),
				array(
					'timeout'   => 15,
					//'body'      => $api_params,
					'sslverify' => false
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) )
				return;

			// Tell WordPress to look for updates
			set_site_transient( 'update_plugins', null );

			// Decode license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			update_option( $this->item_shortname . '_license_active', $license_data->license );

			if( ! (bool) $license_data->success ) {
				set_transient( 'epl_license_error', $license_data, 1000 );
			} else {
				delete_transient( 'epl_license_error' );
			}
		}


		/**
		 * Deactivate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function deactivate_license() {
			if( !isset($_REQUEST['action']) || $_REQUEST['action'] != 'epl_settings' )
				return;

			if ( ! isset( $_POST['epl_license'] ) )
				return;

			if ( ! isset( $_POST['epl_license'][ $this->item_shortname ] ) )
				return;

			// Run on deactivate button press
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) { //Need to check this param

				// Data to send to the API
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => urlencode( $this->item_name ),
					'url'        => home_url()
				);

				// Call the API
				$response = wp_remote_get(
					add_query_arg( $api_params, $this->api_url ),
					array(
						'timeout'   => 15,
						'sslverify' => false
					)
				);

				// Make sure there are no errors
				if ( is_wp_error( $response ) )
					return;

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( $license_data->license == 'deactivated' )
					delete_option( $this->item_shortname . '_license_active' );

				if( ! (bool) $license_data->success ) {
					set_transient( 'epl_license_error', $license_data, 1000 );
				} else {
					delete_transient( 'epl_license_error' );
				}
			}
		}

		/**
		 * Admin notices for errors
		 *
		 * @access  public
		 * @return  void
		 */
		public function notices() {

			if( ! isset( $_GET['page'] ) || 'epl-licenses' !== $_GET['page'] ) {
				return;
			}

			$license_error = get_transient( 'epl_license_error' );

			if( false === $license_error ) {
				return;
			}

			if( ! empty( $license_error->error ) ) {

				switch( $license_error->error ) {

					case 'item_name_mismatch' :

						$message = __( 'This license does not belong to the product you have entered it for.', 'epl' );
						break;

					case 'no_activations_left' :

						$message = __( 'This license does not have any activations left', 'epl' );
						break;

					case 'expired' :

						$message = __( 'This license key is expired. Please renew it.', 'epl' );
						break;

					default :

						$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'epl' ), $license_error->error );
						break;

				}

			}

			if( ! empty( $message ) ) {

				echo '<div class="error">';
					echo '<p>' . $message . '</p>';
				echo '</div>';

			}

			delete_transient( 'epl_license_error' );

		}
	}

endif; // end class_exists check


/**
 * Register the new license field type
 *
 * This has been included in core, but is maintained for backwards compatibility
 *
 * @return  void
 */
if ( ! function_exists( 'epl_license_key_callback' ) ) {
	function epl_license_key_callback( $args ) {
		global $epl_options;

		if ( isset( $epl_options[ $args['id'] ] ) )
			$value = $epl_options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="epl_settings[' . $args['id'] . ']" name="epl_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

		if ( 'valid' == get_option( $args['options']['is_valid_license_option'] ) ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'epl-recurring' ) . '"/>';
		}

		$html .= '<label for="epl_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}
}
