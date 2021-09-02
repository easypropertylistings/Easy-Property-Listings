<?php
/**
 * WIDGET :: EPL Tabs
 *
 * @package     EPL
 * @subpackage  Widget/EPL-Tabs
 * @copyright   Copyright (c) 2021, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Widget_Property_Gallery class
 *
 * @since 3.5.0
 */
class EPL_Tabs extends EPL_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->widget_cssclass    = 'epl-widget epl_widget_epl_tabs';
		$this->widget_description = __( 'Displays EPL Tabs.', 'easy-property-listings' );
		$this->widget_id          = 'epl_widget_epl_tabs';
		$this->widget_name        = __( 'EPL - Tabs', 'easy-property-listings' );
        
		// Settings for widget.
		$this->settings = array(
			'title' => array(
				'type'      => 'text',
				'default'   => __( 'EPL - Tabs', 'easy-property-listings' ),
				'label'     => __( 'Title', 'easy-property-listings' ),
			),
			'id' => array(
				'type'      => 'text',
				'default'   => $this->id,
				'label'     => __( 'ID', 'easy-property-listings' ),
			),
			'epl_tab_count' => array(
				'type'      => 'select',
				'default'   => 1,
                		'label'     => __( 'Tab Count', 'easy-property-listings' ),
                		'options'   => array_combine( range(1, $this->get_max_tabcount() ),  range(1, $this->get_max_tabcount() ) )  
            		),
            		'wrap_class' => array(
				'type'      => 'text',
				'default'   => '',
				'label'     => __( 'Wrapper Class', 'easy-property-listings' ),
            		),
            		'class' => array(
				'type'      => 'text',
				'default'   => '',
				'label'     => __( 'Class', 'easy-property-listings' ),
            		),
            		'template' => array(
				'type'      => 'text',
				'default'   => '',
				'label'     => __( 'Template Name', 'easy-property-listings' ),
            		),
            		'type' => array(
				'type'    => 'select',
				'default' => 'horizontal',
				'label'   => __( 'Type', 'easy-property-listings' ),
				'options' =>  array(
					'horizontal' =>  __( 'Horizontal', 'easy-property-listings' ),
					'vertical'   =>  __( 'Vertical', 'easy-property-listings' ),
				)
			),
		);

		$tab_count = $this->get_max_tabcount();

		for( $i=1; $i<=$tab_count; $i++ ) {

			$this->settings[ 'tab_title_'.$i ] = array(
				'type'      => 'text',
				'default'   => sprintf( __( 'EPL - Tabs %s', 'easy-property-listings' ), $i ),
				'label'     => sprintf( __( 'Tab Title %s', 'easy-property-listings' ), $i ),
			);
			$this->settings[ 'tab_id_'.$i ] = array(
				'type'      => 'text',
				'default'   => '',
				'label'     => sprintf( __( 'Tab ID %s', 'easy-property-listings' ), $i ),
			);
			$this->settings[ 'tab_content_'.$i ] = array(
				'type'      => 'textarea',
				'default'   => '',
				'label'     => sprintf( __( 'Tab Content %s', 'easy-property-listings' ), $i ),
			);
		}
        
		$this->settings = apply_filters('epl_widget_epl_tabs_settings', $this->settings );

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @since 3.5.0
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$this->widget_start( $args, $instance );

		$tab_count  = ! empty( $instance['epl_tab_count'] ) ? absint( $instance['epl_tab_count'] ) : $this->settings['epl_tab_count']['default'];
		$id         = ! empty( $instance['id'] ) ? $instance['id'] : $this->settings['id']['default'];
		$wrap_class = ! empty( $instance['wrap_class'] ) ? $instance['wrap_class'] : $this->settings['wrap_class']['default'];
		$class      = ! empty( $instance['class'] ) ? $instance['class'] : $this->settings['class']['default'];
		$type       = ! empty( $instance['type'] ) ? $instance['type'] : $this->settings['type']['default'];
		$template   = ! empty( $instance['template'] ) ? $instance['template'] : $this->settings['template']['default'];

		$shortcode = '[epl_tabs id="'.$id.'" wrap_class="'.$wrap_class.'" class="'.$class.'"  type="'.$type.'" template="'.$template.'" ]';
		
		for( $i=1; $i<=$tab_count; $i++ ) {
			$shortcode .= '[epl_tab id="'.$instance['tab_id_'.$i].'" title="'.$instance['tab_title_'.$i].'"  ]'.$instance['tab_content_'.$i].'[/epl_tab]';
		}

		$shortcode .= '[/epl_tabs]';
		echo do_shortcode($shortcode); // WPCS: XSS ok.
		$this->widget_end( $args );
		echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
	}
    
	/**
	 * Get Max Tab Count.
	 *
	 * @return int
	 * @since 3.5.0
	 */
	public function get_max_tabcount() {
		return 9;
	}
}

/**
 * Register EPL Tabs Widget.
 *
 * Registers the EPL Widgets.
 *
 * @since 3.5.0
 * @return void
 */
function epl_register_widget_epl_tabs() {
	register_widget( 'EPL_Tabs' );
}
add_action( 'widgets_init', 'epl_register_widget_epl_tabs' );
