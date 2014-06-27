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

<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
