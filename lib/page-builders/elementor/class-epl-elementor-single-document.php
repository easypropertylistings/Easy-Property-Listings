<?php
/**
 * Elementor EPL Single Listing Template document.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor
 * @since      3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dedicated saved-template document for single listing pages.
 *
 * Rendered by EPL_Elementor_Template_Router in place of EPL's default single
 * listing template when a published template of this type matches the
 * current listing's post type — the free-Elementor equivalent of an
 * Elementor Pro Theme Builder "Single" template.
 */
class EPL_Elementor_Single_Document extends EPL_Elementor_Listing_Document {

	/** Document type slug. */
	const TYPE = 'epl-single';

	/**
	 * Get document name.
	 *
	 * @return string
	 */
	public function get_name() {
		return self::TYPE;
	}

	/**
	 * Get document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return self::TYPE;
	}

	/**
	 * Get singular title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'EPL Single Listing', 'easy-property-listings' );
	}

	/**
	 * Get plural title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'EPL Single Listings', 'easy-property-listings' );
	}

	/**
	 * Register document controls.
	 *
	 * Adds "Apply To Listing Types" on top of the shared parent controls —
	 * unlike a Listing Card (a design picked explicitly per widget, with no
	 * routing logic of its own), a Single Listing template is matched
	 * automatically by post type, so it genuinely needs this.
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'epl_single_apply_types',
			array(
				'label' => esc_html__( 'Listing Types', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'epl_apply_post_types',
			array(
				'label'       => esc_html__( 'Apply To Listing Types', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => epl_get_active_post_types(),
				'default'     => array(),
				'description' => esc_html__( 'Leave empty to apply this template to every EPL listing type.', 'easy-property-listings' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Save the document, mirroring "Apply To Listing Types" into plain post
	 * meta so EPL_Elementor_Template_Router::get_single_template_id() can
	 * query for it without unserializing the full Elementor page-settings
	 * meta on every request.
	 *
	 * `$this->get_settings()` is backed by an in-memory cache that is only
	 * ever populated once per request (`Base_Object::ensure_settings()`);
	 * `parent::save()` writes the freshly submitted settings straight to
	 * `_elementor_page_settings` postmeta without refreshing that cache, so
	 * reading it back here would return whatever was cached before this
	 * save — usually the previous value, never the one just submitted. Read
	 * the just-persisted postmeta directly instead, the same way core's own
	 * `Document::update_settings()` does.
	 *
	 * @param array $data Document data.
	 * @return bool|array
	 */
	public function save( $data ) {
		$result = parent::save( $data );

		if ( $result ) {
			$page_settings = $this->get_meta( \Elementor\Core\Settings\Page\Manager::META_KEY );
			$post_types    = isset( $page_settings['epl_apply_post_types'] ) ? array_values( array_filter( array_map( 'sanitize_key', (array) $page_settings['epl_apply_post_types'] ) ) ) : array();
			update_post_meta( $this->post->ID, '_epl_apply_post_types', $post_types );
		}

		return $result;
	}
}
