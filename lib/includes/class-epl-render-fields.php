<?php
/**
 * Render HTML Fields
 *
 * @package     EPL
 * @subpackage  Classes/RenderFields
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3
 * @since       3.4.29 Moved from meta-boxes into its own class for more extensibility
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Render_Fields class.
 *
 * Handles rendering of html fields for EPL settings, meta boxes etc.
 *
 * @since 2.3
 * @since 3.4.29 Moved from meta-boxes into its own class for more extensibility.
 */
class EPL_Render_Fields {

	/**
	 * Default class prefix.
	 *
	 * @var string $class_prefix
	 */
	public $class_prefix = 'epl-form-field-';

	/**
	 * Post object.
	 *
	 * @var array $post
	 */
	public $post;

	/**
	 * Constructs a new instance.
	 */
	public function __construct() {

		add_action( 'epl_render_field_select', array( $this, 'select' ), 10, 2 );
		add_action( 'epl_render_field_select_multiple', array( $this, 'select' ), 10, 2 );
		add_action( 'epl_render_field_checkbox', array( $this, 'checkbox' ), 10, 2 );
		add_action( 'epl_render_field_checkbox_option', array( $this, 'checkbox_option' ), 10, 2 );
		add_action( 'epl_render_field_checkbox_single', array( $this, 'checkbox_single' ), 10, 2 );
		add_action( 'epl_render_field_radio', array( $this, 'radio' ), 10, 2 );
		add_action( 'epl_render_field_file', array( $this, 'file' ), 10, 2 ); // File and image.
		add_action( 'epl_render_field_image', array( $this, 'file' ), 10, 2 ); // File and image.
		add_action( 'epl_render_field_editor', array( $this, 'editor' ), 10, 2 );
		add_action( 'epl_render_field_textarea', array( $this, 'textarea' ), 10, 2 );
		add_action( 'epl_render_field_decimal', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_number', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_date', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_auction-date', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_sold-date', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_email', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_url', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_button', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_color', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_text', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_hidden', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_submit', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_password', array( $this, 'render_default' ), 10, 2 );
		add_action( 'epl_render_field_locked', array( $this, 'locked' ), 10, 2 );
		add_action( 'epl_render_field_help', array( $this, 'help' ), 10, 2 );

	}

	/**
	 * Process field paramaters.
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 *
	 * @return array The array of fields.
	 */
	public function process_field_params( $field, $val ) {

		$field = apply_filters( 'pre_process_field_params', $field );

		if ( 'date' === $field['type'] ) {
			$field['data-format']     = isset( $field['format'] ) ? $field['format'] : 'Y-m-d';
			$field['data-timepicker'] = isset( $field['timepicker'] ) ? $field['timepicker'] : false;
		}

		$field['id']           = $this->get_id( $field );
		$field['name']         = $this->get_name( $field );
		$field['class']        = $this->get_class( $field );
		$field['data']         = $this->get_data_attributes( $field );
		$field['min']          = $this->get_min( $field );
		$field['max']          = $this->get_max( $field );
		$field['maxlength']    = $this->get_maxlength( $field );
		$field['placeholder']  = $this->get_placeholder( $field );
		$field['autocomplete'] = $this->get_autocomplete( $field );
		$field['multiple']     = $this->is_multiple( $field );
		$field['required']     = $this->is_required( $field );
		$field['value']        = 'button' === $field['type'] ? $field['value'] : $val;
		return apply_filters( 'post_process_field_params', $field );
	}

	/**
	 * Gets the minimum.
	 *
	 * @param array $field The field.
	 *
	 * @return string The minimum.
	 */
	public function get_min( $field ) {
		return isset( $field['min'] ) ? $field['min'] : '';
	}

	/**
	 * Determines whether the specified field is multiple.
	 *
	 * @param array $field The field.
	 *
	 * @return boolean True if the specified field is multiple, False otherwise.
	 */
	public function is_multiple( $field ) {
		return 'select_multiple' === $field['type'] ? ' multiple ' : '';
	}

	/**
	 * Gets the maximum.
	 *
	 * @param array $field The field.
	 *
	 * @return string The maximum.
	 */
	public function get_max( $field ) {
		return isset( $field['max'] ) ? $field['max'] : '';
	}

