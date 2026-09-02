<?php
/**
 * Elementor Bundled Templates
 *
 * Installs the design templates that ship inside the plugin as real
 * `elementor_library` posts, so they are immediately available (and editable)
 * in Elementor without the user having to import anything.
 *
 * @package     EPL
 * @subpackage  Elementor/Seeder
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Elementor_Template_Seeder Class
 *
 * Seeds the plugin's bundled Elementor templates on install and on upgrade.
 *
 * Seeding is deliberately idempotent and non-destructive:
 *
 * - Each bundled template is installed once and tracked by slug in the
 *   `epl_elementor_bundled_templates` option, so an upgrade never produces
 *   duplicates.
 * - The checksum of the JSON that was written is recorded. When a newer plugin
 *   ships a revised design, the template is only refreshed if the user has not
 *   edited it. A user-modified template is left completely alone — their work
 *   is never silently overwritten.
 * - Deleting a seeded template is respected. It is not silently resurrected on
 *   the next upgrade.
 *
 * @since 3.5.0
 */
class EPL_Elementor_Template_Seeder {

	/**
	 * Option storing the install state of each bundled template.
	 *
	 * Shape: array( slug => array( 'post_id' => int, 'checksum' => string, 'version' => string ) ).
	 *
	 * @var string
	 */
	const STATE_OPTION = 'epl_elementor_bundled_templates';

