<?php
/**
 * Author Box: Simple Card Gravatar Image
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Container -->
<div class="epl-author-card epl-author author">
	<div class="entry-content">
		<div class="epl-author-box epl-author-image author-box author-image">
			<?php
				if (function_exists('get_avatar')) { 
					echo get_avatar( $epl_author->email , '180' );
				}
			?>
		</div>
		
		<div class="epl-author-box epl-author-details author-box author-details">	
			<?php apply_filters('epl_author_tab_about_callback',epl_author_tab_author_id($epl_author)); ?>	
		</div>
	</div>
</div>
<!-- END Author Box Container -->