	/**
	 * Gets the maxlength.
	 *
	 * @param array $field The field.
	 *
	 * @return string The maxlength.
	 */
	public function get_maxlength( $field ) {
		return isset( $field['maxlength'] ) ? (int) $field['maxlength'] : '';
	}

	/**
	 * Gets the placeholder.
	 *
	 * @param array $field The field.
	 *
	 * @return string The placeholder.
	 */
	public function get_placeholder( $field ) {
		return isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	}

	/**
	 * Gets the autocomplete.
	 *
	 * @param array $field The field.
	 *
	 * @return string The autocomplete.
	 */
	public function get_autocomplete( $field ) {
		return isset( $field['autocomplete'] ) ? $field['autocomplete'] : '';
	}

	/**
	 * Determines whether the specified field is required.
	 *
	 * @param array $field The field.
	 *
	 * @return boolean True if the specified field is required, False otherwise.
	 */
	public function is_required( $field ) {
		return ! empty( $field['required'] ) ? ' required ' : '';
	}

	/**
	 * Gets the identifier.
	 *
	 * @param array $field The field.
	 *
	 * @return boolean The identifier.
	 */
	public function get_id( $field ) {
		$id = isset( $field['id'] ) ? $field['id'] : '';

		if ( empty( $id ) ) {
			$id = isset( $field['name'] ) ? $field['name'] : '';
		}

		return $id;
	}

	/**
	 * Gets the name.
	 *
	 * @param array $field The field.
	 *
	 * @return string The name.
	 */
	public function get_name( $field ) {
		return isset( $field['name'] ) ? $field['name'] : '';
	}

	/**
	 * Gets the decimal validation class.
	 *
	 * @param array $field The field.
	 *
	 * @return string The decimal validation class.
	 */
	public function get_decimal_validation_class( $field = array() ) {
		return apply_filters( 'epl_form_field_decimal_validation_class', 'validate[custom[onlyNumberWithDecimal]]', $field );
	}

	/**
	 * Gets the number validation class.
	 *
	 * @param array $field The field.
	 *
	 * @return string The number validation class.
	 */
	public function get_number_validation_class( $field = array() ) {
		return apply_filters( 'epl_form_field_number_validation_class', 'validate[custom[onlyNumber]]', $field );
	}

	/**
	 * Gets the email validation class.
	 *
	 * @param array $field The field.
	 *
	 * @return string The email validation class.
	 */
	public function get_email_validation_class( $field = array() ) {
		return apply_filters( 'epl_form_field_number_validation_class', 'validate[custom[email]]', $field );
	}

	/**
	 * Gets the url validation class.
	 *
	 * @param array $field The field.
	 *
	 * @return string The The url validation class.
	 */
	public function get_url_validation_class( $field = array() ) {
		return apply_filters( 'epl_form_field_number_validation_class', 'validate[custom[url]]', $field );
	}

	/**
	 * Gets the class.
	 *
	 * @param array $field The field.
	 *
	 * @return boolean The class.
	 */
	public function get_class( $field ) {

		$classes = isset( $field['class'] ) ? $field['class'] : '';

		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}
		$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

		// Append validation classes.
		if ( 'decimal' === $field['type'] ) {
			$classes .= ' ' . $this->get_decimal_validation_class( $field );
		} elseif ( 'number' === $field['type'] ) {
			$classes .= ' ' . $this->get_number_validation_class( $field );
		} elseif ( 'email' === $field['type'] ) {
			$classes .= ' ' . $this->get_email_validation_class( $field );
		} elseif ( 'url' === $field['type'] ) {
			$classes .= ' ' . $this->get_url_validation_class( $field );
		} elseif ( 'date' === $field['type'] ) {
			$classes .= ' epldatepicker ';
		}

