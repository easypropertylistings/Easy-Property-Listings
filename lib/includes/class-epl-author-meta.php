<?php
/**
 * EPL Author Meta class, Alias of EPL_Author_Loader class. Kept for backward compatiblility.
 *
 * @package     EPL
 * @subpackage  Classes/AuthorMeta
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 * @since       3.4.39 Alias of EPL_Author_Loader class. Kept for backward compatiblility.
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
	class EPL_Author_Meta extends EPL_Author_Loader {
	}
endif;
