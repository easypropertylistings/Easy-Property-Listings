<?php
/**
 * Welcome Page Class
 *
 * @package     EPL
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0
 */
class EPL_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'edit_published_posts';

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'epl_welcome' ) );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menus() {
		// About Page
		add_dashboard_page(
			__( 'Welcome to Easy Property Listings', 'easy-property-listings'  ),
			__( 'Welcome to Easy Property Listings', 'easy-property-listings'  ),
			$this->minimum_capability,
			'epl-about',
			array( $this, 'about_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with Easy Property Listings', 'easy-property-listings'  ),
			__( 'Getting started with Easy Property Listings', 'easy-property-listings'  ),
			$this->minimum_capability,
			'epl-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Credits Page
		add_dashboard_page(
			__( 'The people that build Easy Property Listings', 'easy-property-listings'  ),
			__( 'The people that build Easy Property Listings', 'easy-property-listings'  ),
			$this->minimum_capability,
			'epl-credits',
			array( $this, 'credits_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'epl-about' );
		remove_submenu_page( 'index.php', 'epl-getting-started' );
		remove_submenu_page( 'index.php', 'epl-credits' );

		// Badge for welcome page
		$badge_url = EPL_PLUGIN_URL . 'lib/assets/images/epl-wp-badge.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.epl-badge {
			padding-top: 150px;
			height: 52px;
			width: 185px;
			color: #666;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
			margin: 0 -5px;
			background: url('<?php echo $badge_url; ?>') no-repeat;
		}

		.epl-about-wrap .epl-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.epl-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'epl-about';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'epl-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'easy-property-listings'  ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'epl-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'easy-property-listings'  ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'epl-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'easy-property-listings'  ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function about_screen() {
		list( $display_version ) = explode( '-', EPL_PROPERTY_VER );
		?>
		<div class="wrap about-wrap epl-about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'easy-property-listings'  ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'easy-property-listings'  ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'easy-property-listings'  ), $display_version ); ?></div>

			<?php $this->tabs(); ?>


			<?php $lp_link = '<a href="https://easypropertylistings.com.au/extensions/location-profiles/">' . __('Location Profiles' , 'easy-property-listings' ) . '</a>'; ?>
			<?php $tm_link = '<a href="https://easypropertylistings.com.au/extensions/testimonial-manager/">' . __('Testimonial Manager' , 'easy-property-listings' ) . '</a>'; ?>
			<?php $am_link = '<a href="https://easypropertylistings.com.au/extensions/advanced-mapping/">' . __('Advanced Mapping' , 'easy-property-listings' ) . '</a>'; ?>
			<?php $sd_link = '<a href="https://easypropertylistings.com.au/extensions/staff-directory/">' . __('Staff Directory' , 'easy-property-listings' ) . '</a>'; ?>

			<div class="changelog headline-feature">

				<h2><?php _e( 'New Grid Styles' , 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-new-grid.png'; ?>" class="epl-welcome-featured-image"/>
				</div>

				<div class="feature-section">
					<p><?php _e( 'When the grid/list option was added in a previous version last year we did so without adding an internal container so that the grid could neatly format at 33.3% to maintain an even effect.' , 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'We have now implemented an internal container on the listing element and moved the styling options to this new CSS class. So when you decide to use the new styles you’ll have to update your styling to this new class called epl-property-blog-entry-wrapper.' , 'easy-property-listings'  ); ?></p>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2><?php _e( 'Load Custom CSS from Your Theme' , 'easy-property-listings'  );?></h2>

				<div class="feature-section">
					<p><?php _e( 'In 3.1 we have also added the ability to load custom stylesheets from your_theme/easypropertylistings/style.css which allows you to quickly style your listings and keep the stylesheets separate from your main theme stylesheets. This allows you to quickly take your custom created templates and use them on other real estate sites.' , 'easy-property-listings'  ); ?></p>

				</div>
			</div>

			<div class="changelog headline-feature">

				<h2><?php _e( 'Contact and Lead Management System' , 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-contacts.png'; ?>" class="epl-welcome-featured-image"/>
				</div>

				<div class="feature-section">
					<p><?php _e( 'Contact management and lead CRM has been added to the Easy Property Listings plugin and we are very excited to finally release this to you to use on your real estate website.' , 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'Lead generation in real estate is a must online and we have created an easy to use CRM that allows you to track and communicate with your prospects as they make enquiries on your listings. If you already are using a CRM in your business then the contact system will allow us to create add-ons which will be able to pass the lead information directly into your real estate CRM provided yours has an API. This will help you streamline your daily lead generation and prospecting activities with your website.' , 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'The contact system has been designed to be easy to use and allow you to track and capture contact information on your listings.' , 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'Back when I was selling property I would frequently meet the same people at different properties that I had listed for sale and now you will be able to create a story around your potential buyer or seller shopping for an agent.', 'easy-property-listings'  ); ?></p>

				</div>
			</div>

			<div class="changelog headline-feature">

				<h2><?php _e( 'Reports' , 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-reports.png'; ?>" class="epl-welcome-featured-image"/>
				</div>

				<div class="feature-section">
					<p><?php _e( 'This release introduces a reports feature for you to track your listings, sales and rentals so you can visually see how your business is going at a glance.', 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'Graphs are cool and they really can give you a quick picture of how you are going with your real estate business so you can see trends in your business to better prepare your prospecting. Real Estate can be a very seasonal business and when you can see the last 5 years of your listings on a graph you can plan ahead.', 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'If you are running a business and have several sales and property manager staff you will be able to see how your business is going and track the KPI’s of your people.', 'easy-property-listings'  ); ?></p>

					<p><?php _e( 'We have additional enhancements planned for the reports moving forward so you can track commissions so you can see your sales and rental revenue.', 'easy-property-listings'  ); ?></p>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2><?php _e( 'Additional Updates', 'easy-property-listings'  );?></h2>


				<p><?php _e( 'Along with the new Contacts and Reports features we have been optimising code, adding many new filters and functions to the code base to make your site faster and allow developers to create any type of real estate website that they can imagine.', 'easy-property-listings'  ); ?></p>

				<p><?php _e( 'We have fixed some issues and improved the listing edit pages to slim them down a bit.', 'easy-property-listings'  ); ?></p>

				<p><?php _e( 'Many more changes have been made which are noted in the Change Log below.', 'easy-property-listings'  ); ?></p>

			</div>

			<div class="changelog headline-feature">

			<h2 id="guide-changelog"><?php _e( 'Full Change Log','easy-property-listings'  );?></h2>

				<div class="feature-section">

					<h4><?php _e( 'Version 3.1.19', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Filter added to allow filtering of epl_meta_filter_{property_meta_key_name}.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Allow Full URL for user profile, Twitter, Facebook, Google Plus accounts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the epl_property_sub_title_commercial_features filter to allow altering.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the epl_property_sub_title_rural_features filter to allow altering.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the epl_switch_views_sorting_title_sort filter to allow altering.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the epl_switch_views_sorting_title_list filter to allow altering.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the epl_switch_views_sorting_title_grid filter to allow altering.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.18', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Corrected Commercial and Business epl_property_suburb function to only display suburb.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.17', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Geocoding Address with only partial address details will now generate coordinates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Sorting rentals after performing search would sometimes return no results.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.16', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Rebuilt search CSS containers for easier formatting with exact widths.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_property_category_value for altering house category.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Add Listing Status and Under Offer to post class.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added Commercial Type to post class.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Ability to display multiple categories on listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected returning of none and added value to get_property_category, get_property_land_category, get_property_commercial_category and get_property_rural_category functions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Rental sorting error in listing shortcodes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Author widget on pages with sorting.', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1.15', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Car searching Any will now return listings with no carport or garage.', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1.14', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Tweak: Allow author box to be used on non Easy Property Listings posts without error.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed Brazilian Portuguese from plugin as language package is now served from WordPress.org', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1.12', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Filter epl_property_land_area_unit_label for Land Unit Label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_property_building_area_unit_label for Building Unit Label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_the_property_feature_list_before before the features list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_the_property_feature_list_before_common_features before the common features list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_the_property_feature_list_before_additional_features before the additional features list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter epl_the_property_feature_list_after for after the output of the features list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Property, Rural, Commercial Category output to secondary heading.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Altered land sqm output to m2.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Shortcode [listing_auction] now only displays auction listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Property Category now outputs to feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Rural Category now outputs to feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Commercial Category now outputs to feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Empty Commercial Features heading no longer outputs heading if values are empty.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Empty Rural Features heading no longer outputs heading if values are empty.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.11', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Property ID search in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Brazilian Portuguese Translation thanks to Dijo.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_button_target_floorplan filter.', 'easy-property-listings'  );?></li>

					</ul>

					<h4><?php _e( 'Version 3.1.10', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Filter added epl_ical_args for iCal output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Ability to search by property ID when managing listings from the Dashboard.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added Sortable column Unique ID.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.9', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Tweak: Allowed Authors and Contributors to access help screens.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.8', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Corrected Listing not found filters used in archive templates with a new epl_property_search_not_found hook.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translations updated.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.7', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Added epl_template_class to templates and added its context for Listing Templates extension.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Auction Date processing function for import scripts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: REAXML convert date/time to adjust for timezone for import scripts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Wording for delete settings adjusted to reflect radio option.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected missing Property Features title and filter.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.6', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Hierarchical Features Taxonomy EPL_FEATURES_HIERARCHICAL Constant.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter for Commercial For Sale and Lease label epl_commercial_for_sale_and_lease_label when both option selected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added filters for shortcodes to adjust no results messages. Filters epl_shortcode_results_message_title_open for [listing_open] shortcode and epl_shortcode_results_message_title for all other shortcodes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Additional case values for importing additional features now accepts YES, yes, Y, y, on, NO, no, N, n, off.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Common features filter epl_property_common_features_list added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Corrected spelling of meta box group ids for commercial_features and files_n_links.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Author widget will no longer display if hide author box on a listing is ticked.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Filter for epl template class.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Commercial listing lease price text display when both option selected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Property Features title filter epl_property_sub_title_property_features enabling title modification.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Post type archive called incorrectly in some cases.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: PHP 7.1 support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Class adjustment for taxonomy search.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.5', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Added a Google Maps API key notification to Easy Property Listings > Settings when no key is set.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Internal shortcode option documentation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Shortcode offset breaking pagination. Note when using offset, pagination is disabled: [listing] , [listing_category], [listing_feature], [listing_location]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected the default option when using select fields.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.4', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Added offset option to the following shortcodes that allows you to place multiple shortcodes on a single page and prevent displaying duplicate listings. Added to the following shortcodes: [listing] , [listing_category], [listing_feature], [listing_location]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Optimisations to secondary author display by removing duplicate code.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements to extension license updater and notifications on license status.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Performance improvements to admin functions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translations adjustment to load textdomain after all plugins initialised.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.1.3', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Contact linking when editing listings with invalid contact ID.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Shortcode sorting for Current/Sold.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Commercial Lease price display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Output Ensuite to features list.', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1.2', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Corrected the address display of the Commercial and Business listing types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Extension updater class to provide automatic updates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Visiting the plugins page now caches plugin updates.', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1.1', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: [listing] shortcode with author option correctly filters by username.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listing search undefined result when using custom search options.', 'easy-property-listings'  );?></li>
					</ul>


					<h4><?php _e( 'Version 3.1', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Rebuilt templates including additional wrapper for better grid layout.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added legacy CSS option to prevent using new stylesheets when updating to 3.1 ensuring your listing display remains consistent.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Enhanced grid wrapper CSS to better display listings in a grid format and improved CSS by splitting global style.css with style-structure.css allowing for better compatibility with themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Class based front JS scripts for enhanced compatibility.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Implemented cron checking in extension license handler and updated license updater EDD code.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added filter for epl_get_contacts_args to enable contact form field changes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_get_next_contact_link_query filter to adjust contact query.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_contact_access filter to adjust contact system access by user level.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contextual help tab on listing pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_author_description_html filter to adjust the author description.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Cron added to handle scheduled events like license checking and updating.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Auction epl_auction_feed_format date format filter added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_get_property_com_rent to allow commercial rent price formatting.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search radio option and checkbox added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Refactored search into class based code.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Commercial search added (beta) disabled by default.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Conditional post types added for checking on enabled  listing types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Support for DIVI theme framework.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_meta_commercial_category_value to adjust commercial category.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Parse EPL shortcodes for meta queries.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Widget template no image added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sorting order function added.', 'easy-property-listings'  );?></li>
						<li><?php _e( "New: Pagination option added to all listing shortcodes pagination = 'on' default.", 'easy-property-listings'  );?></li>
						<li><?php _e( "New: [listing_category] shortcode added compare option. category_compare = 'IN' usage is based on SQL query options. 'IN','NOT IN','BETWEEN','NOT BETWEEN'", 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Wrapper added to templates to improve display and  provide even grid spacing.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added search address to separate from ID search.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: No image icon for listing attachments.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display lease price if nothing selected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added epl_get_property_price_lease_display filter to control lease price display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: License checker for updates set to daily and constant added to improve plugin page performance and reduce the update checker frequency.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Load custom stylesheet from active_theme/easypropertylistings/style.css', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added Pet Friendly options.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search frontend radio option epl_frontend_search_field_radio.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search frontend multiple checkbox option epl_frontend_search_field_checkbox_multiple.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search placeholders added to text fields.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Correctly wrap epl_the_excerpt.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Divi theme support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Select multiple added as custom field ability.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Custom field option checkbox_option.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Pet Friendly option added to rentals.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Open Parking spaces added to listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Prefixed additional css in templates for better styling.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: License handler using https.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements to contact actions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: License styling improved for better WordPRess UX.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: LinkedIn link adjusted for worldwide usage.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: get_property_meta improved.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial leased sticker corrected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: property_land_area adjustment for numerical value.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial and land category correctly displaying.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: On activation the Property post type is enabled by default.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements to listing widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Inspection time and date format improved.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: File option added to external links for floorplans.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Template wrappers prefixed for details, property meta, icons, address, content.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Languages moved for better compatibility with translation plugins.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Listing search widget status label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Reset page sorting when performing a search on a sub page with a widget or shortcode.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Adjusted price and rental search ranges.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translation fix for rent period.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Numerous changes to CSS to improve listing display and responsiveness.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Settings checkbox options display correctly.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements to author box functions for multi-author.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: LinkedIn author link adjusted.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Conditional tags when lo listing types are activated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Improved onclick links in external, web links to conform with new JS class.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Commercial car spaces displaying incorrectly.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Conditional tags improved.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.0.4', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Internal help videos gzip error, using iframe instead.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected incorrect stray tags on internal welcome page.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.0.3', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Setting to disable Google Maps API if already added by theme or other plugin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to set a Google Maps API Key.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Renamed misspelled Property on linked contact.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Trailing ul tag on search widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Implemented better timezone support for open for inspection. Requires WordPress 3.9.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Tighter spacing on dropdown contact list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated translations file.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Capital c for contact post type.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Dashboard activity widget improved CSS display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Dashboard activity comments better labeled.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Internal links to documentation corrected.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.0.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Featured Listing removed redundant no option.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.0.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Versioning to all CSS and JS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Arabic translation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated German Translation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated French Translation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated Dutch Translation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search by Address and Property ID correctly searches the listing Title. In order to search by property ID, add the property ID to the listing title.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Customise the EPL - Contact Form Widget Submit Label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added Form styling to contact form.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Corrected additional translation strings with contact form labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Corrected spacing in extension plugin updates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Renamed EPL - Contact Form Subscribe label to Submit.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 3.0', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Textdomain and languages files renamed. Changed from epl to easy-property-listings for the WordPress.org translation initiative.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Every epl_action present in the $_GET or $_POST is called using WordPress do_action function in init.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Radio options when adding listings converted to checkboxes to slim down the admin pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Ducted Heating additional features now displays in feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Fully fenced option now displays in feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Optimise Admin Listing queries.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed double display of Under Offer in admin listing list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Leased rental listings now display the weekly rent amount in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial Lease listing details improved in admin list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Sold price displays in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Date Available fix for year.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_available filter allows customising date format.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: External links function improved.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added additional plugin file security access to prevent file reading outside of WordPress.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Number Formatting function PHP warning fixed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: is_epl_post function to prevent error when no posts are activated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial auction listing support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contacts and form system for managing listing leads and history of contact.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: contact_capture shortcode // Needs Author id of page and URL.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contact System for Lead Generation and Capture.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Form API supports editor.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Dashboard Widget Listing and Contact Activity Feed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Date Picker updated JS for improved usage and improved compatibility with themes and plugins.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Code Docblocks created for http://docs.easypropertylistings.com.au code reference.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Link a contact with a listing and display details and quick access to contact.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Error tracking and debug logging helper functions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Form API supports sections breaks.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contextual help tab added to Add/Edit Listing page.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Inspection date format now customisable from settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Extension license updater updated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak Added additional map CSS classes to improve Google Map output with some themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Adjustable Map pin when editing a listing and setting coordinates. Drag the map pin to adust the position.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Imported values of 0 no longer display on commercial listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: epl_render_html_fields allows for css class set in the field array of meta-boxes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial authority default type is now For Sale instead of Auction.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Converted Radio options to tick boxes to reduce space.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial auction listing support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Bedrooms allow studio option.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Applied thousands separator to land sizes using settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Allow for .00 and .0 when adding listing prices.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Toilet supports decimal.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Additional Features increased to three columns to minimise space with single checkboxes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Listing price, sale, and rental price now supports decimal values when saving.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Bond supports decimal figures.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translation strings fixed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: m2 html character added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Listings with prices set to 0 like bond no longer display in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Rental listing when using price text the rental period no longer displays in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Pagination loading globally for use in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Pagination enhanced to enable adjustment of output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Old function in metaboxes removed as it inadvertently caused additional unnecessary queries.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Generate visual reports on your listing KPI status so you can track your listings and sales.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: [listing_search] shortcode using new API and allows for custom templates. Place the template in themes/your_theme/easypropertylistings/templates/ folder.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Enhanced Search Object thanks to codewp allows widget template override.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Building value now accepts decimal.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget and [listing_search] shortcode allows for property status option.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search template now editable using epl_get_template_part.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and [listing_search] shortcode order option added to allow adjusting of field order.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Second agent field allows for searching users.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search upgraded to object thanks to codewp.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search for second listing author on listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and [listing_search] shortcode status search option added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and [listing_search] shortcode support any registered post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and [listing_search] shortcode support single drop down selection for price, land, building.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Session start less likely to cause issues with certain server configurations.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: listing_open shortcode no longer displays sold or leased listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Additional customisation of shortcode-listing.php template part.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Listing Shortcode adjusted for better processing of options.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: [listing_auction] shortcode.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contact shortcode. [epl_contact_form].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contact Form Widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sort by location A-Z added to front end listing filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: iThemes Builder archive-listing.php and single-listing.php templates updated to improve render_content theme function.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Allow extensions to use core templates for output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Added translation string for P.A. label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Translation of land size unit.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: LinkedIn will use full URL or fallback.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Default embedded video width adjustable from settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Video links now support additional formats like Vimeo using the WordPress wp_oembed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Listing widget now loadable using epl_get_template_part thanks to codewp.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Widget descriptions added to widget management.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Stray ul tag with search widget tabbing.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved get_additional_features_html function for additional features and added epl_get_additional_features_html filter', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Contact tags taxonomy added for creating your own contact tags.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Listing heading function enhanced for other post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_feature_taxonomy filter allowing adjustment of listing features.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_auction filter allows adjustment of auction date format.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_auction_label filter to adjust the Auction label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Support for Twenty Sixteen theme.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Active theme function enhanced for older WordPress versions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Templates added for Twenty Fourteen Theme to improve display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Archive title action added for easier implementation and filters to adjust output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_feedsync_format_strip_currency function to strip currency during import with epl_feedsync_format_strip_currency_symbol filter to modify string replacement search.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_archive_title_search_result Filter, default "Search Result".', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_archive_title_fallback Filter, default "Listing".', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_archive_title_default Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_active_theme Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_active_theme Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_active_theme_name Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_active_theme_prefix Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_archive_title_fallback Filer.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: epl_strip_tags function added filter to adjust HTML tag stripping.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_contact_form_description_allowed_tags Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_price_display Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_price_sold_display Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_price_sold_date Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_rent Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bond Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_land_category Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_commercial_auction_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_auction_date Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_price_plain_value Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_price Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_price_sticker Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_price_in_list Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_commercial_category Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_year_built_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_year_built Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bath_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bathrooms_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bath Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bed_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bedrooms_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_bed Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_rooms_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_rooms Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_parking_spaces_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_parking Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_garage_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_garage Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_carport_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_carport Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_air_conditioning_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_air_conditioning Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_pool_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_pool Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_security_system_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_security_system Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_land_area_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_land_value Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_building_area_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_building_area_value Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_new_construction_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_new_construction Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_com_car_spaces_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Dynamic additional features epl_get_{meta_key}_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_additional_features_html Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_additional_rural_features_html Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_additional_commerical_features_html Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_features_from_taxonomy Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_checkbox_single_check_options Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_plus_outgoings_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_available_from_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_available_now_label Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_formatted_property_address filter Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_category  Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_get_property_tax Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_property_features filter for Property Features label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_plus_outgoings filter for Plus Outgoings label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_commercial_features filter for Commercial Features label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_property_sub_title_rural_features filter for Rural Features label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_switch_views_sorting_title_sort filter for Sort label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_switch_views_sorting_title_list filter for List label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_switch_views_sorting_title_grid filter for Grid label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_before_page_numbers  Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_after_page_numbers  Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_content_text Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_tag  Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_dot_tag Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_dot_content Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_dot_attributes Filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: epl_pagination_single_dot Filter.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.3.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Added a hidden field property_images_mod_date for image mod time in preparation for importer plugin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added categories to search for business, rural, land, commercial, commercial_land post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Adjusted z-index of sticker label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Hide address separator when address is empty.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search price fix for commercial, commercial_land, and business.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: POA label now obeys custom label setting.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.3', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Custom Post Type API. Makes it easy to create and register new custom post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Custom Meta Box API. Creating custom fields and being able to configure custom meta fields on existing and new post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Custom Forms API. Will give the ability to create forms and submissions for the coming CRM. (Customer Relationship Manager).', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ordering of extension dynamic custom fields now possible.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Archive template attributes class dynamically added depending on template in use.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: A number of helper functions have been added to better integrate additional custom post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Button meta field for use in extensions and custom fields.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Adjustments to video output function.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Features taxonomy now use archive template instead of blog post view.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filters to adjust the Search not found text epl_property_search_not_found_title and epl_property_search_not_found_message.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Restored get_property_suburb function which was used in Listing Templates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Better author linking and real estate agent user output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements for other extensions to hook into and use maps.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Template fallback functions for improved custom template usage.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Swedish translations updated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translation file updated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: New Construction class corrected to new_construction instead of pool.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Property ID searching improved. If you have a-z characters in your id include them in the title. E.g. aaa222 - 9 Somewhere Street, Brooklyn NY.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.7', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Compatibility for Listing Templates extension.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.6', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Updated extension licensing updater to use https. Update required in order to be able to auto-update your extensions as Easy Property Listings has moved to https.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.5', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Widget construct fixes for WordPress 4.3.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Un-install function.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Plugin page link to settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Languages updated.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.4', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Improvements to Commercial/Commercial Land/Business pricing when set to Lease type to display free form price text.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Bar graph in dashboard will no longer cover address if set to low.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added sticker CSS styling for single listing.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode display house category value instead of key.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode Property ID correctly searches numeric listing ID.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode excluded non searchable fields from land, commercial, commercial land and business post types.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.3', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Adjusted new sorter function to work on lower than PHP version 5.3.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Moved old template functions to theme compatibility, will be removed in future version.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Set sorter list style to none to prevent some themes from displaying a list bullet.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: CSS tweak for image size to retain proportion on some themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Adjusted position of show/hide suburb on Commercial/Business listing types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Archive image correctly loading 300x200 image.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listing address display settings fixed.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Set padding for search tabs for better display on some themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search function fix checking for empty option when using custom filters.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Search shortcode and widget rebuilt to enable adding additional fields through filters and hooks.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search shortcode and widget added additional search fields for City, State, Postcode and Country.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search shortcode and widget allows for optional multi select of house category.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search shortcode and widget improved responsive CSS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Grid styles included in main CSS for use in extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Upload button added for use in custom plug-ins and extensions to upload files.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter to adjust tour labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filters to adjust Floor Plan labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filters to adjust External Link labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sold prices now display when set on front end and manage listings pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Label function for returning meta labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ads on settings no longer display when there is an activated extension present.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Locked and help cases options for use in extensions and custom plugins.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Theme compatibility mode which enables all themes to display correctly with options to disable featured images for themes that automatically add featured images.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: City setting to allow addresses in countries that need more than a suburb Label is customisable from settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Country setting to allow the country to display with the listing address.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Able to adjust or add more registered thumbnail sizes through a filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Function to get all the values associated with a specific post meta key.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Replaced the_post_thumbnail on archive pages and shortcodes with a customisable hook allowing for additional customisation with themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Specific templates for theme compatibility mode for archive and single listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Template loading system allowing for additional templates to be added to shortcodes and widgets from themes, custom plug-ins and extensions. This allows you to create an unlimited number of templates and load them from your theme.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sorter allows for sorting by current/sold leased.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to add additional sorter via filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Post counter function for use in extensions and custom plug-ins.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: User fields re-built which allows for adding on new fields through filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Help meta type allowing for better internal documentation in extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: City meta field added to all listing types when enabled.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Rental display or hide rental price.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Check-box single field type.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Actions added to enable extensions to better hook into listings types and optimised functions for admin column details.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Dashboard widget now displays other extensions content counts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Listing widget now allows for additional selectable templates to be added through custom plug-ins, hooks and themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Replaced widget image with a dynamic action.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filter added for Gravatar image.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Replaced widget and author box image functions with actions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Uninstall function to remove all Easy Property Listings content.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Get option function.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: When saving settings on extensions sub tabs you are no longer taken to the first tab.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Customisable state label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved under offer, sold and leased labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved install function to reduce code and allow for new settings to be added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed redundant code and streamlined templates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved reset query function.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed old functions improving plugin code.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Rebuilt address function to allow for city and country.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved sorter function in all shortcodes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improvements to Commercial and Business listing types to better comply with REAXML format with business takings, franchise, terms and commercial outgoings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Reorganised settings page.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Translations updated and additional tags added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Search button default label changed from "Find Me A Property!" to "Search".', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Applied custom suburb label to EPL - Listing Widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listings house categories correctly display labels instead of values.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listings with carport, garage or values set to zero no longer display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Shortcode compatibility for WordPress 3.3 thanks to codewp', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Saving listing when in debug mode and ticking hide map or hide author box.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: New Zealand currency now displays a dollar sign.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.11', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Tweak: Removed sub titles "Property Manager" and "Real Estate Agent" from the single listing template for better language support and to facilitate the hiding of the author box.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added epl- prefix to all author-box and widget css.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Renamed author-box container with epl-author-box-container as it was harder to target the author box content and adjusted JS for tabs.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved author box responsive CSS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated extension updater for multisite and other improvements.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Leased label when adding a property will use custom label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Wrapper class for property category.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Undefined status if importing listings not using current status.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: When user selects grid/list option and pages the user selected view is retained.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: [listing post_type="rental"] shortcode price sorting for rental.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Author box is now able to be hidden on a per listing basis.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added filters for author box social links.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Inspection filter to adjust the inspection date/time format.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Several author widget filters added to enable additional content through extensions or custom functions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sold, leased, under offer label filter which uses the label setting and label changes dashboard widget, admin category filters and search widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sold label making Sold STC possible or other Sold label variant.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Danish language thanks to pascal.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: German language thanks to ChriKn.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ukrainian language thanks to Alex.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Swedish language thanks to Roland J.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.10', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Email field validation added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added status classes to widgets for better targeting of CSS styles.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved video embed and added a filter to adjust video container size.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved CSS wrappers for listing widget and added dynamic class depending on widget display style.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added additional classes to Listing Widget list variant style list items.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Additional paging issues fixed in listing widget for other options.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Widget leased selection displays rentals correctly.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.9', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Fixed paging issues in listing widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Fix shortcodes when using multiple listing post types.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.8', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: Ability to disable all plugin CSS from Advanced Settings section.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and shortcode now have the option to turn of Location search.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search widget and shortcode now have filters to control the display of "Any". Each field has a unique filter which will allow you to hide the label using CSS and for example change the Location "Any" label to "Location" this will allow you to create super slim search boxes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added translation Belgian (Dutch) thanks to pascal.beyens', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Polish translation thanks to Weronika.urbanczyk', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Two mew shortcode templates table and table_open usable with shortcodes to provide a slim list of listings. Example usage is [listing_open template="table"] or [listing template="table_open"]. You can copy these new templates into your theme/easypropertylistings folder to further customize.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added currency support for Qatar Riyal (QAR), United Arab Emirates (AED), Ukrainian Hryvnia (UAH), Vietnamese đồng (VND)', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: checkbox_single ability for plugin and extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to disable map on each listing.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated currency symbols for: Israeli Shekel, Thai Baht, Indian Rupee, Turkish Lira, Iranian Rial.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved CSS and added additional classes with epl- prefix in templates and search.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved CSS for Location Profiles and Staff Directory extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added filters for commercial titles to allow you to change "For Lease" and "For Sale" using epl_commercial_for_lease_label, and epl_commercial_for_sale_label filters.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Additional CSS classes for Land, Commercial and Rural special features.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Gallery CSS classes added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved table shortcodes CSS and styling for better full display and responsive widths.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: New/Open Sticker now appear on listings with the price display set to no.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Translations work correctly for categories.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.7', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'New: listing_search shortcode now has style option for adjusting the width. You can add style="slim" or style="wide" to the shortcode to adjust the appearance.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Listing Search widget now has style options for adjusting the width.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated translation and added missing sqm translation element.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Allowed for hundredths decimal in bathrooms field.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Floor plan button CSS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Address and price responsive CSS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Auction listing price set to no displays auction date correctly.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Fix: Author position css class.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.6', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Fancy pagination paging works correctly when shortcodes used on home page.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Wrapped new pagination feature in esc_url to prevent vulnerability.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected sorting by price when using shortcodes. Note: Rental sorting works on post_type="rental" in all shortcodes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added rental rate view for text entry of rental rates for REAXML compatibility.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Corrected admin display columns and edit listing pages for better display on mobile devices.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.5', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Tweak: Commercial listing: Ability to set commercial lease rate to a decimal value using the epl_price_number_format_commercial_lease filter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated epl.pot translation file.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed horizontal line elements in the help section to match WordPress 4.2 admin page styles.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Rental Listing: Added epl_property_bond_position filter to adjust the position of the Bond/Deposit to appear either before or after the value.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Rental Listing: Removed CSS padding before bond value.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Rental Listing: Adjusting the Bond/Deposit label will now show your custom label in the Rental Price box.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Rural Listing: Undefined label_leased variable.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Note: Confirmed Easy Property Listings is not vulnerable to recent WordPress exploit.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added setting to show/hide Listing Unique ID column when managing listings.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.4', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Tweak: Pagination optimised and no longer loads in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: New filter epl_price_number_format added for decimal rental rates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Display custom bond label when viewing listings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added filter epl_floorplan_button_label_filter to adjust Floor Plan button label.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.3', 'easy-property-listings'  );?></h4>

					<ul>
						<li><?php _e( 'Fix: Author box upgraded to allow for custom tabs and better extension integration with author box and widget.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Added additional epl-author-archive CSS class for author archive pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Improved CSS classes for author box with better responsive support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Added additional filters for author contact information.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Added secondary global author function for simpler integration for extensions like the Staff Directory.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Changes to author tempaltes and restored author position variable.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Further improved max and min graph values when in listing admin.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Improved Responsive CSS for grid style.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Twenty Fifteen, Twenty Fourteen, Twenty Thirteen, Twenty Twelve CSS styles for better display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added CSS class theme name output to archive and single templates.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Max price defaults set for graph calculations when upgrading from pre 2.0 version.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Fancy pagination option which can be enabled in settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Coordinates now added to listing if not set prior.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to select larger listing image sizes in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added date picker for available date on rental listing.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added date picker for sold date.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: New function that combines all meta box options into one global function for admin pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display second agent name in admin listing lists.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Additional admin option to filter by agent/author. ', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Shortcode [listing_location] to display listings by specific location.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: The following shortcodes can now be filtered by location taxonomy: [listing location="perth"], [listing_open location="sydney"], [listing_category location="melbourne"], [listing_category location="brisbane"], [listing_feature feature="terrace" location="new-york"]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: The following shortcodes can now be sorted by price, date and ordered by ASC and DESC [listing sortby="price" sort_order="ASC"].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Sorter added to shortcodes which can be enabled by adding tools_top="on" to your shortcode options.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Template added in table format for use in shortcodes template="table".', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Function to get all active post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to register additional custom post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Extensions now have additional help text ability.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: All menus now use global function to render fields.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Improved template output and added additional CSS wrappers for some theme and HTML5 themes.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Commercial rental lease duration now selectable.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Rooms field added to set the number of rooms that the listing has.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Date listed field added to all listing types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Year built field added to property, rental, rural listing types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Media upload function for use in extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Ability to customise Under Offer and Leased labels in settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Lease type label loaded from dropdown select. So you can have NNN, P.A., Full Service, Gross Lease Rates, on commercial listing types. Also has a filter to enable customisation of the options.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Disable links in the feature list.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Text domain fixes on template files.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Finnish translation file renamed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: FeedSync date processor strptime function corrected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Bug in parking search field. Was only searching carports and not garages. Now searches both.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: New label now appears on listings not just with an inspection time saved.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Optimised loading of admin scripts and styles to pages where required.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added version to CSS and JS so new versions are automatically used when plugin is updated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Tidy up of admin CSS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Video in author box now responsive.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Increased characters possible in address block fields from 40 to 80 characters and heading block to 200.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Coordinates now correctly being used to generate map.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Inspection times improved style in admin.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Commercial rental rate now accepts decimal numbers.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved google map output.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved default settings on upgrade, install and multisite.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Scripts improve site speed.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Dashboard widget improved query.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Front end CSS tweaks for better responsiveness.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.0.3', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Manually entered inspection capitalization fixed pM to PM.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: French translation (Thanks to Thomas Grimaud)', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Finnish translation (Thanks to Turo)', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.0.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Added fall-back diff() function which is not present in PHP 5.2 or earlier used with the New label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Some Labels in settings were not saving correctly particularly the search widget labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Restored missing author profile contact form tab on author box.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added CSS version to admin CSS and front end CSS.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.0.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Attempted Twenty 15 CSS Fix but causes issues with other themes. Manual fix: Copy CSS from style-front.css to correct, margins and grid/sorter.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Restored Display of Inspection Label for properties with scheduled inspection times.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget security fix and performance improvements.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 2.0', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Extension validator.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Depreciated listing-meta.php into compatibility folder.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Depreciated author-meta.php into compatibility folder.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Global variables: $property, $epl_author and $epl_settings.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added filters for fields and groups in /lib/meta-boxes.php', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Property custom meta re-written into class. This was the big change to 2.0 where we completely re-wrote the output of the meta values which are now accessible using global $property variable and easy template actions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Property meta can now can be output using new actions for easy and quick custom template creation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Reconstructed templates for single, archive & author pages', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Removed unused price script', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Fixed warning related to static instance in strict standard modes', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: API for extensions now support WordPress editor with validation.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: jQuery date time picker formatting added to improve support for auction and sold listing, support for 30+ languages support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Inspection time auto-formats REAXML date eg [13-Dec-2014 11:00am to 11:45am] and will no longer show past inspection times.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Inspection time support multiple dates written one per line.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: CSS improved with better commenting and size reduction.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Dashboard widget now lists all listing status so at a glance you can see your property stock.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display: To enable grid, list and sorter your custom archive-listing.php template requires the new action hook epl_template_before_property_loop before the WordPress loop.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display: Utility hook action hook added epl_template_after_property_loop for future updates.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display: List and grid view with optional masonry effect.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display: Sorter added for price high/low and date newest/oldest.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Auction Date formats nicely. EG [Auction Saturday 28th December at 2:00pm].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Tabbed extensions page support in admin for advanced extensions like Listing Alerts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Multiple author support in Author Box.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Supports multiple listing types, hold Ctrl to enable tabbed front end display.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Labels are configurable from the Display settings allowing you to set for example: Property to Buy and Rental to Rent and use a single widget to search multiple types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget and shortcode supports search by property ID, post Title, Land Area and Building Area.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - removed extra fields from land, added labels for each property type to be shown as tab heading in search widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget - Optimized total queries due to search widget from 1500 + to ~40', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Author variables accessible using new CLASS.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search short code supports array of property types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: REAXML date format function to format date correctly when using WP All Import Pro. Usage [epl_feedsync_format_date({./@modTime})].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: REAXML Unit and lot formatting function for usage in the title when using WP All Import Pro. Usage [epl_feedsync_filter_sub_number({address[1]/subNumber[1]})].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Global $epl_settings settings variable adds new default values on plugin update.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Display: Added customisable label for rental Bond/Deposit.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Template functions completely re-written and can now be output using actions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added NEW sticker with customisable label and ability to set how long a listing displays the new label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Compatibility fixes', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Bar Graph API added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Graph in admin allows you to set the max bar graph value. Default are (2,000,000 sale) and (2,000 rental).', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Graph visually displays price and status.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Price graph now appears in admin pages quickly highlighting price and status visually.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Meta Fields: Support for unit number, lot number (land).', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: South African ZAR currency support.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Corrected Commercial Features ID Spelling', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: YouTube video src to id function is replaced with better method which handles multiple YouTube video formats including shortened & embedded format', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Adding Sold Date processing', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated shortcode templates', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Global $epl_author.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Fixed content/ into EPL_PATH_TEMPLATES_CONTENT', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Support for older extensions added', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Extension offers in menus general tab', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Renamed user profile options section to [Easy Property Listings: Author Box Profile].', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added better Bond/Deposit for rentals labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Deprecated author-meta.php in compatibility folder, class-author-meta.php has been created which will be used in place of author-meta.php & its variables in all author templates', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added template functions for author meta class, modified templates lib/templates/content/content-author-box-simple-card.php lib/templates/content/content-author-box-simple-grav.php lib/templates/content/content-author-box.php to use the template functions based on author meta class instead of variables from author-meta.php', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: author-meta.php depreciated and moved to compatibility directory. Variables globally available using $epl_author variable.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: listing-meta.php depreciated and moved to compatibility directory. Variables globally available with $property variable.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added Listing not Found to default templates when search performed with no results.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved Google maps address output for addresses containing # and /.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listing Pages now have better responsive support for small screen devices like iPhone.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Default templates for Genesis and TwentyTwelve now show Listing Not Found when a search result returns empty.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Purged translations in epl.pot file.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Search Widget and short code drastically reduces database queries.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Templates are now able to be saved in active theme folder /easypropertylistings and edited. Plugin will use these first and fall back to plugin if not located in theme folder.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Extensions Notification and checker updated', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: updated author templates to use new author meta class', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Added prefix to CSS tab-content class. Now epl-tab-content for compatibility.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Update user.php', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved internal documentation and updated screens.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Improved descriptions on author pages.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Better permalink flushing on activation, deactivation and install.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Extensive changes to admin descriptions and labels.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Optimising the php loading of files and scripts.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Define EPL_RUNNING added for extensions to check if plugin is active.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: New options added to setting array when plugin is updated.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Old functions and files moved to plug-in /compatibility folder to ensure old code still works.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Meta Location Label.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Service banners on settings page.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Saving version number so when updating new settings are added.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: iCal functionality for REAXML formatted inspection dates. Further improvements coming for manual date entry. ', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Extensions options pages now with tabs for easier usage.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added ID classes to admin pages and meta fields.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filters to adjust land and building sizes from number to select fields.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Moved old extensions options page to compatibility folder so older extensions still work as expected.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Added filter for land min & max fields in listing search widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Added filter for building min & max fields in listing search widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: For session start effecting certain themes', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Land sizes now allow up to 5 decimal places', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Custom submit label', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Search Widget - Can search by title in property ID / Address field', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added Russian Translation', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 1.2.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'Fix: Search Widget not working on page 2 of archive page in some instances', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Property feature list Toilet and New Construction now display in list when ticked', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: EPL - Listing widget was not displaying featured listings', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Allowed to filter by commercial_listing_type in [listing_category] shortcode', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Updated templates to display Search Results when performing search', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: No longer show Bond when viewing rental list in admin', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Open for inspection sticker now appears on rental properties', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added initial Dutch translation.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 1.2', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Plug in Activation process flushes permalinks', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Plug in deactivation flushes permalinks', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Shortcode [listing_search]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Shortcode [listing_feature]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Shortcode [listing_open] replaces [home_open] shortcode. Retained [home_open] for backward compatibility, however adjust your site. ', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Listing shortcodes allow for default template display if registered by adding template="slim" to the shortcode.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Translation support now correctly loads text domain epl', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added translation tags to all test elements for better translation support', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Updated source epl.pot translation file for translations', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added very rough Italian translation', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Wrapped Featured image in action to allow for easy removal and/or replacement', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Undefined errors when debug is active', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added new CSS classes to widgets for consistent usage', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Admin CSS tweaks to define sections in admin', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: CSS for TwentyThirteen style CSS using .sidebar container', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: CSS for responsive shortcode', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added options to hide/ show various options to EPL - Listing widget: Property Headline, Excerpt, Suburb/Location Label, Street Address, Price, Read More Button', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added customisable "Read More" label to EPL - Listing widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added excerpt to EPL - Listing widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added options to remove search options from EPL - Listing Search widget', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added consistent CSS classes to shortcodes for responsive shortcode', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Date processing function for use with WP All Import when importing REAXML files. Some imports set the current date instead of the date from the REAXML file. Usage in WP All Import Post Date is: [epl_feedsync_format_date({./@modTime})]', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added additional CSS classes to admin menu pages to extensions can be better distinguished when installed and activated', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Registering custom template actions now works correctly', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added additional CSS classes to template files', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Changed property not found wording when using search widget and listing not found. ', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Added defaults to widgets to prevent errors when debug is on', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added WordPress editor support in admin for use with extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added textarea support in admin for use with extensions.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Filters added for all select options on add listing pages which allows for full customisation through simple function', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added rent period, Day, Daily, Month, Monthly to rental listing types', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added property_office_id meta field', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added property_address_country meta field', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Allowed for decimal in bathrooms to allow for 1/2 baths eg 1.5', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added mini map to listing edit screen. Will display mini map in address block when pressing green coordinates button.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Updated admin columns for commercial_land listing type to match other listing type', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Swapped bedrooms/bathroom label on hover', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Added filter epl_listing_meta_boxes which allows additional meta boxes to be added through filter', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 1.1.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'New: Internationalisation support to enable customizing of post types: slug, archive, rewrite, labels, listing categories for meta_types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Created filters for listing meta select fields: property_category, property_rural_category, property_commercial_category, property_land_category.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Created filters for each of the seven custom post types: labels, supports, slug, archive, rewrite, seven custom post types.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'New: Shortcode [listing_category] This shortcode allows for you to output a list of listings by type and filter them by any available meta key and value.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Tweak: Updated search widget for filtered property_categories.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Listing categories were showing key, now showing value.', 'easy-property-listings'  );?></li>
						<li><?php _e( 'Fix: Settings were not showing up after saving, second refresh required setting variable to reload.', 'easy-property-listings'  );?></li>
					</ul>

					<h4><?php _e( 'Version 1.1', 'easy-property-listings'  );?></h4>
					<ul>
						<li><?php _e( 'First official release!', 'easy-property-listings'  );?></li>
					</ul>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-general' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to Easy Property Listings Settings', 'easy-property-listings'  ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function getting_started_screen() {
		list( $display_version ) = explode( '-', EPL_PROPERTY_VER );
		?>
		<div class="wrap about-wrap epl-about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'easy-property-listings'  ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'easy-property-listings'  ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'easy-property-listings'  ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Real Estate Tools for WordPress', 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-welcome.png'; ?>" class="epl-welcome-featured-image"/>
				</div>
			</div>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Quick Start Guide', 'easy-property-listings'  );?></h2>

				<h3 class="about-description" style="text-align: center;"><?php _e( 'Use the tips below to get started using Easy Property Listings. You will be up and running in no time!', 'easy-property-listings'  ); ?></h3>

				<div class="feature-section">
					<ul style="text-align: center;">
						<li><a href="#guide-configure"><?php _e( 'Activate the listing types you need & configure the plugin general settings', 'easy-property-listings'  ); ?></a></li>
						<li><a href="#guide-page"><?php _e( 'Create a blank page for each activated listing type', 'easy-property-listings'  ); ?></a></li>
						<li><a href="#guide-first-listing"><?php _e( 'Publish your first listing for testing your theme setup', 'easy-property-listings'  ); ?></a></li>

						<li><a href="#guide-theme"><?php _e( 'Setup your theme to work with the plugin', 'easy-property-listings'  ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-changelog"><?php _e( 'Full Change Log', 'easy-property-listings'  ); ?></a></li>
						<li><a href="#guide-help"><?php _e( 'Visit Support', 'easy-property-listings'  ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2 id="guide-configure"><?php _e( 'Activate the listing types you need', 'easy-property-listings'  );?></h2>

				<div class="feature-section">

					<div class="col">

						<p><?php _e( 'Visit the general settings page and enable the listing types you need. Once you have pressed save visit the Permalinks page to re-fresh your sites permalinks.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'Instead of classifying everything as a property, Easy Property Listings allows you to separate the different listing types which is better for SEO and RSS feeds.', 'easy-property-listings'  );?></p>

						<p><strong><?php _e( 'Supported Listing Types', 'easy-property-listings' ); ?></strong></p>
						<ul>
							<li><?php _e( 'Property (Residential)', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Rental', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Land', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Rural', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Commercial', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Commercial Land', 'easy-property-listings' ); ?></li>
							<li><?php _e( 'Business', 'easy-property-listings' ); ?></li>
						</ul>
					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-general-settings.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2 id="guide-page"><?php _e( 'Create a blank page for each activated listing type', 'easy-property-listings'  );?></h2>

				<div class="feature-section">
					<div class="col">

						<p><?php _e( 'Doing this allows you to add "Property", "Land" and "Rental" pages to your WordPress menu. Add a new page for each listing type you activated.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'For example, lets say you have activated: Property, Rental and Land. Create three pages, one called "Property", another "Land" and the third "Rental" these will be the custom post type slugs/permalinks eg: property, rental and land.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'Publish a test "Property Listing" and visit your new property page and you will see the new property and others you have created.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'Now you can rename them to whatever you like eg: "For Sale", "For Rent" etc, but leave the slug/permalink as it was,', 'easy-property-listings'  ); ?> <strong><?php _e( 'this is very important.', 'easy-property-listings'  );?></strong></p>

					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-default-pages.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>

			</div>

			<div class="changelog headline-feature">

				<h2 id="guide-first-listing" class="epl-welcome-sub-heading"><?php _e( 'Publish Your First Listing', 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<?php $video_add_listing_id = 'h6B8LLecfbw'; ?>

					<div class="video-container">
						<iframe width="640" height="360" src="//www.youtube.com/embed/<?php echo $video_add_listing_id; ?>" frameborder="0" allowfullscreen ></iframe>
					</div>
				</div>
				<h3 class="epl-welcome-sub-heading"><?php _e( 'Title & Author', 'easy-property-listings'  );?></h3>

				<div class="feature-section">

					<div class="col">

						<h4><?php _e( 'Title', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Use the full listing address as the title.', 'easy-property-listings'  );?>

						<p><?php _e( 'When a property is being sold the "heading" is frequently changed and can cause permalink issues. Not to mention the search engine benefits.', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Author or Primary Real Estate Agent', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Select the author to show the name of the agent who has listed the property with their contact details. For best results each real estate agent should have their own WordPress user profile which allows for the output of their details on the listing and in widgets.', 'easy-property-listings'  );?></p>

					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-title.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>

				<h3 class="epl-welcome-sub-heading"><?php _e( 'Gallery and Featured Image', 'easy-property-listings'  );?></h3>
				<div class="feature-section">

					<div class="col">

						<h3><?php _e( 'Gallery', 'easy-property-listings'  );?></h3>
						<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'easy-property-listings'  ); ?></p>

						<p><?php _e( 'You can automatically output a gallery from the Display options page.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing. You can also easily adjust the number of gallery columns from the plugin Display options.', 'easy-property-listings'  );?></p>

						<h3><?php _e( 'Gallery Light Box', 'easy-property-listings'  );?></h3>
						<p><?php _e( 'Using a light box plug-in like Easy FancyBox, your automatic gallery images will use the light box effect.', 'easy-property-listings'  );?></p>

					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png'; ?>" class="epl-welcome-screenshots"/>
					</div>

				</div>


				<h3 style="font-size: 1.8em; text-align: center;"><?php _e( 'Listing Details', 'easy-property-listings'  );?></h3>

				<div class="feature-section">

					<div class="col">

						<h4><?php _e( 'Heading', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Enter the descriptive listing headline like "Great Property with Views".', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Second Listing Agent', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'If the listing has two real estate agents marketing it, enter their WordPress user name here. The primary agent is the post Author.', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Inspection Times', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Now supports multiple inspection times, add one per line. Past inspection dates will not display when using the new format.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'The output is now wrapped in an iCal format so clicking on the date will open the users calendar.', 'easy-property-listings'  );?></p>

					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details.png'; ?>" class="epl-welcome-screenshots"/>
					</div>

				</div>

				<h3 style="font-size: 1.8em; text-align: center;"><?php _e( 'Search by location', 'easy-property-listings'  );?></h3>

				<div class="feature-section">

					<div class="col">
						<p><?php _e( 'Although the address details are added into the Property Address box the location search you also need to add the City/Suburb to the location search taxonomy.', 'easy-property-listings'  );?></p>
						<p><?php _e( 'This works like post tags and will populate the search widget/shortcode with your listings and it will automatically filter out options if no listings have that option.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details-location.png'; ?>" class="epl-welcome-screenshots"/>
					</div>

				</div>
			</div>

			<div class="changelog headline-feature">

				<h2 id="guide-theme" class="epl-welcome-sub-heading"><?php _e( 'Configure your theme', 'easy-property-listings'  );?></h2>
				<h3 class="about-description" style="text-align: center;"><?php _e( 'We have done our best to integrate Easy Property Listings with all WordPress themes.', 'easy-property-listings'  );?></h3>

				<div class="feature-section col two-col">

					<div>
						<h4><?php _e( 'Theme Compatibility', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Once you add a listing and if your page is really wide or your sidebar is under the content enable Theme Compatibility mode from settings.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'Review your listing and if you are seeing double images, hop back over to Settings page and either disable the theme feature image or the one provided by Easy Property Listings.', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Shortcodes', 'easy-property-listings'  );?></h4>

						<p><?php _e( 'The featured image settings have no impact on the Easy Property Listings shortcodes and widgets.', 'easy-property-listings'  );?></p>
					</div>

					<div class="last-feature">

						<h4><?php _e( 'Theme Compatibility not required for some themes', 'easy-property-listings'  );?></h4>
						<ul>
							<li><a href="http://ithemes.com/member/go.php?r=15200&i=l37"><?php _e( 'iThemes Builder Themes', 'easy-property-listings'  );?></a></li>
							<li><?php _e( 'Genesis Framework by StudioPress', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Headway Theme Framework', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Twenty 12, 13, 14 &#38; 15 by WordPress.', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Many others, add a listing and see how it looks.', 'easy-property-listings'  );?></li>
						</ul>
						<p><?php _e( 'We have a selection of pre configured templates here for many popular themes', 'easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/article/185-theme-templates"><?php _e( 'here', 'easy-property-listings'  );?></a>.</p>
					</div>
				</div>
			</div>

			<div class="changelog headline-feature">
				<h2 id="theme-instructions" class="epl-welcome-sub-heading"><?php _e( 'Advanced instructions', 'easy-property-listings'  );?></h2>

				<div class="feature-section two-col">
					<?php $theme_instructions = '<a href="http://codex.easypropertylistings.com.au/article/49-theme-setup">'. __( 'theme setup instructions can be found here' , 'easy-property-listings'  ) .'</a>'; ?>
					<?php $custom_template_link = '<a href="http://codex.easypropertylistings.com.au/article/121-how-to-create-your-own-custom-theme-templates-or-modify-existing-ones-using-the-theme-template-loader">'. __( 'custom templates' , 'easy-property-listings'  ) .'</a>'; ?>
					<h4 style="text-align: center;"><?php printf( __( 'Detailed %s.', 'easy-property-listings'  ) , $theme_instructions );?></h4>
					<h4 style="text-align: center;"><?php printf( __( 'How to create your own %s.', 'easy-property-listings'  ) , $custom_template_link );?></h4>
				</div>
			</div>

			<div class="changelog headline-feature">

				<h2 class="about-description" style="text-align: center;"><?php _e( 'Stuck getting your theme to work?', 'easy-property-listings'  );?></h2>
				<h3 class="about-description" style="text-align: center;"><?php _e( 'Not all themes follow modern WordPress coding standards and these may take a little more time and experience to get working. If you just can not get it to work, visit', 'easy-property-listings'  );?> <a href="https://easypropertylistings.com.au/support-ticket/"><?php _e( 'premium support', 'easy-property-listings'  );?></a> <?php _e( 'and fill out a theme support request.', 'easy-property-listings'  );?></h3>

				<p style="text-align:center;"><?php _e( 'If the theme is available in the WordPress.org theme directory let us know the theme name and URL where we can download it in your support ticket. If its a premium theme or are in a hurry submit a priority support ticket along with the theme in a zip or a download link to it on a file sharing site like Dropbox.', 'easy-property-listings'  );?></p>

				<h2 id="guide-help"><?php _e( 'Need Help?', 'easy-property-listings'  );?></h2>

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Premium Support','easy-property-listings'  );?></h4>
						<p><?php echo $link = sprintf( __( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="%s">support forums</a>.', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) );?></p>
					</div>

					<div>
						<h4><?php _e( 'Need Even Faster Support?', 'easy-property-listings'  );?></h4>
						<p><?php _e( '<a href="https://easypropertylistings.com.au/support-ticket/">Priority Support forums</a> are there for customers that need faster and/or more in-depth assistance.', 'easy-property-listings'  );?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Documentation and Short Codes','easy-property-listings'  );?></h4>
						<p><?php _e( 'Read the','easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/"><?php _e( 'documentation','easy-property-listings'  );?></a> <?php _e( ' and instructions on how to use the included','easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/category/25-shortcodes"><?php _e( 'shortcodes','easy-property-listings'  );?></a>.</p>
					</div>
				</div>
			</div>

			<div class="changelog headline-feature">

				<div class="feature-section col two-col">
					<div>
						<h3><?php _e( 'Stay Up to Date', 'easy-property-listings'  );?></h3>
						<h4><?php _e( 'Get Notified of Extension Releases','easy-property-listings'  );?></h4>
						<p><?php _e( 'New extensions that make Easy Property Listings even more powerful are released nearly every single week. Subscribe to the newsletter to stay up to date with our latest releases. <a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to ensure you do not miss a release!', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Get Alerted About New Tutorials', 'easy-property-listings'  );?></h4>
						<p><?php _e( '<a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to hear about the latest tutorial releases that explain how to take Easy Property Listings further.', 'easy-property-listings'  );?></p>
					</div>

					<?php $sl_link = '<a href="https://easypropertylistings.com.au/extensions/sliders/">' . __('Sliders' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $br_link = '<a href="https://easypropertylistings.com.au/extensions/brochures/">' . __('Brochures' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $am_link = '<a href="https://easypropertylistings.com.au/extensions/advanced-mapping/">' . __('Advanced Mapping' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $tm_link = '<a href="https://easypropertylistings.com.au/extensions/testimonial-manager/">' . __('Testimonial Manager' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $sd_link = '<a href="https://easypropertylistings.com.au/extensions/staff-directory/">' . __('Agent/Staff Directory' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $lp_link = '<a href="https://easypropertylistings.com.au/extensions/location-profiles/">' . __('Location Profiles' , 'easy-property-listings' ) . '</a>'; ?>
					<?php $store = '<a href="https://easypropertylistings.com.au/extensions/">' . __('Add-On Store' , 'easy-property-listings' ) . '</a>'; ?>
					<div class="last-feature">
						<h3><?php _e( 'Extend With Extensions', 'easy-property-listings'  );?></h3>
						<h4><?php _e( '18 Extensions and many more coming','easy-property-listings'  );?></h4>
						<p><?php printf ( __( 'Add-on plug ins are available that greatly extend the default functionality of Easy Property Listings. There are extensions for %s, %s, %s, %s, %s, %s, and many, many more. Visit the %s to further enhance your real estate website.', 'easy-property-listings'  ) , $sl_link , $br_link , $am_link , $tm_link , $sd_link , $lp_link , $store);?></p>

						<h4><?php _e( 'Visit the Extension Store', 'easy-property-listings'  );?></h4>
						<p><a href="https://easypropertylistings.com.au/extensions/" target="_blank"><?php _e( 'The Extensions store' , 'easy-property-listings'  );?></a> <?php _e( 'has a list of all available extensions to make your real estate website even better.', 'easy-property-listings'  );?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Credits Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function credits_screen() {
		list( $display_version ) = explode( '-', EPL_PROPERTY_VER );
		?>
		<div class="wrap about-wrap epl-about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'easy-property-listings'  ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'easy-property-listings'  ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'easy-property-listings'  ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Easy Property Listings is created by a worldwide team of developers who aim to provide the #1 property listings platform for managing your real estate business through WordPress.', 'easy-property-listings'  ); ?></p>

			<?php echo $this->epl_contributors(); ?>
		</div>
		<?php
	}


	/**
	 * Render Contributors List
	 *
	 * @since 1.0
	 * @uses EPL_Welcome::epl_get_contributors()
	 * @return string $contributor_list HTML formatted list of all the contributors for EPL
	 */
	public function epl_contributors() {
		$contributors = $this->get_epl_contributors();

		if ( empty( $contributors ) )
			return '';

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s" title="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'easy-property-listings'  ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retreive list of contributors from GitHub.
	 *
	 * @access public
	 * @since 1.0
	 * @return array $contributors List of contributors
	 */
	public function get_epl_contributors() {
		$contributors = get_transient( 'epl_contributors' );

		if ( false !== $contributors )
			return $contributors;

		$response = wp_remote_get( 'https://api.github.com/repos/easypropertylistings/Easy-Property-Listings/contributors', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return array();

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) )
			return array();

		set_transient( 'epl_contributors', $contributors, 3600 );

		return $contributors;
	}

	/**
	 * Sends user to the Welcome page on first activation of EPL as well as each
	 * time EPL is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function epl_welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_epl_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_epl_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$upgrade = get_option( 'epl_version_upgraded_from' );

		if( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=epl-getting-started' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=epl-about' ) ); exit;
		}
	}
}
new EPL_Welcome();
