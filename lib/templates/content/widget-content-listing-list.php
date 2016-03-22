<?php
/*
 * Widget Property Template: List
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

<li id="post-<?php the_ID(); ?>" class="epl-widget-list-item <?php do_action('epl_property_widget_status_class'); ?>">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</li>
