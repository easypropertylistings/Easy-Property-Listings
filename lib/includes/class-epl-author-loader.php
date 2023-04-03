<?php
/**
 * EPL Author Loader class
 *
 * @package     EPL
 * @subpackage  Classes/AuthorLoader
 * @copyright   Copyright (c) 2022, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.4.39
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Author_Loader Class
 *
 * @since 3.4.39
 */
if ( ! class_exists( 'EPL_Author_Loader' ) ) :

	/**
	 * EPL_Author_Loader Class
	 *
	 * @since 3.4.39
	 */
	#[AllowDynamicProperties]
	class EPL_Author_Loader {

		/**
		 * Object.
		 *
		 * @var string $object Object.
		 * @since 3.4.39
		 */
		public $object;

		/**
		 * Initiates the loader class and dynamically populates methods & properties for this class from the selected class.
		 *
		 * @since 3.4.39
		 * @param string $author_id The author WordPress user ID.
		 */
		public function __construct( $author_id ) {

			$class_name = apply_filters( 'epl_author_class_name', 'EPL_Author' );

			if ( class_exists( $class_name ) ) {
					$this->object = new $class_name( $author_id );
			} else {
					// Fallback to default class.
					$this->object = new EPL_Author( $author_id );
			}
			$this->import_class_properties();

		}

		/**
		 * Copy class properties from selected class to this class.
		 *
		 * @since 3.4.39
		 */
		public function import_class_properties() {
			foreach ( get_object_vars( $this->object ) as $key => $value ) {
				$this->$key = $value;
			}
		}

		/**
		 * Invoke methods from selected class when called through load class object.
		 *
		 * @param array $name Array of property object.
		 * @param array $arguments Array of property object.
		 *
		 * @since 3.4.39
		 */
		public function __call( $name, $arguments ) {
				return call_user_func_array( array( $this->object, $name ), $arguments );
		}

		/**
		 * Get the global property object
		 *
		 * @param array $property Array of property object.
		 *
		 * @return bool|mixed $return Array of values.
		 * @since 3.4.39
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
