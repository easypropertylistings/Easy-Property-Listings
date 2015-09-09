<?php
/*
Plugin Name: Form Builder
Description: Form builder class for epl.
*/

class EPL_FORM_BUILDER {

	/**
	* prefix for this class, will be used for filters & actions
	*/
	private $prefix = 'epl_form_builder_';
	
	/**
	* text domain for translation
	*/
	private $text_domain = 'epl';
	
	/**
	* text domain for translation
	*/
	public $form_fields = array();
	
	/**
	* classes for form and fields
	*/
	private $form_classes = array();
	
	/**
	* configuration of this form
	*/
	public $configuration = array();
	
	/**
	* form has nonce or not
	*/
	public $has_nonce = false;
	
	/**
	* holds the list of form attributes, defaults & added by user
	*/
	public $form_attributes = array();
	
	function __construct($config = array() ) {
	
		$defaults = array(
			'form_tag'				=>	'on',
			'form_wrap'				=>	'on',
			'form_context'			=>	'default', // default , meta , settings
			'callback_action'		=>	NULL,
			'is_ajax'				=>	false
		);

		$this->configuration = shortcode_atts($defaults,$config);
		
		/** set form defaults **/
		$this->set_defaults();
		
	}
	
	/**
	* call default & user submitted callbacks for this form upon form submission
	*	
	* @return null
	*/
	function __destruct() {
	
		$this->callbacks();
	}
	
	function __get($key) {
		return isset($this->{$key}) ? $this->{$key} : null ; 
	}
	
	function callbacks() {
		
		if( isset($_REQUEST[$this->prefix.'form_submit']) ) {

			// hook to this action to save form data
			do_action($this->prefix.'save_form',get_object_vars($this),$_REQUEST);
			
			//run user defined hook if applicable
			if( !is_null($this->configuration['callback_action']) ) {
				do_action($this->prefix.$this->configuration['callback_action'],get_object_vars($this),$_REQUEST);
			}
				
		}
	}
	
	/**
	* Set form configuration 
	*
	*/
	function get_configuration($key='') {
		return isset($this->configuration[$key]) ? $this->configuration[$key] : false;
	}
	
	function get_value($field) {
		
		switch($this->configuration['form_context']) {
			
			case 'default':
				return isset($field['value']) ? $field['value'] : ( isset($field['default']) ? $field['default'] : '' );
			break;
			
			case 'meta':
				global $post;
				return isset($field['name']) ? get_post_meta($post->ID,$field['name'],true) : '';
			break;
			
			case 'settings':
				global $epl_settings;
				return isset($field['name']) ? $epl_settings[$field['name']] : '';
			break;
			
		}
	}
	
	/**
	* Set form classes
	*
	*/
	function set_form_classes() {
	
		$this->form_classes = apply_filters( $this->prefix.'form_classes',
			array(
				'form_container'	=>	array(
					$this->prefix.'form_container',
					'row',
				),
				'form'	=>	array(
					$this->prefix.'form',
					'col-md-12',
				),
				'field_container'	=>	array(
					$this->prefix.'field_container',
					'col-md-12',
				),
				'field'	=>	array(
					$this->prefix.'field',
					'col-md-12',
				),
				'label_container'	=>	array(
					$this->prefix.'label_container',
					'col-md-12',
				),
				'label'	=>	array(
					$this->prefix.'label',
					'col-md-12',
				),
			),
			$this->form_attributes 
		);
	}
	
	/**
	* List of invalid attributes per field type that must be removed field array before field rendering
	*
	*/	
	function invalid_attributes() {
		return apply_filters( $this->prefix.'invalid_attributes', 
			array(
				'label'		=>	array('all'),
				'default'	=>	array('all'),
				'opts'		=>	array('all'),
			) 
		);
	}
	
