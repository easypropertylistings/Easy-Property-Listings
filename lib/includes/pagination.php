<?php
/**
 * Pagination option
 *
 * @package     EPL
 * @subpackage  Functions/Pagination
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pagination function
 *
 * @since 2.1
 */
function epl_fancy_pagination( $args = array() ) {
	if ( !is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key )
			$args[ $key ] = isset( $argv[ $i ]) ? $argv[ $i ] : "";
	}

	$args = wp_parse_args( $args, array(
		'before' => '',
		'after' => '',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );
	$options = array(
		'pages_text'    => __( 'Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'easy-property-listings'  ),
		'current_text'  => '%PAGE_NUMBER%',
		'page_text'     => '%PAGE_NUMBER%',
		'first_text'    => __( '&laquo; First', 'easy-property-listings'  ),
		'last_text'     => __( 'Last &raquo;', 'easy-property-listings'  ),
		'prev_text'     => __( '&laquo;', 'easy-property-listings'  ),
		'next_text'     => __( '&raquo;', 'easy-property-listings'  ),
		'dotleft_text'  => __( '...', 'easy-property-listings'  ),
		'dotright_text' => __( '...', 'easy-property-listings'  ),
		'num_pages' => 5,
		'num_larger_page_numbers' => 3,
		'larger_page_numbers_multiple' => 10,
		'always_show' => false,
		'use_pagenavi_css' => true,
		'style' => 1,
	);
	$options = apply_filters('epl_pagination_options',$options);
	$instance = new epl_pagination_Call( $args );

	list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();

	if ( 1 == $total_pages && !$options['always_show'] )
		return;

	$pages_to_show = absint( $options['num_pages'] );
	$larger_page_to_show = absint( $options['num_larger_page_numbers'] );
	$larger_page_multiple = absint( $options['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1/2 );
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;

	if ( $start_page <= 0 )
		$start_page = 1;

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
		$end_page = $start_page + $pages_to_show_minus_1;

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page = $total_pages;
	}

	if ( $start_page < 1 )
		$start_page = 1;

	$out = '';
	switch ( intval( $options['style'] ) ) {
		// Normal
		case 1:
			// Text
			if ( !empty( $options['pages_text'] ) ) {
				$pages_text = str_replace(
					array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
					array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
				$options['pages_text'] );
				$out .= "<span class='pages'>$pages_text</span>";
			}

			$out = apply_filters( 'epl_pagination_before_page_numbers', $out, $start_page, $end_page );
			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First
				$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $options['first_text'] );
				$out .= $instance->get_single( 1, $first_text, array(
					'class' => 'first'
				), '%TOTAL_PAGES%' );
			}

			// Previous
			if ( $paged > 1 && !empty( $options['prev_text'] ) ) {
				$out .= $instance->get_single( $paged - 1, $options['prev_text'], array(
					'class' => 'previouspostslink',
					'rel'	=> 'prev'
				) );
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				if ( ! empty( $options['dotleft_text'] ) ) {
					$out .= $instance->get_single_dot( 'span', $options['dotleft_text'], array( 'class' => 'extend' ) );
				}
			}

			// Smaller pages
			$larger_pages_array = array();
			if ( $larger_page_multiple )
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
					$larger_pages_array[] = $i;

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
					$out .= $instance->get_single( $larger_page, $options['page_text'], array(
						'class' => 'smaller page',
					) );
					$larger_page_start++;
				}
			}

			if ( $larger_page_start ) {
				$out .= $instance->get_single_dot( 'span', $options['dotleft_text'], array( 'class' => 'extend' ) );
			}

			// Page numbers
			$timeline = 'smaller';
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged && ! empty( $options['current_text'] ) ) {
					$out .= $instance->get_single( $i, $options['current_text'], array( 'class' => 'current' ), '%PAGE_NUMBER%', 'span' );
					$timeline = 'larger';
				} else {
					$out .= $instance->get_single( $i, $options['page_text'], array(
						'class' => "page $timeline",
					) );
				}
			}

			// Large pages
			$larger_page_end = 0;
			$larger_page_out = '';
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
					$larger_page_out .= $instance->get_single( $larger_page, $options['page_text'], array(
						'class' => 'larger page',
					) );
					$larger_page_end++;
				}
			}

			if ( $larger_page_out ) {
				$out .= $instance->get_single_dot( 'span', $options['dotright_text'], array( 'class' => 'extend' ) );
			}
			$out .= $larger_page_out;

			if ( $end_page < $total_pages && ! empty( $options['dotright_text'] ) ) {
				$out .= $instance->get_single_dot( 'span', $options['dotright_text'], array( 'class' => 'extend' ) );
			}

			// Next
			if ( $paged < $total_pages && ! empty( $options['next_text'] ) ) {
				$out .= $instance->get_single( $paged + 1, $options['next_text'], array(
					'class' => 'nextpostslink',
					'rel'	=> 'next',
				) );
			}

			if ( $end_page < $total_pages ) {
				// Last
				$out .= $instance->get_single( $total_pages, $options['last_text'], array(
					'class' => 'last',
				), '%TOTAL_PAGES%' );
			}
			$out = apply_filters( 'epl_pagination_after_page_numbers', $out, $start_page, $end_page );
			break;

		// Dropdown
		case 2:
			$out .= '<form action="" method="get">'."\n";
			$out .= '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";

			foreach ( range( 1, $total_pages ) as $i ) {
				$page_num = $i;
				if ( $page_num == 1 )
					$page_num = 0;

				if ( $i == $paged ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'" selected="selected" class="current">'.$current_page_text."</option>\n";
				} else {
					$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['page_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'">'.$page_text."</option>\n";
				}
			}

			$out .= "</select>\n";
			$out .= "</form>\n";
			break;
	}
	$out = $before . "<div class='epl-pagination'>\n$out\n</div>" . $after;

	$out = apply_filters( 'epl_pagination_html', $out );

	if ( !$echo )
		return $out;

	echo $out;
}

