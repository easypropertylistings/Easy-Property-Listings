<?php
if( is_admin() ) {
	return;
}

if ( !is_archive() ) {
	function epl_excerpt_length( $length ) {
		global $post;
		if ($post->post_type == 'epl_property')
			return 16;
		else if ($post->post_type == 'epl_rental')
			return 16;
		else if ($post->post_type == 'epl_commercial')
			return 16;
		else if ($post->post_type == 'epl_land')
			return 16;
		else if ($post->post_type == 'epl_suburb')
			return 39;
		else
			return 55;
	}
	add_filter('excerpt_length', 'epl_excerpt_length' , 999);	

	// Add "Read More"
	function epl_property_new_excerpt_more( $more ) {
		return '...<a href="'. get_permalink( $post->ID ) . '" class="epl-more-link">Read More&rarr;</a>';
	}
	add_filter('excerpt_more', 'epl_property_new_excerpt_more');	
}

// Youtube Video Link Filter
function epl_get_youtube_id_from_url($youtube_url) {
	$url = parse_url($youtube_url);
	if( $url['host'] !== 'youtube.com' && $url['host'] !== 'www.youtube.com'&& $url['host'] !== 'youtu.be'&& $url['host'] !== 'www.youtu.be') {
		return '';
	}

	if( $url['host'] === 'youtube.com' || $url['host'] === 'www.youtube.com' ) :
		parse_str(parse_url($youtube_url, PHP_URL_QUERY), $query_string);
		return $query_string["v"];
	endif;

	$youtube_id = substr( $url['path'], 1 );
	if( strpos( $youtube_id, '/' ) )
		$youtube_id = substr( $youtube_id, 0, strpos( $youtube_id, '/' ) );

	return $youtube_id;
}

// Append Custom Post Types to Author Page
function epl_custom_post_author_archive($query) {
	if ($query->is_author)
		$query->set( 'post_type', array('epl_property', 'epl_rental', 'epl_land' ,  'epl_testimonial' , 'epl_commercial' , 'post') );
	
	remove_action( 'pre_get_posts', 'epl_custom_post_author_archive' );
}
add_action('pre_get_posts', 'epl_custom_post_author_archive');
