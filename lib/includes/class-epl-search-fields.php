<?php
/**
 * Search Object
 *
 * @package     EPL
 * @subpackage  Classes/Search
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
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
		add_action( 'epl_frontend_search_field_radio', array( $this, 'render_radio' ), 10, 5 );
		add_action( 'epl_frontend_search_field_checkbox_multiple', array( $this, 'render_checkbox_multiple' ), 10, 5 );

	}

	/**
	 * Renders search frontend Text field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_text( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		?>
		<div class="epl-search-row epl-search-row-text epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">
				<input
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					type="text"
					class="in-field field-width <?php echo esc_attr( $field['meta_key'] ); ?>"
					name="<?php echo esc_attr( $field['meta_key'] ); ?>"
					id="<?php echo esc_attr( $field['meta_key'] ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				/>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders search frontend Checkbox field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_checkbox( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		?>
		<span class="epl-search-row epl-search-row-checkbox <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<input type="checkbox" name="<?php echo esc_attr( $field['meta_key'] ); ?>" id="<?php echo esc_attr( $field['meta_key'] ); ?>" class="in-field <?php echo esc_attr( $field['meta_key'] ); ?>"
			<?php
			if ( isset( $value ) && ! empty( $value ) ) {
				echo 'checked="checked"'; }
			?>
			/>
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="check-label">
			<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
		</span>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders search frontend Select field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_select( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {

		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}

		$min_max_atts = '';

		if ( isset( $field['query']['type'] ) && 'numeric' === $field['query']['type'] ) {
			current( $field['options'] );
			$opt_min = key( $field['options'] );
			end( $field['options'] );
			$opt_max      = key( $field['options'] );
			$min_max_atts = ' data-min="' . $opt_min . '" data-max="' . $opt_max . '" ';
		}

		?>
		<div class="epl-search-row epl-search-row-select epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">
				<select
					name="<?php echo esc_attr( $field['meta_key'] ); ?>"
					id="<?php echo esc_attr( $field['meta_key'] ); ?>"
					<?php echo $min_max_atts; //phpcs:ignore ?>
					class="in-field field-width <?php echo esc_attr( $field['meta_key'] ); ?>">
					<option value="">
						<?php echo esc_attr( apply_filters( 'epl_search_widget_option_label_' . $field['option_filter'], __( 'Any', 'easy-property-listings' ) ) ); ?>
					</option>
					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							echo '<option value="' . esc_attr( $k ) . '"' . selected( $k, $value, false ) . '>' . esc_attr( $v ) . '</option>';
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
	 * Renders search frontend Multiple Select field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_multiple_select( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-select epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">
				<select name="<?php echo esc_attr( $field['meta_key'] ); ?>[]"
					id="<?php echo esc_attr( $field['meta_key'] ); ?>"
					class="in-field field-width field-multiple <?php echo esc_attr( $field['meta_key'] ); ?>" multiple>
					<option value="">
						<?php echo esc_attr( apply_filters( 'epl_search_widget_option_label_' . $field['option_filter'], __( 'Any', 'easy-property-listings' ) ) ); ?>
					</option>
					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							$selected = in_array( $k, (array) $value, true ) ? true : false;
							echo '<option value="' . esc_attr( $k ) . '"' . selected( $selected, true, false ) . '>' . esc_attr( $v ) . '</option>';
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
	 * Renders search frontend Number field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_number( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		?>
		<div class="epl-search-row epl-search-row-number epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">
				<input type="number" class="in-field field-width <?php echo esc_attr( $field['meta_key'] ); ?>"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					name="<?php echo esc_attr( $field['meta_key'] ); ?>"
					id="<?php echo esc_attr( $field['meta_key'] ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				/>
			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders search frontend Hidden field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_hidden( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		?>
		<input type="hidden" class="in-field field-width <?php echo esc_attr( $field['meta_key'] ); ?>"
			name="<?php echo esc_attr( $field['meta_key'] ); ?>"
			id="<?php echo esc_attr( $field['meta_key'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
		/>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Renders search frontend Radio field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_radio( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-radio epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">

					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							?>
							<input
								type="radio"
								<?php checked( $k, $value, true ); ?>
								name="<?php echo esc_attr( $field['meta_key'] ); ?>"
								id="<?php echo esc_attr( $field['meta_key'] ) . '_' . esc_attr( $k ); ?>"
								value="<?php echo esc_attr( $k ); ?>"
								class="in-field field-width <?php echo esc_attr( $field['meta_key'] ) . '_' . esc_attr( $k ); ?>" />
							<label class="epl-search-radio-label"><?php echo esc_attr( $v ); ?></label>
																			<?php
						}
					}
					?>

			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}

	}

	/**
	 * Renders search frontend Radio field.
	 *
	 * @since  3.0
	 * @param  array  $field The field.
	 * @param  string $config Configuration.
	 * @param  string $value Value.
	 * @param  string $post_type Post type name.
	 * @param  string $property_status Listing status.
	 * @return void
	 */
	public function render_checkbox_multiple( array $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {
		if ( isset( $field['wrap_start'] ) ) {
			echo '<div class="' . esc_attr( $field['wrap_start'] ) . '">';
		}
		?>
		<div class="epl-search-row epl-search-row-checkbox-multiple epl-<?php echo esc_attr( $field['meta_key'] ); ?> fm-block <?php echo isset( $field['class'] ) ? esc_attr( $field['class'] ) : ''; ?>">
			<label for="<?php echo esc_attr( $field['meta_key'] ); ?>" class="epl-search-label fm-label">
				<?php echo esc_attr( apply_filters( 'epl_search_widget_label_' . $field['meta_key'], $field['label'] ) ); ?>
			</label>
			<div class="field">

					<?php
					if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
						foreach ( $field['options'] as $k => $v ) {
							$checked = in_array( $k, (array) $value, true ) ? 'checked' : '';
							?>
							<input
								type="checkbox"
								<?php echo ' ' . esc_attr( $checked ) . ' '; ?>
								name="<?php echo esc_attr( $field['meta_key'] ); ?>[]"
								id="<?php echo esc_attr( $field['meta_key'] ) . '_' . esc_attr( $k ); ?>"
								value="<?php echo esc_attr( $k ); ?>"
								class="in-field field-width <?php echo esc_attr( $field['meta_key'] ) . '_' . esc_attr( $k ); ?>" />
							<label class="epl-search-checkbox-label"><?php echo esc_attr( $v ); ?></label>
																				<?php
						}
					}
					?>

			</div>
		</div>
		<?php
		if ( isset( $field['wrap_end'] ) ) {
			echo '</div>';
		}
	}

}
