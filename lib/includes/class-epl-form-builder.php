<?php
/**
 * Form Builder Object
 *
 * @package     EPL
 * @subpackage  Classes/Forms
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification

/**
 * EPL_FORM_BUILDER Class
 *
 * @since 2.3
 */
class EPL_FORM_BUILDER {

	/**
	 * Prefix for this class, will be used for filters & actions
	 *
	 * @var string $prefix Prefix name.
	 * @since 2.3
	 */
	private $prefix = 'epl_form_builder_';

	/**
	 * Text domain for translation
	 *
	 * @var string $text_domain Text domain name.
	 * @since 2.3
	 */
	private $text_domain = 'easy-property-listings';

	/**
	 * Form fields
	 *
	 * @var array $form_fields Form fields.
	 * @since 2.3
	 */
	public $form_fields = array();

	/**
	 * Form sections
	 *
	 * @var array $form_sections Form sections.
	 * @since 2.3
	 */
	public $form_sections = array();

	/**
	 * Classes for form and fields
	 *
	 * @var array $form_classes Form css classes.
	 * @since 2.3
	 */
	private $form_classes = array();

	/**
	 * Configuration of this form
	 *
	 * @var array $configuration Settings of the form.
	 * @since 2.3
	 */
	public $configuration = array();

	/**
	 * Form has nonce or not
	 *
	 * @var bool $has_nonce Security field.
	 * @since 2.3
	 */
	public $has_nonce = false;

	/**
	 * Holds the list of form attributes, defaults & added by user
	 *
	 * @var array $form_attributes Form options.
	 * @since 2.3
	 */
	public $form_attributes = array();

	/**
	 * Get things going
	 *
	 * @param array $config Form ptions.
	 * @since 2.3
	 */
	public function __construct( $config = array() ) {

		$defaults = array(
			'form_tag'        => 'on',
			'form_wrap'       => 'on',
			'form_context'    => 'default', // default , meta , settings.
			'callback_action' => null,
			'is_ajax'         => false,
		);

		$this->configuration = shortcode_atts( $defaults, $config );

		// Set form defaults.
		$this->set_defaults();

	}

	/**
	 * Call default & user submitted callbacks for this form upon form submission
	 *
	 * @since 2.3
	 * @return null
	 */
	public function __destruct() {
		$this->callbacks();
	}

	/**
	 * Call default & user submitted callbacks for this form upon form submission
	 *
	 * @param string $key Meta key.
	 * @return string
	 * @since 2.3
	 */
	public function __get( $key ) {
		return isset( $this->{$key} ) ? $this->{$key} : null;
	}

	/**
	 * Callbacks
	 *
	 * @since 2.3
	 */
	public function callbacks() {

		if ( isset( $_REQUEST[ $this->prefix . 'form_submit' ] ) ) {

			// Hook to this action to save form data.
			do_action( $this->prefix . 'save_form', get_object_vars( $this ), $_REQUEST );

			// Run user defined hook if applicable.
			if ( ! is_null( $this->configuration['callback_action'] ) ) {
				do_action( $this->prefix . $this->configuration['callback_action'], get_object_vars( $this ), $_REQUEST );
			}
		}
	}

	/**
	 * Set form configuration
	 *
	 * @param string $key Meta key.
	 *
	 * @return bool|mixed
	 * @since 2.3
	 */
	public function get_configuration( $key = '' ) {
		return isset( $this->configuration[ $key ] ) ? $this->configuration[ $key ] : false;
	}

	/**
	 * Get value
	 *
	 * @param string $field Field meta key.
	 *
	 * @return mixed|string
	 * @since 2.3
	 */
	public function get_value( $field ) {
		// phpcs:disable
		switch ( $this->configuration['form_context'] ) {

			case 'default':
				return isset( $field['value'] ) ? $field['value'] : ( isset( $field['default'] ) ? $field['default'] : '' );
			break;

			case 'meta':
				global $post;
				return isset( $field['name'] ) ? get_post_meta( $post->ID, $field['name'], true ) : '';
			break;

			case 'settings':
				global $epl_settings;
				return isset( $field['name'] ) ? $epl_settings[ $field['name'] ] : '';
			break;

		}
		// phpcs:enable
	}

