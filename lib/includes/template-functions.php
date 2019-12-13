<?php
/**
 * Template Functions
 *
 * @package     EPL
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.ValidVariableName
// phpcs:disable WordPress.Security.NonceVerification

/**
 * Featured Image on archive template now loading through filter
 *
 * @param array $post Post object.
 *
 * @since 2.2
 */
function epl_reset_property_object( $post ) {

	global $epl_author, $epl_author_secondary;

	if ( ! is_epl_post() ) {
		return;
	}

	global $property;
	$property = new EPL_Property_Meta( $post );
	$ID = epl_listing_has_primary_agent(); //phpcs:ignore
	if ( $ID ) {

		$epl_author = new EPL_Author_meta( $ID ); //phpcs:ignore

	} else {

		$epl_author = new EPL_Author_meta( $post->post_author );
	}

	$SEC_ID = epl_listing_has_secondary_author();

	if ( $SEC_ID ) {
		$epl_author_secondary = new EPL_Author_meta( $SEC_ID );
	}
}
add_action( 'the_post', 'epl_reset_property_object' );

/**
 * Make $property global available for hooks before the_post
 *
 * @since      2.2
 */
function epl_create_property_object() {

	global $post,$property,$epl_author,$epl_author_secondary;

	if ( is_author() ) {
		$author_id  = get_query_var( 'author' );
		$epl_author = new EPL_Author_meta( $author_id );
	}
	if ( is_null( $post ) ) {
		return;
	}
	$epl_author = new EPL_Author_meta( $post->post_author );

	if ( is_epl_post() ) {
		$property = new EPL_Property_Meta( $post );
		$ID       = epl_listing_has_secondary_author();
		if ( $ID ) {
			$epl_author_secondary = new EPL_Author_meta( $ID );
		}
	}
}

add_action( 'wp', 'epl_create_property_object' );

/**
 * Selecting Card Display Style
 *
 * @since 1.0
 * @since 3.4.4 Removed default template check for single templates as this caused incorrect templates to load in some cases.
 */
function epl_property_single() {
	global $epl_settings;

	$action_check = has_action( 'epl_single_template' );
	if ( ! empty( $action_check ) ) {
		do_action( 'epl_single_template' );
	} else {
		epl_property_single_default();
	}
}
add_action( 'epl_property_single', 'epl_property_single', 10, 1 );

/**
 * Featured Image template now loading through filter
 *
 * @param      string  $image_size   The image size.
 * @param      string  $image_class  The image class.
 * @param      boolean $link         The link.
 *
 * @since      1.2.0
 * @since      3.4.8 Corrected missing parameter count to 3.
 */
