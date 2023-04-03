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
	
	class EPL_Author_Loader {

                /**
		 * The user ID
		 *
		 * @since 3.4.44
		 * @var string $author_id User ID.
		 */
		public $author_id;

		/**
		 * The username
		 *
		 * @since 3.4.44
		 * @var string $name User name.
		 */
		public $name;

		/**
		 * The user mobile phone number
		 *
		 * @since 3.4.44
		 * @var string $mobile User mobile number.
		 */
		public $mobile;

		/**
		 * The user office phone number
		 *
		 * @since 3.3.0
		 * @var string $office_phone User office phone.
		 */
		public $office_phone;

		/**
		 * The user Facebook URL
		 *
		 * @since 3.4.44
		 * @var string $facebook User Facebook profile URL.
		 */
		public $facebook;

		/**
		 * The user LinkedIn URL
		 *
		 * @since 3.4.44
		 * @var string $linkedin User LinkedIn profile URL.
		 */
		public $linkedin;

		/**
		 * The user Google Plus URL
		 *
		 * @since 3.4.44
		 * @var string $google User Google Plus profile URL.
		 */
		public $google;

		/**
		 * The user Twitter URL
		 *
		 * @since 3.4.44
		 * @var string $twitter User Twitter ID.
		 */
		public $twitter;

		/**
		 * The user Instagram URL
		 *
		 * @since 3.3.0
		 * @var string $instagram User Instagram profile URL.
		 */
		public $instagram;

		/**
		 * The user Pinterest URL
		 *
		 * @since 3.3.0
		 * @var string $pinterest User Pinterest profile URL.
		 */
		public $pinterest;

		/**
		 * The user YouTube URL
		 *
		 * @since 3.3.0
		 * @var string $youtube User YouTube profile URL.
		 */
		public $youtube;

		/**
		 * The user email address
		 *
		 * @since 3.4.44
		 * @var string $email User email address.
		 */
		public $email;

		/**
		 * The user Skype name
		 *
		 * @since 3.4.44
		 * @var string $skype User Skype ID.
		 */
		public $skype;

		/**
		 * The user text slogan
		 *
		 * @since 3.4.44
		 * @var string $slogan User Slogan text string.
		 */
		public $slogan;

		/**
		 * The user position
		 *
		 * @since 3.4.44
		 * @var string $position User position text string.
		 */
		public $position;

		/**
		 * The user video profile
		 *
		 * @since 3.4.44
		 * @var string $video User video profile URL.
		 */
		public $video;

		/**
		 * The user contact form shortcode
		 *
		 * @since 3.4.44
		 * @var string $contact_form User contact shortcode.
		 */
		public $contact_form;

		/**
		 * The user bio
		 *
		 * @since 3.4.44
		 * @var string $description User bio textarea.
		 */
		public $description;

                /**
		 * Staff ID
		 *
		 * @since 3.4.44
		 * @var string Staff ID used in staff dir extension.
		 */
		public $staff_id;

                /**
		 * Directory
		 *
		 * @since 3.4.44
                */
		public $directory;

                /**
		 * First Name
		 *
		 * @since 3.4.44
                */
		public $first_name;

                /**
		 * Last Name
		 *
		 * @since 3.4.44
                */
		public $last_name;

                /**
		 * Secondary Email
		 *
		 * @since 3.4.44
                */
		public $secondary_email;

                /**
		 * Whatsapp
		 *
		 * @since 3.4.44
                */
		public $whatsapp;

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
