<?php
/**
 * Custom Post Object
 *
 * @package     EPL
 * @subpackage  Classes/CPT
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_CPT Class
 *
 * @since 2.3
 */
class EPL_CPT {

	/**
	 * Post type name.
	 *
	 * @var string $post_type_name Holds the name of the post type.
	 */
	public $post_type_name;

	/**
	 * Holds the singular name of the post type. This is a human friendly
	 * name, capitalized with spaces assigned on __construct().
	 *
	 * @var string $singular Post type singular name.
	 */
	public $singular;

	/**
	 * Holds the plural name of the post type. This is a human friendly
	 * name, capitalized with spaces assigned on __construct().
	 *
	 * @var string $plural Singular post type name.
	 */
	public $plural;

	/**
	 * Post type slug. This is a robot friendly name, all lowercase and uses
	 * hyphens assigned on __construct().
	 *
	 * @var string $slug Holds the post type slug name.
	 */
	public $slug;

	/**
	 * User submitted options assigned on __construct().
	 *
	 * @var array $options Holds the user submitted post type options.
	 */
	public $options;

	/**
	 * Taxonomies
	 *
	 * @var array $taxonomies Holds an array of taxonomies associated with the post type.
	 */
	public $taxonomies;

	/**
	 * Taxonomy settings, an array of the taxonomies associated with the post
	 * type and their options used when registering the taxonomies.
	 *
	 * @var array $taxonomy_settings Holds the taxonomy settings.
	 */
	public $taxonomy_settings;

	/**
	 * Exisiting taxonomies to be registered after the post has been registered
	 *
	 * @var array $exisiting_taxonomies holds exisiting taxonomies
	 */
	public $exisiting_taxonomies;

	/**
	 * Taxonomy filters. Defines which filters are to appear on admin edit
	 * screen used in add_taxonmy_filters().
	 *
	 * @var array $filters Taxonomy filters.
	 */
	public $filters;

	/**
	 * Defines which columns are to appear on the admin edit screen used
	 * in add_admin_columns().
	 *
	 * @var array $columns Columns visible in admin edit screen.
	 */
	public $columns;

	/**
	 * User defined functions to populate admin columns.
	 *
	 * @var array $custom_populate_columns User functions to populate columns.
	 */
	public $custom_populate_columns;

	/**
	 * Sortable columns.
	 *
	 * @var array $sortable Define which columns are sortable on the admin edit screen.
	 */
	public $sortable;

	/**
	 * Textdomain used for translation. Use the set_textdomain() method to set a custom textdomain.
	 *
	 * @var string $textdomain Used for internationalising. Defaults to "cpt" without quotes.
	 */
	public $textdomain = 'easy-property-listings';

	/**
	 * Constructor
	 *
	 * Register a custom post type.
	 *
	 * @param mixed $post_type_names The name(s) of the post type, accepts (post type name, slug, plural, singular).
	 * @param array $options User submitted options.
	 */
	public function __construct( $post_type_names, $options = array() ) {

		// Check if post type names is a string or an array.
		if ( is_array( $post_type_names ) ) {

			// Add names to object.
			$names = array(
				'singular',
				'plural',
				'slug',
			);

			// Set the post type name.
			$this->post_type_name = $post_type_names['post_type_name'];

			// Cycle through possible names.
			foreach ( $names as $name ) {

				// If the name has been set by user.
				if ( isset( $post_type_names[ $name ] ) ) {

					// Use the user setting.
					$this->$name = $post_type_names[ $name ];

					// Else generate the name.
				} else {

					// Define the method to be used.
					$method = 'get_' . $name;

					// Generate the name.
					$this->$name = $this->$method();
				}
			}

			// Else the post type name is only supplied.
		} else {

			// Apply to post type name.
			$this->post_type_name = $post_type_names;

			// Set the slug name.
			$this->slug = $this->get_slug();

			// Set the plural name label.
			$this->plural = $this->get_plural();

			// Set the singular name label.
			$this->singular = $this->get_singular();
		}

		// Set the user submitted options to the object.
		$this->options = $options;

		// Register taxonomies.
		$this->add_action( 'init', array( &$this, 'register_taxonomies' ) );

		// Register the post type.
		$this->add_action( 'init', array( &$this, 'register_post_type' ) );

		// Register exisiting taxonomies.
		$this->add_action( 'init', array( &$this, 'register_exisiting_taxonomies' ) );

		// Add taxonomy to admin edit columns.
		$this->add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( &$this, 'add_admin_columns' ) );

