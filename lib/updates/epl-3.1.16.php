<?php
$notice_display = get_transient( 'epl_admin_notices_display' );

if( !$notice_display ) {
	set_transient( 'epl_admin_notices_display', true, 60*60*24*14 );
}
