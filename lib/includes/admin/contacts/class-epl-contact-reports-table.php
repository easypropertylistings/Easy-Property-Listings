<?php
/**
 * Contact Reports Table Class
 *
 * @package     EPL
 * @subpackage  Contacts/Table
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification
// phpcs:disable WordPress.DB.SlowDBQuery

// Load WP_List_Table if not loaded.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * EPL_Contact_Reports_Table Class.
 *
 * Renders the Contact Reports table
 *
 * @since 3.0
 */
class EPL_Contact_Reports_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 3.0
	 */
	public $per_page = 30;

	/**
	 * Number of contacts found
	 *
	 * @var int
	 * @since 3.0
	 */
	public $count = 0;

	/**
	 * Total contacts
	 *
	 * @var int
	 * @since 3.0
	 */
	public $total = 0;

	/**
	 * Get things started
	 *
	 * @since 3.0
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => __( 'Contact', 'easy-property-listings' ),
				'plural'   => __( 'Contacts', 'easy-property-listings' ),
				'ajax'     => false,
			)
		);

	}

	/**
	 * Show the search field
	 *
	 * @since 3.0
	 * @access public
	 *
	 * @param string $text Label for the search box.
	 * @param string $input_id ID of the search box.
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';

		if ( isset( $_REQUEST['_epl_admin_contact_search_action'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_epl_admin_contact_search_action'] ) ), '_epl_admin_contact_search_action' )
		) {

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				echo '<input type="hidden" name="orderby" value="' . esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) ) . '" />';
			}
			if ( ! empty( $_REQUEST['order'] ) ) {
				echo '<input type="hidden" name="order" value="' . esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) ) . '" />';
			}
		}

		$s_contact_name = isset( $_GET['s_contact_name'] ) ? sanitize_text_field( wp_unslash( $_GET['s_contact_name'] ) ) : '';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo esc_attr( wp_unslash( $input_id ) ); ?>"><?php echo esc_attr( wp_unslash( $text ) ); ?>:</label>
			<input type="search" placeholder="<?php esc_attr_e( 'Search by Summary', 'easy-property-listings' ); ?>" id="<?php echo esc_attr( wp_unslash( $input_id ) ); ?>" name="s" value="<?php esc_attr( wp_unslash( _admin_search_query() ) ); ?>" />
			<input type="search" placeholder="<?php esc_attr_e( 'Search by Name', 'easy-property-listings' ); ?>" id="" name="s_contact_name" value="<?php echo esc_attr( wp_unslash( $s_contact_name ) ); ?>" />
			<?php wp_nonce_field( '_epl_admin_contact_search_action', '_epl_admin_contact_search_action' ); ?>
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 3.0
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 3.0
	 *
	 * @param array  $item Contains all the data of the contacts.
	 * @param string $column_name The name of the column.
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'num_listings':
				$value = 0 === intval( $item['listing_count'] ) ? '' : intval( $item['listing_count'] );
				break;

			case 'background_info':
				$value = esc_html( $item['background_info'] );
				$value = substr( $value, 0, apply_filters( 'epl_contact_bg_info_length', 200 ) );
				break;

			case 'date_created':
				$value = date_i18n( get_option( 'date_format' ), strtotime( $item['date_created'] ) );
				break;

			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}
		return apply_filters( 'epl_contacts_column_' . $column_name, $value, $item['id'] );
	}

	/**
	 * Name for each columns
	 *
	 * @param  array $item Column items.
	 *
	 * @return string
	 */
	public function column_name( $item ) {

		$name     = ! empty( $item['name'] ) ? $item['name'] : '<em>' . __( 'Unnamed Contact', 'easy-property-listings' ) . '</em>';
		$user     = ! empty( $item['user_id'] ) ? $item['user_id'] : $item['email'];
		$view_url = admin_url( 'admin.php?page=epl-contacts&view=overview&id=' . $item['ID'] );
		$actions  = array(
			'view'   => '<a href="' . $view_url . '">' . __( 'View', 'easy-property-listings' ) . '</a>',
			'delete' => '<a href="' . admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $item['ID'] ) . '">' . __( 'Delete', 'easy-property-listings' ) . '</a>',
		);

		$contact = new EPL_Contact( $item['ID'] );
		return '<span class="epl-contact-name">' . $name . '</span>' . $this->row_actions( $actions );
	}

	/**
	 * Type for each column
	 *
	 * @param  array $item Column items.
	 * @return string
	 */
	public function column_type( $item ) {
		return '<span class="epl-contact-type">' . $item['type'] . ' </span>';
	}

	/**
	 * Column summary for each column.
	 *
	 * @param  array $item Column items.
	 * @return string
	 */
	public function column_summary( $item ) {
		ob_start();
		?>
			<div class="epl-contact-assigned-tags-wrap epl-contact-list-table-tags">
				<h4><?php echo esc_attr( $item['heading'] ); ?> </h4>
				<ul class="epl-contact-assigned-tags">
					<?php
						$contact_tags = wp_get_object_terms( $item['ID'], 'epl_contact_tag' );
					if ( ! empty( $contact_tags ) ) {
						if ( ! is_wp_error( $contact_tags ) ) {
							foreach ( $contact_tags as $term ) {
								$bgcolor = epl_get_contact_tag_bgcolor( $term->term_id );
								echo '<li data-id="' . esc_attr( $term->term_id ) . '" id="contact-tag-' . esc_attr( $term->term_id ) . '" style="background:' . esc_attr( $bgcolor ) . '">' . esc_html( $term->name ) . '</li>';
							}
						}
					}
					?>
				</ul>
			</div>
			<?php
			return ob_get_clean();
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 3.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'name'            => __( 'Contact', 'easy-property-listings' ),
			'summary'         => __( 'Summary', 'easy-property-listings' ),
			'background_info' => __( 'Background Info', 'easy-property-listings' ),
			'type'            => __( 'Type', 'easy-property-listings' ),
			'num_listings'    => __( 'Listings', 'easy-property-listings' ),
			'date_created'    => __( 'Date Created', 'easy-property-listings' ),
		);

		return apply_filters( 'epl_report_contact_columns', $columns );

	}

	/**
	 * Render checkbox column
	 *
	 * @param  array $item Column items.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$item['ID']
		);
	}

	/**
	 * Get the sortable columns
	 *
	 * @access public
	 * @since 3.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'date_created' => array( 'date_created', true ),
			'name'         => array( 'name', true ),
			'num_listings' => array( 'listing_count', false ),
		);
	}


	/**
	 * Retrieve the current page number
	 *
	 * @access public
	 * @since 3.0
	 * @return int Current page number
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @access public
	 * @since 1.7
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) ) : false;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @access public
	 * @since 3.3
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_name_search() {
		return ! empty( $_GET['s_contact_name'] ) ? urldecode( trim( sanitize_text_field( wp_unslash( $_GET['s_contact_name'] ) ) ) ) : false;
	}

	/**
	 * Build all the reports data
	 *
	 * @access public
	 * @since 3.0
	 * @global object $wpdb Used to query the database using the WordPress Database API
	 * @return array $reports_data All the data for contact reports
	 */
	public function reports_data() {
		global $wpdb;

		$data        = array();
		$paged       = $this->get_paged();
		$offset      = $this->get_items_per_page( 'contacts_per_page', $this->per_page ) * ( $paged - 1 );
		$search      = $this->get_search();
		$name_search = $this->get_name_search();
		$order       = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC';
		$orderby     = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'id';

		$args = array(
			'post_type'      => 'epl_contact',
			'posts_per_page' => $this->get_items_per_page( 'contacts_per_page', $this->per_page ),
			'offset'         => $offset,
			'order'          => $order,
			'orderby'        => $orderby,
			'meta_query'     => array(),
			'tax_query'      => array(),
		);

		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		if ( ! empty( $name_search ) ) {

			$name_search_array = explode( ' ', sanitize_text_field( $name_search ) );

			if ( ! empty( $name_search_array ) ) {

				$meta_name = array(
					'relation' => 'OR',
				);

				foreach ( $name_search_array as $component ) {

					$meta_name[] = array(
						'key'     => 'contact_first_name',
						'value'   => sanitize_text_field( $component ),
						'compare' => 'LIKE',
					);

					$meta_name[] = array(
						'key'     => 'contact_last_name',
						'value'   => sanitize_text_field( $component ),
						'compare' => 'LIKE',
					);
				}

				$args['meta_query'][] = $meta_name;
			}
		}

		if ( isset( $_GET['cat_filter'] ) && ! empty( $_GET['cat_filter'] ) ) {
			$args['meta_query'][] = array(
				'key'   => 'contact_category',
				'value' => sanitize_text_field( wp_unslash( $_GET['cat_filter'] ) ),
			);
		}

		if ( isset( $_GET['tag_filter'] ) && ! empty( $_GET['tag_filter'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'epl_contact_tag',
				'field'    => 'id',
				'terms'    => intval( $_GET['tag_filter'] ),
			);
		}

		$args = apply_filters( 'epl_get_contacts_args', $args );

		$contacts = epl_get_contacts( $args );

		if ( $contacts ) {

			foreach ( $contacts as $contact ) {
				$contact_object = new EPL_Contact( $contact->ID );
				$data[]         = (array) $contact_object;
			}
		}
		return $data;
	}

	/**
	 * Process bulk contact operations
	 *
	 * @access public
	 * @since 3.3
	 */
	public function process_bulk_action() {

		// Detect when a bulk action is being triggered.
		if ( 'bulk-delete' === $this->current_action() ) {

			$delete_ids = esc_sql( sanitize_text_field( wp_unslash( $_GET['bulk-delete'] ) ) );

			// Loop over the array of record IDs and delete them.
			foreach ( $delete_ids as $id ) {
				$contact = new EPL_Contact( $id );
				$contact->delete();
			}
		}
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 3.0
	 * @uses EPL_Contact_Reports_Table::get_columns()
	 * @uses WP_List_Table::get_sortable_columns()
	 * @uses EPL_Contact_Reports_Table::get_pagenum()
	 * @uses EPL_Contact_Reports_Table::get_total_contacts()
	 * @return void
	 */
	public function prepare_items() {

		/** Process bulk action */
		$this->process_bulk_action();

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns.
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = $this->get_column_info();

		$this->items = $this->reports_data();

		$this->total = epl_count_total_contacts();

		$this->set_pagination_args(
			array(
				'total_items' => $this->total,
				'per_page'    => $this->get_items_per_page( 'contacts_per_page', $this->per_page ),
				'total_pages' => ceil( $this->total / $this->get_items_per_page( 'contacts_per_page', $this->per_page ) ),
			)
		);
	}

	/**
	 * Get bulk contact actions
	 *
	 * @access public
	 * @since 3.3
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => __( 'Delete', 'easy-property-listings' ),
		);

		return $actions;
	}

	/**
	 * Output the contact categories
	 *
	 * @param string $which nav location.
	 *
	 * @access public
	 * @since 3.0
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			?>
			<div class="alignleft actions bulkactions">
				<?php
				$cats = epl_get_contact_categories();
				if ( $cats ) {
					?>
					<select name="cat_filter" class="epl_contact_type_filter">
						<option value=""><?php esc_html_e( 'Type:', 'easy-property-listings' ); ?></option>
						<?php
						foreach ( $cats as $cat_key  => $cat_value ) {
							$selected = '';
							if ( isset( $_GET['cat_filter'] ) && $_GET['cat_filter'] === $cat_key ) {
								$selected = ' selected ';
							}
							?>
							<option value="<?php echo esc_attr( $cat_key ); ?>" <?php echo esc_attr( $selected ); ?>>
								<?php echo esc_attr( $cat_value ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<?php
				}
				?>
			</div>
			<?php
		}

	}
}
