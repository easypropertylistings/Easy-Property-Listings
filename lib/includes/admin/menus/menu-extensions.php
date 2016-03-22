<?php
/**
 * Extension Settings
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$epl_extensions = epl_get_new_admin_option_fields();
$active_tab 	= isset($_GET['tab']) ? sanitize_title($_GET['tab']) : current( array_keys($epl_extensions) );
$active_sub_tab 	= isset($_GET['sub_tab']) ? sanitize_title($_GET['sub_tab']) : '';

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($epl_extensions)) {
		if(array_key_exists($active_tab, $epl_extensions)) {
			$epl_settings = get_option('epl_settings');
			$ext_field_groups = $epl_extensions[$active_tab];
			foreach($ext_field_groups['fields'] as $ext_field_group) {
				foreach($ext_field_group['fields'] as $field) {
					if( $field['type'] == 'radio' || $field['type'] == 'checkbox' ) {
						if(!isset($_REQUEST[ $field['name'] ])) {
							$_REQUEST[ $field['name'] ] = '';
						}
					}

					if($field['type'] == 'text') {

						if( isset($_REQUEST[ $field['name'] ]) && is_array($_REQUEST[ $field['name'] ]) ) {
							array_walk_recursive($_REQUEST[ $field['name'] ], 'sanitize_text_field');
						}

						if( isset($_REQUEST[ $field['name'] ])  && is_string($_REQUEST[ $field['name'] ]) ) {
							$_REQUEST[ $field['name'] ] = sanitize_text_field($_REQUEST[ $field['name'] ]);
						}

					}

					if( isset($_REQUEST[ $field['name'] ]) ) {
						$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];

						// remove fields after adding them to epl_settings
						unset($_REQUEST[ $field['name'] ]);
					}
				}
			}

			/**
			* adding some way for dynamic fields to be added to epl_settings array
			*/

			/** settings page params **/
			$page_params = apply_filters('epl_settings_page_params',array('tab','page','sub_tab','action','submit') );

			/** remove all settings page params from $_REQUEST **/
			foreach($page_params as $page_param) {
				if( isset($_REQUEST[$page_param]) ) {
					unset($_REQUEST[$page_param]);
				}
			}

			/** remain fields are probably dynamic fields added via javascript/jquery
			 *  save them as well if they are not already in $epl_settings
			 */
			 foreach($_REQUEST as $dynamic_key	=>	$dynamic_value) {
			 	if($dynamic_value != '')
			 		$epl_settings[ $dynamic_key ] = $dynamic_value;
			 }
			update_option('epl_settings', $epl_settings);
		}
	}
}

global $epl_settings;
$epl_settings = get_option('epl_settings');

?>
<div class="wrap">
	<h2><?php _e( 'Extensions Settings', 'easy-property-listings' ); ?></h2>
	<p><?php _e( 'Configure your extension settings here. Visit the main settings page for more extension settings.', 'easy-property-listings' ); ?></p>
	<h2 class="nav-tab-wrapper epl-nav-tab-wrapper">
		<?php
			foreach($epl_extensions as $ext_key	=>	$epl_extension){
				$nav_active = $active_tab == $ext_key ? 'nav-tab-active' : ''; ?>
				<a class="nav-tab <?php echo $nav_active; ?>"
					href="<?php echo admin_url('admin.php?page=epl-extensions&tab='.sanitize_title($ext_key)); ?>">
					<?php _e($epl_extension['label'],'easy-property-listings' ); ?>
				</a><?php
			}
		?>

	</h2>
	<?php
	/** re get fields so that we can get updated fields if added via hook **/
	$epl_extensions = epl_get_new_admin_option_fields();
	if(array_key_exists($active_tab, $epl_extensions)):
		$ext_field_groups = $epl_extensions[$active_tab];?>
		<div class="epl-content">
			<?php
			echo '
				<div class="epl-fields-help-content">';
						foreach($ext_field_groups['fields'] as $ext_field_group) {

							if( isset($ext_field_group['intro']) && !empty($ext_field_group['intro']) ) {
								echo '<div class="epl-field epl-field-intro" data-help="tab-menu-'.sanitize_title($ext_field_group['label']).'">';
								_e($ext_field_group['intro'],'easy-property-listings' );
								echo '</div>';
							}

						}
						echo '
				</div>
			';

			?>

			<form action="" method="post" id="epl_extension_settings_form">
				<div class="epl-fields"><?php
				$counter = 1;
				echo '
					<div class="epl-fields-tab-menu">
						<ul>';
							foreach($ext_field_groups['fields'] as $ext_field_group) {

								$current_class = $active_sub_tab == 'tab-menu-'.sanitize_title($ext_field_group['label']) ? 'epl-fields-menu-current' : '';
								if($active_sub_tab == '' && $counter == 1) {
									$current_class = 'epl-fields-menu-current';
								}
								if( !empty($ext_field_group['label']) ) { ?>
									<li class="<?php echo $current_class;?>" data-tab="<?php echo 'tab-menu-'.sanitize_title($ext_field_group['label']); ?>">
										<?php _e($ext_field_group['label'], 'easy-property-listings' ); ?>
									</li>
									<?php
								}
								$counter++;
							}
							echo '
						</ul>
					</div>
				';

				if(!empty($ext_field_groups['fields'])) {

					echo '<div class="epl-fields-tab-content">';
					$counter = 1;
					foreach($ext_field_groups['fields'] as $field_group) {
						$current_class = $active_sub_tab == 'tab-menu-'.sanitize_title($field_group['label']) ? 'epl-fields-field-current' : '';
						if($active_sub_tab == '' && $counter == 1) {
							$current_class = 'epl-fields-field-current';
						} ?>


						<div class="<?php echo $current_class; ?> epl-fields-single-menu" id="<?php echo 'tab-menu-'.sanitize_title($field_group['label']); ?>"><?php

						foreach($field_group['fields'] as $field) {?>
							<div class="epl-field">
								<div class="epl-help-entry-header">
									<div class="epl_help_entry_content<?php //echo $field['name']; ?>"><?php //_e($field['help'], 'easy-property-listings' ); ?></div>
								</div>
								<?php if($field['type'] != 'help') {?>
									<div class="epl-half-left">
										<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'easy-property-listings' ); ?></label>

									</div>
								<?php } ?>
								<div class="<?php echo $field['type'] == 'help' ? 'epl-full': 'epl-half-right'; ?>">
									<?php
										$val = isset($field['default']) ? $field['default'] : '' ;
										if(isset($epl_settings[ $field['name'] ])) {
											$val = $epl_settings[ $field['name'] ];
										}

										epl_render_html_fields ($field,$val);
									?>
								</div>
							</div>
						<?php }
						echo '</div>';
						$counter++;
					}
					echo '</div>';
				} ?>
				<div class="epl-clear"></div>
				<div class="epl-content-footer">
					<input type="hidden" name="action" value="epl_settings" />
					<input type="hidden" name="sub_tab" id="sub_tab" value="" />
					<p class="submit">
						<input type="submit" value="<?php _e('Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit">
					</p>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div><?php

/**
 * Get Extension Settings
 *
 * @since 2.0
 */
function epl_get_new_admin_option_fields() {
	$fields = array( );
	$fields = apply_filters('epl_extensions_options_filter_new', $fields);
	return $fields;
}
