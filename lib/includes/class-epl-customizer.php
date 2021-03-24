<?php

/**
 * Adds options to the customizer for EPL.
 *
 * @version 3.5.0
 * @package EPL
 */

defined( 'ABSPATH' ) || exit;

/**
 * EPL_Customizer class.
 */
class EPL_Customizer {

    public $setting_slug = 'epl_settings';

    /**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'customize_register', array( $this, 'add_sections' ) );
        add_action( 'customize_register', array( $this, 'load_customize_controls' ), 0 );
		// add_action( 'customize_controls_print_styles', array( $this, 'add_styles' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30 );
		// add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_scripts' ) );
	}

    function load_customize_controls() {

        require_once EPL_PATH_LIB . 'includes/class-epl-customizer-control-checkbox-multiple.php';
        require_once EPL_PATH_LIB . 'includes/class-epl-customizer-control-help.php';
    }

    /**
	 * Add settings to the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_sections( $wp_customize ) {

		$wp_customize->add_panel(
			'epl_settings',
			array(
				'priority'       => 120,
				'capability'     => 'manage_options',
				'theme_supports' => '',
				'title'          => __( 'Easy Property Listings', 'easy-property-listings' ),
			)
		);

        $sections = epl_get_admin_option_fields();

        foreach( $sections as $section ) {

            $wp_customize->add_section(
                // $id
                $this->setting_slug.'_' . $section['id'], 
                // parameters
                array(
                    'title' 	    => $section['label'],
                    'description' 	=> $section['help'],
                    'panel'		    => 'epl_settings'
                )
            );
        }

        foreach( $sections as $section ) {

            // Loop through panel for fields
            foreach ( $section['fields'] as $field ) {


                if( 'checkbox' == $field['type'] ) {
                    $field['type'] = 'checkbox_multiple';
                }

                $wp_customize->add_setting(
                    // $id
                    $this->setting_slug.'[' . $field['name'] . ']',
                    // parameters array
                    array(
                        'default'		    => isset( $field['default'] ) ? $field['default'] : '' ,
                        'type'			    => 'option',
                        'sanitize_callback'	=> 'epl_sanitize_' . $field['type'],
            
                    )
                );

                $field_type = in_array( $field['type'], [ 'checkbox_single', 'checkbox_option'] ) ? 'checkbox' : $field['type'];
                
                if( 'checkbox_multiple' == $field_type ) {
                    $wp_customize->add_control(

                        new EPL_Customizer_Control_Checkbox_Multiple(
                            $wp_customize,
                            $this->setting_slug.'[' . $field['name'] . ']',
                            array(
                                'label'		    => isset( $field['label'] ) ? $field['label'] : '',
                                'section'	    =>  $this->setting_slug.'_' . $section['id'],
                                'settings'	    => $this->setting_slug.'['. $field['name'] . ']',
                                'type'		    => $field_type,
                                'description'   => isset( $field['help'] ) ? $field['help'] : '', 
                                'choices'       => !empty( $field['opts'] ) ? $field['opts'] : [] 
                            ) 
                        )
                    );

                } else if( 'help' == $field_type ) {
                    $wp_customize->add_control(

                        new EPL_Customizer_Control_Help(
                            $wp_customize,
                            $this->setting_slug.'[' . $field['name'] . ']',
                            array(
                                'label'		    => isset( $field['label'] ) ? $field['label'] : '',
                                'section'	    =>  $this->setting_slug.'_' . $section['id'],
                                'settings'	    => $this->setting_slug.'['. $field['name'] . ']',
                                'type'		    => $field_type,
                                'description'   => isset( $field['content'] ) ? $field['content'] : '', 
                            ) 
                        )
                    );

                } else {
                    $wp_customize->add_control(
                        // $id
                        $this->setting_slug.'[' . $field['name'] . ']',
                        // parameters array
                        array(
                            'label'		    => isset( $field['label'] ) ? $field['label'] : '',
                            'section'	    =>  $this->setting_slug.'_' . $section['id'],
                            'settings'	    => $this->setting_slug.'['. $field['name'] . ']',
                            'type'		    => $field_type,
                            'description'   => isset( $field['help'] ) ? $field['help'] : '', 
                            'choices'       => !empty( $field['opts'] ) ? $field['opts'] : [] 
                        ) 
                    );
                }
                
            }
        }

	}

    function add_scripts() { ?>

        <script>
            jQuery( document ).ready( function() {

                /* === Checkbox Multiple Control === */

                jQuery( document.body ).on(
                    'change', '.customize-control-checkbox_multiple input[type="checkbox"]', 
                    function() {

                        checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
                            function() {
                                return this.value;
                            }
                        ).get().join( ',' );

                        jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
                    }
                );

            } ); // jQuery( document ).ready
        </script>
        <?php
        
    }
    
}

new EPL_Customizer();