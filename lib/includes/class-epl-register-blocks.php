<?php
/**
 * Register Block for EPL
 *
 * @package EPL
 * @subpackage Block
 * @since 3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EPL_Register_Blocks' ) ) :
	
	/**
	 * EPL Block Template Utils
	 * 
	 * @since 3.6.0
	 */
	class EPL_Register_Blocks {

                /**
		 * Instance
		 *
		 * @var EPL_Register_Blocks
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Constructor
		 *
		 * @since 3.6.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_blocks' ) );
			add_action( 'init', array( $this, 'register_patterns' ), 30 );
			add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_styles' ) );
			add_filter( 'query_loop_block_query_vars', array( $this, 'filter_listings_query' ), 10, 3 );
			add_filter( 'block_categories_all', array( $this, 'register_block_category' ) );
		}

                public static function instance() {
			if ( ! isset( self::$instance )  ) {
				self::$instance = new EPL_Register_Blocks();
			}
			return self::$instance;
		}

		public function archive_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! $this->is_listing_archive_context() ) {
				return '';
			}

			$post_type = isset( $attributes['postType'] ) ? sanitize_key( $attributes['postType'] ) : 'property';

			return $this->render_template( 'fse/archive-listing.php', $post_type, false );
		}

		public function single_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_epl_post_single() ) {
				return '';
			}

			$post_type = isset( $attributes['postType'] ) ? sanitize_key( $attributes['postType'] ) : 'property';

			return $this->render_template( 'fse/single-listing.php', $post_type, true );
		}

		public function archive_directory_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_post_type_archive( 'directory' ) ) {
				return '';
			}

			return $this->render_template( 'fse/archive-directory.php', 'directory', false );
		}

		public function single_directory_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_singular( 'directory' ) ) {
				return '';
			}

			return $this->render_template( 'fse/single-directory.php', 'directory', true );
		}

		/**
		 * Whether a dynamic block is being rendered by the editor REST endpoint.
		 *
		 * @return bool
		 */
		private function is_editor_preview() {
			return defined( 'REST_REQUEST' ) && REST_REQUEST;
		}

		/**
		 * Whether the request uses an EPL listing archive-style query.
		 *
		 * @return bool
		 */
		private function is_listing_archive_context() {
			if ( function_exists( 'epl_is_search' ) && epl_is_search() ) {
				return true;
			}

			if ( is_epl_post_archive() ) {
				return true;
			}

			return is_tax( array( 'location', 'tax_feature', 'tax_business_listing' ) );
		}

		/**
		 * Render an EPL FSE template and preserve the main query globals.
		 *
		 * Editor requests receive representative published content because the Site
		 * Editor does not carry the frontend archive/singular query into REST calls.
		 *
		 * @param string $template  Relative EPL template path.
		 * @param string $post_type Preview post type.
		 * @param bool   $single    Whether to query a single preview item.
		 * @return string
		 */
		private function render_template( $template, $post_type, $single ) {
			global $wp_query, $post;

			$original_query = $wp_query;
			$original_post  = $post;
			$preview_query  = null;

			if ( $this->is_editor_preview() ) {
				$allowed_post_types = array_merge( EPL_Block_Template_Utils::get_supported_post_types(), array( 'directory' ) );
				if ( ! in_array( $post_type, $allowed_post_types, true ) || ! post_type_exists( $post_type ) ) {
					$post_type = 'property';
				}

				$preview_query = new WP_Query(
					array(
						'post_type'           => $post_type,
						'post_status'         => 'publish',
						'posts_per_page'      => $single ? 1 : 3,
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);

				$wp_query = $preview_query;
				$post     = $preview_query->post;
			}

			$query_state = $this->capture_query_state( $wp_query );

			ob_start();
			epl_get_template_part( $template );
			$output = ob_get_clean();

			$this->restore_query_state( $wp_query, $query_state );
			$post = $original_post;

			if ( $preview_query ) {
				$wp_query = $original_query;
				wp_reset_postdata();
			}

			return $output;
		}

		/**
		 * Capture mutable WP_Query loop state.
		 *
		 * @param WP_Query $query Query object.
		 * @return array
		 */
		private function capture_query_state( $query ) {
			return array(
				'current_post' => $query->current_post,
				'in_the_loop'  => $query->in_the_loop,
				'before_loop'  => isset( $query->before_loop ) ? $query->before_loop : null,
				'post'         => $query->post,
			);
		}

		/**
		 * Restore mutable WP_Query loop state.
		 *
		 * @param WP_Query $query Query object.
		 * @param array    $state Captured state.
		 * @return void
		 */
		private function restore_query_state( $query, $state ) {
			$query->current_post = $state['current_post'];
			$query->in_the_loop  = $state['in_the_loop'];
			$query->post         = $state['post'];

			if ( null !== $state['before_loop'] ) {
				$query->before_loop = $state['before_loop'];
			}
		}

		/**
		 * Load frontend EPL presentation styles inside the block-editor iframe.
		 *
		 * @return void
		 */
		public function enqueue_editor_styles() {
			if ( ! is_admin() ) {
				return;
			}

			// Meta element icons use Dashicons. Explicitly enqueue the font in the
			// editor canvas because iframe-based editors do not inherit admin styles.
			wp_enqueue_style( 'dashicons' );

			if ( function_exists( 'epl_enqueue_front_styles' ) ) {
				epl_enqueue_front_styles( true );
			}

			if ( function_exists( 'epl_sd_enqueue_scripts' ) ) {
				epl_sd_enqueue_scripts();
				// Staff Directory's frontend behavior is not used by block previews.
				// Loading it in the editor iframe creates a jQuery race and needlessly
				// executes frontend-only code in every pattern thumbnail.
				wp_dequeue_script( 'epl-sd-scripts' );
			}
		}

		/**
		 * Add a dedicated EPL block inserter category.
		 *
		 * @param array $categories Registered categories.
		 * @return array
		 */
		public function register_block_category( $categories ) {
			foreach ( $categories as $category ) {
				if ( 'epl' === $category['slug'] ) {
					return $categories;
				}
			}

			array_unshift(
				$categories,
				array(
					'slug'  => 'epl',
					'title' => __( 'Easy Property Listings', 'easy-property-listings' ),
					'icon'  => 'admin-home',
				)
			);

			return $categories;
		}

		/**
		 * Register EPL dynamic blocks.
		 *
		 * @since 3.6.0
		 */
		public function register_blocks() {
			if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
				return;
			}

			$renderer = new EPL_Block_Element_Renderer();
			$blocks   = array(
				'epl-archive'           => array( $this, 'archive_block' ),
				'epl-single'            => array( $this, 'single_block' ),
				'epl-archive-directory' => array( $this, 'archive_directory_block' ),
				'epl-single-directory'  => array( $this, 'single_directory_block' ),
				'epl-property-element'  => array( $renderer, 'property_element' ),
				'epl-agent-element'     => array( $renderer, 'agent_element' ),
				'epl-archive-element'   => array( $renderer, 'archive_element' ),
				'epl-listing-advanced'  => array( $this, 'listing_advanced_block' ),
				'epl-listing-search'    => array( $this, 'listing_search_block' ),
			);

			foreach ( $blocks as $directory => $callback ) {
				$block = register_block_type(
					EPL_PATH_LIB . 'blocks/' . $directory,
					array( 'render_callback' => $callback )
				);

				if ( $block && function_exists( 'wp_set_script_translations' ) ) {
					$handles = isset( $block->editor_script_handles ) ? $block->editor_script_handles : array();
					if ( empty( $handles ) && ! empty( $block->editor_script ) ) {
						$handles = array( $block->editor_script );
					}

					foreach ( $handles as $handle ) {
						wp_set_script_translations( $handle, 'easy-property-listings', EPL_PLUGIN_PATH . 'languages' );
						wp_localize_script( $handle, 'eplBlockData', $this->get_editor_data() );
					}
				}
			}
		}

		/** Render the canonical listing_advanced implementation as a block. */
		public function listing_advanced_block( $attributes ) {
			$settings = (array) ( $attributes['settings'] ?? array() );
			$design_source = sanitize_key( $settings['design_source'] ?? 'pattern' );
			$pattern       = sanitize_text_field( $settings['pattern'] ?? 'registered:epl/listing-card-grid' );
			$columns       = 'pattern' === ( $settings['columns'] ?? 'pattern' ) ? 0 : absint( $settings['columns'] ?? 0 );
			$defaults = array(
				'post_type'               => array_keys( epl_get_active_post_types() ),
				'status'                  => array( 'current', 'sold', 'leased' ),
				'commercial_listing_type' => array(),
				'limit'                   => 10,
				'offset'                  => 0,
				'post__in'                => '',
				'post__not_in'            => '',
				'location_id'             => array(),
				'feature_id'              => array(),
				'location'                => '',
				'feature'                 => '',
				'author'                  => array(),
				'agent'                   => '',
				'featured'                => '',
				'open_house'              => '',
				'auction'                 => '',
				'sortby'                  => 'date',
				'sort_order'              => 'DESC',
				'orderby_clause'          => '',
				'template'                => 'default',
				'tools_top'               => 'off',
				'tools_bottom'            => 'off',
				'pagination'              => 'on',
			);
			$settings = wp_parse_args( $settings, $defaults );
			$atts     = array();
			$arrays   = array( 'post_type', 'status', 'commercial_listing_type', 'location_id', 'feature_id', 'author' );

			foreach ( array_keys( $defaults ) as $key ) {
				$value = $settings[ $key ];
				if ( in_array( $key, $arrays, true ) ) {
					$value = implode( ',', array_filter( array_map( 'sanitize_text_field', (array) $value ), 'strlen' ) );
				} elseif ( in_array( $key, array( 'limit', 'offset' ), true ) ) {
					$value = absint( $value );
				} else {
					$value = sanitize_text_field( (string) $value );
				}
				$atts[ $key ] = $value;
			}

			$compare_suffixes = array(
				'equal' => '', 'min' => '_min', 'max' => '_max', 'not_equal' => '_not_equal',
				'like' => '_like', 'not_like' => '_not_like', 'exists' => '_exists', 'not_exists' => '_not_exists',
				'between' => '_between', 'not_between' => '_not_between', 'in' => '_in', 'not_in' => '_not_in',
			);
			foreach ( (array) ( $attributes['dynamicFilters'] ?? array() ) as $filter ) {
				$meta_key = ltrim( sanitize_key( $filter['metaKey'] ?? '' ), '_' );
				$compare  = sanitize_key( $filter['compare'] ?? 'equal' );
				if ( ! $meta_key || ! isset( $compare_suffixes[ $compare ] ) ) {
					continue;
				}
				$key          = '_' . $meta_key . $compare_suffixes[ $compare ];
				$atts[ $key ] = in_array( $compare, array( 'exists', 'not_exists' ), true ) ? '' : sanitize_text_field( (string) ( $filter['value'] ?? '' ) );
			}

			$atts['class']       = 'epl-shortcode-listing epl-block-listing-advanced-results';
			$atts['instance_id'] = wp_unique_id( 'gutenberg-listing-advanced-' );
			$atts                = apply_filters( 'epl_block_listing_advanced_atts', $atts, $attributes );
			$output              = 'pattern' === $design_source
				? $this->render_listing_advanced_pattern( $atts, $pattern, $columns )
				: epl_shortcode_listing_advanced_callback( $atts );

			return sprintf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'epl-block-listing-advanced' ) ), $output );
		}

		/**
		 * Render Listing Advanced results with the Post Template design from a pattern.
		 *
		 * The pattern supplies presentation only. EPL_Advanced_Shortcode_Listing remains
		 * the source of filtering, ordering and pagination behaviour.
		 *
		 * @param array  $atts       Listing Advanced attributes.
		 * @param string $pattern_id Registered-pattern slug or reusable-pattern post ID.
		 * @param int    $columns    Optional column-count override.
		 * @return string
		 */
		private function render_listing_advanced_pattern( $atts, $pattern_id, $columns = 0 ) {
			$content = $this->get_listing_pattern_content( $pattern_id );
			$template = $content ? $this->find_post_template_block( parse_blocks( $content ) ) : null;

			// Never make an invalid or deleted pattern break an existing listings block.
			if ( ! $template || empty( $template['innerBlocks'] ) ) {
				return epl_shortcode_listing_advanced_callback( $atts );
			}

			$shortcode = new EPL_Advanced_Shortcode_Listing( $atts );
			$query     = $shortcode->query_open;
			if ( ! $query->have_posts() ) {
				ob_start();
				do_action( 'epl_shortcode_results_message', 'default' );
				return ob_get_clean();
			}

			$pattern_columns = absint( $template['attrs']['layout']['columnCount'] ?? 1 );
			$columns         = max( 1, min( 6, $columns ? $columns : $pattern_columns ) );
			$gap     = $template['attrs']['style']['spacing']['blockGap'] ?? '32px';
			if ( is_array( $gap ) ) {
				$gap = $gap['top'] ?? reset( $gap );
			}
			$gap = is_string( $gap ) && preg_match( '/^(?:var\(--wp--preset--spacing--[a-z0-9-]+\)|[0-9.]+(?:px|rem|em|%|vw|vh))$/i', $gap ) ? $gap : '32px';

			ob_start();
			?>
			<div class="loop epl-shortcode epl-clearfix epl-block-listing-advanced-pattern">
				<?php if ( 'on' === $atts['tools_top'] ) : ?>
					<?php do_action( 'epl_property_loop_start', $atts ); ?>
				<?php endif; ?>
				<div class="epl-block-listing-advanced-pattern-items epl-block-listing-advanced-pattern-columns-<?php echo esc_attr( $columns ); ?>" style="--epl-listing-pattern-gap:<?php echo esc_attr( $gap ); ?>">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$context = array(
							'postId'   => get_the_ID(),
							'postType' => get_post_type(),
						);
						foreach ( $template['innerBlocks'] as $card_block ) {
							echo ( new WP_Block( $card_block, $context ) )->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block rendering is escaped by each block.
						}
					}
					?>
				</div>
				<?php if ( 'on' === $atts['tools_bottom'] ) : ?>
					<?php do_action( 'epl_property_loop_end' ); ?>
				<?php endif; ?>
				<?php if ( 'on' === $atts['pagination'] ) : ?>
					<div class="loop-footer epl-clearfix"><?php do_action( 'epl_pagination', array( 'query' => $query ) ); ?></div>
				<?php endif; ?>
			</div>
			<?php
			wp_reset_postdata();

			return ob_get_clean();
		}

		/** Get serialized content for a registered or reusable listing pattern. */
		private function get_listing_pattern_content( $pattern_id ) {
			if ( 0 === strpos( $pattern_id, 'registered:' ) ) {
				$slug    = substr( $pattern_id, strlen( 'registered:' ) );
				$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
				return is_array( $pattern ) ? ( $pattern['content'] ?? '' ) : '';
			}

			if ( 0 === strpos( $pattern_id, 'post:' ) ) {
				$post = get_post( absint( substr( $pattern_id, strlen( 'post:' ) ) ) );
				return $post && 'wp_block' === $post->post_type && 'publish' === $post->post_status ? $post->post_content : '';
			}

			return '';
		}

		/** Recursively locate the Post Template that defines an archive pattern's card. */
		private function find_post_template_block( $blocks ) {
			foreach ( (array) $blocks as $block ) {
				if ( 'core/post-template' === ( $block['blockName'] ?? '' ) ) {
					return $block;
				}
				if ( ! empty( $block['innerBlocks'] ) ) {
					$found = $this->find_post_template_block( $block['innerBlocks'] );
					if ( $found ) {
						return $found;
					}
				}
			}

			return null;
		}

		/** Render the canonical WordPress-widget listing search implementation. */
		public function listing_search_block( $attributes ) {
			$settings = (array) ( $attributes['settings'] ?? array() );
			$defaults = epl_search_get_defaults();
			$fields   = epl_search_widget_fields();
			$atts     = array();

			foreach ( $fields as $field ) {
				$key   = sanitize_key( $field['key'] ?? '' );
				$value = array_key_exists( $key, $settings ) ? $settings[ $key ] : ( $defaults[ $key ] ?? '' );
				$atts[ $key ] = is_array( $value )
					? array_values( array_filter( array_map( 'sanitize_text_field', $value ), 'strlen' ) )
					: sanitize_text_field( (string) $value );
			}

			$atts   = apply_filters( 'epl_block_listing_search_atts', $atts, $attributes );
			$title  = (string) ( $atts['title'] ?? '' );
			$output = $title ? '<h3 class="epl-search-title">' . esc_html( $title ) . '</h3>' : '';
			$output .= epl_shortcode_listing_search_callback( $atts );

			return sprintf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'epl-block-listing-search epl-search-widget' ) ), $output );
		}

		/**
		 * Data used by the composable element inspectors.
		 *
		 * @return array
		 */
		private function get_editor_data() {
			$post_types  = epl_get_active_post_types();
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
			$options     = array();
			$image_sizes = array();
			$location_terms = array();
			$feature_terms  = array();
			$authors        = array();
			$listing_patterns = array(
				'registered:epl/listing-card-grid' => __( 'EPL Listings — Grid', 'easy-property-listings' ),
				'registered:epl/listing-card-list' => __( 'EPL Listings — List', 'easy-property-listings' ),
			);

			foreach ( $taxonomies as $taxonomy ) {
				if ( array_intersect( (array) $taxonomy->object_type, array_keys( $post_types ) ) ) {
					$options[ $taxonomy->name ] = $taxonomy->label;
				}
			}

			foreach ( epl_get_thumbnail_sizes() as $name => $dimensions ) {
				$label = ucwords( str_replace( array( '-', '_' ), ' ', $name ) );
				if ( ! empty( $dimensions[0] ) || ! empty( $dimensions[1] ) ) {
					$label .= sprintf( ' (%d×%d)', absint( $dimensions[0] ), absint( $dimensions[1] ) );
				}
				$image_sizes[ $name ] = $label;
			}

			foreach ( array( 'location' => &$location_terms, 'tax_feature' => &$feature_terms ) as $taxonomy => &$term_options ) {
				$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$term_options[ (string) $term->term_id ] = $term->name;
					}
				}
			}
			unset( $term_options );

			foreach ( get_users( array( 'fields' => array( 'user_login', 'display_name' ) ) ) as $user ) {
				$authors[ $user->user_login ] = $user->display_name;
			}

			foreach ( get_posts( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ) ) as $pattern ) {
				if ( false !== strpos( $pattern->post_content, 'epl/property-element' ) && false !== strpos( $pattern->post_content, 'wp:post-template' ) ) {
					$listing_patterns[ 'post:' . $pattern->ID ] = sprintf( __( '%s', 'easy-property-listings' ), $pattern->post_title );
				}
			}

			return array(
				'postTypes'  => $post_types,
				'taxonomies' => $options,
				'imageSizes' => $image_sizes,
				'searchFields'    => array_values( epl_search_widget_fields() ),
				'listingStatuses' => array( 'current' => __( 'Current', 'easy-property-listings' ), 'sold' => __( 'Sold', 'easy-property-listings' ), 'leased' => __( 'Leased', 'easy-property-listings' ) ),
				'commercialTypes' => epl_get_property_com_listing_type_opts(),
				'locationTerms'   => $location_terms,
				'featureTerms'    => $feature_terms,
				'locationLabel'   => epl_tax_location_label(),
				'authors'         => $authors,
				'listingPatterns' => $listing_patterns,
			);
		}

		/**
		 * Apply EPL-specific Query Loop controls.
		 *
		 * Inherited archive queries remain untouched. Static queries may select
		 * multiple listing types and sort all of them by the normalized price key.
		 *
		 * @param array    $query Query arguments.
		 * @param WP_Block $block Query block.
		 * @param int      $page  Current page.
		 * @return array
		 */
		public function filter_listings_query( $query, $block, $page ) {
			$attributes = $block->parsed_block['attrs'] ?? array();
			if ( 'epl/listings-query' !== ( $attributes['namespace'] ?? '' ) ) {
				return $query;
			}

			$epl_query  = (array) ( $attributes['query'] ?? array() );
			$allowed    = array_keys( epl_get_active_post_types() );
			$post_types = array_values( array_intersect( array_map( 'sanitize_key', (array) ( $epl_query['eplPostTypes'] ?? array() ) ), $allowed ) );

			if ( $post_types ) {
				$query['post_type'] = 1 === count( $post_types ) ? $post_types[0] : $post_types;
			}

			if ( 'price' === ( $epl_query['eplOrderBy'] ?? '' ) ) {
				$query['meta_key'] = 'property_price_global'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$query['orderby']  = 'meta_value_num';
			}

			return $query;
		}

		/** Register editable starter patterns for listing and staff templates. */
		public function register_patterns() {
			if ( ! function_exists( 'register_block_pattern' ) ) {
				return;
			}

			register_block_pattern_category( 'epl', array( 'label' => __( 'Easy Property Listings', 'easy-property-listings' ) ) );

			register_block_pattern(
				'epl/listing-card-list',
				array(
					'title'       => __( 'EPL Listing Card — List', 'easy-property-listings' ),
					'description' => __( 'A premium horizontal listing card with a two-column image and details layout.', 'easy-property-listings' ),
					'categories'  => array( 'epl' ),
					'blockTypes'  => array( 'core/post-template' ),
					'content'     => '<!-- wp:group {"className":"epl-pattern-card epl-pattern-card-list","style":{"spacing":{"blockGap":"0"},"color":{"background":"#ffffff","text":"#143d36"},"border":{"radius":"0px","width":"1px","color":"#dfe5ec"},"shadow":"0 8px 28px rgba(20,61,54,0.08)"},"layout":{"type":"constrained"}} --><div class="wp-block-group epl-pattern-card epl-pattern-card-list has-text-color has-background has-border-color" style="border-color:#dfe5ec;border-width:1px;border-radius:0;color:#143d36;background-color:#ffffff;box-shadow:0 8px 28px rgba(20,61,54,0.08)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} --><div class="wp-block-columns"><!-- wp:column {"width":"42%","className":"epl-pattern-card-media"} --><div class="wp-block-column epl-pattern-card-media" style="flex-basis:42%"><!-- wp:epl/property-element {"element":"image","imageSize":"large","showStickers":false} /--><!-- wp:epl/property-element {"element":"status"} /--></div><!-- /wp:column --><!-- wp:column {"width":"58%"} --><div class="wp-block-column" style="flex-basis:58%"><!-- wp:group {"className":"epl-pattern-card-content","style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"blockGap":"12px"}},"layout":{"type":"constrained"}} --><div class="wp-block-group epl-pattern-card-content" style="padding-top:24px;padding-right:24px;padding-bottom:24px;padding-left:24px"><!-- wp:epl/property-element {"element":"title","style":{"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"700","lineHeight":"1.35","textDecoration":"none"}}} /--><!-- wp:epl/property-element {"element":"price","style":{"color":{"text":"#ef3123"},"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"700","lineHeight":"1.3"}}} /--><!-- wp:epl/property-element {"element":"icons","style":{"typography":{"fontSize":"0.9rem"}}} /--></div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
				)
			);

			register_block_pattern(
				'epl/listing-card-grid',
				array(
					'title'       => __( 'EPL Listing Card — Grid', 'easy-property-listings' ),
					'description' => __( 'A premium portrait listing card for multi-column listing grids.', 'easy-property-listings' ),
					'categories'  => array( 'epl' ),
					'blockTypes'  => array( 'core/post-template' ),
					'content'     => '<!-- wp:group {"className":"epl-pattern-card epl-pattern-card-grid","style":{"spacing":{"blockGap":"0"},"color":{"background":"#ffffff","text":"#143d36"},"border":{"radius":"0px","width":"1px","color":"#dfe5ec"},"shadow":"0 8px 28px rgba(20,61,54,0.08)"},"layout":{"type":"constrained"}} --><div class="wp-block-group epl-pattern-card epl-pattern-card-grid has-text-color has-background has-border-color" style="border-color:#dfe5ec;border-width:1px;border-radius:0;color:#143d36;background-color:#ffffff;box-shadow:0 8px 28px rgba(20,61,54,0.08)"><!-- wp:group {"className":"epl-pattern-card-media","layout":{"type":"constrained"}} --><div class="wp-block-group epl-pattern-card-media"><!-- wp:epl/property-element {"element":"image","imageSize":"large","showStickers":false} /--><!-- wp:epl/property-element {"element":"status"} /--></div><!-- /wp:group --><!-- wp:group {"className":"epl-pattern-card-content","style":{"spacing":{"padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"blockGap":"12px"}},"layout":{"type":"constrained"}} --><div class="wp-block-group epl-pattern-card-content" style="padding-top:24px;padding-right:24px;padding-bottom:24px;padding-left:24px"><!-- wp:epl/property-element {"element":"title","style":{"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"700","lineHeight":"1.35","textDecoration":"none"}}} /--><!-- wp:epl/property-element {"element":"price","style":{"color":{"text":"#ef3123"},"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"700","lineHeight":"1.3"}}} /--><!-- wp:epl/property-element {"element":"icons","style":{"typography":{"fontSize":"0.9rem"}}} /--></div><!-- /wp:group --></div><!-- /wp:group -->',
				)
			);

			// Query Loop's "Change design" picker only accepts patterns whose
			// root block is core/query. Re-register the two card designs as scoped
			// EPL Query patterns so generic post patterns are excluded whenever the
			// active Query variation has the epl/listings-query namespace.
			$pattern_registry = WP_Block_Patterns_Registry::get_instance();
			$list_card        = $pattern_registry->get_registered( 'epl/listing-card-list' );
			$grid_card        = $pattern_registry->get_registered( 'epl/listing-card-grid' );
			$status_pattern   = '<!-- wp:epl/property-element {"element":"status","style":{"spacing":{"margin":{"top":"16px","left":"0px"},"padding":{"top":"0.52em","right":"0.82em","bottom":"0.52em","left":"0.82em"}},"color":{"text":"#ffffff"},"typography":{"fontSize":"1rem","fontStyle":"normal","fontWeight":"500","lineHeight":"1"}}} /-->';
			$list_card['content'] = str_replace( '<!-- wp:epl/property-element {"element":"status"} /-->', $status_pattern, $list_card['content'] );
			$grid_card['content'] = str_replace( '<!-- wp:epl/property-element {"element":"status"} /-->', $status_pattern, $grid_card['content'] );
			unregister_block_pattern( 'epl/listing-card-list' );
			unregister_block_pattern( 'epl/listing-card-grid' );

			register_block_pattern(
				'epl/listing-card-list',
				array(
					'title'       => __( 'EPL Listings — List', 'easy-property-listings' ),
					'description' => __( 'A one-column listing stream with premium horizontal image and details cards.', 'easy-property-listings' ),
					'categories'  => array( 'epl' ),
					'blockTypes'  => array( 'core/query', 'core/query/epl/listings-query' ),
					'content'     => $this->listing_query_pattern_content( $list_card['content'], 1 ),
				)
			);

			register_block_pattern(
				'epl/listing-card-grid',
				array(
					'title'       => __( 'EPL Listings — Grid', 'easy-property-listings' ),
					'description' => __( 'A responsive portrait-card grid with a configurable column count.', 'easy-property-listings' ),
					'categories'  => array( 'epl' ),
					'blockTypes'  => array( 'core/query', 'core/query/epl/listings-query' ),
					'content'     => $this->listing_query_pattern_content( $grid_card['content'], 3 ),
				)
			);

			$single_listing_pattern = EPL_Block_Template_Utils::get_template_content( 'single-property' );
			$single_listing_pattern = is_string( $single_listing_pattern )
				? preg_replace( '/<!-- wp:template-part\b[^>]*\/-->\s*/', '', trim( $single_listing_pattern ) )
				: '';

			register_block_pattern(
				'epl/single-listing-essentials',
				array(
					'title'      => __( 'EPL Single Listing — Essentials', 'easy-property-listings' ),
					'categories' => array( 'epl' ),
					'content'    => $single_listing_pattern,
				)
			);

			register_block_pattern(
				'epl/single-staff-profile',
				array(
					'title'      => __( 'EPL Single Staff Profile', 'easy-property-listings' ),
					'categories' => array( 'epl' ),
					'content'    => '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} --><main class="wp-block-group"><!-- wp:columns {"verticalAlignment":"center"} --><div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"35%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%"><!-- wp:epl/agent-element {"element":"photo","imageSize":"large","link":false} /--></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"65%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%"><!-- wp:epl/agent-element {"element":"name","link":false,"style":{"typography":{"fontSize":"2.5rem","fontStyle":"normal","fontWeight":"700"}}} /--><!-- wp:epl/agent-element {"element":"position"} /--><!-- wp:epl/agent-element {"element":"contact"} /--><!-- wp:epl/agent-element {"element":"social"} /--></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:epl/agent-element {"element":"bio"} /--><!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'My Listings', 'easy-property-listings' ) . '</h2><!-- /wp:heading --><!-- wp:epl/agent-element {"element":"listings"} /--></main><!-- /wp:group -->',
				)
			);
		}

		/**
		 * Wrap an EPL listing card in a complete Query Loop design pattern.
		 *
		 * @param string $card_content Serialized listing card blocks.
		 * @param int    $columns      Post Template column count.
		 * @return string
		 */
		private function listing_query_pattern_content( $card_content, $columns ) {
			return '<!-- wp:query {"query":{"perPage":6,"pages":0,"offset":0,"postType":"property","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"namespace":"epl/listings-query","className":"epl-listings-query"} --><div class="wp-block-query epl-listings-query">'
				. '<!-- wp:epl/archive-element {"element":"loop-start"} /-->'
				. '<!-- wp:post-template {"style":{"spacing":{"blockGap":"32px"}},"layout":{"type":"grid","columnCount":' . absint( $columns ) . '}} -->'
				. $card_content
				. '<!-- /wp:post-template -->'
				. '<!-- wp:epl/archive-element {"element":"loop-end"} /-->'
				. '<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} --><!-- wp:query-pagination-previous /--><!-- wp:query-pagination-numbers /--><!-- wp:query-pagination-next /--><!-- /wp:query-pagination -->'
				. '<!-- wp:query-no-results --><!-- wp:paragraph --><p>' . esc_html__( 'No listings found.', 'easy-property-listings' ) . '</p><!-- /wp:paragraph --><!-- /wp:query-no-results -->'
				. '</div><!-- /wp:query -->';
		}
	}

        EPL_Register_Blocks::instance();
endif;
