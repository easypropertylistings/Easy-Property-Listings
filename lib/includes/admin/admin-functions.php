<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Admin/Functions
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'cal_days_in_month' ) ) {
	/**
	 * Fallback in case the calendar extension is not loaded in PHP
	 *
	 * @since 3.3.3
	 */
	function cal_days_in_month( $calendar, $month, $year ) {
		return date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
	}
}

/**
 * Flush Rewrite Rules
 *
 * @since 1.0
 */
function epl_flush_rewrite_rules() {
	$epl_rewrite_rules = get_option('epl_rewrite_rules');
	if(!$epl_rewrite_rules) {
		flush_rewrite_rules();
		update_option('epl_rewrite_rules', true);
	}
}
add_action('admin_init', 'epl_flush_rewrite_rules');
add_action('init', 'epl_flush_rewrite_rules');

/**
 * Notice on Dashboard Widget
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_rough() {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	echo '<div class="epl-internal-ad">';
		echo '<div>' . __('Remove these ads?' , 'easy-property-listings') . '</div>';

		echo '<div><a href="https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_store">' . __('Purchase an extension remove me' , 'easy-property-listings') . '</a></div>';
	echo '</div>';
}

/**
 * Notice presented on settings page, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_admin_sidebar () {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/core-bundle/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_core',
			'img' => 'banner-dash-wide.png',
			'alt' => __('Core bundle for Easy Property Listings', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/advanced-mapping/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_advanced_map',
			'img' => 'offer-advanced-mapping.png',
			'alt' => __('Advanced Mapping', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/sliders/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_sliders',
			'img' => 'offer-sliders.png',
			'alt' => __('Sliders extension', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/staff-directory/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_staff_directory',
			'img' => 'offer-staff-directory.png',
			'alt' => __('Staff Directory', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/listing-templates/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_listing_templates',
			'img' => 'offer-templates.png',
			'alt' => __('Listing Templates', 'easy-property-listings' ),
			'width'	=> 261,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/></a><br/><br/>';
		$i ++;
	}
	?>
	<div class="epl-admin-offer" style="margin-bottom: 1em;">
		<div><span><?php _e('Remove these ads?', 'easy-property-listings' ); ?><span></div>
		<div><a href="http://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_store"><?php _e('Purchase and activate any extension', 'easy-property-listings' ); ?> &#x27F6;</a></div>
	</div>
<?php
}

/**
 * Notice presented on dashboard widget, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' 	=> 'https://easypropertylistings.com.au/extensions/core-bundle/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_core',
			'img' 	=> 'banner-dash-wide.png',
			'alt' 	=> __('Check out the premium extensions', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_store',
			'img' => 'banner-dash-wide-store.png',
			'alt' => __('Browse the extension store', 'easy-property-listings' ),
			'width'	=> 125,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '">
			<img style="margin-right: 0.5em" width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/>
		      </a>';
		$i ++;
	}
	?>

	<div class="epl-admin-offer" style="margin-bottom: 1em;">
		<div><span><?php _e('Remove these ads?', 'easy-property-listings' ); ?><span></div>
		<div><a href="http://easypropertylistings.com.au/extensions/"><?php _e('Purchase and activate any extension', 'easy-property-listings' ); ?> &#x27F6;</a></div>
	</div>
<?php
}

/**
 * Notice presented on post types, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_post_types () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extensions',
			'img' => 'banner-dash-wide-store.png',
			'alt' => __('Browse the extension store', 'easy-property-listings' ),
			'width'	=> 125,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/advanced-mapping/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_advanced_map',
			'img' => 'offer-advanced-mapping.png',
			'alt' => __('Advanced Mapping', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/sliders/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_sliders',
			'img' => 'offer-sliders.png',
			'alt' => __('Sliders extension', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/staff-directory/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_staff_directory',
			'img' => 'offer-staff-directory.png',
			'alt' => __('Staff Directory', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/listing-templates/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_listing_templates',
			'img' => 'offer-templates.png',
			'alt' => __('Listing Templates', 'easy-property-listings' ),
			'width'	=> 175,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '">
			<img style="display: block; float: left; margin: 0.5em 0.5em 0.5em 0" width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/>
		      </a>';
		$i ++;
	}
	?>
<?php
}

/**
 * Notice presented on post types, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_post_types_last () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	echo '<a target="_blank" href="https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_store">
			<img style="display: block; float: right; margin: 0.5em 0 0.5em 0.5em" width="125" src="' . plugins_url( 'lib/assets/images/' . 'banner-dash-wide-store-reverse.png' , EPL_PLUGIN_FILE ) .'" alt="' . __('Purchase and activate any extension to remove these ads', 'easy-property-listings' ) . '"/>
		</a>';
	?>
<?php
}

/**
 * Admin notices
 *
 * @since 3.1.6
*/
function epl_extension_notice_offer() {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$screen = get_current_screen();

	if( is_epl_core_post() == true || $screen->id == 'easy-property-listings_page_epl-reports' || $screen->id == 'easy-property-listings_page_epl-contacts' || $screen->id == 'toplevel_page_epl-general' ) {

		echo '<div class="notice notice-success" style="max-height: 130px; overflow: hidden;">';
			echo '<div class="epl-offers epl-offers-left" style="box-sizing: border-box; width: 85%; float: left; height: 125px">';
				epl_dashboard_widget_offer_post_types();
			echo '</div>';

			echo '<div class="epl-offers epl-offers-right" style="box-sizing: border-box; width: 15%; float: left;  height: 125px">';
				epl_dashboard_widget_offer_post_types_last();
			echo '</div>';
		echo '</div>';
	}
}
add_action( 'admin_notices', 'epl_extension_notice_offer' );

