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

if( !function_exists( 'wp_timezone_string' ) ) {

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

if( !function_exists( 'wp_timezone' ) ) {

        function wp_timezone() {
                return new DateTimeZone( wp_timezone_string() );
        }
        
}

if( !function_exists( 'current_datetime' ) ) {

        function current_datetime() {
                return new DateTimeImmutable( 'now', wp_timezone() );
        }
        
}