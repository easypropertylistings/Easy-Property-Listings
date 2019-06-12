<?php
/**
 * Content template for Author card
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="epl-loop-author-box-wrapper">

	<div class="epl-loop-author-image">
		<?php do_action('epl_author_thumbnail',$epl_author); ?>
	</div>
	<div class="epl-loop-author-content">
		<div class="epl-loop-author-title">
			<?php
				$permalink 	= apply_filters('epl_author_profile_link', get_author_posts_url($epl_author->author_id) , $epl_author);

				$author_title =  apply_filters('epl_author_profile_title',get_the_author_meta( 'display_name',$epl_author->author_id ) ,$epl_author );
			?>
			<a href="<?php echo $permalink ?>">
				<?php echo $author_title;  ?>
			</a>
		</div>
	</div>

</div>