/**
 * Check if a given extensions has beta support enabled
 *
 * @param       string $slug The slug of the extension to check
 * @return      bool True if enabled, false otherwise
 */
function epl_extension_has_beta_support( $slug ) {

	$enabled_betas = epl_get_option( 'enabled_betas', array() );
	$return        = false;
	if( in_array( $slug, $enabled_betas ) ) {
		$return = true;
	}
	return $return;
}

/**
 * EPL Tools Tabs
 * @return [type] [description]
 * @since  3.3 [<description>]
 */
function epl_get_tools_tab() {

	$default_tabs = array(
		'tools'	=>	array(
			'label'		=>	__('Import/Export','easy-property-listings'),
			'callback'	=>	'epl_settings_import_export'
		)
	);

	if( epl_show_upgrade_tab() ) :

		$default_tabs['upgrade'] = array(
			'label'		=>	__('Upgrade','easy-property-listings'),
			'callback'	=>	'epl_settings_upgrade_tab'
		);

	endif;

	return apply_filters('epl_get_tools_tab',$default_tabs);
}

/**
 * Display Upgrade Tab
 *
 * @since       3.3
 */
function epl_show_upgrade_tab() {

	$upgraded = get_option('epl_db_upgraded_to') < 3.3 ? true : false;

	$upgraded = isset($_GET['dev']) ? true : $upgraded;

	return $upgraded;
}

/**
 * Serialize Variable
 *
 * @since 3.3
 * @return serialize string
 */
function epl_serialize($data) {
	return base64_encode(serialize($data));
}

/**
 * Un-serialize Variable
 *
 * @since 3.3
 * @return un-serialized string
 */
function epl_unserialize($data) {
	return unserialize(base64_decode($data));
}

/**
 * Import Tools Settings Screen
 *
 * @since 3.3
 */
function epl_settings_import_export() {

	do_action('epl_pre_import_fields');

	echo '<h2>'.__('Import Options','easy-property-listings').'</h2>'; ?>

	<div class="epl-field">
		<div class="epl-label-wrap">
			<label class="epl-label epl-label-epl_import" for="epl_import">
				<?php
					_e('Import data','easy-property-listings');
				?>
			</label>
		</div>
		<div class="epl-input-wrap">
			<input type="file" name="epl_import" id="epl_import" />
			<span class="epl-help-text">
				<?php
					_e('Import exported file here. Warning! it will override all existing settings','easy-property-listings');
				?>
			</span>
		</div>
	</div>

	<input type="hidden" name="action" value="import">
	<div class="">
		<input type="submit" name="epl_tools_submit" value="<?php _e('Import','easy-property-listings') ?>" class="epl-tools-submit button button-primary"/>
		<span style="color:#f00"><?php _e('WARNING! This will overwrite all existing option values, please proceed with caution','easy-property-listings'); ?></span>
	</div>


	<?php

	do_action('epl_post_import_fields');

	echo '<h2>'.__('Export Options','easy-property-listings').'</h2>';

	$tab    = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'tools';

	echo "<a class='button button-primary' href='?page=epl-tools&tab=$tab&action=export&epl_tools_submit=true'>".__('Download File','easy-property-listings')."</a>";
	?>
	<span style="color:#f00"><?php _e('The following settings are exported. Easy Property Listings settings screen and any Extension settings','easy-property-listings'); ?></span>
	<?php

	do_action('epl_pre_export_fields');

	do_action('epl_post_export_fields');
}

