<?php
/**
 * SHORTCODE :: Listing [listing] class
 *
 * @package     EPL
 * @subpackage  Classes/Listing
 * @copyright   Copyright (c) 2026, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [listing] on the [listing_advanced] engine
 *
 * Same defaults, args filter (epl_shortcode_listing_args), sorting context and template
 * as [listing] always had, plus every [listing_advanced] option: feature, feature_id,
 * location_id, open_house, auction, commercial_listing_type, post__in, post__not_in,
 * orderby_clause and wrap_template.
 *
 * @since 3.6
 */
class EPL_Shortcode_Listing extends EPL_Advanced_Shortcode_Listing {

	/**
	 * Shortcode name.
	 *
	 * @since 3.6
	 * @var string $shortcode Shortcode name.
	 */
	public $shortcode = 'listing';

	/**
	 * Default options: the [listing_advanced] ones, without the extra wrapper class
	 * [listing] never had.
	 *
	 * @since 3.6
	 *
	 * @return array
	 */
	public function get_default_args() {
		parent::get_default_args();
		$this->default_args['class'] = '';

		return $this->default_args;
	}
}
