<?php
/*
 * Template: Book Inspection Button
 */

function epl_button_book_inspection() {
	$meta = get_post_custom();
	$property_address_street_number = $meta['property_address_street_number'][0];	
	$property_address_street = $meta['property_address_street'][0];
	$property_address_suburb = $meta['property_address_suburb'][0];	
	$street = $property_address_street_number . ' ' . $property_address_street . ' ' . $property_address_suburb;
	$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail" );
		
	// Set Post Type
	if ( 'epl_rental' == get_post_type() ) {
		$inspect_type = 'rental'; 
	} else {
		$inspect_type = 'sale'; 
	}
		
	// Agent Account Name
	$agent_account = get_option('epl_settings_epl_inspect_re_account'); ?>
	
	<div class="book-inspection">
		<form action="http://www.inspectrealestate.com.au/RegisterOnline/Register.aspx?AgentAccountName=<?php echo $agent_account; ?>&address=<?php echo urlencode($street); ?>&type=<?php echo $inspect_type; ?>&imgURL=<?php echo urlencode($thumbnail_src[0]); ?>" method="post" target="_blank">
			<input type=submit value="Book Inspection">
		</form>
	</div>
	<?php
}
