<?php
/**
 * WordPress core functions for keeping compatibility.
 *
 * @package     EPL
 * @subpackage  Compatibility/Shortcodes
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1.11
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'shortcode_exists' ) ) {
	/**
	 * Whether a registered shortcode exists named $tag
	 *
	 * @since 2.1.11
	 *
	 * @global array $shortcode_tags List of shortcode tags and their callback hooks.
	 *
	 * @param string $tag Shortcode tag to check.
	 * @return bool Whether the given shortcode exists.
	 */
	function shortcode_exists( $tag ) {
		global $shortcode_tags;
		return array_key_exists( $tag, $shortcode_tags );
	}
}

if ( ! function_exists( 'wp_timezone_string' ) ) {
	/**
	 * Retrieves the timezone of the site as a string.
	 *
	 * Uses the `timezone_string` option to get a proper timezone name if available,
	 * otherwise falls back to a manual UTC ± offset.
	 *
	 * Example return values:
	 *
	 *  - 'Europe/Rome'
	 *  - 'America/North_Dakota/New_Salem'
	 *  - 'UTC'
	 *  - '-06:30'
	 *  - '+00:00'
	 *  - '+08:45'
	 *
	 * @since 3.5.7 Added as this function is not present before WordPress 5.3.0
	 *
	 * @return string PHP timezone name or a ±HH:MM offset.
	 */
        function wp_timezone_string() {
                $timezone_string = get_option( 'timezone_string' );
        
                if ( $timezone_string ) {
                        return $timezone_string;
                }
        
                $offset  = (float) get_option( 'gmt_offset' );
                $hours   = (int) $offset;
                $minutes = ( $offset - $hours );
        
                $sign      = ( $offset < 0 ) ? '-' : '+';
                $abs_hour  = abs( $hours );
                $abs_mins  = abs( $minutes * 60 );
                $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
        
                return $tz_offset;
        }
        
}

if ( ! function_exists( 'wp_timezone' ) ) {
	/**
	 * Retrieves the timezone of the site as a `DateTimeZone` object.
	 *
	 * Timezone can be based on a PHP timezone string or a ±HH:MM offset.
	 *
	 * @since 3.5.7 Added as this function is not present before WordPress 5.3.0
	 *
	 * @return DateTimeZone Timezone object.
	 */
        function wp_timezone() {
                return new DateTimeZone( wp_timezone_string() );
        }
        
}

if ( ! function_exists( 'current_datetime' ) ) {
	/**
	 * Retrieves the current time as an object using the site's timezone.
	 *
	 * @since 3.5.7 Added as this function is not present before WordPress 5.3.0
	 *
	 * @return DateTimeImmutable Date and time object.
	 */
        function current_datetime() {
                return new DateTimeImmutable( 'now', wp_timezone() );
        }
        
}
