<?php
/**
 * EPL Rest API Class.
 *
 * @package     EPL
 * @subpackage  Classes/RestAPI
 * @copyright   Copyright (c) 2024, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.58
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Rest_API Class
 *
 * @since 1.3.0
 */
if ( ! class_exists( 'EPL_Rest_API' ) ) :

	/**
	 * EPL_Rest_API Class
	 *
	 * @since 3.5.58
	 */
	class EPL_Rest_API {

                /**
		 * Instance
		 *
		 * @var EPL_Rest_API instance
		 */
		private static $instance;

                /**
		 * Insures that only one instance of EPL_Rest_API exists in memory.
		 *
		 * @return EPL_Rest_API instance
		 * @static
		 * @staticvar array $instance
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_Rest_API ) ) {
				self::$instance = new EPL_Rest_API();
                                self::$instance->hooks();
			}
			return self::$instance;
		}

                public function hooks() {

                        foreach( epl_get_core_post_types()  as $post_type ) {

                                add_filter( 'rest_prepare_'.$post_type, [ $this, 'meta_to_rest_api' ], 10, 3 );
                        }
                }

                /**
		 * Add EPL Meta data to Rest API.
		 *
		 * @return WP_REST_Response $response
		 */
                public function meta_to_rest_api( $response, $post, $request ) {

                        if( !is_epl_post( $post->post_type ) ) {
                                return $response;
                        }

                        $meta_field_sections = epl_get_meta_boxes();

                        if ( ! empty( $meta_field_sections ) ) {

                                foreach ( $meta_field_sections as $epl_meta_box ) {

                                        $section_post_types = (array) $epl_meta_box['post_type'];

                                        if ( ! in_array( $post->post_type, $section_post_types, true ) ) {
                                                continue;
                                        }

                                        if ( ! empty( $epl_meta_box['groups'] ) ) {

                                                foreach ( $epl_meta_box['groups'] as $group ) {

                                                        $fields = $group['fields'];
                                                        $fields = array_filter( $fields );

                                                        if ( ! empty( $fields ) ) {

                                                                foreach ( $fields as $field ) {

                                                                        if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {

                                                                                if ( in_array( $post->post_type, $field['exclude'], true ) ) {
                                                                                        continue;
                                                                                }
                                                                        }
                
                                                                        if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {

                                                                                if ( ! in_array( $post->post_type, $field['include'], true ) ) {
                                                                                        continue;
                                                                                }
                                                                        }

                                                                        $response->data['meta'][ $field['name'] ] = get_post_meta( $post->ID, $field['name'], true );
                                                                }
                                                        }
                                                }
                                        }
                                }
                        }

                        
                
                        return $response;
                }
                

	}
endif;

EPL_Rest_API::instance();