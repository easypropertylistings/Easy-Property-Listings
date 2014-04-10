wcproperty
==========

WebConnected Property - Premium WordPress Plugin


WebConnected Property Plugin
----------------------------------------------------------------------

2.1 Merv Barrett
	Added LowGrav file to concentate address lib/fn-lowgrav-google-geocode-con.php




2.13 Merv Barrett
	Added LowGrav file to concentate address lib/fn-lowgrav-google-geocode-con.php

2.14 Merv Barrett
	Fixed Buttons to <form>
	Added 1Form Optiopns to Setting to set custom iform ID
	Tweaked Graphbox CSS


2.15 Merv Barrett
	Changed Top Section Layout of templates/content-property-single.php
	Property Meta Function created template/property-meta.php
	Dynamic Description option
	Updated Settings to Checkboxes
	Fixed Geocode Option

2.16 Merv Barrett
	Updated CSS Blog padding
	Cleand up code and added more features to property-meta.php
	Cleaned up divs in property_blog and property_single functions

2.17 Merv Barrett
	Number of gallery thumbs option wc_gallery_number
	Started Dev of Testimonial Widget

2.21 Merv Barrett
	fixed property-meta.php not the_title() but get_the_title()
	Testimonail Widget template function wc_property_testimonial_widget();
	Added serveral customisation options to testimonial and property widget
	PODS Added wc_debug option to wc_settings pod to remove or display geocode results
	PODS Removed User Options
	Added function wc_property_user
	moved all from /templates to /lib/templates
	
2.22 Merv Barrett
	Consolidation of files
	Removed old templates and merged admin-property.php and admin-rental.php to >> admin-options.php

2.23 Merv Barrett
	Added beds/bath options to widget property templates and added variable to property_meta.php => $property_icons_bb
	Author Box Function - wc_property_author()
		Widget Function - Created wc_author_widget()
	Author Fields Added
	User Photo Plugin if no Gravatar

	Testimonial Image option Alignment left right center none

2.23 Merv Barrett
	Started dev of Author Widget
	PODS added Author option to post_type 'directory'. Removed meta 'staff_mobile' , 'staff_email', 'staff_title'

TODO -----------------

	ADMIN
	Remove Geocode Field from Property/Rental

	Supermap
		- Exclude from Supermap
	

	Property/Rental
		- If no Suburb Profile/ Display Suburb...
		- Custom Titles for Archive Pages


	Layout Overide Options

	Staff Directory

	Create Widgets
		- Agent Sidebar Bio
			-Second Agent (Select from List???)
		- Contact Agent
		- DONE - Featured Property / Rental
		- DONE - Testimonial
		- Suburb
		- Search
		- Strip (columns/responsive)

	


	Set PODS Defaults
		- State selection from Admin (WA, VIC etc)
	
	Postcode Taxonomy

	Integrate Table Press into Suburbs... 
