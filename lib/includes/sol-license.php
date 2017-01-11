<?php
/**
 * License class.
 *
 * @since 1.0.0
 *
 * @package Soliloquy
 * @author  Thomas Griffin
 */
 
 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soliloquy_License {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the license key.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $key;

    /**
     * Holds any license error messages.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $errors = array();

    /**
     * Holds any license success messages.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $success = array();

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Soliloquy::get_instance();

        // Possibly verify the key.
        $this->maybe_verify_key();

        // Add potential admin notices for actions around the admin.
        add_action( 'admin_notices', array( $this, 'notices' ) );

        // Grab the license key. If it is not set (even after verification), return early.
        $this->key = $this->base->get_license_key();
        if ( ! $this->key ) {
            return;
        }

        // Possibly handle validating, deactivating and refreshing license keys.
        $this->maybe_validate_key();
        $this->maybe_deactivate_key();
        $this->maybe_refresh_key();

    }

    /**
     * Maybe verifies a license key entered by the user.
     *
     * @since 1.0.0
     *
     * @return null Return early if the key fails to be verified.
     */
    public function maybe_verify_key() {

        if ( ! $this->is_verifying_key() ) {
            return;
        }

        if ( ! $this->verify_key_action() ) {
            return;
        }

        $this->verify_key();

    }

    /**
     * Verifies a license key entered by the user.
     *
     * @since 1.0.0
     */
    public function verify_key() {

        // Perform a request to verify the key.
        $verify = $this->perform_remote_request( 'verify-key', array( 'tgm-updater-key' => $_POST['soliloquy-license-key'] ) );

        // If it returns false, send back a generic error message and return.
        if ( ! $verify ) {
            $this->errors[] = esc_attr__( 'There was an error connecting to the remote key API. Please try again later.', 'soliloquy' );
            return;
        }

        // If an error is returned, set the error and return.
        if ( ! empty( $verify->error ) ) {
            $this->errors[] = $verify->error;
            return;
        }

        // Otherwise, our request has been done successfully. Update the option and set the success message.
        $option                = get_option( 'soliloquy' );
        $option['key']         = $_POST['soliloquy-license-key'];
        $option['type']        = isset( $verify->type ) ? $verify->type : $option['type'];
        $option['is_expired']  = false;
        $option['is_disabled'] = false;
        $option['is_invalid']  = false;
        $this->success[]       = isset( $verify->success ) ? $verify->success : esc_attr__( 'Congratulations! This site is now receiving automatic updates.', 'soliloquy' );
        update_option( 'soliloquy', $option );

    }

    /**
     * Flag to determine if a key is being verified.
     *
     * @since 1.0.0
     *
     * @return bool True if being verified, false otherwise.
     */
    public function is_verifying_key() {

        return isset( $_POST['soliloquy-license-key'] ) && isset( $_POST['soliloquy-verify-submit'] );

    }

    /**
     * Verifies nonces that allow key verification.
     *
     * @since 1.0.0
     *
     * @return bool True if nonces check out, false otherwise.
     */
    public function verify_key_action() {

        return isset( $_POST['soliloquy-verify-submit'] ) && wp_verify_nonce( $_POST['soliloquy-key-nonce'], 'soliloquy-key-nonce' );

    }

    /**
     * Maybe validates a license key entered by the user.
     *
     * @since 1.0.0
     *
     * @return null Return early if the transient has not expired yet.
     */
    public function maybe_validate_key() {

        // Perform a request to validate the key.
        if ( false === ( $validate = get_transient( '_sol_validate_license' ) ) ) {
            // Only run every 12 hours.
            $timestamp = get_option( 'soliloquy_license_updates' );
            if ( ! $timestamp ) {
                $timestamp = strtotime( '+12 hours' );
                update_option( 'soliloquy_license_updates', $timestamp );
                $this->validate_key();
            } else {
                $current_timestamp = time();
                if ( $current_timestamp < $timestamp ) {
                    return;
                } else {
                    update_option( 'soliloquy_license_updates', strtotime( '+12 hours' ) );
                    $this->validate_key();
                }
            }
        }

    }

    /**
     * Validates a license key entered by the user.
     *
     * @since 1.0.0
     *
     * @param bool $forced Force to set contextual messages (false by default).
     */
    public function validate_key( $forced = false ) {

        $validate = $this->perform_remote_request( 'validate-key', array( 'tgm-updater-key' => $this->key ) );

        // If there was a basic API error in validation, only set the transient for 10 minutes before retrying.
        if ( ! $validate ) {
            // If forced, set contextual success message.
            if ( $forced ) {
                $this->errors[] = esc_attr__( 'There was an error connecting to the remote key API. Please try again later.', 'soliloquy' );
            }

            set_transient( '_sol_validate_license', false, 10 * MINUTE_IN_SECONDS );
            return;
        }

        // If a key or author error is returned, the license no longer exists or the user has been deleted, so reset license.
        if ( isset( $validate->key ) || isset( $validate->author ) ) {
            set_transient( '_sol_validate_license', false, DAY_IN_SECONDS );
            $option                = get_option( 'soliloquy' );
            $option['is_expired']  = false;
            $option['is_disabled'] = false;
            $option['is_invalid']  = true;
            update_option( 'soliloquy', $option );
            return;
        }

        // If the license has expired, set the transient and expired flag and return.
        if ( isset( $validate->expired ) ) {
            set_transient( '_sol_validate_license', false, DAY_IN_SECONDS );
            $option                = get_option( 'soliloquy' );
            $option['is_expired']  = true;
            $option['is_disabled'] = false;
            $option['is_invalid']  = false;
            update_option( 'soliloquy', $option );
            return;
        }

        // If the license is disabled, set the transient and disabled flag and return.
        if ( isset( $validate->disabled ) ) {
            set_transient( '_sol_validate_license', false, DAY_IN_SECONDS );
            $option                = get_option( 'soliloquy' );
            $option['is_expired']  = false;
            $option['is_disabled'] = true;
            $option['is_invalid']  = false;
            update_option( 'soliloquy', $option );
            return;
        }

        // If forced, set contextual success message.
        if ( $forced ) {
            $this->success[] = esc_attr__( 'Congratulations! Your key has been refreshed successfully.', 'soliloquy' );
        }

        // Otherwise, our check has returned successfully. Set the transient and update our license type and flags.
        set_transient( '_sol_validate_license', true, DAY_IN_SECONDS );
        $option                = get_option( 'soliloquy' );
        $option['type']        = isset( $validate->type ) ? $validate->type : $option['type'];
        $option['is_expired']  = false;
        $option['is_disabled'] = false;
        $option['is_invalid']  = false;
        update_option( 'soliloquy', $option );

    }

    /**
     * Maybe deactivates a license key entered by the user.
     *
     * @since 1.0.0
     *
     * @return null Return early if the key fails to be deactivated.
     */
    public function maybe_deactivate_key() {

        if ( ! $this->is_deactivating_key() ) {
            return;
        }

        if ( ! $this->deactivate_key_action() ) {
            return;
        }

        $this->deactivate_key();

    }

    /**
     * Deactivates a license key entered by the user.
     *
     * @since 1.0.0
     */
    public function deactivate_key() {

        // Perform a request to deactivate the key.
        $deactivate = $this->perform_remote_request( 'deactivate-key', array( 'tgm-updater-key' => $_POST['soliloquy-license-key'] ) );

        // If it returns false, send back a generic error message and return.
        if ( ! $deactivate ) {
            $this->errors[] = esc_attr__( 'There was an error connecting to the remote key API. Please try again later.', 'soliloquy' );
            return;
        }

        // If an error is returned, set the error and return.
        if ( ! empty( $deactivate->error ) ) {
            $this->errors[] = $deactivate->error;
            return;
        }

        // Otherwise, our request has been done successfully. Reset the option and set the success message.
        $this->success[] = isset( $deactivate->success ) ? $deactivate->success : esc_attr__( 'Congratulations! You have deactivated the key from this site successfully.', 'soliloquy' );
        update_option( 'soliloquy', Soliloquy::default_options() );

    }

    /**
     * Flag to determine if a key is being deactivated.
     *
     * @since 1.0.0
     *
     * @return bool True if being verified, false otherwise.
     */
    public function is_deactivating_key() {

        return isset( $_POST['soliloquy-license-key'] ) && isset( $_POST['soliloquy-deactivate-submit'] );

    }

    /**
     * Verifies nonces that allow key deactivation.
     *
     * @since 1.0.0
     *
     * @return bool True if nonces check out, false otherwise.
     */
    public function deactivate_key_action() {

        return isset( $_POST['soliloquy-deactivate-submit'] ) && wp_verify_nonce( $_POST['soliloquy-key-nonce'], 'soliloquy-key-nonce' );

    }

    /**
     * Maybe refreshes a license key.
     *
     * @since 1.0.0
     *
     * @return null Return early if the key fails to be refreshed.
     */
    public function maybe_refresh_key() {

        if ( ! $this->is_refreshing_key() ) {
            return;
        }

        if ( ! $this->refresh_key_action() ) {
            return;
        }

        // Refreshing is simply a word alias for validating a key. Force true to set contextual messages.
        $this->validate_key( true );

    }

    /**
     * Flag to determine if a key is being refreshed.
     *
     * @since 1.0.0
     *
     * @return bool True if being refreshed, false otherwise.
     */
    public function is_refreshing_key() {

        return isset( $_POST['soliloquy-license-key'] ) && isset( $_POST['soliloquy-refresh-submit'] );

    }

    /**
     * Verifies nonces that allow key refreshing.
     *
     * @since 1.0.0
     *
     * @return bool True if nonces check out, false otherwise.
     */
    public function refresh_key_action() {

        return isset( $_POST['soliloquy-refresh-submit'] ) && wp_verify_nonce( $_POST['soliloquy-key-nonce'], 'soliloquy-key-nonce' );

    }

    /**
     * Outputs any notices generated by the class.
     *
     * @since 1.0.0
     */
    public function notices() {

        // Grab the option and output any nag dealing with license keys.
        $key    = $this->base->get_license_key();
        $option = get_option( 'soliloquy' );
        
        //Only display notices to admin users
		if ( current_user_can( 'manage_options' ) ) {
   
	        // If there is no license key, output nag about ensuring key is set for automatic updates.
	        if ( ! $key ) :
	        ?>
	        <div class="error">
	            <p><?php printf( __( 'No valid license key has been entered, so automatic updates for Soliloquy have been turned off. <a href="%s">Please click here to enter your license key and begin receiving automatic updates.</a>', 'soliloquy' ), esc_url( add_query_arg( array( 'post_type' => 'soliloquy', 'page' => 'soliloquy-settings' ), admin_url( 'edit.php' ) ) ) ); ?></p>
	        </div>
	        <?php
	        endif;
	
	        // If a key has expired, output nag about renewing the key.
	        if ( isset( $option['is_expired'] ) && $option['is_expired'] ) :
	        ?>
	        <div class="error">
	            <p><?php printf( __( 'Your license key for Soliloquy has expired. <a href="%s" target="_blank">Please click here to renew your license key and continue receiving automatic updates.</a>', 'soliloquy' ), 'https://soliloquywp.com/login/' ); ?></p>
	        </div>
	        <?php
	        endif;
	
	        // If a key has been disabled, output nag about using another key.
	        if ( isset( $option['is_disabled'] ) && $option['is_disabled'] ) :
	        ?>
	        <div class="error">
	            <p><?php esc_html_e( 'Your license key for Soliloquy has been disabled. Please use a different key to continue receiving automatic updates.', 'soliloquy' ); ?></p>
	        </div>
	        <?php
	        endif;
	
	        // If a key is invalid, output nag about using another key.
	        if ( isset( $option['is_invalid'] ) && $option['is_invalid'] ) :
	        ?>
	        <div class="error">
	            <p><?php esc_html_e( 'Your license key for Soliloquy is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'soliloquy' ); ?></p>
	        </div>
	        <?php
	        endif;
	
	        // If there are any license errors, output them now.
	        if ( ! empty( $this->errors ) ) :
	        ?>
	        <div class="error">
	            <p><?php echo implode( '<br>', $this->errors ); ?></p>
	        </div>
	        <?php
	        endif;
	
	        // If there are any success messages, output them now.
	        if ( ! empty( $this->success ) ) :
	        ?>
	        <div class="updated">
	            <p><?php echo implode( '<br>', $this->success ); ?></p>
	        </div>
	        <?php
		        
	        endif;
        	
        }

    }

    /**
     * Queries the remote URL via wp_remote_post and returns a json decoded response.
     *
     * @since 1.0.0
     *
     * @param string $action        The name of the $_POST action var.
     * @param array $body           The content to retrieve from the remote URL.
     * @param array $headers        The headers to send to the remote URL.
     * @param string $return_format The format for returning content from the remote URL.
     * @return string|bool          Json decoded response on success, false on failure.
     */
    public function perform_remote_request( $action, $body = array(), $headers = array(), $return_format = 'json' ) {

        // Build the body of the request.
        $body = wp_parse_args(
            $body,
            array(
                'tgm-updater-action'     => $action,
                'tgm-updater-key'        => $this->key,
                'tgm-updater-wp-version' => get_bloginfo( 'version' ),
                'tgm-updater-referer'    => site_url()
            )
        );
        $body = http_build_query( $body, '', '&' );

        // Build the headers of the request.
        $headers = wp_parse_args(
            $headers,
            array(
                'Content-Type'   => 'application/x-www-form-urlencoded',
                'Content-Length' => strlen( $body )
            )
        );

        // Setup variable for wp_remote_post.
        $post = array(
            'headers' => $headers,
            'body'    => $body
        );

        // Perform the query and retrieve the response.
        $response      = wp_remote_post( 'http://soliloquywp.com/', $post );
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        // Bail out early if there are any errors.
        if ( 200 != $response_code || is_wp_error( $response_body ) ) {
            return false;
        }
                
        // Return the json decoded content.
        return json_decode( $response_body );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Soliloquy_License object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_License ) ) {
            self::$instance = new Soliloquy_License();
        }

        return self::$instance;

    }

}

// Load the license class.
$soliloquy_license = Soliloquy_License::get_instance();