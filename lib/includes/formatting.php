<?php
/**
 * Formatting functions for taking care of proper number formats and such
 * Adapted from Easy Digital Downloads
 *
 * @package     EPL
 * @subpackage  Functions/Formatting
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sanitize Amount
 *
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * @since 1.0
 * @param string $amount Price amount to format
 * @return string $amount Newly sanitized amount
 */
function epl_sanitize_amount( $amount ) {
	$thousands_sep = epl_get_thousands_separator();
	$decimal_sep = epl_get_decimal_separator();

	// Sanitize the amount
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		if ( $thousands_sep == '.' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		} elseif( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
			$amount = str_replace( '.', '', $amount );
		}

		$amount = str_replace( $decimal_sep, '.', $amount );
	} elseif( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( $thousands_sep, '', $amount );
	}

	$decimals = apply_filters( 'epl_sanitize_amount_decimals', 2, $amount );
	$amount   = number_format( $amount, $decimals, '.', '' );

	return apply_filters( 'epl_sanitize_amount', $amount );
}

/**
 * Returns a nicely formatted amount.
 *
 * @since 1.0
 *
 * @param string $amount   Price amount to format
 * @param string $decimals Whether or not to use decimals.  Useful when set to false for non-currency numbers.
 *
 * @return string $amount Newly formatted amount or Price Not Available
 */
function epl_format_amount( $amount, $decimals = false ) {
	$thousands_sep = epl_get_thousands_separator();
	$decimal_sep = epl_get_decimal_separator();

	// Format the amount
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		$whole = substr( $amount, 0, $sep_found );
		$part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
		$amount = $whole . '.' . $part;
	}

	// Strip , from the amount (if set as the thousands separator)
	if ( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ',', '', $amount );
	}

	if ( empty( $amount ) ) {
		$amount = 0;
	}

	$decimals  = apply_filters( 'epl_format_amount_decimals', $decimals ? 2 : 0, $amount );
	if( is_numeric($amount) )
		$formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

	return apply_filters( 'epl_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );
}

/**
 * Formats the currency display
 *
 * @since 1.0
 * @param string $price Price
 * @return array $currency Currencies displayed correctly
 */
function epl_currency_filter( $price ) {
	$currency = epl_get_currency();
	$currency_position = epl_get_currency_position();

	$position = ( isset( $currency_position ) && !empty( $currency_position ) ) ? $currency_position : 'before';

	$negative = $price < 0;

	if( $negative ) {
		$price = substr( $price, 1 ); // Remove proceeding "-" -
	}

	if ( $position == 'before' ):
		switch ( $currency ):
			case "GBP" :
				$formatted = '&pound;' . $price;
				break;
			case "BRL" :
				$formatted = 'R&#36;' . $price;
				break;
			case "EUR" :
				$formatted = '&euro;' . $price;
				break;
			case "USD" :
			case "AUD" :
			case "NZD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "SGD" :
				$formatted = '&#36;' . $price;
				break;
			case "JPY" :
				$formatted = '&yen;' . $price;
				break;
			case "ILS" :
				$formatted = '&#8362;' . $price;
				break;
			case "THB" :
				$formatted = '&#3647;' . $price;
				break;
			case "INR" :
				$formatted = '&#8377;' . $price;
				break;
			case "TRY" :
				$formatted = '&#8378;' . $price;
				break;
			case "RIAL" :
				$formatted = '&#65020;' . $price;
				break;
			case "UAH" :
				$formatted = '&#8372;' . $price;
				break;
			case "VND" :
				$formatted = '&#8363;' . $price;
				break;
			case "ZAR" :
				$formatted = 'R' . $price;
				break;
			default :
			    $formatted = $currency . ' ' . $price;
				break;
		endswitch;
		$formatted = apply_filters( 'epl_' . strtolower( $currency ) . '_currency_filter_before', $formatted, $currency, $price );
	else :
		switch ( $currency ) :
			case "GBP" :
				$formatted = $price . '&pound;';
				break;
			case "BRL" :
				$formatted = $price . 'R&#36;';
				break;
			case "EUR" :
				$formatted = $price . '&euro;';
				break;
			case "USD" :
			case "AUD" :
			case "NZD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "SGD" :
				$formatted = $price . '&#36;';
				break;
			case "JPY" :
				$formatted = $price . '&yen;';
				break;
			case "ILS" :
				$formatted = $price . '&#8362;';
				break;
			case "THB" :
				$formatted = $price . '&#3647;';
				break;
			case "INR" :
				$formatted = $price . '&#8377;';
				break;
			case "TRY" :
				$formatted = $price . '&#8378;';
				break;
			case "RIAL" :
				$formatted = $price . '&#65020;';
				break;
			case "UAH" :
				$formatted = $price . '&#8372;';
				break;
			case "VND" :
				$formatted = $price . '&#8363;';
				break;
			case "ZAR" :
				$formatted = $price . 'R';
				break;
			default :
			    $formatted = $price . ' ' . $currency;
				break;
		endswitch;
		$formatted = apply_filters( 'epl_' . strtolower( $currency ) . '_currency_filter_after', $formatted, $currency, $price );
	endif;

	if( $negative ) {
		// Prepend the minus sign before the currency sign
		$formatted = '-' . $formatted;
	}

	return $formatted;
}

/**
 * Set the number of decimal places per currency
 *
 * @since 1.0
 * @param int $decimals Number of decimal places
 * @return int $decimals
*/
function epl_currency_decimal_filter( $decimals = 2 ) {
	$currency = epl_get_currency();

	switch ( $currency ) {
		case 'RIAL' :
		case 'JPY' :
		case 'TWD' :

			$decimals = 0;
			break;
	}

	return $decimals;
}
add_filter( 'epl_sanitize_amount_decimals', 'epl_currency_decimal_filter' );
add_filter( 'epl_format_amount_decimals', 'epl_currency_decimal_filter' );


/**
 * Apply formatting to epl_the_excerpt
 *
 * @since 3.1
 * @return int $decimals
*/
function epl_format_the_excerpt( $content ) {
	$content = '<div class="epl-excerpt-content">' . wpautop( $content ) . '</div>';
	return $content;
}
add_filter( 'epl_the_excerpt' , 'epl_format_the_excerpt' );