	/**
	 * Set form classes
	 *
	 * @since 2.3
	 */
	public function set_form_classes() {

		$this->form_classes = apply_filters(
			$this->prefix . 'form_classes',
			array(
				'form_container'  => array(
					$this->prefix . 'form_container',
					'row',
				),
				'form'            => array(
					$this->prefix . 'form',
					'col-md-12',
				),
				'field_container' => array(
					$this->prefix . 'field_container',
					'col-md-12',
				),
				'field'           => array(
					$this->prefix . 'field',
					'col-md-12',
				),
				'label_container' => array(
					$this->prefix . 'label_container',
					'col-md-12',
				),
				'label'           => array(
					$this->prefix . 'label',
					'col-md-12',
				),
			),
			$this->form_attributes
		);
	}

	/**
	 * List of invalid attributes per field type that must be removed from field array before field rendering
	 *
	 * @since 2.3
	 */
	public function invalid_attributes() {
		return apply_filters(
			$this->prefix . 'invalid_attributes',
			array(
				'label'   => array( 'all' ),
				'default' => array( 'all' ),
				'opts'    => array( 'all' ),
			)
		);
	}

	/**
	 * Get classes for wrappers, form , fields
	 *
	 * @param string $key Meta key.
	 * @param array  $field Field type.
	 *
	 * @return string
	 * @since 2.3
	 */
	public function get_class( $key = '', $field = array() ) {

		if ( isset( $this->form_classes[ $key ] ) ) {
			$classes = implode( ' ', array_map( 'sanitize_html_class', $this->form_classes[ $key ] ) );
			if ( isset( $field['type'] ) ) {
				$classes .= ' ' . $this->prefix . $key . '_' . $field['type'];
			}

			if ( 'field' === $key && isset( $field['class'] ) ) {

				if ( is_array( $field['class'] ) && count( $field['class'] ) > 0 ) {
					$classes .= implode( ' ', array_map( 'sanitize_html_class', $field['class'] ) );
				} else {
					$classes .= ' ' . $field['class'];
				}
			}
			return $classes;
		}
	}

	/**
	 * Get Field Attributes
	 *
	 * @param array $field Fields.
	 *
	 * @return string
	 * @since 2.3
	 */
	public function get_attributes( $field ) {

		$html        = '';
		$field['id'] = isset( $field['id'] ) ? $field['id'] : '';

		if ( empty( $field['id'] ) ) {
			$field['id'] = isset( $field['name'] ) ? epl_sanitize_key( $field['name'] ) : '';
		}

		$invalid_attributes = $this->invalid_attributes();

		foreach ( $field as $key  => $value ) {

			if ( isset( $invalid_attributes[ $key ] ) && ( in_array( $field['type'], $invalid_attributes[ $key ], true ) || in_array( 'all', $invalid_attributes[ $key ], true ) ) ) { //phpcs:ignore
				// This attribute is not valid for current input type, lets skip it.
			} else {

				if ( 'name' === $key ) {
					$value = esc_attr( $value );
				}
				if ( isset( $field['multiple'] ) && in_array( $field['type'], array( 'select', 'checkbox' ), true ) && 'name' === $key ) {
					$value = $value . '[]';
				}
				$value = is_array( $value ) ? implode( ',', $value ) : $value;
				$html .= $key . '="' . $value . '" ';
			}
		}
		return $html;
	}

	/**
	 * The public function can be used to configure the attributes of form
	 *
	 * @param string $key Meta key.
	 * @param string $value Field value.
	 *
	 * @since 2.3
	 */
	public function set_form_attributes( $key = '', $value = '' ) {

		// If user wants to add a single attribute.
		if ( ! empty( $key ) && is_string( $key ) ) {
			$this->form_attributes[ $this->escape( 'text', $key ) ] = $this->escape( 'attribute', $value );

		} elseif ( is_array( $key ) & ! empty( $key ) ) {

			// Multiple attrubtes at once.
			foreach ( $key as $index  => $val ) {
				$this->form_attributes[ $this->escape( 'text', $index ) ] = $this->escape( 'attribute', $val );
			}
		}
	}

	/**
	 * Set default attributes for the form tag
	 *
	 * @since 2.3
	 */
	private function set_default_form_attributes() {

		$this->form_attributes = apply_filters(
			$this->prefix . 'form_default_attributes',
			array(
				'action'  => '',
				'id'      => '',
				'class'   => '',
				'enctype' => '',
				'method'  => 'POST',
				'name'    => '',
			)
		);

		if ( true === $this->configuration['is_ajax'] ) {
			$this->form_attributes[ 'data-' . $this->prefix . 'ajax_submit' ] = 'true';
		}
	}

