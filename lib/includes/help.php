<?php
/**
 * Contextual Help
 *
 * @package     EPL
 * @subpackage  Admin/Help
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds the Contextual Help for the main Listings page
 *
 * @since 3.0
 * @return void
 */
function epl_listings_contextual_help() {
    $screen = get_current_screen();
    if (  !in_array($screen->id, epl_get_core_post_types()))
        return;

    $screen->set_help_sidebar(
        '<p><strong>' . sprintf( __( 'For more information:', 'epl' ) . '</strong></p>' .
            '<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the Easy Property Listings website.', 'epl' ), esc_url( 'https://easypropertylistings.com.au/' ) ) ) . '</p>' .
        '<p>' . sprintf(
            __( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>. View <a href="%s">extensions</a> ', 'epl' ),
            esc_url( 'https://github.com/easypropertylistings/Easy-Property-Listings/issues' ),
            esc_url( 'https://github.com/easypropertylistings/Easy-Property-Listings' ),
            esc_url( 'https://easypropertylistings.com.au/extensions/' )
        ) . '</p>'
    );

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-configuration',
        'title'		=> __('Title One'),
        'content'	=> __('Content One')

    ) );
    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-configuration-two',
        'title'		=> __('Title Two'),
        'content'	=> __('Content Two')

    ) );

    /**
     * Fires off in the EDD Downloads Contextual Help Screen
     *
     * @since 3.0
     * @param object $screen The current admin screen
     */
    do_action( 'epl_listings_contextual_help', $screen );
}
add_action( 'load-post.php', 'epl_listings_contextual_help' );
add_action( 'load-post-new.php', 'epl_listings_contextual_help' );
