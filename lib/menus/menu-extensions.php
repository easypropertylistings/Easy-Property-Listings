<?php
/**
 * Extensions General Options Menu page
 *
 * @since 2.0
 * @return void
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$epl_extensions = epl_get_new_admin_option_fields();
$active_tab 	= isset($_GET['tab']) ? sanitize_title($_GET['tab']) : current( array_keys($epl_extensions) );	

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
						$_REQUEST[ $field['name'] ] = sanitize_text_field($_REQUEST[ $field['name'] ]);
					}

					$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];
				}
			}
			update_option('epl_settings', $epl_settings);
		}
	}
}

global $epl_settings;
$epl_settings = get_option('epl_settings');

?>
<div class="wrap">
	<h2><?php _e( 'Extensions Settings', 'epl'); ?></h2>
	<p><?php _e( 'Configure your extension settings here. Visit the main settings page for more extension settings.', 'epl'); ?></p>
	<h2 class="nav-tab-wrapper epl-nav-tab-wrapper">
		<?php
			foreach($epl_extensions as $ext_key	=>	$epl_extension){
				$nav_active = $active_tab == $ext_key ? 'nav-tab-active' : ''; ?>
				<a class="nav-tab <?php echo $nav_active; ?>" 
					href="<?php echo admin_url('admin.php?page=epl-extensions&tab='.sanitize_title($ext_key)); ?>">
					<?php _e($epl_extension['label'],'epl'); ?>
				</a><?php
			}
		?>
		
	</h2>
	<?php 
	if(array_key_exists($active_tab, $epl_extensions)):
		$ext_field_groups = $epl_extensions[$active_tab];?>
		<div class="epl-content">
			<form action="" method="post">
				<div class="epl-fields"><?php
				$counter = 1;
				echo '
					<div class="epl-fields-tab-menu">
						<ul>';
							foreach($ext_field_groups['fields'] as $ext_field_group) {
								$current_class = $counter == 1 ? 'epl-fields-menu-current' : '';
								if( !empty($ext_field_group['label']) ) { ?>
									<li class="<?php echo $current_class;?>" data-tab="<?php echo 'tab-menu-'.sanitize_title($ext_field_group['label']); ?>">
										<?php _e($ext_field_group['label'], 'epl'); ?>
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
						if( isset($ext_field_groups['intro']) && !empty($ext_field_groups['intro']) ) {
							echo '<div class="epl-field epl-field-intro">';
							_e($ext_field_groups['intro'],'epl');
							echo '</div>';
						}

					$counter = 1;
					foreach($ext_field_groups['fields'] as $field_group) {
						$current_class = $counter == 1? 'epl-fields-field-current':''; ?>
			
						
						<div class="<?php echo $current_class; ?> epl-fields-single-menu" id="<?php echo 'tab-menu-'.sanitize_title($field_group['label']); ?>"><?php

						foreach($field_group['fields'] as $field) {?>
							<div class="epl-field">
								<div class="epl-help-entry-header">
									<div class="epl_help_entry_content<?php //echo $field['name']; ?>"><?php //_e($field['help'], 'epl'); ?></div>
								</div>
								<div class="epl-half-left">
									<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'epl'); ?></label>
									
									<?php if(isset($field['help'])) {
											$field['help'] = trim($field['help']);
											if(!empty($field['help'])) {
												echo '<p class="epl-help-text">'.__($field['help'], 'epl').'</p>';
											}
									} ?>
								</div>
								<div class="epl-half-right">
									<?php
										$val = '';
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
					<p class="submit">
						<input type="submit" value="<?php _e('Save Changes', 'epl'); ?>" class="button button-primary" id="submit" name="submit">
					</p>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div><?php

function epl_get_new_admin_option_fields() {
	$fields = array( );
	$fields = apply_filters('epl_extensions_options_filter_new', $fields);
	return $fields;
}
