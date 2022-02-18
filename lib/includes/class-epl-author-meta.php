<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Classes/AuthorMeta
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Author_Meta Class
 *
 * @since 1.3.0
 */
if ( ! class_exists( 'EPL_Author_Meta' ) ) :

	/**
	 * EPL_Author_Meta Class
	 *
	 * @since 1.3.0
	 */
	class EPL_Author_Meta {

                public $object;

		/**
		 * Get things started
		 *
		 * @since 1.3.0
		 * @param string $author_id The author WordPress user ID.
		 */
		public function __construct( $author_id ) {

                        $class_name = apply_filters( 'epl_author_class_name', 'EPL_Author' );
                        
                        if( class_exists( $class_name ) ) {

                                $this->object = new $class_name( $author_id );
                        } else {

                                // fallback to default class
                                $this->object = new EPL_Author( $author_id );

                        }

                        $this->import_properties();

		}

                public function import_properties() {   

                    foreach ( get_object_vars( $this->object ) as $key => $value ) {
                        $this->$key = $value;
                    }
                }

                function __call( $name, $arguments ) {
                        
                        return call_user_func_array( array($this->object, $name ), $arguments );
                }

                /**
		 * Get the global property object
		 *
		 * @param array $property Array of property object.
		 *
		 * @return bool|mixed $return Array of values.
		 * @since 1.3.0
		 */
		public function __get( $property ) {

			$prop_val  = ! empty( $this->object->{$property} ) ? $this->object->{$property} : false;
			$prop_meta = get_user_meta( $this->author_id, $property, true );

			if ( ! empty( $prop_val ) ) {
				return $prop_val;

			} elseif ( ! empty( $prop_meta ) ) {
				return $prop_meta;
			}
		}
	}
endif;
