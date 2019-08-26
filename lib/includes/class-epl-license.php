<?php
/**
 * EPL License
 *
 * @package     EPL_License Class
 * @subpackage  Classes/License
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification

if ( ! class_exists( 'EPL_License' ) ) :

	/**
	 * License handler for Easy Property Listings
	 *
	 * This class should simplify the process of adding license information to new EPL extensions.
	 *
	 * @since   1.0
	 * @version 1.1
	 */
	class EPL_License {
		/**
		 * File
		 *
		 * @var string $file.
		 */
		private $file;

		/**
		 * License
		 *
		 * @var string $file.
		 */
		private $license;

		/**
		 * Item name
		 *
		 * @var string $file.
		 */
		private $item_name;

		/**
		 * Shortname
		 *
		 * @var string $file.
		 */
		private $item_shortname;

		/**
		 * Version
		 *
		 * @var string $file.
		 */
		private $version;

		/**
		 * Author name
		 *
		 * @var string $file.
		 */
		private $author;

		/**
		 * API URL
		 *
		 * @var string $file.
		 */
		private $api_url = 'https://easypropertylistings.com.au/edd-sl-api/';

		/**
		 * Class constructor
		 *
		 * @global array $epl_options
		 * @param string $_file File.
		 * @param string $_item_name Item name.
		 * @param string $_version Version.
		 * @param string $_author Author name.
		 * @param string $_optname Options.
		 * @param string $_api_url API URL.
		 * @param string $_item_id Item ID.
		 */
		public function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null, $_item_id = null ) {

			global $epl_options;

			$this->file      = $_file;
			$this->item_name = $_item_name;

			if ( is_numeric( $_item_id ) ) {
				$this->item_id = absint( $_item_id );
			}

			$this->item_shortname_without_prefix = preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->item_shortname                = 'epl_' . $this->item_shortname_without_prefix;

			$this->version = $_version;

			$this->license = isset( $epl_options[ $this->item_shortname . '_license_key' ] ) ? trim( $epl_options[ $this->item_shortname . '_license_key' ] ) : '';

			if ( empty( $this->license ) ) {

				$epl_license = get_option( 'epl_license' );
				if ( ! empty( $epl_license ) && isset( $epl_license[ $this->item_shortname_without_prefix ] ) ) {

					$this->license = $epl_license[ $this->item_shortname_without_prefix ];
				}
			}

			$this->author  = $_author;
			$this->api_url = is_null( $_api_url ) ? $this->api_url : $_api_url;

			/**
			 * Allows for backwards compatibility with old license options,
			 * i.e. if the plugins had license key fields previously, the license
			 * handler will automatically pick these up and use those in lieu of the
			 * user having to reactive their license.
			 */
			if ( ! empty( $_optname ) && isset( $epl_options[ $_optname ] ) && empty( $this->license ) ) {

				$this->license = trim( $epl_options[ $_optname ] );
			}

			// Setup hooks.
			$this->includes();
			$this->hooks();
			// phpcs:ignore // Test $this->auto_updater(); code.
			// phpcs:ignore // Test $this->maybe_validate_license(); code.
		}

		/**
		 * Include the updater class
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if ( ! class_exists( 'EPL_SL_Plugin_Updater' ) ) {
				require_once 'class-epl-sl-plugin-updater.php';
			}
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		private function hooks() {

			// Activate license key on settings save.
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key.
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Updater.
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			add_action( 'admin_init', array( $this, 'maybe_validate_license' ), 0 );

			add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );

			add_action( 'admin_notices', array( $this, 'license_notices' ), 5 );

			add_action( 'admin_notices', array( $this, 'notices' ), 20 );

			// Register plugins for beta support.
			add_filter( 'epl_beta_enabled_extensions', array( $this, 'register_beta_support' ) );
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

			if ( 'valid' !== $license ) {
				// dont check for updates on unvalid licensed.
				return;
			}

			$betas = epl_get_option( 'enabled_betas', array() );

			$args = array(
				'version' => $this->version,
				'license' => $this->license,
				'author'  => $this->author,
				'beta'    => function_exists( 'epl_extension_has_beta_support' ) && epl_extension_has_beta_support( $this->item_shortname ),
			);

			if ( ! empty( $this->item_id ) ) {
				$args['item_id'] = $this->item_id;
			} else {
				$args['item_name'] = $this->item_name;
			}

			// Setup the updater.
			$edd_updater = new EPL_SL_Plugin_Updater(
				$this->api_url,
				$this->file,
				$args
			);

		}

		/**
		 * Activate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function activate_license() {

			if ( ! isset( $_REQUEST['action'] ) || 'epl_settings' !== $_REQUEST['action'] ) {
				return;
			}

			if ( ! isset( $_POST['epl_license'] ) ) {
				return;
			}

			if ( empty( $_POST['epl_license'][ $this->item_shortname ] ) ) {

				delete_option( $this->item_shortname . '_license_active' );

				return;
			}

			foreach ( $_POST as $key => $value ) {
				if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key.
					return;
				}
			}

			$license_active = get_option( $this->item_shortname . '_license_active' );

			if ( 'valid' === $license_active ) {
				return;
			}

			$license = sanitize_text_field( wp_unslash( $_POST['epl_license'][ $this->item_shortname ] ) );

			if ( empty( $license ) ) {
				return;
			}

			// Data to send to the API.
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => rawurlencode( $this->item_name ),
				'url'        => home_url(),
			);

			// Call the API.
			$response = wp_remote_get(
				add_query_arg( $api_params, $this->api_url ),
				array(
					'timeout'   => 15,
					// Previous 'body'      => $api_params, Version.
					'sslverify' => false,
				)
			);

			// Make sure there are no errors.
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Tell WordPress to look for updates.
			set_site_transient( 'update_plugins', null );

			// Decode license data.
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			update_option( $this->item_shortname . '_license_active', $license_data->license );

			if ( ! (bool) $license_data->success ) {
				set_transient( 'epl_license_error', $license_data, 1000 );
			} else {
				delete_transient( 'epl_license_error' );
				$option                        = get_option( $this->item_shortname . '_license_status' );
				$option['expired']             = false;
				$option['no_activations_left'] = false;
				$option['item_name_mismatch']  = false;
				$option['missing']             = false;
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

			if ( ! isset( $_REQUEST['action'] ) || 'epl_settings' !== $_REQUEST['action'] ) {
				return;
			}

			if ( ! isset( $_POST['epl_license'] ) ) {
				return;
			}

			if ( ! isset( $_POST['epl_license'][ $this->item_shortname ] ) ) {
				return;
			}

			// Run on deactivate button press.
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) { // Need to check this param.

				// Data to send to the API.
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => rawurlencode( $this->item_name ),
					'url'        => home_url(),
				);

				// Call the API.
				$response = wp_remote_get(
					add_query_arg( $api_params, $this->api_url ),
					array(
						'timeout'   => 15,
						'sslverify' => false,
					)
				);

				// Make sure there are no errors.
				if ( is_wp_error( $response ) ) {
					return;
				}

				// Decode the license data.
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( 'deactivated' === $license_data->license ) {
					delete_option( $this->item_shortname . '_license_active' );
				}

				if ( ! (bool) $license_data->success ) {
					set_transient( 'epl_license_error', $license_data, 1000 );
				} else {
					delete_transient( 'epl_license_error' );
					$option                        = get_option( $this->item_shortname . '_license_status' );
					$option['expired']             = false;
					$option['no_activations_left'] = false;
					$option['item_name_mismatch']  = false;
					$option['missing']             = false;
					update_option( $this->item_shortname . '_license_status', $option );
				}
			}
		}

		/**
		 * Check if validating license
		 *
		 * @access  private
		 * @return  void
		 */
		public function maybe_validate_license() {

			// uncomment next two lines for testing.
			// $this->validate_license(); | Testing.
			// return; | Testing.

			// Perform a request to validate the license.
			// Only run every 24 hours.
			$opt_key = 'epl_license_updates_' . $this->item_shortname;

			$timestamp = get_option( $opt_key );

			if ( ! $timestamp ) {
				$timestamp = strtotime( '+24 hours' );
				update_option( $opt_key, $timestamp );
				$this->validate_license();
			} else {
				$current_timestamp = time();
				if ( $current_timestamp < $timestamp ) {
					return;
				} else {
					update_option( $opt_key, strtotime( '+24 hours' ) );
					$this->validate_license();
				}
			}
		}

		/**
		 * Validate license
		 *
		 * @access  private
		 * @return  void
		 */
		private function validate_license() {

			// Data to send to the API.
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $this->license,
				'item_name'  => rawurlencode( $this->item_name ),
				'url'        => home_url(),
			);

			// Call the API.
			$response = wp_remote_get(
				add_query_arg( $api_params, $this->api_url ),
				array(
					'timeout'   => 15,
					// Previous 'body'      => $api_params, | code.
					'sslverify' => false,
				)
			);

			// Make sure there are no errors.
			if ( is_wp_error( $response ) ) {
				set_transient( $this->item_shortname . '_license_active', false, 10 * MINUTE_IN_SECONDS );
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( isset( $license_data->error ) ) {

				switch ( $license_data->error ) {

					case 'missing':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                        = get_option( $this->item_shortname . '_license_status' );
						$option['expired']             = false;
						$option['no_activations_left'] = false;
						$option['item_name_mismatch']  = false;
						$option['missing']             = true;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'expired':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                        = get_option( $this->item_shortname . '_license_status' );
						$option['expired']             = true;
						$option['no_activations_left'] = false;
						$option['item_name_mismatch']  = false;
						$option['missing']             = false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'no_activations_left':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                        = get_option( $this->item_shortname . '_license_status' );
						$option['expired']             = false;
						$option['no_activations_left'] = true;
						$option['item_name_mismatch']  = false;
						$option['missing']             = false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

					case 'item_name_mismatch':
						set_transient( $this->item_shortname . '_license_active', false, DAY_IN_SECONDS );
						$option                        = get_option( $this->item_shortname . '_license_status' );
						$option['expired']             = false;
						$option['no_activations_left'] = false;
						$option['item_name_mismatch']  = true;
						$option['missing']             = false;
						update_option( $this->item_shortname . '_license_status', $option );
						break;

				}

				return;
			}

			// Otherwise, our check has returned successfully. Set the transient and update our license type and flags.
			set_transient( '_epl_validate_license', true, DAY_IN_SECONDS );
			$option                        = get_option( $this->item_shortname . '_license_status' );
			$option['expired']             = false;
			$option['no_activations_left'] = false;
			$option['item_name_mismatch']  = false;
			$option['missing']             = false;
			update_option( $this->item_shortname . '_license_status', $option );
		}

		/**
		 * Outputs any notices generated by the class.
		 *
		 * @since 3.1.4
		 */
		public function license_notices() {

			$option = get_option( $this->item_shortname . '_license_status' );

			// Only display notices to admin users.
			if ( current_user_can( 'manage_options' ) ) {

				// If there is no license key, output nag about ensuring key is set for automatic updates.
				if ( isset( $option['missing'] ) && $option['missing'] ) :
					?>
					<div class="error">
						<p>
						<?php

							printf( // translators: item name.
								wp_kses_post( __( 'No valid license key has been entered, so automatic updates for %1$s have been turned off. <a href="%2$s">Please click here to enter your license key and begin receiving automatic updates.</a>', 'easy-property-listings' ) ),
								esc_attr( $this->item_name ),
								esc_url( add_query_arg( array( 'page' => 'epl-licenses' ), admin_url( 'admin.php' ) ) )
							);
						?>
						</p>
					</div>
					<?php
				endif;

				// If a key has expired, output nag about renewing the key.
				if ( isset( $option['expired'] ) && $option['expired'] ) :
					?>
					<div class="error">
						<p>
							<?php
								// translators: item name.
								printf( wp_kses_post( __( 'Your license key for %1$s has expired. <a href="%2$s" target="_blank">Please click here to renew your license key and continue receiving automatic updates.</a>', 'easy-property-listings' ) ), esc_attr( $this->item_name ), 'https://easypropertylistings.com.au/your-account/' );
							?>
						</p>
					</div>
					<?php
				endif;

				// If activation limit reached for the license.
				if ( isset( $option['no_activations_left'] ) && $option['no_activations_left'] ) :
					?>
					<div class="error">
						<p>
							<?php
								// translators: item name.
								printf( wp_kses_post( __( 'Your maximum usage limit for license key of %s has been reached. Please use a different key to continue receiving automatic updates.', 'easy-property-listings' ) ), esc_attr( $this->item_name ) );
							?>
								</p>
					</div>
					<?php
				endif;

				// If a key is invalid, output nag about using another key.
				if ( isset( $option['item_name_mismatch'] ) && $option['item_name_mismatch'] ) :
					?>
					<div class="error">
						<p>
							<?php
							// translators: item name.
								printf( wp_kses_post( __( 'The license you entered for %s does not belong to it. Please use a different key to continue receiving automatic updates.', 'easy-property-listings' ) ), esc_attr( $this->item_name ) );
							?>
						</p>
					</div>
					<?php
				endif;
			}
		}


		/**
		 * Admin notices for errors
		 *
		 * @access  public
		 * @return  void
		 */
		public function notices() {

			if ( ! isset( $_GET['page'] ) || 'epl-licenses' !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
				return;
			}

			$license_error = get_transient( 'epl_license_error' );

			if ( false === $license_error ) {
				return;
			}

			if ( ! empty( $license_error->error ) ) {

				switch ( $license_error->error ) {

					case 'item_name_mismatch':
						$message = __( 'This license does not belong to the product you have entered it for.', 'easy-property-listings' );
						break;

					case 'no_activations_left':
						$message = __( 'This license does not have any activations left', 'easy-property-listings' );
						break;

					case 'expired':
						$message = __( 'This license key is expired. Please renew it.', 'easy-property-listings' );
						break;

					default:
						// translators: error.
						$message = sprintf( wp_kses_post( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'easy-property-listings' ) ), $license_error->error );
						break;

				}
			}

			if ( ! empty( $message ) ) {

				echo '<div class="error">';
					echo '<p>' . wp_kses_post( $message ) . '</p>';
				echo '</div>';

			}

			delete_transient( 'epl_license_error' );

		}

		/**
		 * Displays message inline on plugin row that the license key is missing
		 *
		 * @param array $plugin_data Data.
		 * @param array $version_info Version info.
		 * @access  public
		 * @since   2.5
		 * @return  void
		 */
		public function plugin_row_license_missing( $plugin_data, $version_info ) {

			static $showed_imissing_key_message;

			$license = get_option( $this->item_shortname . '_license_active' );

			if ( ( 'valid' !== $license ) && empty( $showed_imissing_key_message[ $this->item_shortname ] ) ) {

				echo '&nbsp;<strong><a href="' . esc_url( add_query_arg( array( 'page' => 'epl-licenses' ), admin_url( 'admin.php' ) ) ) . '">' . esc_html__( 'Enter valid license key for automatic updates.', 'easy-property-listings' ) . '</a></strong>';
				$showed_imissing_key_message[ $this->item_shortname ] = true;
			}

		}

		/**
		 * Adds this plugin to the beta page
		 *
		 * @access  public
		 *
		 * @param array $products Beta products.
		 *
		 * @return array
		 * @since   2.6.11
		 */
		public function register_beta_support( $products ) {
			$products[ $this->item_shortname ] = $this->item_name;

			return $products;
		}
	}

endif; // end class_exists check.
