<?php
/**
 * Listing Elements GUI
 *
 * @package     EPL
 * @subpackage  Admin/Elements
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the listing elements button to the MCI media buttons
 *
 * @since 3.3
 */
function epl_add_elements_gui_button() {

	echo '<style>
	.epl_am_elements_gui_icon:before {
		font: 400 18px/1 dashicons;
		content: "\f478";
	vertical-align: middle;
	}
	</style>
	<a href="#TB_inline?width=480&height=700&inlineId=epl_select_gui_opts"
  		class="thickbox button epl_listing_elements_link"
  		id="epl_add_elements_shortcode"
  		title="' . esc_html__( 'Add Listing Elements', 'easy-property-listings' ) . '">
	    <span class="epl_am_elements_gui_icon"></span> ' . esc_html__( 'Add Listing Elements', 'easy-property-listings' ) . '</a>';
}
add_action( 'media_buttons', 'epl_add_elements_gui_button', 20 );

/**
 * Action target that displays the popup to insert a map to a post
 *
 * @since 3.3
 */
function epl_listing_elements_gui() {
	?>
	<style>
		#TB_window {
			overflow:auto;
		}
		#TB_ajaxContent .wrap select, #TB_ajaxContent .wrap input {
			width: 200px;
			margin: 7px 25px 10px 0;
		}
		#TB_ajaxContent .wrap span {
			width: 100%;
			display: block;
		}
	</style>

	<script>
		function eplInsertElementsShortcode(){

			var shortcode = "[listing_element ";

			var type = jQuery('#epl_elements_shortcode_types').val();

			if(type != '') {
				shortcode += "type='"+type+"' ";
			}

			var val = jQuery('#epl_element_gui_val').val();

			if(val != '') {

				switch( type ) {

					case 'action' :
						shortcode += "action_key='"+val+"' ";
					break;

					case 'meta' :
						shortcode += "meta_key='"+val+"' ";
					break;

					case 'post' :
						shortcode += "post_key='"+val+"' ";
					break;
				}


			}

			shortcode += "]";
			window.send_to_editor(shortcode);
		}
	</script>

	<div id="epl_select_gui_opts" style="display:none;">
		<div class="wrap">

			<div class="epl-popover-element epl-clearfix">
				<h3 class="epl-popover-title">
					<?php esc_html_e( 'Insert Shortcode', 'easy-property-listings' ); ?>
				</h3>
			</div>

			<div class="epl-popover-element epl-clearfix">

				<h4 class="epl-popover-title">
					<?php esc_html_e( 'Select Shortcode Type', 'easy-property-listings' ); ?>
				</h4>

				<select id="epl_elements_shortcode_types">
					<option value=""><?php esc_html_e( 'type', 'easy-property-listings' ); ?></option>
					<?php
					$types = array( 'action', 'meta', 'post', 'suburb_profile', 'formatted_address', 'excerpt' );
					foreach ( $types as $type ) {
						?>
						<option value="<?php echo esc_html( $type ); ?>"><?php echo esc_html( $type ); ?></option>
						<?php
					}
					?>
				</select>
			</div>

			<div class="epl-popover-element epl-clearfix">

				<label class="epl-label" style="display: block;" for="epl_element_gui_val">
					<?php esc_html_e( 'Value', 'easy-property-listings' ); ?>
				</label>

				<input type="text" id="epl_element_gui_val" />

				<div class="epl-gui-help"><span><?php esc_html_e( 'Required for Types : action, meta, post', 'easy-property-listings' ); ?></span></div>
			</div>

			<div class="epl-popover-element epl-clearfix">
				<input type="button" class="button-primary" value="<?php esc_html_e( 'Insert', 'easy-property-listings' ); ?>" onclick="eplInsertElementsShortcode();"/>&nbsp;&nbsp;&nbsp;
				<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php esc_html_e( 'Cancel', 'easy-property-listings' ); ?></a>
			</div>

		</div>

		<div class="epl-popover-element epl-shortcode-gui-help epl-clearfix">
			<h4><?php esc_html_e( 'How to use this shortcode', 'easy-property-listings' ); ?></h4>
			<div class="epl-popover-table">
				<table class="widefat striped">
					<thead>
						<tr>
							<th>
								<strong>
									<?php esc_html_e( 'Actions', 'easy-property-listings' ); ?>
								</strong>

							</th>
							<th>
								<strong>
									<?php esc_html_e( 'Meta Fields', 'easy-property-listings' ); ?>
								</strong>

							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								epl_property_price
							</td>
							<td>
								property_price
							</td>
						</tr>
						<tr>
							<td>
								epl_property_heading
							</td>
							<td>
								property_heading
							</td>
						</tr>
						<tr>
							<td>
								epl_property_icons
							</td>
							<td>
								property_bedrooms
							</td>
						</tr>
						<tr>
							<td>
								epl_property_map
							</td>
							<td>
								property_bathrooms
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<p>
				<?php esc_html_e( 'For complete action reference visit', 'easy-property-listings' ); ?>
				<a target="_blank" href="http://docs.easypropertylistings.com.au/hook-docs.html">
					<?php esc_html_e( 'Here', 'easy-property-listings' ); ?>.
				</a>
			</p>
		</div>
	</div>

	<?php
}
add_action( 'admin_footer', 'epl_listing_elements_gui' );
