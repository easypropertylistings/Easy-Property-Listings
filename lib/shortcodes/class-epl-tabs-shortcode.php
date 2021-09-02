<?php
/**
 * SHORTCODE :: Tabs [epl_tabs]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2021, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * EPL_Tabs_Shortcode Class
 *
 * @since 3.5.0
 */
class EPL_Tabs_Shortcode {

	/**
	 * Default arguments.
	 *
	 * @since 3.5.0
	 * @var array $default_args Default arguments.
	 */
	public $default_args;

	/**
	 * Attributes passed to shortcode.
	 *
	 * @since 3.5.0
	 * @var array $atts Shortcode attributes.
	 */
	public $atts;

	/**
	 * Attributes passed to shortcode and defaults.
	 *
	 * @since 3.5.0
	 * @var array $attributes Shortcode attributes.
	 */
	public $attributes;

	/**
	 * List of tabs.
	 *
	 * @since 3.5.0
	 * @var array $tabs Tabs.
	 */
	public $tabs;
    
	/**
	 * Shortcode Content.
	 *
	 * @since 3.5.0
	 * @var array $content Content.
	 */
	public $content;
    
	/**
	 * Shortcode ID.
	 *
	 * @since 3.5.0
	 * @var array $content Shortcode ID.
	 */
	public static $id = 1;

	/**
	 * Construct the shortcode.
	 *
	 * @since 3.5.0
	 * @param array $atts Shortcode attributes.
	 * @param string Shortcode content.
	 * @param array $overrides Array of variables to override defaults.
	 */
	public function __construct( $atts, $content, $overrides = array(), $tab = false ) {

		if( !$tab ) {
			$this->atts = $atts;
			$this->content = $content;
			$this->shortcode_atts();
			$this->override_atts( $overrides );
			self::$id++;
		}
	}

	/**
	 * Override Attributes.
	 *
	 * @since 3.5.0
	 * @param array $overrides Array of variables to override defaults.
	 */
	public function override_atts( $overrides ) {

		if ( ! empty( $overrides ) ) {

			foreach ( $overrides as $key  => $value ) {
				$this->set_attribute( $key, $value );
			}
		}
	}

	/**
	 * Get default options.
	 *
	 * @since 3.5
	 */
	public function get_default_args() {

		/**
		 * Default args.
		 */

		$this->default_args = array(
			'id'         => self::$id,
			'class'      => '',
			'wrap_class' => '', // wrapper class.
			'template'   => false,
			'title'      => '',
			'type'       => 'horizontal'
		);

		return $this->default_args;
	}
    
	/**
	 * Get default options.
	 *
	 * @since 3.5
	 */
	public function get_tab_default_args() {

		/**
		 * Default args for single tab.
		 */
		$this->tab_default_args = array(
			'id'    => '',
			'title' => __( 'Tab Title', 'easy-property-listings' )
		);

		return $this->tab_default_args;
	}

	/**
	 * Shortcode attributes
	 *
	 * @since 3.5
	 */
	public function shortcode_atts() {
		$this->attributes = shortcode_atts( $this->get_default_args(), array_filter( $this->atts ) );
	}

	/**
	 * Set attributes
	 *
	 * @since 3.5
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 */
	public function set_attribute( $key, $value ) {
		if ( isset( $this->attributes[ $key ] ) ) {
			$this->attributes[ $key ] = $value;
		}
	}

	/**
	 * Get attributes
	 *
	 * @param string $key Meta key.
	 *
	 * @return mixed|null
	 * @since 3.5
	 */
	public function get_attribute( $key ) {
		return isset( $this->attributes[ $key ] ) ? $this->attributes[ $key ] : null;
	}

	/**
	 * Process individual tabs
	 *
	 * @param array $atts Shortcode attributes.
	 * @param string Shortcode content.
	 *
	 * @return string
	 * @since 3.5
	 */
	public function render_tab( $atts, $content ) {
        
		$atts          = shortcode_atts( $this->get_tab_default_args(), $atts );
		$atts['id']    = $atts['id'] ?: rawurldecode( sanitize_title( $atts['title'] ) );;
		$atts['title'] = sanitize_text_field( $atts['title'] );
		// Save tab details to be used later.
		$this->tabs[] = $atts; 
		ob_start(); ?>
			<div class="epl-tab-single" id="<?php echo esc_attr( 'epl-tab-'.$atts['id'] ); ?>">
				<?php
				echo do_shortcode($content);
				?>
			</div> <?php
		return ob_get_clean();
	}
    
	/**
	 * Get the template
	 *
	 * @since 3.5.0
	 */
	public function get_template() {

		$attributes['template'] = str_replace( '_', '-', $this->attributes['template'] );

		$template = empty( $this->attributes['template'] ) ? 'default.php' : $this->attributes['template'] . '.php';

		return $template;
	}

	/**
	 * Render the shortcode
	 *
	 * @since 3.5
	 */
	public function render() {
		$wrap_template = $this->get_template();
        ob_start();
        $content = do_shortcode( $this->content );
		epl_get_template_part(
			'shortcodes/epl-tabs/'.$wrap_template,
			array(
                'attributes'    => $this->attributes,
                'tabs'          => $this->tabs,
                'content'       => $content
			)
		);

		return ob_get_clean();
	}
}
