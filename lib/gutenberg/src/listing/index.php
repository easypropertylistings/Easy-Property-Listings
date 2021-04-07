<?php
/**
 * Handles all server side logic for the listing Gutenberg Block. This block is functionally the same
 * as the [listing] shortcode used within EPL.
 *
 * @package EPL
 * @since 3.5.0
 */

if ( ( class_exists( 'EPL_Gutenberg_Block' ) ) && ( ! class_exists( 'EPL_Gutenberg_Block_Listing' ) ) ) {
	/**
	 * Class for handling EPL Course List Block
	 */
	class EPL_Gutenberg_Block_Listing extends EPL_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug   = 'listing';
			$this->block_slug       = 'listing';
			$this->block_attributes = array(
				'post_type' =>  [
					'type' =>  'array',
					'default' =>  epl_get_option('activate_post_types'),
				],
				'status' =>  [
					'type' =>  'array',
					'default' =>  ['current', 'sold', 'leased'],
				],
				'limit' =>  [
					'type' =>  'string',
					'default' =>  '10'
				],
				'offset' =>  [
					'type' =>  'string',
					'default' =>  ''
				],
				'author' =>  [
					'type' =>  'string',
					'default' =>  '',
				],
				'agent' =>  [
					'type' =>  'string',
					'default' =>  '',
				],
				'featured' =>  [
					'type' =>  'boolean',
					'default' =>  false,
				],
				'template' =>  [
					'type' =>  'string',
					'default' =>  ''
				],
				'location' =>  [
					'type' =>  'string',
					'default' =>  '',
				],
				'tools_top' =>  [
					'type' =>  'string',
					'default' =>  'off',
				],
				'tools_bottom' =>  [
					'type' =>  'string',
					'default' =>  'off',
				],
				'sortby' =>  [
					'type' =>  'string',
					'default' =>  ''
				],
				'sort_order' =>  [
					'type' =>  'string',
					'default' =>  'DESC'
				],
				'pagination' =>  [
					'type' =>  'string',
					'default' =>  'on'
				],
				'instance_id' =>  [
					'type' =>  'string',
					'default' =>  '1'
				],
				'wrapper_class' =>  [
					'type' =>  'string',
					'default' =>  ''
				]
			);
			$this->self_closing     = true;

			$this->init();
		}

		/**
		 * Render Block
		 *
		 * This function is called per the register_block_type() function above. This function will output
		 * the block rendered content
		 *
		 * @since 3.5.0
		 *
		 * @param array $attributes Shortcode attrbutes.
		 * @return none The output is echoed.
		 */
		public function render_block( $attributes = array() ) {

			$attributes           = apply_filters( 'epl_block_markers_shortcode_atts', $attributes, $this->shortcode_slug, $this->block_slug, '' );
            $shortcode_params_str = $this->prepare_block_atts_to_param( $attributes );
            $shortcode_params_str = '[' . $this->shortcode_slug . ' ' . $shortcode_params_str . ']';
            
            $shortcode_out        = do_shortcode( $shortcode_params_str );

            // This is mainly to protect against emty returns with the Gutenberg ServerSideRender function.
            return $this->render_block_wrap( $shortcode_out );
			wp_die();
		}

		/**
		 * Called when parsing the block content.
		 *
		 * @since 3.5.0
		 *
		 * @param array  $attributes The array of attributes parse from the block content.
		 * @param string $shortcode_slug This will match the related LD shortcode ld_profile, listing, etc.
		 * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
		 * @param string $content This is the orignal full content being parsed.
		 *
		 * @return array $attributes.
		 */
		public function epl_block_markers_shortcode_atts_filter( $attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) {
			
            if ( $shortcode_slug === $this->shortcode_slug ) {
                
				if ( isset( $attributes['wrapper_class'] ) ) {
					$attributes['class'] = $attributes['wrapper_class'];
				}
			}

			return $attributes;
		}

		// End of functions.
	}
}
new EPL_Gutenberg_Block_Listing();
