<?php
/**
 * SHORTCODE :: Listing Search [listing_search]
 *
 * @package     EPL
 * @subpackage  Shotrcode/listing_search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Only load on front
if( is_admin() ) {
	return; 
}
/**
 * This shortcode allows for you to specify the property type(s) using 
 * [listing_search title="" post_type="property" property_status="current/sold/leased" search_house_category="on/off" search_price="on/off" search_bed="on/off" search_bath="on/off" search_car="on/off" search_other="on/off"] option
 */
function epl_shortcode_listing_search_callback( $atts ) {

	global $wpdb;
	$atts = shortcode_atts( epl_search_get_defaults(), $atts);
	extract($atts);
	$selected_post_types = $atts['post_type'];
	extract( $_GET );
	$queried_post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	
	if(!is_array($selected_post_types)){
		$selected_post_types = explode(",", $selected_post_types);
		$selected_post_types = array_map('trim', $selected_post_types);
	}
	
	global $epl_settings;
	ob_start();	
	$tabcounter = 1;
	if(!empty($selected_post_types)):
		if(count($selected_post_types) > 1):
			echo "<ul class='epl-search-tabs property_search-tabs epl-search-$style'>";
			foreach($selected_post_types as $post_type):
	
				if( isset($_GET['action'] ) && $_GET['action'] == 'epl_search' ) {
			 
					if( $queried_post_type ==  $post_type ) {
						$is_sb_current = 'epl-sb-current';
					} else {
						$is_sb_current = '';
					}	
				} else {
					$is_sb_current = $tabcounter == 1 ? 'epl-sb-current' : '';
				}
				$post_type_label = isset($epl_settings['widget_label_'.$post_type])?$epl_settings['widget_label_'.$post_type]:$post_type;
				echo '<li data-tab="epl_ps_tab_'.$tabcounter.'" class="tab-link '.$is_sb_current.'">'.$post_type_label.'</li>';
				$tabcounter++;
				
			endforeach;
			echo '</ul>';
		endif;

	?>
	<div class="epl-search-forms-wrapper epl-search-<?php echo $style; ?>">
		<?php
			$tabcounter = 1; // reset tab counter

			foreach($selected_post_types as $post_type):
			
			if( isset($_GET['action'] ) && $_GET['action'] == 'epl_search' ) {
			 
				if( $queried_post_type ==  $post_type ) {
					$is_sb_current = 'epl-sb-current';
				} else {
					$is_sb_current = '';
				}	
			} else {
				$is_sb_current = $tabcounter == 1 ? 'epl-sb-current' : '';
			}
		?>
		</ul>
		<div class="epl-search-form <?php echo $is_sb_current; ?>" id="epl_ps_tab_<?php echo $tabcounter; ?>">
			<?php
				if( isset($show_title) && $show_title == 'true') {
					if(!empty($title)) {
						?><h3><?php echo $title; ?></h3><?php
					}
				}
			?>
			<form method="get" action="<?php echo esc_url( home_url('/') ); ?>">
				<input type="hidden" name="action" value="epl_search" />
				<?php
					$epl_frontend_fields = epl_search_widget_fields_frontend($post_type,$property_status);

					foreach($epl_frontend_fields as $epl_frontend_field) {
						
						$config	=	isset(${$epl_frontend_field['key']}) ? ${$epl_frontend_field['key']} : '';
						$value	=	isset(${$epl_frontend_field['meta_key']}) ? ${$epl_frontend_field['meta_key']} : '';
						epl_widget_render_frontend_fields($epl_frontend_field,$config,$value,$post_type,$property_status);
					}
				?>
		
				<div class="epl-search-submit-row epl-search-submit property-type-search">
					<input type="submit" value="<?php echo $submit_label != '' ? $submit_label : __('Find me a Property!', 'epl'); ?>" class="search-btn" />
				</div>
			</form>
		</div>
		<?php $tabcounter++; endforeach; endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'listing_search', 'epl_shortcode_listing_search_callback' );


