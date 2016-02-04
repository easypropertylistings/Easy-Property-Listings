<?php
/**
 * Lead Interests Table Class
 *
 * @package     EPL
 * @since       2.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * EPL_Lead_Interests_Table Class
 *
 * Renders the Lead Interests table
 *
 * @since 2.4
 */
class EPL_Lead_Interests_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 2.4
	 */
	public $per_page = 30;

	/**
	 * Number of leads found
	 *
	 * @var int
	 * @since 2.4
	 */
	public $count = 0;

	/**
	 * Total leads
	 *
	 * @var int
	 * @since 2.4
	 */
	public $total = 0;

	/**
	 * Get things started
	 *
	 * @since 2.4
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular' => __( 'Lead', 'epl' ),
			'plural'   => __( 'Leads', 'epl' ),
			'ajax'     => false,
		) );

	}

	/**
	 * Show the search field
	 *
	 * @since 2.4
	 * @access public
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
		</p>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 2.4
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
	 * @since 2.4
	 *
	 * @param array $item Contains all the data of the leads
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			case 'num_listings' :
				$value = '<a href="' .
					admin_url( '/admin.php?page=epl-leads&view=interests&user=' . urlencode( $item['email'] )
				) . '">' . esc_html( $item['num_listings'] ) . '</a>';
				break;

			case 'date_created' :
				$value = date_i18n( get_option( 'date_format' ), strtotime( $item['date_created'] ) );
				break;

			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}
		return apply_filters( 'epl_leads_column_' . $column_name, $value, $item['id'] );
	}

	public function column_name( $item ) {
		$name        = '#' . $item['id'] . ' ';
		$name       .= ! empty( $item['name'] ) ? $item['name'] : '<em>' . __( 'Unnamed Lead','epl' ) . '</em>';
		$user        = ! empty( $item['user_id'] ) ? $item['user_id'] : $item['email'];
		$view_url    = admin_url( 'admin.php?page=epl-leads&view=overview&id=' . $item['id'] );
		$actions     = array(
			'view'   => '<a href="' . $view_url . '">' . __( 'View', 'epl' ) . '</a>',
		);

		$lead = new EPL_Lead( $item['id'] );
		return '<a href="' . esc_url( $view_url ) . '">' . $name . '</a>' . $this->row_actions( $actions );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 2.4
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'name'          => __( 'Name', 'epl' ),
			'email'         => __( 'Email', 'epl' ),
			'num_listings' => __( 'Listings', 'epl' ),
			'date_created'  => __( 'Date Created', 'epl' ),
		);

		return apply_filters( 'epl_interest_lead_columns', $columns );

	}

	/**
	 * Get the sortable columns
	 *
	 * @access public
	 * @since 2.4
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'date_created'  => array( 'date_created', true ),
			'name'          => array( 'name', true ),
			'num_listings' => array( 'listing_count', false ),
		);
	}

	/**
	 * Outputs the interesting views
	 *
	 * @access public
	 * @since 2.4
	 * @return void
	 */
	public function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place
	}

	/**
	 * Retrieve the current page number
	 *
	 * @access public
	 * @since 2.4
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
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Build all the interests data
	 *
	 * @access public
	 * @since 2.4
	  * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return array $interests_data All the data for lead interests
	 */
	public function interests_data() {
		global $wpdb;

		$data    = array();
		$paged   = $this->get_paged();
		$offset  = $this->per_page * ( $paged - 1 );
		$search  = $this->get_search();
		$order   = isset( $_GET['order'] )   ? sanitize_text_field( $_GET['order'] )   : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id';

		$args    = array(
			'number'  => $this->per_page,
			'offset'  => $offset,
			'order'   => $order,
			'orderby' => $orderby
		);

		if( is_email( $search ) ) {
			$args['email'] = $search;
		} elseif( is_numeric( $search ) ) {
			$args['id']    = $search;
		} elseif( strpos( $search, 'user:' ) !== false ) {
			$args['user_id'] = trim( str_replace( 'user:', '', $search ) );
		} else {
			$args['name']  = $search;
		}

		$leads = EPL()->leads->get_leads( $args );

		if ( $leads ) {

			foreach ( $leads as $lead ) {

				$user_id = ! empty( $lead->user_id ) ? intval( $lead->user_id ) : 0;

				$data[] = array(
					'id'            => $lead->id,
					'user_id'       => $user_id,
					'name'          => $lead->name,
					'email'         => $lead->email,
					'num_listings' => $lead->listing_count,
					'date_created'  => $lead->date_created,
				);
			}
		}

		return $data;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 2.4
	 * @uses EPL_Lead_Interests_Table::get_columns()
	 * @uses WP_List_Table::get_sortable_columns()
	 * @uses EPL_Lead_Interests_Table::get_pagenum()
	 * @uses EPL_Lead_Interests_Table::get_total_leads()
	 * @return void
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->interests_data();

		$this->total = epl_count_total_leads();

		$this->set_pagination_args( array(
			'total_items' => $this->total,
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $this->total / $this->per_page ),
		) );
	}
}
