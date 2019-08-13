<?php
/**
 * Pagination option
 *
 * @package     EPL
 * @subpackage  Classes/Pagination
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Pagination_Call Class
 *
 * @since 2.1
 * @since 3.4.0 Moved class to separate file.
 */
class EPL_Pagination_Call {

	/**
	 * Arguments
	 *
	 * @var array
	 * @since  2.1
	 */
	protected $args;

	/**
	 * Get things started
	 *
	 * @param array $args Array of arguments.
	 * @since  2.1
	 */
	public function __construct( $args ) {
		$this->args = $args;
	}

	/**
	 * Get the key
	 *
	 * @param array $key Key name.
	 *
	 * @return mixed
	 * @since  2.1
	 */
	public function __get( $key ) {
		return $this->args[ $key ];
	}

	/**
	 * Get Pagination arguments
	 *
	 * @since  2.1
	 */
	public function get_pagination_args() {
		global $numpages;

		$query = $this->query;

		switch ( $this->type ) {
			case 'multipart':
				// Multipart page.
				$posts_per_page = 1;
				$paged          = max( 1, absint( get_query_var( 'page' ) ) );
				$total_pages    = max( 1, $numpages );
				break;
			case 'users':
				// WP_User_Query.
				$posts_per_page = $query->query_vars['number'];
				$paged          = max( 1, floor( $query->query_vars['offset'] / $posts_per_page ) + 1 );
				$total_pages    = max( 1, ceil( $query->total_users / $posts_per_page ) );
				break;
			default:
				// WP_Query.
				$posts_per_page = intval( $query->get( 'posts_per_page' ) );
				$paged          = max( 1, absint( $query->get( 'paged' ) ) );
				$total_pages    = max( 1, absint( $query->max_num_pages ) );
				break;
		}

		return array( $posts_per_page, $paged, $total_pages );
	}

	/**
	 * Get the single pagination
	 *
	 * @param string $page page id.
	 * @param string $raw_text text content.
	 * @param array  $attr attributes of page.
	 * @param string $format format of single page.
	 * @param string $tag attributes of single page tag.
	 *
	 * @return string
	 * @since  2.1
	 */
	public function get_single( $page, $raw_text, $attr, $format = '%PAGE_NUMBER%', $tag = 'a' ) {
		if ( empty( $raw_text ) ) {
			return '';
		}

		$text = str_replace( $format, number_format_i18n( $page ), $raw_text );
		$text = apply_filters( 'epl_pagination_single_content_text', $text, $raw_text );

		$attr['href'] = $this->get_url( $page );

		list( $posts_per_page, $paged, $total_pages ) = $this->get_pagination_args();
		$tag = apply_filters( 'epl_pagination_single_tag', $tag, $page, $paged );

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
					$output .= ' ' . $key . '="' . $value . '"';
				}
			}
		}
		// Tag is self closed.
		if ( in_array( $tag, array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' ), true ) ) {
			$output .= ' />';
		} else {
			// Tag is not self closed.
			$output .= '>' . $content . '</' . $tag . '>';
		}

		return apply_filters( 'epl_pagination_single_dot', $output );
	}

	/**
	 * Get url
	 *
	 * @param string $page Page number.
	 *
	 * @return string
	 * @since  2.1
	 */
	public function get_url( $page ) {

		$link = ( 'multipart' === $this->type ) ? get_multipage_link( $page ) : get_pagenum_link( $page );

		if ( $this->query->get( 'is_epl_shortcode' ) &&
			in_array( $this->query->get( 'epl_shortcode_name' ), epl_get_shortcode_list(), true ) ) {
			$link = epl_add_or_update_params( $link, 'pagination_id', $this->query->get( 'instance_id' ) );
		}

		return $link;
	}
}
