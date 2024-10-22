<?php
/**
 * Pagination option
 *
 * @package     EPL
 * @subpackage  Functions/Pagination
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pagination function
 *
 * @param array $args Arguments.
 *
 * @return mixed|string|void
 * @since 2.1
 * @since 3.5 Added accessibility labels to select elements.
 */
function epl_fancy_pagination( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key ) {
			$args[ $key ] = isset( $argv[ $i ] ) ? $argv[ $i ] : '';
		}
	}

	$args = wp_parse_args(
		$args,
		array(
			'before'  => '',
			'after'   => '',
			'options' => array(),
			'query'   => $GLOBALS['wp_query'],
			'type'    => 'posts',
			'echo'    => true,
		)
	);

	foreach ( $args as $args_key => $args_value ) {
		${$args_key} = $args_value;
	}

	$options  = array(
		'pages_text'                   => __( 'Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'easy-property-listings' ),
		'current_text'                 => '%PAGE_NUMBER%',
		'page_text'                    => '%PAGE_NUMBER%',
		'first_text'                   => __( '&laquo; First', 'easy-property-listings' ),
		'last_text'                    => __( 'Last &raquo;', 'easy-property-listings' ),
		'prev_text'                    => __( '&laquo;', 'easy-property-listings' ),
		'next_text'                    => __( '&raquo;', 'easy-property-listings' ),
		'dotleft_text'                 => __( '...', 'easy-property-listings' ),
		'dotright_text'                => __( '...', 'easy-property-listings' ),
		'num_pages'                    => 5,
		'num_larger_page_numbers'      => 3,
		'larger_page_numbers_multiple' => 10,
		'always_show'                  => false,
		'use_pagenavi_css'             => true,
		'style'                        => 1,
	);
	$options  = apply_filters( 'epl_pagination_options', $options );
	$instance = new EPL_Pagination_Call( $args );

	list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();

	if ( 1 === $total_pages && ! $options['always_show'] ) {
		return;
	}

	$pages_to_show         = absint( $options['num_pages'] );
	$larger_page_to_show   = absint( $options['num_larger_page_numbers'] );
	$larger_page_multiple  = absint( $options['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start       = floor( $pages_to_show_minus_1 / 2 );
	$half_page_end         = ceil( $pages_to_show_minus_1 / 2 );
	$start_page            = $paged - $half_page_start;

	if ( $start_page <= 0 ) {
		$start_page = 1;
	}

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) !== $pages_to_show_minus_1 ) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page   = $total_pages;
	}

	if ( $start_page < 1 ) {
		$start_page = 1;
	}

	$out = '';
	switch ( intval( $options['style'] ) ) {
		// Normal.
		case 1:
			// Text.
			if ( ! empty( $options['pages_text'] ) ) {
				$pages_text = str_replace(
					array( '%CURRENT_PAGE%', '%TOTAL_PAGES%' ),
					array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
					$options['pages_text']
				);
				$out       .= "<span class='pages'>$pages_text</span>";
			}

			$out = apply_filters( 'epl_pagination_before_page_numbers', $out, $start_page, $end_page );
			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First.
				$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $options['first_text'] );
				$out       .= $instance->get_single(
					1,
					$first_text,
					array(
						'class' => 'first',
					),
					'%TOTAL_PAGES%'
				);
			}

			// Previous.
			if ( $paged > 1 && ! empty( $options['prev_text'] ) ) {
				$out .= $instance->get_single(
					$paged - 1,
					$options['prev_text'],
					array(
						'class' => 'previouspostslink',
						'rel'   => 'prev',
					)
				);
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				if ( ! empty( $options['dotleft_text'] ) ) {
					$out .= $instance->get_single_dot( 'span', $options['dotleft_text'], array( 'class' => 'extend' ) );
				}
			}

			// Smaller pages.
			$larger_pages_array = array();
			if ( $larger_page_multiple ) {
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i += $larger_page_multiple ) {
					$larger_pages_array[] = $i;
				}
			}

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ( $start_page - $half_page_start ) && $larger_page_start < $larger_page_to_show ) {
					$out .= $instance->get_single(
						$larger_page,
						$options['page_text'],
						array(
							'class' => 'smaller page',
						)
					);
					$larger_page_start++;
				}
			}

			if ( $larger_page_start ) {
				$out .= $instance->get_single_dot( 'span', $options['dotleft_text'], array( 'class' => 'extend' ) );
			}

			// Page numbers.
			$timeline = 'smaller';
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged && ! empty( $options['current_text'] ) ) { //phpcs:ignore
					$out     .= $instance->get_single( $i, $options['current_text'], array( 'class' => 'current' ), '%PAGE_NUMBER%', 'span' );
					$timeline = 'larger';
				} else {
					$out .= $instance->get_single(
						$i,
						$options['page_text'],
						array(
							'class' => "page $timeline",
						)
					);
				}
			}

			// Large pages.
			$larger_page_end = 0;
			$larger_page_out = '';
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page > ( $end_page + $half_page_end ) && $larger_page_end < $larger_page_to_show ) {
					$larger_page_out .= $instance->get_single(
						$larger_page,
						$options['page_text'],
						array(
							'class' => 'larger page',
						)
					);
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

			// Next.
			if ( $paged < $total_pages && ! empty( $options['next_text'] ) ) {
				$out .= $instance->get_single(
					$paged + 1,
					$options['next_text'],
					array(
						'class' => 'nextpostslink',
						'rel'   => 'next',
					)
				);
			}

			if ( $end_page < $total_pages ) {
				// Last.
				$out .= $instance->get_single(
					$total_pages,
					$options['last_text'],
					array(
						'class' => 'last',
					),
					'%TOTAL_PAGES%'
				);
			}
			$out = apply_filters( 'epl_pagination_after_page_numbers', $out, $start_page, $end_page );
			break;

		// Dropdown.
		case 2:
			$out .= '<form action="" method="get">' . "\n";
			$out .= '<select aria-label="' . esc_attr__( 'Pagination', 'easy-property-listings' ) . '" size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">' . "\n";

			foreach ( range( 1, $total_pages ) as $i ) {
				$page_num = $i;
				if ( 1 === $page_num ) {
					$page_num = 0;
				}

				if ( $i == $paged ) { //phpcs:ignore
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out              .= '<option value="' . esc_url( $instance->get_url( $page_num ) ) . '" selected="selected" class="current">' . $current_page_text . "</option>\n";
				} else {
					$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['page_text'] );
					$out      .= '<option value="' . esc_url( $instance->get_url( $page_num ) ) . '">' . $page_text . "</option>\n";
				}
			}

			$out .= "</select>\n";
			$out .= "</form>\n";
			break;
	}
	$out = $before . "<div class='epl-pagination'>\n$out\n</div>" . $after;

	$out = apply_filters( 'epl_pagination_html', $out );

	if ( ! $echo ) {
		return $out;
	}

	echo $out; //phpcs:ignore
}

