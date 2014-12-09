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
						echo get_avatar( $epl_author->email , '180' );
					}
				} ?>
		</div>
		
		<div class="author-box-tall author-details epl-clearfix">
				<h5 class="author-title">
					<a href="<?php echo get_author_posts_url( $epl_author->author_id ); ?>">
						<?php the_author_meta( 'display_name',$epl_author->author_id ); ?>
					</a>
				</h5>
				<div class="author-position"><?php echo $epl_author->get_author_position() ?></div>
				<div class="author-contact">
					<?php if ( $epl_author->get_author_mobile() != '' ) { ?>
						<span class="label-mobile"><?php _e('Mobile', 'epl'); ?> </span>
						<span class="mobile"><?php echo $epl_author->get_author_mobile() ?></span>
					<?php } ?>
				</div>		
				<?php if ( $d_icons == 'on' ) { ?>
					<div class="author-social-buttons">
						<?php
							$social_icons = apply_filters('epl_display_author_social_icons',array('email','facebook','twitter','google','linkedin','skype'));
							foreach($social_icons as $social_icon){
								echo call_user_func(array($epl_author,'get_'.$social_icon.'_html')); 
							}
						?>
					</div>
				<?php }
			
				if ( $d_bio == 'on' ) {
					$epl_author->get_description_html(); 
				}
			?>
		</div>	
	</div>
</div>
