<?php
function epl_admin_enqueue_scripts($screen) {
	$current_dir_path = plugins_url('', __FILE__ );
	
	wp_enqueue_style( 'epl-admin-styles', $current_dir_path . '/css/style.admin.css' );
	wp_enqueue_script( 'epl-admin-scripts', $current_dir_path . '/js/jquery.admin.scripts.js', array('jquery') );
	
	if( $screen == 'post.php' || $screen == 'post-new.php' ) {
		wp_enqueue_style( 'epl-jquery-validation-engine-style', $current_dir_path . '/css/validationEngine.jquery.css' );
		wp_enqueue_script( 'epl-jquery-validation-engine-lang-scripts', $current_dir_path . '/js/jquery.validationEngine-en.js', array('jquery') );
		wp_enqueue_script( 'epl-jquery-validation-engine-scripts', $current_dir_path . '/js/jquery.validationEngine.js', array('jquery') );
	}	
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

function epl_wp_enqueue_scripts() {
	$current_dir_path = plugins_url('', __FILE__ );
	
	wp_enqueue_style( 'epl-front-styles', $current_dir_path . '/css/style.front.css' );
	wp_enqueue_style( 'epl-graph-style', $current_dir_path . '/css/style.graph.box.css' );
	wp_enqueue_script( 'epl-front-scripts', $current_dir_path . '/js/jquery.front.scripts.js', array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );
