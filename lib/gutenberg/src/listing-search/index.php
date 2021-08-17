<?php
/**
 * Handles all server side logic for the listing Gutenberg Block. This block is functionally the same
 * as the [listing] shortcode used within EPL.
 *
 * @package EPL
 * @since 3.5.0
 */

if ( ( class_exists( 'EPL_Gutenberg_Block' ) ) && ( ! class_exists( 'EPL_Gutenberg_Block_Listing_Search' ) ) ) {
	/**
	 * Class for handling EPL Course List Block
	 */
	class EPL_Gutenberg_Block_Listing_Search extends EPL_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {

			$this->shortcode_slug   = 'listing_search';
			$this->block_slug       = 'listing-search';

			

			$this->block_attributes = epl_get_gutenberg_search_attributes();
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
		 * @param string $shortcode_slug This will match the related listing, etc.
		 * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
		 * @param string $content This is the orignal full content being parsed.
		 *
		 * @return array $attributes.
		 */
		public function epl_block_markers_shortcode_atts_filter( $attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) {
			
            if ( $shortcode_slug === $this->shortcode_slug ) {
                
				
			}

			return $attributes;
		}

		// End of functions.
	}
}
new EPL_Gutenberg_Block_Listing_Search();
