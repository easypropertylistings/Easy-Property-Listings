<?php
/**
 * Author Box: Simple Card
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Container -->
<div class="epl-author-card epl-author">
	<div class="entry-content">
		<div class="epl-author-box epl-author-image">
			<!-- Featured Image -->
			<?php
				if ( has_post_thumbnail() ) { 
					the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) );
				} elseif (function_exists('get_avatar')) {
					echo get_avatar( $epl_author->email , '180' );
				}
			?>
		</div>
		
	<div class="epl-author-box epl-author-details">
		<?php apply_filters('epl_author_tab_about_callback',epl_author_tab_author_id($epl_author))	?>	
	</div>
</div>
<!-- END Author Box Container -->
