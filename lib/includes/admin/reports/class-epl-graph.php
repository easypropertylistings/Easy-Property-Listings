<?php
/**
 * Reports
 *
 * This class handles building report graphs
 *
 * @package     EPL
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Graph Class
 *
 * @since 3.0
 */
class EPL_Graph {

	/**
	 * Data to graph
	 *
	 * @var array
	 * @since 3.0
	 */
	private $data;

	/**
	 * Unique ID for the graph
	 *
	 * @var string
	 * @since 3.0
	 */
	private $id = '';

	/**
	 * Graph options
	 *
	 * @var array
	 * @since 3.0
	 */
	private $options = array();

	/**
	 * Get things started
	 *
	 * @since 3.0
	 * @param string $_data the data.
	 */
	public function __construct( $_data ) {

		$this->data = $_data;

		// Generate unique ID.
		$this->id = 'a' . md5( wp_rand() );

		// Setup default options.
		$this->options = array(
			'y_mode'          => null,
			'x_mode'          => null,
			'y_decimals'      => 0,
			'x_decimals'      => 0,
			'y_position'      => 'right',
			'time_format'     => '%d/%b',
			'ticksize_unit'   => 'day',
			'ticksize_num'    => 1,
			'multiple_y_axes' => false,
			'bgcolor'         => '#f9f9f9',
			'bordercolor'     => '#ccc',
			'color'           => '#bbb',
			'borderwidth'     => 2,
			'bars'            => false,
			'lines'           => true,
			'points'          => true,
		);
	}

	/**
	 * Set an option
	 *
	 * @since 3.0.0
	 * @param string $key The option key to set.
	 * @param string $value The value to assign to the key.
	 */
	public function set( $key, $value ) {
		$this->options[ $key ] = $value;
	}

	/**
	 * Get an option
	 *
	 * @param string $key The option key to get.
	 *
	 * @return bool|mixed
	 * @since 3.0
	 */
	public function get( $key ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : false;
	}

	/**
	 * Get graph data
	 *
	 * @since 3.0
	 */
	public function get_data() {
		return apply_filters( 'epl_get_graph_data', $this->data, $this );
	}

	/**
	 * Load the graphing library script
	 *
	 * @since 3.0
	 */
	public function load_scripts() {
		// Use minified libraries if SCRIPT_DEBUG is turned off.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'epl-jquery-flot', EPL_PLUGIN_URL . 'lib/assets/js/jquery-flot' . $suffix . '.js', array(), EPL_PROPERTY_VER, true );

		do_action( 'epl_graph_load_scripts' );
	}

	/**
	 * Build the graph and return it as a string
	 *
	 * @var array
	 * @since 3.0
	 * @return string
	 */
	public function build_graph() {

		$yaxis_count = 1;

		$this->load_scripts();

		ob_start();
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function($) {
				$.plot(
					$("#epl-graph-<?php echo esc_attr( $this->id ); ?>"),
					[
						<?php foreach ( $this->get_data() as $label => $data ) : ?>
						{
							label: "<?php echo esc_attr( $data['label'] ); ?>",
							id: "<?php echo esc_attr( sanitize_key( $data['id'] ) ); ?>",

							<?php if ( ! is_null( $data['color'] ) ) : ?>

								color: "<?php echo esc_attr( $data['color'] ); ?>",

							<?php endif; ?>
							// data format is: [ point on x, value on y ]
							data: [
							<?php
							foreach ( $data['data'] as $point ) {
								echo esc_attr( '[' . implode( ',', $point ) . '],' );
							}
							?>
							],
							points: {
								show: <?php echo $this->options['points'] ? 'true' : 'false'; ?>,
							},
							bars: {
								show: <?php echo $this->options['bars'] ? 'true' : 'false'; ?>,
								barWidth: 12,
								aling: 'center',
							},
							lines: {
								show: <?php echo $this->options['lines'] ? 'true' : 'false'; ?>
							},
							<?php if ( $this->options['multiple_y_axes'] ) : ?>
							yaxis: <?php echo esc_attr( $yaxis_count ); ?>
							<?php endif; ?>
						},
							<?php
							$yaxis_count++;
							endforeach;
						?>
					],
					{
						// Options
						grid: {
							show: true,
							aboveData: false,
							color: "<?php echo esc_attr( $this->options['color'] ); ?>",
							backgroundColor: "<?php echo esc_attr( $this->options['bgcolor'] ); ?>",
							borderColor: "<?php echo esc_attr( $this->options['bordercolor'] ); ?>",
							borderWidth: <?php echo esc_attr( absint( $this->options['borderwidth'] ) ); ?>,
							clickable: false,
							hoverable: true
						},
						xaxis: {
							mode: "<?php echo esc_attr( $this->options['x_mode'] ); ?>",
							timeFormat: "<?php echo 'time' === $this->options['x_mode'] ? esc_attr( $this->options['time_format'] ) : ''; ?>",
							tickSize: "<?php echo 'time' === $this->options['x_mode'] ? '' : esc_attr( $this->options['ticksize_num'] ); ?>",
							<?php if ( 'time' !== $this->options['x_mode'] ) : ?>
							tickDecimals: <?php echo esc_attr( $this->options['x_decimals'] ); ?>
							<?php endif; ?>
						},
						yaxis: {
							position: 'right',
							axisLabelColor: '#ff0000',
							min: 0,
							mode: "<?php echo esc_attr( $this->options['y_mode'] ); ?>",
							timeFormat: "<?php echo 'time' === $this->options['y_mode'] ? esc_attr( $this->options['time_format'] ) : ''; ?>",
							<?php if ( 'time' !== $this->options['y_mode'] ) : ?>
							tickDecimals: <?php echo esc_attr( $this->options['y_decimals'] ); ?>
							<?php endif; ?>
						}
					}

				);

				function epl_flot_tooltip(x, y, contents) {
					$('<div id="epl-flot-tooltip">' + contents + '</div>').css( {
						position: 'absolute',
						display: 'none',
						top: y + 5,
						left: x + 5,
						border: '1px solid #fdd',
						padding: '2px',
						'background-color': '#fee',
						opacity: 0.80
					}).appendTo("body").fadeIn(200);
				}

				var previousPoint = null;
				$("#epl-graph-<?php echo esc_attr( $this->id ); ?>").bind("plothover", function (event, pos, item) {
					$("#x").text(pos.x.toFixed(2));
					$("#y").text(pos.y.toFixed(2));
					if (item) {
						if (previousPoint != item.dataIndex) {
							previousPoint = item.dataIndex;
							$("#epl-flot-tooltip").remove();
							var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
							if( item.series.id == 'earnings' ) {
								if( epl_vars.currency_pos == 'before' ) {
									epl_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + epl_vars.currency_sign + y );
								} else {
									epl_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + y + epl_vars.currency_sign );
								}
							} else {
								epl_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + y.replace( '.00', '' ) );
							}
						}
					} else {
						$("#epl-flot-tooltip").remove();
						previousPoint = null;
					}
				});

			});

		</script>
		<div id="epl-graph-<?php echo esc_attr( $this->id ); ?>" class="epl-graph" style="height: 300px;"></div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Output the final graph
	 *
	 * @since 3.0
	 */
	public function display() {
		do_action( 'epl_before_graph', $this );
		echo $this->build_graph(); //phpcs:ignore WordPress.Security.EscapeOutput
		do_action( 'epl_after_graph', $this );
	}
}
