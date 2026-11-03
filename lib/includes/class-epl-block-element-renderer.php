<?php
/**
 * Composable Gutenberg element renderers.
 *
 * @package EPL
 * @subpackage Block
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EPL_Block_Element_Renderer' ) ) :

	/**
	 * Renders listing and agent elements with a temporary, isolated EPL context.
	 */
	class EPL_Block_Element_Renderer {

		/** @var array Previous render globals. */
		private $previous = array();

		/**
		 * Render a property element block.
		 *
		 * @param array    $attributes Block attributes.
		 * @param string   $content    Saved content.
		 * @param WP_Block $block      Block instance.
		 * @return string
		 */
		public function property_element( $attributes, $content, $block ) {
			$post_id = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
			$post    = $this->enter_context( $post_id, sanitize_key( $attributes['previewPostType'] ?? 'property' ), false );

			if ( ! $post ) {
				$this->leave_context();
				return $this->placeholder( __( 'No EPL listing is available for preview.', 'easy-property-listings' ) );
			}

			$element = sanitize_key( $attributes['element'] ?? 'title' );
			if ( 'status' === $element ) {
				$status = $this->resolve_status( $attributes );
				$this->leave_context();

				if ( empty( $status ) ) {
					return $this->placeholder( __( 'This listing has no content for the selected element.', 'easy-property-listings' ) );
				}

				$wrapper_attributes = array(
					'class' => 'epl-property-element epl-property-element-status epl-property-status-label epl-property-status-kind-' . $status['kind'],
				);
				if ( $status['color'] ) {
					$wrapper_attributes['style'] = 'background-color:' . $status['color'];
				}

				return sprintf(
					'<span %1$s>%2$s</span>',
					get_block_wrapper_attributes( $wrapper_attributes ),
					esc_html( $status['label'] )
				);
			}
			if ( 'title' === $element ) {
				$title = get_the_title( $post );
				$link  = ! $this->is_editor_request() && ! empty( $attributes['link'] );
				$this->leave_context();
				$wrapper_attributes = array(
					'class' => 'epl-property-element epl-property-element-title',
				);

				if ( $link ) {
					$wrapper_attributes['href'] = get_permalink( $post );

					return sprintf( '<a %1$s>%2$s</a>', get_block_wrapper_attributes( $wrapper_attributes ), esc_html( $title ) );
				}

				return sprintf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( $wrapper_attributes ), esc_html( $title ) );
			}

			ob_start();
			$this->render_property_element( $element, $attributes, $post );
			$output = ob_get_clean();
			$this->leave_context();

			if ( '' === trim( $output ) ) {
				return $this->placeholder( __( 'This listing has no content for the selected element.', 'easy-property-listings' ) );
			}

			$wrapper_attributes = array( 'class' => 'epl-property-element epl-property-element-' . $element );
			if ( 'image' === $element && ! empty( $attributes['enableImageHoverEffects'] ) ) {
				$wrapper_attributes['class'] .= ' epl-has-image-effects';
				$wrapper_attributes['style']  = $this->image_hover_style( $attributes );
			}

			return sprintf(
				'<div %1$s>%2$s</div>',
				get_block_wrapper_attributes( $wrapper_attributes ),
				$output
			);
		}

		/**
		 * Build sanitized CSS custom properties for optional image hover effects.
		 *
		 * @param array $attributes Block attributes.
		 * @return string
		 */
		private function image_hover_style( $attributes ) {
			$values = array(
				'scale'      => array( 'imageHoverZoom', 100, 150, 105, '' ),
				'blur'       => array( 'imageHoverBlur', 0, 20, 0, 'px' ),
				'brightness' => array( 'imageHoverBrightness', 0, 200, 100, '%' ),
				'contrast'   => array( 'imageHoverContrast', 0, 200, 100, '%' ),
				'saturate'   => array( 'imageHoverSaturation', 0, 300, 100, '%' ),
				'grayscale'  => array( 'imageHoverGrayscale', 0, 100, 0, '%' ),
				'opacity'    => array( 'imageHoverOpacity', 0, 100, 100, '%' ),
				'duration'   => array( 'imageHoverDuration', 0, 3, 0.3, 's' ),
			);
			$style = '';

			foreach ( $values as $property => $settings ) {
				$value  = isset( $attributes[ $settings[0] ] ) && is_numeric( $attributes[ $settings[0] ] ) ? (float) $attributes[ $settings[0] ] : $settings[3];
				$value  = max( $settings[1], min( $settings[2], $value ) );
				$number = rtrim( rtrim( number_format( $value, 2, '.', '' ), '0' ), '.' );
				$style .= '--epl-image-hover-' . $property . ':' . $number . $settings[4] . ';';
			}

			return $style;
		}

		/**
		 * Render an agent element block.
		 *
		 * @param array    $attributes Block attributes.
		 * @param string   $content    Saved content.
		 * @param WP_Block $block      Block instance.
		 * @return string
		 */
		public function agent_element( $attributes, $content, $block ) {
			$post_id = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
			$post    = $this->enter_context( $post_id, sanitize_key( $attributes['previewPostType'] ?? 'property' ), true );
			$agent   = $post ? $this->resolve_agent( max( 1, min( 4, absint( $attributes['agentIndex'] ?? 1 ) ) ) ) : null;

			if ( ! $agent ) {
				$this->leave_context();
				return $this->placeholder( __( 'No agent is available for preview.', 'easy-property-listings' ) );
			}

			$element = sanitize_key( $attributes['element'] ?? 'card' );
			ob_start();
			$this->render_agent_element( $element, $agent, $attributes );
			$output = ob_get_clean();
			$this->leave_context();

			if ( '' === trim( $output ) ) {
				return $this->placeholder( __( 'This agent has no content for the selected element.', 'easy-property-listings' ) );
			}

			return sprintf(
				'<div %1$s>%2$s</div>',
				get_block_wrapper_attributes( array( 'class' => 'epl-agent-element epl-agent-element-' . $element ) ),
				$output
			);
		}

		/**
		 * Render an EPL archive lifecycle hook or the default listing loop item.
		 *
		 * @param array    $attributes Block attributes.
		 * @param string   $content    Saved content.
		 * @param WP_Block $block      Block instance.
		 * @return string
		 */
		public function archive_element( $attributes, $content, $block ) {
			$element = sanitize_key( $attributes['element'] ?? 'loop-start' );
			$actions = array(
				'archive-title' => 'epl_the_archive_title',
				'loop-start'    => 'epl_property_loop_start',
				'loop-end'      => 'epl_property_loop_end',
				'view-switch'    => 'epl_switch_views',
				'sorting-tool'   => 'epl_sorting_tool',
				'pagination'    => 'epl_pagination',
				'not-found'     => 'epl_property_search_not_found',
			);

			ob_start();
			if ( 'archive-title' === $element && $this->is_editor_request() ) {
				// The legacy title callback calls the_post()/rewind_posts(). Site
				// Editor REST previews do not contain a complete main WP_Query, so
				// advancing it can pass null posts into WP_Query::the_post().
				$post_type = sanitize_key( $attributes['previewPostType'] ?? 'property' );
				$object    = get_post_type_object( $post_type );
				$title     = $object && ! empty( $object->labels->name ) ? $object->labels->name : __( 'Listings', 'easy-property-listings' );
				$title     = apply_filters( 'epl_block_archive_title_preview', $title, $post_type, $attributes );
				echo esc_html( $title );
			} elseif ( 'pagination' === $element && $this->is_editor_request() ) {
				$this->render_pagination_preview();
			} elseif ( 'pagination' === $element && ! empty( $block->context['query'] ) ) {
				echo $this->render_query_pagination( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} elseif ( 'listing' === $element ) {
				$post_id = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
				$post    = $this->enter_context( $post_id, sanitize_key( $attributes['previewPostType'] ?? 'property' ), false );
				if ( $post ) {
					do_action( 'epl_property_blog' );
				}
				$this->leave_context();
			} elseif ( isset( $actions[ $element ] ) ) {
				$hook_attributes                = $attributes;
				$hook_attributes['instance_id'] = sanitize_key( $attributes['instanceId'] ?? '1' ) ?: '1';

				if ( 'loop-start' === $element ) {
					$show_switch     = ! isset( $attributes['showViewSwitch'] ) || ! empty( $attributes['showViewSwitch'] );
					$show_sorting    = ! isset( $attributes['showSorting'] ) || ! empty( $attributes['showSorting'] );
					$show_additional = ! isset( $attributes['showAdditionalTools'] ) || ! empty( $attributes['showAdditionalTools'] );
					$toolbar_filter  = static function( $tools ) use ( $show_switch, $show_sorting, $show_additional ) {
						return array_values(
							array_filter(
								(array) $tools,
								static function( $tool ) use ( $show_switch, $show_sorting, $show_additional ) {
									if ( 'switch_views' === $tool ) {
										return $show_switch;
									}
									if ( 'sorting_tool' === $tool ) {
										return $show_sorting;
									}
									return $show_additional;
								}
							)
						);
					};
					add_filter( 'epl_listing_toolbar_items', $toolbar_filter, PHP_INT_MAX );
					do_action( $actions[ $element ], $hook_attributes );
					remove_filter( 'epl_listing_toolbar_items', $toolbar_filter, PHP_INT_MAX );
				} elseif ( 'sorting-tool' === $element ) {
					do_action( $actions[ $element ], $hook_attributes );
				} else {
					do_action( $actions[ $element ] );
				}
			}
			$output = ob_get_clean();

			if ( '' === trim( $output ) ) {
				// Loop end is a lifecycle extension point and commonly emits no
				// markup. Empty output is successful and should not be presented as
				// an editor warning.
				if ( 'loop-end' === $element ) {
					return '';
				}

				return $this->is_editor_request()
					? $this->placeholder( sprintf( /* translators: %s: archive element name. */ __( 'The %s hook produced no preview output.', 'easy-property-listings' ), str_replace( '-', ' ', $element ) ) )
					: '';
			}

			$tag = 'archive-title' === $element ? 'h1' : 'div';
			return sprintf(
				'<%1$s %2$s>%3$s</%1$s>',
				$tag,
				get_block_wrapper_attributes( array( 'class' => 'epl-archive-element epl-archive-element-' . $element ) ),
				$output
			);
		}

		/** Render a stable pagination example in the iframe editor preview. */
		private function render_pagination_preview() {
			?>
			<nav class="epl-pagination epl-block-pagination-preview" aria-label="<?php esc_attr_e( 'Pagination preview', 'easy-property-listings' ); ?>">
				<span><?php esc_html_e( 'Previous Page', 'easy-property-listings' ); ?></span>
				<span aria-current="page">1</span>
				<span>2</span>
				<span><?php esc_html_e( 'Next Page', 'easy-property-listings' ); ?></span>
			</nav>
			<?php
		}

		/**
		 * Render pagination against the containing Query block rather than the
		 * unrelated global query used by EPL's classic archive templates.
		 *
		 * @param WP_Block $block Current EPL pagination block.
		 * @return string
		 */
		private function render_query_pagination( $block ) {
			$blocks = parse_blocks(
				'<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->'
				. '<!-- wp:query-pagination-previous /-->'
				. '<!-- wp:query-pagination-numbers /-->'
				. '<!-- wp:query-pagination-next /-->'
				. '<!-- /wp:query-pagination -->'
			);
			if ( empty( $blocks[0] ) ) {
				return '';
			}

			$pagination = new WP_Block( $blocks[0], $block->context );

			return $pagination->render();
		}

		/** Establish the current block listing or staff post. */
		private function enter_context( $post_id, $preview_post_type, $allow_directory ) {
			global $post, $property, $epl_author, $epl_author_secondary, $epl_author_third, $epl_author_fourth;

			$this->previous = array(
				'post'                 => $post ?? null,
				'property'             => $property ?? null,
				'epl_author'           => $epl_author ?? null,
				'epl_author_secondary' => $epl_author_secondary ?? null,
				'epl_author_third'     => $epl_author_third ?? null,
				'epl_author_fourth'    => $epl_author_fourth ?? null,
			);

			$types = array_keys( epl_get_active_post_types() );
			if ( $allow_directory && post_type_exists( 'directory' ) ) {
				$types[] = 'directory';
			}

			$context_post = $post_id ? get_post( $post_id ) : null;
			if ( ! $context_post && $post instanceof WP_Post && in_array( $post->post_type, $types, true ) ) {
				$context_post = $post;
			}
			if ( ! $context_post || ! in_array( $context_post->post_type, $types, true ) ) {
				$preview_post_type = in_array( $preview_post_type, $types, true ) ? $preview_post_type : ( $types[0] ?? 'property' );
				$posts             = get_posts(
					array(
						'post_type'      => $preview_post_type,
						'post_status'    => 'publish',
						'posts_per_page' => 1,
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);
				$context_post      = $posts[0] ?? null;
			}

			if ( ! $context_post ) {
				return null;
			}

			$post = $context_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $post );
			do_action( 'the_post', $post );

			return $context_post;
		}

		/** Restore globals changed while rendering the block. */
		private function leave_context() {
			global $post;

			foreach ( $this->previous as $key => $value ) {
				$GLOBALS[ $key ] = $value;
			}

			$post = $this->previous['post'] ?? null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			if ( $post instanceof WP_Post ) {
				setup_postdata( $post );
			} else {
				wp_reset_postdata();
			}
		}

		/** Render one property element. */
		private function render_property_element( $element, $attributes, $post ) {
			global $property;

			switch ( $element ) {
				case 'image':
					$link     = ! $this->is_editor_request() && ! empty( $attributes['link'] );
					$stickers = ! isset( $attributes['showStickers'] ) || ! empty( $attributes['showStickers'] );
					do_action( 'epl_property_archive_featured_image', sanitize_key( $attributes['imageSize'] ?? 'large' ), 'epl-block-property-image', $link, $stickers );
					break;
				case 'gallery':
					do_action( 'epl_property_gallery' );
					break;
				case 'address':
					do_action( 'epl_property_address' );
					break;
				case 'price':
					do_action( 'epl_property_price' );
					break;
				case 'icons':
					$this->render_editor_svg_symbols( true, false );
					do_action( 'epl_property_icons', sanitize_key( $attributes['iconStyle'] ?? 'i' ) );
					break;
				case 'heading':
					do_action( 'epl_property_heading' );
					break;
				case 'meta':
					$key   = sanitize_key( $attributes['metaKey'] ?? '' );
					$value = $key && is_object( $property ) ? $property->get_property_meta( $key ) : '';
					if ( '' !== (string) $value ) {
						$this->render_meta( $value, $attributes );
					}
					break;
				case 'category':
					$tag       = sanitize_key( $attributes['categoryTag'] ?? 'div' );
					$tag       = in_array( $tag, array( 'div', 'span', 'p' ), true ) ? $tag : 'div';
					$classname = $this->sanitize_class_names( $attributes['categoryClass'] ?? 'property-category' );
					do_action( 'epl_property_category', $tag, $classname ?: 'property-category' );
					break;
				case 'taxonomy':
					$taxonomy = sanitize_key( $attributes['taxonomy'] ?? 'location' );
					$terms    = get_the_term_list( $post->ID, $taxonomy, '', (string) ( $attributes['separator'] ?? ', ' ) );
					if ( ! is_wp_error( $terms ) ) {
						echo wp_kses_post( (string) $terms );
					}
					break;
				case 'content':
					echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;
				case 'excerpt':
					echo wp_kses_post( get_the_excerpt( $post ) );
					break;
				case 'inspections':
					do_action( 'epl_property_inspection_times' );
					break;
				case 'map':
					do_action( 'epl_property_map', array() );
					break;
				case 'video':
					do_action( 'epl_property_video', 900 );
					break;
				case 'features':
					do_action( 'epl_property_features' );
					break;
				case 'buttons':
					do_action( 'epl_buttons_single_property' );
					break;
				case 'agents':
					$this->render_editor_svg_symbols( false, true );
					do_action( 'epl_single_author' );
					break;
			}
		}

		/** Sanitize a space-separated HTML class list. */
		private function sanitize_class_names( $classes ) {
			$classes = preg_split( '/\s+/', trim( (string) $classes ) );
			$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

			return implode( ' ', $classes );
		}

		/** Render a configurable meta value and optional Dashicon. */
		private function render_meta( $value, $attributes ) {
			if ( ! empty( $attributes['metaIcon'] ) ) {
				wp_enqueue_style( 'dashicons' );
				$color = sanitize_hex_color( (string) ( $attributes['metaIconColor'] ?? '' ) );
				$size  = max( 8, min( 200, absint( $attributes['metaIconSize'] ?? 20 ) ) );
				$style = 'font-size:' . $size . 'px;width:' . $size . 'px;height:' . $size . 'px;' . ( $color ? 'color:' . $color . ';' : '' );
				echo '<span class="epl-meta-icon dashicons dashicons-' . esc_attr( sanitize_html_class( $attributes['metaIcon'] ) ) . '" style="' . esc_attr( $style ) . '" aria-hidden="true"></span>';
			}

			echo '<span class="epl-meta-value">' . esc_html( (string) ( $attributes['prefix'] ?? '' ) . $value . (string) ( $attributes['suffix'] ?? '' ) ) . '</span>';
		}

		/**
		 * Resolve the current status label and its configured background color.
		 *
		 * @param array $attributes Block attributes.
		 * @return array|null
		 */
		private function resolve_status( $attributes ) {
			global $property;
			if ( ! is_object( $property ) ) {
				return null;
			}

			$status = sanitize_key( (string) $property->get_property_meta( 'property_status' ) ) ?: 'current';
			$kind   = $status;
			$label  = ucfirst( str_replace( '_', ' ', $status ) );

			if ( 'current' === $status && 'yes' === $property->get_property_meta( 'property_under_offer' ) ) {
				$kind  = 'under-offer';
				$label = (string) ( $attributes['underOfferLabel'] ?? __( 'Under Offer', 'easy-property-listings' ) );
			} elseif ( 'current' === $status ) {
				$post_type = get_post_type();
				if ( 'rental' === $post_type ) {
					$kind  = 'current-rental';
					$label = (string) ( $attributes['rentalLabel'] ?? __( 'For Lease', 'easy-property-listings' ) );
				} elseif ( in_array( $post_type, array( 'commercial', 'commercial_land', 'business' ), true ) ) {
					$type  = sanitize_key( (string) $property->get_property_meta( 'property_com_listing_type' ) );
					$kind  = 'lease' === $type ? 'current-commercial-lease' : ( 'both' === $type ? 'current-commercial-both' : 'current-commercial-sale' );
					if ( 'lease' === $type ) {
						$label = (string) ( $attributes['commercialLeaseLabel'] ?? __( 'For Lease', 'easy-property-listings' ) );
					} elseif ( 'both' === $type ) {
						$label = (string) ( $attributes['commercialBothLabel'] ?? __( 'For Sale / Lease', 'easy-property-listings' ) );
					} else {
						$label = (string) ( $attributes['commercialSaleLabel'] ?? __( 'For Sale', 'easy-property-listings' ) );
					}
				} else {
					$kind  = 'current-sale';
					$label = (string) ( $attributes['saleLabel'] ?? __( 'For Sale', 'easy-property-listings' ) );
				}
			} elseif ( 'sold' === $status ) {
				$label = (string) ( $attributes['soldLabel'] ?? __( 'Sold', 'easy-property-listings' ) );
			} elseif ( 'leased' === $status ) {
				$label = (string) ( $attributes['leasedLabel'] ?? __( 'Leased', 'easy-property-listings' ) );
			} elseif ( 'withdrawn' === $status ) {
				$label = (string) ( $attributes['withdrawnLabel'] ?? __( 'Withdrawn', 'easy-property-listings' ) );
			} elseif ( 'offmarket' === $status ) {
				$label = (string) ( $attributes['offmarketLabel'] ?? __( 'Off Market', 'easy-property-listings' ) );
			} elseif ( 'deleted' === $status ) {
				$label = (string) ( $attributes['deletedLabel'] ?? __( 'Deleted', 'easy-property-listings' ) );
			}

			$colors = (array) ( $attributes['statusColors'] ?? array() );
			$color  = isset( $colors[ $kind ] ) ? sanitize_hex_color( $colors[ $kind ] ) : '';

			return array(
				'kind'  => sanitize_html_class( $kind ),
				'label' => $label,
				'color' => $color,
			);
		}

		/** Resolve user or Staff Directory backed agents through existing EPL objects. */
		private function resolve_agent( $index ) {
			$post = get_post();
			if ( $post && 'directory' === $post->post_type && class_exists( 'EPL_SD_Staff' ) ) {
				return new EPL_SD_Staff( $post->ID );
			}

			$keys  = array( 1 => 'epl_author', 2 => 'epl_author_secondary', 3 => 'epl_author_third', 4 => 'epl_author_fourth' );
			$agent = $GLOBALS[ $keys[ $index ] ?? 'epl_author' ] ?? null;
			if ( is_object( $agent ) ) {
				return $agent;
			}

			return $post && class_exists( 'EPL_Author_Meta' ) ? new EPL_Author_Meta( (int) $post->post_author ) : null;
		}

		/** Render one normalized agent element. */
		private function render_agent_element( $element, $agent, $attributes ) {
			$url  = $this->agent_profile_url( $agent );
			$name = $this->agent_value( $agent, 'get_author_name', 'name' );

			switch ( $element ) {
				case 'card':
					$this->render_editor_svg_symbols( false, true );
					do_action( 'epl_single_author', $agent );
					break;
				case 'name':
					if ( ! $this->is_editor_request() && ! empty( $attributes['link'] ) && $url ) {
						printf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $name ) );
					} else {
						echo esc_html( $name );
					}
					break;
				case 'photo':
					$image = $this->agent_image( $agent, sanitize_key( $attributes['imageSize'] ?? 'medium' ) );
					echo ! $this->is_editor_request() && ! empty( $attributes['link'] ) && $url ? '<a href="' . esc_url( $url ) . '">' . wp_kses_post( $image ) . '</a>' : wp_kses_post( $image );
					break;
				case 'position':
					echo esc_html( $this->agent_value( $agent, 'get_author_position', 'position' ) );
					break;
				case 'bio':
					echo wp_kses_post( $this->agent_value( $agent, 'get_description', 'description' ) );
					break;
				case 'contact':
					$this->render_editor_svg_symbols( false, true );
					$this->render_agent_contact( $agent );
					break;
				case 'social':
					$this->render_editor_svg_symbols( false, true );
					$this->render_agent_social( $agent );
					break;
				case 'listings':
					$this->render_agent_listings( $agent, $attributes );
					break;
			}
		}

		/**
		 * Include EPL SVG symbol definitions in server-rendered editor previews.
		 *
		 * Frontend requests receive these symbols from wp_body_open/wp_footer.
		 * Those hooks do not run inside the block editor iframe, leaving SVG use
		 * elements with unresolved fragment identifiers unless included here.
		 *
		 * @param bool $listing Include listing feature symbols.
		 * @param bool $social  Include author/social symbols.
		 * @return void
		 */
		private function render_editor_svg_symbols( $listing, $social ) {
			if ( ! $this->is_editor_request() ) {
				return;
			}

			if ( $listing && function_exists( 'epl_load_svg_listing_icons_head' ) ) {
				epl_load_svg_listing_icons_head();
			}

			if ( $social && function_exists( 'epl_load_svg_social_icons_head' ) ) {
				epl_load_svg_social_icons_head();
			}
		}

		/** Render Staff Directory listings using its public layouts, with a core fallback. */
		private function render_agent_listings( $agent, $attributes ) {
			$directory    = $agent->directory ?? null;
			$directory_id = $directory instanceof WP_Post ? $directory->ID : absint( $directory );
			if ( shortcode_exists( 'listing_directory_listings' ) ) {
				$author = $directory_id ?: absint( $agent->author_id ?? 0 );
				$mode   = in_array( ( $attributes['listingsMode'] ?? '' ), array( '', 'list', 'tabbed' ), true ) ? (string) $attributes['listingsMode'] : '';
				$tabs   = 'vertical' === ( $attributes['tabStyle'] ?? '' ) ? 'vertical' : 'horizontal';
				echo do_shortcode( '[listing_directory_listings author="' . $author . '" mode="' . esc_attr( $mode ) . '" tab_style="' . esc_attr( $tabs ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}

			echo do_shortcode( '[listing_advanced author="' . absint( $agent->author_id ?? 0 ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/** Render normalized contact links. */
		private function render_agent_contact( $agent ) {
			$items = array(
				'mobile'  => array( $this->agent_value( $agent, 'get_author_mobile', 'mobile' ), 'tel:' ),
				'office'  => array( $this->agent_value( $agent, 'get_author_office_phone', 'office_phone' ), 'tel:' ),
				'email'   => array( $this->agent_value( $agent, 'get_email', 'email' ), 'mailto:' ),
				'website' => array( $this->agent_value( $agent, 'get_website', 'website' ), '' ),
			);
			foreach ( $items as $class => $item ) {
				if ( '' !== $item[0] ) {
					printf( '<a class="epl-agent-contact-%1$s" href="%2$s">%3$s</a>', esc_attr( $class ), esc_url( $item[1] . $item[0] ), esc_html( $item[0] ) );
				}
			}
		}

		/** Render available social profile links. */
		private function render_agent_social( $agent ) {
			foreach ( array( 'facebook', 'linkedin', 'twitter', 'instagram', 'pinterest', 'youtube' ) as $network ) {
				$url = isset( $agent->{$network} ) && is_scalar( $agent->{$network} ) ? (string) $agent->{$network} : '';
				if ( $url ) {
					printf( '<a class="epl-agent-social-%1$s" href="%2$s" rel="noopener noreferrer">%3$s</a>', esc_attr( $network ), esc_url( $url ), esc_html( ucfirst( $network ) ) );
				}
			}
		}

		/** Resolve an agent profile URL through extension filters. */
		private function agent_profile_url( $agent ) {
			$author_id = absint( $agent->author_id ?? 0 );
			$url       = $author_id ? get_author_posts_url( $author_id ) : '';
			return (string) apply_filters( 'epl_author_profile_link', $url, $agent );
		}

		/** Resolve a Directory featured image or avatar fallback. */
		private function agent_image( $agent, $size ) {
			$directory    = $agent->directory ?? null;
			$directory_id = $directory instanceof WP_Post ? $directory->ID : absint( $directory );
			if ( $directory_id && has_post_thumbnail( $directory_id ) ) {
				$image = wp_get_attachment_image( get_post_thumbnail_id( $directory_id ), $size, false, array( 'class' => 'epl-agent-photo' ) );
				if ( $image ) {
					return $image;
				}
			}

			$email = $this->agent_value( $agent, 'get_email', 'email' );
			$image = get_avatar( $email ?: absint( $agent->author_id ?? 0 ), 300, '', '', array( 'class' => 'epl-agent-photo' ) );
			return (string) apply_filters( 'epl_author_tab_image', $image, $agent );
		}

		/** Safely access a normalized getter or property. */
		private function agent_value( $agent, $getter, $property ) {
			$value = method_exists( $agent, $getter ) ? $agent->{$getter}() : ( $agent->{$property} ?? '' );
			return is_scalar( $value ) ? (string) $value : '';
		}

		/** Render linked or plain text. */
		private function linked_text( $text, $link ) {
			if ( $link ) {
				printf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink() ), esc_html( $text ) );
			} else {
				echo esc_html( $text );
			}
		}

		/** Editor-only empty-state message. */
		private function placeholder( $message ) {
			return $this->is_editor_request() ? '<div class="epl-block-placeholder">' . esc_html( $message ) . '</div>' : '';
		}

		/** Whether ServerSideRender is requesting an editor preview. */
		private function is_editor_request() {
			return defined( 'REST_REQUEST' ) && REST_REQUEST;
		}
	}

endif;
