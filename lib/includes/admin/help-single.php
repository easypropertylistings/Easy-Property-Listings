<?php
/**
 * Contextual Help
 *
 * @package     EPL
 * @subpackage  Admin/Help
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds the Contextual Help for the main Listings page
 *
 * @since 3.0
 * @return void
 */
function epl_listings_contextual_help() {
    $screen = get_current_screen();

    if (  !in_array($screen->id, epl_get_core_post_types()))
        return;

    $screen->set_help_sidebar(
        '<p><strong>' . sprintf( __( 'For more information:', 'easy-property-listings'  ) . '</strong></p>' .
        '<p>' . sprintf( __( '<a href="%s">Documentation on how to add a listing</a>.', 'easy-property-listings'  ), esc_url( 'http://codex.easypropertylistings.com.au/article/135-how-to-add-a-listing-to-easy-property-listings' ) ) ) . '</p>' .
        '<p>' . sprintf(  __( 'Visit the <a href="%s">Store</a>', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/extensions/' ) ) . '</p>' .
	'<p>' . sprintf(  __( 'Need help? Open a <a href="%s">Support</a> ticket.', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) ) . '</p>'

    );

    // Customising This Display Tab
    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-customising',
        'title'		=> __('Customising This Display'),
        'content'	=> '<p>' . __('The title field and the big Post Editing Area are fixed in place, but you can reposition all the other boxes using drag and drop. You can also minimise or expand them by clicking the title bar of each box. Use the Screen Options tab to unhide more boxes (Location, Features, Property Address, Pricing, Linked Contact, Featured Image, Excerpt, Discussion, Comments, Slug, Author, Listing Details, Listing Features, Additional Features, Files and Links) or to choose a 1- or 2-column layout for this screen.') . '</p>'

    ) );

    // Title and Post Editor Tab
    $title_editor	= '<p><strong>' . __('Title' , 'easy-property-listings'  ) . '</strong> — ' . __( 'Enter a title for your post, for best results enter the full property address. After you enter a title, you’ll see the permalink below, which you can edit.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p><strong>' . __('Post editor' , 'easy-property-listings'  ) . '</strong> — ' . __( 'Enter the text for your post. There are two modes of editing: Visual and Text. Choose the mode by clicking on the appropriate tab.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p>' . __('Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p>' . __('The Text mode allows you to enter HTML along with your post text. Line breaks will be converted to paragraphs automatically.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p>' . __('You can insert media files by clicking the icons above the post editor and following the directions. You can align or edit images using the inline formatting toolbar available in Visual mode.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p>' . __('You can enable distraction-free writing mode using the icon to the right. This feature is not available for old browsers or devices with small screens, and requires that the full-height editor be enabled in Screen Options.' , 'easy-property-listings'  ) . '</p>';

    $title_editor	.= '<p>' . __('Keyboard users: When you’re working in the visual editor, you can use <code>Alt + F10</code> to access the toolbar.' , 'easy-property-listings'  ) . '</p>';

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-configuration-two',
        'title'		=> __('Title and Post Editor'),
        'content'	=> $title_editor

    ) );

    // Inserting Media
    $media	= '<p>' . __( 'You can upload and insert media (images, audio, documents, etc.) by clicking the Add Media button. You can select from the images and files already uploaded to the Media Library, or upload new media to add to your page or post. To manually create an image gallery, select the images to add and click the “Create a new gallery” button.' , 'easy-property-listings'  ) . '</p>';

    $media	.= '<p>' . __( 'You can also embed media from many popular websites including Twitter, YouTube, Flickr and others by pasting the media URL on its own line into the content of your post/page. Please refer to the Codex to learn more about embeds.' , 'easy-property-listings'  ) . '</p>';

    $media	.= '<p><strong>' . __('Note on Automatic Gallery Output:' , 'easy-property-listings'  ) . '</strong> ' . __( 'When your Easy Property Listings gallery settings are set to automatic the images that have been uploaded to this listing will automatically display and you cannot control the image order or exclude images. For full gallery control, disable the automatic gallery output and “Create a new gallery” for each listing.' , 'easy-property-listings'  ) . '</p>';



    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-media',
        'title'		=> __('Inserting Media'),
        'content'	=> $media

    ) );

    // Property Suburb/Location
    $s = epl_tax_location_label();
    $location	= '<p>' . __( "Add your listing $s as this is what powers the search by $s in the search widget." , 'easy-property-listings'  ) . '</p>';

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-location',
        'title'		=> epl_tax_location_label(),
        'content'	=> $location

    ) );

    // Property Address
    $address	= '<p>' . __( 'Add the full listing address and generate the coordinates for the listing. You can drag the pin to adjust the lat/long coordinates of the listing. If you do not check the "Display Street Address" option the street number and street will not display to your visitor. You can also hide the map on a per listing basis.' , 'easy-property-listings'  ) . '</p>';

    $address	.= '<p>' . __( 'If you do not check the "Display Street Address" option, the street number and street will not display to your visitor.' , 'easy-property-listings'  ) . '</p>';

    $address	.= '<p>' . __( 'Adjust the lat/long coordinates of the listing by dragging the pin to a new location if required. You can also hide the map on a per listing basis.' , 'easy-property-listings'  ) . '</p>';

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-address',
        'title'		=> __('Property Address'),
        'content'	=> $address

    ) );

    // Pricing

    $label_poa = epl_get_option( 'label_poa' , 'POA' );

    $pricing	= '<p><strong>' . __('Search Price/Rent' , 'easy-property-listings'  ) . '</strong> — ' . __( 'The search price should always be set as this will allow your visitor to narrow down their search. This number will be formatted as per your Easy Property Listings Currency settings.' , 'easy-property-listings'  ) . '</p>';

    $pricing	.= '<p><strong>' . __('Price/Rent Text' , 'easy-property-listings'  ) . '</strong> — ' . __( 'This allows you to enter text as the price and allows you to enter a range, from price or anything you want to display. When entered this is displayed instead of the Search Price. No formatting is applied to the Price/Rent text so manually format the price you enter e.g. From $600,000.' , 'easy-property-listings'  ) . '</p>';

    $pricing	.= '<p><strong>' . __('Display Price' , 'easy-property-listings'  ) . '</strong> — ' . __( 'When this is not ticked your "No Price Label" will be displayed which you can adjust from settings. Your custom No Price label is currenty:' , 'easy-property-listings'  ) . ' <code>' . $label_poa . '</code></p>';

    $pricing	.= '<p><strong>' . epl_meta_under_offer_label() . '</strong> — ' . __( 'Tick this when your listing is pending sale. The price will be replaced with your cusom label which is currently:' , 'easy-property-listings'  ) . ' <code>' . epl_meta_under_offer_label() . '</code></p>';

    $pricing	.= '<p><strong>' . __('Sale Price' , 'easy-property-listings'  ) . '</strong> — ' . __( 'Once your listing is sold, you can enter the sale price and you can choose to "Display Sale Price" to your visitor.' , 'easy-property-listings'  ) . '</p>';

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-pricing',
        'title'		=> __('Pricing/Rent' , 'easy-property-listings' ),
        'content'	=> $pricing

    ) );

    // Linked Contact
    $contact	= '<p>' . __( 'Search for their name and select the contact and the contact ID will be determined. Publish/Update your listing and their details will be displayed for quick access.' , 'easy-property-listings'  ) . '</p>';

    $screen->add_help_tab( array(
        'id'		=> 'epl-listing-contact',
        'title'		=> __('Linked Contact' , 'easy-property-listings' ),
        'content'	=> $contact

    ) );
    /**
     * Fires off in the EPL Contextual Help Screen
     *
     * @since 3.0
     * @param object $screen The current admin screen
     */
    do_action( 'epl_listings_contextual_help', $screen );
}

add_action( 'current_screen', 'epl_listings_contextual_help' );