		// Populate the taxonomy columns with the posts terms.
		$this->add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( &$this, 'populate_admin_columns' ), 10, 2 );

		// Add filter select option to admin edit.
		$this->add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );
	}

	/**
	 * Get
	 *
	 * Helper function to get an object variable.
	 *
	 * @param string $var The variable you would like to retrieve.
	 * @return mixed Returns the value on success, boolean false whe it fails.
	 */
	public function get( $var ) {

		// If the variable exists.
		if ( $this->$var ) {

			// On success return the value.
			return $this->$var;

		} else {

			// On fail return false.
			return false;
		}
	}

	/**
	 * Set
	 *
	 * Helper function used to set an object variable. Can overwrite existsing
	 * variables or create new ones. Cannot overwrite reserved variables.
	 *
	 * @param mixed $var The variable you would like to create/overwrite.
	 * @param mixed $value The value you would like to set to the variable.
	 */
	public function set( $var, $value ) {

		// An array of reserved variables that cannot be overwritten.
		$reserved = array(
			'config',
			'post_type_name',
			'singular',
			'plural',
			'slug',
			'options',
			'taxonomies',
		);

		// If the variable is not a reserved variable.
		if ( ! in_array( $var, $reserved, true ) ) {

			// Write variable and value.
			$this->$var = $value;
		}
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
	 * Get slug
	 *
	 * Creates an url friendly slug.
	 *
	 * @param  string $name Name to slugify.
	 * @return string $name Returns the slug.
	 */
	public function get_slug( $name = null ) {

		// If no name set use the post type name.
		if ( ! isset( $name ) ) {

			$name = $this->post_type_name;
		}

		// Name to lower case.
		$name = strtolower( $name );

		// Replace spaces with hyphen.
		$name = str_replace( ' ', '-', $name );

		// Replace underscore with hyphen.
		$name = str_replace( '_', '-', $name );

		return $name;
	}

	/**
	 * Get plural
	 *
	 * Returns the friendly plural name.
	 *
	 *    ucwords      capitalize words
	 *    strtolower   makes string lowercase before capitalizing
	 *    str_replace  replace all instances of _ to space
	 *
	 * @param  string $name The slug name you want to pluralize.
	 * @return string the friendly pluralized name.
	 */
	public function get_plural( $name = null ) {

		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {

			$name = $this->post_type_name;
		}

		// Return the plural name. Add 's' to the end.
		return $this->get_human_friendly( $name ) . 's';
	}

	/**
	 * Get singular
	 *
	 * Returns the friendly singular name.
	 *
	 *    ucwords      capitalize words
	 *    strtolower   makes string lowercase before capitalizing
	 *    str_replace  replace all instances of _ to space
	 *
	 * @param string $name The slug name you want to unpluralize.
	 * @return string The friendly singular name.
	 */
	public function get_singular( $name = null ) {

		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {

			$name = $this->post_type_name;

		}

		// Return the string.
		return $this->get_human_friendly( $name );
	}

	/**
	 * Get human friendly
	 *
	 * Returns the human friendly name.
	 *
	 *    ucwords      capitalize words
	 *    strtolower   makes string lowercase before capitalizing
	 *    str_replace  replace all instances of hyphens and underscores to spaces
	 *
	 * @param string $name The name you want to make friendly.
	 * @return string The human friendly name.
	 */
	public function get_human_friendly( $name = null ) {

		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {

			$name = $this->post_type_name;
		}

		// Return human friendly name.
		return ucwords( strtolower( str_replace( '-', ' ', str_replace( '_', ' ', $name ) ) ) );
	}

	/**
	 * Register Post Type
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type() {

		// Friendly post type names.
		$plural   = $this->plural;
		$singular = $this->singular;
		$slug     = $this->slug;

		// Default labels.
		$labels = array(

			/*
			TODO: %s Strings should have translatable content.
			*/

			/* Translators: %s is the post type name. */
			'name'               => $plural,
			/* Translators: %s is the post type name. */
			'singular_name'      => $singular,
			/* Translators: %s is the post type name. */
			'menu_name'          => $plural,
			/* Translators: %s is the post type name. */
			'all_items'          => $plural,
			/* Translators: %s is the post type name. */
			'add_new'            => __( 'Add New', 'easy-property-listings' ),
			/* Translators: %s is the post type name. */
			'add_new_item'       => sprintf( __( 'Add New %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is the post type name. */
			'edit_item'          => sprintf( __( 'Edit %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is the post type name. */
			'new_item'           => sprintf( __( 'New %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is the post type name. */
			'view_item'          => sprintf( __( 'View %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is the post type name. */
			'search_items'       => sprintf( __( 'Search %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is the post type name. */
			'not_found'          => sprintf( __( 'No %s found', 'easy-property-listings' ), $plural ),
			/* Translators: %s is the post type name. */
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'easy-property-listings' ), $plural ),
			/* Translators: %s is the post type name. */
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'easy-property-listings' ), $singular ),
		);

		// Default options.
		$defaults = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => '26.87',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ),
		);

		// Merge user submitted options with defaults.
		$options = array_replace_recursive( $defaults, $this->options );

		// Set the object options as full options passed.
		$this->options = $options;

		// Check that the post type doesn't already exist.
		if ( ! post_type_exists( $this->post_type_name ) ) {

			// Register the post type.
			register_post_type( $this->post_type_name, $options );
		}
	}

	/**
	 * Register taxonomy
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy
	 *
	 * @param string $taxonomy_names The slug for the taxonomy.
	 * @param array  $options Taxonomy options.
	 */
	public function register_taxonomy( $taxonomy_names, $options = array() ) {

		// Post type defaults to $this post type if unspecified.
		$post_type = $this->post_type_name;

		// An array of the names required excluding taxonomy_name.
		$names = array(
			'singular',
			'plural',
			'slug',
		);

		// If an array of names are passed.
		if ( is_array( $taxonomy_names ) ) {

			// Set the taxonomy name.
			$taxonomy_name = $taxonomy_names['taxonomy_name'];

			// Cycle through possible names.
			foreach ( $names as $name ) {

				// If the user has set the name.
				if ( isset( $taxonomy_names[ $name ] ) ) {

					// Use user submitted name.
					$$name = $taxonomy_names[ $name ];

					// Else generate the name.
				} else {

					// Define the function to be used.
					$method = 'get_' . $name;

					// Generate the name.
					$$name = $this->$method( $taxonomy_name );

				}
			}

			// Else if only the taxonomy_name has been supplied.
		} else {

			// Create user friendly names.
			$taxonomy_name = $taxonomy_names;
			$singular      = $this->get_singular( $taxonomy_name );
			$plural        = $this->get_plural( $taxonomy_name );
			$slug          = $this->get_slug( $taxonomy_name );

		}

		// Default labels.
		$labels = array(
			/* Translators: %s is taxonomy label. */
			'name'                       => $plural,
			/* Translators: %s is taxonomy label. */
			'singular_name'              => $singular,
			/* Translators: %s is taxonomy label. */
			'menu_name'                  => $plural,
			/* Translators: %s is taxonomy label. */
			'all_items'                  => sprintf( __( 'All %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'edit_item'                  => sprintf( __( 'Edit %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is taxonomy label. */
			'view_item'                  => sprintf( __( 'View %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is taxonomy label. */
			'update_item'                => sprintf( __( 'Update %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is taxonomy label. */
			'add_new_item'               => sprintf( __( 'Add New %s', 'easy-property-listings' ), $singular ),
			/* Translators: %s is taxonomy label. */
			'new_item_name'              => sprintf( __( 'New %s Name', 'easy-property-listings' ), $singular ),
			/* Translators: %s is taxonomy label. */
			'parent_item'                => sprintf( __( 'Parent %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'search_items'               => sprintf( __( 'Search %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'popular_items'              => sprintf( __( 'Popular %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'separate_items_with_commas' => sprintf( __( 'Seperate %s with commas', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'choose_from_most_used'      => sprintf( __( 'Choose from most used %s', 'easy-property-listings' ), $plural ),
			/* Translators: %s is taxonomy label. */
			'not_found'                  => sprintf( __( 'No %s found', 'easy-property-listings' ), $plural ),
		);

		// Default options.
		$defaults = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug' => $slug,
			),
		);

		// Merge default options with user submitted options.
		$options = array_replace_recursive( $defaults, $options );

		// Add the taxonomy to the object array, this is used to add columns and filters to admin panel.
		$this->taxonomies[] = $taxonomy_name;

		// Create array used when registering taxonomies.
		$this->taxonomy_settings[ $taxonomy_name ] = $options;

	}

	/**
	 * Register taxonomies
	 *
	 * Cycles through taxonomies added with the class and registers them.
	 */
	public function register_taxonomies() {

		if ( is_array( $this->taxonomy_settings ) ) {

			// Foreach taxonomy registered with the post type.
			foreach ( $this->taxonomy_settings as $taxonomy_name => $options ) {

				// Register the taxonomy if it doesn't exist.
				if ( ! taxonomy_exists( $taxonomy_name ) ) {

					// Register the taxonomy with WordPress.
					register_taxonomy( $taxonomy_name, $this->post_type_name, $options );

				} else {

					// If taxonomy exists, register it later with register_exisiting_taxonomies.
					$this->exisiting_taxonomies[] = $taxonomy_name;
				}
			}
		}
	}

	/**
	 * Register Exisiting Taxonomies
	 *
	 * Cycles through exisiting taxonomies and registers them after the post type has been registered
	 */
	public function register_exisiting_taxonomies() {

		if ( is_array( $this->exisiting_taxonomies ) ) {
			foreach ( $this->exisiting_taxonomies as $taxonomy_name ) {
				register_taxonomy_for_object_type( $taxonomy_name, $this->post_type_name );
			}
		}
	}

	/**
	 * Add admin columns
	 *
	 * Adds columns to the admin edit screen. Function is used with add_action
	 *
	 * @param array $columns Columns to be added to the admin edit screen.
	 * @return array
	 */
	public function add_admin_columns( $columns ) {

		// If no user columns have been specified, add taxonomies.
		if ( ! isset( $this->columns ) ) {

			$new_columns = array();

			// determine which column to add custom taxonomies after.
			if ( in_array( 'post_tag', $this->taxonomies, true ) || 'post' === $this->post_type_name ) {
				$after = 'tags';
			} elseif ( in_array( 'category', $this->taxonomies, true ) || 'post' === $this->post_type_name ) {
				$after = 'categories';
			} elseif ( post_type_supports( $this->post_type_name, 'author' ) ) {
				$after = 'author';
			} else {
				$after = 'title';
			}

			// Foreach exisiting columns.
			foreach ( $columns as $key => $title ) {

				// Add exisiting column to the new column array.
				$new_columns[ $key ] = $title;

				// we want to add taxonomy columns after a specific column.
				if ( $key === $after ) {

					// If there are taxonomies registered to the post type.
					if ( is_array( $this->taxonomies ) ) {

						// Create a column for each taxonomy.
						foreach ( $this->taxonomies as $tax ) {

							// WordPress adds Categories and Tags automatically, ignore these.
							if ( 'category' !== $tax && 'post_tag' !== $tax ) {
								// Get the taxonomy object for labels.
								$taxonomy_object = get_taxonomy( $tax );

								// Column key is the slug, value is friendly name.
								/* Translators: %s is taxonomy label. */
								$new_columns[ $tax ] = $taxonomy_object->labels->name;
							}
						}
					}
				}
			}

			// Overide with new columns.
			$columns = $new_columns;

		} else {

			// Use user submitted columns, these are defined using the object columns() method.
			$columns = $this->columns;
		}

		return $columns;
	}

	/**
	 * Populate admin columns
	 *
	 * Populate custom columns on the admin edit screen.
	 *
	 * @param string  $column The name of the column.
	 * @param integer $post_id The post ID.
	 */
	public function populate_admin_columns( $column, $post_id ) {

		// Get WordPress $post object.
		global $post;

		// Determine the column.
		switch ( $column ) {

			// If column is a taxonomy associated with the post type.
			case ( taxonomy_exists( $column ) ):
				// Get the taxonomy for the post.
				$terms = get_the_terms( $post_id, $column );

				// If we have terms.
				if ( ! empty( $terms ) ) {

					$output = array();

					// Loop through each term, linking to the 'edit posts' page for the specific term.
					foreach ( $terms as $term ) {

						// Output is an array of terms associated with the post.
						$output[] = sprintf(

							// Define link.
							'<a href="%s">%s</a>',
							// Create filter url.
							esc_url(
								add_query_arg(
									array(
										'post_type' => $post->post_type,
										$column     => $term->slug,
									),
									'edit.php'
								)
							),
							// Create friendly term name.
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $column, 'display' ) )
						);

					}

					// Join the terms, separating them with a comma.
					echo join( ', ', wp_kses_post( $output ) );

					// If no terms found.
				} else {

					// Get the taxonomy object for labels.
					$taxonomy_object = get_taxonomy( $column );

					// Echo no terms.
					// translators: tax label.
					printf( esc_html__( 'No %s', 'easy-property-listings' ), esc_attr( $taxonomy_object->labels->name ) );
				}

				break;

			// If column is for the post ID.
			case 'post_id':
				echo esc_attr( $post->ID );

				break;

			// if the column is prepended with 'meta_', this will automagically retrieve the meta values and display them.
			case ( preg_match( '/^meta_/', $column ) ? true : false ):
				// Meta_book_author (meta key = book_author).
				$x = substr( $column, 5 );

				$meta = get_post_meta( $post->ID, $x );

				echo join( ', ', wp_kses_post( $meta ) );

				break;

			// If the column is post thumbnail.
			case 'icon':
				// Create the edit link.
				$link = esc_url(
					add_query_arg(
						array(
							'post'   => $post->ID,
							'action' => 'edit',
						),
						'post.php'
					)
				);

				// If it post has a featured image.
				if ( has_post_thumbnail() ) {

					// Display post featured image with edit link.
					echo '<a href="' . esc_url( $link ) . '">';
						the_post_thumbnail( array( 60, 60 ) );
					echo '</a>';

				} else {

					// Display default media image with link.
					echo '<a href="' . esc_url( $link ) . '"><img src="' . esc_url( site_url( '/wp-includes/images/crystal/default.png' ) ) . '" alt="' . esc_attr( $post->post_title ) . '" /></a>';

				}

				break;

			// Default case checks if the column has a user function, this is most commonly used for custom fields.
			default:
				// If there are user custom columns to populate.
				if ( isset( $this->custom_populate_columns ) && is_array( $this->custom_populate_columns ) ) {

					// If this column has a user submitted function to run.
					if ( isset( $this->custom_populate_columns[ $column ] ) && is_callable( $this->custom_populate_columns[ $column ] ) ) {

						// Run the function.
						$this->custom_populate_columns[ $column ]( $column, $post );

					}
				}

				break;
		} // end switch
	}

	/**
	 * Filters
	 *
	 * User function to define which taxonomy filters to display on the admin page.
	 *
	 * @param array $filters An array of taxonomy filters to display.
	 */
	public function filters( $filters = array() ) {

		$this->filters = $filters;
	}

	/**
	 *  Add taxtonomy filters
	 *
	 * Creates select fields for filtering posts by taxonomies on admin edit screen.
	 */
	public function add_taxonomy_filters() {

		global $typenow;
		global $wp_query;

		// Must set this to the post type you want the filter(s) displayed on.
		if ( $typenow === $this->post_type_name ) {

			// If custom filters are defined use those.
			if ( is_array( $this->filters ) ) {

				$filters = $this->filters;

				// Else default to use all taxonomies associated with the post.
			} else {

				$filters = $this->taxonomies;
			}

			if ( ! empty( $filters ) ) {

				// Foreach of the taxonomies we want to create filters for...
				foreach ( $filters as $tax_slug ) {

					// ...object for taxonomy, doesn't contain the terms.
					$tax = get_taxonomy( $tax_slug );

					// Get taxonomy terms and order by name.
					$args = array(
						'orderby'    => 'name',
						'hide_empty' => false,
					);

					// Get taxonomy terms.
					$terms = get_terms( $tax_slug, $args );

					// If we have terms.
					if ( $terms ) {

						// Set up select box.
						printf( ' &nbsp;<select name="%s" class="postform">', esc_attr( $tax_slug ) );

						// Default show all.
						// translators: tax label.
						printf( '<option value="0">%s</option>', sprintf( esc_html__( 'Show all %s', 'easy-property-listings' ), esc_attr( $tax->label ) ) );

						// Foreach term create an option field...
						foreach ( $terms as $term ) {

							// ...if filtered by this term make it selected.
							if ( isset( $_GET[ $tax_slug ] ) && sanitize_title( wp_unslash( $_GET[ $tax_slug ] ) ) === $term->slug ) { // phpcs:ignore
								// translators: tax slug, tax name, tax count.
								printf( '<option value="%s" selected="selected">%s (%s)</option>', esc_attr( $term->slug ), esc_attr( $term->name ), esc_attr( $term->count ) );

								// ...create option for taxonomy.
							} else {
								// translators: tax slug, tax name, tax count.
								printf( '<option value="%s">%s (%s)</option>', esc_attr( $term->slug ), esc_attr( $term->name ), esc_attr( $term->count ) );
							}
						}
						// End the select field.
						print( '</select>&nbsp;' );
					}
				}
			}
		}
	}

	/**
	 * Columns
	 *
	 * Choose columns to be displayed on the admin edit screen.
	 *
	 * @param array $columns An array of columns to be displayed.
	 */
	public function columns( $columns ) {

		// If columns is set.
		if ( isset( $columns ) ) {

			// Assign user submitted columns to object.
			$this->columns = $columns;

		}
	}

	/**
	 * Populate columns
	 *
	 * Define what and how to populate a speicific admin column.
	 *
	 * @param string $column_name The name of the column to populate.
	 * @param string $function An anonyous function to run when populating the column.
	 */
	public function populate_column( $column_name, $function ) {

		$this->custom_populate_columns[ $column_name ] = $function;

	}

	/**
	 * Sortable
	 *
	 * Define what columns are sortable in the admin edit screen.
	 *
	 * @param array $columns An array of columns that are sortable.
	 */
	public function sortable( $columns = array() ) {

		// Assign user defined sortable columns to object variable.
		$this->sortable = $columns;

		// Run filter to make columns sortable.
		$this->add_filter( 'manage_edit-' . $this->post_type_name . '_sortable_columns', array( &$this, 'make_columns_sortable' ) );

		// Run action that sorts columns on request.
		$this->add_action( 'load-edit.php', array( &$this, 'load_edit' ) );
	}

	/**
	 * Make columns sortable
	 *
	 * Internal function that adds user defined sortable columns to WordPress default columns.
	 *
	 * @param array $columns Columns to be sortable.
	 *
	 * @return array
	 */
	public function make_columns_sortable( $columns ) {

		// For each sortable column.
		foreach ( $this->sortable as $column => $values ) {

			// Make an array to merge into WordPress sortable columns.
			$sortable_columns[ $column ] = $values[0];
		}

		// Merge sortable columns array into WordPress sortable columns.
		$columns = array_merge( $sortable_columns, $columns );

		return $columns;
	}

	/**
	 * Load edit
	 *
	 * Sort columns only on the edit.php page when requested.
	 *
	 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/request
	 */
	public function load_edit() {

		// Run filter to sort columns when requested.
		$this->add_filter( 'request', array( &$this, 'sort_columns' ) );

	}

	/**
	 * Sort columns
	 *
	 * Internal function that sorts columns on request.
	 *
	 * @see load_edit()
	 *
	 * @param array $vars The query vars submitted by user.
	 * @return array A sorted array.
	 */
	public function sort_columns( $vars ) {

		// Cycle through all sortable columns submitted by the user.
		foreach ( $this->sortable as $column => $values ) {

			// Retrieve the meta key from the user submitted array of sortable columns.
			$meta_key = $values[0];

			// If the meta_key is a taxonomy.
			if ( taxonomy_exists( $meta_key ) ) {

				// Sort by taxonomy.
				$key = 'taxonomy';

			} else {

				// else by meta key.
				$key = 'meta_key';
			}

			// If the optional parameter is set and is set to true.
			if ( isset( $values[1] ) && true === $values[1] ) {

				// Vaules needed to be ordered by integer value.
				$orderby = 'meta_value_num';

			} else {

				// Values are to be order by string value.
				$orderby = 'meta_value';
			}

			// Check if we're viewing this post type.
			if ( isset( $vars['post_type'] ) && $this->post_type_name === $vars['post_type'] ) {

				// find the meta key we want to order posts by.
				if ( isset( $vars['orderby'] ) && $meta_key === $vars['orderby'] ) {

					// Merge the query vars with our custom variables.
					$vars = array_merge(
						$vars,
						array(
							'meta_key' => $meta_key, // phpcs:ignore
							'orderby'  => $orderby,
						)
					);
				}
			}
		}
		return $vars;
	}

	/**
	 * Set menu icon
	 *
	 * Use this function to set the menu icon in the admin dashboard. Since WordPress v3.8
	 * dashicons are used. For more information see @link http://melchoyce.github.io/dashicons/
	 *
	 * @param string $icon dashicon name.
	 */
	public function menu_icon( $icon = 'dashicons-admin-page' ) {

		if ( is_string( $icon ) && stripos( $icon, 'dashicons' ) !== false ) {

			$this->options['menu_icon'] = $icon;

		} else {

			// Set a default menu icon.
			$this->options['menu_icon'] = 'dashicons-admin-page';
		}
	}

	/**
	 * Set textdomain
	 *
	 * @param string $textdomain Textdomain used for translation.
	 */
	public function set_textdomain( $textdomain ) {
		$this->textdomain = 'easy-property-listings';
	}
}