	/**
	 * Sets up all the form defaults while object instantiation
	 *
	 * @since 2.3
	 */
	private function set_defaults() {

		do_action( $this->prefix . 'before_setup_defaults' );
		$this->set_default_form_attributes();
		$this->set_form_classes();
		do_action( $this->prefix . 'after_setup_defaults' );
	}

	/**
	 * Escape necessary data
	 *
	 * @param string $type Field type.
	 * @param string $value Field value.
	 *
	 * @return string|void
	 * @since 2.3
	 */
	private function escape( $type = '', $value = '' ) {
		// phpcs:disable
		switch ( $type ) {

			case 'url':
				return esc_url( $value );
			break;

			case 'class':
			case 'id':
				return sanitize_html_class( $value );
			break;

			case 'text':
				return sanitize_text_field( $value );
			break;
			case 'attribute':
				return esc_attr( $value );
		} // phpcs:enable
	}

	/**
	 * Add multiple form sections at once
	 *
	 * @param array $sections Field sections.
	 * @since 2.3
	 */
	public function add_sections( $sections = array() ) {

		foreach ( $sections as $section ) {
			$this->add_section( $section );
		}
	}

	/**
	 * Add single section to form
	 *
	 * @param array $section Field sections.
	 * @since 2.3
	 */
	public function add_section( $section = array() ) {
		$this->form_sections[] = $section;
	}

	/**
	 * Add single field to form
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	public function add_field( $field = array() ) {
		foreach ( $field as $key  => &$val ) {
			if ( is_string( $val ) ) {
				$val = esc_attr( $val );
			}
		}
		$this->form_fields[] = $field;
	}

	/**
	 * Add multiple form fields at once
	 *
	 * @param array $fields Fields array.
	 * @since 2.3
	 */
	public function add_fields( $fields = array() ) {

		foreach ( $fields as $field ) {
			$this->add_field( $field );
		}

		$this->add_field(
			array(
				'type'  => 'hidden',
				'name'  => $this->prefix . 'form_submit',
				'value' => 'true',
			)
		);

		if ( ! is_null( $this->configuration['callback_action'] ) ) {
			$this->add_field(
				array(
					'type'  => 'hidden',
					'name'  => $this->prefix . 'form_action',
					'value' => $this->prefix . $this->configuration['callback_action'],
				)
			);
		}
	}

	/**
	 * Render form html based on the fields & form attributes
	 *
	 * @since 2.3
	 */
	public function render_form() {

		ob_start();

		if ( 'on' === $this->get_configuration( 'form_wrap' ) ) {
			do_action( 'before_' . $this->prefix . 'form_container_open' );
			$this->render_form_container_open();
		}

		if ( 'on' === $this->get_configuration( 'form_tag' ) ) {
			do_action( 'before_' . $this->prefix . 'form_open' );
			$this->render_form_open();
		}

		do_action( 'before_' . $this->prefix . 'form_sections' );
		$this->render_sections();
		do_action( 'after_' . $this->prefix . 'form_sections' );

		do_action( 'before_' . $this->prefix . 'form_fields' );
		$this->render_fields();
		do_action( 'after_' . $this->prefix . 'form_fields' );

		if ( 'on' === $this->get_configuration( 'form_tag' ) ) {
			$this->render_form_close();
			do_action( 'after_' . $this->prefix . 'form_close' );
		}

		if ( 'on' === $this->get_configuration( 'form_wrap' ) ) {
			$this->render_form_container_close();
			do_action( 'after_' . $this->prefix . 'form_container_close' );
		}

		echo ob_get_clean(); // phpcs:ignore
	}

	/**
	 * Render form container opening tag
	 *
	 * @since 2.3
	 */
	public function render_form_container_open() {
		// phpcs:disable WordPress.Security.EscapeOutput
		$html  = "\n<div  ";
		$html .= 'class ="' . esc_attr( $this->get_class( 'form_container' ) ) . '" ';
		$html .= '>';
		echo apply_filters( $this->prefix . 'form_container_open_tag', $html, $this->form_attributes );
	}

	/**
	 * Render form container closing tag
	 *
	 * @since 2.3
	 */
	public function render_form_container_close() {

		echo apply_filters( $this->prefix . 'form_container_close_tag', "\n</div>", $this->form_attributes );
	}

