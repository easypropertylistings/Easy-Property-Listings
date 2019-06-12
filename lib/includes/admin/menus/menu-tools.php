<?php
/**
 * Menu Tools
 *
 * @package     EPL
 * @subpackage  Admin/Menu-Tools
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap epl-wrap">
	<h2><?php _e('Tools','easy-property-listings'); ?></h2>
	<p><?php _e('Visit the main settings page for Easy Property Listings Settings','easy-property-listings');?></p>

	<div class="epl-content">

		<div class="epl-tabs">
			<?php
			$tabs       = epl_get_tools_tab();
			$current    = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'tools'; // default is import

			echo '<h1 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $tab_options ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=epl-tools&tab=$tab'>{$tab_options['label']}</a>";
			}
			echo '</h1>';
			?>
		</div>

		<div class="epl-tool-msgs">
			<?php do_action('epl_import_status'); ?>
		</div>

		<div class="epl-tabs-content">
			<form class="epl-tools-form" method="post" enctype="multipart/form-data">
				<?php
				if( isset($tabs[$current]) )
					call_user_func($tabs[$current]['callback']);
				?>
			</form>
		</div>

	</div>

	<div class="epl-footer"></div>
</div>