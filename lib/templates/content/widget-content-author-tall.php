<?php
/**
 * Author Card used in Widget
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Tall Container -->
<div class="epl-widget epl-author-card epl-author author-card author">
	<div class="entry-content">
		<?php do_action('epl_author_widget_before_image'); ?>
		<div class="epl-author-box-tall epl-author-image author-box-tall author-image epl-clearfix">
			<?php if ( 'on' == $d_image ) {
					do_action('epl_author_thumbnail',$epl_author);
				} ?>
		</div>
		<?php do_action('epl_author_widget_after_image'); ?>

		<?php do_action('epl_author_widget_before_content'); ?>
		<div class="epl-author-box-tall epl-author-details author-box-tall author-details epl-clearfix"> <?php
			$permalink 	= apply_filters('epl_author_profile_link', get_author_posts_url($epl_author->author_id) , $epl_author);
			$author_title	= apply_filters('epl_author_profile_title',get_the_author_meta( 'display_name',$epl_author->author_id ) ,$epl_author ); ?>

			<?php do_action('epl_author_widget_before_title'); ?>
			<h5 class="epl-author-title author-title"><a href="<?php echo $permalink; ?>"><?php echo $author_title; ?></a></h5>
			<?php do_action('epl_author_widget_after_title'); ?>

			<div class="epl-author-position author-position"><?php echo $epl_author->get_author_position() ?></div>

			<?php do_action('epl_author_widget_before_contact'); ?>
			<div class="epl-author-contact author-contact">
				<?php if ( $epl_author->get_author_mobile() != '' ) { ?>
					<span class="label-mobile"><?php _e('Mobile', 'easy-property-listings' ); ?> </span>
					<span class="mobile"><?php echo $epl_author->get_author_mobile() ?></span>
				<?php } ?>
			</div>
			<?php do_action('epl_author_widget_after_contact'); ?>

			<?php do_action('epl_author_widget_before_icons'); ?>
			<?php if ( $d_icons == 'on' ) { ?>
				<div class="epl-author-social-buttons author-social-buttons">
					<?php
						$social_icons = apply_filters('epl_display_author_social_icons',array('email','facebook','twitter','google','linkedin','skype'));
						foreach($social_icons as $social_icon){
							echo call_user_func(array($epl_author,'get_'.$social_icon.'_html'));
						}
					?>
				</div>
			<?php } ?>
			<?php do_action('epl_author_widget_after_icons'); ?>

			<?php do_action('epl_author_widget_before_bio'); ?>
			<?php if ( $d_bio == 'on' ) {
				echo $epl_author->get_description_html();
			} ?>
			<?php do_action('epl_author_widget_after_bio'); ?>
		</div>
		<?php do_action('epl_author_widget_after_content'); ?>
	</div>
</div>
