<?php
/*
 * GeoRSS formatted output for Google Maps
 * Authors: Alastair Mucklow, Chris Toppon, Merv Barrett
 */
 
//header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
//$more = 1;
// Use DOMAIN_NAME/CONTENT_TYPE/?feed=georss

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'" standalone="yes"?'.'>'; ?>
 
<feed xmlns="http://www.w3.org/2005/Atom"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
xmlns:georss="http://www.georss.org/georss"
xmlns:woe="http://where.yahooapis.com/v1/schema.rng"
xmlns:media="http://search.yahoo.com/mrss/">

	<title><?php bloginfo_rss('name'); echo ' - '; wp_title_rss(); ?></title>
	<link rel="self" href="<?php self_link(); ?>" />
	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('url') ?>"/>
	<subtitle><?php the_category_rss(); ?></subtitle>
	<updated><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></updated>
	<generator uri="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></generator>

	<?php
		while( have_posts()) : the_post();
			$post_type = get_post_type( get_the_ID() ); 
			if ('epl_suburb' == $post_type ) {
				// Suburb GeoRSS Profile
				$coords = $meta['suburb_map_location'][0];
				$suburb_title = get_the_title();
			} elseif ('epl_property' == $post_type ) {
				// Property GeoRSS Profile
				include('property-meta.php');
			} elseif ('epl_rental' == $post_type ) {
				// Rental GeoRSS Profile
				include('property-meta.php');
			} elseif ('epl_commercial' == $post_type ) {
				// Property GeoRSS Profile
				include('property-meta.php');
			}

			$coords_geo = str_replace( ',', ' ', $coords );
			if ($coords) : 	?>
				<entry>
					<title><?php echo $address_street; ?><?php echo $suburb_title; ?></title>
					<link rel="alternate" type="text/html" href="<?php the_permalink_rss() ?>"/>
					<published><?php echo mysql2date('r', get_the_time('Y-m-d H:i:s')); ?></published>
					<updated><?php echo mysql2date('r', get_the_modified_time('Y-m-d H:i:s')); ?></updated>
					<content type="html"><![CDATA[
						<div class="info-window">
							<?php the_post_thumbnail('thumbnail'); ?>
							<a href="<?php the_permalink_rss() ?>">
								<div class="sub-title"><?php echo $property_address_street; ?></div>
								<div><?php echo $property_address_suburb; ?></div>
							</a>
							<?php if ( $post_type == 'epl_rental' || $post_type == 'epl_property' || $post_type == 'epl_land' ) { ?>
								<div></div>
								<ul>
									<?php
										echo $l_price;
										echo $l_htype;
										echo $l_bed;
										echo $l_bath;
									?>
								</ul>
							<?php } elseif ( $post_type == 'epl_commercial' ) { ?>
								<div><?php echo $price; ?></div>
								<ul>
									<?php echo $l_htype; ?>
								</ul>
							<?php } ?>
						</div>
					]]></content>
					<author>
						<name><?php the_author(); ?></name>
						<uri><?php the_author_posts_link(); ?></uri>
					</author>
					<link rel="enclosure" type="image/jpeg" href="<?php //the_post_thumbnail('large'); ?>" />
					<georss:point><?php echo $coords_geo ?></georss:point>
				</entry>
				<?php
			endif;
		endwhile;
	?>
</feed>
