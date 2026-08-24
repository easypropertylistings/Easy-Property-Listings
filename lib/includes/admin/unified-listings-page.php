<?php
/**
 * Unified Listings Admin Page
 *
 * Registers the new admin menu item and enqueues React assets for the
 * unified listings interface.
 *
 * @package     EPL
 * @subpackage  Admin/UnifiedListings
 * @copyright   Copyright (c) 2024, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the unified listings admin page.
 *
 * @since 4.0.0
 */
function epl_register_unified_listings_page() {
	$page_hook = add_submenu_page(
		'epl-general',                                              // Parent slug
		__( 'All Listings', 'easy-property-listings' ),             // Page title
		__( 'All Listings', 'easy-property-listings' ),             // Menu title
		'edit_posts',                                                // Capability
		'epl-listings',                                              // Menu slug
		'epl_render_unified_listings_page'                           // Callback function
	);

	if ( $page_hook ) {
		// Enqueue scripts only on this page.
		add_action( "admin_print_scripts-{$page_hook}", 'epl_enqueue_unified_listings_assets' );
		add_action( "admin_print_styles-{$page_hook}", 'epl_enqueue_unified_listings_styles' );
	}
}
add_action( 'admin_menu', 'epl_register_unified_listings_page', 15 );

/**
 * Render the unified listings page container.
 *
 * @since 4.0.0
 */
