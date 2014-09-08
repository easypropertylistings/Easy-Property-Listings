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
	$page_title	= __('Easy Property Listings', 'epl');
	$menu_title	= __('Easy Property Listings', 'epl');
	$capability	= 'manage_options';
	$main_menu_slug = $menu_slug	= 'epl-general';
	$function	= 'epl_menu_general';
	$icon = 'dashicons-portfolio';
	$position = '25.9';
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);

	$menu_title = __('Settings', 'epl');
    add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

	$page_title	= __('Display', 'epl');
	$menu_title	= __('Display', 'epl');
	$menu_slug	= 'epl-display';
	$function	= 'epl_menu_display';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

	// Enable Menu if extension requires extension options
	if ( has_filter( 'epl_extensions_options_filter' ) ) {
		$page_title	= __('Extensions', 'epl');
		$menu_title	= __('Extensions', 'epl');
		$menu_slug	= 'epl-extensions';
		$function	= 'epl_menu_extensions';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	// Enable Menu if extension requires license options
	if ( has_filter( 'epl_license_options_filter' ) ) {
		$page_title	= __('Licenses', 'epl');
		$menu_title	= __('Licenses', 'epl');
		$menu_slug	= 'epl-licenses';
		$function	= 'epl_menu_licenses';
		add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}

	$page_title	= __('Add Ons', 'epl');
	$menu_title	= __('Add Ons', 'epl');
	$menu_slug	= 'epl-addons';
	$function	= 'epl_menu_addons';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

function epl_menu_general() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-general.php';
}

function epl_menu_display() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-display.php';
}

function epl_menu_extensions() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-extensions.php';

}

function epl_menu_licenses() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-licenses.php';
}

function epl_menu_addons() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-addons.php';
}
