<?php
/**
 * Metabox Oject
 *
 * @package     EPL
 * @subpackage  Classes/Metaboxs
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 * Prefix used in nonces and other places to make them unique
	 *
	 * Default is epl_
	 *
	 * @var array $epl_meta_boxes
	 */
	protected $prefix;

	/**
	 * Translation domain used to translate string
	 *
	 * Default is epl
	 *
	 * @var array $text_domain
	 */
	protected $text_domain;

	/**
	 * Constructor
	 *
	 * Register a mea box.
	 *
	 * @param mixed  $epl_meta_boxes The name(s) of the meta box.
	 * @param string $prefix Prefix of meta box.
	 * @param string $text_domain Text domain name.
	 */
	public function __construct( $epl_meta_boxes, $prefix = 'epl_', $text_domain = 'easy-property-listings' ) {

		$this->epl_meta_boxes = $epl_meta_boxes;

		$this->prefix = (string) $prefix;

		$this->text_domain = (string) $text_domain;

		// Register meta boxes.
		$this->add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );

		// Save meta boxes.
		$this->add_action( 'save_post', array( &$this, 'save_meta_box' ) );

	}

	/**
	 * Add Action
	 *
	 * Helper function to add add_action WordPress filters.
	 *
	 * @param string  $action Name of the action.
	 * @param string  $function Function to hook that will run on action.
	 * @param int     $priority Order in which to execute the function, relation to other functions hooked to this action.
	 * @param integer $accepted_args The number of arguments the function accepts.
	 */
	public function add_action( $action, $function, $priority = 10, $accepted_args = 1 ) {

		// Pass variables into WordPress add_action function.
		add_action( $action, $function, $priority, $accepted_args );
	}

	/**
	 * Add Filter
	 *
	 * Create add_filter WordPress filter.
	 *
	 * @see http://codex.wordpress.org/Function_Reference/add_filter
	 *
	 * @param  string $action           Name of the action to hook to, e.g 'init'.
	 * @param  string $function         Function to hook that will run on @action.
	 * @param  int    $priority         Order in which to execute the function, relation to other function hooked to this action.
	 * @param  int    $accepted_args    The number of arguements the function accepts.
	 */
	public function add_filter( $action, $function, $priority = 10, $accepted_args = 1 ) {

		// Pass variables into WordPress add_action function.
		add_filter( $action, $function, $priority, $accepted_args );
	}

	/**
	 * Add metaboxes
	 *
	 * See how to register custom field metaboxes to listings here.
	 *
	 * @link http://codex.easypropertylistings.com.au/article/127-epllistingmetaboxes-filter
	 */
	public function add_meta_boxes() {

		if ( ! empty( $this->epl_meta_boxes ) ) {
			foreach ( $this->epl_meta_boxes as $epl_meta_box ) {

				// If we have multiple metaboxes.
				if ( isset( $epl_meta_box['id'] ) && is_array( $epl_meta_box ) ) {
					// Multiple post type.
					if ( is_array( $epl_meta_box['post_type'] ) ) {
						foreach ( $epl_meta_box['post_type'] as $post_type ) {
							$this->add_meta_box(
								$epl_meta_box['id'],
								$epl_meta_box['label'],
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
							$epl_meta_box['label'],
							'inner_meta_box',
							$epl_meta_box['post_type'],
							$epl_meta_box['context'],
							$epl_meta_box['priority'],
							$epl_meta_box
						);
					}
				} else {
						// If we have single metabox.
						$epl_meta_box = $this->epl_meta_boxes;
						// Multiple post type.
					if ( is_array( $epl_meta_box['post_type'] ) ) {
						foreach ( $epl_meta_box['post_type'] as $post_type ) {
							$this->add_meta_box(
								$epl_meta_box['id'],
								$epl_meta_box['label'],
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
							$epl_meta_box['label'],
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
	 * Class wrapper for WordPress function add_meta_box
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_meta_box
	 *
	 * @param string $id Meta box ID (used in the 'id' attribute for the meta box).
	 * @param string $label Title of the meta box.
	 * @param string $func Function that fills the box with the desired content.
	 *                                The function should echo its output.
	 * @param array  $post_type Post type name.
	 * @param string $context Optional. The context within the screen where the boxes
	 *                                should display. Available contexts vary from screen to
	 *                                screen. Post edit screen contexts include 'normal', 'side',
	 *                                and 'advanced'. Comments screen contexts include 'normal'
	 *                                and 'side'. Menus meta boxes (accordion sections) all use
	 *                                the 'side' context. Global default is 'advanced'.
	 * @param string $priority Optional. The priority within the context where the boxes
	 *                                should show ('high', 'low'). Default 'default'.
	 * @param array  $args Optional. Data that should be set as the $args property
	 *                                 of the box array (which is the second parameter passed
	 *                                 to your callback). Default null.
	 */
	public function add_meta_box( $id = '', $label = '', $func = 'inner_meta_box', $post_type = array(), $context = 'normal', $priority = 'default', $args ) {
		add_meta_box(
			$id,
			$label,
			array( $this, $func ),
			$post_type,
			$context,
			$priority,
			$args
		);
	}

	/**
	 * Used to render the metabox fields
	 *
	 * @param array $post Post object.
	 * @param array $args Array of options.
	 */
	public function inner_meta_box( $post, $args ) {
		$groups = $args['args']['groups'];
		$groups = array_filter( $groups );
		if ( ! empty( $groups ) ) {
			wp_nonce_field( $this->prefix . 'inner_custom_box', $this->prefix . 'inner_custom_box_nonce' );
			foreach ( $groups as $group ) { ?>
			<div class="epl-inner-div col-<?php echo esc_attr( $group['columns'] ); ?> table-<?php echo esc_attr( $args['args']['context'] ); ?>">
						<?php
						$group['label'] = trim( $group['label'] );
						if ( ! empty( $group['label'] ) ) {
							echo '<h3>' . esc_attr( $group['label'] ) . '</h3>';
						}
						?>
					<table class="form-table epl-form-table">
					<tbody>
								<?php
								$fields = $group['fields'];
								$fields = array_filter( $fields );
								if ( ! empty( $fields ) ) {
									foreach ( $fields as $field ) {
										if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
											if ( in_array( $post->post_type, $field['exclude'], true ) ) {
												continue;
											}
										}

										if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
											if ( ! in_array( $post->post_type, $field['include'], true ) ) {
												continue;
											}
										}
										?>
								<tr class="form-field">
										<?php if ( 'checkbox_single' !== $field['type'] || ( isset( $field['opts'] ) && 1 !== count( $field['opts'] ) ) ) : ?>
									<th valign="top" scope="row">
										<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
									</th>
									<?php endif; ?>

										<?php if ( $group['columns'] > 1 ) { ?>
										</tr><tr class="form-field">
									<?php } ?>

									<td>
										<?php
											$val = get_post_meta( $post->ID, $field['name'], true );
											epl_render_html_fields( $field, $val );
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
			}
			?>
		<input type="hidden" name="epl_meta_box_ids[]" value="<?php echo esc_attr( $args['id'] ); ?>" />
		<div class="epl-clear"></div>
			<?php
		}
	}

	/**
	 * Callback function hooked on WordPress save_post hook
	 * used to save all the meta fields
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
	 *
	 * @param int $post_ID The post ID.
	 *
	 * @return int
	 * @since 3.4.17	Fixed issue : empty values not getting saved for decimals & numbers
	 */
	public function save_meta_box( $post_ID ) {

		if ( ! isset( $_POST[ $this->prefix . 'inner_custom_box_nonce' ] ) ) {
			return $post_ID;
		}

		$nonce = $_POST[ $this->prefix . 'inner_custom_box_nonce' ]; //phpcs:ignore

		if ( ! wp_verify_nonce( $nonce, $this->prefix . 'inner_custom_box' ) ) {
			return $post_ID;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_ID;
		}

		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_ID ) ) {
				return $post_ID;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_ID ) ) {
				return $post_ID;
			}
		}

		$epl_meta_box_ids = '';
		if ( isset( $_POST['epl_meta_box_ids'] ) ) {
			$epl_meta_box_ids = wp_unslash( $_POST['epl_meta_box_ids'] ); //phpcs:ignore
		}

		if ( ! empty( $epl_meta_box_ids ) ) {
			if ( ! empty( $this->epl_meta_boxes ) ) {
				foreach ( $epl_meta_box_ids as $epl_meta_box_id ) {
					foreach ( $this->epl_meta_boxes as $epl_meta_box ) {
						if ( $epl_meta_box['id'] === $epl_meta_box_id ) {
							if ( ! empty( $epl_meta_box['groups'] ) ) {
								foreach ( $epl_meta_box['groups'] as $group ) {

									$fields = $group['fields'];
									if ( ! empty( $fields ) ) {
										foreach ( $fields as $field ) {

											// Dont go further if the current post type is in excluded list of the current field.
											if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
												if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $field['exclude'], true ) ) {
													continue;
												}
											}

											// Dont go further if the current post type is not in included list of the current field.
											if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
												if ( ! in_array( $_POST['post_type'], $field['include'] ) ) { // phpcs:ignore
													continue;
												}
											}

											if ( 'radio' === $field['type'] ) {
												if ( ! isset( $_POST[ $field['name'] ] ) ) {
													continue;
												}
											} elseif ( 'checkbox_single' === $field['type'] ) {
												if ( ! isset( $_POST[ $field['name'] ] ) ) {
													$_POST[ $field['name'] ] = '';
												}
											} elseif ( in_array( $field['type'], array( 'number', 'decimal' ), true ) ) {

												// Validate numeric data.

												if ( ! is_numeric( $_POST[ $field['name'] ] ) && ! empty( $_POST[ $field['name'] ] ) ) {
													continue;
												}
											} elseif ( in_array( $field['type'], array( 'textarea' ), true ) ) {

												if ( function_exists( 'sanitize_textarea_field' ) ) {
													$_POST[ $field['name'] ] =
													sanitize_textarea_field( wp_unslash( $_POST[ $field['name'] ] ) );
												}
											} elseif ( in_array( $field['type'], array( 'url', 'file' ), true ) ) {

												// Sanitize URLs.

												$_POST[ $field['name'] ] = esc_url_raw( wp_unslash( $_POST[ $field['name'] ] ) );

											} elseif ( 'auction-date' === $field['type'] && ! empty( $_POST[ $field['name'] ] ) ) {
												$epl_date = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );
												if ( strpos( $epl_date, 'T' ) !== false ) {
													$epl_date = date( 'Y-m-d\TH:i', strtotime( $epl_date ) );
												} else {
													$epl_date = DateTime::createFromFormat( 'Y-m-d-H:i:s', $epl_date );

													if ( $epl_date ) {
														$epl_date = $epl_date->format( 'Y-m-d\TH:i' );
													}
												}
												$_POST[ $field['name'] ] = $epl_date;
											} elseif ( 'sold-date' === $field['type'] && ! empty( $_POST[ $field['name'] ] ) ) {
												$epl_date = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );
												if ( strpos( $epl_date, 'T' ) !== false ) {
													$epl_date = date( 'Y-m-d\TH:i', strtotime( $epl_date ) );
												} else {
													$epl_date = DateTime::createFromFormat( 'Y-m-d', $epl_date );

													if ( $epl_date ) {
														$epl_date = $epl_date->format( 'Y-m-d' );
													}
												}
												$_POST[ $field['name'] ] = $epl_date;
											}
											if ( isset( $_POST[ $field['name'] ] ) ) {
												$meta_value = wp_unslash( $_POST[ $field['name'] ] ); // phpcs:ignore
												update_post_meta( $post_ID, $field['name'], $meta_value );
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
}
