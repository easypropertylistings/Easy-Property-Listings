<?php
/*
 * Plugin Name: Easy Property Listings
 * Plugin URI: http://www.easypropertylistings.com.au
 * Description:  The complete real estate platform for today's agent to list property using any theme for WordPress fast, easy and free. Just enable the listing types you need add some properties, tweak your settings and you're done. Extend the core with cool dynamic add-on extensions that give your visitors more reasons to come back.
 * Author: Merv Barrett
 * Author URI: http://www.realestateconnected.com.au
 * Version: 1.3 (dev Beta 4)
 * Text Domain: epl
 * Domain Path: languages
 *
 * Easy Property Listings is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Easy Property Listings is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Property Listings. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EPL
 * @category Core
 * @author Merv Barrett
 * @version 1.2
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !session_id() ) {
	session_start();
}

if ( ! class_exists( 'Easy_Property_Listings' ) ) :
	/*
	 * Main Easy_Property_Listings Class
	 *
	 * @since 1.0
	 */
	final class Easy_Property_Listings {
		
		/*
		 * @var Easy_Property_Listings The one true Easy_Property_Listings
		 * @since 1.0
		 */
		private static $instance;
	
		/*
		 * Main Easy_Property_Listings Instance
		 *
		 * Insures that only one instance of Easy_Property_Listings exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @uses Easy_Property_Listings::includes() Include the required files
		 * @see EPL()
		 * @return The one true Easy_Property_Listings
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Easy_Property_Listings ) ) {
				self::$instance = new Easy_Property_Listings;
				self::$instance->hooks();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				update_option('epl_running',true);
				$outdated = get_option('epl_outdated_extensions');
			}
			return self::$instance;

		}
		
		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			// activation
			add_action( 'admin_init', array( $this, 'check_installed_extensions_compatibility' ) );
		}
		
		/**
		 * Check if all installed extensions are compatible with current version of the plugin
		 * @access public
		 * @since 1.3.1
		 * @return void
		 */
		function check_installed_extensions_compatibility() {
			$outdated = array();
			$activated_plugins = wp_get_active_and_valid_plugins();
			foreach($activated_plugins as $activated_plugin) {
			
				$plugin_data = get_plugin_data($activated_plugin);
				$compatible_extensions = $this->compatible_extensions ();
				if(array_key_exists($plugin_data['Name'],$compatible_extensions)) {
					if (version_compare($plugin_data['Version'], $compatible_extensions[$plugin_data['Name']]) >= 0) {
						// extension is compatible with current version of epl
					} else {
						$outdated[$plugin_data['Name']] = $plugin_data['Version'];
					}
				}
			}
			if(count($outdated)>0) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
				
			}
			update_option('epl_outdated_extensions',$outdated);

		}

		function compatible_extensions () {
			$extensions = array(
				'Easy Property Listings - Listing Alerts'					=>	'1.0.1',
				'Easy Property Listings - Listing Unlimited'				=>	'1.1',
				'Easy Property Listings - Location Profiles'				=>	'1.1.6',
				'Easy Property Listings - 1Form Integration'				=>	'1.1.2',
				'Easy Property Listings - Advanced Mapping'					=>	'1.1.6',
				'Easy Property Listings - Award Manager'					=>	'1.1.1',
				'Easy Property Listings - Business Directory'				=>	'1.1.3',
				'Easy Property Listings - Inspect Real Estate'				=>	'1.1.2',
				'Easy Property Listings - Listing Templates'				=>	'1.2',
				'Easy Property Listings - Market Research'					=>	'1.0.0',
				'Easy Property Listings - Staff Directory'					=>	'1.2',
				'Easy Property Listings - Testimonial Management'			=>	'1.1.4',
			);
			return apply_filters('epl_compatible_extensions',$extensions);
		}
		
		
		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {
			$outdated = get_option('epl_outdated_extensions');
			if( isset($outdated) && !empty($outdated)){
					foreach($outdated as $extension_name	=>	$extension_version) {
						echo '<div class="error"><p>';
						_e('Currently installed version : '.$extension_version.' of '.$extension_name.' is outdated, you should consider updating it','epl');
						echo '</p></div>';
					}
			}
		}
		
		
		/*
		 * Setup plugin constants
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function setup_constants() {		
			// Plugin version
			if ( ! defined( 'EPL_PROPERTY_VER' ) ) {
				define( 'EPL_PROPERTY_VER', '1.3' );
			}
			
			// Plugin DB version
			if ( ! defined( 'EPL_PROPERTY_DB_VER' ) ) {
				define( 'EPL_PROPERTY_DB_VER', '1.3' );
			}
			
			// Current Page
			if ( ! defined( 'EPL_CURRENT_PAGE' ) ) {
				define("EPL_CURRENT_PAGE", basename($_SERVER['PHP_SELF']));
			}
			
			// Current Page
			if ( ! defined( 'EPL_PLUGIN_FILE' ) ) {
				define("EPL_PLUGIN_FILE", plugin_basename( __FILE__ ));
			}

			// Plugin Folder URL
			if ( ! defined( 'EPL_PLUGIN_URL' ) ) {
				define( 'EPL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			
			// Plugin Folder Path
			if ( ! defined( 'EPL_PLUGIN_PATH' ) ) {
				define( 'EPL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Sub-Directory Paths
			if ( ! defined( 'EPL_PATH_LIB' ) ) {
				define( 'EPL_PATH_LIB', EPL_PLUGIN_PATH . 'lib/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES' ) ) {
				define( 'EPL_PATH_TEMPLATES', EPL_PATH_LIB . 'templates/' );
			}
			if ( ! defined( 'EPL_COMPATABILITY' ) ) {
				define( 'EPL_COMPATABILITY', EPL_PATH_LIB . 'compatibility/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES_CONTENT' ) ) {
				define( 'EPL_PATH_TEMPLATES_CONTENT', EPL_PATH_TEMPLATES . 'content/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES_POST_TYPES' ) ) {
				define( 'EPL_PATH_TEMPLATES_POST_TYPES', EPL_PATH_TEMPLATES . 'themes/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT' ) ) {
				define( 'EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT', EPL_PATH_TEMPLATES_POST_TYPES . 'default/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES' ) ) {
				define( 'EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES', EPL_PATH_TEMPLATES_POST_TYPES . 'ithemes-builder/' );
			}
			
			if ( ! defined( 'EPL_PATH_TEMPLATES_POST_TYPES_GENESIS' ) ) {
				define( 'EPL_PATH_TEMPLATES_POST_TYPES_GENESIS', EPL_PATH_TEMPLATES_POST_TYPES . 'genesis/' );
			}
		}

		/*
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
		
			global $epl_settings;
			
			require_once EPL_PATH_LIB . 'includes/register-settings.php';
			$epl_settings = epl_get_settings();
		
			require_once EPL_PATH_LIB . 'includes/functions.php';
			require_once EPL_PATH_LIB . 'includes/options-global.php';
			require_once EPL_PATH_LIB . 'includes/formatting.php';
			require_once EPL_PATH_LIB . 'includes/plugins.php';

			require_once EPL_PATH_LIB . 'assets/assets.php';
			
			// Activate post types based on settings
			if(isset($epl_settings['activate_post_types'])) {
				$epl_activated_post_types = $epl_settings['activate_post_types'];
			} else {
				$epl_activated_post_types = '';
			}
			
			if( is_array( $epl_activated_post_types ) ) {
				foreach ( $epl_activated_post_types as $key => $value) {
					switch ( $value ) {
					
						case 'property' :
							require_once EPL_PATH_LIB . 'post-types/post-type-property.php';
							break;
						
						case 'land' :
							require_once EPL_PATH_LIB . 'post-types/post-type-land.php';
							break;
						
						case 'rental' :
							require_once EPL_PATH_LIB . 'post-types/post-type-rental.php';
							break;
						
						case 'rural' :
							require_once EPL_PATH_LIB . 'post-types/post-type-rural.php';
							break;
						
						case 'business' :
							require_once EPL_PATH_LIB . 'post-types/post-type-business.php';
							break;
						
						case 'commercial' :
							require_once EPL_PATH_LIB . 'post-types/post-type-commercial.php';
							break;
						
						case 'commercial_land' :
							require_once EPL_PATH_LIB . 'post-types/post-type-commercial_land.php';
							break;
						
						default :
							break;

					}
				}
				
				require_once EPL_PATH_LIB . 'post-types/post-types.php';
			}

			require_once EPL_PATH_LIB . 'taxonomies/tax-location.php';
			require_once EPL_PATH_LIB . 'taxonomies/tax-features.php';
			require_once EPL_PATH_LIB . 'taxonomies/tax-business_listings.php';

			require_once EPL_PATH_LIB . 'widgets/widget-author.php';
			require_once EPL_PATH_LIB . 'widgets/widget-listing.php';
			require_once EPL_PATH_LIB . 'widgets/widget-listing-gallery.php';
			require_once EPL_PATH_LIB . 'widgets/widget-listing-search.php';
			require_once EPL_PATH_LIB . 'widgets/widget-dashboard-epl-status.php';

			require_once EPL_PATH_LIB . 'hooks/hooks.php';
						
			require_once EPL_PATH_LIB . 'includes/EPL_License_Handler.php';
			require_once EPL_PATH_LIB . 'includes/class-property-meta.php';
			require_once EPL_PATH_LIB . 'includes/class-author-meta.php';
			require_once EPL_PATH_LIB . 'includes/template-functions.php';
			if ( is_admin() ) {
				require_once EPL_PATH_LIB . 'includes/user.php';
				
				require_once EPL_PATH_LIB . 'menus/menus.php';
				require_once EPL_PATH_LIB . 'menus/menu-welcome.php';
		
				require_once EPL_PATH_LIB . 'meta-boxes/meta-boxes.php';
			} else {
				require_once EPL_PATH_LIB . 'templates/themes/themes.php';
			
				require_once EPL_PATH_LIB . 'includes/options-front-end.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-googlemap.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-listing.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-listing-search.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-listing-open.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-listing-category.php';
				require_once EPL_PATH_LIB . 'shortcodes/shortcode-listing-tax-feature.php';
				
				require_once EPL_PATH_LIB . 'hooks/hook-property-map.php';
				require_once EPL_PATH_LIB . 'hooks/hook-external-links.php';
				require_once EPL_PATH_LIB . 'hooks/hook-floorplan.php';
				require_once EPL_PATH_LIB . 'hooks/hook-mini-web.php';
				require_once EPL_PATH_LIB . 'hooks/hook-read-more.php';
			
			}
			
			require_once EPL_PATH_LIB . 'includes/install.php';
		}
		

		
		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.1.2
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$epl_lang_dir = EPL_PATH_LIB . 'languages/';
			$epl_lang_dir = apply_filters( 'epl_languages_directory', $epl_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'epl' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'epl', $locale );

			// Setup paths to current locale file
			$mofile_local  = $epl_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/epl/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/epl folder
				load_textdomain( 'epl', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/easy-property-listings/languages/ folder
				load_textdomain( 'epl', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'epl', false, $epl_lang_dir );
			}
		}
	}
endif; // End if class_exists check


/*
 * The main function responsible for returning the one true Easy_Property_Listings
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $epl = EPL(); ?>
 *
 * @since 1.0
 * @return object The one true Easy_Property_Listings Instance
 */
function EPL() {
	return Easy_Property_Listings::instance();
}

// Get EPL Running
EPL();