	/**
	* Get classes for wrappers, form , fields
	*
	*/
	function get_class( $key='',$field=array() ) {
		
		if( isset($this->form_classes[$key]) ) {
			$classes 	= implode(' ', array_map( 'sanitize_html_class' , $this->form_classes[$key] ) );
			if( isset($field['type']) ) {
				$classes 	.= " ".$this->prefix.$key.'_'.$field['type'];
			}
			
			if( $key=='field' && isset($field['class']) ) {
			
				if(is_array($field['class']) && count($field['class']) > 0 ) {
					$classes 	.= implode(' ', array_map( 'sanitize_html_class' ,$field['class'] ) );
				} else {
					$classes 	.= " ".$field['class'];
				}
			}
			return $classes;
		}
	}
	
	function get_attributes($field) {
	
		$html = '';
		$field['id']		= isset($field['id']) ? $field['id'] :  isset($field['name']) ? $field['name'] : '';
		$invalid_attributes = $this->invalid_attributes();

		foreach($field as $key 	=>	$value) {
		
			if( isset($invalid_attributes[$key]) && ( in_array( $field['type'], $invalid_attributes[$key] ) || in_array( 'all', $invalid_attributes[$key] ) )  ) {
				/** this attribute is not valid for current input type, lets skip it **/
			} else {
			
				if( isset($field['multiple']) && in_array($field['type'], array('select','checkbox') ) && $key == 'name') {
					$value = $value.'[]';
				}
				
				$html .= $key.'="'.$value.'" ';
			}
			
		}
		return $html;
	}
	
	/**
	* The function can be used to configure the attributes of form
	*
	* 
	*/
	function set_form_attributes($key='',$value='') {
		
		/* if user wants to add a single attribute */
		if( $key!='' && is_string($key)  ) {
			$this->form_attributes[$this->escape('text',$key)] = $this->escape('attribute',$value);
			
		} elseif(is_array($key) & !empty($key) ) {
		
			/* multiple attrubtes at once */
			foreach($key as $index	=>	$val) {
				$this->form_attributes[$this->escape('text',$index)] = $this->escape('attribute',$val);
			}
		
		}
	}
	
	
	/**
	* set default attributes for the form tag
	*
	* 
	*/
	private function set_default_form_attributes() {
	
		$this->form_attributes = apply_filters(
			$this->prefix.'form_default_attributes', 
				array(
					'action'	=>	'',
					'id'		=>	'',
					'class'		=>	'',
					'enctype'	=>	'',
					'method'	=>	'POST',
					'name'		=>	''
					
				)
			);
			
		if( $this->configuration['is_ajax'] == true) {
			$this->form_attributes['data-'.$this->prefix.'ajax_submit'] = 'true';
		}

	}
	

	/**
	* setsup all the form defaults while object instantiation
	*
	* 
	*/
	private function set_defaults() {
	
		do_action($this->prefix.'before_setup_defaults');
		$this->set_default_form_attributes();
		$this->set_form_classes();
		do_action($this->prefix.'after_setup_defaults');
	}
	

	/**
	* escape necessary data
	*/
	private function escape($type='',$value='') {
		
		switch($type) {
			
			case 'url':
				return esc_url($value);
			break;
			
			case 'class':
			case 'id':
				return sanitize_html_class($value);
			break;
			
			case 'text':
				return sanitize_text_field($value);
			break;
			case 'attribute':
				return esc_attr($value);
			
			
		}
	}
	
	/**
	* Add single field to form
	*
	*
	*/
	function add_field($field = array() ) {
		foreach($field as $key	=>	&$val) {
			
			if( is_string($val) ) 
				$val = esc_attr($val);
				
		}
		$this->form_fields[] = $field;		
	}
	
	/**
	* Add multiple form fields at once
	*
	*
	*/
	function add_fields($fields = array() ) {
	
		foreach($fields as $field) {
			$this->add_field($field);
		}
		
		$this->add_field(
			array(
				'type'	=>	'hidden',
				'name'	=>	$this->prefix.'form_submit',
				'value'	=>	'true'
			)
		);
		
		if( !is_null($this->configuration['callback_action']) ) {
			$this->add_field(
				array(
					'type'	=>	'hidden',
					'name'	=>	$this->prefix.'form_action',
					'value'	=>	$this->prefix.$this->configuration['callback_action']
				)
			);
		}
	}
	