		// Append type class.
		$classes .= ' ' . $this->class_prefix . sanitize_text_field( $field['type'] );
		return $classes;
	}

	/**
	 * Gets the data attributes.
	 *
	 * @param array $field The field.
	 *
	 * @return string
	 */
	public function get_data_attributes( $field ) {

		$atts_html = '';
		if ( ! empty( $field['data'] ) ) {
			foreach ( $field['data'] as $data_key => $data_value ) {

				if ( ! epl_starts_with( $data_key, 'data-' ) ) {
					$data_key = 'data-' . $data_key;
				}

				if ( ! is_array( $data_value ) && ! is_object( $data_value ) ) {
					$atts_html .= $data_key . "='" . $data_value . "'";
				} else {
					$atts_html .= $data_key . "='" . wp_json_encode( $data_value ) . "'";
				}
			}
		}

		foreach ( $field as $key => $value ) {

			if ( epl_starts_with( $key, 'data-' ) ) {
				if ( ! is_array( $value ) && ! is_object( $value ) ) {
					$atts_html .= $key . "='" . $value . "'";
				} else {
					$atts_html .= $key . "='" . wp_json_encode( $value ) . "'";
				}
			}
		}

		return $atts_html;

	}

	/**
	 * Determines whether the specified field is name array.
	 *
	 * @param array $field The field.
	 *
	 * @return boolean True if the specified field is name array, False otherwise.
	 */
	public function is_name_array( $field ) {

		$name_arrays = apply_filters( 'epl_form_field_name_arrays', array( 'select_multiple', 'checkbox' ) );
		return in_array( $field['type'], $name_arrays, true ) ? true : false;

	}

	/**
	 * Gets the opening field tag.
	 *
	 * @param string  $tag        The tag.
	 * @param array   $field      The field.
	 * @param boolean $self_close The self close.
	 *
	 * @return     string   The opening field tag.
	 *
	 * @since 2.3.0
	 * @since 3.4.38 PHP 8.0 fix for Required parameter $field follows optional parameter $tag.
	 */
	public function get_opening_field_tag( $tag = 'input', $field = [], $self_close = false ) {

		$tag = sanitize_key( $tag );

		$html = '<' . $tag . ' ';

		if ( ! empty( $field['type'] ) && ! in_array( $field['type'], array( 'select', 'select_multiple', 'textarea' ), true ) ) {
			$html .= ' type ="' . esc_attr( $field['type'] ) . '" ';
			$html .= ' value ="' . esc_attr( stripslashes( $field['value'] ) ) . '" ';
		}

		if ( ! empty( $field['multiple'] ) ) {
			$html .= esc_attr( $field['multiple'] );
		}

		if ( ! empty( $field['min'] ) ) {
			$html .= ' min ="' . esc_attr( $field['min'] ) . '" ';
		}

		if ( ! empty( $field['max'] ) ) {
			$html .= ' max ="' . esc_attr( $field['max'] ) . '" ';
		}

		if ( ! empty( $field['maxlength'] ) ) {
			$html .= ' maxlength ="' . esc_attr( $field['maxlength'] ) . '" ';
		}

		if ( ! empty( $field['class'] ) ) {
			$html .= ' class ="' . esc_attr( $field['class'] ) . '" ';
		}

		if ( ! empty( $field['name'] ) ) {
			$name = esc_attr( $field['name'] );
			if ( $this->is_name_array( $field ) ) {
				$name .= '[]';
			}
			$html .= ' name ="' . $name . '" ';
		}

		if ( ! empty( $field['id'] ) ) {
			$html .= ' id ="' . esc_attr( $field['id'] ) . '" ';
		}

		if ( ! empty( $field['placeholder'] ) ) {
			$html .= ' placeholder ="' . esc_attr( $field['placeholder'] ) . '" ';
		}

		if ( ! empty( $field['autocomplete'] ) ) {
			$html .= ' autocomplete ="' . esc_attr( $field['autocomplete'] ) . '" ';
		}

		if ( ! empty( $field['data'] ) ) {
			$html .= ' ' . wp_kses_post( $field['data'] );
		}

		$html .= $self_close ? '/>' : '>';

		return $html;
	}

	/**
	 * Renders the field based on field type
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function render( $field = array(), $val = '' ) {

		global $post;

		if ( ! is_null( $post ) ) {
			$this->post = $post;
		}

		$type  = sanitize_text_field( $field['type'] );
		$field = $this->process_field_params( $field, $val );
		do_action( 'epl_render_field_' . $type, $field, $val );

		if ( isset( $field['geocoder'] ) ) {
			if ( 'true' == $field['geocoder'] ) { //phpcs:ignore
				echo '<span class="epl-geocoder-button"></span>';
			}

			do_action( 'epl_admin_listing_map', esc_attr( stripslashes( $val ) ) );
		}

		if ( isset( $field['help'] ) ) {
			$field['help'] = trim( $field['help'] );
			if ( ! empty( $field['help'] ) ) {
				echo '<span class="epl-help-text">' . wp_kses_post( $field['help'] ) . '</span>';
			}
		}
	}

	/**
	 * Renders select
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function select( $field, $val ) {

		if ( $this->is_multiple( $field ) ) {
			$val = (array) $val;
		}

		echo $this->get_opening_field_tag( 'select', $field ); //phpcs:ignore

		if ( isset( $field['opts'] ) && ! empty( $field['opts'] ) ) {

			foreach ( $field['opts'] as $k => $v ) {
				$selected = '';

				if ( $this->is_multiple( $field ) ) {
					if ( in_array( $k, $val ) ) { //phpcs:ignore
						$selected = 'selected';
					}
				} else {
					if ( $val == $k ) { //phpcs:ignore
						$selected = 'selected';
					}
				}

				if ( is_array( $v ) ) {
					if ( isset( $v['exclude'] ) && ! empty( $v['exclude'] ) ) {
						if ( ! is_null( $this->post ) && in_array( $this->post->post_type, $v['exclude'], true ) ) {
							continue;
						}
					}

					if ( isset( $v['include'] ) && ! empty( $v['include'] ) ) {
						if ( ! is_null( $this->post ) && ! in_array( $this->post->post_type, $v['include'], true ) ) {
							continue;
						}
					}
					$v = $v['label'];
				}

				echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $v ) . '</option>';
			}
		} else {
			echo '<option value=""> </option>';
		}
		echo '</select>';
	}

	/**
	 * Renders checkbox
	 *
	 * @param array  $field The field.
	 * @param string $val  The value.
	 */
	public function checkbox( $field, $val ) {

		if ( ! empty( $field['opts'] ) ) {
			foreach ( $field['opts'] as $k => $v ) {
				$checked = '';

				if ( ! empty( $val ) ) {

					$val = (array) $val;
					if ( in_array( $k, $val ) ) { //phpcs:ignore
						$checked = 'checked';
					}
				}
				echo '<span class="epl-field-row">
						<input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" 
							' . wp_kses_post( $field['data'] ) . ' 
							class="' . esc_attr( $field['class'] ) . '"  
							id="' . esc_attr( $field['id'] ) . '_' . esc_attr( $k ) . '" 
							value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> 
						<label for="' . esc_html( $field['id'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label>
					</span>';
			}
		}
	}

	/**
	 * Renders checkbox single
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 *
	 * @since 3.4.34 Tweak: Added hidden empty field for checkboxes so that they always show up in $_POST data while saving.
	 */
	public function checkbox_single( $field, $val ) {

		if ( ! empty( $field['opts'] ) ) {
			foreach ( $field['opts'] as $k => $v ) {
				$checked = '';
				if ( ! empty( $val ) ) {
					$checkbox_single_options = apply_filters( 'epl_checkbox_single_check_options', array( 1, 'yes', 'on', 'true', '1' ) );
					if ( $k == $val || in_array( $val, $checkbox_single_options, true ) ) { //phpcs:ignore
						$checked = 'checked';
					}
				}
				if ( 1 === count( $field['opts'] ) ) {
					$v = $field['label'];
				}
				echo '<span class="epl-field-row">
				<input type="hidden" name="' . esc_attr( $field['name'] ) . '" value="" />
				<input type="checkbox" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . wp_kses_post( $field['data'] ) . ' id="' . esc_attr( $field['id'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['id'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
			}
		}
	}

	/**
	 * Renders checkbox option
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 *
	 * @since 3.4.34 Tweak: Added hidden empty field for checkboxes so that they always show up in $_POST data while saving.
	 */
	public function checkbox_option( $field, $val ) {

		if ( ! empty( $field['opts'] ) ) {
			foreach ( $field['opts'] as $k => $v ) {
				$checked = '';
				if ( ! empty( $val ) ) {
					if ( $k == $val ) { //phpcs:ignore
						$checked = 'checked';
					}
				}
				echo '<span class="epl-field-row">
				<input type="hidden" name="' . esc_attr( $field['name'] ) . '" value="" />
				<input type="checkbox" name="' . esc_attr( $field['name'] ) . '" ' . wp_kses_post( $field['data'] ) . ' id="' . esc_attr( $field['id'] ) . '_' . esc_attr( $k ) . '" class="' . esc_attr( $field['class'] ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['id'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
			}
		}
	}

	/**
	 * Renders radio
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function radio( $field, $val ) {

		if ( ! empty( $field['opts'] ) ) {
			foreach ( $field['opts'] as $k => $v ) {
				$checked = '';
				if ( $val == $k ) { //phpcs:ignore
					$checked = 'checked';
				}
				echo '<span class="epl-field-row"><input type="radio" name="' . esc_attr( $field['name'] ) . '" ' . wp_kses_post( $field['data'] ) . ' class="' . esc_attr( $field['class'] ) . '"  id="' . esc_attr( $field['id'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['id'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
			}
		}
	}

	/**
	 * Renders file / image
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function file( $field, $val ) {
		if ( is_array( $val ) ) {
			$val = isset( $val['image_url_or_path'] ) ? $val['image_url_or_path'] : '';
		}

		if ( ! empty( $val ) ) {
			$img = esc_attr( stripslashes( $val ) );
		} else {
			$img = plugin_dir_url( __DIR__ ) . 'assets/images/no_image.png'; //phpcs:ignore
		}

		echo '
			<div class="epl-media-row">
				<input type="text" ' . wp_kses_post( $field['data'] ) . ' class="' . esc_attr( $field['class'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" />
				&nbsp;&nbsp;<input type="button" name="epl_upload_button" class="button" value="' . esc_html__( 'Add File', 'easy-property-listings' ) . '" />';

		if ( in_array( pathinfo( $img, PATHINFO_EXTENSION ), array( 'jpg', 'jpeg', 'png', 'gif' ), true ) ) {
			echo '&nbsp;&nbsp;<img src="' . esc_url( $img ) . '" alt="" />';
		}
		echo '<div class="epl-clear"></div>
			</div>
		';
	}

	/**
	 * Renders editor
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function editor( $field, $val ) {
		wp_editor(
			stripslashes( $val ),
			esc_attr( $field['id'] ),
			$settings = array(
				'textarea_rows' => 5,
				'editor_class'  => $field['class'],
			)
		);
	}

	/**
	 * Renders textarea
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function textarea( $field, $val ) {
		echo $this->get_opening_field_tag( 'textarea', $field ); //phpcs:ignore
		echo wp_kses_post( stripslashes( $val ) );
		echo '</textarea>';
	}

	/**
	 * Renders locked
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function locked( $field, $val ) {
		echo '<span>' . esc_attr( stripslashes( $field['value'] ) ) . '</span>';
	}

	/**
	 * Renders help
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 */
	public function help( $field, $val ) {

		$content = isset( $field['content'] ) ? $field['content'] : '';
		$help_id = isset( $field['name'] ) ? sanitize_key( $field['name'] ) : '';
		//phpcs:ignore
		echo '<div class="epl-help-container" id="'.$help_id.'">
				' . wp_kses_post( $content ) . '
			</div>';
	}

	/**
	 * Renders default input types
	 *
	 * @param array  $field The field.
	 * @param string $val   The value.
	 * @since 3.4.35 fixed hidden field rendering.
	 */
	public function render_default( $field, $val ) {

		if ( ! in_array( $field['type'], array( 'button', 'number', 'color', 'submit', 'hidden' ), true ) ) {
			$field['type'] = 'text';
		}
		echo $this->get_opening_field_tag( 'input', $field, true ); //phpcs:ignore
	}
}
