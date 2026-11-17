<?php
/**
 * Settings updates for 3.6.1
 *
 * Under offer listings go back to hiding their price. 3.6 showed the price
 * next to the Under Offer label by default, for the NSW underquoting rules.
 * NSW paused those rules on 16 November 2026, the day 3.6 shipped, so 3.6.1
 * switches the setting back to hide once. Sites that want the price can turn
 * it on again in Settings > Labels > Under Offer Price Display, and this
 * won't run again.
 *
 * @package EPL
 * @subpackage updates
 * @since 3.6.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$epl_settings = epl_settings();

if ( isset( $epl_settings['display_price_under_offer'] ) && 'yes' === $epl_settings['display_price_under_offer'] ) {
	$epl_settings['display_price_under_offer'] = 'no';
	update_option( 'epl_settings', $epl_settings );
}
