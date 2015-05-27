<?php
/*
 * Widget Property Template: List
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<li id="post-<?php the_ID(); ?>" class="epl-widget-list-item">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</li>
