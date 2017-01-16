<?php
/**
 * EPL License
 *
 * @package     EPL_License Class
 * @subpackage  Classes/License
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
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
			$this->maybe_validate_license();
		}

		private function maybe_validate_license(){

			// uncomment next two lines for testing
			// $this->validate_license();
			// return;

			//Perform a request to validate the license.
			if ( false === ( $validate = get_transient( '_epl_validate_license' ) ) ) {
				// Only run every 24 hours.
				$timestamp = get_option( 'epl_license_updates' );
				if ( ! $timestamp ) {
					$timestamp = strtotime( '+24 hours' );
					update_option( 'epl_license_updates', $timestamp );
					$this->validate_license();
				} else {
					$current_timestamp = time();
					if ( $current_timestamp < $timestamp ) {
						return;
					} else {
						update_option( 'epl_license_updates', strtotime( '+24 hours' ) );
						$this->validate_license();
					}
				}
			}
		}

		private function validate_license() {

	        // Data to send to the API
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $this->license,
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
			if ( is_wp_error( $response ) ){
				set_transient( $this->item_shortname . '_license_active', false, 10 * MINUTE_IN_SECONDS );
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( isset($license_data->error) ) {

				switch($license_data->error) {

					case 'missing':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                			= get_option( $this->item_shortname . '_license_status' );
						$option['expired']  			= false;
						$option['no_activations_left'] 		= false;
						$option['item_name_mismatch']  		= false;
						$option['missing']  			= true;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'expired':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                			= get_option( $this->item_shortname . '_license_status' );
						$option['expired']  			= true;
						$option['no_activations_left'] 		= false;
						$option['item_name_mismatch']  		= false;
						$option['missing']  			= false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'no_activations_left':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                			= get_option( $this->item_shortname . '_license_status' );
						$option['expired']  			= false;
						$option['no_activations_left'] 		= true;
						$option['item_name_mismatch']  		= false;
						$option['missing']  			= false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'item_name_mismatch':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                			= get_option( $this->item_shortname . '_license_status' );
						$option['expired']  			= false;
						$option['no_activations_left'] 		= false;
						$option['item_name_mismatch']  		= true;
						$option['missing']  			= false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

				}

				return;
			}

	        // Otherwise, our check has returned successfully. Set the transient and update our license type and flags.
	        set_transient( '_epl_validate_license', true, DAY_IN_SECONDS );
		$option                			= get_option( $this->item_shortname . '_license_status' );
		$option['expired']  			= false;
		$option['no_activations_left'] 	= false;
		$option['item_name_mismatch']  	= false;
		$option['missing']  			= false;
		update_option( $this->item_shortname . '_license_status', $option );
		}

		/**
		* Outputs any notices generated by the class.
		*
		* @since 3.1.4
		*/
		public function license_notices() {

			$option = get_option( $this->item_shortname . '_license_status' );

			//Only display notices to admin users
			if ( current_user_can( 'manage_options' ) ) {

			        // If there is no license key, output nag about ensuring key is set for automatic updates.
			        if ( isset( $option['missing'] ) && $option['missing'] ) :
			        ?>
				        <div class="error">
				            <p><?php printf( __( 'No valid license key has been entered, so automatic updates for %s have been turned off. <a href="%s">Please click here to enter your license key and begin receiving automatic updates.</a>', 'easy-property-listings' ),
				            	$this->item_name,
				            	esc_url( add_query_arg( array( 'page' => 'epl-licenses' ), admin_url( 'admin.php' ) ) ) ); ?></p>
				        </div>
				        <?php
				        endif;

				        // If a key has expired, output nag about renewing the key.
				        if ( isset( $option['expired'] ) && $option['expired'] ) :
				        ?>
				        <div class="error">
				            <p><?php printf( __( 'Your license key for %s has expired. <a href="%s" target="_blank">Please click here to renew your license key and continue receiving automatic updates.</a>', 'easy-property-listings' ),$this->item_name, 'https://easypropertylistings.com.au/your-account/' ); ?></p>
				        </div>
				        <?php
				        endif;

				        // If activation limit reached for the license
				        if ( isset( $option['no_activations_left'] ) && $option['no_activations_left'] ) :
				        ?>
				        <div class="error">
				            <p><?php printf(__( 'Your maximum usage limit for license key of %s has been reached. Please use a different key to continue receiving automatic updates.', 'easy-property-listings' ),$this->item_name); ?></p>
				        </div>
				        <?php
				        endif;

				        // If a key is invalid, output nag about using another key.
				        if ( isset( $option['item_name_mismatch'] ) && $option['item_name_mismatch'] ) :
				        ?>
				        <div class="error">
				            <p><?php printf(__( 'The license you entered for %s does not belong to it. Please use a different key to continue receiving automatic updates.', 'easy-property-listings' ),$this->item_name); ?></p>
				        </div>
			        <?php
			        endif;

			}
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

			// Activate license key on settings save
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Updater
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			add_action( 'admin_notices', array( $this, 'license_notices' ), 5 );
			add_action( 'admin_notices', array( $this, 'notices' ),20 );
		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @global  array $epl_options
		 * @return  void
		 */
		public function auto_updater() {

			$license = get_option( $this->item_shortname . '_license_active' );
			if( 'valid' !== $license ) {
				// dont check for updates on unvalid licensed
				return;
			}

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
				$option                			= get_option( $this->item_shortname . '_license_status' );
				$option['expired']  			= false;
				$option['no_activations_left'] 	= false;
				$option['item_name_mismatch']  	= false;
				$option['missing']  			= false;
				update_option( $this->item_shortname . '_license_status', $option );
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
					$option                			= get_option( $this->item_shortname . '_license_status' );
					$option['expired']  			= false;
					$option['no_activations_left'] 	= false;
					$option['item_name_mismatch']  	= false;
					$option['missing']  			= false;
					update_option( $this->item_shortname . '_license_status', $option );
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

						$message = __( 'This license does not belong to the product you have entered it for.', 'easy-property-listings'  );
						break;

					case 'no_activations_left' :

						$message = __( 'This license does not have any activations left', 'easy-property-listings'  );
						break;

					case 'expired' :

						$message = __( 'This license key is expired. Please renew it.', 'easy-property-listings'  );
						break;

					default :

						$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'easy-property-listings'  ), $license_error->error );
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