	/**
	 * Render form opening tag
	 *
	 * @since 2.3
	 */
	public function render_form_open() {

		$html = "\n<form  ";
		foreach ( $this->form_attributes as $key  => $value ) {
			$html .= $key . '="' . $value . '" ';
		}
		$html .= ' >';
		echo apply_filters( $this->prefix . 'form_open_tag', $html, $this->form_attributes );
	}

	/**
	 * Render form closing tag
	 *
	 * @since 2.3
	 */
	public function render_form_close() {

		echo apply_filters( $this->prefix . 'form_close_tag', "\n</form>", $this->form_attributes );
	}

	/**
	 * Render all form sections
	 *
	 * @since 2.3
	 */
	private function render_sections() {

		if ( ! empty( $this->form_sections ) ) {
			foreach ( $this->form_sections as $section ) {
				$this->render_section( $section );

			}
		}
	}

	/**
	 * Render single form section
	 *
	 * @param array $section Field section.
	 * @since 2.3
	 */
	private function render_section( $section ) {

		$section_class = $this->prefix . 'form_section ' . isset( $section['class'] ) ? $section['class'] : '';
		$section_id    = isset( $section['id'] ) ? $section['id'] : '';
		$section_label = isset( $section['label'] ) ? $section['label'] : '';
		$section_help  = isset( $section['help'] ) ? $section['help'] : ''; ?>

		<div id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_class ); ?>" >

			<span class="<?php echo esc_attr( $this->prefix . 'section_label' ); ?>">
				<?php
					echo esc_attr( $section_label );
				?>
			</span>

			<span class="<?php echo esc_attr( $this->prefix . 'section_help' ); ?>">
				<?php
					echo wp_kses_post( $section_help );
				?>
			</span>

