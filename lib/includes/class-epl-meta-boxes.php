<?php
/**
 * Metabox Oject
 *
 * @package     EPL
 * @subpackage  Classes/Metaboxs
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_METABOX class

 * Can also be used by extensions to handle all the metabox functionality
 * it adds metabox wrapper & renders metabox fields and finally save it on save_post hook
 * the constructor of the class accepts one or more array of metabox to be rendered
 * the structure of the metabox array should be similar to make it work
 *
 * @since 2.0
 */
class EPL_METABOX {

	/**
	 * Holds the user submitted metabox array
	 *
	 * @var array $epl_meta_boxes
	 */
	protected $epl_meta_boxes;

	/**
	 * prefix used in nonces and other places to make them unique
	 *
	 * default is epl_
	 *
	 * @var array $epl_meta_boxes
	 */
	protected $prefix;

	/**
	 * translation domain used to translate string
	 *
	 * default is epl
	 *
	 * @var array $text_domain
	 */
	protected $text_domain;

	function __construct($epl_meta_boxes,$prefix='epl_',$text_domain='easy-property-listings' ) {

		$this->epl_meta_boxes 	= $epl_meta_boxes;

		$this->prefix 			= (string) $prefix;

		$this->text_domain 		= (string) $text_domain;

		// register meta boxes
		$this->add_action('add_meta_boxes', array( &$this, 'add_meta_boxes') );

		// save meta boxes
		$this->add_action('save_post',  array( &$this, 'save_meta_box') );

	}

	/**
	 * Add Action
	 *
	 * Helper function to add add_action WordPress filters.
	 *
	 * @param string $action Name of the action.
	 * @param string $function Function to hook that will run on action.
	 * @param integet $priority Order in which to execute the function, relation to other functions hooked to this action.
	 * @param integer $accepted_args The number of arguments the function accepts.
	 */
	function add_action( $action, $function, $priority = 10, $accepted_args = 1 ) {

		// Pass variables into WordPress add_action function
		add_action( $action, $function, $priority, $accepted_args );
	}

	/**
	 * Add Filter
	 *
	 * Create add_filter WordPress filter.
	 *
	 * @see http://codex.wordpress.org/Function_Reference/add_filter
	 *
	 * @param  string  $action           Name of the action to hook to, e.g 'init'.
	 * @param  string  $function         Function to hook that will run on @action.
	 * @param  int     $priority         Order in which to execute the function, relation to other function hooked to this action.
	 * @param  int     $accepted_args    The number of arguements the function accepts.
	 */
	function add_filter( $action, $function, $priority = 10, $accepted_args = 1 ) {

		// Pass variables into Wordpress add_action function
		add_filter( $action, $function, $priority, $accepted_args );
	}

	/**
	 * Add metaboxes
	 *
	 * See how to register custom field metaboxes to listings here.
	 *
	 * @link http://codex.easypropertylistings.com.au/article/127-epllistingmetaboxes-filter
	 */
	function add_meta_boxes() {

		if(!empty($this->epl_meta_boxes)) {
			foreach($this->epl_meta_boxes as $epl_meta_box) {

				/* If we have multiple metaboxes */
				if( isset($epl_meta_box['id']) && is_array($epl_meta_box) ) {
					/* multiple post type ? */
			                if( is_array($epl_meta_box['post_type']) ) {
			                    foreach($epl_meta_box['post_type'] as $post_type) {
			                        $this->add_meta_box(
			                            $epl_meta_box['id'],
			                            __( $epl_meta_box['label'], $this->text_domain ),
			                            'inner_meta_box',
			                            $post_type,
			                            $epl_meta_box['context'],
			                            $epl_meta_box['priority'],
			                            $epl_meta_box
			                        );
			                    }
			                } else {
			                    $this->add_meta_box(
			                        $epl_meta_box['id'],
			                        __( $epl_meta_box['label'], $this->text_domain ),
			                        'inner_meta_box',
			                        $epl_meta_box['post_type'],
			                        $epl_meta_box['context'],
			                        $epl_meta_box['priority'],
			                        $epl_meta_box
			                    );
			                }
			        } else {
			            	/* If we have single metabox */
			            	$epl_meta_box = $this->epl_meta_boxes;
			            	/* multiple post type ? */
			            	if( is_array($epl_meta_box['post_type']) ) {
			                    foreach($epl_meta_box['post_type'] as $post_type) {
			                        $this->add_meta_box(
			                            $epl_meta_box['id'],
			                            __( $epl_meta_box['label'], $this->text_domain ),
			                            'inner_meta_box',
			                            $post_type,
			                            $epl_meta_box['context'],
			                            $epl_meta_box['priority'],
			                            $epl_meta_box
			                        );
			                    }
			                } else {
			                    $this->add_meta_box(
			                        $epl_meta_box['id'],
			                        __( $epl_meta_box['label'], $this->text_domain ),
			                        'inner_meta_box',
			                        $epl_meta_box['post_type'],
			                        $epl_meta_box['context'],
			                        $epl_meta_box['priority'],
			                        $epl_meta_box
			                    );
			                }
					break;
			        }
			}
		}
	}

