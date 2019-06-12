<?php
/**
 * Welcome Page Class
 *
 * @package     EPL
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2019, Merv Barrett
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
	 * Parse the EPL readme.txt file
	 *
	 * @since 3.1.20
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( EPL_PLUGIN_PATH . 'readme.txt' ) ? EPL_PLUGIN_PATH . 'readme.txt' : null;
		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'easy-property-listings' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );
			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}
		return $readme;
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

			<div class="changelog headline-feature epl-clearfix">
				<h2><?php _e( 'Major Release with Huge Improvements', 'easy-property-listings'  );?></h2>

				<div class="feature-section">

					<p class="lead-description"><?php _e( 'Easy Property Listings 3.3 is a major release with a huge number of new features for your real estate website. Whether you are a developer helping clients or you manage your site yourself, these features will help you showcase your listings.' , 'easy-property-listings'); ?></p>
				</div>

				<h4 style="text-align:center"><?php _e( 'Standout Upgrades', 'easy-property-listings'  );?></h4>

				<div class="feature-section">
					<ul style="text-align: center;">
						<li><?php _e( 'Gutenberg enabled with REST API.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Price slider search and major search upgrades.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'New super awesome [listing_advanced] shortcode.', 'easy-property-listings'  ); ?></li>

						<li><?php _e( 'Re-created Social icons and several others.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Agent profile links for Instagram, Pinterest and YouTube.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Minified code with production and development mode.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Listing administration screens slimmed.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Set listings as featured even with imports.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Tools to import and export settings.', 'easy-property-listings'  ); ?></li>
						<li><?php _e( 'Unified search.', 'easy-property-listings'  ); ?></li>
						<li><a href="#guide-changelog"><?php _e( 'So much more.', 'easy-property-listings'  ); ?></a></li>
					</ul>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2><?php _e( 'Gutenberg Support', 'easy-property-listings'  );?></h2>

				<div class="feature-section">

					<p><?php _e( 'Now that WordPress Gutenberg has been out and compatibility issues solved with the core WordPress team, we have enabled Gutenberg as default for Easy Property Listings. This also enables the REST API for all the custom post types.' , 'easy-property-listings'); ?></p>

					<p><?php echo $link = sprintf( __( 'Prefer classic WordPress? We have you covered. If you have the <a href="%s" target="_blank">Classic Editor WordPress plugin</a> installed and activated your listing entry screens will revert to the classic WordPress experience.', 'easy-property-listings'  ), esc_url( 'https://wordpress.org/plugins/classic-editor/' ) );?></p>

				</div>

			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2><?php _e( 'Grid Mode and Enhanced CSS Styles', 'easy-property-listings'  );?></h2>

				<div class="feature-section">

					<p><?php _e( 'Major changes to underlying front end CSS is always a challenge. We know a large number of Easy Property Listings developers have applied a considerable amount of customisations to the default look and feel of the plugin. This is exactly what we hoped would happen and have seen some stunning examples.' , 'easy-property-listings'); ?></p>

					<p><?php _e( 'Saying this we understand that making wide sweeping changes can have unintended consequences to your site. We do not want that to happen to you. So we have added an option to enable Enhanced CSS from the Advanced Settings page.' , 'easy-property-listings'); ?></p>

					<p><?php _e( 'One of the changes this applies to is the grid mode view of your listings. Structurally this converts a float left block into an inline block for better wrapping of listings in grid mode and prevents white space appearing if your listings are not the exact same size.' , 'easy-property-listings'); ?></p>

					<div class="featured-image">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/grid-mode.png'; ?>" class="epl-welcome-featured-image"/>
					</div>

					<p><?php _e( 'If you install Easy Property Listings fresh this option will be automatically enabled, however if you are upgrading to 3.3 you will need to <strong>manually enable the option</strong> in the Advanced Settings and check that your customisations still work.' , 'easy-property-listings'); ?></p>

					<p><?php _e( 'A lot of work has gone into the Enhanced CSS that will be documented in the codex as there are a number of new classes that allow you to customise templates faster.' , 'easy-property-listings'); ?></p>

				</div>

			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2 id="feature-enhanced-styles" class="about-description" style="text-align: center;"><?php _e( 'Improvements for Everyone', 'easy-property-listings'  );?></h2>

				<div class="feature-section col three-col">
					<div class="col">
						<h4><?php _e( 'Listing Entry','easy-property-listings'  );?></h4>
						<p><?php _e( 'This release contains a number of improvements to the listing entry screens to trim them down in size significantly which should aid you with listing entry. We have also optimised the experience if you are using classic WordPress or Gutenberg.' , 'easy-property-listings'); ?></p>
					</div>

					<div class="col">
						<h4><?php _e( 'New Social Icons', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Social profile icons has been altered to a more modern flat style that can be easily customised with CSS when using SVG versions or modify the PNG file. Any existing customisations are preserved.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col last-feature">
						<h4><?php _e( 'Search','easy-property-listings'  );?></h4>
						<p><?php _e( 'Unified price and listing type search is now possible. The result URL now only contains the options selected and you will also find a price slider solution.', 'easy-property-listings'  );?></p>
					</div>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2><?php _e( 'Production and Development Mode', 'easy-property-listings'  );?></h2>

				<div class="feature-section">

					<p><?php _e( 'Optomising your sites performance and keeping it running as fast possible has been a key cornerstone in development of Easy Property Listings since the beginning. We are further enhancing this with a production and development mode option that will use minified JS and CSS files. This is enabled by default and can be easily disabled from the Advanced Settings of Easy Property Listings.' , 'easy-property-listings'); ?></p>

				</div>

			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2 id="feature-enhanced-styles" class="about-description" style="text-align: center;"><?php _e( 'WordPress Developers', 'easy-property-listings'  );?></h2>

				<div class="feature-section col three-col">
					<div class="col">
						<h4><?php _e( 'Import and Export Settings','easy-property-listings'  );?></h4>
						<p><?php _e( 'Now you can visit the Tools page and export your settings and import them into another project. This also exports and imports your extension settings.' , 'easy-property-listings'); ?></p>
					</div>

					<div class="col">
						<h4><?php _e( 'Shortcodes Updates', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Often you want to easily set the default sort of your shortcodes to feature current listings first. Well now you can with the sortby=status option.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col last-feature">
						<h4><?php _e( 'Code Upgrades','easy-property-listings'  );?></h4>
						<p><?php _e( 'There have been major code upgrades applied across a large number of funcations and features in the plugin, improving extensibility, performance and ease of use.', 'easy-property-listings'  );?></p>
					</div>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature">

				<h2 id="guide-changelog"><?php _e( 'Full Change Log','easy-property-listings'  );?></h2>

				<div class="feature-section">

					<?php echo $this->parse_readme(); ?>

				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to Easy Property Listings Settings', 'easy-property-listings'  ); ?></a>
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
		<div class="wrap about-wrap epl-about-wrap epl-clearfix">
			<h1><?php printf( __( 'Welcome to Easy Property Listings %s', 'easy-property-listings'  ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Easy Property Listings %s is ready to make your real estate website faster, safer and better!', 'easy-property-listings'  ), $display_version ); ?></div>
			<div class="epl-badge"><?php printf( __( 'Version %s', 'easy-property-listings'  ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog headline-feature epl-clearfix">
				<h2><?php _e( 'Real Estate Tools for WordPress', 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-welcome.png'; ?>" class="epl-welcome-featured-image"/>
				</div>
			</div>

			<div class="changelog headline-feature epl-clearfix">
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

			<div class="changelog headline-feature epl-clearfix">

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

			<div class="changelog headline-feature epl-clearfix">

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

			<div class="changelog headline-feature epl-clearfix">

				<h2 id="guide-first-listing" class="epl-welcome-sub-heading"><?php _e( 'Publish Your First Listing', 'easy-property-listings'  );?></h2>

				<div class="featured-image">
					<?php $video_add_listing_id = 'h6B8LLecfbw'; ?>

					<div class="video-container">
						<iframe width="640" height="360" src="//www.youtube.com/embed/<?php echo $video_add_listing_id; ?>" frameborder="0" allowfullscreen ></iframe>
					</div>
				</div>

				<h3 class="epl-welcome-sub-heading"><?php _e( 'Title & Author', 'easy-property-listings'  );?></h3>

				<div class="feature-section epl-clearfix">

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

				<div class="feature-section epl-clearfix">

					<div class="col">

						<h4><?php _e( 'Gallery', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Add a gallery of images to your listings with the WordPress Add Media button.' , 'easy-property-listings'  ); ?></p>

						<p><?php _e( 'You can automatically output a gallery from the Display options page.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'If set to automatic, just upload your images to the listing and press x to close the media upload box once the images are attached to the listing. You can also easily adjust the number of gallery columns from the plugin Display options.', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Gallery Light Box', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Using a light box plug-in like Easy FancyBox, your automatic gallery images will use the light box effect.', 'easy-property-listings'  );?></p>

					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-gallery.png'; ?>" class="epl-welcome-screenshots"/>
					</div>

				</div>


				<h3 class="epl-welcome-sub-heading"><?php _e( 'Listing Details', 'easy-property-listings'  );?></h3>

				<div class="feature-section epl-clearfix">

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

				<div class="feature-section epl-clearfix">

					<div class="col">
						<p><?php _e( 'Although the address details are added into the Property Address box the location search you also need to add the City/Suburb to the location search taxonomy.', 'easy-property-listings'  );?></p>
						<p><?php _e( 'This works like post tags and will populate the search widget/shortcode with your listings and it will automatically filter out options if no listings have that option.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col">
						<img src="<?php echo EPL_PLUGIN_URL . 'lib/assets/images/screenshots/epl-add-listing-details-location.png'; ?>" class="epl-welcome-screenshots"/>
					</div>

				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2 id="guide-theme" class="epl-welcome-sub-heading"><?php _e( 'Configure your theme', 'easy-property-listings'  );?></h2>
				<h3 class="about-description" style="text-align: center;"><?php _e( 'We have done our best to integrate Easy Property Listings with all WordPress themes.', 'easy-property-listings'  );?></h3>

				<div class="feature-section col two-col">

					<div class="col">
						<h4><?php _e( 'Theme Compatibility', 'easy-property-listings'  );?></h4>
						<p><?php _e( 'Once you add a listing and if your page is really wide or your sidebar is under the content enable Theme Compatibility mode from settings.', 'easy-property-listings'  );?></p>

						<p><?php _e( 'Review your listing and if you are seeing double images, hop back over to Settings page and either disable the theme feature image or the one provided by Easy Property Listings.', 'easy-property-listings'  );?></p>

						<h4><?php _e( 'Shortcodes', 'easy-property-listings'  );?></h4>

						<p><?php _e( 'The featured image settings have no impact on the Easy Property Listings shortcodes and widgets.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col last-feature">

						<h4><?php _e( 'Theme Compatibility not required for some themes', 'easy-property-listings'  );?></h4>
						<ul>
							<li><a href="http://ithemes.com/member/go.php?r=15200&i=l37"><?php _e( 'iThemes Builder Themes', 'easy-property-listings'  );?></a></li>
							<li><?php _e( 'Genesis Framework by StudioPress', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Headway Theme Framework', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Twenty 11, 12, 13, 14, 15, 16, 17 &#38; 19 by WordPress.', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Divi by Elegant Themes.', 'easy-property-listings'  );?></li>
							<li><?php _e( 'Many others, add a listing and see how it looks.', 'easy-property-listings'  );?></li>
						</ul>
						<p><?php _e( 'We have a selection of pre configured templates here for many popular themes', 'easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/article/185-theme-templates"><?php _e( 'here', 'easy-property-listings'  );?></a>.</p>
					</div>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">
				<h2 id="theme-instructions" class="epl-welcome-sub-heading"><?php _e( 'Advanced instructions', 'easy-property-listings'  );?></h2>

				<div class="feature-section">
					<?php $theme_instructions = '<a href="http://codex.easypropertylistings.com.au/article/49-theme-setup">'. __( 'theme setup instructions can be found here' , 'easy-property-listings'  ) .'</a>'; ?>
					<?php $custom_template_link = '<a href="http://codex.easypropertylistings.com.au/article/121-how-to-create-your-own-custom-theme-templates-or-modify-existing-ones-using-the-theme-template-loader">'. __( 'custom templates' , 'easy-property-listings'  ) .'</a>'; ?>
					<h4 style="text-align: center;"><?php printf( __( 'Detailed %s.', 'easy-property-listings'  ) , $theme_instructions );?></h4>
					<h4 style="text-align: center;"><?php printf( __( 'How to create your own %s.', 'easy-property-listings'  ) , $custom_template_link );?></h4>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<div class="feature-section">

					<h2 class="about-description" style="text-align: center;"><?php _e( 'Stuck getting your theme to work?', 'easy-property-listings'  );?></h2>
					<p style="text-align: center;"><?php _e( 'Not all themes follow modern WordPress coding standards and these may take a little more time and experience to get working. If you just can not get it to work, visit', 'easy-property-listings'  );?> <a href="https://easypropertylistings.com.au/support-ticket/"><?php _e( 'premium support', 'easy-property-listings'  );?></a> <?php _e( 'and fill out a theme support request.', 'easy-property-listings'  );?></p>

					<p style="text-align:center;"><?php _e( 'If the theme is available in the WordPress.org theme directory let us know the theme name and URL where we can download it in your support ticket. If its a premium theme or are in a hurry submit a priority support ticket along with the theme in a zip or a download link to it on a file sharing site like Dropbox.', 'easy-property-listings'  );?></p>

				</div>

			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<h2 id="guide-help" class="about-description" style="text-align: center;"><?php _e( 'Need Help?', 'easy-property-listings'  );?></h2>

				<div class="feature-section col three-col">
					<div class="col">
						<h4><?php _e( 'Premium Support','easy-property-listings'  );?></h4>
						<p><?php echo $link = sprintf( __( 'We do our best to provide the best support we can. If you encounter a problem or have a question, post a question in the <a href="%s">support forums</a>.', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) );?></p>
					</div>

					<div class="col">
						<h4><?php _e( 'Need Even Faster Support?', 'easy-property-listings'  );?></h4>
						<p><?php _e( '<a href="https://easypropertylistings.com.au/support-ticket/">Priority Support forums</a> are there for customers that need faster and/or more in-depth assistance.', 'easy-property-listings'  );?></p>
					</div>

					<div class="col last-feature">
						<h4><?php _e( 'Documentation and Short Codes','easy-property-listings'  );?></h4>
						<p><?php _e( 'Read the','easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/"><?php _e( 'documentation','easy-property-listings'  );?></a> <?php _e( ' and instructions on how to use the included','easy-property-listings'  );?> <a href="http://codex.easypropertylistings.com.au/category/25-shortcodes"><?php _e( 'shortcodes','easy-property-listings'  );?></a>.</p>
					</div>
				</div>
			</div>

			<hr/>

			<div class="changelog headline-feature epl-clearfix">

				<div class="feature-section col two-col">
					<div class="col">
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
					<div class="col last-feature">
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