function epl_render_unified_listings_page() {
	?>
	<div class="wrap epl-unified-listings-wrap">
		<div id="epl-unified-listings-root">
			<div class="epl-loading-placeholder">
				<span class="spinner is-active"></span>
				<p><?php esc_html_e( 'Loading listings...', 'easy-property-listings' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Enqueue scripts for the unified listings page.
 *
 * @since 4.0.0
 */
function epl_enqueue_unified_listings_assets() {
	$asset_file = EPL_PATH_LIB . 'admin/unified-listings/build/index.asset.php';

	// Check if build exists.
	if ( ! file_exists( $asset_file ) ) {
		// Development fallback - show notice.
		add_action( 'admin_notices', 'epl_unified_listings_build_notice' );
		return;
	}

	$assets = include $asset_file;

	wp_enqueue_script(
		'epl-unified-listings',
		EPL_PLUGIN_URL . 'lib/admin/unified-listings/build/index.js',
		$assets['dependencies'],
		$assets['version'],
		true
	);

	// Get status labels.
	$status_labels = array(
		'current'   => epl_get_the_status_label( 'current' ),
		'sold'      => epl_get_the_status_label( 'sold' ),
		'leased'    => epl_get_the_status_label( 'leased' ),
		'withdrawn' => epl_get_the_status_label( 'withdrawn' ),
		'offmarket' => epl_get_the_status_label( 'offmarket' ),
		'deleted'   => epl_get_the_status_label( 'deleted' ),
	);

	// Get post type labels.
	$post_type_labels = array();
	foreach ( epl_get_core_post_types() as $type ) {
		$obj = get_post_type_object( $type );
		if ( $obj ) {
			$post_type_labels[ $type ] = $obj->labels->singular_name;
		}
	}

	// Get users for author filter.
	$users = get_users(
		array(
			'role__not_in' => array( 'subscriber' ),
			'orderby'      => 'display_name',
			'fields'       => array( 'ID', 'display_name' ),
		)
	);

	$authors = array();
	foreach ( $users as $user ) {
		$authors[] = array(
			'id'   => $user->ID,
			'name' => $user->display_name,
		);
	}

	// Get property categories.
	$categories = array();
	$raw_categories = epl_listing_load_meta_property_category();
	if ( is_array( $raw_categories ) ) {
		foreach ( $raw_categories as $key => $label ) {
			$categories[] = array(
				'value' => $key,
				'label' => $label,
			);
		}
	}

	// Get location taxonomy terms (suburbs).
	$suburbs = array();
	$location_terms = get_terms( array(
		'taxonomy'   => 'location',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );
	if ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) {
		foreach ( $location_terms as $term ) {
			$suburbs[] = array(
				'value' => $term->slug,
				'label' => $term->name,
			);
		}
	}
	// Get registered image sizes for thumbnail dropdown.
	$image_sizes = array();
	$registered_sizes = get_intermediate_image_sizes();
	$registered_sizes = array_merge( array( 'thumbnail', 'medium', 'large', 'full' ), $registered_sizes );
	$registered_sizes = array_unique( $registered_sizes );
	foreach ( $registered_sizes as $size ) {
		$image_sizes[] = array(
			'value' => $size,
			'label' => ucfirst( str_replace( array( '-', '_' ), ' ', $size ) ),
		);
	}
	/**
	 * Filter the available image sizes for the unified listings.
	 *
	 * @param array $image_sizes Array of image size options.
	 */
	$image_sizes = apply_filters( 'epl_unified_listings_image_sizes', $image_sizes );

	// Quick edit fields configuration.
	// Each field can have: key, label, type (text/number/select/toggle/combobox), options (for select/combobox), apiKey (listing property to read initial value from).
	$quick_edit_fields = array(
		array(
			'key'     => 'property_status',
			'label'   => __( 'Property Status', 'easy-property-listings' ),
			'type'    => 'select',
			'options' => 'statusLabels',
			'apiKey'  => 'propertyStatus',
			'group'   => 'status',
		),
		array(
			'key'     => 'post_status',
			'label'   => __( 'Post Status', 'easy-property-listings' ),
			'type'    => 'select',
			'options' => array(
				array( 'value' => 'publish', 'label' => __( 'Published', 'easy-property-listings' ) ),
				array( 'value' => 'draft', 'label' => __( 'Draft', 'easy-property-listings' ) ),
				array( 'value' => 'pending', 'label' => __( 'Pending', 'easy-property-listings' ) ),
				array( 'value' => 'private', 'label' => __( 'Private', 'easy-property-listings' ) ),
			),
			'apiKey'  => 'postStatus',
			'group'   => 'status',
		),
		array(
			'key'     => 'post_author',
			'label'   => __( 'Author', 'easy-property-listings' ),
			'type'    => 'select',
			'options' => 'authors',
			'apiKey'  => 'author.id',
		),
		array(
			'key'     => 'location',
			'label'   => __( 'Suburb', 'easy-property-listings' ),
			'type'    => 'combobox',
			'options' => 'suburbs',
			'apiKey'  => 'locationSlugs.0',
		),
		array(
			'key'       => 'property_price',
			'label'     => __( 'Price', 'easy-property-listings' ),
			'type'      => 'number',
			'apiKey'    => 'price',
			'hideFor'   => array( 'rental' ),
		),
		array(
			'key'       => 'property_rent',
			'label'     => __( 'Rent', 'easy-property-listings' ),
			'type'      => 'number',
			'apiKey'    => 'rent',
			'showFor'   => array( 'rental' ),
		),
		array(
			'key'     => 'property_bedrooms',
			'label'   => __( 'Bedrooms', 'easy-property-listings' ),
			'type'    => 'number',
			'apiKey'  => 'bedrooms',
			'group'   => 'rooms',
		),
		array(
			'key'     => 'property_bathrooms',
			'label'   => __( 'Bathrooms', 'easy-property-listings' ),
			'type'    => 'number',
			'apiKey'  => 'bathrooms',
			'group'   => 'rooms',
		),
		array(
			'key'     => 'property_featured',
			'label'   => __( 'Featured', 'easy-property-listings' ),
			'type'    => 'toggle',
			'apiKey'  => 'featured',
			'group'   => 'toggles',
		),
		array(
			'key'     => 'property_under_offer',
			'label'   => __( 'Under Offer', 'easy-property-listings' ),
			'type'    => 'toggle',
			'apiKey'  => 'underOffer',
			'group'   => 'toggles',
		),
		array(
			'key'     => 'property_images_mod_date',
			'label'   => __( 'Images Mod Date', 'easy-property-listings' ),
			'type'    => 'text',
			'apiKey'  => 'propertyImagesModDate',
			'group'   => 'dates',
		),
		array(
			'key'     => 'property_mod_date',
			'label'   => __( 'Property Mod Date', 'easy-property-listings' ),
			'type'    => 'text',
			'apiKey'  => 'propertyModDate',
			'group'   => 'dates',
		),
	);
	/**
	 * Filter the quick edit fields for the unified listings.
	 *
	 * @param array $quick_edit_fields Array of quick edit field definitions.
	 */
	$quick_edit_fields = apply_filters( 'epl_unified_listings_quick_edit_fields', $quick_edit_fields );

	// Pass configuration to JavaScript.
	wp_localize_script(
		'epl-unified-listings',
		'eplAdminListings',
		array(
			'restUrl'         => rest_url( 'epl/v1/admin/' ),
			'nonce'           => wp_create_nonce( 'wp_rest' ),
			'postTypes'       => epl_get_core_post_types(),
			'postTypeLabels'  => $post_type_labels,
			'statusLabels'    => $status_labels,
			'currentUser'     => get_current_user_id(),
			'authors'         => $authors,
			'categories'      => $categories,
			'suburbs'         => $suburbs,
			'imageSizes'      => $image_sizes,
			'quickEditFields' => $quick_edit_fields,
			'currency'        => array(
				'symbol'   => epl_unified_listings_get_currency_symbol(),
				'position' => epl_get_currency_position(),
			),
			'labels'         => array(
				'allListings'     => __( 'All Listings', 'easy-property-listings' ),
				'search'          => __( 'Search listings...', 'easy-property-listings' ),
				'filter'          => __( 'Filter', 'easy-property-listings' ),
				'status'          => __( 'Status', 'easy-property-listings' ),
				'author'          => __( 'Author', 'easy-property-listings' ),
				'type'            => __( 'Type', 'easy-property-listings' ),
				'allStatuses'     => __( 'All Statuses', 'easy-property-listings' ),
				'allAuthors'      => __( 'All Authors', 'easy-property-listings' ),
				'allTypes'        => __( 'All Types', 'easy-property-listings' ),
				'noListings'      => __( 'No listings found.', 'easy-property-listings' ),
				'loading'         => __( 'Loading...', 'easy-property-listings' ),
				'edit'            => __( 'Edit', 'easy-property-listings' ),
				'view'            => __( 'View', 'easy-property-listings' ),
				'trash'           => __( 'Trash', 'easy-property-listings' ),
				'restore'         => __( 'Restore', 'easy-property-listings' ),
				'delete'          => __( 'Delete Permanently', 'easy-property-listings' ),
				'bulkActions'     => __( 'Bulk Actions', 'easy-property-listings' ),
				'apply'           => __( 'Apply', 'easy-property-listings' ),
				'settings'        => __( 'View Settings', 'easy-property-listings' ),
				'listView'        => __( 'List View', 'easy-property-listings' ),
				'gridView'        => __( 'Grid View', 'easy-property-listings' ),
				'compactView'     => __( 'Compact View', 'easy-property-listings' ),
				'columns'         => __( 'Columns', 'easy-property-listings' ),
				'perPage'         => __( 'Per Page', 'easy-property-listings' ),
				'save'            => __( 'Save', 'easy-property-listings' ),
				'cancel'          => __( 'Cancel', 'easy-property-listings' ),
				'saved'           => __( 'Saved', 'easy-property-listings' ),
				'error'           => __( 'Error', 'easy-property-listings' ),
				'yes'             => __( 'Yes', 'easy-property-listings' ),
				'no'              => __( 'No', 'easy-property-listings' ),
				'featured'        => __( 'Featured', 'easy-property-listings' ),
				'beds'            => __( 'Beds', 'easy-property-listings' ),
				'baths'           => __( 'Baths', 'easy-property-listings' ),
				'price'           => __( 'Price', 'easy-property-listings' ),
				'address'         => __( 'Address', 'easy-property-listings' ),
				'modified'        => __( 'Modified', 'easy-property-listings' ),
				'date'            => __( 'Date', 'easy-property-listings' ),
				'selectAll'       => __( 'Select All', 'easy-property-listings' ),
				'selected'        => __( 'selected', 'easy-property-listings' ),
				'confirmDelete'   => __( 'Are you sure you want to delete the selected listings permanently?', 'easy-property-listings' ),
				'confirmTrash'    => __( 'Are you sure you want to move the selected listings to trash?', 'easy-property-listings' ),
			),
			'adminUrl'       => admin_url(),
		)
	);

	// Set script translations.
	wp_set_script_translations( 'epl-unified-listings', 'easy-property-listings' );
}

/**
 * Enqueue styles for the unified listings page.
 *
 * @since 4.0.0
 */
function epl_enqueue_unified_listings_styles() {
	$style_file = EPL_PATH_LIB . 'admin/unified-listings/build/index.css';

	if ( file_exists( $style_file ) ) {
		$asset_file = EPL_PATH_LIB . 'admin/unified-listings/build/index.asset.php';
		$assets     = file_exists( $asset_file ) ? include $asset_file : array( 'version' => EPL_PROPERTY_VER );

		wp_enqueue_style(
			'epl-unified-listings',
			EPL_PLUGIN_URL . 'lib/admin/unified-listings/build/index.css',
			array( 'wp-components' ),
			$assets['version']
		);
	}

	// Enqueue additional inline styles for loading state.
	wp_add_inline_style(
		'wp-components',
		'
		.epl-unified-listings-wrap {
			max-width: 100%;
		}
		.epl-loading-placeholder {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 60px 20px;
			background: #fff;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			margin-top: 20px;
		}
		.epl-loading-placeholder .spinner {
			float: none;
			margin: 0 0 10px 0;
		}
		.epl-loading-placeholder p {
			margin: 0;
			color: #646970;
		}
		'
	);
}

/**
 * Show admin notice when build is missing.
 *
 * @since 4.0.0
 */
function epl_unified_listings_build_notice() {
	?>
	<div class="notice notice-warning">
		<p>
			<strong><?php esc_html_e( 'Easy Property Listings:', 'easy-property-listings' ); ?></strong>
			<?php esc_html_e( 'The unified listings interface needs to be built. Please run', 'easy-property-listings' ); ?>
			<code>npm install && npm run build</code>
			<?php esc_html_e( 'in the', 'easy-property-listings' ); ?>
			<code>lib/admin/unified-listings</code>
			<?php esc_html_e( 'directory.', 'easy-property-listings' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Get currency symbol from currency code.
 *
 * @since 4.0.0
 * @return string Currency symbol.
 */
function epl_unified_listings_get_currency_symbol() {
	$currency = epl_get_currency();

	$symbols = array(
		'USD'  => '$',
		'AUD'  => '$',
		'NZD'  => '$',
		'CAD'  => '$',
		'HKD'  => '$',
		'MXN'  => '$',
		'SGD'  => '$',
		'GBP'  => '£',
		'EUR'  => '€',
		'JPY'  => '¥',
		'INR'  => '₹',
		'BRL'  => 'R$',
		'ZAR'  => 'R',
		'ILS'  => '₪',
		'THB'  => '฿',
		'TRY'  => '₺',
		'UAH'  => '₴',
		'VND'  => '₫',
		'RIAL' => '﷼',
		'CZK'  => 'Kč',
		'NAD'  => '$',
	);

	return isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency;
}

