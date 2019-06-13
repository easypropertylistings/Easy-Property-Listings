<?php
/**
 * Author Box: Details Tab
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Author Box Container Tabbed Content -->
<div class="epl-author-contact-details author-contact-details">

	<h5 class="epl-author-title author-title">
		<a href="<?php echo $permalink ?>">
			<?php echo $author_title;  ?>
		</a>
	</h5>

	<div class="epl-author-position author-position">
		<span class="label-position"></span>
		<span class="position"><?php echo $epl_author->get_author_position() ?></span>
	</div>

	<div class="epl-author-contact author-contact">
		<span class="label-mobile"></span>
		<span class="mobile"><?php echo $epl_author->get_author_mobile() ?></span>
	</div>

	<div class="epl-author-contact author-contact author-contact-office-phone">
		<span class="label-office-phone"></span>
		<span class="office-phone"><?php echo $epl_author->get_author_office_phone() ?></span>
	</div>
</div>
<div class="epl-author-slogan author-slogan"><?php echo $epl_author->get_author_slogan() ?></div>
<div class="epl-clearfix"></div>
<div class="epl-author-social-buttons author-social-buttons">
	<?php
		$social_icons = apply_filters('epl_display_author_social_icons',array('email','facebook','twitter','instagram','pinterest','linkedin','skype','youtube'));
		foreach($social_icons as $social_icon){
			echo call_user_func(array($epl_author,'get_'.$social_icon.'_html'));
		}
	?>
</div>

