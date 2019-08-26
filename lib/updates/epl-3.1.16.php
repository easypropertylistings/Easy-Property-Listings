<?php
/**
 * Settings updates for 3.1.16
 *
 * @package EPL
 * @subpackage updates
 * @since 3.1.16
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notice_display = get_transient( 'epl_admin_notices_display' );

if ( ! $notice_display ) {
	set_transient( 'epl_admin_notices_display', true, 60 * 60 * 24 * 14 );
}
