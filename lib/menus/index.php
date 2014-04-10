<?php
add_action('admin_menu', 'epl_admin_menu');

function epl_admin_menu() {
	$page_title	= __('Easy Property Listings', 'epl');
	$menu_title	= __('Easy Property Listings', 'epl');
	$capability	= 'manage_options';
	$main_menu_slug = $menu_slug	= 'epl-general';
	$function	= 'epl_menu_general';
	$icon = 'dashicons-portfolio';
	$position = 5;
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);
	
	$menu_title = __('General', 'epl');
    add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$page_title	= __('Display', 'epl');
	$menu_title	= __('Display', 'epl');
	$menu_slug	= 'epl-display';
	$function	= 'epl_menu_display';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$page_title	= __('Extensions', 'epl');
	$menu_title	= __('Extensions', 'epl');
	$menu_slug	= 'epl-extensions';
	$function	= 'epl_menu_extensions';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$page_title	= __('Integrations', 'epl');
	$menu_title	= __('Integrations', 'epl');
	$menu_slug	= 'epl-integrations';
	$function	= 'epl_menu_integrations';
	add_submenu_page($main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	
	$page_title	= __('Help', 'epl');
	$menu_title	= __('Help', 'epl');
	$menu_slug	= 'epl-help';
	$function	= 'epl_menu_help';
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

function epl_menu_integrations() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-integrations.php';
}

function epl_menu_help() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.', 'epl') );
	}

	require_once 'menu-help.php';
}
