<?php
/*
 * Widget Property Template: Image Only
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" class="epl-listing-widget property-widget-image epl-clearfix">
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
