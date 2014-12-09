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
				<h3><?php _e( 'Listing Management Pages', 'epl' );?></h3>
				<div class="feature-section">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-listing-admin-list.png'; ?>" class="epl-welcome-screenshots"/>
					
					<h4><?php _e( 'Graph API','epl' );?></h4>
					<p><?php _e( 'Visual graph indicating listing prices so you can at a glance visually see what price your listings. Customise the max price to suit your price range from the Settings page. Default max for for sale listings is 2,000,000 and rental listing 2,000.', 'epl' );?></p>
					
					<h4><?php _e( 'Listing Details','epl' );?></h4>
					<p><?php _e( 'Get quick access to important details like what the property has and the scheduled inspection times.', 'epl' );?></p>
					
					<h4><?php _e( 'Auction Listings','epl' );?></h4>
					<p><?php _e( 'Keep track of your coming Auction dates from the Price column.', 'epl' );?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Widgets', 'epl' );?></h3>
				<div class="feature-section">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-widget-listing.png'; ?>" class="epl-welcome-screenshots"/>
					<h4><?php _e( 'EPL - Listing','epl' );?></h4>
					<p><?php _e( 'Added additional options giving you greater control to customise your "featured" listings using a widget. Everything is now customisable even the button label.', 'epl' );?></p>
					
					<h4><?php _e( 'EPL - Listing Search','epl' );?></h4>
					<p><?php _e( 'Tabbed search supports multiple listing types from one widget with customisable labels. Also added are land and building size search fields. Performance improved by reducing the number of database queries to keep your site fast.', 'epl' );?></p>
				
				</div>
			</div>
		
			<div class="changelog">
				<h3><?php _e( 'Dashboard Widget', 'epl' );?></h3>
				<div class="feature-section">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-dashboard-widget.png'; ?>" class="epl-welcome-screenshots"/>
					<h4><?php _e( 'Dashboard Widget','epl' );?></h4>
					<p><?php _e( 'Dashboard widget now lists all listings and status so at a glance you can see what you have.', 'epl' );?></p>
					
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Other Changes', 'epl' );?></h3>
				<div class="feature-section">
					
					<h4><?php _e( 'Inspection times','epl' );?></h4>
					<p><?php _e( 'Inspection time support multiple dates written one per line.', 'epl' );?></p>
					
					<h4><?php _e( 'New Label','epl' );?></h4>
					<p><?php _e( 'Set how many days you want your newly added listings to appear new for. Adjust the number of days from the settings.', 'epl' );?></p>
					
					<h4><?php _e( 'Auction Date','epl' );?></h4>
					<p><?php _e( 'Calendar to select auction date and outputs nicely.', 'epl' );?></p>
					
					<h4><?php _e( 'Sale Date','epl' );?></h4>
					<p><?php _e( 'Calendar to select sold date.', 'epl' );?></p>
					
					<h4><?php _e( 'Grid & List View','epl' );?></h4>
					<p><?php _e( 'Display Settings.', 'epl' );?></p>
					
					<h4><?php _e( 'Date and Price Sorter','epl' );?></h4>
					<p><?php _e( 'Display Settings.', 'epl' );?></p>
					
					<h4><?php _e( 'Easier to create custom templates','epl' );?></h4>
					<p><?php _e( 'Display Settings.', 'epl' );?></p>
					
					<h4><?php _e( 'Author Box','epl' );?></h4>
					<p><?php _e( 'Supports multiple real estate agents per listing.', 'epl' );?></p>
					
					<h4><?php _e( 'Meta Fields','epl' );?></h4>
					<p><?php _e( 'Property unit/lot number, office id, .', 'epl' );?></p>
					
					<h4><?php _e( 'Additional hooks and filters for customising','epl' );?></h4>
					<p><?php _e( 'Property unit/lot number, office id, .', 'epl' );?></p>

				</div>
			</div>
			
			<div class="changelog">
				<div class="feature-section">
					<h4><?php _e( 'Full change log of version 1.3', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Extension validator.', 'epl' );?></li>
						<li><?php _e( 'New: Moved listing-meta.php into compatibility folder.', 'epl' );?></li>
						<li><?php _e( 'New: Global $property variable.', 'epl' );?></li>
						<li><?php _e( 'New: Property custom meta re-written into class. This was the big change to 1.3 where we completely re-wrote the output of the meta values which are now accessible using global $property variable.', 'epl' );?></li>
						<li><?php _e( 'New: Property meta can now can be output using new actions for easy and quick custom template creation.', 'epl' );?></li>
						<li><?php _e( 'New: API for extensions now support WordPress editor with validation.', 'epl' );?></li>
						<li><?php _e( 'New: jQuery date time picker formatting added to improve support for auction and sold listing, support for 30+ languages support.', 'epl' );?></li>
						<li><?php _e( 'New: Inspection time auto-formats REAXML date eg "13-Dec-2014 11:00am to 11:45am" and will no longer show past inspection times.', 'epl' );?></li>
						<li><?php _e( 'New: Inspection time support multiple dates written one per line.', 'epl' );?></li>
						<li><?php _e( 'New: CSS improved with better commenting and size reduction.', 'epl' );?></li>
						<li><?php _e( 'New: Dashboard widget now lists all listing status so at a glance you can see your property stock.', 'epl' );?></li>
						<li><?php _e( 'New: Display: To enable grid, list and sorter your custom archive-listing.php template requires the new action hook "epl_template_before_property_loop" before the WordPress loop.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Utility hook action hook added "epl_template_after_property_loop" for future updates.', 'epl' );?></li>
						<li><?php _e( 'New: Display: List and grid view with optional masonry effect.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Sorter added for price high/low and date newest/oldest.', 'epl' );?></li>
						<li><?php _e( 'New: Auction Date formats nicely. EG "Auction Saturday 28th December at 2:00pm".', 'epl' );?></li>
						<li><?php _e( 'New: Tabbed extensions page support in admin for advanced extensions like "Listing Alerts".', 'epl' );?></li>
						<li><?php _e( 'New: Multiple author support in Author Box.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget now supports multiple listing types, hold Ctrl to enable tabbed front end display.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget Labels are configurable from the Display settings allowing you to set for example: "Property" to "Buy" and "Rental" to "Rent" and use a single widget to search multiple types.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget and short code supports search by property ID, Land Area and Building Area.', 'epl' );?></li>
						<li><?php _e( 'New: Search short code supports array of property types.', 'epl' );?></li>
						<li><?php _e( 'New: Author variables accessible using new CLASS.', 'epl' );?></li>
						<li><?php _e( 'New: REAXML Unit and lot formatting function for usage in the title when using WP All Import Pro. Usage [epl_feedsync_filter_sub_number({address[1]/subNumber[1]})].', 'epl' );?></li>
						<li><?php _e( 'New: Global $epl_settings settings variable adds new default values on plug-in update.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Added customisable label for rental Bond/Deposit.', 'epl' );?></li>
						<li><?php _e( 'New: Template functions completely re-written and can now be output using hooks.', 'epl' );?></li>
						<li><?php _e( 'New: Added NEW sticker with customisable label and ability to set how long a listing displays the new label.', 'epl' );?></li>
						<li><?php _e( 'New: Display: jQuery Masonry effect can be enabled from display settings.', 'epl' );?></li>
						<li><?php _e( 'New: Bar Graph API added.', 'epl' );?></li>
						<li><?php _e( 'New: Graph in admin allows you to set the max bar graph value. Default are (2,000,000 sale) and (2,000 rental).', 'epl' );?></li>
						<li><?php _e( 'New: Graph visually displays price and status.', 'epl' );?></li>
						<li><?php _e( 'New: Price graph now appears in admin pages quickly highlighting price and status visually.', 'epl' );?></li>
						<li><?php _e( 'New: Meta Fields: Support for unit number, lot number (land).', 'epl' );?></li>
						<li><?php _e( 'New: South African ZAR currency support.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 1.2.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Search Widget not working on page 2 of archive page in some instances', 'epl' );?></li>
						<li><?php _e( 'Fix: Property feature list Toilet and New Construction now display in list when ticked', 'epl' );?></li>
						<li><?php _e( 'Fix: EPL - Listing widget was not displaying featured listings', 'epl' );?></li>
						<li><?php _e( 'Fix: Allowed to filter by commercial_listing_type in [listing_category] shortcode', 'epl' );?></li>
						<li><?php _e( 'Fix: Updated templates to display Search Results when performing search', 'epl' );?></li>
						<li><?php _e( 'Fix: No longer show Bond when viewing rental list in admin', 'epl' );?></li>
						<li><?php _e( 'Fix: Open for inspection sticker now appears on rental properties', 'epl' );?></li>
						<li><?php _e( 'New: Added initial Dutch translation.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 1.2', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Plug in Activation process flushes permalinks', 'epl' );?></li>
						<li><?php _e( 'New: Plug in deactivation flushes permalinks', 'epl' );?></li>
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
						<li><?php _e( 'Tweak: Admin CSS tweaks to define sections in admin', 'epl' );?></li>
						<li><?php _e( 'Fix: CSS for TwentyThirteen style CSS using .sidebar container', 'epl' );?></li>
						<li><?php _e( 'Fix: CSS for responsive shortcode', 'epl' );?></li>
						<li><?php _e( 'New: Added options to hide/ show various options to EPL - Listing widget: Property Headline, Excerpt, Suburb/Location Label, Street Address, Price, Read More Button', 'epl' );?></li>
						<li><?php _e( 'New: Added customisable "Read More" label to EPL - Listing widget', 'epl' );?></li>
						<li><?php _e( 'New: Added excerpt to EPL - Listing widget', 'epl' );?></li>
						<li><?php _e( 'New: Added options to remove search options from EPL - Listing Search widget', 'epl' );?></li>
						<li><?php _e( 'New: Added consistent CSS classes to shortcodes for responsive shortcode', 'epl' );?></li>
						<li><?php _e( 'New: Date processing function for use with WP All Import when importing REAXML files. Some imports set the current date instead of the date from the REAXML file. Usage in WP All Import Post Date is: [epl_feedsync_format_date({./@modTime})]', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added additional CSS classes to admin menu pages to extensions can be better distinguished when installed and activated', 'epl' );?></li>
						<li><?php _e( 'Fix: Registering custom template actions now works correctly', 'epl' );?></li>
						<li><?php _e( 'New: Added additional CSS classes to template files', 'epl' );?></li>
						<li><?php _e( 'Fix: Changed property not found wording when using search widget and listing not found. ', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added defaults to widgets to prevent errors when debug is on', 'epl' );?></li>
						<li><?php _e( 'New: Added WordPress editor support in admin for use with extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Added textarea support in admin for use with extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Filters added for all select options on add listing pages which allows for full customisation through simple function', 'epl' );?></li>
						<li><?php _e( 'New: Added rent period, Day, Daily, Month, Monthly to rental listing types', 'epl' );?></li>
						<li><?php _e( 'New: Added property_office_id meta field', 'epl' );?></li>
						<li><?php _e( 'New: Added property_address_country meta field', 'epl' );?></li>
						<li><?php _e( 'Tweak: Allowed for decimal in bathrooms to allow for 1/2 baths eg 1.5', 'epl' );?></li>
						<li><?php _e( 'New: Added mini map to listing edit screen. Will display mini map in address block when pressing green coordinates button.', 'epl' );?></li>
						<li><?php _e( 'Fix: Updated admin columns for commercial_land listing type to match other listing type', 'epl' );?></li>
						<li><?php _e( 'Fix: Swapped bedrooms/bathroom label on hover', 'epl' );?></li>
						<li><?php _e( 'New: Added filter epl_listing_meta_boxes which allows additional meta boxes to be added through filter', 'epl' );?></li>
					</ul>
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
				<h3><?php _e( 'Quick Start Guide', 'epl' );?></h3>
				<div class="feature-section">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-general-settings.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Activate the Listing Types You Need', 'epl' );?></h4>
					<p><?php _e( 'Instead of classifying everything as a property, Easy Property Listings allows you to separate the different listing types which is better for SEO and RSS feeds.', 'epl' );?></p>
					<p><?php _e( 'Visit the general settings page and enable the listing types you need. Once you have pressed save visit the Permalinks page to re-fresh your sites permalinks.', 'epl' );?></p>
					
					<p><strong><?php _e( 'Supported Listing Types', 'epl'); ?></strong></p>
					<ul>
						<li><?php _e( 'Property (Residential)', 'epl'); ?></li>
						<li><?php _e( 'Rental', 'epl'); ?></li>
						<li><?php _e( 'Land', 'epl'); ?></li>
						<li><?php _e( 'Rural', 'epl'); ?></li>
						<li><?php _e( 'Commercial', 'epl'); ?></li>
						<li><?php _e( 'Commercial Land', 'epl'); ?></li>
						<li><?php _e( 'Business', 'epl'); ?></li>
					</ul>
					
					<h4><?php _e( 'Create a page for each activated listing type', 'epl' );?></h4>
					<p><?php _e( 'Next add a new page for each listing type you activated.', 'epl' );?></p>
					<p><?php _e( 'For example, lets say you have activated: Property, Rental and Land. Create 3 pages, one called "Property", another "Land" and the third "Rental" these will be the custom post type slugs eg: property, rental and land. Once created then they can be renamed but leave the page slugs as property, rental and land.', 'epl' );?></p>
					<p><?php _e( 'Publish a test "Property" and visit your new Property page and you will see the new property and others you have created.', 'epl' );?></p>
					<p><?php _e( 'Doing this allows you to add "Property", "Land" and "Rental" pages to your WordPress menu and you can rename them to whatever you like eg: "For Sale", "For Rent" etc.', 'epl' );?></p>
				</div>
				
				<h3><?php _e( 'Add Your First Listing', 'epl' );?></h3>
				<p><?php _e( 'Once you have activated your listing types you can now add a listing. Enter the listing details and press publish.', 'epl' );?></p>
					
				<div class="feature-section">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-listing-admin.png'; ?>" class="epl-welcome-screenshots"/>

					<h4><?php _e( 'Title', 'epl' );?></h4>
					<p><?php _e( 'Best practice is to Using the listing address as the title is better than a heading that frequently changes, add your text heading "Great Property" to the Heading field in the Listing Details box. Using the address as that does not change and offers SEO and link sharing advantages.', 'epl' );?></p>
					
					<h4><?php _e( 'Listing Details', 'epl' );?></h4>
					<ul>
						<li><strong><?php _e( 'Heading', 'epl' );?></strong> <?php _e( 'is where you can place your fancy heading "Best View Of Sydney Harbour".', 'epl' );?></li>
						<li><strong><?php _e( 'Second Listing Agent', 'epl' );?></strong> <?php _e( 'If you have two real estate agents marketing a listing, enter their WordPress user name here. The primary agent is the Author like posts.', 'epl' );?></li>
						<li><strong><?php _e( 'Inspection Times', 'epl' );?></strong> <?php _e( 'now supports multiple inspection times, add one per line. When importing from REAXML past inspection dates will not display.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Gallery', 'epl' );?></h4>
					<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'epl' ); ?></p>
					<p><?php _e( 'You can automatically output a gallery from the Display options page. When set to automatic, upload your images to the listing and press x to close the media upload box.', 'epl' );?></p>
					<p><?php _e( 'When using the automatic gallery you can adjust the number of image columns from the Display options.', 'epl' );?></p>
					
					<h4><?php _e( 'Gallery Light Box', 'epl' );?></h4>
					<p><?php _e( 'The gallery can be configured to use a light box plug-in like Easy FancyBox. Just install that plugin and activate and your gallery images will use the light box effect.', 'epl' );?></p>

					<h4><?php _e( 'Listing Features & Additional Features', 'epl' );?></h4>
					<p><?php _e( 'Enter your listing details like bedrooms, bathrooms, land size etc. Tick the additional features that apply to your listing. For even more features you can add as many as you like using the Features taxonomy box. It works just like WordPress post tags.', 'epl' );?></p>
					
					<h4><?php _e( 'Author', 'epl' );?></h4>
					<p><?php _e( 'Set the author to show the name of the agent who has listed the property with their contact details. See Author Box below for instructions on how to modify the individual user profiles.', 'epl' );?></p>
					
					<h4><?php _e( 'Files and Links', 'epl' );?></h4>
					<p><?php _e( 'Add your floor plan, listing YouTube video or mini property website links.', 'epl' );?></p>

					<?php echo wp_oembed_get('https://www.youtube.com/watch?v=h6B8LLecfbw', array('width'=>500)); ?>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'epl' );?></h3>
				<div class="feature-section">
					<h4><?php _e( 'Phenomenal Support','epl' );?></h4>
					<p><?php _e( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="http://easypropertylistings.com.au/support/">support forums</a>.', 'epl' );?></p>
					
					<h4><?php _e( 'Need Even Faster Support?', 'epl' );?></h4>
					<p><?php _e( 'Visit the <a href="http://easypropertylistings.com.au/support/pricing/">Priority Support forums</a> are there for customers that need faster and/or more in-depth assistance.', 'epl' );?></p>
					
					<h4><?php _e( 'Documentation and Short Codes','epl' );?></h4>
					<p><?php _e( 'Read the','epl' );?> <a href="http://easypropertylistings.com.au/documentation/"><?php _e( 'documentation','epl' );?></a> <?php _e( ' and instructions on how to use the included','epl' );?> <a href="http://easypropertylistings.com.au/section/short-codes/"><?php _e( 'shortcodes','epl' );?></a>.</p>

					<p>
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
					<h4><?php _e( '12 Extensions and many more coming','epl' );?></h4>
					<p><?php _e( 'Add-on plug ins are available that greatly extend the default functionality of Easy Property Listings. There are extensions for Advanced mapping, testimonials, listing alerts, CMA Market Reports, Location Profiles, and many, many more.', 'epl' );?></p>
					
					<h4><?php _e( 'Visit the Extension Store', 'epl' );?></h4>
					<p><a href="http://easypropertylistings.com.au/extensions/" target="_blank"><?php _e( 'The Extensions store' , 'epl' );?></a> <?php _e( 'has a list of all available extensions, including convenient category filters so you can find exactly what you are looking for.', 'epl' );?></p>
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