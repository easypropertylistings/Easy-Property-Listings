<?php
/*
 * Author Card used in Widget
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Tall Container -->
<div class="epl-widget epl-author-card author">
	<div class="entry-content">
		<div class="author-box-tall author-image epl-clearfix">
			<?php if ( 'on' == $d_image ) {
					if ( function_exists('get_avatar') ) { 
						echo get_avatar( get_the_author_meta('email' , $author_id ), '180' );
					}
				} ?>
		</div>
		
		<div class="author-box-tall author-details epl-clearfix">
				<h5 class="author-title"><a href="<?php echo get_author_posts_url( $author_id ); ?>"><? echo $name; ?></a></h5>
				<div class="author-position"><?php echo $position ?></div>
				<div class="author-contact">
					<?php if ( $mobile != '' ) { ?>
						<span class="label-mobile"><?php _e('Mobile', 'epl'); ?> </span><span class="mobile"><?php echo $mobile ?></span>
					<?php } ?>
				</div>		
				<?php if ( $d_icons == 'on' ) { ?>
					<div class="author-social-buttons">
						<?php
							if(isset($i_email)) { echo $i_email; }
							if(isset($i_facebook)) { echo $i_facebook; }
							if(isset($i_twitter)) { echo $i_twitter; }
							if(isset($i_google)) { echo $i_google; }
							if(isset($i_linkedin)) { echo $i_linkedin; }
							if(isset($i_skype)) { echo $i_skype; }
						?>
					</div>
				<?php }
			
				if ( $d_bio == 'on' ) {
					echo '<p>';
						the_author_meta( 'description' , $author_id );
					echo '</p>';
					?> 
						<span class="bio-more"><a href="<?php echo get_author_posts_url( $author_id ); ?>"><?php _e('Read More', 'epl'); ?></a></span>
					<?php 
				}
			?>
		</div>	
	</div>
</div>