if ( ! function_exists( 'epl_pagination_html' ) ) :

	/**
	 * Pagination HTML
	 *
	 * @param string $tag Wrapper tag.
	 * @since 2.1
	 */
	function epl_pagination_html( $tag ) {
		static $self_closing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

		$args = func_get_args();

		$tag = array_shift( $args );

		if ( is_array( $args[0] ) ) {
			$closing    = $tag;
			$attributes = array_shift( $args );
			foreach ( $attributes as $key => $value ) {
				if ( false === $value ) {
					continue;
				}

				if ( true === $value ) {
					$value = $key;
				}

				$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		} else {
			list( $closing ) = explode( ' ', $tag, 2 );
		}

		if ( in_array( $closing, $self_closing_tags, true ) ) {
			return "<{$tag} />";
		}

		$content = implode( '', $args );

		return "<{$tag}>{$content}</{$closing}>";
	}
	endif;

if ( ! function_exists( 'epl_get_multipage_link' ) ) :

	/**
	 * Pagination Multipage link
	 *
	 * @param int $page Page number.
	 * @since 2.1
	 */
	function epl_get_multipage_link( $page = 1 ) {
		global $post, $wp_rewrite;

		if ( 1 === $page ) {
			$url = get_permalink();
		} else {
			$opt_permalink_str = get_option( 'permalink_structure' );
			if ( empty( $opt_permalink_str ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				$url = add_query_arg( 'page', $page, get_permalink() );
			} elseif ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $wp_rewrite->pagination_base . "/$page", 'single_paged' );
			} else {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
			}
		}

		return esc_url( $url );
	}
endif;

/**
 * Get next page URL for EPL archives / shortcodes
 *
 * @param  WP_Query $query WP Query object.
 * @return string
 * @since 3.3.3
 * @since 3.5.1 Fixed shortcode pagination when permalinks are plain.
 * @since 3.5.3 Fixed sorting not working for pagination on shortcode.
 */
