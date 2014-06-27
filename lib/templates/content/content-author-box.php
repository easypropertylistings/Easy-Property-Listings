<?php
/*
 * Author Box: Advanced Style
 *
 * @package EPL
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Container Tabbed -->
<div id="epl-box" class="epl-author-box">		
	<ul class="author-tabs">
				<li class="tab-link author-current" data-tab="tab-1"><?php _e('About', 'epl'); ?></li>
		<?php
			if ( get_the_author_meta('description') != '' ) { ?>
				<li class="tab-link" data-tab="tab-2"><?php _e('Bio', 'epl'); ?></li>
			<?php }
			
			if ( $video != '' ) { ?>
				<li class="tab-link" data-tab="tab-3"><?php _e('Video', 'epl'); ?></li>
			<?php }
			
			if ( $contact_form != '') { ?>
				<li class="tab-link" data-tab="tab-6"><?php _e('Contact', 'epl'); ?></li>
			<?php }
		?>			
	</ul>

	<div class="author-box-outer-wrapper epl-clearfix">			
		<div class="author-box author-image">
			<?php
		
				if ( function_exists('get_avatar') ) {
					echo get_avatar( get_the_author_meta('email') , '150' );
				}
			?>
		</div>
		
		<div id="tab-1" class="author-box author-details author-tab-content author-current">
			<div class="author-contact-details">
			
				<!--- Author Page Style --->
				<h5 class="author-title"><?php the_author_posts_link(); ?></h5>
			
				<div class="author-position"><?php echo $position ?></div>
				<div class="author-contact">
					<span class="label-mobile"></span><span class="mobile"><?php echo $mobile ?></span>
				</div>
				<!--<div class="author-contact">
					<span class="label-email"></span><a href="mailto:<?php //the_author_email(); ?>"><span class="email"><?php //the_author_email(); ?></span></a>
				</div>-->
			</div>
			
			<?php if ( $slogan != '' ) { ?>
				<div class="author-slogan"><?php echo $slogan ?></div>
			<?php } ?>
			
			<div class="epl-clearfix"></div>
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
		</div>

		<?php
			if ( get_the_author_meta('description') != '' ) { ?>
				<div id="tab-2" class="author-box author-bio author-tab-content">
					<div class="author-content"><?php the_author_meta('description'); ?></div>
						<span class="bio-more"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php _e('Read More', 'epl'); ?></a></span>
				</div>
				<?php
			}
		
			if ( $video != '' ) { ?>
				<div id="tab-3" class="author-box author-description author-tab-content">
					<div class="author-video"><?php echo $e_video ?></div>
				</div>
				<?php
			}
		
			wp_reset_query(); 
		?>

		<div id="tab-5" class="author-box author-social author-tab-content">
			<h6 class="author-box-title"><?php _e('Social', 'epl'); ?></h6>
		</div>
		<div id="tab-6" class="author-box author-contact-form author-tab-content">
			<h6 class="author-box-title"><?php _e('Contact', 'epl'); ?></h6>
				<?php
				
					echo do_shortcode($contact_form);
					
					//if ( function_exists('gravity_form') && $contact_form != '') {
					//	gravity_form($contact_form, false, false, false, '', false); 
					//}
				?>
		</div>			
	</div>
</div>
<!-- END Author Box Container -->