	/**
	* Render form html based on the fields & form attributes
	*
	*
	*/
	function render_form() {
		
		ob_start();
		
		if( $this->get_configuration('form_wrap') == 'on') {
			do_action('before_'.$this->prefix.'form_container_open');
			$this->render_form_container_open();
		}
		
		if( $this->get_configuration('form_tag') == 'on') {
			do_action('before_'.$this->prefix.'form_open');
			$this->render_form_open();
		}
		do_action('before_'.$this->prefix.'form_fields');
		$this->render_fields();
		do_action('after_'.$this->prefix.'form_fields');
		
		if( $this->get_configuration('form_tag') == 'on') {
			$this->render_form_close();
			do_action('after_'.$this->prefix.'form_close');
		}
		
		if( $this->get_configuration('form_wrap') == 'on') {
			$this->render_form_container_close();
			do_action('after_'.$this->prefix.'form_container_close');
		}
		
		echo ob_get_clean();
	}
	
	/**
	* Render form container opening tag
	*/
	function render_form_container_open() {
		
		$html 		 = "\n<div  ";
		$html 		.= 'class ="'.$this->get_class('form_container').'" ';
		$html 		.= '>';
		echo apply_filters($this->prefix.'form_container_open_tag',$html,$this->form_attributes);
	}
	
	/**
	* Render form container closing tag
	*/
	function render_form_container_close() {
		
		echo apply_filters($this->prefix.'form_container_close_tag',"\n</div>",$this->form_attributes);
	}
	
	/**
	* Render form opening tag
	*/
	function render_form_open() {
		
		$html = "\n<form  ";
		foreach($this->form_attributes as $key	=>	$value ) {
			$html .= $key.'="'.$value.'" ';
		}
		$html .= ' >';
		echo apply_filters($this->prefix.'form_open_tag',$html,$this->form_attributes);
	}
	
	/**
	* Render form closing tag
	*/
	function render_form_close() {
		
		echo apply_filters($this->prefix.'form_close_tag',"\n</form>",$this->form_attributes);
	}
	
	/**
	* Render all form fields based on type
	*
	*
	*/
	private function render_fields() {
		
		if( !empty($this->form_fields) ) {
			foreach($this->form_fields as $field) {
				$this->render_field($field);
				
			}
		}
	}
	
	/**
	* Render single form field
	*
	*
	*/
	private function render_field($field) {
	
		$this->render_field_container_open();
		$this->render_field_label($field);
		
		switch($field['type']) {
					
			case 'text':
			case 'url':
			case 'number':
			case 'tel':
			case 'hidden':
				$this->render_text($field);
			break;
			case 'textarea':
				$this->render_textarea($field);
			break;
			case 'radio':
				$this->render_radio($field);
			break;
			case 'select':
			case 'select_single':
				$this->render_select($field);
			break;
			case 'checkbox':
			case 'checkbox_single':
				$this->render_checkbox($field);
			break;
				
			default :
				$this->render_text($field);
			break;
			
		}
		
		$this->render_field_container_close();
	}
	
	/**
	* Render field container opening tag
	*/
	function render_field_container_open() {
		
		$html 		 = "\n<div  ";
		$html 		.= 'class ="'.$this->get_class('field_container').'" ';
		$html 		.= '>';
		echo apply_filters($this->prefix.'field_container_open_tag',$html,$this->form_attributes);
	}
	
	/**
	* Render field container closing tag
	*/
	function render_field_container_close() {
		
		echo apply_filters($this->prefix.'field_container_close_tag',"\n</div>",$this->form_attributes);
	}

	/**
	* Render field label
	*/
	function render_field_label( $field = array() ) {
		
		$wrapper 		 = "\n<span  ";
		$wrapper 		.= 'class ="'.$this->get_class('label_container',$field).'" ';
		$wrapper 		.= '>';

		if( isset($field['label']) ) {
		
			$html 		 = "\n<label  ";
			$html 		.= 'class ="'.$this->get_class('label',$field).'" ';
			$html 		.= '>';
			$html 		.= apply_filters($this->prefix.'field_label',$field['label'],$field);
			$html 		.= "\n</label>";
			$wrapper 	.= $html;
		}
		
		
		$wrapper .= "\n</span>";
		
		echo apply_filters($this->prefix.'field_label_html',$wrapper,$field);
	}