	/**
	 * Class wrapper for wordpress function add_meta_box
	 * @see https://codex.wordpress.org/Function_Reference/add_meta_box
	 */
	public function  add_meta_box($id='',$label='',$func='inner_meta_box',$post_type=array(),$context='normal',$priority='default',$args) {
		add_meta_box(
			$id,
			$label,
			array($this,$func),
			$post_type,
			$context,
			$priority,
			$args
		);
	}

	/**
	 * used to render the metabox fields
	 */

	function inner_meta_box($post, $args) {
		$groups = $args['args']['groups'];
		$groups = array_filter($groups);
		if(!empty($groups)) {
		wp_nonce_field( $this->prefix.'inner_custom_box', $this->prefix.'inner_custom_box_nonce' );
		foreach($groups as $group) { ?>
			<div class="epl-inner-div col-<?php echo $group['columns']; ?> table-<?php echo $args['args']['context']; ?>">
			    	<?php
				$group['label'] = trim($group['label']);
				if(!empty($group['label'])) {
					echo '<h3>'.__($group['label'], $this->text_domain).'</h3>';
				}
				?>
			        <table class="form-table epl-form-table">
					<tbody>
			                	<?php
						$fields = $group['fields'];
						$fields = array_filter($fields);
						if(!empty($fields)) {
							foreach($fields as $field) {
								if(isset($field['exclude']) && !empty($field['exclude'])) {
									if( in_array($post->post_type, $field['exclude']) ) {
										continue;
									}
								}

								if(isset($field['include']) && !empty($field['include'])) {
									if( !in_array($post->post_type, $field['include']) ) {
										continue;
									}
								} ?>
								<tr class="form-field">
									<th valign="top" scope="row">
										<label for="<?php echo $field['name']; ?>">
									    		<?php _e($field['label'], $this->text_domain); ?>
										</label>
									</th>

									<?php if($group['columns'] > 1) { ?>
										</tr><tr class="form-field">
									<?php } ?>

									<td>
										<?php
									        $val = get_post_meta($post->ID, $field['name'], true);
									        epl_render_html_fields ($field,$val);
										?>
									</td>
								</tr>
							<?php
							}
						}
			                ?>
					</tbody>
				</table>
			</div>
		    <?php
		} ?>
		<input type="hidden" name="epl_meta_box_ids[]" value="<?php echo $args['id']; ?>" />
		<div class="epl-clear"></div>
		<?php
		}
	}

