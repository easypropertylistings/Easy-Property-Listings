<?php
/**
 * Settings Menu
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$fields = epl_get_admin_option_fields();
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($fields)) {
		foreach($fields as &$field_group) {
			$field_group['fields'] = array_filter($field_group['fields']);
			foreach($field_group['fields'] as $field) {

				if($field['type'] == 'help')
					continue;

				if( $field['type'] == 'radio' || $field['type'] == 'checkbox' || $field['type'] == 'checkbox_single') {
					if(!isset($_REQUEST[ $field['name'] ])) {

						$_REQUEST[ $field['name'] ] = '';
					}

				}

				if($field['type'] == 'text') {
					if (isset($_REQUEST[ $field['name'] ]))
						$_REQUEST[ $field['name'] ] = sanitize_text_field($_REQUEST[ $field['name'] ]);
				}
				$epl_settings = get_option('epl_settings');

				if( isset($field['default']) && $field['type'] != 'checkbox_single' ) {
					if( !isset($_REQUEST[ $field['name'] ]) || $_REQUEST[ $field['name'] ] == '' ) {
						$_REQUEST[ $field['name'] ] = $field['default'];
					}
				}
				$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];
				update_option('epl_settings', $epl_settings);
			}
		}
	}
	update_option('epl_rewrite_rules', false);
}

global $epl_settings;

//get the latest saved settings
$epl_settings = get_option('epl_settings');
?>

<div class="wrap">
	<h2><?php _e('General Settings', 'easy-property-listings' ); ?></h2>
	<p><?php _e('Configure Easy Property Listings. Visit ', 'easy-property-listings' ); ?><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php _e( 'Getting Started', 'easy-property-listings'  ); ?></a><?php _e(' for help.', 'easy-property-listings' );?></p>

	<div id="epl-menu-general" class="epl-content epl-menu-content-wrapper">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page  metabox-holder">
				<?php if(!empty($fields)) { ?>
						<div id="meta-sortables" class="meta-box-sortables ui-sortable epl-menu-content">
							<?php foreach($fields as &$field_group) {
								$field_id 	= isset($field_group['id']) ? $field_group['id'] : 'extension';
								$field_class 	= isset($field_group['class']) ? $field_group['class'] : 'extension';
								?>

								<div id="epl-<?php echo $field_id; ?>" class="postbox epl-menu-section epl-<?php echo $field_class; ?>">
									<?php
									if( !empty($field_group['label']) ) { ?>
										<!--<div class="handlediv" title="Click to toggle"><br></div>-->
										<h3 class="hndle ui-sortable-handle epl-section-title"><?php _e($field_group['label'], 'easy-property-listings' ); ?></h3>
									<?php
									} ?>

									<div class="inside epl-section-content">
										<?php if( !empty($field_group['label']) ) { ?>
											<p><?php if(isset($field_group['help'])) _e($field_group['help'], 'easy-property-listings' ); ?></p>
										<?php
										} ?>
										<?php
										$field_group['fields'] = array_filter($field_group['fields']);
										foreach($field_group['fields'] as $field) { ?>
											<div class="epl-field-wrapper epl-clearfix">
												<div class="epl-field">
													<?php if($field['type'] != 'help') {?>
													<div class="epl-half-left">
														<h4 id="epl-field-<?php echo $field['name']; ?>" class="epl-setting-heading"><?php _e($field['label'], 'easy-property-listings' ); ?></h4>

													</div>
													<?php } ?>
													<div class="<?php echo $field['type'] == 'help' ? 'epl-full': 'epl-half-right'; ?>">
														<?php
															$val = '';
															if(isset($epl_settings[ $field['name'] ])) {
																$val = $epl_settings[ $field['name'] ];
															} else{
																if(isset($field['default'])) {
																	$val = $field['default'];
																}
															}
															epl_render_html_fields ($field,$val);
														?>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php }
				?>
			</div>

			<div class="epl-content-footer submit">
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>

	<div class="epl-content-sidebar-wrapper">
		<div class="epl-content-sidebar ">
			<?php epl_admin_sidebar (); ?>
		</div>
	</div>
</div>