	/**
	* render text & similar fields
	*/
	private function render_text($field) {
	
		$field['value']	 = $this->get_value($field);
		$field['class']  = $this->get_class('field',$field);
		$html 			 = "\n<input  ";
		$html 			.= $this->get_attributes($field);
		$html 			.= ' />';
		echo apply_filters($this->prefix.'form_'.$field["type"].'_tag',$html,$field);
			
		
	}
	
	/**
	* render textarea
	*/
	private function render_textarea($field) {
		
		$value	 		 = $this->get_value($field);
		$field['class']  = $this->get_class('field',$field);
		$html 			 = "\n<textarea  ";
		$html 			.= $this->get_attributes($field);
		$html 			.= '>';
		$html			.= $value;
		$html 			.= '</textarea>';
		echo apply_filters($this->prefix.'form_'.$field["type"].'_tag',$html,$field);
			
		
	}
	
	/**
	* render radio
	*/
	private function render_radio($field) {
		
		$options		= $field['opts'];
		$value	 		= $this->get_value($field);
		
		unset($field['opts']);
		
		foreach($options as $option	=>	$label) {
		
			if($option == $value) 
			$field['checked'] 	= 'checked' ;
			
			$field['value']	 	= $option;
			$field['class']  	= $this->get_class('field',$field);
			$html 			 	= "\n<input  ";
			$html 				.= $this->get_attributes($field);
			$html 				.= ' >'.$label;
			echo apply_filters($this->prefix.'form_'.$field["type"].'_tag',$html,$field);
			unset($field['checked']);
		}
	}

	/**
	* render radio
	*/
	private function render_checkbox($field) {
		
		$options		= $field['opts'];
		$value	 		= $this->get_value($field);
		
		unset($field['opts']);
		
		foreach($options as $option	=>	$label) {
		
			if($option == $value) 
			$field['checked'] 	= 'checked' ;
			
			$field['value']	 	= $option;
			$field['class']  	= $this->get_class('field',$field);
			$html 			 	= "\n<input  ";
			$html 				.= $this->get_attributes($field);
			$html 				.= ' >'.$label;
			echo apply_filters($this->prefix.'form_'.$field["type"].'_tag',$html,$field);
			unset($field['checked']);
		}
	}

	/**
	* render select
	*/
	private function render_select($field) {
		global $post;
		$options		= $field['opts'];
		unset($field['opts']);

		$value 		 	 = $this->get_value($field);
		$field['class']  = $this->get_class('field',$field);
		$html 			 = "\n<select  ";
		$html 			.= $this->get_attributes($field);
		$html 			.= ' >';
		$value			= (array) $value;
		
		foreach($options as $option	=>	$label) {
		
			if(is_array($label)) {
				if(isset($label['exclude']) && !empty($label['exclude'])) {
					if( in_array($post->post_type, $label['exclude']) ) {
						continue;
					}
				}
			
				if(isset($label['include']) && !empty($label['include'])) {
					if( !in_array($post->post_type, $label['include']) ) {
						continue;
					}
				}
				$label = $label['label'];
			}
			$html 			 	.= "\n<option  ";
			$html 				.= 'value = "'.$option.'"';
			
			if( in_array($option,$value) ) 
			$html 				.= ' selected = "selected" ';
			
			$html 				.= ' >'.$label."\n</option>";
			
		}
		$html .="\n</select>";
		echo apply_filters($this->prefix.'form_'.$field["type"].'_tag',$html,$field);
	}
	
	/**
	* render nonce field
	*/

	function add_nonce($action='') {
		
		$this->has_nonce 	= true;
		$this->nonce_key 	= $action != '' ? $action : $this->prefix.'nonce_action';
		$this->nonce_value 	= wp_create_nonce($action);
		
		$this->add_field(
			array(
				'type'	=>	'hidden',
				'name'	=>	$this->nonce_key,
				'value'	=>	$this->nonce_value
			)
		);
	}

}

?>
