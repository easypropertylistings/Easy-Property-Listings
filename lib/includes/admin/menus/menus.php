<?php
/**
 * Menus
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $epl_contact_page_hook;

add_action('admin_menu', 'epl_admin_menu');

/**
 * Admin Menus
 *
 * @since 1.0
 */
function epl_admin_menu() {
	global $epl_contact_page_hook;
	// Contributors and above can access these options
	$menu_title	= __('Easy Property Listings', 'easy-property-listings' );
	$page_title	= __('Easy Property Listings', 'easy-property-listings' );
	$capability	= 'edit_posts';
	$main_menu_slug = $menu_slug	= 'epl-general';
	$function	= 'epl_menu_general';
	$icon 		= 'dashicons-portfolio';
	$position 	= '25.9';
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);

	$page_title	= __('Help', 'easy-property-listings' );
	$menu_title	= __('Help', 'easy-property-listings' );
	add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

	// Administrator and above can see these options
	$page_title	= __('Settings', 'easy-property-listings' );
	$menu_title 	= __('Settings', 'easy-property-listings' );
	$capability	= 'manage_options';
	$function	= 'epl_menu_settings';
	$menu_slug	= 'epl-settings';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

	// Contacts Menu item
	$page_title	= __('Contacts', 'easy-property-listings' );
	$menu_title	= __('Contacts', 'easy-property-listings' );
	$menu_slug	= 'epl-contacts';
	$function	= 'epl_contacts_page';
	$this_user	= wp_get_current_user();
	if ( epl_contact_access() ) {
		$capability	= $this_user->roles[0];
		$epl_contact_page_hook = add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	// Reports Access, $this_user is common with contacts
	$page_title	= __('Reports', 'easy-property-listings' );
	$menu_title	= __('Reports', 'easy-property-listings' );
	$capability	= 'edit_published_posts';
	$menu_slug	= 'epl-reports';
	$function	= 'epl_reports_page';
	if ( epl_reports_access() ) {
		$capability	= $this_user->roles[0];
		$epl_reports_page_hook = add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	// Administrator and above can see these options
	$capability	= 'manage_options';

	// Enable Menu if extension requires extension options
	if ( has_filter( 'epl_extensions_options_filter_new' ) ) {
		$page_title	= __('Extensions', 'easy-property-listings' );
		$menu_title	= __('Extensions', 'easy-property-listings' );
		$menu_slug	= 'epl-extensions';
		$function	= 'epl_menu_extensions';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	if ( has_filter( 'epl_extensions_options_filter' ) ) {
		// show their menu on separate page
		$page_title_old	= __('Extensions Updates', 'easy-property-listings' );
		$menu_title_old	= '<span style="color:#ff0000">'.__('Update Extensions', 'easy-property-listings' ).'</span>';
		$menu_slug_old	= 'epl-extensions-compat';
		$function_old	= 'epl_menu_extensions_compat';
		add_submenu_page($main_menu_slug, $page_title_old, $menu_title_old, $capability, $menu_slug_old, $function_old);
	}

	// Enable Menu if extension requires license options
	if ( has_filter( 'epl_license_options_filter' ) ) {
		$page_title	= __('Licenses', 'easy-property-listings' );
		$menu_title	= __('Licenses', 'easy-property-listings' );
		$menu_slug	= 'epl-licenses';
		$function	= 'epl_menu_licenses';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	// Author level and above can see these options
	$page_title	= __('Add Ons', 'easy-property-listings' );
	$menu_title	= __('Add Ons', 'easy-property-listings' );
	$capability	= 'edit_published_posts';
	$menu_slug	= 'epl-addons';
	$function	= 'epl_menu_addons';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

/**
 * General Menu
 *
 * @since 1.0
 */
function epl_menu_general() {
	if(!current_user_can('edit_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-help.php';
}

/**
 * Settings Menu
 *
 * @since 1.0
 */
function epl_menu_settings() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-general.php';
}

/**
 * Extensions Menu
 *
 * @since 1.0
 */
function epl_menu_extensions() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-extensions.php';
}

/**
 * Extensions Compatibility Menu
 *
 * @since 1.0
 */
function epl_menu_extensions_compat() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-extensions-compat.php';
}

/**
 * Licenses Menu
 *
 * @since 1.0
 */
function epl_menu_licenses() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-licenses.php';
}

/**
 * Addons Menu
 *
 * @since 1.0
 */
function epl_menu_addons() {
	if(!current_user_can('edit_published_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-addons.php';
}

/**
 * Help Menu
 *
 * @since 1.0
 */
function epl_menu_help() {
	if(!current_user_can('edit_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'easy-property-listings' ) );
	}
	require_once 'menu-help.php';
}
