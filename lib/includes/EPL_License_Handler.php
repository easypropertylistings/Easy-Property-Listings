<?php
/**
 * EPL License
 *
 * @package     EPL
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

			$epl_options = get_option('epl_license');
			if( is_null($_optname) ) {
				$_optname = sanitize_key($_item_name);
			}
			$this->file           = $_file;
			$this->item_name      = $_item_name;
			$this->item_shortname = 'epl_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->version        = $_version;
			$this->license        = isset( $epl_options[ $_optname ] ) ? trim( $epl_options[ $_optname ] ) : '';
			$this->author         = $_author;
			$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;

			// Setup hooks
			$this->includes();
			$this->hooks();
			$this->auto_updater();
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

			// Check that license is valid once per week
			add_action( 'epl_weekly_scheduled_events', array( $this, 'weekly_license_check' ) );

			//force wp to check for fresh updates from server
			// set_site_transient( 'update_plugins', null );
			//add_action( 'init', array( $this, 'auto_updater' ), 0 );

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

			if ( empty( $_POST['epl_license'][ $this->item_shortname ] ) ) {

				delete_option( $this->item_shortname . '_license_active' );

				return;

			}

			foreach( $_POST as $key => $value ) {
				if( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key
					return;
				}
			}

			$details = get_option( $this->item_shortname . '_license_active' );

			if ( is_object( $details ) && 'valid' === $details->license ) {
				return;
			}

			$license = sanitize_text_field( $_POST['epl_license'][ $this->item_shortname ] );

			if( empty( $license ) ) {
				return;
			}

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);


			// Call the API
			// body not needed as api_params are sent via GET request in api_url
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

				delete_option( $this->item_shortname . '_license_active' );

			}
		}

		/**
		 * Check if license key is valid once per week
		 *
		 * @access  public
		 * @since   2.5
		 * @return  void
		 */
		public function weekly_license_check() {

			if( ! empty( $_POST['epl_settings'] ) ) {
				return; // Don't fire when saving settings
			}

			if( empty( $this->license ) ) {
				return;
			}

			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'check_license',
				'license' 	=> $this->license,
				'item_name' => urlencode( $this->item_name ),
				'url'       => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				add_query_arg( $api_params, $this->api_url ),
				array(
					'timeout'   => 15,
					'sslverify' => false
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data );

		}


		/**
		 * Admin notices for errors
		 *
		 * @access  public
		 * @return  void
		 */
		public function notices() {

			if( empty( $this->license ) ) {
				return;
			}

			if( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$messages = null;

			$license = get_option( $this->item_shortname . '_license_active' );

			if( 'valid' !== $license ) {

				if( empty( $_GET['page'] ) || 'epl-licenses' !== $_GET['page'] ) {

					$messages = sprintf(
						__( 'You have invalid or expired license keys for Easy Property Listings. Please go to the <a href="%s" title="Go to Licenses page">Licenses page</a> to correct this issue.', 'easy-property-listings' ),
						admin_url( 'admin.php?page=epl-licenses' )
					);

					$showed_invalid_message = true;

				}

			}

			if( ! is_null( $messages ) ) {

				echo '<div class="error">';
					echo '<p>' . $messages . '</p>';
				echo '</div>';

			}

		}

	}

endif; // end class_exists check