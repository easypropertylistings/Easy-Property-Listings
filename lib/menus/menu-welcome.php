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
	public $minimum_capability = 'manage_options';

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
			__( 'Welcome to Easy Property Listings', 'epl' ),
			__( 'Welcome to Easy Property Listings', 'epl' ),
			$this->minimum_capability,
			'epl-about',
			array( $this, 'about_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with Easy Property Listings', 'epl' ),
			__( 'Getting started with Easy Property Listings', 'epl' ),
			$this->minimum_capability,
			'epl-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Credits Page
		add_dashboard_page(
			__( 'The people that build Easy Property Listings', 'epl' ),
			__( 'The people that build Easy Property Listings', 'epl' ),
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

		.about-wrap .epl-badge {
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
				<?php _e( "What's New", 'epl' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'epl-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'epl' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'epl-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'epl' ); ?>
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
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'epl' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'epl' ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'epl' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>
			
			<div class="changelog">
				<h3><?php _e( 'Version 1.2', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Filter By Specific Listing Meta Key and Value', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Plugin Activation process flushes permalinks', 'epl' );?></li>
						<li><?php _e( 'New: Plugin deactivation flushes permalinks', 'epl' );?></li>
						<li><?php _e( 'New: Shortcode [listing_search]', 'epl' );?></li>
						<li><?php _e( 'New: Shortcode [listing_feature]', 'epl' );?></li>
						<li><?php _e( 'New: Shortcode [listing_open] replaces [home_open] shortcode. Retained [home_open] for backward compatability, however adjust your site. ', 'epl' );?></li>
						<li><?php _e( 'New: Listing shortcodes allow for default template display if registered by adding template="slim" to the shortcode.', 'epl' );?></li>
						<li><?php _e( 'New: Translation support now correctly loads text domain epl', 'epl' );?></li>
						<li><?php _e( 'New: Added translation tags to all test elements for better translation support', 'epl' );?></li>
						<li><?php _e( 'New: Updated source epl.pot translation file for translations', 'epl' );?></li>
						<li><?php _e( 'New: Added very rough Italian translation', 'epl' );?></li>
						<li><?php _e( 'New: Wrapped Featured image in action to allow for easy removal and/or replacement', 'epl' );?></li>
						<li><?php _e( 'Fix: Undefined errors when debug is active', 'epl' );?></li>
						<li><?php _e( 'New: Added new CSS classes to widgets for consistent usage', 'epl' );?></li>
						<li><?php _e( 'tweak: Admin CSS tweaks to define sections in admin', 'epl' );?></li>
						<li><?php _e( 'Fix: CSS for TwentyThirteen style CSS using .sidebar container', 'epl' );?></li>
						<li><?php _e( 'Fix: CSS for responsive shortcode', 'epl' );?></li>
						<li><?php _e( 'New: Added options to hide/ show various options to EPL - Listing widget: Property Headline, Excerpt, Suburb/Location Label, Street Address, Price, Read More Button', 'epl' );?></li>
						<li><?php _e( 'New: Added customisable "Read More" label to EPL - Listing widget', 'epl' );?></li>
						<li><?php _e( 'New: Added excerpt to EPL - Listing widget', 'epl' );?></li>
						<li><?php _e( 'New: Added options to remove search options from EPL - Listing Search widget', 'epl' );?></li>
						<li><?php _e( 'New: Added consistent CSS classes to shortcodes for responsive shortcode', 'epl' );?></li>
						<li><?php _e( 'New: Date processing function for use with WP All Import when importing REAXML files. Some imports set the current date instead of the date from the REAXML file. Usage in WP All Import Post Date is: [epl_feedsync_format_date({./@modTime})]', 'epl' );?></li>
						<li><?php _e( 'tweak: Added additional CSS classes to admin menu pages to extensions can be better distinguished when installed and activated', 'epl' );?></li>
						<li><?php _e( 'Fix: Registering custom template actions now works correctly', 'epl' );?></li>
						<li><?php _e( 'New: Added additional CSS classes to template files', 'epl' );?></li>
						<li><?php _e( 'Fix: Changed property not found wording when using search widget and listing not found. ', 'epl' );?></li>
						<li><?php _e( 'tweak: Added defaults to widgets to prevent errors when debug is on', 'epl' );?></li>
						<li><?php _e( 'New: Added WordPress editor support in admin for use with extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Added textarea support in admin for use with extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Filters added for all select options on add listing pages which allows for full customisation through simple function', 'epl' );?></li>
						<li><?php _e( 'New: Added rent period, Day, Daily, Month, Monthly to rental listing types', 'epl' );?></li>
						<li><?php _e( 'New: Added property_office_id meta field', 'epl' );?></li>
						<li><?php _e( 'New: Added property_address_country meta field', 'epl' );?></li>
						<li><?php _e( 'tweak: Allowed for decimal in bathrooms to allow for 1/2 baths eg 1.5', 'epl' );?></li>
						<li><?php _e( 'New: Added mini map to listing edit screen. Will display mini map in address block when pressing green coordinates button.', 'epl' );?></li>
						<li><?php _e( 'Fix: Updated admin columns for commercial_land listing type to match other listing type', 'epl' );?></li>
						<li><?php _e( 'Fix: Swapped bedrooms/bathroom label on hover', 'epl' );?></li>
						<li><?php _e( 'New: Added filter epl_listing_meta_boxes which allows additional meta boxes to be added through filter', 'epl' );?></li>
					</ul>
				</div>
			</div>
			

			<div class="changelog">
				<h3><?php _e( 'Shortcode: [listing_category]', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Filter By Specific Listing Meta Key and Value', 'epl' );?></h4>
					<p><?php _e( 'This shortcode allows for you to output a list of listings by type and filter them by any available meta key and value. So you can display a list of </em>property</em> that are <em>House</em>, or perhaps by <em>4</em> bedrooms, all possible with this shortcode.', 'epl' );?></p>
					<p><?php _e( 'The category_value accepts multiple values separated by commas. category_value="2,3,4" or category_value="House,Unit"', 'epl' );?></p>
					<p><?php _e( '<strong>Example:</strong>', 'epl' );?></p>
					<p><?php _e( '[listing_category post_type="property" category_key="property_category" category_value="House"]', 'epl' );?></p>
					<p><?php _e( '<strong>Variables Available for this shortcode:</strong>', 'epl' );?></p>
					<p><?php _e( '<ul><li>post_type="property" <em>// Single Listing Post Type</em></li>
									<li>category_key="property_category" <em>// Available Listing Meta Key</em></li>
									<li>category_value="House" <em>// Comma separated values</em></li>
									<li>status="current,sold,leased" <em>// Comma separated status</em></li>
									<li>limit="5" <em>// Single Number eg -1 to 99</em></li></ul>', 'epl' );?></p>
					<p><?php _e( '<a href="http://easypropertylistings.com.au/docs/listing-category-short-code/">Additional documentation and usage instructions</a>', 'epl' );?></p>
				</div>
			</div>
			
			<div class="changelog">
				<h3><?php _e( 'Customization Filters and Constants to Support Real Estate in any Country', 'epl' );?></h3>

				<div class="feature-section">
					<h4><?php _e( 'Listing Category Filters', 'epl' );?></h4>
					<p><?php _e( 'Each Listing type has a category filter that allows you to replace the default categories with your own in your functions.php or mini plugin.', 'epl' );?></p>
					
					<p><?php _e( '<h4>Filters Added</h4>
					<ul><li>epl_listing_meta_property_category <em>// Shared with rental listing type</em></li>
					<li>epl_listing_meta_commercial_category</li>
					<li>epl_listing_meta_rural_category</li>
					<li>epl_listing_meta_land_category</li></ul>', 'epl' );?></p>
					<p><?php _e( '<a href="http://easypropertylistings.com.au/docs/modify-listing-categories-filters/">Additional instructions</a>', 'epl' );?></p>
				</div>
				
				<div class="feature-section">
					<h4><?php _e( 'Constant to change Listing Post Types Slug', 'epl' );?></h4>
					<p><?php _e( 'This constant allows you to change the post slug of each of the different listing types. Be default the slug for the <em>Property</em> Listing type is <em>property</em>, resulting is URLs that look like: http://yoursite.com/property/property-name, by using this constant, you can define the slug as anything you want.', 'epl' );?></p>
					
					<p><?php _e( '<h4>Post Slug Constants Added</h4>
					<ul><li>EPL_PROPERTY_SLUG</li>
					<li>EPL_RENTAL_SLUG</li>
					<li>EPL_RURAL_SLUG</li>
					<li>EPL_LAND_SLUG</li>
					<li>EPL_BUSINESS_SLUG</li>
					<li>EPL_COMMERCIAL_SLUG</li>
					<li>EPL_COMMERCIAL_LAND_SLUG</li></ul>', 'epl' );?></p>
					<p><?php _e( '<a href="http://easypropertylistings.com.au/docs/change-slug-listing-post-types/">Additional instructions</a>', 'epl' );?></p>
				</div>
				
				<div class="feature-section">
					<h4><?php _e( 'Settings Not Updating when Saved', 'epl' );?></h4>
					<p><?php _e( 'When saving the initial settings the page is refreshed but the old settings were still being shown. This has been fixed.', 'epl' );?></p>
					<h4><?php _e( 'Tweak: Search Widget', 'epl' );?></h4>
					<p><?php _e( 'Updated search widget to use custom filtered property_category.', 'epl' );?></p>
					<h4><?php _e( 'Fix: Single Listing for property category', 'epl' );?></h4>
					<p><?php _e( 'Listing categories were showing key, now showing value', 'epl' );?></p>
				</div>
				
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-general' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to Easy Property Listings Settings', 'epl' ); ?></a>
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
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'epl' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'epl' ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'epl' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Use the tips below to get started using Easy Property Listings. You will be up and running in no time!', 'epl' ); ?></p>

			<div class="changelog">
				<h3><?php _e( 'Configure the Property Types You Need', 'epl' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/general-settings.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Property Types', 'epl' );?></h4>
					<p><?php _e( 'Instead of classifying everything as a property, Easy Property Listings allows you to separate the different property types which is better for SEO and RSS feeds.', 'epl' );?></p>
					<p><?php _e( 'Visit the general settings page and enable the listing types you need. Once you have pressed save visit the Permalinks page to re-fresh your sites permalinks.', 'epl' );?></p>
					
					
					
					<p><?php _e( '<strong>Supported Listing Types</strong>
					<ul>
						<li>Property (Residential)</li>
						<li>Rental</li>
						<li>Land</li>
						<li>Rural</li>
						<li>Commercial</li>
						<li>Commercial Land</li>
						<li>Business</li>
					</ul>', 'epl' );?></p>
				</div>
				
				
				<h3><?php _e( 'Add Your First Listing', 'epl' );?></h3>

				<div class="feature-section">
				
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/listing-admin.png'; ?>" class="epl-welcome-screenshots"/>

					<p><?php _e( 'Once you have re-set the WordPress permalinks you can now add a listing to the post type you have enabled. Enter the listing details and press publish.', 'epl' );?></p>
					
					<p><?php _e( 'You now can view the listing using the default template included with Easy Property Listings. Once you have added a listing and previewed the single view of the page you can review the archive page. Each post type created has a slug associated with that, so just by adding /property or /rental to your browser address you should see any listings that you have saved.', 'epl' );?></p>
					
					<h4><?php _e( 'Listing Title', 'epl' );?></h4>
					<p><?php _e( 'Note that the Title can either be the property address or the text heading. For an SEO advantage we suggest to use the property address, as this also becomes the website page address. Also, using the address will keep you from breaking the permalinks if you need to change the text heading.', 'epl' );?></p>
					
					<h4><?php _e( 'Gallery', 'epl' );?></h4>
					<p><?php _e( 'To add a gallery of images to your listings use the standard WordPress Add Media button. If you have enabled the automatic gallery display option then you do not need to create a gallery just upload the images without pressing <strong>insert into post</strong>.', 'epl' );?></p>
					
					<p><?php _e( 'When your listing has more than one image attached they will be displayed with the number of columns you have specified in the display options. When Automatic gallery is off you can use the EPL - Property Gallery widget to display your images or manually insert them into the content area.', 'epl' );?></p>
					
					<h4><?php _e( 'Gallery Lightbox', 'epl' );?></h4>
					<p><?php _e( 'We have not included a lightbox gallery pop-over as there are some excellent lighbox plugins available. Install and activate one like the free Easy Fancy Box.', 'epl' );?></p>

					<h4><?php _e( 'Features', 'epl' );?></h4>
					<p><?php _e( 'Fill in the house and land features to display the standard house features. For additional features simply add them to the features box.', 'epl' );?></p>
					
					<h4><?php _e( 'Author', 'epl' );?></h4>
					<p><?php _e( 'Set the author to show the name of the agent who has listed the property with their contact details. See Author Box below for instructions on how to modify the individual user profiles.', 'epl' );?></p>
					
					<h4><?php _e( 'Files and Links', 'epl' );?></h4>
					<p><?php _e( 'Add your floorplan, property video or mini property website links to add a button with a link to these pages.', 'epl' );?></p>

				</div>
			</div>
		
			<div class="changelog">
				<h3><?php _e( 'Short Codes', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Open Home List','epl' );?></h4>
					<p><?php _e( 'Easily add a short code to a page or post and it will always only show properties that have a current home open so your posts and pages will always be up-to date', 'epl' );?></p>
					<p><?php _e( 'The short code accepts a few optional parameters:', 'epl' );?></p>
					<p><?php _e( '<ul>
									<li><em>post_type</em> - the listing type; property, rental, commercial, business, land, rural</li>
									<li><em>limit</em> - the number of listings to display, default is -1 which means all</li>
								</ul>', 'epl' );?></p>
					<h5><?php _e( 'Example', 'epl' );?></h5>	
					<p><pre>[home_open_list]</pre></p>
					<h5><?php _e( 'Only Show 5 Rental Listings Example', 'epl' );?></h5>
					<p><pre>[home_open_list post_type="rental" limit="5"]</pre></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Template Pricing and Home Open Label Options', 'epl' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/property-pricing.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Property Pricing','epl' );?></h4>
					<p><?php _e( 'Property pricing can be a nightmare with so many options available like: No Price, Under Offer, Fixed Pricing, Ranges, Under Offer, Auctions. We have created a function that handles all the different options for you. You can customise the labels for No Price from the Display Settings.', 'epl' );?></p>

					<h4><?php _e( 'Home Open', 'epl' );?></h4>
					<p><?php _e( 'When a property has an inspection time or date set that information is displayed on the property templates. The "Home Open" label can be customised also from the display settings page.', 'epl' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Widgets', 'epl' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/widget-listing.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Recent Property','epl' );?></h4>
					<p><?php _e( 'Property pricing can be a nightmare with so many options available like: No Price, Under Offer, Fixed Pricing, Ranges, Under Offer, Auctions. We have created a function that handles all the different options for you. You can customise the labels for No Price from the Display Settings.', 'epl' );?></p>

					<h4><?php _e( 'Author Box', 'epl' );?></h4>
					<p><?php _e( 'When a property has an inspection time or date set that information is displayed on the property templates. The "Home Open" label can be customised also from the display settings page.', 'epl' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Author Profiles', 'epl' );?></h3>

				<div class="feature-section">

					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/widget-author.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Author Box', 'epl' );?></h4>
					<p><?php _e( 'When a property has an inspection time or date set that information is displayed on the property templates. The "Home Open" label can be customised also from the display settings page.', 'epl' );?></p>

				</div>
			</div>
			
			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Phenomenal Support','epl' );?></h4>
					<p><?php _e( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="http://easypropertylistings.com.au/support">support forums</a>.', 'epl' );?></p>

					<h4><?php _e( 'Need Even Faster Support?', 'epl' );?></h4>
					<p><?php _e( 'Our <a href="http://easypropertylistings.com.au/support/pricing/">Priority Support forums</a> are there for customers that need faster and/or more in-depth assistance.', 'epl' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Stay Up to Date', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Get Notified of Extension Releases','epl' );?></h4>
					<p><?php _e( 'New extensions that make Easy Property Listings even more powerful are released nearly every single week. Subscribe to the newsletter to stay up to date with our latest releases. <a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to ensure you do not miss a release!', 'epl' );?></p>

					<h4><?php _e( 'Get Alerted About New Tutorials', 'epl' );?></h4>
					<p><?php _e( '<a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to hear about the latest tutorial releases that explain how to take Easy Property Listings further.', 'epl' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Extensions directory is Growing', 'epl' );?></h3>

				<div class="feature-section">

					<h4><?php _e( '10 Extensions and many more coming','epl' );?></h4>
					<p><?php _e( 'Add-on plug ins are available that greatly extend the default functionality of Easy Property Listings. There are extensions for advanced Google mapping, extensions for testimonials, suburb profiles, and many, many more.', 'epl' );?></p>

					<h4><?php _e( 'Visit the Extension Store', 'epl' );?></h4>
					<p><?php _e( '<a href="http://easypropertylistings.com.au/extensions" target="_blank">The Extensions store</a> has a list of all available extensions, including convenient category filters so you can find exactly what you are looking for.', 'epl' );?></p>

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
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'epl' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'epl' ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'epl' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Easy Property Listings is created by a worldwide team of developers who aim to provide the #1 property listings platform for managing your real estate business through WordPress.', 'epl' ); ?></p>

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
				esc_html( sprintf( __( 'View %s', 'epl' ), $contributor->login ) )
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