/**
 * Settings upgrade tab screen
 *
 * @since 3.3
 */
function epl_settings_upgrade_tab() {

	echo '<h2>'.__('Upgrade Options','easy-property-listings').'</h2>';

	echo '<div style="color:red">'.__('Upgrading the database will copy all the listing pricing info into a unified price data column for searching and ordering. We recommend taking a database backup before performing this action.','easy-property-listings').'</div>';

	echo "<div><br><a class='button button-primary epl-upgrade-btn' data-upgrade='3.3' href='#'>".__('Upgrade Database','easy-property-listings')."</a></div>";

	echo '<div style="display:none;" class="epl-ajax-notice">'.__('Processing...','easy-property-listings').'</div>';
}

/**
 * Import and Export Form
 *
 * @since 3.3
 */
function epl_handle_tools_form() {

    if( !isset($_GET['page']) || $_GET['page'] != 'epl-tools' || !isset($_REQUEST['epl_tools_submit'])  )
        return;


    if( !isset($_REQUEST['action']) ){
    	return;
    }

    $action  = $_REQUEST['action'];

    // sanitize post array
    $post_data	= filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    switch($action) {

        case 'export':
		$export = get_option('epl_settings');

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=epl-settings-export.txt');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_clean();
		flush();
		echo epl_serialize($export);
		die;
        break;

        case 'import':
        $uploadedfile 	= $_FILES['epl_import'];
        $upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			$imported_data = file_get_contents($movefile['url']);
			$imported_data = epl_unserialize( $imported_data );
			$options_backup = get_option('epl_settings');
			update_option('epl_settings_backup',$options_backup);
			$status = update_option('epl_settings',$imported_data);
		}

        break;
    }

}
add_action('admin_init', 'epl_handle_tools_form' );

/**
 * Upgrade Database Notice
 *
 * @since       3.3
 */
function epl_upgrade_admin_notice(){

	$upgraded_to = get_option('epl_db_upgraded_to');

	if( $upgraded_to < 3.3 && current_user_can( 'administrator' ) ) :

		$head = __('It looks like you upgraded to latest version of Easy Property Listings','easy-property-listings');

		$msg = __('For better performance, we need to upgrade the database. You can upgrade the database by visiting tools page','easy-property-listings');

	     echo '<div class="notice notice-warning epl-upgrade-notice is-dismissible">
	             <p><strong>'.$head.'</strong></p>
	             <p>'.$msg.'</p>
	             <p><a class="button" href="admin.php?page=epl-tools&tab=upgrade">'.__("Take me to the upgrade tool","easy-property-listings").'</a></p>
	         </div>';
 	endif;
}
add_action('admin_notices', 'epl_upgrade_admin_notice');

/**
 * Upgrade EPL Database to 3.3
 *
 * @since       3.3
 */
function epl_upgrade_db() {

	if( !isset($_POST['upgrade_to']) ){
		wp_die( json_encode(array('status'	=>	'fail', 'msg'	=>	__('Some error occured','easy-property-listings') ) ) );
	}

	$ver = $_POST['upgrade_to'];

	switch( $ver ) {

		case '3.3' :
			epl_upgrade_db_to_3_3();
		break;
	}
}
add_action('wp_ajax_epl_upgrade_db','epl_upgrade_db');

/**
 * update user note comment type to note
 *
 * @since       3.3
 */
function epl_update_user_note_type() {

	$comments_query = new WP_Comment_Query;

	$comments = $comments_query->query( array(
		'type__in'	=>	array('epl_user_note')
	)  );

	if( !empty( $comments ) ) {
		foreach( $comments as $comment ) {
			wp_update_comment(
				array(
					'comment_ID'		=>	$comment->comment_ID,
					'comment_type'		=>	'note'
				)
			);
		}
	}
}

/**
 * Check if need to upgrade EPL Database to 3.3
 *
 * @since       3.3
 */
