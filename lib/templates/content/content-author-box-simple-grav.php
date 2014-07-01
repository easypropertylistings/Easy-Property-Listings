<?php
/*
 * Author Box: Simple Card Gravatar Image
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Container -->
<div class="epl-author-card author">
	<div class="entry-content">
		<div class="author-box author-image">
			<?php
				if (function_exists('get_avatar')) { 
					echo get_avatar( get_the_author_meta('email') , '180' );
				}
			?>
		</div>
		
		<div class="author-box author-details">	
			<!--- Author Page Style --->
			<?php if ( $author_style == 1) { ?>
				<h5 class="author-title"><a href="<?php the_permalink(); ?>"><? the_title(); ?></a></h5>
			<?php } else { ?>
				<h5 class="author-title"><?php the_author_posts_link(); ?></h5>
			<?php } ?>
			
			<div class="author-position"><?php echo $position ?></div>
			<div class="author-contact">
				<?php if ( $mobile != '' ) { ?>
					<span class="label-mobile"><?php _e('Mobile', 'epl'); ?> </span><span class="mobile"><?php echo $mobile ?></span>
				<?php } ?>
			</div>
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
	</div>
</div>
<!-- END Author Box Container -->
