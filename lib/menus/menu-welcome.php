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
		<div class="wrap about-wrap epl-about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'epl' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'epl' ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'epl' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>
			

			<?php $lp_link = '<a href="http://easypropertylistings.com.au/extensions/location-profiles/">' . __('Location Profiles' , 'epl') . '</a>'; ?>
			<?php $tm_link = '<a href="http://easypropertylistings.com.au/extensions/testimonial-manager/">' . __('Testimonial Manager' , 'epl') . '</a>'; ?>
			<?php $am_link = '<a href="http://easypropertylistings.com.au/extensions/advanced-mapping/">' . __('Advanced Mapping' , 'epl') . '</a>'; ?>
			<?php $sd_link = '<a href="http://easypropertylistings.com.au/extensions/staff-directory/">' . __('Staff Directory' , 'epl') . '</a>'; ?>
			
			<div class="changelog headline-feature">
			
				<h2><?php _e( 'New API&#39;s' , 'epl' );?></h2>
				
				<div class="feature-section">
					<h4><?php _e( 'Three new API&#39;s have been added which will make it easier to add custom post types, custom fields and forms' , 'epl' ); ?></h4>
					<p><?php _e( 'This release is focused on making it easier to add custom post types, custom meta fields and forms in preparation for the Contact Management System (CRM). These structural changes will allow us to create even more real estate plugins and tools moving forward and gives you the possibility to create your own custom functions and features.', 'epl' ); ?></p>
					
					<p><?php _e( 'These changes lay the groundwork for the Customer Relationship Manager (CRM) that will  give you better lead generation and capture tools.', 'epl' ); ?></p>
					
					<p><?php _e( 'We have a number of really cool things planned for Easy Property Listings and our goal is to make Real Estate websites easy with WordPress.', 'epl' ); ?></p>
				</div>
			</div>

			<div class="changelog headline-feature">
			
				<h2><?php _e( 'Theme Compatibility Mode introduced in 2.2' , 'epl' );?></h2>
				
				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-themes.jpg'; ?>" class="epl-welcome-featured-image"/>
				</div>
				
				<div class="feature-section">
					<p><?php _e( 'We have been hard at work to improve theme compatibility in this version of Easy Property Listings which required a huge amount of new code and adjustments to make this possible. Many users were unable to get listings to display correctly on their WordPress theme without having to dive in an edit some code.', 'epl' ); ?></p>
					
					<p><?php _e( 'When theme compatibility mode is enabled your WordPress theme will look fantastic and you will be able to use the most advanced real estate platform to power your real estate business.', 'epl' ); ?></p>
					
					<p><?php _e( 'This required the creation of additional templates specifically for theme compatibility mode. Once you enable theme compatibility mode you may get a double up of your featured images as some themes automatically add images and we have added settings where you can either disable the theme images or the plugin.', 'epl' ); ?></p>
					
					<p><?php _e( 'We have tested Easy Property Listings with hundreds of WordPress themes and it works great!', 'epl' ); ?></p>
				</div>
			</div>
			
			<div class="changelog headline-feature">

				<h2><?php _e( 'Powerful Search Remastered' , 'epl' );?></h2>
				
				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-widget-search-remastered.png'; ?>" class="epl-welcome-featured-image"/>
				</div>
				
				<div class="feature-section">

					<p><?php _e( 'We totally rebuilt the search capabilities of Easy Property Listings and added additional search fields for you to use on your real estate website. We have enabled an additional customisable City field and this is great for use in many countries that need an additional location field on their listings.', 'epl' ); ?></p>
					
					<p><?php _e( 'For power users you now can add any number of custom search fields to the search widget using the new filters and hooks. Along with the search improvements we have improved the widget display and improved the responsive display.', 'epl' ); ?></p>
					
					<h4><?php _e( 'Additional Search Improvements.', 'epl' ); ?></h4>
					<ul>
						<li><?php _e( 'Multiple house category select.', 'epl' ); ?></li>
						<li><?php _e( 'Search by city, state/province, postcode/zip and Country.', 'epl' ); ?></li>
						<li><?php _e( 'Search by anything, add your own search fields in minutes.', 'epl' ); ?></li>
					</ul>
				</div>
			</div>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Developer Features' , 'epl' );?></h2>
				
				<div class="feature-section">
					<p><?php _e( 'We have taken the time to add many additional filters and hooks which will open the door for a whole lot more cool features and real estate tools.', 'epl' ); ?></p>
					
					<p><?php _e( 'Many under the hood improvements have been made to increase the extendibility of Easy Property Listings and allow for the creation of additional extensions and features. ', 'epl' ); ?></p>
					
					<?php $custom_template_link = '<a href="https://easypropertylistings.com.au/docs/create-custom-theme-templates-using-included-hooks/">'. __( 'custom templates' , 'epl' ) .'</a>'; ?>
					<h3><?php _e( 'Custom Templates' , 'epl' );?></h3>
					
					<p><?php printf( __( 'Easy Property Listings 2.2 introduces a %s system allowing you to create any number of templates and use them in shortcodes and widgets allowing you and theme developers to create an unlimited number of templates and load them from your theme.', 'epl' ) , $custom_template_link ); ?></p>
				</div>
			</div>
			
			<div class="changelog headline-feature">
			
				<h2><?php _e( 'Additional Updates', 'epl' );?></h2>
			
				<div class="feature-section col two-col">
				
					<div class="col">
						<h4><?php _e( 'Sold Price Display','epl' );?></h4>
						<p><?php _e( 'With Easy Property Listings 2.2 you can now display your sold prices and show off your real estate success to your visitors.', 'epl' );?></p>
						
						<h4><?php _e( 'Adjustable Image Sizes','epl' );?></h4>
						<p><?php _e( 'Edit the image sizes and add more using the new filter allowing for endless possibilities with your WordPress theme.', 'epl' );?></p>
						
						<h4><?php _e( 'Sort by Current, Sold and Leased','epl' );?></h4>
						<p><?php _e( 'Sorting your listings by status is now possible and we have added additional filters to allow you to sort your listings by anything.', 'epl' );?></p>
						
						<h4><?php _e( 'Listing Widget','epl' );?></h4>
						<p><?php _e( 'Listing widget now allows for the creation of additional templates that your can select and create your own custom look.', 'epl' );?></p>
						
						<h4><?php _e( 'Meta Fields','epl' );?></h4>
						<p><?php _e( 'We have added additional fields to better support other countries and improve on the widely used REAXML format.', 'epl' );?></p>
					</div>
					
					<div class="last-feature">
					
						<h4><?php _e( 'Future Extensions','epl' );?></h4>
						<p><?php _e( 'We have added a number of new features for the creation of more advanced real estate tools and have big plans for the future.', 'epl' );?></p>
						
						<h4><?php _e( 'Updated Extensions','epl' );?></h4>
						<p><?php _e( 'Along with the release of Easy Property Listings are a number of improvements to the extensions that help you make your real estate website even better and they will be released alongside Easy Property Listings 2.2.', 'epl' );?></p>
						
						<h4><?php _e( 'More Filters Added','epl' );?></h4>
						<p><?php _e( 'Dozens of new filters have been added allowing you to customise every aspect of Easy Property Listings quickly and easily.', 'epl' );?></p>
						
						<h4><?php _e( 'Loading speed improvements','epl' );?></h4>
						<p><?php _e( 'Changes to only load scripts and CSS when they are needed. Many other optimisations to the code to reduce load times. Dashboard widget loads much faster.', 'epl' );?></p>
					</div>
					
				</div>

			</div>
			
			<div class="changelog headline-feature">
			
			<h2 id="guide-changelog"><?php _e( 'Full Change Log','epl' );?></h2>
			
				<div class="feature-section">
					
					<h4><?php _e( 'Version 2.3', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Custom Post Type API. Makes it easy to create and register new custom post types.', 'epl' );?></li>
						<li><?php _e( 'New: Custom Meta Box API. Creating custom fields and being able to configure custom meta fields on existing and new post types.', 'epl' );?></li>
						<li><?php _e( 'New: Custom Forms API. Will give the ability to create forms and submissions for the coming CRM. (Customer Relationship Manager).', 'epl' );?></li>
						<li><?php _e( 'New: Ordering of extension dynamic custom fields now possible.', 'epl' );?></li>
						<li><?php _e( 'New: Archive template attributes class dynamically added depending on template in use.', 'epl' );?></li>
						<li><?php _e( 'New: A number of helper functions have been added to better integrate additional custom post types.', 'epl' );?></li>
						<li><?php _e( 'New: Button meta field for use in extensions and custom fields.', 'epl' );?></li>
						<li><?php _e( 'New: Adjustments to video output function.', 'epl' );?></li>
						<li><?php _e( 'New: Features taxonomy now use archive template instead of blog post view.', 'epl' );?></li>
						<li><?php _e( 'New: Filters to adjust the Search not found text epl_property_search_not_found_title and epl_property_search_not_found_message.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Restored get_property_suburb function which was used in Listing Templates.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Better author linking and real estate agent user output.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improvements for other extensions to hook into and use maps.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Template fallback functions for improved custom template usage.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Swedish translations updated.', 'epl' );?></li>
						<li><?php _e( 'Fix: New Construction class corrected to new_construction instead of pool.', 'epl' );?></li>
						<li><?php _e( 'Fix: Property ID searching improved. If you have a-z characters in your id include them in the title. E.g. aaa222 - 9 Somewhere Street, Brooklyn NY.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.2.7', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Compatibility for Listing Templates extension.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.2.6', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Updated extension licensing updater to use https. Update required in order to be able to auto-update your extensions as Easy Property Listings has moved to https.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.2.5', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Widget construct fixes for WordPress 4.3.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Un-install function.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Plugin page link to settings.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Languages updated.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.2.4', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Improvements to Commercial/Commercial Land/Business pricing when set to Lease type to display free form price text.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Bar graph in dashboard will no longer cover address if set to low.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added sticker CSS styling for single listing.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode display house category value instead of key.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode Property ID correctly searches numeric listing ID.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget/Shortcode excluded non searchable fields from land, commercial, commercial land and business post types.', 'epl' );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.3', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Adjusted new sorter function to work on lower than PHP version 5.3.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Moved old template functions to theme compatibility, will be removed in future version.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Set sorter list style to none to prevent some themes from displaying a list bullet.', 'epl' );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2.2', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Tweak: CSS tweak for image size to retain proportion on some themes.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Adjusted position of show/hide suburb on Commercial/Business listing types.', 'epl' );?></li>
						<li><?php _e( 'Fix: Archive image correctly loading 300x200 image.', 'epl' );?></li>
						<li><?php _e( 'Fix: Listing address display settings fixed.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.2.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Tweak: Set padding for search tabs for better display on some themes.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search function fix checking for empty option when using custom filters.', 'epl' );?></li>
					</ul>

					<h4><?php _e( 'Version 2.2', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Search shortcode and widget rebuilt to enable adding additional fields through filters and hooks.', 'epl' );?></li>
						<li><?php _e( 'New: Search shortcode and widget added additional search fields for City, State, Postcode and Country.', 'epl' );?></li>
						<li><?php _e( 'New: Search shortcode and widget allows for optional multi select of house category.', 'epl' );?></li>
						<li><?php _e( 'New: Search shortcode and widget improved responsive CSS.', 'epl' );?></li>
						<li><?php _e( 'New: Grid styles included in main CSS for use in extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Upload button added for use in custom plug-ins and extensions to upload files.', 'epl' );?></li>
						<li><?php _e( 'New: Filter to adjust tour labels.', 'epl' );?></li>
						<li><?php _e( 'New: Filters to adjust Floor Plan labels.', 'epl' );?></li>
						<li><?php _e( 'New: Filters to adjust External Link labels.', 'epl' );?></li>
						<li><?php _e( 'New: Sold prices now display when set on front end and manage listings pages.', 'epl' );?></li>
						<li><?php _e( 'New: Label function for returning meta labels.', 'epl' );?></li>
						<li><?php _e( 'New: Ads on settings no longer display when there is an activated extension present.', 'epl' );?></li>
						<li><?php _e( 'New: Locked and help cases options for use in extensions and custom plugins.', 'epl' );?></li>
						<li><?php _e( 'New: Theme compatibility mode which enables all themes to display correctly with options to disable featured images for themes that automatically add featured images.', 'epl' );?></li>
						<li><?php _e( 'New: City setting to allow addresses in countries that need more than a suburb Label is customisable from settings.', 'epl' );?></li>
						<li><?php _e( 'New: Country setting to allow the country to display with the listing address.', 'epl' );?></li>
						<li><?php _e( 'New: Able to adjust or add more registered thumbnail sizes through a filter.', 'epl' );?></li>
						<li><?php _e( 'New: Function to get all the values associated with a specific post meta key.', 'epl' );?></li>
						<li><?php _e( 'New: Replaced the_post_thumbnail on archive pages and shortcodes with a customisable hook allowing for additional customisation with themes.', 'epl' );?></li>
						<li><?php _e( 'New: Specific templates for theme compatibility mode for archive and single listings.', 'epl' );?></li>
						<li><?php _e( 'New: Template loading system allowing for additional templates to be added to shortcodes and widgets from themes, custom plug-ins and extensions. This allows you to create an unlimited number of templates and load them from your theme.', 'epl' );?></li>
						<li><?php _e( 'New: Sorter allows for sorting by current/sold leased.', 'epl' );?></li>
						<li><?php _e( 'New: Ability to add additional sorter via filter.', 'epl' );?></li>
						<li><?php _e( 'New: Post counter function for use in extensions and custom plug-ins.', 'epl' );?></li>
						<li><?php _e( 'New: User fields re-built which allows for adding on new fields through filter.', 'epl' );?></li>
						<li><?php _e( 'New: Help meta type allowing for better internal documentation in extensions.', 'epl' );?></li>
						<li><?php _e( 'New: City meta field added to all listing types when enabled.', 'epl' );?></li>
						<li><?php _e( 'New: Rental display or hide rental price.', 'epl' );?></li>
						<li><?php _e( 'New: Check-box single field type.', 'epl' );?></li>
						<li><?php _e( 'New: Actions added to enable extensions to better hook into listings types and optimised functions for admin column details.', 'epl' );?></li>
						<li><?php _e( 'New: Dashboard widget now displays other extensions content counts.', 'epl' );?></li>
						<li><?php _e( 'New: Listing widget now allows for additional selectable templates to be added through custom plug-ins, hooks and themes.', 'epl' );?></li>
						<li><?php _e( 'New: Replaced widget image with a dynamic action.', 'epl' );?></li>
						<li><?php _e( 'New: Filter added for Gravatar image.', 'epl' );?></li>
						<li><?php _e( 'New: Replaced widget and author box image functions with actions.', 'epl' );?></li>
						<li><?php _e( 'New: Uninstall function to remove all Easy Property Listings content.', 'epl' );?></li>
						<li><?php _e( 'New: Get option function.', 'epl' );?></li>
						<li><?php _e( 'New: When saving settings on extensions sub tabs you are no longer taken to the first tab.', 'epl' );?></li>
						<li><?php _e( 'New: Customisable state label.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved under offer, sold and leased labels.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved install function to reduce code and allow for new settings to be added.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Removed redundant code and streamlined templates.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved reset query function.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Removed old functions improving plugin code.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Rebuilt address function to allow for city and country.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved sorter function in all shortcodes.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improvements to Commercial and Business listing types to better comply with REAXML format with business takings, franchise, terms and commercial outgoings.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Reorganised settings page.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Translations updated and additional tags added.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Search button default label changed from "Find Me A Property!" to "Search".', 'epl' );?></li>
						<li><?php _e( 'Tweak: Applied custom suburb label to EPL - Listing Widget.', 'epl' );?></li>
						<li><?php _e( 'Fix: Listings house categories correctly display labels instead of values.', 'epl' );?></li>
						<li><?php _e( 'Fix: Listings with carport, garage or values set to zero no longer display.', 'epl' );?></li>
						<li><?php _e( 'Fix: Shortcode compatibility for WordPress 3.3 thanks to codewp', 'epl' );?></li>
						<li><?php _e( 'Fix: Saving listing when in debug mode and ticking hide map or hide author box.', 'epl' );?></li>
						<li><?php _e( 'Fix: New Zealand currency now displays a dollar sign.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.11', 'epl' );?></h4>
				
					<ul>
						<li><?php _e( 'Tweak: Removed sub titles "Property Manager" and "Real Estate Agent" from the single listing template for better language support and to facilitate the hiding of the author box.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added epl- prefix to all author-box and widget css.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Renamed author-box container with epl-author-box-container as it was harder to target the author box content and adjusted JS for tabs.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved author box responsive CSS.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated extension updater for multisite and other improvements.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Leased label when adding a property will use custom label.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Wrapper class for property category.', 'epl' );?></li>
						<li><?php _e( 'Fix: Undefined status if importing listings not using current status.', 'epl' );?></li>
						<li><?php _e( 'Fix: When user selects grid/list option and pages the user selected view is retained.', 'epl' );?></li>
						<li><?php _e( 'Fix: [listing post_type="rental"] shortcode price sorting for rental.', 'epl' );?></li>
						<li><?php _e( 'New: Author box is now able to be hidden on a per listing basis.', 'epl' );?></li>
						<li><?php _e( 'New: Added filters for author box social links.', 'epl' );?></li>
						<li><?php _e( 'New: Inspection filter to adjust the inspection date/time format.', 'epl' );?></li>
						<li><?php _e( 'New: Several author widget filters added to enable additional content through extensions or custom functions.', 'epl' );?></li>
						<li><?php _e( 'New: Sold, leased, under offer label filter which uses the label setting and label changes dashboard widget, admin category filters and search widget.', 'epl' );?></li>
						<li><?php _e( 'New: Sold label making Sold STC possible or other Sold label variant.', 'epl' );?></li>
						<li><?php _e( 'New: Danish language thanks to pascal.', 'epl' );?></li>
						<li><?php _e( 'New: German language thanks to ChriKn.', 'epl' );?></li>
						<li><?php _e( 'New: Ukrainian language thanks to Alex.', 'epl' );?></li>
						<li><?php _e( 'New: Swedish language thanks to Roland J.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.10', 'epl' );?></h4>
				
					<ul>
						<li><?php _e( 'New: Email field validation added.', 'epl' );?></li>
						<li><?php _e( 'New: Added status classes to widgets for better targeting of CSS styles.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved video embed and added a filter to adjust video container size.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved CSS wrappers for listing widget and added dynamic class depending on widget display style.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added additional classes to Listing Widget list variant style list items.', 'epl' );?></li>
						<li><?php _e( 'Fix: Additional paging issues fixed in listing widget for other options.', 'epl' );?></li>
						<li><?php _e( 'Fix: Widget leased selection displays rentals correctly.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.1.9', 'epl' );?></h4>
				
					<ul>
						<li><?php _e( 'Fix: Fixed paging issues in listing widget.', 'epl' );?></li>
						<li><?php _e( 'Fix: Fix shortcodes when using multiple listing post types.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.8', 'epl' );?></h4>
				
					<ul>
						<li><?php _e( 'New: Ability to disable all plugin CSS from Advanced Settings section.', 'epl' );?></li>
						<li><?php _e( 'New: Search widget and shortcode now have the option to turn of Location search.', 'epl' );?></li>
						<li><?php _e( 'New: Search widget and shortcode now have filters to control the display of "Any". Each field has a unique filter which will allow you to hide the label using CSS and for example change the Location "Any" label to "Location" this will allow you to create super slim search boxes.', 'epl' );?></li>
						<li><?php _e( 'New: Added translation Belgian (Dutch) thanks to pascal.beyens', 'epl' );?></li>
						<li><?php _e( 'New: Polish translation thanks to Weronika.urbanczyk', 'epl' );?></li>
						<li><?php _e( 'New: Two mew shortcode templates table and table_open usable with shortcodes to provide a slim list of listings. Example usage is [listing_open template="table"] or [listing template="table_open"]. You can copy these new templates into your theme/easypropertylistings folder to further customize.', 'epl' );?></li>
						<li><?php _e( 'New: Added currency support for Qatar Riyal (QAR), United Arab Emirates (AED), Ukrainian Hryvnia (UAH), Vietnamese đồng (VND)', 'epl' );?></li>
						<li><?php _e( 'New: checkbox_single ability for plugin and extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Ability to disable map on each listing.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated currency symbols for: Israeli Shekel, Thai Baht, Indian Rupee, Turkish Lira, Iranian Rial.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved CSS and added additional classes with epl- prefix in templates and search.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved CSS for Location Profiles and Staff Directory extensions.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added filters for commercial titles to allow you to change "For Lease" and "For Sale" using epl_commercial_for_lease_label, and epl_commercial_for_sale_label filters.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Additional CSS classes for Land, Commercial and Rural special features.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Gallery CSS classes added.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved table shortcodes CSS and styling for better full display and responsive widths.', 'epl' );?></li>
						<li><?php _e( 'Fix: New/Open Sticker now appear on listings with the price display set to no.', 'epl' );?></li>
						<li><?php _e( 'Fix: Translations work correctly for categories.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.1.7', 'epl' );?></h4>

					<ul>
						<li><?php _e( 'New: listing_search shortcode now has style option for adjusting the width. You can add style="slim" or style="wide" to the shortcode to adjust the appearance.', 'epl' );?></li>
						<li><?php _e( 'New: Listing Search widget now has style options for adjusting the width.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated translation and added missing sqm translation element.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Allowed for hundredths decimal in bathrooms field.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Floor plan button CSS.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Address and price responsive CSS.', 'epl' );?></li>
						<li><?php _e( 'Fix: Auction listing price set to no displays auction date correctly.', 'epl' );?></li>
						<li><?php _e( 'Fix: Fix: Author position css class.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.6', 'epl' );?></h4>
				
					<ul>
						<li><?php _e( 'Fix: Fancy pagination paging works correctly when shortcodes used on home page.', 'epl' );?></li>
						<li><?php _e( 'Fix: Wrapped new pagination feature in esc_url to prevent vulnerability.', 'epl' );?></li>
						<li><?php _e( 'Fix: Corrected sorting by price when using shortcodes. Note: Rental sorting works on post_type="rental" in all shortcodes.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added rental rate view for text entry of rental rates for REAXML compatibility.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Corrected admin display columns and edit listing pages for better display on mobile devices.', 'epl' );?></li>
					</ul>
										
					<h4><?php _e( 'Version 2.1.5', 'epl' );?></h4>
					
					<ul>
						<li><?php _e( 'Tweak: Commercial listing: Ability to set commercial lease rate to a decimal value using the epl_price_number_format_commercial_lease filter.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated epl.pot translation file.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Removed horizontal line elements in the help section to match WordPress 4.2 admin page styles.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Rental Listing: Added epl_property_bond_position filter to adjust the position of the Bond/Deposit to appear either before or after the value.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Rental Listing: Removed CSS padding before bond value.', 'epl' );?></li>
						<li><?php _e( 'Fix: Rental Listing: Adjusting the Bond/Deposit label will now show your custom label in the Rental Price box.', 'epl' );?></li>
						<li><?php _e( 'Fix: Rural Listing: Undefined label_leased variable.', 'epl' );?></li>
						<li><?php _e( 'Note: Confirmed Easy Property Listings is not vulnerable to recent WordPress exploit.', 'epl' );?></li>
						<li><?php _e( 'New: Added setting to show/hide Listing Unique ID column when managing listings.', 'epl' );?></li>
					</ul>

					<h4><?php _e( 'Version 2.1.4', 'epl' );?></h4>
					
					<ul>
						<li><?php _e( 'Tweak: Pagination optimised and no longer loads in admin.', 'epl' );?></li>
						<li><?php _e( 'Tweak: New filter epl_price_number_format added for decimal rental rates.', 'epl' );?></li>
						<li><?php _e( 'Fix: Display custom bond label when viewing listings.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added filter epl_floorplan_button_label_filter to adjust Floor Plan button label.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.3', 'epl' );?></h4>
					
					<ul>
						<li><?php _e( 'Fix: Author box upgraded to allow for custom tabs and better extension integration with author box and widget.', 'epl' );?></li>
						<li><?php _e( 'Fix: Added additional epl-author-archive CSS class for author archive pages.', 'epl' );?></li>
						<li><?php _e( 'Fix: Improved CSS classes for author box with better responsive support.', 'epl' );?></li>
						<li><?php _e( 'Fix: Added additional filters for author contact information.', 'epl' );?></li>
						<li><?php _e( 'Fix: Added secondary global author function for simpler integration for extensions like the Staff Directory.', 'epl' );?></li>
						<li><?php _e( 'Fix: Changes to author tempaltes and restored author position variable.', 'epl' );?></li>
						<li><?php _e( 'Fix: Further improved max and min graph values when in listing admin.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1.2', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Improved Responsive CSS for grid style.', 'epl' );?></li>
						<li><?php _e( 'Fix: Twenty Fifteen, Twenty Fourteen, Twenty Thirteen, Twenty Twelve CSS styles for better display.', 'epl' );?></li>
						<li><?php _e( 'New: Added CSS class theme name output to archive and single templates.', 'epl' );?></li>
					</ul>
				
					<h4><?php _e( 'Version 2.1.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Max price defaults set for graph calculations when upgrading from pre 2.0 version.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Fancy pagination option which can be enabled in settings.', 'epl' );?></li>
						<li><?php _e( 'New: Coordinates now added to listing if not set prior.', 'epl' );?></li>
						<li><?php _e( 'New: Ability to select larger listing image sizes in admin.', 'epl' );?></li>
						<li><?php _e( 'New: Added date picker for available date on rental listing.', 'epl' );?></li>
						<li><?php _e( 'New: Added date picker for sold date.', 'epl' );?></li>
						<li><?php _e( 'New: New function that combines all meta box options into one global function for admin pages.', 'epl' );?></li>
						<li><?php _e( 'New: Display second agent name in admin listing lists.', 'epl' );?></li>
						<li><?php _e( 'New: Additional admin option to filter by agent/author. ', 'epl' );?></li>
						<li><?php _e( 'New: Shortcode [listing_location] to display listings by specific location.', 'epl' );?></li>
						<li><?php _e( 'New: The following shortcodes can now be filtered by location taxonomy: [listing location="perth"], [listing_open location="sydney"], [listing_category location="melbourne"], [listing_category location="brisbane"], [listing_feature feature="terrace" location="new-york"]', 'epl' );?></li>
						<li><?php _e( 'New: The following shortcodes can now be sorted by price, date and ordered by ASC and DESC [listing sortby="price" sort_order="ASC"].', 'epl' );?></li>
						<li><?php _e( 'New: Sorter added to shortcodes which can be enabled by adding tools_top="on" to your shortcode options.', 'epl' );?></li>
						<li><?php _e( 'New: Template added in table format for use in shortcodes template="table".', 'epl' );?></li>
						<li><?php _e( 'New: Function to get all active post types.', 'epl' );?></li>
						<li><?php _e( 'New: Ability to register additional custom post types.', 'epl' );?></li>
						<li><?php _e( 'New: Extensions now have additional help text ability.', 'epl' );?></li>
						<li><?php _e( 'New: All menus now use global function to render fields.', 'epl' );?></li>
						<li><?php _e( 'New: Improved template output and added additional CSS wrappers for some theme and HTML5 themes.', 'epl' );?></li>
						<li><?php _e( 'New: Commercial rental lease duration now selectable.', 'epl' );?></li>
						<li><?php _e( 'New: Rooms field added to set the number of rooms that the listing has.', 'epl' );?></li>
						<li><?php _e( 'New: Date listed field added to all listing types.', 'epl' );?></li>
						<li><?php _e( 'New: Year built field added to property, rental, rural listing types.', 'epl' );?></li>
						<li><?php _e( 'New: Media upload function for use in extensions.', 'epl' );?></li>
						<li><?php _e( 'New: Ability to customise Under Offer and Leased labels in settings.', 'epl' );?></li>
						<li><?php _e( 'New: Lease type label loaded from dropdown select. So you can have NNN, P.A., Full Service, Gross Lease Rates, on commercial listing types. Also has a filter to enable customisation of the options.', 'epl' );?></li>
						<li><?php _e( 'New: Disable links in the feature list.', 'epl' );?></li>
						<li><?php _e( 'Fix: Text domain fixes on template files.', 'epl' );?></li>
						<li><?php _e( 'Fix: Finnish translation file renamed.', 'epl' );?></li>
						<li><?php _e( 'Fix: FeedSync date processor strptime function corrected.', 'epl' );?></li>
						<li><?php _e( 'Fix: Bug in parking search field. Was only searching carports and not garages. Now searches both.', 'epl' );?></li>
						<li><?php _e( 'Fix: New label now appears on listings not just with an inspection time saved.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Optimised loading of admin scripts and styles to pages where required.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added version to CSS and JS so new versions are automatically used when plugin is updated.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Tidy up of admin CSS.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Video in author box now responsive.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Increased characters possible in address block fields from 40 to 80 characters and heading block to 200.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Coordinates now correctly being used to generate map.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Inspection times improved style in admin.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Commercial rental rate now accepts decimal numbers.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved google map output.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved default settings on upgrade, install and multisite.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Scripts improve site speed.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Dashboard widget improved query.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Front end CSS tweaks for better responsiveness.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.0.3', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Manually entered inspection capitalization fixed pM to PM.', 'epl' );?></li>
						<li><?php _e( 'New: French translation (Thanks to Thomas Grimaud)', 'epl' );?></li>
						<li><?php _e( 'New: Finnish translation (Thanks to Turo)', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.0.2', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Added fall-back diff() function which is not present in PHP 5.2 or earlier used with the New label.', 'epl' );?></li>
						<li><?php _e( 'Fix: Some Labels in settings were not saving correctly particularly the search widget labels.', 'epl' );?></li>
						<li><?php _e( 'Fix: Restored missing author profile contact form tab on author box.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added CSS version to admin CSS and front end CSS.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.0.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'Fix: Attempted Twenty 15 CSS Fix but causes issues with other themes. Manual fix: Copy CSS from style-front.css to correct, margins and grid/sorter.', 'epl' );?></li>
						<li><?php _e( 'Fix: Restored Display of Inspection Label for properties with scheduled inspection times.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget security fix and performance improvements.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 2.0', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Extension validator.', 'epl' );?></li>
						<li><?php _e( 'New: Depreciated listing-meta.php into compatibility folder.', 'epl' );?></li>
						<li><?php _e( 'New: Depreciated author-meta.php into compatibility folder.', 'epl' );?></li>
						<li><?php _e( 'New: Global variables: $property, $epl_author and $epl_settings.', 'epl' );?></li>
						<li><?php _e( 'New: Added filters for fields and groups in /lib/meta-boxes.php', 'epl' );?></li>
						<li><?php _e( 'New: Property custom meta re-written into class. This was the big change to 2.0 where we completely re-wrote the output of the meta values which are now accessible using global $property variable and easy template actions.', 'epl' );?></li>
						<li><?php _e( 'New: Property meta can now can be output using new actions for easy and quick custom template creation.', 'epl' );?></li>
						<li><?php _e( 'New: Reconstructed templates for single, archive & author pages', 'epl' );?></li>
						<li><?php _e( 'Tweak: Removed unused price script', 'epl' );?></li>
						<li><?php _e( 'Fix: Fixed warning related to static instance in strict standard modes', 'epl' );?></li>
						<li><?php _e( 'New: API for extensions now support WordPress editor with validation.', 'epl' );?></li>
						<li><?php _e( 'New: jQuery date time picker formatting added to improve support for auction and sold listing, support for 30+ languages support.', 'epl' );?></li>
						<li><?php _e( 'New: Inspection time auto-formats REAXML date eg [13-Dec-2014 11:00am to 11:45am] and will no longer show past inspection times.', 'epl' );?></li>
						<li><?php _e( 'New: Inspection time support multiple dates written one per line.', 'epl' );?></li>
						<li><?php _e( 'Tweak: CSS improved with better commenting and size reduction.', 'epl' );?></li>
						<li><?php _e( 'New: Dashboard widget now lists all listing status so at a glance you can see your property stock.', 'epl' );?></li>
						<li><?php _e( 'New: Display: To enable grid, list and sorter your custom archive-listing.php template requires the new action hook epl_template_before_property_loop before the WordPress loop.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Utility hook action hook added epl_template_after_property_loop for future updates.', 'epl' );?></li>
						<li><?php _e( 'New: Display: List and grid view with optional masonry effect.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Sorter added for price high/low and date newest/oldest.', 'epl' );?></li>
						<li><?php _e( 'New: Auction Date formats nicely. EG [Auction Saturday 28th December at 2:00pm].', 'epl' );?></li>
						<li><?php _e( 'New: Tabbed extensions page support in admin for advanced extensions like Listing Alerts.', 'epl' );?></li>
						<li><?php _e( 'New: Multiple author support in Author Box.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Supports multiple listing types, hold Ctrl to enable tabbed front end display.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Labels are configurable from the Display settings allowing you to set for example: Property to Buy and Rental to Rent and use a single widget to search multiple types.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget and shortcode supports search by property ID, post Title, Land Area and Building Area.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - removed extra fields from land, added labels for each property type to be shown as tab heading in search widget', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget - Optimized total queries due to search widget from 1500 + to ~40', 'epl' );?></li>
						<li><?php _e( 'New: Author variables accessible using new CLASS.', 'epl' );?></li>
						<li><?php _e( 'New: Search short code supports array of property types.', 'epl' );?></li>
						<li><?php _e( 'New: REAXML date format function to format date correctly when using WP All Import Pro. Usage [epl_feedsync_format_date({./@modTime})].', 'epl' );?></li>
						<li><?php _e( 'New: REAXML Unit and lot formatting function for usage in the title when using WP All Import Pro. Usage [epl_feedsync_filter_sub_number({address[1]/subNumber[1]})].', 'epl' );?></li>
						<li><?php _e( 'New: Global $epl_settings settings variable adds new default values on plugin update.', 'epl' );?></li>
						<li><?php _e( 'New: Display: Added customisable label for rental Bond/Deposit.', 'epl' );?></li>
						<li><?php _e( 'New: Template functions completely re-written and can now be output using actions.', 'epl' );?></li>
						<li><?php _e( 'New: Added NEW sticker with customisable label and ability to set how long a listing displays the new label.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Compatibility fixes', 'epl' );?></li>
						<li><?php _e( 'New: Bar Graph API added.', 'epl' );?></li>
						<li><?php _e( 'New: Graph in admin allows you to set the max bar graph value. Default are (2,000,000 sale) and (2,000 rental).', 'epl' );?></li>
						<li><?php _e( 'New: Graph visually displays price and status.', 'epl' );?></li>
						<li><?php _e( 'New: Price graph now appears in admin pages quickly highlighting price and status visually.', 'epl' );?></li>
						<li><?php _e( 'New: Meta Fields: Support for unit number, lot number (land).', 'epl' );?></li>
						<li><?php _e( 'New: South African ZAR currency support.', 'epl' );?></li>
						<li><?php _e( 'Fix: Corrected Commercial Features ID Spelling', 'epl' );?></li>
						<li><?php _e( 'Tweak: YouTube video src to id function is replaced with better method which handles multiple YouTube video formats including shortened & embedded format', 'epl' );?></li>
						<li><?php _e( 'New: Adding Sold Date processing', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated shortcode templates', 'epl' );?></li>
						<li><?php _e( 'Tweak: Global $epl_author.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Fixed content/ into EPL_PATH_TEMPLATES_CONTENT', 'epl' );?></li>
						<li><?php _e( 'New: Support for older extensions added', 'epl' );?></li>
						<li><?php _e( 'New: Extension offers in menus general tab', 'epl' );?></li>
						<li><?php _e( 'Tweak: Renamed user profile options section to [Easy Property Listings: Author Box Profile].', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added better Bond/Deposit for rentals labels.', 'epl' );?></li>
						<li><?php _e( 'Fix: Deprecated author-meta.php in compatibility folder, class-author-meta.php has been created which will be used in place of author-meta.php & its variables in all author templates', 'epl' );?></li>
						<li><?php _e( 'New: Added template functions for author meta class, modified templates lib/templates/content/content-author-box-simple-card.php lib/templates/content/content-author-box-simple-grav.php lib/templates/content/content-author-box.php to use the template functions based on author meta class instead of variables from author-meta.php', 'epl' );?></li>
						<li><?php _e( 'New: author-meta.php depreciated and moved to compatibility directory. Variables globally available using $epl_author variable.', 'epl' );?></li>
						<li><?php _e( 'Tweak: listing-meta.php depreciated and moved to compatibility directory. Variables globally available with $property variable.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Added Listing not Found to default templates when search performed with no results.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved Google maps address output for addresses containing # and /.', 'epl' );?></li>
						<li><?php _e( 'Fix: Listing Pages now have better responsive support for small screen devices like iPhone.', 'epl' );?></li>
						<li><?php _e( 'Fix: Default templates for Genesis and TwentyTwelve now show Listing Not Found when a search result returns empty.', 'epl' );?></li>
						<li><?php _e( 'Fix: Purged translations in epl.pot file.', 'epl' );?></li>
						<li><?php _e( 'Fix: Search Widget and short code drastically reduces database queries.', 'epl' );?></li>
						<li><?php _e( 'New: Templates are now able to be saved in active theme folder /easypropertylistings and edited. Plugin will use these first and fall back to plugin if not located in theme folder.', 'epl' );?></li>
						<li><?php _e( 'Fix: Extensions Notification and checker updated', 'epl' );?></li>
						<li><?php _e( 'New: updated author templates to use new author meta class', 'epl' );?></li>
						<li><?php _e( 'Fix: Added prefix to CSS tab-content class. Now epl-tab-content for compatibility.', 'epl' );?></li>
						<li><?php _e( 'New: Update user.php', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved internal documentation and updated screens.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Improved descriptions on author pages.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Better permalink flushing on activation, deactivation and install.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Extensive changes to admin descriptions and labels.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Optimising the php loading of files and scripts.', 'epl' );?></li>
						<li><?php _e( 'New: Define EPL_RUNNING added for extensions to check if plugin is active.', 'epl' );?></li>
						<li><?php _e( 'New: New options added to setting array when plugin is updated.', 'epl' );?></li>
						<li><?php _e( 'New: Old functions and files moved to plug-in /compatibility folder to ensure old code still works.', 'epl' );?></li>
						<li><?php _e( 'New: Meta Location Label.', 'epl' );?></li>
						<li><?php _e( 'New: Service banners on settings page.', 'epl' );?></li>
						<li><?php _e( 'New: Saving version number so when updating new settings are added.', 'epl' );?></li>
						<li><?php _e( 'New: iCal functionality for REAXML formatted inspection dates. Further improvements coming for manual date entry. ', 'epl' );?></li>
						<li><?php _e( 'New: Extensions options pages now with tabs for easier usage.', 'epl' );?></li>
						<li><?php _e( 'New: Added ID classes to admin pages and meta fields.', 'epl' );?></li>
						<li><?php _e( 'New: Filters to adjust land and building sizes from number to select fields.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Moved old extensions options page to compatibility folder so older extensions still work as expected.', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Added filter for land min & max fields in listing search widget', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Added filter for building min & max fields in listing search widget', 'epl' );?></li>
						<li><?php _e( 'Fix: For session start effecting certain themes', 'epl' );?></li>
						<li><?php _e( 'New: Land sizes now allow up to 5 decimal places', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Custom submit label', 'epl' );?></li>
						<li><?php _e( 'New: Search Widget - Can search by title in property ID / Address field', 'epl' );?></li>
						<li><?php _e( 'New: Added Russian Translation', 'epl' );?></li>
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
						<li><?php _e( 'New: Shortcode [listing_open] replaces [home_open] shortcode. Retained [home_open] for backward compatibility, however adjust your site. ', 'epl' );?></li>
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
					
					<h4><?php _e( 'Version 1.1.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'New: Internationalisation support to enable customizing of post types: slug, archive, rewrite, labels, listing categories for meta_types.', 'epl' );?></li>
						<li><?php _e( 'New: Created filters for listing meta select fields: property_category, property_rural_category, property_commercial_category, property_land_category.', 'epl' );?></li>
						<li><?php _e( 'New: Created filters for each of the seven custom post types: labels, supports, slug, archive, rewrite, seven custom post types.', 'epl' );?></li>
						<li><?php _e( 'New: Shortcode [listing_category] This shortcode allows for you to output a list of listings by type and filter them by any available meta key and value.', 'epl' );?></li>
						<li><?php _e( 'Tweak: Updated search widget for filtered property_categories.', 'epl' );?></li>
						<li><?php _e( 'Fix: Listing categories were showing key, now showing value.', 'epl' );?></li>
						<li><?php _e( 'Fix: Settings were not showing up after saving, second refresh required setting variable to reload.', 'epl' );?></li>
					</ul>
					
					<h4><?php _e( 'Version 1.1', 'epl' );?></h4>
					<ul>
						<li><?php _e( 'First official release!', 'epl' );?></li>
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
		<div class="wrap about-wrap epl-about-wrap">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'epl' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'epl' ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'epl' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog headline-feature">
				<h2><?php _e( 'Real Estate Tools for WordPress', 'epl' );?></h2>
				
				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-welcome.png'; ?>" class="epl-welcome-featured-image"/>
				</div>
			</div>
			
			<div class="changelog headline-feature">
				<h2><?php _e( 'Quick Start Guide', 'epl' );?></h2>
				
				<h3 class="about-description" style="text-align: center;"><?php _e( 'Use the tips below to get started using Easy Property Listings. You will be up and running in no time!', 'epl' ); ?></h3>
				
				<div class="feature-section">
					<ul style="text-align: center;">
						<li><a href="#guide-configure"><?php _e( 'Activate the listing types you need & configure the plugin general settings', 'epl' ); ?></a></li>
						<li><a href="#guide-page"><?php _e( 'Create a blank page for each activated listing type', 'epl' ); ?></a></li>
						<li><a href="#guide-first-listing"><?php _e( 'Publish your first listing for testing your theme setup', 'epl' ); ?></a></li>
						
						<li><a href="#guide-theme"><?php _e( 'Setup your theme to work with the plugin', 'epl' ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-about' ), 'index.php' ) ) ); ?>#guide-changelog"><?php _e( 'Full Change Log', 'epl' ); ?></a></li>
						<li><a href="#guide-help"><?php _e( 'Visit Support', 'epl' ); ?></a></li>
					</ul>
				</div>
			</div>
			
			<div class="changelog headline-feature">
			
				<h2 id="guide-configure"><?php _e( 'Activate the listing types you need', 'epl' );?></h2>
			
				<div class="feature-section">
				
					<div class="col">
					
						<p><?php _e( 'Visit the general settings page and enable the listing types you need. Once you have pressed save visit the Permalinks page to re-fresh your sites permalinks.', 'epl' );?></p>
						
						<p><?php _e( 'Instead of classifying everything as a property, Easy Property Listings allows you to separate the different listing types which is better for SEO and RSS feeds.', 'epl' );?></p>
						
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
					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-general-settings.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>
			</div>
			
			<div class="changelog headline-feature">
			
				<h2 id="guide-page"><?php _e( 'Create a blank page for each activated listing type', 'epl' );?></h2>
			
				<div class="feature-section">
					<div class="col">
						
						<p><?php _e( 'Doing this allows you to add "Property", "Land" and "Rental" pages to your WordPress menu. Add a new page for each listing type you activated.', 'epl' );?></p>
						
						<p><?php _e( 'For example, lets say you have activated: Property, Rental and Land. Create three pages, one called "Property", another "Land" and the third "Rental" these will be the custom post type slugs/permalinks eg: property, rental and land.', 'epl' );?></p>
						
						<p><?php _e( 'Publish a test "Property Listing" and visit your new property page and you will see the new property and others you have created.', 'epl' );?></p>
						
						<p><?php _e( 'Now you can rename them to whatever you like eg: "For Sale", "For Rent" etc, but leave the slug/permalink as it was,', 'epl' ); ?> <strong><?php _e( 'this is very important.', 'epl' );?></strong></p>
					
					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-default-pages.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>
				
			</div>
			
			<div class="changelog headline-feature">
			
				<h2 id="guide-first-listing" class="epl-welcome-sub-heading"><?php _e( 'Publish Your First Listing', 'epl' );?></h2>
				
				<div class="featured-image">
					<?php echo wp_oembed_get('https://www.youtube.com/watch?v=h6B8LLecfbw', array('width'=>600)); ?>
				</div>
				<h3 class="epl-welcome-sub-heading"><?php _e( 'Title & Author', 'epl' );?></h3>
				
				<div class="feature-section">

					<div class="col">
					
						<h4><?php _e( 'Title', 'epl' );?></h4>
						<p><?php _e( 'Use the full listing address as the title.', 'epl' );?>
						
						<p><?php _e( 'When a property is being sold the "heading" is frequently changed and can cause permalink issues. Not to mention the search engine benefits.', 'epl' );?></p>
						
						<h4><?php _e( 'Author or Primary Real Estate Agent', 'epl' );?></h4>
						<p><?php _e( 'Select the author to show the name of the agent who has listed the property with their contact details. For best results each real estate agent should have their own WordPress user profile which allows for the output of their details on the listing and in widgets.', 'epl' );?></p>
						
					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-title.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
				</div>
				
				<h3 class="epl-welcome-sub-heading"><?php _e( 'Gallery and Featured Image', 'epl' );?></h3>
				<div class="feature-section">
						
					<div class="col">
						
						<h3><?php _e( 'Gallery', 'epl' );?></h3>
						<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'epl' ); ?></p>
						
						<p><?php _e( 'You can automatically output a gallery from the Display options page.', 'epl' );?></p>
						
						<p><?php _e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing. You can also easily adjust the number of gallery columns from the plugin Display options.', 'epl' );?></p>
						
						<h3><?php _e( 'Gallery Light Box', 'epl' );?></h3>
						<p><?php _e( 'Using a light box plug-in like Easy FancyBox, your automatic gallery images will use the light box effect.', 'epl' );?></p>

					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
					
				</div>
				
				
				<h3 style="font-size: 1.8em; text-align: center;"><?php _e( 'Listing Details', 'epl' );?></h3>
				
				<div class="feature-section">
					
					<div class="col">
						
						<h4><?php _e( 'Heading', 'epl' );?></h4>
						<p><?php _e( 'Enter the descriptive listing headline like "Great Property with Views".', 'epl' );?></p>
						
						<h4><?php _e( 'Second Listing Agent', 'epl' );?></h4>
						<p><?php _e( 'If the listing has two real estate agents marketing it, enter their WordPress user name here. The primary agent is the post Author.', 'epl' );?></p>
						
						<h4><?php _e( 'Inspection Times', 'epl' );?></h4>
						<p><?php _e( 'Now supports multiple inspection times, add one per line. Past inspection dates will not display when using the new format.', 'epl' );?></p> 
						
						<p><?php _e( 'The output is now wrapped in an iCal format so clicking on the date will open the users calendar.', 'epl' );?></p>
						
					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
					
				</div>
				
				<h3 style="font-size: 1.8em; text-align: center;"><?php _e( 'Search by location', 'epl' );?></h3>
				
				<div class="feature-section">
					
					<div class="col">
						<p><?php _e( 'Although the address details are added into the Property Address box the location search you also need to add the City/Suburb to the location search taxonomy.', 'epl' );?></p>
						<p><?php _e( 'This works like post tags and will populate the search widget/shortcode with your listings and it will automatically filter out options if no listings have that option.', 'epl' );?></p>
					</div>
					
					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details-location.png'; ?>" class="epl-welcome-screenshots"/>
					</div>
					
				</div>
			</div>
			
			<div class="changelog headline-feature">
			
				<h2 id="guide-theme" class="epl-welcome-sub-heading"><?php _e( 'Configure your theme', 'epl' );?></h2>
				<h3 class="about-description" style="text-align: center;"><?php _e( 'We have done our best to integrate Easy Property Listings with all WordPress themes.', 'epl' );?></h3>

				<div class="feature-section col two-col">

					<div>
						<h4><?php _e( 'Theme Compatibility', 'epl' );?></h4>
						<p><?php _e( 'Once you add a listing and if your page is really wide or your sidebar is under the content enable Theme Compatibility mode from settings.', 'epl' );?></p>
						
						<p><?php _e( 'Review your listing and if you are seeing double images, hop back over to Settings page and either disable the theme feature image or the one provided by Easy Property Listings.', 'epl' );?></p>
						
						<h4><?php _e( 'Shortcodes', 'epl' );?></h4>

						<p><?php _e( 'The featured image settings have no impact on the Easy Property Listings shortcodes and widgets.', 'epl' );?></p>
					</div>
					
					<div class="last-feature">
					
						<h4><?php _e( 'Theme Compatibility not required for some themes', 'epl' );?></h4>
						<ul>
							<li><a href="http://ithemes.com/member/go.php?r=15200&i=l37"><?php _e( 'iThemes Builder Themes', 'epl' );?></a></li>
							<li><?php _e( 'Genesis Framework by StudioPress', 'epl' );?></li>
							<li><?php _e( 'Twenty 12, 13, 14 &#38; 15 by WordPress.', 'epl' );?></li>
							<li><?php _e( 'Many others, add a listing and see how it looks.', 'epl' );?></li>
						</ul>
						<p><?php _e( 'We have a selection of pre configured templates here for many popular themes', 'epl' );?> <a href="http://easypropertylistings.com.au/support/forum/theme-support/"><?php _e( 'here', 'epl' );?></a>.</p>
					</div>
				</div>
			</div>

			<div class="changelog headline-feature">
				<h2 id="theme-instructions" class="epl-welcome-sub-heading"><?php _e( 'Advanced theme integration instructions', 'epl' );?></h2>

				<div class="feature-section two-col">
					<?php $theme_instructions = '<a href="https://easypropertylistings.com.au/docs/setup-wordpress-theme-easy-property-listings-2-0/">'. __( 'theme setup instructions can be found here' , 'epl' ) .'</a>'; ?>
					<h4 style="text-align: center;"><?php printf( __( 'Detailed %s.', 'epl' ) , $theme_instructions );?></h4>
				</div>
			</div>
			
			<div class="changelog headline-feature">
			
				<h2 class="about-description" style="text-align: center;"><?php _e( 'Stuck getting your theme to work?', 'epl' );?></h2>
				<h3 class="about-description" style="text-align: center;"><?php _e( 'Not all themes follow modern WordPress coding standards and these may take a little more time and experience to get working. If you just can not get it to work, visit', 'epl' );?> <a href="http://easypropertylistings.com.au/support/"><?php _e( 'premium support', 'epl' );?></a> <?php _e( 'and fill out a theme support request.', 'epl' );?></em></h3>
				
				<p style="text-align:center;"><?php _e( 'If the theme is available in the WordPress.org theme directory let us know the theme name and URL where we can download it in your support ticket. If its a premium theme or are in a hurry submit a priority support ticket along with the theme in a zip or a download link to it on a file sharing site like Dropbox.', 'epl' );?></p>
				
				<h2 id="guide-help"><?php _e( 'Need Help?', 'epl' );?></h2>
				
				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Premium Support','epl' );?></h4>
						<p><?php echo $link = sprintf( __( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="%s">support forums</a>.', 'epl' ), esc_url( 'http://easypropertylistings.com.au/support/' ) );?></p>
					</div>
					
					<div>
						<h4><?php _e( 'Need Even Faster Support?', 'epl' );?></h4>
						<p><?php _e( '<a href="http://easypropertylistings.com.au/support/pricing/">Priority Support forums</a> are there for customers that need faster and/or more in-depth assistance.', 'epl' );?></p>
					</div>
					
					<div class="last-feature">
						<h4><?php _e( 'Documentation and Short Codes','epl' );?></h4>
						<p><?php _e( 'Read the','epl' );?> <a href="http://easypropertylistings.com.au/documentation/"><?php _e( 'documentation','epl' );?></a> <?php _e( ' and instructions on how to use the included','epl' );?> <a href="http://easypropertylistings.com.au/section/short-codes/"><?php _e( 'shortcodes','epl' );?></a>.</p>
					</div>
				</div>	
			</div>	
				
			<div class="changelog headline-feature">

				<div class="feature-section col two-col">
					<div>
						<h3><?php _e( 'Stay Up to Date', 'epl' );?></h3>
						<h4><?php _e( 'Get Notified of Extension Releases','epl' );?></h4>
						<p><?php _e( 'New extensions that make Easy Property Listings even more powerful are released nearly every single week. Subscribe to the newsletter to stay up to date with our latest releases. <a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to ensure you do not miss a release!', 'epl' );?></p>

						<h4><?php _e( 'Get Alerted About New Tutorials', 'epl' );?></h4>
						<p><?php _e( '<a href="http://eepurl.com/TRO9f" target="_blank">Sign up now</a> to hear about the latest tutorial releases that explain how to take Easy Property Listings further.', 'epl' );?></p>
					</div>
					
					<div class="last-feature">
						<h3><?php _e( 'Extend With Extensions', 'epl' );?></h3>
						<h4><?php _e( '18 Extensions and many more coming','epl' );?></h4>
						<p><?php _e( 'Add-on plug ins are available that greatly extend the default functionality of Easy Property Listings. There are extensions for Listing Sliders, Brochures, Advanced mapping, Testimonials, Agent Staff Directory, Listing alerts, CMA Market Reports, Location Profiles, and many, many more.', 'epl' );?></p>
						
						<h4><?php _e( 'Visit the Extension Store', 'epl' );?></h4>
						<p><a href="http://easypropertylistings.com.au/extensions/" target="_blank"><?php _e( 'The Extensions store' , 'epl' );?></a> <?php _e( 'has a list of all available extensions to make your real estate website even better.', 'epl' );?></p>
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