function epl_upgrade_db_to_3_3() {

	$updated_listings = (array) get_option('epl_updated_global_price');
	$current_batch = array();

	$args = array(
		'post_type'	=> epl_get_core_post_types(),
		'post_status'	=> 'any',
		'numberposts' 	=> 200, // 200 listings in single batch
		'exclude'	=> $updated_listings
	);

	$all_posts = get_posts( $args );

	if( !empty($all_posts) ) {

		foreach($all_posts as $single) {

			switch($single->post_type) {

				case 'rental' :
					$price = get_post_meta($single->ID,'property_rent',true);
					update_post_meta($single->ID,'property_price_global',$price);
				break;

				case 'commercial' :
					$price = get_post_meta($single->ID,'property_price',true);
					if($price == ''){
						$price = get_post_meta($single->ID,'property_com_rent',true);
					}
					update_post_meta($single->ID,'property_price_global',$price);
				break;

				default :
					$price = get_post_meta($single->ID,'property_price',true);
					update_post_meta($single->ID,'property_price_global',$price);
				break;
			}
			$current_batch[] = $single->ID;
			$updated_listings[] = $single->ID;
		}

		update_option('epl_updated_global_price',$updated_listings);
		wp_die(
			json_encode(
				array(
					'status'	=>	'success',
					'buffer'	=>	'processing',
					'msg'		=>	sprintf( __('Database upgrade in process, following listings updated : %s. Please wait...','easy-property-listings'), implode(', ',$current_batch)  )
				)
			)
		);

	} else {

		update_option('epl_db_upgraded_to','3.3');
		wp_die(
			json_encode(
				array(
					'status'	=>	'success',
					'buffer'	=>	'complete',
					'msg'		=>	__('Database upgraded successfully','easy-property-listings')
				)
			)
		);
	}
}

/**
 * Update the property_price_global when saving or updating an EPL post
 *
 * @since       3.3
 */
function epl_sync_property_price_global($post_id, $post, $update) {

	if( is_epl_post() ) {

		if ( 'rental' == $post->post_type ) {
			$price = get_post_meta($post_id,'property_rent',true);
			update_post_meta($post_id,'property_price_global',$price);

		} elseif ( 'commercial' == $post->post_type ) {

			$price = get_post_meta($post_id,'property_price',true);
			if($price == ''){
				$price = get_post_meta($post_id,'property_com_rent',true);
			}
			update_post_meta($post_id,'property_price_global',$price);

		} else {

			$price = get_post_meta($post_id,'property_price',true);
			update_post_meta($post_id,'property_price_global',$price);
		}
	}
}

add_action('save_post','epl_sync_property_price_global',40,3);

/**
 * Filter the contacts comments
 *
 * @since       3.3
 */
function epl_get_avatar_filter($avatar, $id_or_email,$args) {

	if( !is_object($id_or_email) ){
		return $avatar;
	}

	if($id_or_email->comment_agent != 'easy-property-listings') {
		return $avatar;
	}

	$label = __('Note','easy-property-listings');

	switch($id_or_email->comment_type) {

		case 'call' :
			$label = __('Call','easy-property-listings');
		break;

		case 'email' :
			$label = __('Mail','easy-property-listings');
		break;

		case 'note' :
			$label = __('Note','easy-property-listings');
		break;

		case 'listing_alert' :
			$label = __('Alert','easy-property-listings');
		break;

		default:
			$label = __('Note','easy-property-listings');
		break;
	}

	return '<div class="avatar epl-notes-grav">
			<div class="epl-notes-icon">
				'.substr($label, 0, 1).'
			</div>
			<div class="epl-notes-label">
				'.$label.'
			</div>
		</div>';
}
add_filter('pre_get_avatar','epl_get_avatar_filter',10,5);

/**
 * Update a featured listing when pressing the star icon
 *
 * @since       3.3
 */
function epl_update_featured_listing() {

	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

	if($id <= 0)
		return;

	$featured = get_post_meta($id,'property_featured',true);
	$is_featured = 'no';

	if( $featured == 'yes' ) {
		update_post_meta($id,'property_featured','');
		$is_featured = 'no';
	} else {
		update_post_meta($id,'property_featured','yes');
		$is_featured = 'yes';
	}

	wp_die( json_encode( array( 'status'	=>	'successful', 'featured'	=>	$is_featured ) ) );
}
add_action('wp_ajax_epl_update_featured_listing','epl_update_featured_listing');