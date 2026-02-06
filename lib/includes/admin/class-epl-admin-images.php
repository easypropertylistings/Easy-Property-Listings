<?php

/**
* Base class for handling images for listings in the admin panel.
*
* @package     EPL
* @subpackage  Admin/Classes/EPL_Admin_Images
* @copyright   Copyright (c) 2025, Merv Barrett
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       3.5.16
*/

if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
}

/**
 * EPL_Admin_Images Class
 *
 * @since 3.5.16
 */
if ( ! class_exists( 'EPL_Admin_Images' ) ) :
	class EPL_Admin_Images {
		
		public $config;
		
		// Static properties to manage all instances
		private static $registered_extensions   = [];
		private static $save_hook_added         = false;
		
		public function __construct( $config = [] ) {
			
			$defaults = [
				'extension'      => 'slider',
				'prefix'         => 'epl_slider_',
				'title'          => __( 'Slider Images', 'easy-property-listings' ),
				'button_label'   => __( 'Add to Slider', 'easy-property-listings' ),
				'order_meta_key' => 'epl_slides_order'
			];
			
			$this->config = wp_parse_args( $config, $defaults );
			
			// Register this instance
			self::$registered_extensions[$this->get_config('extension')] = $this;
			
			// Add the save hook only once for all instances
			if ( !self::$save_hook_added ) {
				add_action( 'save_post', [__CLASS__, 'save_all_extensions_data'] );
				self::$save_hook_added = true;
			}
			
			// Add individual hooks (non-conflicting ones)
			$this->add_individual_hooks();
		}
		
		public function get_config( $key = '' ) {
			
			if( empty( $key ) ) {
				return $this->config;
			}
			
			if( isset( $this->config[ $key ] ) ) {
				return $this->config[ $key ];
			}
			
			return false;
		}
		
		private function add_individual_hooks() {
			
			add_action('add_meta_boxes', [$this, 'add_meta_box' ] );
			add_action('wp_ajax_epl_images_save_order', [$this, 'save_image_order' ] );
			add_action('wp_ajax_epl_images_unattach', [$this, 'unattach_image' ] );
			add_action( 'add_attachment' , [$this, 'update_image_order_on_new_attachment' ] );
		}
		
		/**
		* Static method to handle saving for all registered extensions
		*
		* @since 3.5.16
		*/
		public static function save_all_extensions_data( $post_id ) {
			
			// Basic validation
			if ( empty( $post_id ) || !is_numeric( $post_id ) ) {
				return;
			}
	
			$post = get_post( $post_id );
	
			if ( !is_epl_post( $post->post_type ) ) {
				return;
			}
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			if ( !current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			// Process each registered extension
			foreach ( self::$registered_extensions as $extension ) {
				$extension->process_save_data( $post_id );
			}
		}
		
		/**
		* Process save data for this specific extension
		*
		* @since 3.5.16
		*/
		public function process_save_data( $post_id ) {
			
			$prefix = $this->get_config('prefix');
			$enabled_thumbs_key = $prefix . 'enabled_thumbs';
			// Only process if this extension's data exists in POST
			if ( !isset( $_POST[$enabled_thumbs_key] ) ) {
				return;
			}
			
			// Mark selection made
			update_post_meta( $post_id, $prefix . 'selection_made', true );
			
			// Save enabled thumbnails
			if ( !empty( $_POST[$enabled_thumbs_key] ) ) {
				$enabled_thumbs = array_map( 'absint', $_POST[$enabled_thumbs_key] );
				$enabled_thumbs = array_filter( $enabled_thumbs );
				update_post_meta( $post_id, $enabled_thumbs_key, $enabled_thumbs );
			} else {
				update_post_meta( $post_id, $enabled_thumbs_key, array() );
			}
		}
		
		public function is_external_link( $url ) {
			
			$s = get_option( 'siteurl' );
			if ( $s != substr( $url, 0, strlen( $s ) ) ) {
				return true;
			}
			return false;
		}
		
		/**
		* Add meta box for extension
		*
		* @since 2.0
		*/
		function add_meta_box() {
			
			add_meta_box(
				'epl_images_management_'.$this->get_config('extension'),
				$this->get_config('title'),
				[ $this, 'epl_images_management_callback' ],
				epl_all_post_types()
			);
		}
		
		function get_parent_images( $post ) {
			
			$args = array(
				'post_parent'    => $post->ID,
				'post_type'      => 'attachment',
				'numberposts'    => -1,
				'post_mime_type' => 'image',
				'orderby'        => 'ID',
				'fields'         => 'ids'
			);
			
			return get_posts( $args );
		}
		
		/**
		* Attachments Callback for adding, disabling and ordering images.
		*
		* @since 3.5.16
		*/
		function epl_images_management_callback($post) {
			
			$parent_attachments = $this->get_parent_images( $post );
			
			$args = array(
				'post_type'      => 'attachment',
				'numberposts'    => -1,
				'post_mime_type' => 'image',
				'orderby'        => 'post__in',
				'post__in'       =>  []    
			);
			
			$attachments = [];
			$initial     = false;
			
			$extension      = $this->get_config('extension');
			$prefix         = $this->get_config('prefix');
			
			$enabled                = get_post_meta( $post->ID, $prefix.'enabled_thumbs', true );
			$selection_made         = get_post_meta($post->ID, $prefix.'selection_made', true);
			
			if ( empty( $enabled ) && !$selection_made ) {
				$initial = true;
				$enabled = (array) $enabled;
			}
			
			$enabled = array_filter( (array) $enabled );
			$enabled = array_map( 'intval', $enabled );
			
			$custom_images = array_diff($enabled, $parent_attachments);
			$all_images    = array_merge( $parent_attachments, $custom_images );
			$post_not_in   = [];
			
			// check if listing has floorplan image
			$floorplan = get_post_meta( $post->ID, 'property_floorplan', true );
			$floorplan_id = '';
			if( !$this->is_external_link( $floorplan ) ) {
				
				// if it's not an external image check if it's an attachment
				$floorplan_id = attachment_url_to_postid( $floorplan );
				
				if( !empty( $floorplan_id ) ) {
					$post_not_in[] = $floorplan_id; 
				}
				
			}
			
			$floorplan_2 = get_post_meta( $post->ID, 'property_floorplan_2', true );
			$floorplan_id_2 = '';
			if( !$this->is_external_link( $floorplan_2 ) ) {
				
				// if it's not an external image check if it's an attachment
				$floorplan_id_2 = attachment_url_to_postid( $floorplan_2 );
				
				if( !empty( $floorplan_id_2 ) ) {
					$post_not_in[] = $floorplan_id_2; 
				}
				
			}
			
			if( !empty( $all_images ) ) {
				
				$args['post__in'] = $all_images;
			}
			
			if( !empty( $post_not_in ) ){
				
				foreach( $post_not_in as $current_not_in ) {
					
					if( !empty( $args['post__in'] ) ) {
						if ( ( $key = array_search( $current_not_in, $args['post__in'] ) ) !== false ) {
							unset( $args['post__in'][$key] );
						}
					}
				}
				
				$args['post__not_in']   = $post_not_in;
			}
			
			if ( get_post_meta( $post->ID, $this->get_config('order_meta_key'), true ) !== '' ) {
				
				$ordered_posts          = get_post_meta( $post->ID, $this->get_config('order_meta_key'), true );
				
				if( !is_array( $ordered_posts ) ) {
					$ordered_posts = explode( ',', $ordered_posts );
				}
				$unordered_posts        = array_diff($all_images, $ordered_posts);
				$all_images = array_merge( $ordered_posts, $unordered_posts );
				$args['post__in'] = $all_images;
				$args['orderby']  = 'post__in';
			}
			
			if ( has_post_thumbnail( $post->ID ) ) {
				
				$featured_image = get_post_thumbnail_id( $post->ID );
				
				if ( ! empty( $args['post__in'] ) ) {
					if ( ( $key = array_search( $featured_image, $args['post__in'], true ) ) !== false ) {
						unset( $args['post__in'][ $key ] );
					}
				} else {
					$args['exclude'] = $featured_image;
				}
				
				if( !empty( $args['post__in']) ) {
					$attachments = get_posts( $args );
				}
				
				
			} else {
				if( !empty( $args['post__in']) ) {
					$attachments = get_posts( $args );
				}
			}
			
			if ( empty( $attachments ) ) {
				
				unset( $args['post__in'] );
				$args['orderby'] = 'ID';
				$args['post_parent'] = $post->ID;
			}

			$attachments = get_posts( $args ); ?>
			
			<div class="epl-listing-attachments-list-wrap">
	
				<?php if ( $attachments ) : ?>
					<ul 
						data-prefix="<?php echo esc_attr( $prefix ); ?>" 
						data-extension="<?php echo esc_attr( $extension ); ?>"  
						id="epl-<?php echo esc_attr( $extension ); ?>-post-attachments" 
						class="epl-<?php echo esc_attr( $extension ); ?>-post-attachments epl-listing-attachments-list"
					>
						<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>enabled_thumbs[]" value="" />
	
						<?php foreach ( $attachments as $attachment ) : ?>
							<?php
							// Determine checked status
							$checked = $initial ? 'checked=checked' : '';
							
							if ( ! empty( $enabled ) ) {
								$checked = in_array( $attachment->ID, $enabled ) ? 'checked=checked' : '';
							}
	
							// Get thumbnail details
							$thumb_size = epl_get_option( $prefix . 'admin_image_size', 'admin-list-thumb' );
							$thumb      = wp_get_attachment_image_src( $attachment->ID, $thumb_size ); ?>
	
							<li data-id="<?php echo esc_attr( $attachment->ID ); ?>"  class="ui-state-default epl-listing-attachments-list-item <?php echo esc_attr( $prefix ); ?>admin_thumb">
	
								<span class="epl-slider-unattach">
									<div class="epl-radio-switch">
									<input 
										id="epl-<?php echo esc_attr( $extension ); ?>-cmn-toggle-<?php echo esc_attr( $attachment->ID ); ?>" 
										class="epl-radio-switch-input epl-radio-switch-input--yes-no" 
										name="<?php echo esc_attr( $prefix ); ?>enabled_thumbs[]" 
										<?php echo esc_attr( $checked ); ?> 
										value="<?php echo esc_attr( $attachment->ID ); ?>" 
										type="checkbox"
									/>
									<label for="epl-<?php echo esc_attr( $extension ); ?>-cmn-toggle-<?php echo esc_attr( $attachment->ID ); ?>"></label>
									</div>
								</span>
	
								<div class="epl-<?php echo esc_attr( $extension ); ?>-slide-tools epl-listing-attachment-tools">
									<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . $attachment->ID . '&action=edit' ) ); ?>">
									<span class="epl-<?php echo esc_attr( $extension ); ?>-edit-attach dashicons dashicons-edit"></span>
									</a>
									<a href="#" class="epl-slider-<?php echo esc_attr( $extension ); ?>-delete-attach epl-listing-attachment-tools-delete-attach">
									<span class="dashicons dashicons-trash"></span>
									</a>
								</div>
								
								<img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php esc_attr_e( 'Attachment Thumbnail', 'easy-property-listings' ); ?>" />
							</li>
						<?php endforeach; ?>
	
					</ul>
					<div class="epl-clearfix"></div>
					<?php echo $this->extension_messages( $post ); ?>
	
				<?php else : ?>
	
					<div class="update-nag">
						<?php esc_html_e( 'Add images to the slider using add media button.', 'easy-property-listings' ); ?>
					</div>
					<ul id="epl-<?php echo esc_attr( $extension ); ?>-post-attachments" class="epl-<?php echo esc_attr( $extension ); ?>-post-attachments"></ul>
					<div class="epl-clearfix"></div>
					
				<?php endif; ?>
	
				<div class="update-nag">
					<?php esc_html_e( 'Drag to reorder images and use the switch to remove images from the slider. Update the post to save changes.', 'easy-property-listings' ); ?>
				</div>
				<input 
					type="button" 
					class="btn button epl-<?php echo esc_attr( $extension ); ?>-upload-btn epl-listing-images-upload-btn" 
					name="<?php echo esc_attr( $prefix ); ?>upload_button" 
					value="<?php echo esc_attr( $this->get_config( 'button_label' ) ); ?>" 
				/>
			</div> <?php
	
		}
	
		public function extension_messages( $post ) {
			
			ob_start();
			do_action( 'epl_image_extension_messages', $this->config, $post );
			return ob_get_clean();
		}
		
		/**
		* Save Image Order
		*
		* @since 1.0.0
		*/
		function save_image_order() {
			
			if ( is_admin() ) {
				// Verify nonce
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'epl_ajax_nonce' ) ) {
					wp_send_json_error( 'Invalid nonce' );
				}

				// Verify capability
				if ( ! current_user_can( 'edit_posts' ) ) {
					wp_send_json_error( 'Unauthorized' );
				}

				if ( isset( $_POST['id'] ) && intval( $_POST['id'] ) > 0 ) {
					$order = sanitize_text_field( $_POST['order'] );
					
					// Get extension from POST data to determine correct order meta key
					$extension = isset( $_POST['extension'] ) ? sanitize_text_field( $_POST['extension'] ) : 'slider';
					
					// Find the registered extension instance
					$order_meta_key = 'epl_slides_order'; // default fallback
					if ( isset( self::$registered_extensions[$extension] ) ) {
						$order_meta_key = self::$registered_extensions[$extension]->get_config('order_meta_key');
					} else {
						// Fallback: construct meta key based on extension
						$order_meta_key = 'epl_' . $extension . '_order';
					}
					
					update_post_meta( intval( $_POST['id'] ), $order_meta_key, esc_attr( $order ) );
				}
			}
			die( $order );
		}
		
		/**
		* Detach Images
		*
		* @since 3.5.16
		*/
		function unattach_image() {
	
			$prefix         = wp_unslash( $_POST['prefix'] );
			$prefix         = !empty( $prefix ) ? sanitize_text_field( $prefix ) : $this->get_config('prefix');
			$image_id       = isset( $_POST['img_id'] ) ? absint( $_POST['img_id'] ) : 0;
			$post_id        = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	
			if ( is_admin() && !empty( $image_id ) && !empty( $post_id ) ) {
				// Verify nonce
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'epl_ajax_nonce' ) ) {
					wp_die( 'Invalid nonce' );
				}

				// Verify capability
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					wp_die( 'Unauthorized' );
				}
				
				// Get current parent images to check if this is a custom image
				$parent_attachments = $this->get_parent_images( get_post( $post_id ) );
				$is_custom_image = !in_array( $image_id, $parent_attachments );
				
				// Only detach if it's actually a child of this post
				if ( !$is_custom_image ) {
					wp_update_post(
						array(
							'ID'          => $image_id,
							'post_parent' => 0,
						)
					);
				}
				
				// Remove from enabled thumbs (this handles both custom and parent images)
				$enabled = get_post_meta( $post_id, $prefix.'enabled_thumbs', true );
				if ( ! empty( $enabled ) ) {
					$key = array_search( $image_id, $enabled );
					if ( $key !== false ) {
						unset( $enabled[ $key ] );
						// Re-index array to prevent gaps
						$enabled = array_values( $enabled );
						update_post_meta( $post_id, $prefix.'enabled_thumbs', $enabled );
					}
				}
				
				// Remove from slide order
				// Get extension to determine correct order meta key
				$extension = isset( $_POST['extension'] ) ? sanitize_text_field( $_POST['extension'] ) : 'slider';
				$order_meta_key = 'epl_slides_order'; // default fallback
				if ( isset( self::$registered_extensions[$extension] ) ) {
					$order_meta_key = self::$registered_extensions[$extension]->get_config('order_meta_key');
				} else {
					// Fallback: construct meta key based on extension
					$order_meta_key = 'epl_' . $extension . '_order';
				}
				
				$order = get_post_meta( $post_id, $order_meta_key, true );
				if ( ! empty( $order ) ) {
					$order_array = is_array( $order ) ? $order : explode( ',', $order );
					$key = array_search( $image_id, $order_array );
					if ( $key !== false ) {
						unset( $order_array[ $key ] );
						// Re-index array and update
						$order_array = array_values( $order_array );
						update_post_meta( $post_id, $order_meta_key, implode( ',', $order_array ) );
					}
				}
			}
			die;
		}
			
		/**
		* Update slide order on adding new attachment
		*
		* @since 3.5.16
		*/
		function update_image_order_on_new_attachment( $post_ID ) {
			
			$post   = get_post( $post_ID );
			$parent = get_post( $post->post_parent );
			
			if( is_null( $parent ) ) {
				return;
			}
	
			// Update order for all registered extensions
			foreach ( self::$registered_extensions as $extension ) {
				$order_meta_key = $extension->get_config('order_meta_key');
				$order = get_post_meta( $parent->ID, $order_meta_key, true );
	
				if ( !empty( $order ) ) {
					update_post_meta( $parent->ID, $order_meta_key, $order . ',' . $post_ID );
				}
			}
			
		}
	}
endif;
