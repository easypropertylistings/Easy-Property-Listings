<?php
/**
 * Settings and options pages for Easy Property Listings
 *
 * @since 1.0
 * @return void
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load in admin
if( !is_admin() ) {
	return;
}

add_action('admin_menu', 'epl_admin_menu');

function epl_admin_menu() {
	// Contributors and above can access these options
	$menu_title	= __('Easy Property Listings', 'epl');
	$page_title	= __('Easy Property Listings', 'epl');
	$capability	= 'edit_posts';
	$main_menu_slug = $menu_slug	= 'epl-general';
	$function	= 'epl_menu_general';
	$icon 		= 'dashicons-portfolio';
	$position 	= '25.9';
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);
	
	$page_title	= __('Help', 'epl');
	$menu_title	= __('Help', 'epl');
	add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

	// Administrator and above can see these options
	$page_title	= __('Settings', 'epl');
	$menu_title 	= __('Settings', 'epl');
	$capability	= 'manage_options';
	$function	= 'epl_menu_settings';
	$menu_slug	= 'epl-settings';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	// Enable Menu if extension requires extension options	
	if ( has_filter( 'epl_extensions_options_filter_new' ) ) {
		$page_title	= __('Extensions', 'epl');
		$menu_title	= __('Extensions', 'epl');
		$menu_slug	= 'epl-extensions';
		$function	= 'epl_menu_extensions';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	if ( has_filter( 'epl_extensions_options_filter' ) ) {
		// show their menu on separate page
		$page_title_old	= __('Extensions Updates', 'epl');
		$menu_title_old	= '<span style="color:#ff0000">'.__('Update Extensions', 'epl').'</span>';
		$menu_slug_old	= 'epl-extensions-compat';
		$function_old	= 'epl_menu_extensions_compat';
		add_submenu_page($main_menu_slug, $page_title_old, $menu_title_old, $capability, $menu_slug_old, $function_old);
	}
	
	// Enable Menu if extension requires license options
	if ( has_filter( 'epl_license_options_filter' ) ) {
		$page_title	= __('Licenses', 'epl');
		$menu_title	= __('Licenses', 'epl');
		$menu_slug	= 'epl-licenses';
		$function	= 'epl_menu_licenses';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	// Editor level and above can see these options
	$page_title	= __('Add Ons', 'epl');
	$menu_title	= __('Add Ons', 'epl');
	$capability	= 'edit_published_posts';
	$menu_slug	= 'epl-addons';
	$function	= 'epl_menu_addons';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	// Editor level and above can see these options
	$page_title	= __('Leads', 'epl');
	$menu_title	= __('Leads', 'epl');
	$capability	= 'edit_published_posts';
	$menu_slug	= 'epl-leads';
	$function	= 'epl_leads_page';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
}

function epl_menu_general() {
	if(!current_user_can('edit_published_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-help.php';
}

function epl_menu_settings() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-general.php';
}

function epl_menu_extensions() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-extensions.php';
}

function epl_menu_extensions_compat() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-extensions-compat.php';
}

function epl_menu_licenses() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-licenses.php';
}

function epl_menu_addons() {
	if(!current_user_can('edit_published_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-addons.php';
}


function epl_menu_help() {
	if(!current_user_can('edit_posts')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}
	require_once 'menu-help.php';
}
