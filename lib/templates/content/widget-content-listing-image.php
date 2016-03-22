<?php
/*
 * Widget Property Template: Image Only
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

<div id="post-<?php the_ID(); ?>" class="epl-listing-widget epl-property-widget-image-only <?php do_action('epl_property_widget_status_class'); ?> epl-clearfix">
	<div class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="epl-img-widget">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