function epl_property_featured_image( $image_size = 'index_thumbnail', $image_class = 'index-thumbnail', $link = true ) {

	/**
	 * Filter: Allow user or extension to enable or disable link behaviour on featured image.
	 */
	$link = apply_filters( 'epl_property_featured_image_link', $link );

	if ( has_post_thumbnail() ) { ?>
		<div class="entry-image">
			<div class="epl-featured-image it-featured-image">
				<?php if ( true === $link ) { ?>
					<a href="<?php the_permalink(); ?>">
				<?php } ?>
						<?php the_post_thumbnail( $image_size, array( 'class' => $image_class ) ); ?>
				<?php if ( true === $link ) { ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<?php
	}

}
add_action( 'epl_property_featured_image', 'epl_property_featured_image', 10, 3 );
add_action( 'epl_single_featured_image', 'epl_property_featured_image', 10, 3 );

/**
 * Featured Image on archive template now loading through filter
 *
 * @since      2.2
 *
 * @param      string  $image_size   The image size.
 * @param      string  $image_class  The image class.
 * @param      boolean $link         The link.
 */
function epl_property_archive_featured_image( $image_size = 'epl-image-medium-crop', $image_class = 'teaser-left-thumb', $link = true ) {

	if ( empty( $image_size ) ) {
		$image_size = 'epl-image-medium-crop';
	}

	/**
	 * Filter: Allow user or extension to enable or disable link behaviour on archive image.
	 */
	$link = apply_filters( 'epl_property_archive_featured_image_link', $link );

	if ( has_post_thumbnail() ) {
		?>
		<div class="epl-archive-entry-image">
			<?php if ( true === $link ) { ?>
				<a href="<?php the_permalink(); ?>">
			<?php } ?>
					<div class="epl-blog-image">
						<div class="epl-stickers-wrapper">
							<?php echo wp_kses_post( epl_get_price_sticker() ); ?>
						</div>
						<?php the_post_thumbnail( $image_size, array( 'class' => $image_class ) ); ?>
					</div>
			<?php if ( true === $link ) { ?>
				</a>
			<?php } ?>
		</div>
		<?php
	}

}
add_action( 'epl_property_archive_featured_image', 'epl_property_archive_featured_image', 10, 3 );

/**
 * Featured Image in widgets
 *
 * @since      2.2
 *
 * @param      string  $image_size   The image size.
 * @param      string  $image_class  The image class.
 * @param      boolean $link         The link.
 */
function epl_property_widgets_featured_image( $image_size = 'epl-image-medium-crop', $image_class = 'teaser-left-thumb', $link = true ) {

	if ( has_post_thumbnail() ) {
		?>
		<div class="epl-archive-entry-image">
			<?php if ( $link ) { ?>
				<a href="<?php the_permalink(); ?>">
			<?php } ?>
					<div class="epl-blog-image">
						<?php the_post_thumbnail( $image_size, array( 'class' => $image_class ) ); ?>
					</div>
			<?php if ( $link ) { ?>
				</a>
			<?php } ?>
		</div>
		<?php
	}

}
add_action( 'epl_property_widgets_featured_image', 'epl_property_widgets_featured_image', 10, 3 );

/**
 * Single Listing Templates
 *
 * @since      1.0
 */
function epl_property_single_default() {

	global $epl_settings;
	if ( isset( $epl_settings['epl_feeling_lucky'] ) && 'on' === $epl_settings['epl_feeling_lucky'] ) {

		epl_get_template_part( 'content-listing-single-compatibility.php' );

	} else {
		$single_tpl = 'content-listing-single.php';
		$single_tpl = apply_filters( 'epl_property_single_default', $single_tpl );
		epl_get_template_part( $single_tpl );
	}
}

/**
 * Template Path
 *
 * @since      2.0
 *
 * @return string
 */
function epl_get_content_path() {
	return apply_filters( 'epl_templates_base_path', EPL_PATH_TEMPLATES_CONTENT );
}

/**
 * Template Fallback Path
 *
 * @return mixed|void
 * @since      3.0
 */
function epl_get_fallback_content_path() {
	return apply_filters( 'epl_templates_fallback_base_path', EPL_PATH_TEMPLATES_CONTENT );
}

/**
 * Attempts to load templates in order of priority
 *
 * @since 3.0
 * @param string $template Template name.
 * @param array  $arguments Options to pass to template.
 */
function epl_get_template_part( $template, $arguments = array() ) {

	$base_path = epl_get_content_path();
	$default   = $template;
	$find[]    = epl_template_path() . $template;
	$template  = locate_template( array_unique( $find ) );
	if ( ! $template ) {
		$template = $base_path . $default;
		if ( ! file_exists( $template ) ) {
			// fallback to core.
			$base_path = epl_get_fallback_content_path();
			$template  = $base_path . $default;
		}
	}
	if ( ! isset( $arguments['epl_author'] ) ) {
		global $epl_author;
	}

	foreach ( $arguments as $key => $val ) {
		${$key} = $val;
	}

	include $template;
}

/**
 * Modify the Excerpt length on Archive pages
 *
 * @since 1.0
 * @param string $length Excerpt word length.
 * @return int|string
 */
function epl_archive_custom_excerpt_length( $length ) {
	global $epl_settings;
	$excerpt = '';
	if ( ! empty( $epl_settings ) && isset( $epl_settings['display_excerpt_length'] ) ) {
		$excerpt = $epl_settings['display_excerpt_length'];
	}
	if ( ! empty( $excerpt ) ) {
		return 22;
	} else {
		return $excerpt;
	}
}

/**
 * Filter which listing status should not be displayed
 *
 * @return mixed|void
 * @since 3.1.20
 */
function epl_hide_listing_statuses() {

	return apply_filters( 'epl_hide_listing_statuses', array( 'withdrawn', 'offmarket' ) );
}

/**
 * Selecting Card Display Style
 *
 * Allows the use of one function where we can then select a different template
 * when needed
 *
 * @since 1.0.0
 * @since 3.4.4 Removed default template check for loop templates as this caused incorrect templates to load in some cases.
 *
 * @param string $template  The template.
 */
function epl_property_blog( $template = '' ) {

	if ( empty( $template ) || 'blog' === $template ) {
		$template = 'default';
	}
	$template = str_replace( '_', '-', $template );

	add_filter( 'excerpt_length', 'epl_archive_custom_excerpt_length', 999 );
	global $epl_settings,$property;

	if ( is_null( $property ) ) {
		return;
	}

	$property_status = $property->get_property_meta( 'property_status' );
	// Status Removal Do Not Display Withdrawn or OffMarket listings.
	if ( ! in_array( $property_status, epl_hide_listing_statuses(), true ) ) {
		// Do Not Display Withdrawn or OffMarket listings.
		$action_check = has_action( 'epl_loop_template' );
		if ( ! empty( $action_check ) && in_array( $template, array( 'default', 'blog' ), true ) ) {
			do_action( 'epl_loop_template' );
		} else {

			if ( isset( $epl_settings['epl_feeling_lucky'] ) && 'on' === $epl_settings['epl_feeling_lucky'] ) {

				epl_get_template_part( 'loop-listing-blog-' . $template . '-compatibility.php' );

			} else {
				$tpl_name = 'loop-listing-blog-' . $template . '.php';
				$tpl_name = apply_filters( 'epl_property_blog_template', $tpl_name );
				epl_get_template_part( $tpl_name );
			}
		}
	} // End Status Removal.
}
add_action( 'epl_property_blog', 'epl_property_blog', 10, 1 );

/**
 * Renders default author box
 *
 * @since      3.2
 */
function epl_property_author_default() {
	global $epl_author_secondary;
	epl_get_template_part( 'content-author-box.php' );
	if ( is_epl_post() && epl_listing_has_secondary_author() ) {
		epl_get_template_part( 'content-author-box.php', array( 'epl_author' => $epl_author_secondary ) );
		epl_reset_post_author();
	}
}
/**
 * AUTHOR CARD : Tabbed Style
 *
 * @since      1.0
 */
function epl_property_author_box() {

	if ( has_action( 'epl_author_template' ) ) {
		do_action( 'epl_author_template' );
	} else {
		epl_property_author_default();
	}
}

/**
 * Reset post author
 *
 * @since      1.0
 */
function epl_reset_post_author() {
	global $post, $epl_author;
	if ( class_exists( 'EPL_Author_meta' ) ) {

		$ID = epl_listing_has_primary_agent();

		if ( is_epl_post() && $ID ) {

			$epl_author = new EPL_Author_meta( $ID );

		} else {

			$epl_author = new EPL_Author_meta( $post->post_author );
		}
	}
}
add_action( 'epl_single_author', 'epl_property_author_box', 10 );

/**
 * AUTHOR CARD : Standard
 *
 * @since      1.0
 */
function epl_property_author_box_simple_card() {
	global $property,$epl_author,$epl_author_secondary;
	epl_get_template_part( 'content-author-box-simple-card.php' );
	if ( is_epl_post() && epl_listing_has_secondary_author() ) {
			epl_get_template_part( 'content-author-box-simple-card.php', array( 'epl_author' => $epl_author_secondary ) );
	}
	epl_reset_post_author();
}

/**
 * AUTHOR CARD : Gravatar
 *
 * @since      1.0
 */
function epl_property_author_box_simple_grav() {
	global $property,$epl_author,$epl_author_secondary;
	epl_get_template_part( 'content-author-box-simple-grav.php' );
	if ( is_epl_post() && epl_listing_has_secondary_author() ) {
			epl_get_template_part( 'content-author-box-simple-grav.php', array( 'epl_author' => $epl_author_secondary ) );
	}
	epl_reset_post_author();
}

/**
 * WIDGET LISTING : Listing Card
 *
 * @since      1.0
 *
 * @param      string $display            The display.
 * @param      string $image              The image.
 * @param      string $title              The title.
 * @param      string $icons              The icons.
 * @param      string $more_text          The more text.
 * @param      string $d_excerpt          The d excerpt.
 * @param      string $d_suburb           The d suburb.
 * @param      string $d_street           The d street.
 * @param      string $d_price            The d price.
 * @param      string $d_more             The d more.
 * @param      string $d_inspection_time  The d inspection time.
 * @param      string $d_ical_link        The d ical link.
 *
 * @since 3.4.13 for custom display, file extension not required and file name format enforced to the format widget-content-listing-{$display}.php
 */
function epl_property_widget( $display, $image, $title, $icons, $more_text = "__('Read More','easy-property-listings' )", $d_excerpt, $d_suburb, $d_street, $d_price, $d_more, $d_inspection_time, $d_ical_link ) {
	global $property;

	if ( is_null( $property ) ) {
		return;
	}

	$property_status = $property->get_property_meta( 'property_status' );

	switch ( $display ) {
		case 'list':
			$tpl = 'widget-content-listing-list.php';
			break;
		case 'hide-image':
			$tpl = 'widget-content-listing-hide-image.php';
			break;
		case 'image-only':
			$tpl = 'widget-content-listing-image.php';
			break;
		case 'image':
			$tpl = 'widget-content-listing.php';
			break;
		default:
			$tpl = $display;
			if ( ! epl_starts_with( $tpl, 'widget-content-listing' ) ) {
				$tpl = 'widget-content-listing-' . $tpl;
			}
			if ( ! epl_ends_with( $tpl, '.php' ) ) {
				$tpl .= '.php';
			}
			break;

	}

	// Status Removal.
	if ( ! in_array( $property_status, epl_hide_listing_statuses(), true ) ) {
		// Do Not Display Withdrawn or OffMarket listings.
		$arg_list = get_defined_vars();
		if ( has_action( 'epl_listing_widget_template' ) ) {
			do_action( 'epl_listing_widget_template', $tpl, $arg_list );
		} else {
			epl_get_template_part( $tpl, $arg_list );
		}
	} // End Status Removal.
}

/**
 * WIDGET LISTING : Listing List
 *
 * @since      1.0
 */
function epl_property_widget_list_option() {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal.
	if ( ! in_array( $property_status, epl_hide_listing_statuses(), true ) ) {
		epl_get_template_part( 'widget-content-listing-list.php' );
	}
} // End Status Removal.


/**
 * WIDGET LISTING : Image Only
 *
 * @since      1.0
 *
 * @param      string $image  The image.
 */
function epl_property_widget_image_only_option( $image ) {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal.
	if ( ! in_array( $property_status, epl_hide_listing_statuses(), true ) ) {
		$arg_list = get_defined_vars();
	}
		epl_get_template_part( 'widget-content-listing-image.php', $arg_list );
} // End Status Removal.


/**
 * WIDGET LISTING : Widget Tall Card
 *
 * @since 1.0
 * @since 3.3 Revised.
 *
 * @param      string $d_image   The d image.
 * @param      string $d_icons   The d icons.
 * @param      string $d_bio     The d bio.
 * @param      string $username  The username.
 */
function epl_property_author_box_simple_card_tall( $d_image, $d_icons, $d_bio, $username ) {

	if ( ! empty( $username ) ) {
		epl_show_author_widget_by_username( $d_image, $d_icons, $d_bio, $username );
		return;
	}

	global $property,$epl_author,$epl_author_secondary;
	if ( is_null( $epl_author ) ) {
		return;
	}

	$arg_list = get_defined_vars();
	epl_get_template_part( 'widget-content-author-tall.php', $arg_list );

	// Second Author.
	if ( is_single() && ! is_null( $property ) ) {
		if ( is_epl_post() && epl_listing_has_secondary_author() ) {
				$epl_author = $epl_author_secondary;
				epl_get_template_part( 'widget-content-author-tall.php', $arg_list );
		}
		epl_reset_post_author();
	}
}

/**
 * Display widget by username
 *
 * @since      3.3
 *
 * @param      string $d_image   The d image.
 * @param      string $d_icons   The d icons.
 * @param      string $d_bio     The d bio.
 * @param      string $username  The username.
 */
function epl_show_author_widget_by_username( $d_image, $d_icons, $d_bio, $username ) {
	$username = explode( ',', $username );
	$username = array_filter( $username );
	foreach ( $username as $uname ) {
		$author = get_user_by( 'login', sanitize_user( $uname ) );
		if ( false !== $author ) {
			$epl_author = new EPL_Author_meta( $author->ID );
			$arg_list   = get_defined_vars();
			epl_get_template_part( 'widget-content-author-tall.php', $arg_list );
		}
	}
}

/**
 * Get the full address
 *
 * @return string  The full address of the listing.
 *
 * @since 1.0.0
 */
function epl_property_get_the_full_address() {
	global $property;

		$address = '';
		$sub_num = $property->get_property_meta( 'property_address_sub_number' );
	if ( ! empty( $sub_num ) ) {
		$address .= $property->get_property_meta( 'property_address_sub_number' ) . '/';
	}
		$address .= $property->get_property_meta( 'property_address_street_number' ) . ' ';
		$address .= $property->get_property_meta( 'property_address_street' ) . ' ';
		$address .= $property->get_property_meta( 'property_address_suburb' ) . ' ';
		$address .= $property->get_property_meta( 'property_address_city' ) . ', ';
		$address .= $property->get_property_meta( 'property_address_state' ) . ' ';
		$address .= $property->get_property_meta( 'property_address_postal_code' ) . ' ';
		$address .= $property->get_property_meta( 'property_address_country' );

	return $address;
}

/**
 * Get the full address
 *
 * @hooked epl_property_title
 * @hooked property_tab_address
 *
 * @param bool   $full Set to false to only display the street address.
 * @param bool   $street_separator Display the street separator.
 * @param string $separator_symbol Symbol to use as the street separator, default is a comma.
 *
 * @since 1.0
 * @since 3.3.3 Revised.
 * @since 3.4.8 Corrected separator location to appear AFTER the street name and options to control display.
 * @since 3.4.9 Added option allowing passing of $full parameter as false to restrict output to street items only.
 */
function epl_property_the_address( $full = true, $street_separator = true, $separator_symbol = ',' ) {

	global $property, $epl_settings;

	if ( ! is_bool( $full ) ) {
		$full = true;
	}

	$epl_property_address_separator        = apply_filters( 'epl_property_address_separator', ',' );
	$epl_property_address_separator_suburb = apply_filters( 'epl_property_address_separator_suburb', false );
	$epl_property_address_separator_city   = apply_filters( 'epl_property_address_separator_city', false );

	?>
	<?php if ( 'yes' === $property->get_property_meta( 'property_address_display' ) ) { ?>
		<span class="item-street"><?php echo wp_kses_post( $property->get_formatted_property_address( $street_separator, $separator_symbol ) ); ?></span>
	<?php } ?>

	<?php
	if ( true === $full ) {
		?>
		<span class="entry-title-sub">
			<?php
			if ( 'commercial' === $property->post_type || 'business' === $property->post_type ) {
				if ( 'yes' === $property->get_property_meta( 'property_com_display_suburb' ) || 'yes' === $property->get_property_meta( 'property_address_display' ) ) {
					?>
					<span class="item-suburb"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></span>
					<?php
					if ( true === $epl_property_address_separator_suburb && strlen( trim( $property->get_property_meta( 'property_address_suburb' ) ) ) ) {
						echo '<span class="item-separator">' . esc_attr( $epl_property_address_separator ) . '</span>';
					}
				}
			} else {
				?>
				<span class="item-suburb"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></span>
				<?php
				if ( true === $epl_property_address_separator_suburb && strlen( trim( $property->get_property_meta( 'property_address_suburb' ) ) ) ) {
					echo '<span class="item-separator">' . esc_attr( $epl_property_address_separator ) . '</span>';
				}
			}
			?>

			<?php
			if ( 'yes' === $property->get_epl_settings( 'epl_enable_city_field' ) ) {
				?>
				<span class="item-city"><?php echo esc_attr( $property->get_property_meta( 'property_address_city' ) ); ?></span>
				<?php
				if ( true === $epl_property_address_separator_city && strlen( trim( $property->get_property_meta( 'property_address_city' ) ) ) ) {
					echo '<span class="item-separator">' . esc_attr( $epl_property_address_separator ) . '</span>';
				}
			}
			?>
			<span class="item-state"><?php echo esc_attr( $property->get_property_meta( 'property_address_state' ) ); ?></span>
			<span class="item-pcode"><?php echo esc_attr( $property->get_property_meta( 'property_address_postal_code' ) ); ?></span>
			<?php
			if ( 'yes' === $property->get_epl_settings( 'epl_enable_country_field' ) ) {
				?>
				<span class="item-country"><?php echo esc_attr( $property->get_property_meta( 'property_address_country' ) ); ?></span>
				<?php
			}
			?>
		</span>
		<?php
	}
}
add_action( 'epl_property_title', 'epl_property_the_address', 10, 3 );
add_action( 'epl_property_tab_address', 'epl_property_the_address', 10, 3 );
add_action( 'epl_property_address', 'epl_property_the_address', 10, 3 );

/**
 * Suburb Name Kept for listing templates extensions which use this function
 *
 * @since 1.3
 * @since 3.1.18 Revised.
 */
function epl_property_suburb() {
	global $property;
	// Commercial and Business Address.
	if ( 'commercial' === $property->post_type || 'business' === $property->post_type ) {
		?>

		<span class="entry-title-sub">
			<?php if ( 'yes' === $property->get_property_meta( 'property_com_display_suburb' ) ) { ?>
				<span class="item-suburb"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></span>
			<?php } else { ?>
				<?php $prop_addr_city = $property->get_property_meta( 'property_address_city' ); ?>
				<?php if ( ! empty( $prop_addr_city ) ) { ?>
					<span class="item-city"><?php echo esc_attr( $property->get_property_meta( 'property_address_city' ) ) . ' '; ?></span>
				<?php } ?>

				<span class="item-state"><?php echo esc_attr( $property->get_property_meta( 'property_address_state' ) ) . ' '; ?></span>
				<span class="item-pcode"><?php echo esc_attr( $property->get_property_meta( 'property_address_postal_code' ) ); ?></span>
			<?php } ?>
		</span>

		<?php
	} else {
		?>
		<span class="entry-title-sub">
			<span class="item-suburb"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></span>
		</span>
		<?php
	}
}
add_action( 'epl_property_suburb', 'epl_property_suburb' );

/**
 * Get the price
 *
 * @since      1.0 @hooked property_price
 * @hooked property_price_content
 */
function epl_property_price() {
	echo wp_kses_post( epl_get_property_price() );
}
add_action( 'epl_property_price', 'epl_property_price' );
add_action( 'epl_property_price_content', 'epl_property_price' );

/**
 * Get Property icons
 *
 * @param      array  $args        The arguments.
 * @param      string $returntype  The returntype.
 *
 * @return false|string
 *
 * @since 1.0
 * @since 3.3.3 Added switch.
 */
function epl_get_property_icons( $args = array(), $returntype = 'i' ) {

	global $property;

	$defaults = array( 'bed', 'bath', 'parking', 'ac', 'pool' );

	$icons      = apply_filters( 'epl_get_property_icons', $defaults );
	$returntype = apply_filters( 'epl_icons_return_type', $returntype );

	ob_start();
	//phpcs:disable
	foreach ( $icons as $icon ) {

		if ( ! empty( $args ) && ! in_array( $icon, $args, true ) ) {
			continue;
		}

		switch ( $icon ) {

			case 'bed':
				echo $property->get_property_bed( $returntype );
				break;

			case 'bath':
				echo $property->get_property_bath( $returntype );
				break;

			case 'parking':
				echo $property->get_property_parking( $returntype );
				break;

			case 'ac':
				echo $property->get_property_air_conditioning( $returntype );
				break;

			case 'pool':
				echo $property->get_property_pool( $returntype );
				break;

			default:
				// action to hook additional icons.
				do_action( 'epl_get_property_icon_' . $icon );
				break;
		}
	}
	//phpcs:enable
	return ob_get_clean();
}

/**
 * Property icons
 *
 * @param string $returntype  The returntype.
 *
 * @since 1.0.0
 * @since 3.3.0 Revides.
 */
function epl_property_icons( $returntype = 'i' ) {
	$returntype = empty( $returntype ) ? 'i' : $returntype;
	echo epl_get_property_icons( array(), $returntype ); //phpcs:ignore
}
add_action( 'epl_property_icons', 'epl_property_icons', 10, 1 );

/**
 * Property bed/bath icons.
 *
 * @since 1.0
 *
 * @return string
 */
function epl_get_property_bb_icons() {
	global $property;
	return $property->get_property_bed() . ' ' .
		$property->get_property_bath();
}

/**
 * Property land category
 *
 * @since      1.0
 * @hooked property_land_category
 */
function epl_property_land_category() {
	global $property;
	echo wp_kses_post( $property->get_property_land_category() );
}
add_action( 'epl_property_land_category', 'epl_property_land_category' );

/**
 * Property Commercial category
 *
 * @since      1.0 @hooked property_commercial_category
 */
function epl_property_commercial_category() {
	global $property;
	if ( 'commercial' === $property->post_type ) {
		if ( 1 === (int) $property->get_property_meta( 'property_com_plus_outgoings' ) ) {
			echo '<div class="price-type">' . esc_html( apply_filters( 'epl_property_sub_title_plus_outgoings_label', __( 'Plus Outgoings', 'easy-property-listings' ) ) ) . '</div>';
		}
		echo wp_kses_post( $property->get_property_commercial_category() );
	}
}
add_action( 'epl_property_commercial_category', 'epl_property_commercial_category' );

/**
 * Property Available Dates
 *
 * @since      1.0 @hooked property_available_dates
 */
function epl_property_available_dates() {
	global $property;
	$date_avail = $property->get_property_meta( 'property_date_available' );
	if ( 'rental' === $property->post_type &&
		! empty( $date_avail )
		&& 'leased' !== $property->get_property_meta( 'property_status' ) ) {
		// Rental Specifics.
		echo '<div class="property-meta date-available">' . wp_kses_post( apply_filters( 'epl_property_sub_title_available_from_label', __( 'Available from', 'easy-property-listings' ) ) ) . ' ', wp_kses_post( $property->get_property_available() ) , '</div>';
	}
}
add_action( 'epl_property_available_dates', 'epl_property_available_dates' );

/**
 * Property Inspection Times
 *
 * @since      1.0 @hooked property_inspection_times
 */
function epl_property_inspection_times() {
	global $property;
	$property_inspection_times = $property->get_property_inspection_times();
	$label_home_open           = '';
	$property_inspection_times = trim( $property_inspection_times );

	if ( ! empty( $property_inspection_times ) ) {
		$label_home_open = $property->get_epl_settings( 'label_home_open' );

		$label_home_open = apply_filters( 'epl_inspection_times_label', $label_home_open );
		?>
	<div class="epl-inspection-times">
		<span class="epl-inspection-times-label">
			<?php echo wp_kses_post( $label_home_open ); ?>
		</span>
		<?php echo wp_kses_post( $property_inspection_times ); ?>
	</div>
		<?php
	}
}
add_action( 'epl_property_inspection_times', 'epl_property_inspection_times' );

/**
 * Getting heading/title of the listing.
 *
 * @since      2.3.1
 *
 * @param      mixed $listing listing instance.
 *
 * @return     string                         listing heading or title
 */
function epl_get_property_heading( $listing = null ) {
	if ( null === $listing ) {
		global $property;
	} elseif ( $listing instanceof EPL_Property_Meta ) {
		$property = $listing;
	} elseif ( $listing instanceof WP_Post ) {
		$property = new EPL_Property_Meta( $listing );
	} else {
		$property = get_post( $listing );
		$property = new EPL_Property_Meta( $property );
	}

	if ( $property ) {
		$property_heading = $property->get_property_meta( 'property_heading' );
		if ( strlen( trim( $property_heading ) ) ) {
			return $property_heading;
		}
		return get_the_title( $property->post->ID );
	}
	return '';
}

/**
 * Property Heading
 *
 * @since 1.0
 * @since      1.0 @hooked the_property_heading
 *
 * @param      string $listing  The listing.
 */
function epl_property_heading( $listing = null ) {
	echo wp_kses_post( epl_get_property_heading( $listing ) );
}
add_action( 'epl_property_heading', 'epl_property_heading' );

/**
 * Property Heading
 *
 * @since      1.0 @hooked property_secondary_heading
 */
function epl_property_secondary_heading() {
	global $property;

	if ( in_array( $property->post_type, array( 'rental', 'property' ), true ) ) {
		echo wp_kses_post( $property->get_property_category( 'span', 'epl-property-category' ) );
	}

	if ( 'rural' === $property->post_type ) {
		echo wp_kses_post( $property->get_property_rural_category( 'span', 'epl-rural-category' ) );
	}

	if ( 'commercial' === $property->post_type || 'commercial_land' === $property->post_type ) {
		echo wp_kses_post( $property->get_property_commercial_category( 'span', 'epl-commercial-category' ) );
	}

	if ( 'sold' === $property->get_property_meta( 'property_status' ) ) {
		echo ' <span class="sold-status">' . esc_attr( $property->label_sold ) . '</span>';
	}
	echo ' <span class="suburb"> - ' . wp_kses_post( $property->get_property_meta( 'property_address_suburb' ) ) . ' </span>';
	echo ' <span class="state">' . wp_kses_post( $property->get_property_meta( 'property_address_state' ) ) . '</span>';
}
add_action( 'epl_property_secondary_heading', 'epl_property_secondary_heading' );

/**
 * Property Category
 *
 * @param string $tag The div tag.
 * @param string $class The css class name.
 *
 * @since 1.0.0
 * @since 3.4.9 Removed passed 'value' option, added epl_property_category hook and passing of tag and class.
 */
function epl_property_category( $tag = 'div', $class = 'property-category' ) {
	global $property;

	if ( empty( $tag ) ) {
		$tag = 'div';
	}

	echo wp_kses_post( $property->get_property_category( $tag, $class ) );
}
add_action( 'epl_property_category', 'epl_property_category', 10, 2 );

/**
 * Video type
 *
 * @param string $url    The url.
 * @return string
 *
 * @since 3.3.0
 */
function epl_get_video_host( $url ) {

	$host = 'unknown';

	if ( strpos( $url, 'youtu' ) > 0 ) {
		$host = 'youtube';
	} elseif ( strpos( $url, 'vimeo' ) > 0 ) {
		$host = 'vimeo';
	}

	return $host;
}

/**
 * Property Video HTML
 *
 * @param      string  $property_video_url  The property video url.
 * @param      integer $width               The width.
 *
 * @return     string
 *
 * @since 1.0
 * @since 3.3
 */
function epl_get_video_html( $property_video_url = '', $width = 600 ) {

	/** Remove related videos from youtube */
	if ( 'youtube' === epl_get_video_host( $property_video_url ) ) {

		if ( strpos( $property_video_url, '?' ) > 0 ) {
			$property_video_url .= '&rel=0';
		} else {
			$property_video_url .= '?rel=0';
		}
	}
	$width = epl_get_option( 'epl_video_width', $width );
	if ( ! empty( $property_video_url ) ) {
		$video_html = '<div class="epl-video-container videoContainer">';

			$video_html .= wp_oembed_get(
				$property_video_url,
				array( 'width' => apply_filters( 'epl_property_video_width', $width ) )
			);
		$video_html     .= '</div>';
		return $video_html;
	}
}

/**
 * Video Output Function
 *
 * @hooked property_after_content
 *
 * @param      integer $width  The width.
 * @since 1.0
 * @since 3.3 Revised.
 */
function epl_property_video_callback( $width = 600 ) {

	global $property;
	$video_width        = ! empty( $width ) ? $width : 600;
	$property_video_url = $property->get_property_meta( 'property_video_url' );
	echo epl_get_video_html( $property_video_url, $video_width ); //phpcs:ignore

}
add_action( 'epl_property_video', 'epl_property_video_callback', 10, 1 );

/**
 * Previous Video Hook, maintained for backward compatibility.
 *
 * @since 3.3
 * @hooked property_after_content
 */
add_action( 'epl_property_content_after', 'epl_property_video_callback', 10, 1 );

/**
 * Property Tab section details output
 *
 * @since      1.0
 * @since      3.4.14 Bug Fix : custom features callback output wrongly placed.
 * @hooked property_tab_section
 */
function epl_property_tab_section() {
	global $property;
	$post_type                 = $property->post_type;
	$the_property_feature_list = apply_filters( 'epl_the_property_feature_list_before', '' );

	$general_features_array = array(
		'category',
		'rural_category',
		'commercial_category',
		'bed',
		'bath',
		'rooms',
		'year_built',
		'parking',
		'ac',
		'pool',
		'security',
		'land_value',
		'building_value',
		'energy_rating',
		'new_construction',
	);

	$general_features_array = apply_filters( 'epl_property_general_features_list', $general_features_array );

	foreach ( $general_features_array as $general_feature ) {

		switch ( $general_feature ) {

			case 'category':
				if ( 'property' === $post_type || 'rental' === $post_type ) {
					$the_property_feature_list .= $property->get_property_category( 'li' );
				}

				break;

			case 'rural_category':
				if ( 'rural' === $post_type ) {
					$the_property_feature_list .= $property->get_property_rural_category( 'li' );
				}

				break;

			case 'commercial_category':
				if ( 'commercial' === $post_type || 'commercial_land' === $post_type || 'business' === $post_type ) {
					$the_property_feature_list .= $property->get_property_commercial_category( 'li' );
				}

				break;

			case 'bed':
				$the_property_feature_list .= $property->get_property_bed( 'l' ) . ' ';

				break;

			case 'bath':
				$the_property_feature_list .= $property->get_property_bath( 'l' ) . ' ';

				break;

			case 'rooms':
				$the_property_feature_list .= $property->get_property_rooms( 'l' ) . ' ';

				break;

			case 'year_built':
				$the_property_feature_list .= $property->get_property_year_built( 'l' ) . ' ';

				break;

			case 'parking':
				$the_property_feature_list .= $property->get_property_parking( 'l' ) . ' ';

				break;

			case 'ac':
				$the_property_feature_list .= $property->get_property_air_conditioning( 'l' ) . ' ';

				break;

			case 'pool':
				$the_property_feature_list .= $property->get_property_pool( 'l' );

				break;

			case 'security':
				$the_property_feature_list .= $property->get_property_security_system( 'l' ) . ' ';

				break;

			case 'land_value':
				$the_property_feature_list .= $property->get_property_land_value( 'l' );

				break;

			case 'building_value':
				$the_property_feature_list .= $property->get_property_building_area_value( 'l' ) . ' ';

				break;

			case 'energy_rating':
				$the_property_feature_list .= $property->get_property_energy_rating( 'l' );

				break;

			case 'new_construction':
				$the_property_feature_list .= $property->get_property_new_construction( 'l' );

				break;

			default:
				ob_start();
				do_action( 'epl_property_general_feature_' . $general_feature );
				$the_property_feature_list .= ob_get_clean();

				break;

		}
	}

	$the_property_feature_list .= apply_filters( 'epl_the_property_feature_list_before_common_features', '' );

	$common_features = array(
		'property_toilet',
		'property_ensuite',
		'property_pet_friendly',
		'property_garage',
		'property_carport',
		'property_open_spaces',
		'property_com_parking_comments',
		'property_com_car_spaces',
		'property_category',
		'property_holiday_rental',
		'property_furnished',
	);
	$common_features = apply_filters( 'epl_property_common_features_list', $common_features );

	foreach ( $common_features as $common_feature ) {
		$the_property_feature_list .= $property->get_additional_features_html( $common_feature );
	}

	$the_property_feature_list .= apply_filters( 'epl_the_property_feature_list_before_additional_features', '' );

	$additional_features = array(
		'property_remote_garage',
		'property_secure_parking',
		'property_study',
		'property_dishwasher',
		'property_built_in_robes',
		'property_gym',
		'property_workshop',
		'property_rumpus_room',
		'property_floor_boards',
		'property_broadband',
		'property_pay_tv',
		'property_vacuum_system',
		'property_intercom',
		'property_spa',
		'property_tennis_court',
		'property_balcony',
		'property_deck',
		'property_courtyard',
		'property_outdoor_entertaining',
		'property_shed',
		'property_open_fire_place',
		'property_ducted_heating',
		'property_ducted_cooling',
		'property_split_system_heating',
		'property_hydronic_heating',
		'property_split_system_aircon',
		'property_gas_heating',
		'property_reverse_cycle_aircon',
		'property_evaporative_cooling',
		'property_land_fully_fenced',
	);
	$additional_features = apply_filters( 'epl_property_additional_features_list', $additional_features );

	if ( 'property' === $property->post_type || 'rental' === $property->post_type || 'rural' === $property->post_type ) {
		foreach ( $additional_features as $additional_feature ) {
			$the_property_feature_list .= $property->get_additional_features_html( $additional_feature );
		}
	}

	$the_property_feature_list .= apply_filters( 'epl_the_property_feature_list_after', '' );

	if ( 'land' !== $property->post_type || 'business' !== $property->post_type ) {
		?>
		<?php $property_features_title = apply_filters( 'epl_property_sub_title_property_features', __( 'Property Features', 'easy-property-listings' ) ); ?>
		<h5 class="epl-tab-title epl-tab-title-property-features tab-title"><?php echo esc_attr( $property_features_title ); ?></h5>
			<div class="epl-tab-content tab-content">
				<ul class="epl-property-features listing-info epl-tab-<?php echo esc_attr( $property->get_epl_settings( 'display_feature_columns' ) ); ?>-columns">
					<?php echo wp_kses_post( $the_property_feature_list . ' ' . $property->get_features_from_taxonomy() ); ?>
				</ul>
			</div>
	<?php } ?>

	<div class="epl-tab-content epl-tab-content-additional tab-content">
		<?php
			// Land Category.
		if ( 'land' === $property->post_type || 'commercial_land' === $property->post_type ) {
			echo '<div class="epl-land-category">' . wp_kses_post( $property->get_property_land_category( 'value' ) ) . '</div>';
		}

			// Commercial Options.
		if ( 'commercial' === $property->post_type ) {
			if ( 1 === (int) $property->get_property_meta( 'property_com_plus_outgoings' ) ) {
				echo '<div class="epl-commercial-outgoings price-type">' . wp_kses_post( apply_filters( 'epl_property_sub_title_plus_outgoings', __( 'Plus Outgoings', 'easy-property-listings' ) ) ) . '</div>';
			}
		}
		?>
	</div>
	<?php
}
add_action( 'epl_property_tab_section', 'epl_property_tab_section' );

/**
 * Property Tab section details output for commercial, business and commercial
 * land
 *
 * @since      1.0 @hooked property_after_tab_section
 */
function epl_property_tab_section_after() {
	global $property;
	$post_type = $property->post_type;
	if ( 'commercial' === $post_type || 'business' === $post_type || 'commercial_land' === $post_type ) {

		$the_property_commercial_feature_list = '';
		$features_lists                       = array(
			'property_com_further_options',
			'property_com_highlight_1',
			'property_com_highlight_2',
			'property_com_highlight_3',
			'property_com_zone',
		);

		// Check for values in the commercial features.
		$commercial_value = '';

		$result = array();

		foreach ( $features_lists as $feature ) {

			$commercial_value = $property->get_property_meta( $feature );

			if ( ! empty( $commercial_value ) ) {
				$result[] = $commercial_value;
			}
		}

		// Display results if $result array is not empty.
		if ( ! empty( $result ) ) {

			foreach ( $features_lists as $features_list ) {
				$the_property_commercial_feature_list .= $property->get_additional_commerical_features_html( $features_list );
			}

			?>
			<div class="epl-tab-section epl-tab-section-commercial-features">
				<h5 class="epl-tab-title epl-tab-title-commercial-features tab-title"><?php echo wp_kses_post( apply_filters( 'epl_property_sub_title_commercial_features', __( 'Commercial Features', 'easy-property-listings' ) ) ); ?></h5>
				<div class="epl-tab-content tab-content">
					<div class="epl-commercial-features listing-info">
						<?php echo wp_kses_post( $the_property_commercial_feature_list ); ?>
					</div>
				</div>
			</div>
			<?php
		}
	}

	if ( 'rural' === $property->post_type ) {
		$the_property_rural_feature_list = '';
		$features_lists                  = array(
			'property_rural_fencing',
			'property_rural_annual_rainfall',
			'property_rural_soil_types',
			'property_rural_improvements',
			'property_rural_council_rates',
			'property_rural_irrigation',
			'property_rural_carrying_capacity',
		);

		// Check for values in the rural features.
		$rural_value = '';

		$result = array();

		foreach ( $features_lists as $feature ) {

			$rural_value = $property->get_property_meta( $feature );

			if ( ! empty( $rural_value ) ) {
				$result[] = $rural_value;
			}
		}

		// Display results if $result array is not empty.
		if ( ! empty( $result ) ) {

			foreach ( $features_lists as $features_list ) {
				$the_property_rural_feature_list .= $property->get_additional_rural_features_html( $features_list );
			}

			?>
		<div class="epl-tab-section epl-tab-section-rural-features">
			<h5 class="epl-tab-title epl-tab-title-rural-features tab-title"><?php echo wp_kses_post( apply_filters( 'epl_property_sub_title_rural_features', __( 'Rural Features', 'easy-property-listings' ) ) ); ?></h5>
			<div class="epl-tab-content tab-content">
				<div class="epl-rural-features listing-info">
					<?php echo wp_kses_post( $the_property_rural_feature_list ); ?>
				</div>
			</div>
		</div>
			<?php
		}
	}
}
add_action( 'epl_property_tab_section_after', 'epl_property_tab_section_after' );

/**
 * Get price sticker
 *
 * @return string
 * @since      1.0
 */
function epl_get_price_sticker() {
	global $property;
	return $property->get_price_sticker();
}

/**
 * Get Property Price
 *
 * @return string
 * @since 1.0
 */
function epl_get_property_price() {
	global $property;
	return $property->get_price();
}

/**
 * Get listing Address for Widget
 *
 * @since      1.0
 *
 * @param      string $d_suburb  The d suburb.
 * @param      string $d_street  The d street.
 */
function epl_widget_listing_address( $d_suburb = '', $d_street = '' ) {
	global $property;
	if ( 'commercial' === $property->post_type || 'business' === $property->post_type ) {
		// Address Display not Commercial or Business type.
		if ( 'yes' === $property->get_property_meta( 'property_address_display' ) ) {
			?>
			<?php
			// Suburb.
			if ( 'on' === $d_suburb && 'yes' === $property->get_property_meta( 'property_com_display_suburb' ) ) {
				?>
				<div class="property-meta suburb-name"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></div>
			<?php } ?>

			<?php
			// Street.
			if ( 'on' === $d_street ) {
				?>
				<div class="property-meta street-name"><?php echo wp_kses_post( $property->get_formatted_property_address() ); ?></div>
			<?php } ?>
		<?php } else { ?>
			<?php
			// Suburb.
			if ( 'on' === $d_suburb && 'yes' === $property->get_property_meta( 'property_com_display_suburb' ) ) {
				?>
				<div class="property-meta suburb-name"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></div>
			<?php } ?>
			<?php
		}
	} else {
		// Address Display not Commercial or Business type.
		if ( 'yes' === $property->get_property_meta( 'property_address_display' ) ) {
			?>
			<?php
			// Suburb.
			if ( 'on' === $d_suburb ) {
				?>
				<div class="property-meta suburb-name"><?php echo esc_attr( $property->get_property_meta( 'property_address_suburb' ) ); ?></div>
			<?php } ?>

			<?php
			// Street.
			if ( 'on' === $d_street ) {
				?>
				<div class="property-meta street-name"><?php echo wp_kses_post( $property->get_formatted_property_address() ); ?></div>
			<?php } ?>
		<?php } else { ?>
			<?php
			// Suburb.
			if ( 'on' === $d_suburb ) {
				?>
				<div class="property-meta suburb-name"><?php echo wp_kses_post( $property->get_property_meta( 'property_address_suburb' ) ); ?></div>
			<?php } ?>
			<?php
		}
	}
}

/**
 * Get Sorting Options
 *
 * @since      2.0
 *
 * @param      boolean $post_type  The post type.
 *
 * @return     mixed|void
 */
function epl_sorting_options( $post_type = null ) {
	// phpcs:disable WordPress.Security.NonceVerification
	if ( is_null( $post_type ) ) {
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : 'property';
	}

	return apply_filters(
		'epl_sorting_options',
		array(
			array(
				'id'      => 'high',
				'label'   => __( 'Price: High to Low', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_price_global',
				'order'   => 'DESC',
				'orderby' => 'meta_value_num',
			),
			array(
				'id'      => 'low',
				'label'   => __( 'Price: Low to High', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_price_global',
				'order'   => 'ASC',
				'orderby' => 'meta_value_num',

			),
			array(
				'id'    => 'new',
				'label' => __( 'Date: Newest First', 'easy-property-listings' ),
				'type'  => 'post',
				'key'   => 'post_date',
				'order' => 'DESC',

			),
			array(
				'id'    => 'old',
				'label' => __( 'Date: Oldest First', 'easy-property-listings' ),
				'type'  => 'post',
				'key'   => 'post_date',
				'order' => 'ASC',
			),
			array(
				'id'      => 'status_asc',
				'label'   => __( 'Status : Current First', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_status',
				'order'   => 'ASC',
				'orderby' => 'meta_value',

			),
			array(
				'id'      => 'status_desc',
				'label'   => __( 'Status : Sold/Leased First', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_status',
				'order'   => 'DESC',
				'orderby' => 'meta_value',

			),
			array(
				'id'      => 'location_asc',
				'label'   => epl_labels( 'label_suburb' ) . __( ' A-Z', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_address_suburb',
				'order'   => 'ASC',
				'orderby' => 'meta_value',

			),
			array(
				'id'      => 'location_desc',
				'label'   => epl_labels( 'label_suburb' ) . __( ' Z-A', 'easy-property-listings' ),
				'type'    => 'meta',
				'key'     => 'property_address_suburb',
				'order'   => 'DESC',
				'orderby' => 'meta_value',

			),
		)
	);
}

/**
 * Switch Sorting Wrapper
 *
 * @since      3.3
 */
function epl_tools_utility_wrapper() {

	// Wrapper Start.
	do_action( 'epl_archive_utility_wrap_start' );

		do_action( 'epl_add_custom_menus' );

	// Wrapper End.
	do_action( 'epl_archive_utility_wrap_end' );

}
add_action( 'epl_property_loop_start', 'epl_tools_utility_wrapper', 10 );

/**
 * Switch Sorting Wrapper
 *
 * @since 2.0
 * @since 3.3 Revised.
 */
function epl_listing_toolbar_items() {

	echo get_epl_listing_toolbar_items(); //phpcs:ignore;

}
add_action( 'epl_add_custom_menus', 'epl_listing_toolbar_items', 10 );

/**
 * Retrieves the switch and sorting options normally right aligned
 *
 * @since      3.3
 *
 * @param array $args   The arguments.
 *
 * @return string
 */
function get_epl_listing_toolbar_items( $args = array() ) {

	$defaults = array(
		'switch_views',
		'sorting_tool',
	);

	$tools = apply_filters( 'epl_listing_toolbar_items', $defaults );

	ob_start();

	// Wrapper.
	if ( ! empty( $defaults ) ) {
		?>
		<div class="epl-loop-tools epl-loop-tools-switch-sort epl-switching-sorting-wrap">
			<?php
			foreach ( $tools as $tool ) {

				if ( ! empty( $args ) && ! in_array( $tool, $args, true ) ) {
					continue;
				}

				switch ( $tool ) {

					case 'switch_views':
						do_action( 'epl_switch_views' );
						break;

					case 'sorting_tool':
						do_action( 'epl_sorting_tool' );
						break;

					default:
						// action to hook additional tools.
						do_action( 'epl_listing_toolbar_' . $tool );
						break;
				}
			}
			?>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Switch Views
 *
 * @since      2.0
 */
function epl_switch_views() {
	?>
	<div class="epl-loop-tool epl-tool-switch epl-switch-view">
		<ul>
			<li title="<?php echo esc_attr( apply_filters( 'epl_switch_views_sorting_title_list', __( 'List', 'easy-property-listings' ) ) ); ?>" class="epl-current-view view-list" data-view="list">
			</li>
			<li title="<?php echo esc_attr( apply_filters( 'epl_switch_views_sorting_title_grid', __( 'Grid', 'easy-property-listings' ) ) ); ?>" class="view-grid" data-view="grid">
			</li>
		</ul>
	</div>
	<?php
}
add_action( 'epl_switch_views', 'epl_switch_views' );

/**
 * Displays the Switch Sorting select options
 *
 * @since 2.0
 * @since 3.3 Revised.
 */
function epl_sorting_tool() {
	$sortby = '';
	if ( ! empty( $_GET['sortby'] ) ) {
		$sortby = sanitize_text_field( wp_unslash( $_GET['sortby'] ) );
	}
	$sorters = epl_sorting_options();
	?>

	<div class="epl-loop-tool epl-tool-sorting epl-properties-sorting epl-clearfix">
		<select id="epl-sort-listings">
			<option <?php selected( $sortby, '' ); ?> value="">
				<?php echo esc_attr( apply_filters( 'epl_switch_views_sorting_title_sort', esc_html__( 'Sort', 'easy-property-listings' ) ) ); ?>
			</option>
			<?php
			foreach ( $sorters as $sorter ) {
				?>
					<option <?php selected( $sortby, $sorter['id'] ); ?> value="<?php echo esc_attr( $sorter['id'] ); ?>">
						<?php echo esc_attr( $sorter['label'] ); ?>
					</option>
					<?php
			}
			?>
		</select>
	</div>
	<?php
}
add_action( 'epl_sorting_tool', 'epl_sorting_tool' );

/**
 * Displays the Sorting tabs
 *
 * @since      3.3
 */
function epl_sorting_tabs() {
	$sortby = '';
	if ( ! empty( $_GET['sortby'] ) ) {
		$sortby = sanitize_text_field( wp_unslash( $_GET['sortby'] ) );
	}
	$sorters = epl_sorting_options();

	global $wp;
	$get_data    = $_GET;
	$get_data    = array_map( 'sanitize_text_field', $get_data );
	$current_url = home_url( add_query_arg( array( $get_data ), $wp->request ) );
	?>

	<div class="epl-loop-tool epl-tool-sorting-tabs epl-properties-sorting epl-clearfix">
		<ul id="epl-sort-tabs-listings">
			<?php
			foreach ( $sorters as $sorter ) {
				$href  = epl_add_or_update_params( $current_url, 'sortby', $sorter['id'] );
				$class = $sortby === $sorter['id'] ? 'epl-sortby-selected' : '';
				?>
					<li class="epl-sortby-list <?php echo esc_attr( $class ); ?>">
						<a href="<?php echo esc_url( $href ); ?>">
						<?php echo esc_attr( $sorter['label'] ); ?>
						</a>
					</li>
					<?php
			}
			?>
		</ul>
	</div>
	<?php
}

/**
 * Update parameters
 *
 * @param string $url The url.
 * @param string $key The key.
 * @param string $value The value.
 *
 * @return string
 * @since 3.3
 */
function epl_add_or_update_params( $url, $key, $value ) {

	$a     = wp_parse_url( $url );
	$query = isset( $a['query'] ) ? $a['query'] : '';
	parse_str( $query, $params );
	$params[ $key ] = $value;
	$query          = http_build_query( $params );
	$result         = '';
	if ( $a['scheme'] ) {
		$result .= $a['scheme'] . ':';
	}
	if ( $a['host'] ) {
		$result .= '//' . $a['host'];
	}
	if ( ! empty( $a['port'] ) ) {
		$result .= ':' . $a['port'];
	}
	if ( $a['path'] ) {
		$result .= $a['path'];
	}
	if ( $query ) {
		$result .= '?' . $query;
	}
	return $result;
}

/**
 * Archive Sorting
 *
 * @since      2.0
 *
 * @param     array $query  The query.
 */
function epl_archive_sorting( $query ) {
	$post_types_sold   = array( 'property', 'land', 'commercial', 'business', 'commercial_land', 'location_profile', 'rural' );
	$post_types_rental = array( 'rental' );

	if ( ! $query->is_main_query() ) {
		return;
	}

	if ( is_post_type_archive( $post_types_sold ) || is_post_type_archive( $post_types_rental ) || is_tax( 'location' ) || is_tax( 'tax_feature' ) || is_tax( 'tax_business_listing' ) || epl_is_search() ) {

		if ( ! empty( $_GET['sortby'] ) ) {

			$orderby = sanitize_text_field( wp_unslash( $_GET['sortby'] ) );
			$sorters = epl_sorting_options( $query->get( 'post_type' ) );

			foreach ( $sorters as $sorter ) {

				if ( $orderby === $sorter['id'] ) {

					if ( 'meta' === $sorter['type'] ) {
						$query->set( 'orderby', $sorter['orderby'] );
						$query->set( 'meta_key', $sorter['key'] );
					} else {
						$query->set( 'orderby', $sorter['key'] );
					}
					$query->set( 'order', $sorter['order'] );
					break;
				}
			}
		}
	}
}
add_action( 'pre_get_posts', 'epl_archive_sorting' );

/**
 * Author Tabs
 *
 * @since      1.0
 *
 * @return array
 */
function epl_author_tabs() {
	global $epl_author;
	$author_tabs = array(
		'author_id'    => __( 'About', 'easy-property-listings' ),
		'description'  => __( 'Bio', 'easy-property-listings' ),
		'video'        => __( 'Video', 'easy-property-listings' ),
		'contact_form' => __( 'Contact', 'easy-property-listings' ),
	);
	$author_tabs = apply_filters( 'epl_author_tabs', $author_tabs );
	return $author_tabs;
}

/**
 * Author Class
 *
 * @since      2.0
 *
 * @param      string $classes  The classes.
 */
function epl_author_class( $classes ) {
	$classes = explode( ' ', $classes . ' epl-author-box author-box' );
	$classes = array_filter( array_unique( $classes ) );
	$classes = apply_filters( 'epl_author_class', $classes );
	if ( ! empty( $classes ) ) {
		$classes = implode( ' ', $classes );
		echo esc_attr( $classes );
	}
}

/**
 * Author Tab ID
 *
 * @since      2.0
 *
 * @param      array $epl_author  The epl author.
 *
 * @return false|string
 */
function epl_author_tab_author_id( $epl_author = array() ) {

	if ( empty( $epl_author ) ) {
		global $epl_author;
	}

	$permalink    = apply_filters( 'epl_author_profile_link', get_author_posts_url( $epl_author->author_id ), $epl_author );
	$author_title = apply_filters( 'epl_author_profile_title', get_the_author_meta( 'display_name', $epl_author->author_id ), $epl_author );

	$arg_list = get_defined_vars();

	ob_start();

		epl_get_template_part( 'content-author-box-tab-details.php', $arg_list );

	return ob_get_clean();
}

/**
 * Author Tab Image
 *
 * @since      2.0
 *
 * @param      array $epl_author  The epl author.
 */
function epl_author_tab_image( $epl_author = array() ) {

	if ( empty( $epl_author ) ) {
		global $epl_author;
	}

	if ( function_exists( 'get_avatar' ) ) {
		echo wp_kses_post( apply_filters( 'epl_author_tab_image', get_avatar( $epl_author->email, '150' ), $epl_author ) );
	}
}
add_action( 'epl_author_thumbnail', 'epl_author_tab_image', 10, 2 );

/**
 * Author Tab Description
 *
 * @since      1.0
 *
 * @param      array $epl_author  The epl author.
 */
function epl_author_tab_description( $epl_author = array() ) {
	if ( empty( $epl_author ) ) {
		global $epl_author;
	}
	echo wp_kses_post( $epl_author->get_description_html() );
}

/**
 * Author Tab Video
 *
 * @since      1.0
 *
 * @param      array $epl_author  The epl author.
 */
function epl_author_tab_video( $epl_author = array() ) {
	if ( empty( $epl_author ) ) {
		global $epl_author;
	}
	$video_html = $epl_author->get_video_html();
	if ( ! empty( $video_html ) ) {
		echo '<div class="epl-author-video author-video epl-video-container">' . $video_html . '</div>'; //phpcs:ignore
	}
}

/**
 * Author Tab Contact Form
 *
 * @since      1.0
 *
 * @param      array $epl_author  The epl author.
 */
function epl_author_tab_contact_form( $epl_author = array() ) {
	if ( empty( $epl_author ) ) {
		global $epl_author;
	}
	echo $epl_author->get_author_contact_form(); //phpcs:ignore
}

/**
 * Archive Utility Wrapper Before
 *
 * @since      1.0
 */
function epl_archive_utility_wrap_before() {
	echo '<div class="epl-loop-tools-wrap epl-archive-utility-wrapper epl-clearfix">';
}
add_action( 'epl_archive_utility_wrap_start', 'epl_archive_utility_wrap_before' );

/**
 * Archive Utility Wrapper After
 *
 * @since      1.0
 */
function epl_archive_utility_wrap_after() {
	echo '</div>';
}
add_action( 'epl_archive_utility_wrap_end', 'epl_archive_utility_wrap_after' );

/**
 * Listing Image Gallery
 *
 * @since 1.0
 * @since 3.3 Revised.
 */
function epl_property_gallery() {

	$d_gallery = (int) epl_get_option( 'display_single_gallery' );

	$d_gallery_n = epl_get_option( 'display_gallery_n' );

	if ( 1 !== $d_gallery ) {
		return;
	}

	$attachments = get_children(
		array(
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
		)
	);

	if ( $attachments ) {
		?>

		<div class="epl-gallery property-gallery">
			<!-- Gallery -->
			<div class="epl-gallery-entry entry-gallery epl-clearfix">
				<?php
					$gallery_shortcode = '[gallery columns="' . $d_gallery_n . '" link="file"]';
					$gallery           = apply_filters( 'epl_property_gallery_shortcode', $gallery_shortcode, $d_gallery_n );
					echo do_shortcode( $gallery ); // phpcs:ignore
				?>
			</div>
		</div>
		<?php
	}
}
add_action( 'epl_property_gallery', 'epl_property_gallery' );

/**
 * Get the template path.
 *
 * @return     string
 * @since      1.0
 */
function epl_template_path() {
	return apply_filters( 'epl_template_path', 'easypropertylistings/' );
}

/**
 * Outputs a wrapper div before the first button
 *
 * @since      1.3
 */
function epl_buttons_wrapper_before() {
	echo '<div class="epl-button-wrapper epl-clearfix">';
}

/**
 * Outputs a wrapper div after the last button
 *
 * @since      1.3
 */
function epl_buttons_wrapper_after() {
	echo '</div>';
}
add_action( 'epl_buttons_single_property', 'epl_buttons_wrapper_before', 1 );
add_action( 'epl_buttons_single_property', 'epl_buttons_wrapper_after', 99 );

/**
 * Used to mark home inspection on apple devices
 *
 * @param string $start The start.
 * @param string $end The end.
 * @param string $name The name.
 * @param string $description The description.
 * @param string $location The location.
 * @param null   $post_id The post ID.
 *
 * @since 2.0.0
 * @since 3.4.9 Corrected issue where output was trimmed, added better unique ID and URL to output.
 */
function epl_create_ical_file( $start = '', $end = '', $name = '', $description = '', $location = '', $post_id = null ) {

	if ( is_null( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$description = str_replace( "\n", "\\n", str_replace( ';', '\;', str_replace( ',', '\,', $description ) ) );
	$uid         = $post_id . current_time( 'timestamp' );
	$url         = get_permalink( $post_id );
	$prodid      = '-//' . get_bloginfo( 'name' ) . '/EPL//NONSGML v1.0//EN';
	$args        = get_defined_vars();
	$args        = apply_filters( 'epl_ical_args', $args );
	$data        = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:" . $prodid . "\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:" . date( 'Ymd\THis', strtotime( $start ) ) . "\nDTEND:" . date( 'Ymd\THis', strtotime( $end ) ) . "\nLOCATION:" . $location . "\nURL:" . $url . "\nTRANSP:OPAQUE\nSEQUENCE:0\nUID:" . $uid . "\nDTSTAMP:" . date( 'Ymd\THis\Z' ) . "\nSUMMARY:" . $name . "\nDESCRIPTION:" . $description . "\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";

	header( 'Content-type:text/calendar' );
	header( 'Content-Disposition: attachment; filename="' . $name . '.ics"' );
	Header( 'Content-Length: ' . strlen( $data ) );
	Header( 'Connection: close' );
	echo $data; //phpcs:ignore
	die;
}

/**
 * Output iCal clickable dates
 *
 * @since      2.0
 */
function epl_process_event_cal_request() {
	global $epl_settings;
	if ( isset( $_GET['propid'] ) && isset( $_GET['epl_cal_dl'] ) && 1 === (int) $_GET['epl_cal_dl'] && intval( $_GET['propid'] ) > 0 ) {
		if ( isset( $_GET['cal'] ) ) {
			$type = sanitize_text_field( wp_unslash( $_GET['cal'] ) );
			switch ( $type ) {
				case 'ical':
					$item = base64_decode( sanitize_text_field( wp_unslash( $_GET['dt'] ) ) ); //phpcs:ignore
					if ( is_numeric( $item[0] ) ) {
						$post_id   = isset( $_GET['propid'] ) ? intval( $_GET['propid'] ) : 0;
						$timearr   = explode( ' ', $item );
						$starttime = current( $timearr );
						if ( isset( $timearr[1] ) ) {
							$starttime .= ' ' . $timearr[1];
						}
						$endtime = current( $timearr ) . ' ' . end( $timearr );
						$post    = get_post( $post_id );
						if ( is_null( $post ) ) {
							return;
						}
						$subject = $epl_settings['label_home_open'] . ' - ' . get_post_meta( $post_id, 'property_heading', true );

						$address      = '';
						$prop_sub_num = get_post_meta( $post_id, 'property_address_sub_number', true );
						if ( ! empty( $prop_sub_num ) ) {
							$address .= get_post_meta( $post_id, 'property_address_sub_number', true ) . '/';
						}
						$address .= get_post_meta( $post_id, 'property_address_street_number', true ) . ' ';
						$address .= get_post_meta( $post_id, 'property_address_street', true ) . ' ';
						$address .= get_post_meta( $post_id, 'property_address_suburb', true ) . ', ';
						$address .= get_post_meta( $post_id, 'property_address_state', true ) . ' ';
						$address .= get_post_meta( $post_id, 'property_address_postal_code', true );

						epl_create_ical_file( $starttime, $endtime, $subject, wp_strip_all_tags( $post->post_content ), $address, $post_id );
					}
					break;
			}
		}
	}
}
add_action( 'init', 'epl_process_event_cal_request' );

/**
 * Add coordinates to meta for faster loading on second view
 *
 * @since      2.1
 */
function epl_update_listing_coordinates() {
	if ( ( ! isset( $_POST['listid'] ) || 0 === intval( $_POST['listid'] ) ) || empty( $_POST['coordinates'] ) ) {
		return;
	}
	$coordinates = rtrim( ltrim( sanitize_text_field( wp_unslash( $_POST['coordinates'] ) ), '(' ), ')' ); //phpcs:ignore
	if ( update_post_meta( intval( wp_unslash( $_POST['listid'] ) ), 'property_address_coordinates', $coordinates ) ) { //phpcs:ignore
		wp_die( 'success' );
	} else {
		wp_die( 'fail' );
	}
}
add_action( 'wp_ajax_epl_update_listing_coordinates', 'epl_update_listing_coordinates' );
add_action( 'wp_ajax_nopriv_epl_update_listing_coordinates', 'epl_update_listing_coordinates' );

/**
 * Adapted from wp core to add additional filters
 *
 * @param      string $id        The identifier.
 * @param      string $taxonomy  The taxonomy.
 * @param      string $before    The before.
 * @param      string $sep       The separator.
 * @param      string $after     The after.
 *
 * @return bool|false|string|WP_Error|WP_Term[]
 *
 * @since 2.1
 * @since 3.3 Revised.
 */
function epl_get_the_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms( $id, $taxonomy );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( empty( $terms ) ) {
		return false;
	}

	foreach ( $terms as $term ) {

		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) ) {
			return $link;
		}

		if ( true === apply_filters( 'epl_features_taxonomy_link_filter', true ) ) {

			$term_links[] = '<li class="epl-tax-feature ' . $term->slug . ' ">' .
						'<a href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>'
					. '</li>' . $sep;

		} else {

			$term_links[] = '<li class="epl-tax-feature ' . $term->slug . ' ">' . $term->name . '</li>' . $sep;

		}
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links ); //phpcs:ignore

	$html = $before;
	foreach ( $term_links as $term_link ) {
		$html .= $term_link;
	}
	$html .= $after;

	return $html;
}

/**
 * Get Property Meta
 *
 * @param string $key Meta key.
 *
 * @return  string $key  The property meta.
 * @since 2.1
 */
function get_property_meta( $key ) {
	global $property;
	return $property->get_property_meta( $key );
}

/**
 * The Property Meta
 *
 * @since 2.1
 *
 * @param string $key    The key.
 */
function the_property_meta( $key ) {
	global  $property;
	echo wp_kses_post( $property->get_property_meta( $key ) );
}

/**
 * Template Class
 *
 * @since      2.1
 *
 * @param      boolean $class    The class.
 * @param      string  $context  The context.
 *
 * @return mixed|void
 */
function epl_template_class( $class = false, $context = 'single' ) {

	if ( $class ) {
		$class = 'epl-template-' . $class;
	} else {
		$class = 'epl-template-blog';
	}

	return apply_filters( 'epl_template_class', $class, $context );
}

/**
 * Pagination
 *
 * @since      2.1
 *
 * @param      array $query  The query.
 */
function epl_pagination( $query = array() ) {
	global $epl_settings;
	$fancy_on = 1 === (int) epl_get_option( 'use_fancy_navigation', 0 ) ? 1 : 0;
	if ( $fancy_on ) {
		epl_fancy_pagination( $query );
	} else {
		epl_wp_default_pagination( $query );
	}
}
add_action( 'epl_pagination', 'epl_pagination' );

/**
 * Returns active theme name as a lowercase name
 *
 * @since      3.0
 *
 * @return string
 */
function epl_get_active_theme() {
	if ( function_exists( 'wp_get_theme' ) ) { // wp version >= 3.4.
		$active_theme = wp_get_theme();
		$active_theme = $active_theme->get( 'Name' );

	} else {
		// older versions.
		$active_theme = get_current_theme(); //phpcs:ignore
	}
	$active_theme = str_replace( ' ', '', strtolower( $active_theme ) );
	return apply_filters( 'epl_active_theme', $active_theme );
}

/**
 * Returns active theme name as a css class with prefix for use in default
 * templates
 *
 * @return mixed|void
 * @since      2.1.2
 */
function epl_get_active_theme_name() {
	$epl_class_prefix = apply_filters( 'epl_active_theme_prefix', 'epl-active-theme-' );
	$active_theme     = epl_get_active_theme();
	return apply_filters( 'epl_active_theme_name', $epl_class_prefix . $active_theme );
}

/**
 * Returns core shortcode names
 *
 * @return array
 * @since 3.3
 */
function epl_get_shortcode_list() {
	return array(
		'listing',
		'listing_category',
		'listing_open',
		'listing_feature',
		'listing_location',
		'listing_auction',
		'listing_advanced',
	);
}

/**
 * Wrapper for wp_doing_ajax with fallback for lower WP versions
 *
 * @return     bool  True if its an ajax request
 * @since      3.4.17
 */
function epl_wp_doing_ajax() {

	if( function_exists( 'wp_doing_ajax' ) ) {
		return wp_doing_ajax();
	} else {
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}

/**
 * Pagination fix for home
 *
 * @param      array $query  The query.
 *
 * @since 2.1.2
 * @since 3.3 Revised.
 */
function epl_home_pagination_fix( $query ) {

	global $wp_query;
	$queried_post_type = isset( $query->query_vars['post_type'] ) ? (array) $query->query_vars['post_type'] : array();
	$diff              = array_diff( $queried_post_type, epl_get_core_post_types() );

	if ( isset( $wp_query->query['paged'] ) && 0 === count( $diff ) ) {
		$query->set( 'paged', $wp_query->query['paged'] );
	}

	$shortcodes = epl_get_shortcode_list();

	if ( $query->get( 'is_epl_shortcode' ) &&
		in_array( $query->get( 'epl_shortcode_name' ), $shortcodes, true ) && ! epl_wp_doing_ajax() ) {

		if ( isset( $_GET['pagination_id'] ) && $_GET['pagination_id'] === $query->get( 'instance_id' ) ) {
			$query->set( 'paged', $query->get( 'paged' ) );
		} else {
			$query->set( 'paged', 1 );
		}
	}
}
add_action( 'pre_get_posts', 'epl_home_pagination_fix', 99 );

/**
 * Returns status class
 *
 * @since      2.1.10
 */
function epl_property_widget_status_class() {
	global $property;
	echo 'epl-widget-status-' . esc_attr( $property->get_property_meta( 'property_status' ) );
}
add_action( 'epl_property_widget_status_class', 'epl_property_widget_status_class' );

/**
 * Ability to hide map on single listings
 *
 * @since      2.1.8
 */
function epl_hide_map_from_front() {
	$epl_posts = epl_get_active_post_types();
	$epl_posts = array_keys( $epl_posts );

	global $post,$property;

	if ( is_single() && in_array( $post->post_type, $epl_posts, true ) ) {

		$hide_map = get_post_meta( $post->ID, 'property_address_hide_map', true );
		if ( 'yes' === $hide_map ) {
			remove_all_actions( 'epl_property_map' );
		}
	}
}
add_action( 'wp', 'epl_hide_map_from_front', 10 );

/**
 * Disable paging on listing widget
 *
 * @since      2.1.8
 *
 * @param      array $query  The query.
 */
function epl_nopaging( $query ) {
	$restrict_paging = $query->get( 'epl_nopaging' );
	if ( true === $restrict_paging ) {
		$query->set( 'paged', 1 );
	}
}
add_action( 'pre_get_posts', 'epl_nopaging' );

/**
 * Ability to hide author box on single listings
 *
 * @since      2.1.11
 */
function epl_hide_author_box_from_front() {
	$epl_posts = epl_get_active_post_types();
	$epl_posts = array_keys( $epl_posts );

	global $post,$property;

	if ( is_single() && in_array( $post->post_type, $epl_posts, true ) ) {

		$hide_author_box = get_post_meta( $post->ID, 'property_agent_hide_author_box', true );
		if ( 'yes' === $hide_author_box ) {
			remove_all_actions( 'epl_single_author' );
		}
	}
}
add_action( 'wp', 'epl_hide_author_box_from_front', 10 );

/**
 * Retain user grid/list view
 *
 * @since      2.1.11
 */
function epl_update_default_view() {

	$view = isset( $_POST['view'] ) ? sanitize_text_field( wp_unslash( $_POST['view'] ) ) : '';

	if ( in_array( $view, array( 'list', 'grid' ), true ) ) {

		setcookie( 'preferredView', $view, 0, '/' );
	}
	wp_die( 'success' );
}
add_action( 'wp_ajax_epl_update_default_view', 'epl_update_default_view' );
add_action( 'wp_ajax_nopriv_epl_update_default_view', 'epl_update_default_view' );

/**
 * Custom the_content filter
 *
 * @since      2.2
 */
function epl_the_content_filters() {

	if ( ! has_filter( 'epl_get_the_content', 'wptexturize' ) ) {

		add_filter( 'epl_get_the_content', 'wptexturize' );
		add_filter( 'epl_get_the_content', 'convert_smilies' );
		add_filter( 'epl_get_the_content', 'convert_chars' );
		add_filter( 'epl_get_the_content', 'wpautop' );
		add_filter( 'epl_get_the_content', 'shortcode_unautop' );
		add_filter( 'epl_get_the_content', 'prepend_attachment' );
		$vidembed = new WP_Embed();
		add_filter( 'epl_get_the_content', array( &$vidembed, 'run_shortcode' ), 8 );
		add_filter( 'epl_get_the_content', array( &$vidembed, 'autoembed' ), 8 );
		add_filter( 'epl_get_the_content', 'do_shortcode', 11 );
	}

	add_filter( 'epl_get_the_excerpt', 'epl_trim_excerpt' );
}
add_action( 'init', 'epl_the_content_filters', 1 );

/**
 * Disable property-box left and right class
 *
 * @since      2.2
 */
function epl_compatibility_archive_class_callback() {
	$class = '-disable';
	echo esc_attr( $class );
}

/**
 * Apply the i'm feeling lucky theme options
 *
 * @since      2.2
 */
function epl_apply_feeling_lucky_config() {

	global $epl_settings;

	$epl_posts = epl_get_active_post_types();
	$epl_posts = array_keys( $epl_posts );

	// remove epl featured image on single pages in lucky mode.
	if ( 'on' === epl_get_option( 'epl_lucky_disable_single_thumb' ) ) {

		if ( is_single() && in_array( get_post_type(), $epl_posts, true ) ) {
			remove_all_actions( 'epl_property_featured_image' );
		}
	}

	// remove active theme's featured image on single pages in lucky mode.
	if ( 'on' === epl_get_option( 'epl_lucky_disable_theme_single_thumb' ) ) {

		if ( is_single() && in_array( get_post_type(), $epl_posts, true ) ) {
			add_filter( 'post_thumbnail_html', 'epl_remove_single_thumbnail', 20, 5 );
		}
	}

	// remove featured image on archive pages in lucky mode.
	if ( 'on' === epl_get_option( 'epl_lucky_disable_archive_thumb' ) ) {

		if ( is_post_type_archive( $epl_posts ) ) {
			add_filter( 'post_thumbnail_html', 'epl_remove_archive_thumbnail', 20, 5 );
		}
	}

	// remove epl featured image on archive pages in lucky mode.
	if ( 'on' === epl_get_option( 'epl_lucky_disable_epl_archive_thumb' ) ) {

		if ( is_post_type_archive( $epl_posts ) ) {
			remove_all_actions( 'epl_property_archive_featured_image' );

			// Adds class to disable property-box right and left.
			add_action( 'epl_compatibility_archive_class', 'epl_compatibility_archive_class_callback' );
		}
	}

}
add_action( 'wp', 'epl_apply_feeling_lucky_config', 1 );

/**
 * A workaround to avoid duplicate thumbnails for single listings being
 * displayed on archive pages via theme & epl attempts to null the post
 * thumbnail image called from theme & display thumbnail image called from epl
 *
 * @param string $html               The html.
 * @param string $post_id            The post identifier.
 * @param string $post_thumbnail_id  The post thumbnail identifier.
 * @param string $size               The size.
 * @param string $attr               The attribute.
 * @return null
 *
 * @since 2.2
 */
function epl_remove_archive_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	if ( is_admin() ) {
		return $html;
	}

	if ( is_epl_post_archive() ) { //phpcs:ignore
		// allow archive listing images as well as widget images.
		if ( //phpcs:ignore
			doing_action( 'epl_property_archive_featured_image' ) ||
			doing_action( 'epl_property_widgets_featured_image' ) ||
			doing_action( 'epl_author_thumbnail' ) ||
			doing_action( 'epl_author_widget_thumbnail' )
		) {

		} else {
			$html = '';
		}
	}

	return $html;
}

/**
 * A workaround to avoid duplicate thumbnails for single listings
 *
 * @since      2.2
 *
 * @param      string $html               The html.
 * @param      string $post_id            The post identifier.
 * @param      string $post_thumbnail_id  The post thumbnail identifier.
 * @param      string $size               The size.
 * @param      string $attr               The attribute.
 *
 * @return     string  ( description_of_the_return_value )
 */
function epl_remove_single_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	if ( is_admin() ) {
		return $html;
	}

	if ( is_epl_post() ) {
		// Allow single listing images as well as widget images.
		if ( doing_action( 'epl_property_featured_image' ) || doing_action( 'epl_property_widgets_featured_image' ) ) { //phpcs:ignore

		} else {
			$html = '';
		}
	}
	return $html;
}

/**
 * Custom property the_content
 *
 * @since      2.2
 */
function epl_the_content() {

	global $property;
	$content = apply_filters( 'epl_get_the_content', get_the_content() );
	echo str_replace( ']]>', ']]&gt;', $content ); //phpcs:ignore
}
add_action( 'epl_property_the_content', 'epl_the_content' );

/**
 * Custom property the_content
 *
 * @since      2.2
 *
 * @param      string $content  The content.
 *
 * @return     false|string
 */
function epl_feeling_lucky( $content ) {

	global $epl_settings;

	if ( ! isset( $epl_settings['epl_feeling_lucky'] ) || 'on' !== $epl_settings['epl_feeling_lucky'] ) {
		return $content;
	}

	$epl_posts = epl_get_active_post_types();
	$epl_posts = array_keys( $epl_posts );

	if ( is_single() && in_array( get_post_type(), $epl_posts, true ) ) {
		ob_start();
		do_action( 'epl_property_single' );
		return ob_get_clean();
	} elseif ( is_post_type_archive( $epl_posts ) ) {
		ob_start();
		do_action( 'epl_property_blog' );
		/**
		* Using return VS echo resolves issues with Yoast SEO repeating content in some cases but breaks the template loading correctly in compatibility mode.
		*/
		echo ob_get_clean(); //phpcs:ignore

	} else {
		return $content;
	}
}

add_filter( 'the_content', 'epl_feeling_lucky' );

/**
 * Custom property the_excerpt
 *
 * @since      2.2
 *
 * @param      string $text   The text.
 *
 * @return mixed|void
 */
function epl_trim_excerpt( $text = '' ) {

	$raw_excerpt = $text;
	if ( empty( $text ) ) {
		$text = get_the_content( '' );

		$text = strip_shortcodes( $text );

		$text = apply_filters( 'epl_get_the_content', $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more   = apply_filters( 'excerpt_more', ' [&hellip;]' );
		$text           = wp_trim_words( $text, $excerpt_length, $excerpt_more );

	}
	return apply_filters( 'epl_trim_excerpt', $text, $raw_excerpt );
}

/**
 * Custom property the_excerpt
 *
 * @since      2.2
 */
function epl_the_excerpt() {
	echo wp_kses_post( apply_filters( 'epl_the_excerpt', epl_get_the_excerpt() ) );
}

/**
 * Custom property the_excerpt
 *
 * @since      2.2
 *
 * @param      string $deprecated  The deprecated.
 *
 * @return mixed|string|void
 */
function epl_get_the_excerpt( $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.3' );
	}

	$post = get_post();
	if ( empty( $post ) ) {
		return '';
	}

	if ( post_password_required() ) {
		return esc_html__( 'There is no excerpt because this is a protected post.', 'easy-property-listings' );
	}

	return apply_filters( 'epl_get_the_excerpt', $post->post_excerpt );
}

/**
 * Syntax Highlighter
 *
 * @since      2.2
 *
 * @param      string $str    The string.
 * @param      string $class  The class.
 *
 * @return     string
 */
function epl_syntax_highlight( $str = '', $class = '' ) {

	return '<pre><code class="' . $class . '">' . htmlentities( $str ) . '</code></pre>';
}

/**
 * Strip Tags
 *
 * @since      2.2
 *
 * @param      string $value the value.
 * @param      string $allowed_tags allowed tags.
 *
 * @return string
 */
function epl_strip_tags( $value, $allowed_tags = '' ) {

	if ( ! is_array( $value ) ) {
		return strip_tags( $value, $allowed_tags );
	}
	return $value;
}

/**
 * Esc Attr
 *
 * @since      2.2
 *
 * @param      string $value  The value.
 *
 * @return string|void
 */
function epl_esc_attr( $value ) {

	if ( ! is_array( $value ) ) {
		return esc_attr( $value );
	}
	return $value;
}

/**
 * Post Count
 *
 * @since      2.2
 *
 * @param      string $type        The type.
 * @param      string $meta_key    The meta key.
 * @param      string $meta_value  The meta value.
 * @param      string $author_id   The author identifier.
 *
 * @return     null
 */
function epl_get_post_count( $type = '', $meta_key, $meta_value, $author_id = '' ) {
	global $wpdb;

	$sql = "
		SELECT count( Distinct p.ID ) AS count
		FROM {$wpdb->prefix}posts AS p
		INNER JOIN $wpdb->postmeta pm  ON (p.ID = pm.post_id)
		INNER JOIN $wpdb->postmeta pm2  ON (p.ID = pm2.post_id)
		WHERE p.post_status = 'publish' ";

	if ( empty( $type ) ) {
		$epl_posts = epl_get_active_post_types();
		$epl_posts = '"' . implode( '","', array_keys( $epl_posts ) ) . '"';

		$sql .= " AND p.post_type IN ( {$epl_posts} )";
	} else {
		$sql .= " AND p.post_type = '{$type}'";
	}

	if ( ! empty( $author_id ) ) {
		$user_info = get_userdata( $author_id );
		$sql      .= " AND (
						p.post_author =  $author_id
						OR (
							pm2.meta_key 	= 'property_second_agent'
							AND
							pm2.meta_value 	= '$user_info->user_login'
						)
					)";
	}
	$sql  .= "
		AND p.ID = pm.post_id
		AND pm.meta_key = '{$meta_key}'
		AND pm.meta_value = '{$meta_value}'
	";
	$count = $wpdb->get_row( $sql ); //phpcs:ignore
	return $count->count;
}

/**
 * Get the inspection date format
 *
 * @since 3.3
 *
 * @return string
 */
function epl_get_inspection_date_format() {

	$date_format = epl_get_option( 'inspection_date_format' ) === 'custom_inspection_date_format' ?
		epl_get_option( 'custom_inspection_date_format' ) : epl_get_option( 'inspection_date_format' );

	if ( empty( $date_format ) ) {
		$date_format = 'd-M-Y';
	}

	return apply_filters( 'epl_inspection_date_format', $date_format );
}

/**
 * Get the inspection time format
 *
 * @since 3.3
 *
 * @return     string
 */
function epl_get_inspection_time_format() {

	$time_format = 'custom_inspection_time_format' === epl_get_option( 'inspection_time_format' ) ?
			epl_get_option( 'custom_inspection_time_format' ) : epl_get_option( 'inspection_time_format' );

	if ( empty( $time_format ) ) {
		$time_format = 'h:i A';
	}

	return apply_filters( 'epl_inspection_time_format', $time_format );

}

/**
 * Inspection Format
 *
 * @since      2.2
 *
 * @param      array $inspection_date  The inspection date.
 *
 * @return     string
 */
function epl_inspection_format( $inspection_date ) {

	$formatted_date  = '';
	$inspection_date = explode( ' ', $inspection_date );

	$date_format = epl_get_inspection_date_format();
	$time_format = epl_get_inspection_time_format();

	$date       = isset( $inspection_date[0] ) ? date( $date_format, strtotime( $inspection_date[0] ) ) : '';
	$time_start = isset( $inspection_date[1] ) ? date( $time_format, strtotime( $inspection_date[1] ) ) : '';
	$time_end   = isset( $inspection_date[3] ) ? date( $time_format, strtotime( $inspection_date[3] ) ) : '';

	return "{$date} {$time_start} to {$time_end}";
}
add_action( 'epl_inspection_format', 'epl_inspection_format' );

/**
 * Counts the total number of contacts.
 *
 * @access     public
 * @since      3.0
 *
 * @return     int   - The total number of contacts.
 */
function epl_count_total_contacts() {
	$counts = wp_count_posts( 'epl_contact' );
	return $counts->publish;
}

/**
 * Hide contacts notes from showing on frontend
 *
 * @since      3.0
 *
 * @param      array  $comments  The comments.
 * @param      string $post_id   The post identifier.
 *
 * @return     mixed
 */
function epl_filter_listing_comments_array( $comments, $post_id ) {
	foreach ( $comments as $key   => &$comment ) {
		if ( 'epl' === $comment->comment_agent ) {
			unset( $comments[ $key ] );
		}
	}
	return $comments;
}
add_filter( 'comments_array', 'epl_filter_listing_comments_array', 10, 2 );

/**
 * Archive Page Title
 *
 * @since 3.0
 * @return void the archive title
 */
function epl_archive_title_callback() {
	the_post();

	if ( is_tax() && function_exists( 'epl_is_search' ) && false === epl_is_search() ) { // Tag Archive.
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		// translators: term name.
		$title = sprintf( __( 'Property in %s', 'easy-property-listings' ), $term->name );
	} elseif ( function_exists( 'epl_is_search' ) && epl_is_search() ) { // Search Result.
		$title = apply_filters( 'epl_archive_title_search_result', __( 'Search Result', 'easy-property-listings' ) );
	} elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() && function_exists( 'post_type_archive_title' ) ) { // Post Type Archive.
		$title = post_type_archive_title( '', false );
	} else { // Default catchall just in case.
		$title = apply_filters( 'epl_archive_title_fallback', __( 'Listing', 'easy-property-listings' ) );
	}

	if ( is_paged() ) {
		// translators: title, page number.
		printf( '%s &ndash; Page %d', esc_attr( $title ), esc_attr( get_query_var( 'paged' ) ) );
	} else {
		echo wp_kses_post( apply_filters( 'epl_archive_title_default', $title ) );
	}

	rewind_posts();
}
add_action( 'epl_the_archive_title', 'epl_archive_title_callback' );

/**
 * Shortcode Sorter
 *
 * @since      3.0
 *
 * @param      array  $args   The arguments.
 * @param      string $type   The type.
 * @param      string $name   The name.
 *
 * @return mixed $args
 */
function epl_add_orderby_args( $args, $type = '', $name = '' ) {

	if ( 'shortcode' === $type ) {
		$args['is_epl_shortcode']   = true;
		$args['epl_shortcode_name'] = $name;
	}

	$post_type = isset( $args['post_type'] ) ? current( $args['post_type'] ) : '';
	$post_type = sanitize_text_field( wp_unslash( $post_type ) );
	$orderby   = isset( $_GET['sortby'] ) ? sanitize_text_field( wp_unslash( $_GET['sortby'] ) ) : '';
	if ( ! empty( $orderby ) ) {

		$sorters = epl_sorting_options( $post_type );

		foreach ( $sorters as $sorter ) {

			if ( $orderby === $sorter['id'] ) {

				if ( 'meta' === $sorter['type'] ) {
					$args['orderby']  = $sorter['orderby'];
					$args['meta_key'] = $sorter['key']; //phpcs:ignore
				} else {
					$args['orderby'] = $sorter['key'];
				}
				$args['order'] = $sorter['order'];
				break;
			}
		}
	}
	return $args;
}

/**
 * Shortcode Sorter
 *
 * @since      3.1.5
 *
 * @param      string $shortcode  The shortcode.
 */
function epl_shortcode_results_message_callback( $shortcode = 'default' ) {

	$title = apply_filters( 'epl_shortcode_results_message_title', __( 'Nothing found, please check back later.', 'easy-property-listings' ) );

	if ( 'open' === $shortcode ) {
		$title = apply_filters( 'epl_shortcode_results_message_title_open', __( 'Nothing currently scheduled for inspection, please check back later.', 'easy-property-listings' ) );
	}

	echo '<h3 class="epl-shortcode-listing-open epl-alert">' . esc_attr( $title ) . '</h3>';

}
add_action( 'epl_shortcode_results_message', 'epl_shortcode_results_message_callback' );

/**
 * Search Not Found Messages
 *
 * @since      3.1.8
 */
function epl_property_search_not_found_callback() {

	$title = apply_filters( 'epl_property_search_not_found_title', __( 'Listing not Found', 'easy-property-listings' ) );

	$message = apply_filters( 'epl_property_search_not_found_message', __( 'Listing not found, expand your search criteria and try again.', 'easy-property-listings' ) );

	?>

	<div class="epl-search-not-found-title entry-header clearfix">
		<h3 class="entry-title"><?php echo esc_attr( $title ); ?></h3>
	</div>

	<div class="epl-search-not-found-message entry-content clearfix">
		<p><?php echo wp_kses_post( $message ); ?></p>
	</div>

	<?php
}
add_action( 'epl_property_search_not_found', 'epl_property_search_not_found_callback' );

/**
 * Add Listing Status and Under Offer to Post Class
 *
 * @since      3.1.16
 *
 * @param      array $classes  The classes.
 *
 * @return     array
 */
function epl_property_post_class_listing_status_callback( $classes ) {

	if ( is_epl_post() ) {

		$property_status      = get_property_meta( 'property_status' );
		$property_under_offer = get_property_meta( 'property_under_offer' );
		$commercial_type      = get_property_meta( 'property_com_listing_type' );
		$class_prefix         = 'epl-status-';

		if ( ! empty( $property_status ) ) {
			$classes[] = $class_prefix . strtolower( $property_status );
		}
		if ( 'yes' === $property_under_offer && 'sold' !== $property_status ) {
			$classes[] = $class_prefix . 'under-offer';
		}
		if ( ! empty( $commercial_type ) ) {
			$class_prefix = 'epl-commercial-type-';
			$classes[]    = $class_prefix . strtolower( $commercial_type );
		}
	}
	return $classes;
}
add_filter( 'post_class', 'epl_property_post_class_listing_status_callback' );

/**
 * Get the author loop
 *
 * @since 3.3
 */
function epl_archive_author_callback() {
	global $epl_author_secondary;
	epl_get_template_part( 'content-author-archive-card.php' );
	if ( is_epl_post() && epl_listing_has_secondary_author() ) {
		epl_get_template_part( 'content-author-archive-card.php', array( 'epl_author' => $epl_author_secondary ) );
		epl_reset_post_author();
	}
}
add_action( 'epl_archive_author', 'epl_archive_author_callback' );

/**
 * Contact capture action and messages
 *
 * @since      3.3
 */
function epl_contact_capture_action() {

	$success = array(
		'status' => 'success',
		'msg'    => apply_filters( 'epl_contact_capture_success_msg', __( 'Form submitted successfully', 'easy-property-listings' ) ),
	);

	$fail = array(
		'status' => 'fail',
		'msg'    => apply_filters( 'epl_contact_capture_fail_msg', __( 'Some issues with form submitted', 'easy-property-listings' ) ),
	);

	if (
		! isset( $_POST['epl_contact_widget'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['epl_contact_widget'] ) ), 'epl_contact_widget' )
	) {
		wp_die( wp_json_encode( $fail ) );
	}

	if ( ! empty( $_POST['epl_contact_anti_spam'] ) ) {
		wp_die( wp_json_encode( $fail ) );
	}

	if ( empty( $_POST['epl_contact_email'] ) ) {
		wp_die(
			wp_json_encode(
				array(
					'status' => 'fail',
					'msg'    => __(
						'Email is required',
						'easy-property-listings'
					),
				)
			)
		);
	}

	$contact = new EPL_contact( sanitize_text_field( wp_unslash( $_POST['epl_contact_email'] ) ) );
	$fname   = isset( $_POST['epl_contact_first_name'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_first_name'] ) ) : '';
	$lname   = isset( $_POST['epl_contact_last_name'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_last_name'] ) ) : '';
	$phone   = isset( $_POST['epl_contact_phone'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_phone'] ) ) : '';
	$title   = isset( $_POST['epl_contact_title'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_title'] ) ) : '';
	$title   = trim( $title );
	if ( empty( $title ) && ( ! empty( $fname ) || ! empty( $lname ) ) ) {
		$title = $fname . ' ' . $lname;
	}

	if ( empty( $title ) && ( ! empty( $_POST['epl_contact_email'] ) ) ) {
		$title = sanitize_text_field( wp_unslash( $_POST['epl_contact_email'] ) );
	}

	$contact_listing_id = isset( $_POST['epl_contact_listing_id'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_listing_id'] ) ) : false;

	$contact_listing_note = isset( $_POST['epl_contact_note'] ) ?
	sanitize_text_field( wp_unslash( $_POST['epl_contact_note'] ) ) : false;
	if ( empty( $contact->id ) ) {

		$contact_data = array(
			'name'  => $title,
			'email' => sanitize_email( wp_unslash( $_POST['epl_contact_email'] ) ),
		);
		if ( $contact->create( $contact_data ) ) {
			$contact->update_meta( 'contact_first_name', $fname );
			$contact->update_meta( 'contact_last_name', $lname );
			$contact->update_meta( 'contact_phones', array( 'phone' => $phone ) );
			$contact->update_meta( 'contact_category', 'widget' );
			$contact->attach_listing( $contact_listing_id );
			$contact->add_note( $contact_listing_note, 'note', $contact_listing_id );
			wp_die( wp_json_encode( $success ) );
		} else {
			wp_die( wp_json_encode( $fail ) );
		}
	} else {

		if ( $contact->update( array( 'name' => $title ) ) ) {
			$contact->add_note(
				sanitize_textarea_field( wp_unslash( $_POST['epl_contact_note'] ) ),
				'note',
				$contact_listing_id
			);
			$contact->attach_listing( $contact_listing_id );
			wp_die( wp_json_encode( $success ) );
		} else {
			wp_die( wp_json_encode( $fail ) );
		}
	}
}
add_action( 'wp_ajax_epl_contact_capture_action', 'epl_contact_capture_action' );
add_action( 'wp_ajax_nopriv_epl_contact_capture_action', 'epl_contact_capture_action' );
