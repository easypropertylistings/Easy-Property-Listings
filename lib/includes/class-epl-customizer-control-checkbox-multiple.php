<?php

/**
 * Multiple checkbox customize control class.
 *
 * @since  3.5.0
 * @access public
 */
class EPL_Customizer_Control_Checkbox_Multiple extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.5.0
	 * @access public
	 * @var    string
	 */
	public $type = 'checkbox_multiple';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  3.5.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		
	}

	/**
	 * Displays the control content.
	 *
	 * @since  3.5.0
	 * @access public
	 * @return void
	 */
	public function render_content() {
		
		if ( empty( $this->choices ) )
			return; ?>

		<?php if ( !empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>

		<?php if ( !empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
		<?php endif; ?>

		<?php $multi_values = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

		<ul>
			<?php foreach ( $this->choices as $value => $label ) : ?>

				<li>
					<label>
						<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				</li>

			<?php endforeach; ?>
		</ul>

		<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
	<?php }
}