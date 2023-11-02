# [Easy Property Listings](https://easypropertylistings.com.au/)

![Plugin Version](https://img.shields.io/wordpress/plugin/v/easy-property-listings.svg) ![Total Downloads](https://img.shields.io/wordpress/plugin/dt/easy-property-listings.svg?cacheSeconds=3600) ![Plugin Rating](https://img.shields.io/wordpress/plugin/r/easy-property-listings.svg?cacheSeconds=3600) ![WordPress Compatibility](https://img.shields.io/wordpress/v/easy-property-listings.svg?cacheSeconds=3600) [![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)](https://github.com/easypropertylistings/Easy-Property-Listings/blob/master/license.txt)

### Welcome to our GitHub Repository

Properties listed for rent or sale is something that not a single one of the large WordPress real estate plugins has ever gotten really right. This plugin aims to fix that. Instead of focusing on providing every single feature under the sun, Easy Property Listings tries to provide only the ones that you really need. It aims to make real estate websites through WordPress easy, and complete.

More information can be found at [easypropertylistings.com.au](https://easypropertylistings.com.au/).

### Installation

For detailed setup instructions, visit the official [Documentation](https://codex.easypropertylistings.com.au/) page.

1. You can clone the GitHub repository: `https://github.com/easypropertylistings/Easy-Property-Listings.git`
2. Or download it directly as a ZIP file: `https://github.com/easypropertylistings/Easy-Property-Listings/archive/master.zip`

This will download the latest developer copy of Easy Property Listings.

## Bugs

If you find an issue, let us know [here](https://github.com/easypropertylistings/Easy-Property-Listings/issues?state=open)!

## Support

This is a developer's portal for Easy Property Listings and should _not_ be used for support. Please open a [support ticket](https://easypropertylistings.com.au/support-ticket/).

## Contributions

Anyone is welcome to contribute to Easy Property Listings. Please read the [guidelines for contributing](https://github.com/easypropertylistings/Easy-Property-Listings/blob/master/CONTRIBUTING.md) to this repository.

There are various ways you can contribute:

1. Raise an [Issue](https://github.com/easypropertylistings/Easy-Property-Listings/issues) on GitHub
2. Send us a Pull Request with your bug fixes and/or new features
3. Translate Easy Property Listings into different languages
4. Provide feedback and suggestions on [enhancements](https://github.com/easypropertylistings/Easy-Property-Listings/issues?direction=desc&labels=Enhancement&page=1&sort=created&state=open)

## Change Log

= 3.5 October 30, 2023 =

- New: epl_property_tab_section hook is replaced with epl_property_features, older hook is kept for backward compatibility.
- New: Filters added to alter an empty value if required: epl_get_year_built_empty_value, epl_get_property_bed_empty_value, epl_get_property_bath_empty_value, epl_get_property_rooms_empty_value, epl_get_parking_spaces_empty_value, epl_get_property_garage_empty_value, epl_get_property_carport_empty_value, epl_get_air_conditioning_empty_value, epl_get_property_pool_empty_value, epl_get_security_system_empty_value
- New: Using the global status function to get status labels.
- New: Prefixed Google Maps API key script name.
- New: Display hidden fields in admin when editing listings.
- New: Additional args for custom template action: epl*loop_template*{$post_type}, epl_loop_template.
- New: Search shortcode and widgets. Support for instance ID, fallback generates unique instance ID automatically. Allowing multiple search with tabs on the same page to not clash with each other.
- New: Added support for 'status' in search shortcodes.
- New: Support for conditional fields in extension settings.
- New: Support for number type field for widget settings.

- Tweak: Shortcode results message allow basic html.
- Tweak: Shortcodes, make sure meta_query in arg is defined as array, prior to usage.
- Tweak: Added accessibility labels to select elements.
- Tweak: Escaping additional widget elements.
- Tweak: Correct spelling of Separate.
- Tweak: Additional PHP 8.2 code improvements.
- Tweak: Prevent importing non text files in settings.
- Fix: Availability time is only shown when there is a date.
- Fix: Toolbar items function missing arguments.
- Fix: Improvements to settings imports if file is incorrectly uploaded.
- Fix: Price Sticker, inspections code trim string when is null.
- Fix: PHP 8.2 Automatic conversion of false to array is deprecated fix.
- Fix: Floor plans, energy certificate, external links and min web labels using esc_html instead of esc_attr.

= 3.4.48 August 29, 2023 =

- New: Support for Namibian dollar.
- New: Field type textarea_html which works like the editor field type but without loading the MCE buttons.
- Tweak: Removed duplicate ID from social SVG.
- Tweak: Shortcode results not found classes passed for each shortcode type.
- Tweak: Search not found container added for easier styling.
- Tweak: Accessibility label added for select field.
- Fix: Class name is now unique for [listing_open], [listing_location], [listing_auction], [listing_category], [listing_feature] shortcodes.
- Fix: Price formatting anomaly as per settings in price slider.
- Fix: Missing space between classes names for shortcode open & location.
- Fix: PHP 8.1 warning when no user role set for user.

= 3.4.47 August 3, 2023 =

- Tweak: Additional options added to alter the default behaviour of the accordion feature. Custom classes supported to enable accordions.

= 3.4.46 July 6, 2023 =

- Fix: Support for arguments with epl_the_excerpt when in theme compatibility mode.

= 3.4.45 July 1, 2023 =

- New: Deleted status added for REAXML support.
- Tweak: Reordered status options in edit post and admin search filtering.
- Tweak: Pass user provided attributes to the [listing_search] Shortcode template.
- Fix: JS Issue - Assignment to constant variable.
- Fix: Compatibility mode accidentally styling in admin area.

= 3.4.44 April 4, 2023 =

- New: Added filter epl_maybe_delete_inspection for deciding whether to remove inspection entry.
- New: Added field type class for field wrapper and custom wrapper class support through wrapper_class.
- New: Range slider epl_get_range_slider_label_html filter to allow changing html for range slider with {range_start} {range_sep} {range_end} components.
- New: Changed jQuery target #epl-sort-listings to .epl-sort-listings to support multiple instances of sorting dropdown on page.
- New: Filter for land category epl_get_property_land_category was incorrect.
- New: Added help field type in meta boxes fields that will render html.
- New: Support for YouTube shorts.
- New: Added support for YouTube shorts and epl_property_video_url filter for video URL.
- New: Option to sort by title.
- New: Added epl_get_shortcode_list filter.
- New: Added epl_any_label_status_filter and epl_current_label_status_filter filters for any and current status labels.
- Tweak: Option sortby based on instance_id now.
- Tweak: Parking Comments label before value.
- Tweak: Added check for empty videos.
- Tweak: Unique ID for sort dropdown per instance.
- Tweak: Get shortcode attributes as parameter.
- Fix: Missing commercial auction listings in listing_action shortcode.
- Fix: Warning for preg_replace when value is empty.
- Fix: Warning for wp_kses_post when empty link.
- Fix: Notice when $post is null.
- Fix: Appended dash sign for field sliders.
- Fix: Error if area is non numeric in land or building values when using imported data.
- Fix: PHP 8.1 and 8.2 improvements.
- Fix: Fixed callback error for google maps.

= 3.4.43 January 9, 2023 =

- Fix: Avada theme archive listing template updated.

= 3.4.42 December 20, 2022 =

- Fix: Avada single listing template updated.

= 3.4.41 July 26, 2022 =

- Fix: Only the default template was loading in shortcodes.
- Fix: Added land category to the epl_property_secondary_heading hook.

= 3.4.40 May 06, 2022 =

- Fix: Search category options fixed for the all search tab.
- Fix: Search address options fixed for the all search tab.
- Fix: Order corrected to display the newest first in dashboard activity widget.
- Tweak: Search selection results will be sorted in a-z order.

= 3.4.39 February 24, 2022 =

- Tweak: Adjustment to the $epl_author class to allow override and extending.
- Tweak: Added stroke and stroke-width to SVG allowed tags.
- Fix: Added missing arguments variable in epl_no_property_featured_image action.
- Fix: Using correctly spelt get_additional_commercial_features_html function.

= 3.4.38 February 07, 2022 =

- New: Shortcode template fallback. If a template file does not exist then the default will load instead of producing an error.
- New: Updated switch.png graphic to switch.svg for lossless scaling.
- New: Finnish Translation Added. Thanks to Eevastiina Hyvönen.
- New: Updated frontend scripts.
- New: Filter added for energy certificate: epl_energy_certificate_keys
- New: Filter added for external link: epl_external_link_keys
- New: Filter added for floor plans: epl_floorplan_keys
- New: Filter added for mini web: epl_mini_web_keys
- New: Filter added for widget read more: epl_property_widget_read_more
- New: Rental pricing. Using label_poa for no rental price.
- New: Added epl_price_rent_period filter.
- New: Added filter epl_pa_price for P.A label.
- New: Support for 3rd and 4th listing agents on commercial listings.
- New: Additional parameters default_template to pass the default template which will be used if the template is not found.
- New: Support for external/local hosted video formats like mp4, mov etc.
- New: Added custom body class epl-search-results for search results.
- New: Added filter epl_number_suffix for suffix.
- New: Added epl_get_property_available_date_time_separator filter for date/time separator.
- New: Added epl_get_property_auction_date_time_separator filter for date/time separator.
- Tweak: Admin area CSS. thumbnail aligned to the right for file type meta fields.
- Tweak: Added filter epl_property_featured_image_args to control all parameters & epl_no_property_featured_image action.
- Tweak: Added filter epl_property_archive_featured_image to control all parameters & epl_no_archive_featured_image action if no featured image.
- Fix: [listing_element] shortcode fatal error with global $property.
- Fix: Removed un-needed slider-handle.png requirements.
- Fix: Contacts system, bulk delete action not working.
- Fix: Don't display land area when it's < 0.
- Fix: For required parameter $field follows optional parameter $tag.
- Fix: jQuery error and sort function in some cases.

= 3.4.37 September 11, 2021 =

- Tweak: WordPress 5.8.1 introduced a CSS tweak the broke the flow of the admin fields on the listing entry screen.

= 3.4.36 March 25, 2021 =

- New: Additional action support for listing templates. Actions are: epl*loop_template*{post_type}, epl_loop_template_listing.
- Fix: Corrected issue with multi select where values are numbers.

= 3.4.35 December 23, 2020 =

- Tweak: Added support for WordPress 5.6 blocks rendering.
- Tweak: Support for true/false values in features checklist.
- Fix: [listing_advanced] not marked as EPL shortcode in WP_Query object.
- Fix: Incorrect meta key in carport function.
- Fix: Hidden field rendering.

= 3.4.34 October 1, 2020 =

- Fix: Admin columns output escaping.
- Tweak: Added hidden empty field for checkboxes so that they always show up in $\_POST data while saving.

= 3.4.33 September 24, 2020 =

- Tweak: Search results not found filter epl_property_search_not_found_title allows basic html to be passed.

= 3.4.32 September 23, 2020 =

- Fix: Further enhancements to loading SVG icons as many themes are missing wp_body_open. SVG's now fallback to load in the footer.

= 3.4.31 September 17, 2020 =

- Fix: SVG Icons preventing favicon from loading in some themes. SVG's are now loading in body tag using wp_body_open hook introduced in WordPress 5.2.

= 3.4.30 September 4, 2020 =

- Fix: Depreciated JS ready function.
- New: Render password field support.
- Fix: Search only variables can be passed via reference.
- Fix: Archive featured image filter missing parameter filter. Increased from 3 to 4.
- Fix: Property Features title set to pass basic html.
- Fix: Admin commercial options now using epl_get_meta_field_label for dynamic labels.

= 3.4.29 June 17, 2020 =

- New: Converted render field function to class for with control and flexibility.
- New: Custom body classes epl-single-listing for single and epl-post-type-archive for search results pages.
- Tweak: Meta box tabs and conditional fields.
- Tweak: Render HTML class & style for tabs.
- Fix: Issue when changing select into a select_multiple field was not saving.
- Fix: Json rendering in data-attributes.
- Fix: Hidden field type.
- Fix: Search widget was not rendering in page builders.
- Fix: Admin css on mobile.

= 3.4.28 May 25, 2020 =

- New: Option added to number formatting function to trim decimal numbers.
- New: Admin filter epl_land_value_decimal_format for land value decimal formatting.
- New: Globally loading shortcodes and widgets in order to allow page builders to use widgets. Additionally this will now render shortcodes when using front-end page builders.
- New: Helper action epl_property_status used to output the listing status in templates.
- Tweak: Enhancements to the new stickers function.
- Tweak. Altered land formatting in admin to remove decimals from land values unless they have decimal places.

= 3.4.27 May 22, 2020 =

- New: Removed change log from plugin, this change was made to aid translators in translating the plugin to 100% and not need to translate the long change log items.
- New: Added sticker support to allow the disabling of image stickers to the epl_property_archive_featured_image function.
- New: Added filter epl_inspection_link to handle inspection time link. Allowing handling of non date inspection values in the get_property_inspection_times function.
- New: Added prefix support to epl_get_the_address helper function.
- New: Stickers helper function epl_stickers which renders stickers, based on meta values, an alternative to the epl_price_stickers function.
- Tweak: Helper class epl-grid-hidden block display on list mode.
- Tweak: Better support for various YouTube style links.
- Fix: Escaping issue and formatting for land size in admin.
- Fix: Dashboard activity widget comment positioning.
- Fix: Translation issue with rent period.
- Fix: Issue with excerpt length option not working correctly with some themes.
- Fix: Depreciated epl_excerpt_length function and filter. Retained function for compatibility.
- Fix: No spaces between classes when both class & template attributes are set.

= 3.4.26 April 15, 2020 =

- New: Added Avada theme support.
- Tweak: Re-ordered commercial authority options.
- Tweak: Minor function optimisations.

= 3.4.25 March 31, 2020 =

- New: Filters added to floorplan, external link, mini web and energy certificate that allow you to disable then through a filter, great for extensions to control the default button. eg set the epl_show_property_energy_certificate to false to disable button rendering.

= 3.4.24 March 20, 2020 =

- New: Read More button action hook epl_button_read_more to use in templates accepts a passed $label.
- Tweak: Corrected Floor Plan spelling into 2 words.
- Tweak: Refactored mini web url output by adding, epl_button_label_mini_web filters for labels.
- Tweak: Added full image size as option with selecting image sizes using epl_get_thumbnail_sizes function.
- Fix: Floor plan label filter was incorrect. Filters are now epl_button_label_floorplan & epl_button_label_floorplan_2.

= 3.4.23 March 18, 2020 =

- Tweak: Added land unit filter epl_property_land_area_unit_label to admin area when viewing listings.
- Tweak: Altered the admin output of property category to use the label instead of value.
- Tweak: Removed compatibility template for loop as we are passing the class using post_class filter. If you were using loop-listing-blog-default-compatibility.php in your template remove -compatibility from the filename.
- Tweak: Ordered the additional featured items to a-z order.
- Fix: Security function check.

= 3.4.22 February 26, 2020 =

- Fix: For attribute added for checkbox labels in search.

= 3.4.21 January 22, 2020 =

- Tweak: Corrected the new parking filter.

= 3.4.20 January 22, 2020 =

- New: Filter epl_total_parking_spaces added to the parking spaces function allowing you to alter the total value of the parking icons.
- New: Helper epl_get_post_id_from_unique_id function to get the WordPress post id from the property_unique_id field.
- Tweak: Better flushing of permalinks when settings are saved or on install.

= 3.4.19 December 19, 2019 =

- New: Added epl_recent_property_widget_default_args and epl_recent_property_widget_query filters for defaults and query args making targeting widget query easier in pre_get_posts.
- Tweak: Updated to new html structure for fields using html lists in new custom post types for extensions.
- Tweak: Removed article wrapper from Divi Template.

= 3.4.18 December 10, 2019 =

- Tweak: Added a wrapper css class epl-section-map to the default map output wrapper.
- Fix: Internal help page video link was not working correctly for Adding Listing.

= 3.4.17 December 2, 2019 =

- Tweak: Moved Readme widget option to appear before the Read me label.
- Tweak: Wrapper for wp_doing_ajax with fallback for 3.7 versions of WordPress or lower.
- Fix: SVG filtering now allows circle tag correcting issue with LinkedIn icon missing a dot.
- Fix: Empty values not getting saved for decimals and numbers.

= 3.4.16 November 25, 2019 =

- Fix: Added check for post type in epl_admin_posts_filter to avoid conflict with other plugins like ninja forms.
- Fix: Filter by property author now shows results for both primary and secondary author.

= 3.4.15 November 21, 2019 =

- New: Migrated from PHP Session to WP Session to prevent WordPress > Tools > Site Health REST API error.
- Fix: City label missing translation string.

= 3.4.14 November 13, 2019 =

- New: Support for WordPress 5.3 TwentyTwenty theme.
- Fix: Custom additional features hook corrected as custom features were outputting incorrectly.

= 3.4.13 November 11, 2019 =

- Tweak: Listing widget: Custom template display, file extension no longer required and file name format enforced to the format widget-content-listing-{template_name}.php
- Tweak: CSS convert helper grid class to inline-block instead of float left.
- Fix: Search was not searching correctly when no post type was set.
- Fix: Listing Widget bed/bath only icons SVG output.

= 3.4.12 November 1, 2019 =

- Fix: Loading admin css and js on appearance > widgets screens.

= 3.4.11 October 30, 2019 =

- Fix: Empty check in date function to prevent notice errors during listing imports.
- Fix: Index check in iCal function and additional check to avoid notices.

= 3.4.10 October 23, 2019 =

- Fix: Shortcodes [listing_category] and [listing_advanced] when using numbers to filter ranges. e.g. category_compare="BETWEEN" and NOT_BETWEEN.
- Fix: Issue with listings dashboard widget when no post types are active.

= 3.4.9 September 17, 2019 =

- New: Added the listing URL to the iCal card.
- New: Enhanced the epl_property_address action hook to support multiple address parameters to easier control output in templates.
- New: Enhanced the epl_property_category action hook to support tag and class that is passed through in templates.
- Fix: iCal inspection time cards were trimming the description.

= 3.4.8 September 16, 2019 =

- Tweak: Added filter to optionally control separator output to appear after suburb with epl_property_address_separator_suburb or after the city with the epl_property_address_separator_city filter.
- Fix: Correction to address separator placement that appeared after suburb in some cases.
- Fix: Passing the third link parameter to the feature image through a hook has been corrected.

= 3.4.7 September 16, 2019 =

- Tweak: Whitelist use tag for SVG usage.
- Fix: Correct business listing type pricing display, behaves like listing for sale.

= 3.4.6 September 9, 2019 =

- Tweak: Support for polygon tag in SVG icons.
- Fix: Price display error in admin columns on commercial listings types that were set to both sale and lease.
- Fix: Home open title appearing in admin columns when imported data was empty.

= 3.4.5 September 5, 2019 =

- Fix: Editor custom field type stripping html.
- Fix: Remove strict type for checkbox & select multiple field types which prevented saving number type array options.

= 3.4.4 September 4, 2019 =

- Fix: Checkbox array options not saving correctly in extensions settings.
- Fix: Removed default template check for loop and single templates as this caused incorrect templates to load in some cases.

= 3.4.3 August 29, 2019 =

- Fix: Displaying of Geo and Unique ID columns in admin.

= 3.4.2 August 27, 2019 =

- Fix: Issue when using WordPress default pagination, output corrected.

= 3.4.1 August 25, 2019 =

- New: Hidden meta fields for currency support, and floor plan modified date time.
- Tweak: Support for meta fields file type to support as an array of data.
- Tweak: Allow embeds and scripts in meta fields like floor plans and energy certificates to support an array of data which is handy during data import.
- Tweak: Corrected undefined variables in widgets since WordPress 5.2.2.
- Tweak: Admin contacts added wrapper classes to contact values.
- Fix: Currency support for search widget price sliders.
- Fix: Commercial search fix, widget select type field fix: not saving.
- Fix: Agent search suggestions in admin.
- Fix: Reports graph date display issues.
- Fix: Search templates will no longer ignore custom fields added using filters.
- Fix: When adding dynamic content to a listing using the WordPress editor default WordPress behaviour is restored allowing page builder content to display correctly.

= 3.4 August 16, 2019 =

- MAJOR Security Update Release. Important to update to the latest version to protect your website. Easy Property Listings has been reviewed and approved by the WordPress plugin team.
- WordPress standards coding applied to all plugin files and code.
- This is a critical update to Easy Property Listings plugin that is focused on security enhancements. Update you site to this version.
- New: Reset settings to default values tool page added viewable with &dev=true added to tools page URL.
- New: Added autocomplete option to meta-fields array.
- Tweak: Internal code documentation enhanced.
- Tweak: Admin, structure, style, enhanced, legacy CSS optimisations.
- Tweak: JS enhancements, improvements and optimisations.
- Tweak: Removed depreciated author meta compatibility file.
- Tweak: Removed depreciated listing meta compatibility file.
- Tweak: Removed depreciated extensions compatibility file.
- Tweak: Ability to arrange EPL - Search Widget options dashboard field order.
- Tweak: Wording alteration for Inspection Times - removed (one per line).
- Tweak: Removed subscriber user type from Dashboard filtering by User.
- Tweak: Now using WordPress jQuery touch punch JS script.
- Tweak: Removed cURL php requirement and instead use WordPress helper function.
- Tweak: Additional CSS classes added to SVG icons.
- Tweak: Fix bath and car svg wrapper class name.
- Tweak: Author box will no longer display bio tab is user bio is empty.
- Fix: Minimised scripts and css in admin areas to EPL pages.
- Fix: Versioning added to epl js scripts.
- Fix: Upgrade database on new installs message no longer displaying as no action is required.
- Fix: Business listing type pricing.
- Fix: Translation strings corrected in several places across the entire plugin. Internal translation guides added.
- Fix: jQuery UI CSS now loading minified version in production mode.
- Fix: [listing_element] shortcode will now display shortcode values when using frontend GUI builder.
- Fix: Loading custom CSS using the style-single.css now works correctly.
- Fix: XSS security flaw.
- Important Security Update.

= 3.3.3 June 13, 2019 =

- New: Shortcode [listing_meta_doc] for custom field documentation output.
- Tweak: CSS tweaked for list/grid mode preventing bullets appearing on some themes.
- Tweak: Added fallback function for reporting if PHP module not enabled on some servers.
- Tweak: New installations will no longer see database upgrade notice as it is not required.
- Tweak: Improvements to property_price_global function.
- Tweak: Code optimisation and added internal documentation to functions missing notes.
- Tweak: When importing listings a helper function would in some cases trigger a notice error.
- Tweak: Attached files would in some cases produce an error on imported listings with no files attached in the dashboard.
- Fix: Reporting graphs were some times blank when viewing last year then this year.
- Fix: Reworked database upgrade process for larger workloads processing 200 records at a time to prevent server timeouts.
- Fix: Missing post wrapper classes for iThemes builder and Heuman theme when using Listing Template extensions and masonry effect.
- Fix: Using default WordPress pagination with shortcodes corrected as they have all been enhanced to support multiple shortcodes on one page using the instance_id= option.
- Fix: Pagination corrected for shortcodes when using on the site home page.
- Fix: Commercial and Business listing types were not obeying the hide suburb option in some cases.

= 3.3.2 May 31, 2019 =

- Tweak: Added epl-clearfix to shortcode template to better clear when using page builder plugins.
- Fix: New search feature corrected when no post type is set.

= 3.3.1 May 27, 2019 =

- Tweak: Grid CSS tweaked when using Enhanced CSS option with some themes.
- Tweak: Altered the options to the new [listing_element] shortcode for easier use and documentation.
- Fix: Warning and notice errors when using the new [listing_advanced] shortcode with no options.
- Fix: Error when using the new [listing_element] shortcode.

= 3.3 May 22, 2019 =

- New: Gutenberg support along with the REST WordPress API. Using the WordPress classic plugin will disable Gutenberg and Easy Property Listings or define constants.
- New: Shortcode [listing_advanced] that is a super powered shortcode with a million possible filters possible.
- New: Shortcode [listing_element] for use with page builder systems to output variables, meta fields, action hooks, excerpt, onto templates.
- New: Re-created Social SVG and PNG Icons.
- New: Production/Development mode option to enable or disable minimised CSS and JS files.
- New: Enhanced CSS implemented to enable better listing grid wrapping along with a large number of helper classes to construct custom templates for listings. Enabled on new installations. When upgrading the enhanced CSS is disabled. Enable and disable from Advanced Settings.
- New: Tools page holding Import and Export options along with the Upgrade screen.
- New: Export Easy Property Listings settings and import them into another site on the Tools page.
- New: Shortcodes [listing], [listing_category] and [listing_advanced] all support agent option for filtering by either primary or secondary agents.
- New: Shortcodes now support default sorting by status with the sortby=status option.
- New: Search results URL will now only contain commands that are being searched, making the URL a whole lot shorter.
- New: Upgrade notice that will copy the listing pricing for all listing types into a unified price search.
- New: Unified price search slider usable in the EPL - Listing Search widget and [listing_search] shortcode.
- New: Able to add a functions.php, functions-single.php and functions-archive.php files to the active_child_theme/easypropertylistings where you can store code and filter customisations.
- New: Able to add style-single.css and style-archive.css to the active_child_theme/easypropertylistings where you can store CSS customisations along with the already implemented style.css file.
- New: Implemented a Featured listing system that will allow you to set your listings as featured from the listing administration page. With the update to the EPL Importer add-on listings that you mark as featured will stay featured when updated from external sources.
- New: Major tweaks to the custom fields screens allowing for a smaller data entry screen when adding your listing details.
- New: Contacts now allow you to bulk select and delete contacts.
- New: Contacts summary search.
- New: Contact widget now has a hidden field to prevent bots from entering form details.
- New: Contact shortcode renamed to [listing_contact] instead of [epl_contact_form]. Retained old shortcode name for backward compatibility.
- New: EPL - Contact form completely re-built with better handling of success and error messages to the user.
- New: Added a notice to the frontend explaining where the map should go when a Google Maps API key is not set and re-built the JS to avoid any issues when the API key is not added.
- New: Easier to add additional custom stickers to listings with the epl_property_stickers hook.
- New: Address function for use in custom templates epl_the_address, epl_get_the_address.
- New: Status functions for use in custom templates epl_the_status, epl_get_the_status.
- New: Under Offer functions for use in custom templates epl_the_under_offer, epl_get_the_under_offer.
- New: Enabled Mini Website URL meta field as a number of REAXML providers are now using 3D Tours along with support for custom button titles.
- New: Able to configure sorting dropdown as tabs.
- New: EPL - Author widget supports single or multiple users with user searching capabilities. Allowing you to use the widget and set a specific agent or WordPress user.
- New: Floor plan uploader now support a custom label added to the listing entry screen. Label filter is still in place.
- New: External Links now 3 are now supported with a custom label. Label filter is still in place.
- New: Mini Website URL, 2 are supported with a custom label. Label filter is still in place.
- New: Energy Certificate supports a custom label. Label filter is still in place.
- New: Map Icon with filter for customising.
- New: Listing Map Icon for when the location is not exact, IE when the user selects to not display the full address.
- New: Users profiles now have Instagram, Pinterest and YouTube links and social icons added.
- New: Pets icons and function to handle output with the get_property_pets function.
- New: Rebuilt several functions which now allow for greater filtered output. Rebuilt functions are: get_property_year_built, get_property_bed, get_property_bath, get_property_rooms, get_property_parking, get_property_garage, get_property_carport, get_property_air_conditioning, get_property_pool, get_property_security_system, get_property_land_value, get_property_building_area_value, get_property_energy_rating, get_property_new_construction, get_property_holiday_rental, get_property_furnished. (New Functions) get_property_pets, get_property_featured.
- New: Rental Leased Date custom field.
- New: Ability to apply multiple templates using the epl_property_single_default filter to the single listing template loading queue.
- New: Ability to apply multiple templates using the epl_property_blog_template filter to the loop listing template loading queue.
- New: Specify the default return type of the property listing icons. Basically using the epl_icons_return_type filter you can force all icons to output text instead of an icon. Or configure a completely custom output.
- New: Improvements to video link handling with YouTube and Vimeo support.
- New: Video hook implemented into templates. Before we were using the epl_property_content_after hook where we have now added a better named epl_property_video action hook.
- New: Random sorting option added to [listing_category], [listing]
- New: Search by linked contacts — search_linked_contact.
- New: YouTube User Profile link.
- New: Office Phone added to user profile and outputs on author profile and widget.
- New: Default and custom classes added to meta field generator used in admin edit listing screens.
- New: Added Portugal Portuguese language partial translation by Fábio Nunes.
- New: Disable REST support and Gutenberg by defining new constants for post types. EPL_BUSINESS_DISABLE_REST, EPL_COMMERCIAL_DISABLE_REST, EPL_COMMERCIAL_LAND_DISABLE_REST, EPL_LAND_DISABLE_REST, EPL_PROPERTY_DISABLE_REST, EPL_RENTAL_DISABLE_REST, EPL_RURAL_DISABLE_REST.
- New: Disable REST support and Gutenberg by defining new constants for taxonomies. EPL_BUSINESS_CAT_DISABLE_REST, EPL_FEATURES_DISABLE_REST, EPL_LOCATION_DISABLE_REST.
- New: Pakistani Rupee currency.
- New: Better support for hidden admin custom fields.
- Tweak: Filters to alter the default Mobile and Office labels epl_author_widget_label_office, epl_author_widget_label_mobile.
- Tweak: Custom classes in admin screens added to some meta fields for example usage.
- Tweak: Dashboard activity widget improved CSS display with icons denoting comment type.
- Tweak: Rebuilt JS validation engine to better handle numeric numbers and the date system to optimise and streamline listing entry.
- Tweak: Rebuilt mapping JS to avoid issues with websites missing Google Maps API keys.
- Tweak: Moved several old functions to compatibility functions for future removal. Functions moved are epl_the_property_address, epl_display_label_postcode, epl_display_label_bond, epl_display_label_suburb.
- Tweak: Inspection times function altered to allow disabling of iCal function.
- Tweak: Added additional inspection time filters which allow you to filter the label further in templates where needed.
- Tweak: Auction get_property_auction function greatly improved with better date formatting for different country formats.
- Tweak: Property Available get_property_available function greatly improved with better date formatting for different country formats.
- Tweak: Improvements to search and global price search slider options now available.
- Tweak: Parse EPL shortcodes for meta queries improved.
- Tweak: Completely re-worked the icons system for listings which makes it easier to customise with hooks and filters and add additional icons and adjust the order.
- Tweak: Reworked the Additional features output allowing further control over output of features along with adjusting the order and output style. Use the epl*property_general_features_list filter to alter the order and what is included. Also add your own customised feature list output with the dynamic epl_property_general_feature*{new_one} filter.
- Tweak: Total rebuild of the sorting and tools code allowing additional tools to be hooked in and rework the existing tools.
- Tweak: Code optimisations of gallery functions.
- Tweak: Widget templates to handle the Inspection time and iCal options.
- Tweak: Selecting secondary agents works better now on the edit listing pages.
- Tweak: epl_property_widget Options for Inspection times and iCal links to EPL - Listing Widget.
- Tweak: Acre to Acres tweak - Implement formatting for measurements.
- Tweak: Pagination and multiple shortcodes on the same page working correctly.
- Tweak: Removed Google Plus from Users as it no longer exists.
- Tweak: Removed redundant before and after hooks on the manage listing screens.
- Tweak: All shortcodes now include a filter to alter the default options. epl_shortcode_listing_auction_args, epl_shortcode_listing_category_args, epl_shortcode_listing_open_args, epl_shortcode_listing_feature_args, epl_shortcode_listing_location_args, epl_shortcode_listing_args
- Tweak: Replaced grid/list icon with a better quality one.
- Tweak: Replaced all internal icons.
- Tweak: Several older functions moved to depreciated functions for future removal.
- Tweak: Converted floor plan button into button element instead of link with button styling. This makes all buttons consistent.
- Tweak: When using custom image sizes we've improved the admin image sizes to not exceed the column sizes when managing listings from the dashboard.
- Tweak: Floor plan opens link in another window by default to match other buttons.
- Tweak: Contacts system better displays contact information. IE hiding empty fields when nothing is set.
- Tweak: Default sorting is now using the hidden and automatically generated property_price_global value which allows sorting taxonomy filtered listings.
- Tweak: Custom taxonomy features now output a class to the list item for css targeting.
- Tweak: Unique IDs applied to the SVG icons as they were duplicated for each icon.
- Fix: SVG Listing icons filter epl_svg_icons corrected and will now correctly load customised SVG icons.
- Fix: SVG Social icons filter epl_svg_social_icons corrected and will now correctly load customised SVG icons.
- Fix: Archive image hook epl_property_archive_featured_image third option for link now working.
- Fix: Widget image hook epl_property_widgets_featured_image third option for link now working.
- Fix: Images no longer overlap in admin when the image filter is used to alter the default image sizes.
- Fix: [listing_auction] shortcode now correctly lists set auctions.
- Fix: [listing_open] shortcode now correctly lists listings that have an inspection set.
- Fix: Home Open label improved as sometimes empty data is imported and the label appears where it should not.
- Fix: [listing_search] enabling the multiple select option now works better.
- Fix: Several typcasting fixes to various functions to prevent any issues with data that is not entirely empty.
- Fix: Features list output class name fix for bathrooms, building size and furnished.
- Fix: Improvements to compatibility mode that prevents YoastSEO outputting multiple times on the page. This will also correct any other plugins with a similar issue.
- Fix: Adding of contacts with no summary is now possible.
- Fix: When checkbox option defaults were set to on user was unable to save option, corrected the behaviour.

= 3.2.3 November 22, 2018 =

- New: WordPress 5 TwentyNineteen theme support.

= 3.2.2 May 23, 2018 =

- New: Added business category taxonomy slug filter.
- New: Added DEFINED FILTERS to taxonomy slug Additional Filters added to Taxonomies. Business Categories
- New: Filter epl_property_featured_image_link allow user or extension to enable or disable link behaviour on featured image.
- New: Filter epl_property_archive_featured_image_link allow user or extension to enable or disable link behaviour on archive image.
- New: Added a epl_property_gallery_shortcode filter.
- Tweak: Renamed business_listing category slug to business-category permalinks will automatically re-fresh on update.
- Tweak: Removed business category from EPL meta-boxes, use the hierarchal taxonomy to manage categories and sub categories when editing business listings.
- Tweak: Added widget names to code for filter and ensure backward compatibility.
- Tweak: Implemented responsive breakpoints in epl-container-grid css class.
- Fix: Business Search Categories in Listing Search widget and shortcode.
- Fix: Enhanced for PHP 7.2 and removed create_function depreciated notice.

= 3.2.1 March 24, 2018 =

- Tweak: Implemented a minimum width to Property Features info columns for better responsive formatting.
- Tweak: Changed Energy Efficiency rating to text field to support EU requirements.
- Tweak: Renamed EER (Energy Efficiency Rating) to Energy Rating, adjustable with epl_get_property_energy_rating_label filter.

= 3.2 March 22, 2018 =

- New: Refactored meta-boxes to allow for better translations of additional features options on frontend.
- New: Search fields allow for placeholder to be defined for each search field.
- New: Ability to sort be featured image in the dashboard when managing listings allowing user to sort columns by listings without a featured image set.
- New: Search by listing features now possible when using EPL - Listing Search widget or [listing_search] shortcode.
- New: Placeholders set for Land Min Area and Max Area.
- New: Adjustments to taxonomy searching allowing search of features and locations or additional custom taxonomies.
- New: Removed changelog entries from plugin core files to greatly reduce translation requirements for plugin and translation will be far easier for translators now.
- New: Change log items are parsed from readme.txt file which removes the need to translate over 700 change log entries.
- New: Added a new CSS class epl-property-features to the listing features column.
- New: Searching by features taxonomy now possible with [listing_search] shortcode and EPL - Listing Search widget.
- New: Search Query Filter epl_search_query_pre_parse allows altering of query after its ready to be processed. I.e. after query is setup but before parsing it.
- New: Search Query Filter epl_search_get_data allows filtering of $\_GET & $\_POST data which is fed to epl search class.
- New: Search Query Filter epl_search_post_data allows filtering of $\_GET & $\_POST data which is fed to epl search class.
- New: Search Query Filter epl_preprocess_search_tax_query filter to alter taxonomy query.
- New: Search results template filter epl_common_search_template allowing using an alternative template for search results.
- New: Filter for epl_get_unique_post_meta_values to adjust data parsing.
- New: Selection to set default country for map coordinate generation when adding listings.
- New: Implementation to support beta releases of extensions, enabled when EPL_BETA_VERSIONS is true.
- New: Added additional filters for epl_author_mobile, epl_author_id, epl_author_slogan, epl_author_position, epl_author_name and epl_author_contact_form.
- New: Implemented support for energy rating value on listings as it is mandatory in some regions. New meta field is property_energy_rating.
- New: Added Energy Certificate link to listings which allows for an image upload and button output. Field is property_energy_certificate.
- New: Ability to display offmarket and withdrawn listings on archive pages through the epl_hide_listing_statuses filter.
- New: Migrated Author details tab into a new template file content-author-box-tab-details.php that can be overridden in active_theme/easypropertylistings folder which enables easier editing of the details tab contents.
- New: Filters added for listing admin columns allowing other plugins to hook in correctly to listing dashboard columns and display additional info like Yoast SEO, Post Counter and many other WordPress plugins: epl_post_type_business_admin_columns, epl_post_type_commercial_admin_columns, epl_post_type_commercial_land_admin_columns, epl_post_type_land_admin_columns, epl_post_type_property_admin_columns, epl_post_type_rental_admin_columns, epl_post_type_rural_admin_columns.
- New: Implemented a filter epl_common_search_template to allow altering of search results template. Default is archive-listing.php and can be overridden by creating a search-listing.php or by post type search-{post_type_name}.php.
- New: Ability to search for primary listing agent when adding a listing, secondary agent already has this functionallity.
- New: Added additional options to the image functions which allow disabling of the image links to epl_property_featured_image, epl_property_archive_featured_image, epl_property_widgets_featured_image.
- New: Added options for SVG icons for listing icons and author social icons. Enable from Settings.
- New: SVG assets added using inline allowing for icon styling using CSS.
- New: Implemented filters to replace or add additional SVG icons Filters are epl_svg_icons and epl_svg_social_icons.
- New: Enhanced author class with additional get variables to return the formatted value without html content.
- New: Added options to return text values to each of the get_property functions. Allowing return of a text result instead of a formatted list item.
- New: Filters added to adjust the return format of each get_property function: epl_get_property_year_built_return_type, epl_get_property_bedrooms_return_type, epl_get_property_bathrooms_return_type, epl_get_property_rooms_return_type, epl_get_property_parking_spaces_return_type, epl_get_property_garage_return_type, epl_get_property_carport_return_type, epl_get_property_air_conditioning_return_type, epl_get_property_pool_return_type, epl_get_property_security_system_return_type, epl_get_property_new_construction_return_type, epl_get_property_holiday_rental_return_type.
- New: Added functions for get_property_furnished and get_property_holiday_rental.
- New: House Category now displays in admin column for quick identification of the listing type.
- Tweak: Removed screenshots to reduce plugin size.
- Tweak: Removed Whats New page descriptions to significantly reduce translation requirements for translators.
- Tweak: Admin Getting Started CSS enhancements.
- Tweak: Adjustment to the handling of the primary and secondary listing agents on listings. When a primary agent details are entered this will be the listing agent displayed. If the primary agent for a listing is not set then the Author will be the primary agent displayed.
- Tweak: Added option to use a seperate template for search results.
- Tweak: Corrected building size css class name from land-size to building-size.
- Tweak: Extension updater class cache improvements implemented.
- Tweak: Updates to licensing system to support beta releases of extension versions.
- Tweak: Class check to ensure that the EPL_Author_Meta is already defined, class will not load in error.
- Tweak: Field type checkbox_single altered so that the label is not displayed twice when adding a custom field.
- Tweak: Altered the plugin loading order for better WPML support.
- Tweak: Better support to search multiple post types.
- Tweak: Search better supports multiple post types in an array: epl_get_unique_post_meta_values('property_bedroom','current', array('property','rental') ).
- Tweak: Sorting option better allows sorting options based on post types by passing post type.
- Tweak: Contact form CSS added for better formatting.
- Tweak: Adjusted admin images padding.
- Tweak: Added date picker to Listed Date and Commercial End Lease date fields.
- Fix: Translations added for Admin on commercial listings (Return, Outgoings, Lease End )
- Fix: Sorting by rental price corrected in admin for Rental listings.
- Fix: Land details missing on Commercial Land listing type.
- Fix: Select form render multiple selections for search.
- Fix: Price bar graph in admin when no price set.
- Fix: Translation issues for search drop downs.
- Fix: Location taxonomy search redirection.
- Fix: Sorting by Unique ID in Dashboard manage listings.
- Fix: Carport label filter name corrected to epl_get_property_carport_label.
- Fix: Added land size to commercial land.

= 3.1.19 July 12, 2017 =

- New: Filter added to allow filtering of property meta with epl*meta_filter*{property_meta_key_name}.
- Tweak: Allow Full URL for user profile, Twitter, Facebook, Google Plus accounts.
- Fix: Corrected the epl_property_sub_title_commercial_features filter to allow altering.
- Fix: Corrected the epl_property_sub_title_rural_features filter to allow altering.
- Fix: Corrected the epl_switch_views_sorting_title_sort filter to allow altering.
- Fix: Corrected the epl_switch_views_sorting_title_list filter to allow altering.
- Fix: Corrected the epl_switch_views_sorting_title_grid filter to allow altering.

= 3.1.18 July 7, 2017 =

- Fix: Corrected Commercial and Business epl_property_suburb function to only display suburb.

= 3.1.17 June 15, 2017 =

- Fix: Geocoding Address with only partial address details will now generate coordinates.
- Fix: Sorting rentals after performing search would sometimes return no results.

= 3.1.16 May 25, 2017 =

- New: Rebuilt search CSS containers for easier formatting with exact widths.
- New: Filter epl_property_category_value for altering house category.
- New: Add Listing Status and Under Offer to post class.
- New: Added Commercial Type to post class.
- Tweak: Ability to display multiple categories on listings.
- Fix: Corrected returning of none and added value to get_property_category, get_property_land_category, get_property_commercial_category and get_property_rural_category functions.
- Fix: Rental sorting error in listing shortcodes.
- Fix: Author widget on pages with sorting.

= 3.1.15 May 17, 2017 =

- Fix: Car searching Any will now return listings with no carport or garage.

= 3.1.14 May 9, 2017 =

- Tweak: Allow author box to be used on non Easy Property Listings posts without error.
- Tweak: Removed Brazilian Portuguese from plugin as language package is now served from WordPress.org

= 3.1.12 April 27, 2017 =

- New: Filter epl_property_land_area_unit_label for Land Unit Label Filter.
- New: Filter epl_property_building_area_unit_label for Building Unit Label Filter.
- New: Filter epl_the_property_feature_list_before before the features list.
- New: Filter epl_the_property_feature_list_before_common_features before the common features list.
- New: Filter epl_the_property_feature_list_before_additional_features before the additional features list.
- New: Filter epl_the_property_feature_list_after for after the output of the features list.
- Tweak: Property, Rural, Commercial Category output to secondary heading.
- Tweak: Altered land sqm output to m2.
- Tweak: Shortcode [listing_auction] now only displays auction listings.
- Fix: Property Category now outputs to feature list.
- Fix: Rural Category now outputs to feature list.
- Fix: Commercial Category now outputs to feature list.
- Fix: Empty Commercial Features heading no longer outputs heading if values are empty.
- Fix: Empty Rural Features heading no longer outputs heading if values are empty.

= 3.1.11 April 6, 2017 =

- Fix: Property ID search in admin.
- New: Brazilian Portuguese Translation thanks to Dijo.
- New: Added epl_button_target_floorplan filter.

= 3.1.10 March 27, 2017 =

- New: Filter added epl_ical_args for iCal output.
- Tweak: Ability to search by property ID when managing listings from the Dashboard.
- Tweak: Added Sortable column Unique ID.

= 3.1.9 March 23, 2017 =

- Tweak: Allowed Authors and Contributors to access help screens.

= 3.1.8 March 22, 2017 =

- Fix: Corrected Listing not found filters used in archive templates with a new epl_property_search_not_found hook.
- Tweak: Translations updated.

= 3.1.7 March 22, 2017 =

- New: Added epl_template_class to templates and added its context for Listing Templates extension.
- New: Auction Date processing function for import scripts.
- New: REAXML convert date/time to adjust for timezone for import scripts.
- Tweak: Wording for delete settings adjusted to reflect radio option.
- Fix: Corrected missing Property Features title and filter.

= 3.1.6 March 10, 2017 =

- New: Hierarchical Features Taxonomy EPL_FEATURES_HIERARCHICAL Constant.
- New: Filter for Commercial For Sale and Lease label epl_commercial_for_sale_and_lease_label when both option selected.
- New: Added filters for shortcodes to adjust no results messages. Filters epl_shortcode_results_message_title_open for [listing_open] shortcode and epl_shortcode_results_message_title for all other shortcodes.
- Tweak: Additional case values for importing additional features now accepts YES, yes, Y, y, on, NO, no, N, n, off.
- New: Common features filter epl_property_common_features_list added.
- Tweak: Corrected spelling of meta box group ids for commercial_features and files_n_links.
- Tweak: Author widget will no longer display if hide author box on a listing is ticked.
- Tweak: Filter for epl template class.
- Fix: Commercial listing lease price text display when both option selected.
- Fix: Property Features title filter epl_property_sub_title_property_features enabling title modification.
- Fix: Post type archive called incorrectly in some cases.
- Fix: PHP 7.1 support.
- Fix: Class adjustment for taxonomy search.

= 3.1.5 January 18, 2017 =

- New: Added a Google Maps API key notification to Easy Property Listings > Settings when no key is set.
- Tweak: Internal shortcode option documentation.
- Fix: Shortcode offset feature breaking pagination. Note when using offset, pagination is disabled: [listing] , [listing_category], [listing_feature], [listing_location]
- Fix: Corrected the default option when using select fields.

= 3.1.4 January 16, 2017 =

- New: Added offset option to the following shortcodes that allows you to place multiple shortcodes on a single page and prevent displaying duplicate listings. Added to the following shortcodes: [listing] , [listing_category], [listing_feature], [listing_location]
- Tweak: Optimisations to secondary author display by removing duplicate code.
- Tweak: Improvements to extension license updater and notifications on license status.
- Tweak: Performance improvements to admin functions.
- Tweak: Translations adjustment to load textdomain after all plugins initialised.

= 3.1.3 January 3, 2017 =

- Fix: Contact linking when editing listings with invalid contact ID.
- Fix: Shortcode sorting for Current/Sold.
- Fix: Commercial Lease price display.
- Tweak: Output Ensuite to features list.

= 3.1.2 December 13, 2016 =

- Fix: Corrected the address display of the Commercial and Business listing types.
- Fix: Extension updater class to provide automatic updates.
- Tweak: Visiting the plugins page now caches plugin updates.

= 3.1.1 December 6, 2016 =

- Fix: [listing] shortcode with author option correctly filters by username.
- Fix: Listing search undefined result when using custom search options.

= 3.1 November 28, 2016 =

- New: Rebuilt templates including additional wrapper for better grid layout.
- New: Added legacy CSS option to prevent using new stylesheets when updating to 3.1 ensuring your listing display remains consistent.
- New: Enhanced grid wrapper CSS to better display listings in a grid format and improved CSS by splitting global style.css with style-structure.css allowing for better compatibility with themes.
- New: Class based front JS scripts for enhanced compatibility.
- New: Implemented cron checking in extension license handler and updated license updater EDD code.
- New: Added filter for epl_get_contacts_args to enable contact form field changes.
- New: Added epl_get_next_contact_link_query filter to adjust contact query.
- New: Added epl_contact_access filter to adjust contact system access by user level.
- New: Contextual help tab on listing pages.
- New: Added epl_author_description_html filter to adjust the author description.
- New: Cron added to handle scheduled events like license checking and updating.
- New: Auction epl_auction_feed_format date format filter added.
- New: Added epl_get_property_com_rent to allow commercial rent price formatting.
- New: Search radio option and checkbox added.
- New: Refactored search into class based code.
- New: Commercial search added (beta) disabled by default.
- New: Conditional post types added for checking on enabled listing types.
- New: Support for DIVI theme framework.
- New: Added epl_meta_commercial_category_value to adjust commercial category.
- New: Parse EPL shortcodes for meta queries.
- New: Widget template no image added.
- New: Sorting order function added.
- New: Pagination option added to all listing shortcodes pagination = 'on' default.
- New: [listing_category] shortcode added compare option. category_compare = 'IN' usage is based on SQL query options. 'IN','NOT IN','BETWEEN','NOT BETWEEN'
- New: Wrapper added to templates to improve display and provide even grid spacing.
- New: Added search address to separate from ID search.
- New: No image icon for listing attachments.
- New: Display lease price if nothing selected.
- New: Added epl_get_property_price_lease_display filter to control lease price display.
- New: License checker for updates set to daily and constant added to improve plugin page performance and reduce the update checker frequency.
- New: Load custom stylesheet from active_theme/easypropertylistings/style.css
- New: Added Pet Friendly options.
- New: Search frontend radio option epl_frontend_search_field_radio.
- New: Search frontend multiple checkbox option epl_frontend_search_field_checkbox_multiple.
- New: Search placeholders added to text fields.
- New: Correctly wrap epl_the_excerpt.
- New: Divi theme support.
- New: Select multiple added as custom field ability.
- New: Custom field option checkbox_option.
- New: Pet Friendly option added to rentals.
- New: Open Parking spaces added to listings.
- New: Prefixed additional css in templates for better styling.
- Tweak: License handler using https.
- Tweak: Improvements to contact actions.
- Tweak: License styling improved for better WordPRess UX.
- Tweak: LinkedIn link adjusted for worldwide usage.
- Tweak: get_property_meta improved.
- Tweak: Commercial leased sticker corrected.
- Tweak: property_land_area adjustment for numerical value.
- Tweak: Commercial and land category correctly displaying.
- Tweak: On activation the Property post type is enabled by default.
- Tweak: Improvements to listing widget.
- Tweak: Inspection time and date format improved.
- Tweak: File option added to external links for floor plans.
- Tweak: Template wrappers prefixed for details, property meta, icons, address, content.
- Tweak: Languages moved for better compatibility with translation plugins.
- Tweak: Listing search widget status label.
- Tweak: Reset page sorting when performing a search on a sub page with a widget or shortcode.
- Tweak: Adjusted price and rental search ranges.
- Tweak: Translation fix for rent period.
- Tweak: Numerous changes to CSS to improve listing display and responsiveness.
- Tweak: Settings checkbox options display correctly.
- Tweak: Improvements to author box functions for multi-author.
- Tweak: LinkedIn author link adjusted.
- Fix: Conditional tags when lo listing types are activated.
- Fix: Improved onclick links in external, web links to conform with new JS class.
- Fix: Commercial car spaces displaying incorrectly.
- Fix: Conditional tags improved.

= 3.0.4 May 4, 2016 =

- Fix: Internal help videos gzip error, using iframe instead.
- Fix: Corrected incorrect stray tags on internal welcome page.

= 3.0.3 May 2, 2016 =

- New: Setting to disable Google Maps API if already added by theme or other plugin.
- New: Ability to set a Google Maps API Key.
- Fix: Renamed misspelled Property on linked contact.
- Fix: Trailing ul tag on search widget.
- Fix: Implemented better timezone support for open for inspection. Requires WordPress 3.9.
- Tweak: Tighter spacing on dropdown contact list.
- Tweak: Updated translations file.
- Tweak: Capital c for contact post type.
- Tweak: Dashboard activity widget improved CSS display.
- Tweak: Dashboard activity comments better labeled.
- Tweak: Internal links to documentation corrected.

= 3.0.2 April 10, 2016 =

- Fix: Featured Listing removed redundant no option.

= 3.0.1 April 8, 2016 =

- Tweak: Versioning to all CSS and JS.
- New: Arabic translation.
- Tweak: Updated German Translation.
- Tweak: Updated French Translation.
- Tweak: Updated Dutch Translation.
- Fix: Search by Address and Property ID correctly searches the listing Title. In order to search by property ID, add the property ID to the listing title.
- New: Customise the EPL - Contact Form Widget Submit Label.
- Tweak: Added Form styling to contact form.
- Tweak: Corrected additional translation strings with contact form labels.
- Tweak: Corrected spacing in extension plugin updates.
- Tweak: Renamed EPL - Contact Form Subscribe label to Submit.

= 3.0 March 30, 2016 =

- Tweak: Textdomain and languages files renamed. Changed from epl to easy-property-listings for the WordPress.org translation initiative.
- New: Every epl_action present in the $\_GET or $\_POST is called using WordPress do_action function in init.
- Tweak: Radio options when adding listings converted to checkboxes to slim down the admin pages.
- Fix: Ducted Heating additional features now displays in feature list.
- Fix: Fully fenced option now displays in feature list.
- Tweak: Optimise Admin Listing queries.
- Tweak: Removed double display of Under Offer in admin listing list.
- Tweak: Leased rental listings now display the weekly rent amount in admin.
- Tweak: Commercial Lease listing details improved in admin list.
- Tweak: Sold price displays in admin.
- Fix: Date Available fix for year.
- New: epl_get_property_available filter allows customising date format.
- Tweak: External links function improved.
- Tweak: Added additional plugin file security access to prevent file reading outside of WordPress.
- Fix: Number Formatting function PHP warning fixed.
- Fix: is_epl_post function to prevent error when no posts are activated.
- Tweak: Commercial auction listing support.
- New: Contacts and form system for managing listing leads and history of contact.
- New: contact_capture shortcode // Needs Author id of page and URL.
- New: Contact System for Lead Generation and Capture.
- New: Form API supports editor.
- New: Dashboard Widget Listing and Contact Activity Feed.
- New: Date Picker updated JS for improved usage and improved compatibility with themes and plugins.
- Tweak: Code Docblocks created for http://docs.easypropertylistings.com.au code reference.
- New: Link a contact with a listing and display details and quick access to contact.
- New: Error tracking and debug logging helper functions.
- New: Form API supports sections breaks.
- New: Contextual help tab added to Add/Edit Listing page.
- New: Inspection date format now customisable from settings.
- Tweak: Extension license updater updated.
- Tweak: Added additional map CSS classes to improve Google Map output with some themes.
- New: Adjustable Map pin when editing a listing and setting coordinates. Drag the map pin to adujst the position.
- Tweak: Imported values of 0 no longer display on commercial listings.
- Tweak: epl_render_html_fields allows for css class set in the field array of meta-boxes.
- Tweak: Commercial authority default type is now For Sale instead of Auction.
- Tweak: Converted Radio options to tick boxes to reduce space.
- Tweak: Commercial auction listing support.
- Tweak: Bedrooms allow studio option.
- Tweak: Applied thousands separator to land sizes using settings.
- Tweak: Allow for .00 and .0 when adding listing prices.
- Tweak: Toilet supports decimal.
- Tweak: Additional Features increased to three columns to minimise space with single checkboxes.
- Tweak: Listing price, sale, and rental price now supports decimal values when saving.
- Tweak: Bond supports decimal figures.
- Tweak: Translation strings fixed.
- Tweak: m2 html character added.
- Tweak: Listings with prices set to 0 like bond no longer display in admin.
- Fix: Rental listing when using price text the rental period no longer displays in admin.
- Tweak: Pagination loading globally for use in admin.
- New: Pagination enhanced to enable adjustment of output.
- Fix: Old function in metaboxes removed as it inadvertently caused additional unnecessary queries.
- New: Generate visual reports on your listing KPI status so you can track your listings and sales.
- Tweak: [listing_search] shortcode using new API and allows for custom templates. Place the template in themes/your_theme/easypropertylistings/templates/ folder.
- Tweak: Enhanced Search Object thanks to codewp allows widget template override.
- New: Search Widget and [listing_search] shortcode allows for property status option.
- New: Search template now editable using epl_get_template_part.
- New: Search widget and [listing_search] shortcode order option added to allow adjusting of field order.
- New: Second agent field allows for searching users.
- New: Search upgraded to object thanks to codewp.
- New: Search for second listing author on listings.
- New: Search widget and [listing_search] shortcode status search option added.
- New: Search widget and [listing_search] shortcode support any registered post types.
- New: Search widget and [listing_search] shortcode support single drop down selection for price, land, building.
- Fix: Session start less likely to cause issues with certain server configurations.
- Fix: listing_open shortcode no longer displays sold or leased listings.
- New: Additional customisation of shortcode-listing.php template part.
- Tweak: Listing Shortcode adjusted for better processing of options.
- New: [listing_auction] shortcode.
- New: Contact shortcode. [epl_contact_form]
- New: Contact Form Widget.
- New: Sort by location A-Z added to front end listing filter.
- Tweak: iThemes Builder archive-listing.php and single-listing.php templates updated to improve render_content theme function.
- New: Allow extensions to use core templates for output.
- Fix: Added translation string for P.A. label.
- Fix: Translation of land size unit.
- Tweak: LinkedIn will use full URL or fallback.
- New: Default embedded video width adjustable from settings.
- New: Video links now support additional formats like Vimeo using the WordPress wp_oembed.
- New: Listing widget now loadable using epl_get_template_part thanks to codewp.
- Tweak: Widget descriptions added to widget management.
- Fix: Stray ul tag with search widget tabbing.
- Tweak: Improved get_additional_features_html function for additional features and added epl_get_additional_features_html filter
- New: Contact tags taxonomy added for creating your own contact tags.
- Tweak: Listing heading function enhanced for other post types.
- Tweak: Building value now accepts decimal.
- New: Support for Twenty Sixteen theme.
- Tweak: Active theme function enhanced for older WordPress versions.
- New: Templates added for Twenty Fourteen Theme to improve display.
- New: Archive title action added for easier implementation and filters to adjust output.
- New: epl_feedsync_format_strip_currency function to strip currency during import with epl_feedsync_format_strip_currency_symbol filter to modify string replacement search.
- New: epl_archive_title_search_result Filter, default “Search Result”.
- New: epl_archive_title_fallback Filter, default “Listing”.
- New: epl_archive_title_default Filter.
- New: epl_get_active_theme Filter.
- New: epl_active_theme Filter.
- New: epl_active_theme_name Filter.
- New: epl_active_theme_prefix Filter.
- New: epl_archive_title_fallback Filer.
- Tweak: epl_strip_tags function added filter to adjust HTML tag stripping.
- New: epl_contact_form_description_allowed_tags Filter.
- New: epl_get_property_feature_taxonomy filter allowing adjustment of listing features.
- New: epl_get_property_auction filter allows adjustment of auction date format.
- New: epl_get_property_auction_label filter to adjust the Auction label.
- New: epl_get_property_price_display Filter.
- New: epl_get_property_price_sold_display Filter.
- New: epl_get_property_price_sold_date Filter.
- New: epl_get_property_rent Filter.
- New: epl_get_property_bond Filter.
- New: epl_get_property_land_category Filter.
- New: epl_commercial_auction_label Filter.
- New: epl_get_property_auction_date Filter.
- New: epl_get_price_plain_value Filter.
- New: epl_get_price Filter.
- New: epl_get_price_sticker Filter.
- New: epl_get_price_in_list Filter.
- New: epl_get_property_commercial_category Filter.
- New: epl_get_property_year_built_label Filter.
- New: epl_get_property_year_built Filter.
- New: epl_get_property_bath_label Filter.
- New: epl_get_property_bathrooms_label Filter.
- New: epl_get_property_bath Filter.
- New: epl_get_property_bed_label Filter.
- New: epl_get_property_bedrooms_label Filter.
- New: epl_get_property_bed Filter.
- New: epl_get_property_rooms_label Filter.
- New: epl_get_property_rooms Filter.
- New: epl_get_parking_spaces_label Filter.
- New: epl_get_property_parking Filter.
- New: epl_get_property_garage_label Filter.
- New: epl_get_property_garage Filter.
- New: epl_get_property_carport_label Filter.
- New: epl_get_property_carport Filter.
- New: epl_get_property_air_conditioning_label Filter.
- New: epl_get_property_air_conditioning Filter.
- New: epl_get_property_pool_label Filter.
- New: epl_get_property_pool Filter.
- New: epl_get_property_security_system_label Filter.
- New: epl_get_property_security_system Filter.
- New: epl_get_property_land_area_label Filter.
- New: epl_get_property_land_value Filter.
- New: epl_get_property_building_area_label Filter.
- New: epl_get_property_building_area_value Filter.
- New: epl_get_property_new_construction_label Filter.
- New: epl_get_property_new_construction Filter.
- New: epl_get_property_com_car_spaces_label Filter.
- New: Dynamic additional features epl*get*{meta_key}\_label Filter.
- New: epl_get_additional_features_html Filter.
- New: epl_get_additional_rural_features_html Filter.
- New: epl_get_additional_commerical_features_html Filter.
- New: epl_get_features_from_taxonomy Filter.
- New: epl_checkbox_single_check_options Filter.
- New: epl_property_sub_title_plus_outgoings_label Filter.
- New: epl_property_sub_title_available_from_label Filter.
- New: epl_property_sub_title_available_now_label Filer.
- New: epl_get_formatted_property_address filter.
- New: epl_get_property_category filter.
- New: epl_get_property_tax.
- New: epl_property_sub_title_property_features filter for Property Features label.
- New: epl_property_sub_title_plus_outgoings filter for Plus Outgoings label.
- New: epl_property_sub_title_commercial_features filter for Commercial Features label.
- New: epl_property_sub_title_rural_features filter for Rural Features label.
- New: epl_switch_views_sorting_title_sort filter for Sort label.
- New: epl_switch_views_sorting_title_list filter for List label.
- New: epl_switch_views_sorting_title_grid filter for Grid label.
- New: epl_pagination_before_page_numbers filter.
- New: epl_pagination_after_page_numbers filter.
- New: epl_pagination_single_content_text Filter
- New: epl_pagination_single_tag Filter.
- New: epl_pagination_single Filter.
- New: epl_pagination_single_dot_tag Filter.
- New: epl_pagination_single_dot_content Filter.
- New: epl_pagination_single_dot_attributes Filter.
- New: epl_pagination_single_dot Filter.

= 2.3.1 October 5, 2015 =

- New: Added a hidden field property_images_mod_date for image mod time in preparation for importer plugin.
- Tweak: Added categories to search for business, rural, land, commercial, commercial_land post types.
- Tweak: Adjusted z-index of sticker label.
- Tweak: Hide address separator when address is empty.
- Fix: Search price fix for commercial, commercial_land, and business.
- Fix: POA label now obeys custom label setting.

= 2.3 September 17, 2015 =

- New: Custom Post Type API. Makes it easy to create and register new custom post types.
- New: Custom Meta Box API. Creating custom fields and being able to configure custom meta fields on existing and new post types.
- New: Custom Forms API. Will give the ability to create forms and submissions for the coming CRM. (Customer Relationship Manager).
- New: Ordering of extension dynamic custom fields now possible.
- New: Archive template attributes class dynamically added depending on template in use.
- New: A number of helper functions have been added to better integrate additional custom post types.
- New: Button meta field for use in extensions and custom fields.
- New: Adjustments to video output function.
- New: Features taxonomy now use archive template instead of blog post view.
- New: Filters to adjust the Search not found text epl_property_search_not_found_title and epl_property_search_not_found_message.
- Tweak: Restored get_property_suburb function which was used in Listing Templates.
- Tweak: Better author linking and real estate agent user output.
- Tweak: Improvements for other extensions to hook into and use maps.
- Tweak: Template fallback functions for improved custom template usage.
- Tweak: Swedish translations updated.
- Tweak: Translation file updated.
- Fix: New Construction class corrected to new_construction instead of pool.
- Fix: Fix: Property ID searching improved. If you have a-z characters in your id include them in the title. E.g. aaa222 - 9 Somewhere Street, Brooklyn NY.

= 2.2.7 September 9, 2015 =

- Tweak: Compatibility for Listing Templates extension.

= 2.2.6 August 22, 2015 =

- Fix: Updated extension licensing updater to use https. Update required in order to be able to auto-update your extensions as Easy Property Listings has moved to https.

= 2.2.5 August 20, 2015 =

- Fix: Widget construct fixes for WordPress 4.3.
- Tweak: Un-install function.
- Tweak: Plugin page link to settings.
- Tweak: Languages updated.

= 2.2.4 August 05, 2015 =

- Tweak: Improvements to Commercial/Commercial Land/Business pricing when set to Lease type to display free form price text.
- Tweak: Bar graph in dashboard will no longer cover address if set to low.
- Tweak: Added sticker CSS styling for single listing.
- Fix: Search Widget/Shortcode display house category value instead of key.
- Fix: Search Widget/Shortcode Property ID correctly searches numeric listing ID.
- Fix: Search Widget/Shortcode excluded non searchable fields from land, commercial, commercial land and business post types.

= 2.2.3 July 27, 2015 =

- Tweak: Adjusted new sorter function to work on lower than PHP version 5.3.
- Tweak: Moved old template functions to theme compatibility, will be removed in future version.
- Tweak: Set sorter list style to none to prevent some themes from displaying a list bullet.

= 2.2.2 July 25, 2015 =

- Tweak: CSS tweak for image size to retain proportion on certain themes.
- Tweak: Adjusted position of show/hide suburb on Commercial/Business listing types.
- Fix: Archive image correctly loading 300x200 image.
- Fix: Listing address display settings fixed.

= 2.2.1 July 24, 2015 =

- Tweak: Set padding for search tabs for better display on some themes.
- Fix: Search function fix checking for empty option when using custom filters.

= 2.2 July 24, 2015 =

- New: Search shortcode and widget rebuilt to enable adding additional fields through filters and hooks.
- New: Search shortcode and widget added additional search fields for City, State, Postcode and Country.
- New: Search shortcode and widget allows for optional multi select of house category.
- New: Search shortcode and widget improved responsive CSS.
- New: Grid styles included in main CSS for use in extensions.
- New: Upload button added for use in custom plug-ins and extensions to upload files.
- New: Filter to adjust tour labels.
- New: Filters to adjust Floor Plan labels.
- New: Filters to adjust External Link labels.
- New: Sold prices now display when set on front end and manage listings pages.
- New: Label function for returning meta labels.
- New: Ads on settings no longer display when there is an activated extension present.
- New: Locked and help cases options for use in extensions and custom plugins.
- New: Theme compatibility mode which enables all themes to display correctly with options to disable featured images for themes that automatically add featured images.
- New: City setting to allow addresses in countries that need more than a suburb Label is customisable from settings.
- New: Country setting to allow the country to display with the listing address.
- New: Able to adjust or add more registered thumbnail sizes through a filter.
- New: Function to get all the values associated with a specific post meta key.
- New: Replaced the_post_thumbnail on archive pages and shortcodes with a customisable hook allowing for additional customisation with themes.
- New: Specific templates for theme compatibility mode for archive and single listings.
- New: Template loading system allowing for additional templates to be added to shortcodes and widgets from themes, custom plug-ins and extensions. This allows you to create an unlimited number of templates and load them from your theme.
- New: Sorter allows for sorting by current/sold leased.
- New: Ability to add additional sorter via filter.
- New: Post counter function for use in extensions and custom plug-ins.
- New: User fields re-built which allows for adding on new fields through filter.
- New: Help meta type allowing for better internal documentation in extensions.
- New: City meta field added to all listing types when enabled.
- New: Rental display or hide rental price.
- New: Check-box single field type.
- New: Actions added to enable extensions to better hook into listings types and optimised functions for admin column details.
- New: Dashboard widget now displays other extensions content counts.
- New: Listing widget now allows for additional selectable templates to be added through custom plug-ins, hooks and themes.
- New: Replaced widget image with a dynamic action.
- New: Filter added for Gravatar image.
- New: Replaced widget and author box image functions with actions.
- New: Uninstall function to remove all Easy Property Listings content.
- New: Get option function.
- New: When saving settings on extensions sub tabs you are no longer taken to the first tab.
- New: Customisable state label.
- Tweak: Improved under offer, sold and leased labels.
- Tweak: Improved install function to reduce code and allow for new settings to be added.
- Tweak: Removed redundant code and streamlined templates.
- Tweak: Improved reset query function.
- Tweak: Removed old functions improving plugin code.
- Tweak: Rebuilt address function to allow for city and country.
- Tweak: Improved sorter function in all shortcodes.
- Tweak: Improvements to Commercial and Business listing types to better comply with REAXML format with business takings, franchise, terms and commercial outgoings.
- Tweak: Reorganised settings page.
- Tweak: Translations updated and additional tags added.
- Tweak: Search button default label changed from "Find Me A Property!" to "Search".
- Tweak: Applied custom suburb label to EPL - Listing Widget.
- Fix: Listings house categories correctly display labels instead of values.
- Fix: Listings with carport, garage or values set to zero no longer display.
- Fix: Shortcode compatibility for WordPress 3.3 thanks to codewp.
- Fix: Saving listing when in debug mode and ticking hide map or hide author box.
- Fix: New Zealand currency now displays a dollar sign.

= 2.1.11 June 5, 2015 =

- Tweak: Removed sub titles "Property Manager" and "Real Estate Agent" from the single listing template for better language support and to facilitate the hiding of the author box.
- Tweak: Added epl- prefix to all author-box and widget css.
- Tweak: Renamed author-box container with epl-author-box-container as it was harder to target the author box content and adjusted JS for tabs.
- Tweak: Improved author box responsive CSS.
- Tweak: Updated extension updater for multisite and other improvements.
- Tweak: Leased label when adding a property will use custom label.
- Tweak: Wrapper class for property category.
- Fix: Undefined status if importing listings not using current status.
- Fix: When user selects grid/list option and pages the user selected view is retained.
- Fix: [listing post_type="rental"] shortcode price sorting for rental.
- New: Author box is now able to be hidden on a per listing basis.
- New: Added filters for author box social links.
- New: Inspection filter to adjust the inspection date/time format.
- New: Several author widget filters added to enable additional content through extensions or custom functions.
- New: Sold, leased, under offer label filter which uses the label setting and label changes dashboard widget, admin category filters and search widget.
- New: Sold label making Sold STC possible or other Sold label variant.
- New: Danish language thanks to pascal.
- New: German language thanks to ChriKn.
- New: Ukrainian language thanks to Alex.
- New: Swedish language thanks to Roland J.

= 2.1.10 May 31, 2015 =

- New: Email field validation added.
- New: Added status classes to widgets for better targeting of CSS styles.
- Tweak: Improved video embed and added a filter to adjust video container size.
- Tweak: Improved CSS wrappers for listing widget and added dynamic class depending on widget display style.
- Tweak: Added additional classes to Listing Widget list variant style list items.
- Fix: Additional paging issues fixed in listing widget for other options.
- Fix: Widget leased selection displays rentals correctly.

= 2.1.9 May 27, 2015 =

- Fix: Fixed paging issues in listing widget.
- Fix: Fix shortcodes when using multiple listing post types.

= 2.1.8 May 16, 2015 =

- New: Ability to disable all plugin CSS from Advanced Settings section.
- New: Search widget and shortcode now have the option to turn of Location search.
- New: Search widget and shortcode now have filters to control the display of "Any". Each field has a unique filter which will allow you to hide the label using CSS and for example change the Location "Any" label to "Location" this will allow you to create super slim search boxes.
- New: Added translation Belgian (Dutch) thanks to pascal.beyens
- New: Polish translation thanks to Weronika.urbanczyk
- New: Two mew shortcode templates table and table_open usable with shortcodes to provide a slim list of listings. Example usage is [listing_open template="table"] or [listing template="table_open"]. You can copy these new templates into your theme/easypropertylistings folder to further customize.
- New: Added currency support for Qatar Riyal (QAR), United Arab Emirates (AED), Ukrainian Hryvnia (UAH), Vietnamese đồng (VND)
- New: checkbox_single ability for plugin and extensions.
- New: Ability to disable map on each listing.
- Tweak: Updated currency symbols for: Israeli Shekel, Thai Baht, Indian Rupee, Turkish Lira, Iranian Rial.
- Tweak: Improved CSS and added additional classes with epl- prefix in templates and search.
- Tweak: Improved CSS for Location Profiles and Staff Directory extensions.
- Tweak: Added filters for commercial titles to allow you to change "For Lease" and "For Sale" using epl_commercial_for_lease_label, and epl_commercial_for_sale_label filters.
- Tweak: Additional CSS classes for Land, Commercial and Rural special features.
- Tweak: Gallery CSS classes added.
- Tweak: Improved table shortcodes CSS and styling for better full display and responsive widths.
- Fix: New/Open Sticker now appear on listings with the price display set to no.
- Fix: Translations work correctly for categories.

= 2.1.7 May 6, 2015 =

- New: listing_search shortcode now has style option for adjusting the width. You can add style="slim" or style="wide" to the shortcode to adjust the appearance.
- New: Listing Search widget now has style options for adjusting the width.
- Tweak: Updated translation epl.pot and added missing sqm translation element.
- Tweak: Allowed for hundredths decimal in bathrooms field.
- Tweak: Floor plan button CSS.
- Tweak: Address and price responsive CSS.
- Fix: Auction listing price set to no displays auction date correctly.
- Fix: Fix: Author position css class.

= 2.1.6 May 1, 2015 =

- Fix: Fancy pagination paging works correctly when shortcodes used on home page.
- Fix: Wrapped new pagination feature in esc_url to prevent vulnerability.
- Fix: Corrected sorting by price when using shortcodes. Note: Rental sorting works on post_type="rental" in all shortcodes.
- Tweak: Added rental rate view for text entry of rental rates for REAXML compatibility.
- Tweak: Corrected admin display columns and edit listing pages for better display on mobile devices.

= 2.1.5 April 25, 2015 =

- Tweak: Commercial listing: Ability to set commercial lease rate to a decimal value using the epl_price_number_format_commercial_lease filter.
- Tweak: Updated epl.pot translation file.
- Tweak: Removed horizontal line elements in the help section to match WordPress 4.2 admin page styles.
- Tweak: Rental Listing: Added epl_property_bond_position filter to adjust the position of the Bond/Deposit to appear either before or after the value.
- Tweak: Rental Listing: Removed CSS padding before bond value.
- Fix: Rental Listing: Adjusting the Bond/Deposit label will now show your custom label in the Rental Price box.
- Fix: Rural Listing: Undefined label_leased variable.
- Note: Confirmed Easy Property Listings is not vulnerable to recent WordPress exploit.
- New: Added setting to show/hide Listing Unique ID column when managing listings.

= 2.1.4 April 22, 2015 =

- Tweak: Pagination optimised and no longer loads in admin.
- Tweak: New filter epl_price_number_format added for decimal rental rates.
- Tweak: Customise bond label from settings.
- Tweak: Added filter epl_floorplan_button_label_filter to adjust Floor Plan button label.

= 2.1.3 April 17, 2015 =

- Fix: Author box upgraded to allow for custom tabs.
- Fix: Author box upgraded to allow for better staff directory integration with author box and widget.
- Fix: Added CSS class for author archive pages.
- Fix: Improved CSS classes for author box with better responsive support.
- Fix: Added additional filters for author contact information.
- Fix: Added secondary global author function for simpler integration for extensions like the Staff Directory.
- Fix: Changes to author templates and restored author position variable.
- Fix: Further improved max and min graph values when in listing admin.

= 2.1.2 April 11, 2015 =

- Fix: Improved Responsive CSS for grid style.
- Fix: Twenty Fifteen, Twenty Fourteen, Twenty Thirteen, Twenty Twelve CSS styles for better display.
- New: Added CSS class theme name output to archive and single templates.

= 2.1.1 April 10, 2015 =

- Fix: Max price defaults set for graph calculations when upgrading from pre 2.0 version.

= 2.1 April 9, 2015 =

- New: Fancy pagination option which can be enabled in settings.
- New: Coordinates now added to listing if not set prior.
- New: Ability to select larger listing image sizes in admin.
- New: Added date picker for available date on rental listing.
- New: Added date picker for sold date.
- New: New function that combines all meta box options into one global function for admin pages.
- New: Display second agent name in admin listing lists.
- New: Additional admin option to filter by agent/author.
- New: Shortcode [listing_location] to display listings by specific location.
- New: The following shortcodes can now be filtered by location taxonomy: [listing location="perth"], [listing_open location="sydney"], [listing_category location="melbourne"], [listing_category location="brisbane"], [listing_feature feature="terrace" location="new-york"]
- New: The following shortcodes can now be sorted by price, date and ordered by ASC and DESC [listing sortby="price" sort_order="ASC"].
- New: Sorter added to shortcodes which can be enabled by adding tools_top="on" to your shortcode options.
- New: Template added in table format for use in shortcodes template="table".
- New: Function to get all active post types.
- New: Ability to register additional custom post types.
- New: Extensions now have additional help text ability.
- New: All menus now use global function to render fields.
- New: Improved template output and added additional CSS wrappers for some theme and HTML5 themes.
- New: Commercial rental lease duration now selectable.
- New: Rooms field added to set the number of rooms that the listing has.
- New: Date listed field added to all listing types.
- New: Year built field added to property, rental, rural listing types.
- New: Media upload function for use in extensions.
- New: Ability to customise Under Offer and Leased labels in settings.
- New: Lease type label loaded from drop-down select. So you can have NNN, P.A., Full Service, Gross Lease Rates, on commercial listing types. Also has a filter to enable customisation of the options.
- New: Disable links in the feature list.
- Fix: Text domain fixes on template files.
- Fix: Finnish translation file renamed.
- Fix: FeedSync date processor strptime function corrected.
- Fix: Bug in parking search field. Was only searching carports and not garages. Now searches both.
- Fix: New label now appears on listings not just with an inspection time saved.
- Tweak: Optimised loading of admin scripts and styles to pages where required.
- Tweak: Added version to CSS and JS so new versions are automatically used when plugin is updated.
- Tweak: Tidy up of admin CSS.
- Tweak: Video in author box now responsive.
- Tweak: Increased characters possible in address block fields from 40 to 80 characters and heading block to 200.
- Tweak: Coordinates now correctly being used to generate map.
- Tweak: Inspection times improved style in admin.
- Tweak: Commercial rental rate now accepts decimal numbers.
- Tweak: Improved google map output.
- Tweak: Improved default settings on upgrade, install and multisite.
- Tweak: Scripts improve site speed.
- Tweak: Dashboard widget improved query.
- Tweak: Front end CSS tweaks for better responsiveness.

= 2.0.4: February 12, 2015 =

- Fix: Bulgarian Translation (Thanks to Slavcho Aangeliev)
- Tweak: Finnish translation updated

= 2.0.3: February 9, 2015 =

- Fix: Manually entered inspection time corrected from pM to PM
- New: French translation (Thanks to Thomas Grimaud)
- New: Finnish translation (Thanks to Turo)

= 2.0.2: February 2, 2015 =

- Fix: Added fall-back diff() function which is not present in PHP 5.2 or earlier used with the New label.
- Fix: Some Labels in settings were not saving correctly particularly the search widget labels.
- Fix: Restored missing author profile contact form tab on author box.
- Tweak: Added CSS version to admin CSS and front end CSS.

= 2.0.1: January 29, 2015 =

- Fix: Attempted Twenty 15 CSS Fix but causes issues with other themes. Manual fix: Copy CSS from style-front.css to correct, margins and grid/sorter.
- Fix: Restored Display of Inspection Label for properties with scheduled inspection times.
- Fix: Search Widget security fix and performance improvements.

= 2.0: January 27, 2015 =

- New: Extension validator.
- New: Depreciated listing-meta.php into compatibility folder.
- New: Depreciated author-meta.php into compatibility folder.
- New: Global variables: $property, $epl_author and $epl_settings.
- New: Added filters for fields and groups in /lib/meta-boxes.php
- New: Property custom meta re-written into class. This was the big change to 2.0 where we completely re-wrote the output of the meta values which are now accessible using global $property variable and easy template actions.
- New: Property meta can now can be output using new actions for easy and quick custom template creation.
- New: Reconstructed templates for single, archive & author pages
- Tweak: Removed unused price script
- Fix: Fixed warning related to static instance in strict standard modes
- New: API for extensions now support WordPress editor with validation.
- New: jQuery date time picker formatting added to improve support for auction and sold listing, support for 30+ languages support.
- New: Inspection time auto-formats REAXML date eg "13-Dec-2014 11:00am to 11:45am" and will no longer show past inspection times.
- New: Inspection time support multiple dates written one per line.
- Tweak: CSS improved with better commenting and size reduction.
- New: Dashboard widget now lists all listing status so at a glance you can see your property stock.
- New: Display: To enable grid, list and sorter your custom archive-listing.php template requires the new action hook 'epl_template_before_property_loop' before the WordPress loop.
- New: Display: Utility hook action hook added 'epl_template_after_property_loop' for future updates.
- New: Display: List and grid view with optional masonry effect.
- New: Display: Sorter added for price high/low and date newest/oldest.
- New: Auction Date formats nicely. EG "Auction Saturday 28th December at 2:00pm".
- New: Tabbed extensions page support in admin for advanced extensions like "Listing Alerts".
- New: Multiple author support in Author Box.
- New: Search Widget - Supports multiple listing types, hold Ctrl to enable tabbed front end display.
- New: Search Widget - Labels are configurable from the Display settings allowing you to set for example: "Property" to "Buy" and "Rental" to "Rent" and use a single widget to search multiple types.
- New: Search Widget and shortcode supports search by property ID, post Title, Land Area and Building Area.
- New: Search Widget - removed extra fields from land, added labels for each property type to be shown as tab heading in search widget
- Fix: Search Widget - Optimized total queries due to search widget from 1500 + to ~40
- New: Author variables accessible using new CLASS.
- New: Search short code supports array of property types.
- New: REAXML date format function to format date correctly when using WP All Import Pro. Usage [epl_feedsync_format_date({./@modTime})].
- New: REAXML Unit and lot formatting function for usage in the title when using WP All Import Pro. Usage [epl_feedsync_filter_sub_number({address[1]/subNumber[1]})].
- New: Global $epl_settings settings variable adds new default values on plugin update.
- New: Display: Added customisable label for rental Bond/Deposit.
- New: Template functions completely re-written and can now be output using actions.
- New: Added NEW sticker with customisable label and ability to set how long a listing displays the new label.
- Tweak: Compatibility fixes
- New: Bar Graph API added.
- New: Graph in admin allows you to set the max bar graph value. Default are (2,000,000 sale) and (2,000 rental).
- New: Graph visually displays price and status.
- New: Price graph now appears in admin pages quickly highlighting price and status visually.
- New: Meta Fields: Support for unit number, lot number (land).
- New: South African ZAR currency support.
- Fix: Corrected Commercial Features ID Spelling
- Tweak: YouTube video src to id function is replaced with better method which handles multiple YouTube video formats including shortened & embedded format
- New: Adding Sold Date processing
- Tweak: Updated shortcode templates
- Tweak: Global $epl_author.
- Tweak: Fixed content/ into EPL_PATH_TEMPLATES_CONTENT
- New: Support for older extensions added
- New: Extension offers in menus general tab
- Tweak: Renamed user profile options section to "Easy Property Listings: Author Box Profile".
- Tweak: Added better Bond/Deposit for rentals labels.
- Fix: Deprecated author-meta.php in compatibility folder, class-author-meta.php has been created which will be used in place of author-meta.php & its variables in all author templates
- New: Added template functions for author meta class, modified templates lib/templates/content/content-author-box-simple-card.php lib/templates/content/content-author-box-simple-grav.php lib/templates/content/content-author-box.php to use the template functions based on author meta class instead of variables from author-meta.php
- New: author-meta.php depreciated and moved to compatibility directory. Variables globally available using $epl_author variable.
- Tweak: listing-meta.php depreciated and moved to compatibility directory. Variables globally available with $property variable.
- Tweak: Added "Listing not Found" to default templates when search performed with no results.
- Tweak: Improved Google maps address output for addresses containing # and /.
- Fix: Listing Pages now have better responsive support for small screen devices like iPhone.
- Fix: Default templates for Genesis and TwentyTwelve now show "Listing Not Found" when a search result returns empty.
- Fix: Purged translations in epl.pot file.
- Fix: Search Widget and short code drastically reduces database queries.
- New: Templates are now able to be saved in active theme folder /easypropertylistings and edited. Plugin will use these first and fall back to plugin if not located in theme folder.
- Fix: Extensions Notification and checker updated
- New: updated author templates to use new author meta class
- Fix: Added prefix to CSS tab-content class. Now epl-tab-content for compatibility.
- New: Update user.php
- Tweak: Improved internal documentation and updated screens.
- Tweak: Improved descriptions on author pages.
- Tweak: Better permalink flushing on activation, deactivation and install.
- Tweak: Extensive changes to admin descriptions and labels.
- Tweak: Optimising the php loading of files and scripts.
- New: Define EPL_RUNNING added for extensions to check if plugin is active.
- New: New options added to setting array when plugin is updated.
- New: Old functions and files moved to plug-in /compatibility folder to ensure old code still works.
- New: Meta Location Label.
- New: Service banners on settings page.
- New: Saving version number so when updating new settings are added.
- New: iCal functionality for REAXML formatted inspection dates. Further improvements coming for manual date entry.
- New: Extensions options pages now with tabs for easier usage.
- New: Added ID classes to admin pages and meta fields.
- New: Filters to adjust land and building sizes from number to select fields.
- Tweak: Moved old extensions options page to compatibility folder so older extensions still work as expected.
- New: Search Widget - Added filter for land min & max fields in listing search widget
- New: Search Widget - Added filter for building min & max fields in listing search widget
- Fix: For session start effecting certain themes
- New: Land sizes now allow up to 5 decimal places
- New: Search Widget - Custom submit label
- New: Search Widget - Can search by title in property ID / Address field
- New: Added Russian Translation

= 1.2.1: September 23, 2014 =

- Fix: Search Widget not working on page 2 of archive page in some instances.
- Fix: Property feature list Toilet and New Construction now display in list when ticked.
- Fix: EPL - Listing widget was not displaying featured listings.
- Fix: Allowed to filter by commercial_listing_type in [listing_category] shortcode.
- Fix: Updated templates to display Search Results when performing search.
- Fix: No longer show Bond when viewing rental list in admin.
- Fix: Open for inspection sticker now appears on rental properties.
- New: Added initial Dutch translation.

= 1.2: September 8, 2014 =

- New: Plug in Activation process flushes permalinks.
- New: Plug in deactivation flushes permalinks.
- New: Shortcode [listing_search]
- New: Shortcode [listing_feature]
- New: Shortcode [listing_open] replaces [home_open] shortcode. Retained [home_open] for backward compatibility, however adjust your site.
- New: Listing shortcodes allow for default template display if registered by adding template="slim" to the shortcode.
- New: Translation support now correctly loads text domain epl.
- New: Added translation tags to all test elements for better translation support.
- New: Updated source epl.pot translation file for translations.
- New: Added very rough Italian translation.
- New: Wrapped Featured image in action to allow for easy removal and/or replacement.
- New: Added new CSS classes to widgets for consistent usage.
- New: Added options to hide/ show various options to EPL - Listing widget: Property Headline, Excerpt, Suburb/Location Label, Street Address, Price, Read More Button.
- New: Added customisable "Read More" label to EPL - Listing widget.
- New: Added excerpt to EPL - Listing widget.
- New: Added options to remove search options from EPL - Listing Search widget.
- New: Added consistent CSS classes to shortcodes for responsive shortcode.
- New: Date processing function for use with WP All Import when importing REAXML files. Some imports set the current date instead of the date from the REAXML file. Usage in WP All Import Post Date is: [epl_feedsync_format_date({./@modTime})]
- New: Added additional CSS classes to template files.
- New: Added WordPress editor support in admin for use with extensions.
- New: Added textarea support in admin for use with extensions.
- New: Filters added for all select options on add listing pages which allows for full customisation through simple function.
- New: Added rent period, Day, Daily, Month, Monthly to rental listing types.
- New: Added property_office_id meta field.
- New: Added property_address_country meta field.
- New: Added filter epl_listing_meta_boxes which allows additional meta boxes to be added through filter.
- New: Added mini map to listing edit screen. Will display mini map in address block when pressing green coordinates button.
- Tweak: Admin CSS tweaks to define sections in admin.
- Tweak: Added additional CSS classes to admin menu pages to extensions can be better distinguished when installed and activated.
- Tweak: Added defaults to widgets to prevent errors when debug is on.
- Tweak: Allowed for decimal in bathrooms to allow for 1/2 baths eg 1.5.
- Fix: Undefined errors when debug is active.
- Fix: CSS for TwentyThirteen style CSS using .sidebar container.
- Fix: CSS for responsive shortcode.
- Fix: Registering custom template actions now works correctly.
- Fix: Changed property not found wording when using search widget and listing not found.
- Fix: Updated admin columns for commercial_land listing type to match other listing type.
- Fix: Swapped bedrooms/bathroom label on hover.

= 1.1.1: July 7, 2014 =

- New: Internationalisation support to enable customizing of post types: slug, archive, rewrite, labels, listing categories for meta_types.
- New: Created filters for listing meta select fields: property_category, property_rural_category, property_commercial_category, property_land_category
- New: Created filters for each of the seven custom post types: labels, supports, slug, archive, rewrite, seven custom post types
- New: Shortcode [listing_category] This shortcode allows for you to output a list of listings by type and filter them by any available meta key and value.
- Tweak: Updated search widget for filtered property_categories
- Fix: Listing categories were showing key, now showing value.
- Fix: Settings were not showing up after saving, second refresh required setting variable to reload.

= 1.1: June 27, 2014 =

- First official release!
