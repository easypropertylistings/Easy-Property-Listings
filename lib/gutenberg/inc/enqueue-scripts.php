<?php

/**
 * Load and enqueue styles for gutenberg.
 *
 * @since 3.5.0
 */
function epl_gutenberg_load_styles() {

	$mode                  = epl_get_option( 'epl_plugin_mode', 'development' );
	$suffix                = 'production' === $mode ? '.min' : '';
	$current_dir_path 	   = EPL_PLUGIN_URL.'lib/assets';

	// All CSS including Structual.
	if ( epl_get_option( 'epl_use_core_css', 'off' ) === 'on' ) {
		// Dont use css.
	} else {

		// Legacy CSS: on is to enable visual css, default off.
		if ( epl_get_option( 'epl_css_legacy', 'off' ) === 'on' ) {

			wp_enqueue_style( 'epl-front-styles', $current_dir_path . '/css/style-legacy' . $suffix . '.css', false, EPL_PROPERTY_VER );

		} else {
			// Structural CSS.
			wp_enqueue_style( 'epl-css-lib', $current_dir_path . '/css/style-structure' . $suffix . '.css', false, EPL_PROPERTY_VER );
			wp_enqueue_style( 'epl-style', $current_dir_path . '/css/style' . $suffix . '.css', false, EPL_PROPERTY_VER );
		}

		// Enhanced CSS: on is to enable visual css, default on for new installations.
		if ( epl_get_option( 'epl_css_enhanced', 'off' ) === 'on' ) {
			wp_enqueue_style( 'epl-style-enhanced', $current_dir_path . '/css/style-enhanced' . $suffix . '.css', false, EPL_PROPERTY_VER );
		}

		/**
		 * Load theme specific stylesheet for epl, if exists
		 * Loaded at the end to override any styles in plugin
		 */
		if ( file_exists( get_stylesheet_directory() . '/easypropertylistings/style.css' ) ) {
			wp_enqueue_style( 'epl-theme-style', get_stylesheet_directory_uri() . '/easypropertylistings/style.css', false, EPL_PROPERTY_VER );
		}
	}

}

/**
 * Enqueue block editor only JavaScript and CSS
 */
function epl_editor_scripts() {

	
    $indexURL = EPL_PLUGIN_URL.'lib/gutenberg/build/index.js';
	$editorStylePath = EPL_PLUGIN_URL.'lib/gutenberg/build/epl-gutenberg.css';

	// Enqueue the bundled block JS file.
	wp_enqueue_script(
		'epl-blocks-js',
		$indexURL,
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
        EPL_PROPERTY_VER
	);

	$post_types = epl_get_post_types();

	$opts_post_types = [];

	foreach( $post_types as $post_type_key	=>	$label ) {
		$opts_post_types[] = [
			'label'	=>	$label,
			'value' =>	$post_type_key
		];
	}

	$epl_settings = array(
		'settings' 						=> epl_settings(),
		'post_types'					=> $opts_post_types,
		'search_attributes'				=> epl_get_gutenberg_search_attributes()
	);

	$epl_settings['meta'] = array();
	$epl_settings['meta']['posts_per_page'] = get_option( 'posts_per_page' );

	if ( is_admin() ) {
		$current_screen = get_current_screen();
		if ( 'post' === $current_screen->base ) {

			global $post, $post_type, $editing;
			$epl_settings['meta']['post'] = array();

			$epl_settings['meta']['post']['post_id'] = $post->ID;
			$epl_settings['meta']['post']['post_type'] = $post_type;
			$epl_settings['meta']['post']['editing'] = $editing;

			$epl_settings['meta']['post']['course_id'] = 0;
		}
	}

	// Load the MO file translations into wp.i18n script hook.
	epl_load_inline_script_locale_data();

	wp_localize_script( 'epl-blocks-js', 'epl_settings', $epl_settings );

	// load styles for EPL Blocks
	epl_gutenberg_load_styles();
}
// Hook scripts function into block editor hook.
add_action( 'enqueue_block_editor_assets', 'epl_editor_scripts' );

/**
 * Enqueue front end and editor JavaScript and CSS
 */
function epl_block_scripts() {
}

// Hook scripts function into block editor hook.
add_action( 'enqueue_block_assets', 'epl_block_scripts' );


/**
 * Register custom block category.
 *
 * @since 3.5.0
 * @param array  $block_categories Array of current block categories.
 * @param object $post WP_Post instance of post being edited.
 * @return array $block_categories.
 */
function epl_block_categories( $block_categories = array(), $post = false ) {

	$ld_block_cat_found = false;

	foreach( $block_categories as $block_cat ) {
		if ( ( isset( $block_cat['slug'] ) ) && ( $block_cat['slug'] == 'epl-blocks' ) ) {
			$ld_block_cat_found = true;
		}
	}

	if ( false === $ld_block_cat_found ) {
		if ( ( $post ) && ( is_a( $post, 'WP_Post' ) ) ) {
			if ( in_array( $post->post_type, epl_get_core_post_types() ) ) {
				$block_categories = array_merge(
					array(
						array(
							'slug'  => 'epl-blocks',
							'title' => esc_html__( 'EPL Blocks', 'easy-property-listings' ),
							'icon'  => false,
						),
					),
					$block_categories
				);
			} else {
				$block_categories[] = array(
					'slug'  => 'epl-blocks',
					'title' => esc_html__( 'EPL Blocks', 'epeasy-property-listingsl' ),
					'icon'  => false,
				);
			}
		}
	}

	// Always return $default_block_categories.
	return $block_categories;
}
add_filter( 'block_categories', 'epl_block_categories', 30, 2 );

/**
 * Get search attributes for gutenberg.
 *
 * @since 3.5.0
 */
function epl_get_gutenberg_search_attributes() {

	$search_fields = epl_search_widget_fields();

	$block_attributes = [];

	foreach( $search_fields as $search_field ) {

		$opts = [];

		if( 'select' == $search_field['type'] ) {
			$type = 'string';
			$render_type = 'select';
			if( isset( $search_field['multiple']) && true == $search_field['multiple'] ) {
				$type = 'array';
				$render_type = 'select_multiple';
			}

			foreach( $search_field['options'] as $option_key => $option_label ) {
				$opts[] = [
					'label'	=>	$option_label,
					'value'	=>	$option_key
				];
			}
			
		}

		if( 'text' == $search_field['type'] ) {
			$type = 'string';
			$render_type = 'text';
		}

		if( 'checkbox' == $search_field['type'] ) {
			$type = 'string';
			$render_type = 'select';
		}

		if( 'show_property_status_frontend' == $search_field['key'] ) {
			$search_field['label'] = __( 'Show status on frontend?', 'easy-property-listings' );
		}
		
		$block_attributes[ $search_field['key'] ] = [
			'type' 		=>  $type,
			'default' 	=>  $search_field['default'],
			'label'		=>	$search_field['label'],
			'key'		=>	$search_field['key'],
			'render_type'	=> $render_type,
			'opts'		=>	$opts
		];
		
	}

	return $block_attributes;
}
