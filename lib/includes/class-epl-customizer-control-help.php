<?php

/**
 * Multiple checkbox customize control class.
 *
 * @since  3.5.0
 * @access public
 */
class EPL_Customizer_Control_Help extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.5.0
	 * @access public
	 * @var    string
	 */
	public $type = 'help';

	/**
	 * Displays the control content.
	 *
	 * @since  3.5.0
	 * @access public
	 * @return void
	 */
	public function render_content() {  ?>

		<?php if ( !empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
		<?php endif; ?>

	<?php }
}