			<div class="<?php echo esc_attr( $this->prefix . 'section_fields' ); ?>">
				<?php
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$this->render_field( $field );

					}
				}
				?>
			</div>

		</div>
		<?php
	}

	/**
	 * Render all form fields based on type
	 *
	 * @since 2.3
	 */
	private function render_fields() {

		if ( ! empty( $this->form_fields ) ) {
			foreach ( $this->form_fields as $field ) {
				$this->render_field( $field );

			}
		}
	}

	/**
	 * Render single form field
	 *
	 * @param array $field Field array.
	 * @since 2.3
	 */
	private function render_field( $field ) {

		$this->render_field_container_open();
		$this->render_field_label( $field );

		switch ( $field['type'] ) {

			case 'text':
			case 'url':
			case 'number':
			case 'tel':
			case 'hidden':
				$this->render_text( $field );
				break;
			case 'textarea':
				$this->render_textarea( $field );
				break;
			case 'radio':
				$this->render_radio( $field );
				break;
			case 'select':
			case 'select_single':
				$this->render_select( $field );
				break;
			case 'checkbox':
			case 'checkbox_single':
				$this->render_checkbox( $field );
				break;
			case 'wp_editor':
				$this->render_wp_editor( $field );
				break;

			default:
				$this->render_text( $field );
				break;

		}
		$this->render_field_container_close();
	}

	/**
	 * Render field container opening tag
	 *
	 * @since 2.3
	 */
	public function render_field_container_open() {

		$html  = "\n<div  ";
		$html .= 'class ="' . $this->get_class( 'field_container' ) . '" ';
		$html .= '>';
		echo apply_filters( $this->prefix . 'field_container_open_tag', $html, $this->form_attributes );
	}

	/**
	 * Render field container closing tag
	 *
	 * @since 2.3
	 */
	public function render_field_container_close() {

		echo apply_filters( $this->prefix . 'field_container_close_tag', "\n</div>", $this->form_attributes );
	}

	/**
	 * Render field label
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	public function render_field_label( $field = array() ) {

		$wrapper  = "\n<span  ";
		$wrapper .= 'class ="' . $this->get_class( 'label_container', $field ) . '" ';
		$wrapper .= '>';

		if ( isset( $field['label'] ) ) {

			$html     = "\n<label  ";
			$html    .= 'class ="' . $this->get_class( 'label', $field ) . '" ';
			$html    .= '>';
			$html    .= apply_filters( $this->prefix . 'field_label', $field['label'], $field );
			$html    .= "\n</label>";
			$wrapper .= $html;
		}

		$wrapper .= "\n</span>";

		echo apply_filters( $this->prefix . 'field_label_html', $wrapper, $field );
	}

	/**
	 * Render text & similar fields
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_text( $field ) {

		$field['value'] = esc_attr( stripslashes( $this->get_value( $field ) ) );
		$field['class'] = $this->get_class( 'field', $field );
		$html           = "\n<input  ";
		$html          .= $this->get_attributes( $field );
		$html          .= ' />';
		echo apply_filters( $this->prefix . 'form_' . $field['type'] . '_tag', $html, $field );
	}

	/**
	 * Render textarea
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_textarea( $field ) {

		$value          = $this->get_value( $field );
		$field['class'] = $this->get_class( 'field', $field );
		$html           = "\n<textarea  ";
		$html          .= $this->get_attributes( $field );
		$html          .= '>';
		$html          .= esc_attr( $value );
		$html          .= '</textarea>';
		echo apply_filters( $this->prefix . 'form_' . $field['type'] . '_tag', $html, $field );

	}

	/**
	 * Render radio
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_radio( $field ) {

		$options = $field['opts'];
		$value   = $this->get_value( $field );

		unset( $field['opts'] );

		foreach ( $options as $option => $label ) {

			if ( $option === $value ) {
				$field['checked'] = 'checked';
			}

			$field['value'] = esc_attr( stripslashes( $option ) );
			$field['class'] = $this->get_class( 'field', $field );
			$html           = "\n<input  ";
			$html          .= $this->get_attributes( $field );
			$html          .= ' >' . $label;
			echo apply_filters( $this->prefix . 'form_' . $field['type'] . '_tag', $html, $field );
			unset( $field['checked'] );
		}
	}

	/**
	 * Render checkbox
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_checkbox( $field ) {

		$options = $field['opts'];
		$value   = $this->get_value( $field );

		unset( $field['opts'] );

		foreach ( $options as $option => $label ) {

			if ( $option === $value ) {
				$field['checked'] = 'checked';
			}

			$field['value'] = esc_attr( stripslashes( $option ) );
			$field['class'] = $this->get_class( 'field', $field );
			$html           = "\n<input  ";
			$html          .= $this->get_attributes( $field );
			$html          .= ' >' . $label;
			echo apply_filters( $this->prefix . 'form_' . $field['type'] . '_tag', $html, $field );
			unset( $field['checked'] );
		}
	}

	/**
	 * Render Editior
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_wp_editor( $field ) {
		$value          = stripslashes( $this->get_value( $field ) );
		$field['class'] = $this->get_class( 'field', $field );
		wp_editor( $value, $field['name'] );
	}

	/**
	 * Render select
	 *
	 * @param array $field Fields array.
	 * @since 2.3
	 */
	private function render_select( $field ) {
		global $post;
		$options = $field['opts'];
		unset( $field['opts'] );

		$value          = $this->get_value( $field );
		$field['class'] = $this->get_class( 'field', $field );
		$html           = "\n<select  ";
		$html          .= $this->get_attributes( $field );
		$html          .= ' >';
		$value          = (array) $value;
		$value          = array_map( 'sanitize_text_field', $value );

		foreach ( $options as $option => $label ) {

			if ( is_array( $label ) ) {
				if ( isset( $label['exclude'] ) && ! empty( $label['exclude'] ) ) {
					if ( in_array( $post->post_type, $label['exclude'], true ) ) {
						continue;
					}
				}

				if ( isset( $label['include'] ) && ! empty( $label['include'] ) ) {
					if ( ! in_array( $post->post_type, $label['include'], true ) ) {
						continue;
					}
				}
				$label = $label['label'];
			}
			$html .= "\n<option  ";
			$html .= 'value = "' . stripslashes( $option ) . '"';

			if ( in_array( $option, $value, true ) ) {
				$html .= ' selected = "selected" ';
			}

			$html .= ' >' . $label . "\n</option>";

		}
		$html .= "\n</select>";
		echo apply_filters( $this->prefix . 'form_' . $field['type'] . '_tag', $html, $field );
	}

	/**
	 * Render nonce field
	 *
	 * @param string $action Action name.
	 * @since 2.3
	 */
	public function add_nonce( $action = '' ) {

		$this->has_nonce   = true;
		$this->nonce_key   = ! empty( $action ) ? $action : $this->prefix . 'nonce_action';
		$this->nonce_value = wp_create_nonce( $action );

		$this->add_field(
			array(
				'type'  => 'hidden',
				'name'  => $this->nonce_key,
				'value' => $this->nonce_value,
			)
		);
	}
}