/**
 * epl_pagination_Call Class
 *
 * @since 2.1
 */
class epl_pagination_Call {

	protected $args;

	function __construct( $args ) {
		$this->args = $args;
	}

	function __get( $key ) {
		return $this->args[ $key ];
	}

	function get_pagination_args() {
		global $numpages;

		$query = $this->query;

		switch( $this->type ) {
		case 'multipart':
			// Multipart page
			$posts_per_page = 1;
			$paged = max( 1, absint( get_query_var( 'page' ) ) );
			$total_pages = max( 1, $numpages );
			break;
		case 'users':
			// WP_User_Query
			$posts_per_page = $query->query_vars['number'];
			$paged = max( 1, floor( $query->query_vars['offset'] / $posts_per_page ) + 1 );
			$total_pages = max( 1, ceil( $query->total_users / $posts_per_page ) );
			break;
		default:
			// WP_Query
			$posts_per_page = intval( $query->get( 'posts_per_page' ) );
			$paged = max( 1, absint( $query->get( 'paged' ) ) );
			$total_pages = max( 1, absint( $query->max_num_pages ) );
			break;
		}

		return array( $posts_per_page, $paged, $total_pages );
	}

	function get_single( $page, $raw_text, $attr, $format = '%PAGE_NUMBER%', $tag = 'a' ) {
		if ( empty( $raw_text ) )
			return '';

		$text = str_replace( $format, number_format_i18n( $page ), $raw_text );
		$text = apply_filters( 'epl_pagination_single_content_text', $text, $raw_text );

		$attr['href'] = $this->get_url( $page );

		list( $posts_per_page, $paged, $total_pages ) = $this->get_pagination_args();
		$tag  = apply_filters( 'epl_pagination_single_tag', $tag, $page, $paged );

		return apply_filters( 'epl_pagination_single', epl_pagination_html( $tag, $attr, $text ), $page, $paged, $total_pages, $posts_per_page );
	}

	/**
	 * Outputting content of dot sigle elements.
	 *
	 * @since  2.3.1
	 * @param  string $tag        tag of single dot.
	 * @param  string $content    content of single dot.
	 * @param  array  $attributes attributes of single dot tag.
	 * @return string
	 */
	public function get_single_dot( $tag = 'span', $content = '...', array $attributes = array() ) {
		$tag        = apply_filters( 'epl_pagination_single_dot_tag', $tag );
		$content    = apply_filters( 'epl_pagination_single_dot_content', $content );
		$attributes = apply_filters( 'epl_pagination_single_dot_attributes', $attributes );

		$output = '<' . $tag;
		if ( is_array( $attributes ) && count( $attributes ) ) {
			foreach ( $attributes as $key => $value ) {
				if ( ! empty( $key ) && ! empty( $value ) ) {
					$output .= ' ' . $key . '="' . $value .'"';
				}
			}
		}
		// Tag is self closed.
		if ( in_array( $tag, array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' ) ) ) {
			$output .= ' />';
		}
		// Tag is not self closed.
		else {
			$output .= '>' . $content . '</' . $tag . '>';
		}

		return apply_filters( 'epl_pagination_single_dot', $output );
	}

	function get_url( $page ) {
		return ( 'multipart' == $this->type ) ? get_multipage_link( $page ) : get_pagenum_link( $page );
	}
}

if ( ! function_exists( 'epl_pagination_html' ) ):

	/**
	 * Pagination HTML
	 *
	 * @since 2.1
	 */
	function epl_pagination_html( $tag ) {
		static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

		$args = func_get_args();

		$tag = array_shift( $args );

		if ( is_array( $args[0] ) ) {
			$closing = $tag;
			$attributes = array_shift( $args );
			foreach ( $attributes as $key => $value ) {
				if ( false === $value )
					continue;

				if ( true === $value )
					$value = $key;

				$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		} else {
			list( $closing ) = explode( ' ', $tag, 2 );
		}

		if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
			return "<{$tag} />";
		}

		$content = implode( '', $args );

		return "<{$tag}>{$content}</{$closing}>";
	}
	endif;

if ( !function_exists( 'epl_get_multipage_link' ) ) :

	/**
	 * Pagination Multipage link
	 *
	 * @since 2.1
	 */
	function epl_get_multipage_link( $page = 1 ) {
		global $post, $wp_rewrite;

		if ( 1 == $page ) {
			$url = get_permalink();
		} else {
			if ( '' == get_option('permalink_structure') || in_array( $post->post_status, array( 'draft', 'pending') ) )
				$url = add_query_arg( 'page', $page, get_permalink() );
			elseif ( 'page' == get_option( 'show_on_front' ) && get_option('page_on_front') == $post->ID )
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $wp_rewrite->pagination_base . "/$page", 'single_paged' );
			else
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
		}

		return esc_url($url);
	}
endif;

/**
 * WordPress Default Pagination *
 * @since 2.1
 */
function epl_wp_default_pagination($query = array() ) {
	if(empty($query)) {

	?>
	<div class="epl-paginate-default-wrapper epl-clearfix">
		<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'easy-property-listings'  ) ); ?></div>
		<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'easy-property-listings'  ) ); ?></div>
	</div> <?php  } else {

		$query_open = $query['query']; ?>

	<div class="epl-paginate-default-wrapper epl-clearfix">
		<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'easy-property-listings'  ), $query_open->max_num_pages ); ?></div>
		<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'easy-property-listings'  ), $query_open->max_num_pages ); ?></div>
	</div> <?php }

}