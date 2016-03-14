<?php
/**
 * Search Object
 *
 * @package     EPL
 * @subpackage	Classes/Search
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Search_Fields Class
 *
 * @since      3.0
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class EPL_Search_Fields {

	/**
	 * Initialize hooks.
	 *
	 * @since  3.0
	 * @return void
	 */
	public function init() {
		// Initialize hooks for displaying search frontend fields.
		add_action( 'epl_frontend_search_field_text', array( $this, 'render_text' ), 10, 5 );
		add_action( 'epl_frontend_search_field_checkbox', array( $this, 'render_checkbox' ), 10, 5 );
		add_action( 'epl_frontend_search_field_select', array( $this, 'render_select' ), 10, 5 );
		add_action( 'epl_frontend_search_field_multiple_select', array( $this, 'render_multiple_select' ), 10, 5 );
		add_action( 'epl_frontend_search_field_number', array( $this, 'render_number' ), 10, 5 );
		add_action( 'epl_frontend_search_field_hidden', array( $this, 'render_hidden' ), 10, 5 );
	}

	/**
	 * Renders serach frontend Text field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_text( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-text epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
			<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
				<?php echo apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ); ?>
			</label>
			<div class="field">
				<input
					type="text"
					class="in-field field-width"
					name="<?php echo $field['meta_key']; ?>"
					id="<?php echo $field['meta_key']; ?>"
					value="<?php echo $value; ?>"
				/>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders serach frontend Checkbox field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_checkbox( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<span class="epl-search-row epl-search-row-checkbox <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
			<input type="checkbox" name="<?php echo $field['meta_key']; ?>" id="<?php echo $field['meta_key']; ?>" class="in-field"
			<?php if ( isset( $value ) && ! empty( $value ) ) { echo 'checked="checked"'; } ?> />
			<label for="<?php echo $field['meta_key']; ?>" class="check-label">
			<?php echo apply_filters( 'epl_search_widget_label_' . $field['meta_key'], __( $field['label'], 'epl' ) ); ?>
			</label>
		</span>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders serach frontend Select field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_select( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-select epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
			<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
				<?php echo apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ); ?>
			</label>
			<div class="field">
				<select
					name="<?php echo $field['meta_key']; ?>"
					id="<?php echo $field['meta_key']; ?>"
					class="in-field field-width">
					<option value="">
						<?php echo apply_filters( 'epl_search_widget_option_label_' . $field['option_filter'], __( 'Any', 'epl' ) ); ?>
					</option>
					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							echo '<option value="' . esc_attr( $k ) . '"' . selected( $k, $value, false ) . '>' . $v . '</option>';
						}
					}
					?>
				</select>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders serach frontend Multiple Select field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_multiple_select( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-select epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
			<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
				<?php echo apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ); ?>
			</label>
			<div class="field">
				<select name="<?php echo $field['meta_key']; ?>"
					id="<?php echo $field['meta_key']; ?>"
					class="in-field field-width" multiple>
					<option value="">
						<?php echo apply_filters( 'epl_search_widget_option_label_' . $field['option_filter'], __( 'Any', 'epl' ) ); ?>
					</option>
					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							$selected = in_array( $k, $value ) ? true : false;
							echo '<option value="' . esc_attr( $k ) . '"' . selected( $selected, true, false ) . '>' . $v . '</option>';
						}
					}
					?>
				</select>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders serach frontend Number field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_number( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-number epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
			<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
				<?php echo apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ); ?>
			</label>
			<div class="field">
				<input type="number" class="in-field field-width"
					name="<?php echo $field['meta_key']; ?>"
					id="<?php echo $field['meta_key']; ?>"
					value="<?php echo $value; ?>"
				/>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders serach frontend Hidden field.
	 *
	 * @since  3.0
	 * @param  array  $field
	 * @param  string $config
	 * @param  string $value
	 * @param  string $post_type
	 * @param  string $property_status
	 * @return void
	 */
	public function render_hidden( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . $field['wrap_start'] . '">';
		}
		?>
		<input type="hidden" class="in-field field-width"
			name="<?php echo $field['meta_key']; ?>"
			id="<?php echo $field['meta_key']; ?>"
			value="<?php echo $value; ?>"
		/>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}
}
