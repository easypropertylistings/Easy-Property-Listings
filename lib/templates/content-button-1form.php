<?php
/*
 * Template: 1Form Button
 */

function epl_button_1form() {
	$meta = get_post_custom();

	// Options
	$epl_1form_code = get_option('epl_settings_epl_1form_code');
	$epl_1form_button = get_option('epl_settings_epl_1form_button');

	//address
	$property_unique_id = $meta['property_unique_id'][0];	
	$property_address_street_number = $meta['property_address_street_number'][0];	
	$property_address_street = $meta['property_address_street'][0];
	$property_address_suburb = $meta['property_address_suburb'][0];		
	$property_address_state = $meta['property_address_state'][0];
	$property_address_postal_code = $meta['property_address_postal_code'][0];
	$property_date_available = $meta['property_date_available'][0];
	$property_rent = $meta['property_rent'][0];
	$property_bond = $meta['property_bond'][0];
	
	$keys = get_the_terms( $post->ID, 'property_type' );
	if ($keys != '') {
		global $post;
		foreach($keys as $key) {
			$property_house_type = $key->name;
		}
	}

	$property_bedrooms = $meta['property_bedrooms'][0];
	$property_bathrooms = $meta['property_bathrooms'][0];		
	$property_garage = $meta['property_garage'][0];
	$property_carport = $meta['property_carport'][0];
	$property_parking = $property_garage + $property_carport;
		
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'post-thumbnail' );
	$thumbnail_src = $thumb[0]; ?>
	
	<div class="book-inspection 1form">
		<form action="https://www.1form.com/au/tenant/application/start/" method="post" target="_blank">
			<input type="hidden" id="tagid" name="tagid" value="<?php echo $epl_1form_code; ?>">
			<input type="hidden" id="papf_realestateco" name="papf_realestateco" value="<?php bloginfo(); ?> ">
			<input type="hidden" id="papf_realestateag" name="papf_realestateag" value="<?php the_author(); ?>">
			<input type="hidden" id="papf_realestatem" name="papf_realestatem" value="<?php the_author_meta( 'email' ); ?>">
			<input type="hidden" id="papf_logo" name="papf_logo" value="company logo URL">
			<!-- Input company image source url in full including "https" ONLY -->
			<input type="hidden" id="papf_propid" name="papf_propid" value="<?php echo $property_unique_id; ?>">
			<input type="hidden" id="papf_propadd" name="papf_propadd" value="<?php echo $property_address_street_number , ' ' , $property_address_street; ?>">
			<input type="hidden" id="papf_propsub" name="papf_propsub" value="<?php echo $property_address_suburb; ?>">
			<input type="hidden" id="papf_proppc" name="papf_proppc" value="<?php echo $property_address_postal_code; ?>">
			<input type="hidden" id="papf_propstat" name="papf_propstat" value="<?php echo $property_address_state; ?>">
			<input type="hidden" id="papf_available" name="papf_available" value="<?php echo $property_date_available; ?>">
			<input type="hidden" id="papf_rent" name="papf_rent" value="<?php echo $property_rent; ?>">
			<input type="hidden" id="papf_bond" name="papf_bond" value="<?php echo $property_bond; ?>">
			<input type="hidden" id="papf_proptype" name="papf_proptype" value="<?php echo $property_house_type; ?>">
			<input type="hidden" id="papf_propnobed" name="papf_propnobed" value="<?php echo $property_bedrooms; ?>">
			<input type="hidden" id="papf_propnobath" name="papf_propnobath" value="<?php echo $property_bathrooms; ?>">
			<input type="hidden" id="papf_propnocar" name="papf_propnocar" value="<?php echo $property_parking; ?>">
			<input type="hidden" id="papf_image" name="papf_image" value="<?php echo $thumbnail_src; ?>">
			<!-- Input property image source url in full including "https" ONLY. For multiple images, delimit the URLs with | e.g. https://www.host.com/image1.png|https://www.host.com/image2.png-->
	
			<?php
				 // Remove Image Button
				if ( $epl_1form_button == 1 ) { ?>
					<input type=image src="https://b.cdn1form.com/buttons/default.png">
				<?php } else { ?>
					<input type=submit value="Apply for this property">
				<?php }
			?>
		</form>
	</div>
	<?php 
}