	/**
	 * Marks a post as having been created by this seeder, and with which slug.
	 *
	 * @var string
	 */
	const SLUG_META = '_epl_bundled_template';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Runs for installs, plugin updates, and the case where Elementor is
		// activated after EPL. Cheap: the option check short-circuits at once
		// when everything is already seeded.
		add_action( 'admin_init', array( $this, 'maybe_seed' ), 20 );
	}

	/**
	 * The templates bundled with this plugin.
	 *
	 * `version` is a content version for the bundled design. Bump it when the
	 * shipped JSON changes and the new design should reach existing sites that
	 * have not customised it.
	 *
	 * @return array
	 */
	public static function get_bundled_templates() {
		return array(
			'epl-card' => array(
				'title'   => __( 'EPL Card', 'easy-property-listings' ),
				'type'    => EPL_Elementor_Loop_Card_Document::TYPE,
				'context' => 'listing',
				'file'    => EPL_PATH_LIB . 'page-builders/elementor/templates/epl-card.json',
				'version' => '1.0',
			),
		);
	}

	/**
	 * Install any bundled template that is not yet present, and refresh any
	 * that the user has left untouched when a newer design ships.
	 *
	 * @return void
	 */
	public function maybe_seed() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$state   = (array) get_option( self::STATE_OPTION, array() );
		$changed = false;

		foreach ( self::get_bundled_templates() as $slug => $template ) {
			$result = $this->seed_template( $slug, $template, $state );

			if ( null !== $result ) {
				$state[ $slug ] = $result;
				$changed        = true;
			}
		}

		if ( $changed ) {
			update_option( self::STATE_OPTION, $state );
		}
	}

	/**
	 * Install or refresh a single bundled template.
	 *
	 * @param string $slug     Bundled template slug.
	 * @param array  $template Template definition.
	 * @param array  $state    Current seeder state for all templates.
	 * @return array|null New state for this slug, or null when nothing was done.
	 */
	private function seed_template( $slug, $template, $state ) {
		$json = $this->read_template_file( $template['file'] );

		if ( '' === $json ) {
			return null;
		}

		$checksum = md5( $json );
		$existing = isset( $state[ $slug ] ) ? $state[ $slug ] : array();
		$post_id  = isset( $existing['post_id'] ) ? absint( $existing['post_id'] ) : 0;

		// Never seed twice for the same slug, even if the tracked post was
		// deleted — a user removing a bundled template means they do not want
		// it, so it must not reappear on the next upgrade.
		if ( $post_id ) {
			if ( 'publish' !== get_post_status( $post_id ) ) {
				return null;
			}

			$installed_checksum = isset( $existing['checksum'] ) ? $existing['checksum'] : '';
			$current_checksum   = md5( (string) get_post_meta( $post_id, '_elementor_data', true ) );

			// Same design already installed, or the user has customised it.
			if ( $checksum === $installed_checksum || $current_checksum !== $installed_checksum ) {
				return null;
			}

			$this->write_template_data( $post_id, $json );

			return array(
				'post_id'  => $post_id,
				'checksum' => $checksum,
				'version'  => $template['version'],
			);
		}

		if ( ! empty( $existing ) ) {
			return null;
		}

		// The tracking option can be lost independently of the templates it
		// tracks — a partial migration, a cloned staging database, a reset
		// options table. Adopt a template this seeder already created rather
		// than installing a second copy of it.
		$orphan = $this->find_seeded_post( $slug );

		if ( $orphan ) {
			return array(
				'post_id'  => $orphan,
				'checksum' => md5( (string) get_post_meta( $orphan, '_elementor_data', true ) ),
				'version'  => $template['version'],
			);
		}

		$post_id = wp_insert_post(
			array(
				'post_title'  => $template['title'],
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return null;
		}

		update_metadata( 'post', $post_id, '_elementor_edit_mode', 'builder' );
		update_metadata( 'post', $post_id, '_elementor_template_type', $template['type'] );
		update_metadata( 'post', $post_id, '_epl_elementor_template_context', $template['context'] );
		update_metadata( 'post', $post_id, self::SLUG_META, $slug );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_metadata( 'post', $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}

		$this->write_template_data( $post_id, $json );

		return array(
			'post_id'  => $post_id,
			'checksum' => $checksum,
			'version'  => $template['version'],
		);
	}

	/**
	 * Find a template this seeder previously created for a slug.
	 *
	 * @param string $slug Bundled template slug.
	 * @return int Post ID, or 0 when none exists.
	 */
	private function find_seeded_post( $slug ) {
		$found = get_posts(
			array(
				'post_type'        => 'elementor_library',
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
				'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Runs once per install/upgrade.
					array(
						'key'   => self::SLUG_META,
						'value' => $slug,
					),
				),
			)
		);

		return empty( $found ) ? 0 : absint( $found[0] );
	}

	/**
	 * Write the Elementor element tree onto a template post and drop its
	 * generated CSS so it regenerates from the new markup.
	 *
	 * @param int    $post_id Template post ID.
	 * @param string $json    Element tree as JSON.
	 * @return void
	 */
	private function write_template_data( $post_id, $json ) {
		// Elementor stores this slashed; `update_metadata()` unslashes once.
		update_metadata( 'post', $post_id, '_elementor_data', wp_slash( $json ) );
		delete_metadata( 'post', $post_id, '_elementor_css' );
		delete_metadata( 'post', $post_id, '_elementor_element_cache' );

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * Read and validate a bundled template JSON file.
	 *
	 * @param string $file Absolute path to the JSON file.
	 * @return string Compact JSON, or '' when the file is missing or invalid.
	 */
	private function read_template_file( $file ) {
		if ( ! is_readable( $file ) ) {
			return '';
		}

		$raw = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local bundled asset.

		if ( false === $raw ) {
			return '';
		}

		$decoded = json_decode( $raw, true );

		if ( ! is_array( $decoded ) || empty( $decoded ) ) {
			return '';
		}

		return wp_json_encode( $decoded );
	}

	/**
	 * The post ID of an installed bundled template.
	 *
	 * @param string $slug Bundled template slug.
	 * @return int Post ID, or 0 when not installed or no longer published.
	 */
	public static function get_template_id( $slug ) {
		$state = (array) get_option( self::STATE_OPTION, array() );

		if ( empty( $state[ $slug ]['post_id'] ) ) {
			return 0;
		}

		$post_id = absint( $state[ $slug ]['post_id'] );

		return ( 'publish' === get_post_status( $post_id ) ) ? $post_id : 0;
	}

	/**
	 * The bundled card used as the out-of-the-box default listing card.
	 *
	 * @return int Post ID, or 0 when unavailable.
	 */
	public static function get_default_card_id() {
		return (int) apply_filters( 'epl_elementor_default_card_id', self::get_template_id( 'epl-card' ) );
	}
}