function epl_get_next_page_link( $query ) {
	$link = next_posts( $query->max_num_pages, false );

	if ( $query->get( 'is_epl_shortcode' ) &&
		in_array( $query->get( 'epl_shortcode_name' ), epl_get_shortcode_list(), true ) ) {

		$permalink_structure = get_option( 'permalink_structure' );

		if ( empty( $permalink_structure ) ) {

			$page = $query->get( 'paged' );

			if ( ! $page ) {
				$page = 1;
			}

			$page++;

			$link = epl_add_or_update_params( $link, 'paged', $page );
		}

		$link = epl_add_or_update_params( $link, 'pagination_id', $query->get( 'instance_id' ) );
		$link = epl_add_or_update_params( $link, 'instance_id', $query->get( 'instance_id' ) );
	}
	return apply_filters( 'epl_get_next_page_link', $link );
}

/**
 * Next page Link
 *
 * @param  WP_Query $query WP Query object.
 * @param  string   $label Pagination 'next' label.
 * @return string
 * @since 3.3.3
 */
function epl_next_post_link( $query, $label = null ) {

	$paged    = $query->get( 'paged' );
	$nextpage = intval( $paged ) + 1;

	if ( $nextpage <= $query->max_num_pages ) {

		if ( null === $label ) {
			$label = __( 'Next Page &raquo;', 'easy-property-listings' );
		}

		$attr = apply_filters( 'epl_next_posts_link_attributes', '' );
		return '<a href="' . epl_get_next_page_link( $query ) . "\" $attr>" . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
	}
}

/**
 * Get Prev page URL for EPL archives / shortcodes
 *
 * @param  WP_Query $query WP Query object.
 * @return string
 * @since 3.3.3
 * @since 3.5.1 Fixed shortcode pagination when permalinks are plain.
 * @since 3.5.3 Fixed sorting not working for pagination on shortcode.
 */
function epl_get_prev_page_link( $query ) {

	$link = previous_posts( false );

	if ( $query->get( 'is_epl_shortcode' ) &&
		in_array( $query->get( 'epl_shortcode_name' ), epl_get_shortcode_list(), true ) ) {
			$permalink_structure = get_option( 'permalink_structure' );

		if ( empty( $permalink_structure ) ) {

			$page = $query->get( 'paged' );

			if ( ! $page ) {
				$page = 1;
			}

			$page--;

			$link = epl_add_or_update_params( $link, 'paged', $page );
		}
		$link = epl_add_or_update_params( $link, 'pagination_id', $query->get( 'instance_id' ) );
		$link = epl_add_or_update_params( $link, 'instance_id', $query->get( 'instance_id' ) );
	}
	return apply_filters( 'epl_get_prev_page_link', $link );
}

/**
 * Prev page Link
 *
 * @since 3.3.3
 * @param  WP_Query $query WP Query object.
 * @param  string   $label Pagination 'previous' label.
 * @return string
 */
function epl_prev_post_link( $query, $label = null ) {

	global $paged;

	if ( $paged > 1 ) {

		if ( null === $label ) {
			$label = __( '&laquo; Previous Page', 'easy-property-listings' );
		}

		$attr = apply_filters( 'epl_prev_posts_link_attributes', '' );
		return '<a href="' . epl_get_prev_page_link( $query ) . "\" $attr>" . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
	}
}

/**
 * WordPress Default Pagination
 *
 * @param  WP_Query $query WP Query object.
 *
 * @since 2.1
 * @since 3.3.3  Revised.
 * @since 3.4.44 Fix: Warning for wp_kses_post when empty link.
 * @since 3.5.11 Fix: Pagination not using return return value in write context.
 */
function epl_wp_default_pagination( $query = array() ) {
	if ( empty( $query ) ) {
		?>
	<div class="epl-paginate-default-wrapper epl-clearfix">
		<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'easy-property-listings' ) ); ?></div>
		<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'easy-property-listings' ) ); ?></div>
	</div>
		<?php
	} else {
		$query_open = $query['query'];
		?>
		<div class="epl-paginate-default-wrapper epl-clearfix">
			<div class="alignleft">
				<?php
				$prev_post_link = epl_prev_post_link( $query_open );
				if ( ! empty( $prev_post_link ) ) {
					echo wp_kses_post( epl_prev_post_link( $query_open ) );
				}
				?>
			</div>
			<div class="alignright">
				<?php
				$next_post_link = epl_next_post_link( $query_open );
				if ( ! empty( $next_post_link ) ) {
					echo wp_kses_post( epl_next_post_link( $query_open ) );
				}
				?>
			</div>
		</div>
		<?php
	}
}