	/**
	 * callback function hooked on wordpress save_post hook
	 * used to save all the meta fields
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
	 */
	function save_meta_box( $post_ID ) {

	    if ( ! isset( $_POST[$this->prefix.'inner_custom_box_nonce'] ) )
	        return $post_ID;

	    $nonce = $_POST[$this->prefix.'inner_custom_box_nonce'];

	    if ( ! wp_verify_nonce( $nonce, $this->prefix.'inner_custom_box' ) )
	        return $post_ID;

	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	        return $post_ID;

	    if ( 'page' == $_POST['post_type'] ) {
	        if ( ! current_user_can( 'edit_page', $post_ID ) )
	            return $post_ID;
	    } else {
	        if ( ! current_user_can( 'edit_post', $post_ID ) )
	        return $post_ID;
	    }


	    $epl_meta_box_ids = '';
	    if(isset($_POST['epl_meta_box_ids'])) {
	        $epl_meta_box_ids = $_POST['epl_meta_box_ids'];
	    }

	    if(!empty($epl_meta_box_ids)) {
	        if(!empty($this->epl_meta_boxes)) {
	            foreach($epl_meta_box_ids as $epl_meta_box_id) {
	                foreach($this->epl_meta_boxes as $epl_meta_box) {
	                    if($epl_meta_box['id'] == $epl_meta_box_id) {
	                        if(!empty($epl_meta_box['groups'])) {
	                            foreach($epl_meta_box['groups'] as $group) {

	                                $fields = $group['fields'];
	                                if(!empty($fields)) {
	                                    foreach($fields as $field) {

	                                    	// dont go further if the current post type is in excluded list of the current field
	                                        if(isset($field['exclude']) && !empty($field['exclude'])) {
	                                            if( in_array($_POST['post_type'], $field['exclude']) ) {
	                                                continue;
	                                            }
	                                        }

						// dont go further if the current post type is not in included list of the current field
	                                        if(isset($field['include']) && !empty($field['include'])) {
	                                            if( !in_array($_POST['post_type'], $field['include']) ) {
	                                                continue;
	                                            }
	                                        }

	                                        if( $field['type'] == 'radio' ) {
	                                            if(!isset($_POST[ $field['name'] ])) {
	                                                continue;
	                                            }
	                                        } else if( $field['type'] == 'checkbox_single') {
	                                            if(!isset($_POST[ $field['name'] ])) {
	                                                $_POST[ $field['name'] ] = '';
	                                            }
	                                        } else if( $field['type'] == 'auction-date' && $_POST[ $field['name'] ] != '') {
	                                            $epl_date = $_POST[ $field['name'] ];
	                                            if(strpos($epl_date, 'T') !== FALSE){
	                                                $epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
	                                            } else {
	                                                $epl_date = DateTime::createFromFormat('Y-m-d-H:i:s', $epl_date);

	                                                if($epl_date)
	                                                    $epl_date = $epl_date->format('Y-m-d\TH:i');
	                                            }
	                                            $_POST[ $field['name'] ] = $epl_date;
	                                        } else if( $field['type'] == 'sold-date' && $_POST[ $field['name'] ] != '') {
	                                            $epl_date = $_POST[ $field['name'] ];
	                                            if(strpos($epl_date, 'T') !== FALSE){
	                                                $epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
	                                            } else {
	                                                $epl_date = DateTime::createFromFormat('Y-m-d', $epl_date);

	                                                if($epl_date)
	                                                    $epl_date = $epl_date->format('Y-m-d');
	                                            }
	                                            $_POST[ $field['name'] ] = $epl_date;
	                                        }

	                                        update_post_meta( $post_ID, $field['name'], $_POST[ $field['name'] ] );
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	}
}

/*********** sample usage *************

$epl_dh_meta_boxes = array(
	array(
		'id'		=>	'epl-display-homes-section-id',
		'label'		=>	'Location Details',
		'post_type'	=>	'display_home',
		'context'	=>	'normal',
		'priority'	=>	'high',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	apply_filters('epl_dh_meta_fields',
					array(

						array(
							'name'		=>	'display_home_name',
							'label'		=>	'Location Address including state and postcode/zip',
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'display_home_state',
							'label'		=>	'State',
							'type'		=>	'text',
							'maxlength'	=>	'10'
						),

						array(
							'name'		=>	'display_home_postcode',
							'label'		=>	'Postcode/Zip',
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'display_home_video_url',
							'label'		=>	'YouTube Video Link',
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),
					)
				)
			)
		)
	)
);

new EPL_METABOX($epl_dh_meta_boxes);

***********   sample usage ends  **********/
