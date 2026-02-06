/**
 * EPL Unified Listings - Pagination Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { Button, Icon } from '@wordpress/components';
import { chevronLeft, chevronRight } from '@wordpress/icons';

export default function Pagination( {
	page,
	totalPages,
	total,
	perPage,
	onPageChange,
} ) {
	if ( totalPages <= 1 ) {
		return null;
	}

	const start = ( page - 1 ) * perPage + 1;
	const end = Math.min( page * perPage, total );

	// Build page numbers
	const getPageNumbers = () => {
		const pages = [];
		const showPages = 5;
		let startPage = Math.max( 1, page - Math.floor( showPages / 2 ) );
		let endPage = Math.min( totalPages, startPage + showPages - 1 );

		if ( endPage - startPage < showPages - 1 ) {
			startPage = Math.max( 1, endPage - showPages + 1 );
		}

		if ( startPage > 1 ) {
			pages.push( 1 );
			if ( startPage > 2 ) {
				pages.push( '...' );
			}
		}

		for ( let i = startPage; i <= endPage; i++ ) {
			pages.push( i );
		}

		if ( endPage < totalPages ) {
			if ( endPage < totalPages - 1 ) {
				pages.push( '...' );
			}
			pages.push( totalPages );
		}

		return pages;
	};

	return (
		<div className="epl-pagination">
			<div className="epl-pagination__info">
				{ __( 'Showing', 'easy-property-listings' ) } { start }–{ end }{ ' ' }
				{ __( 'of', 'easy-property-listings' ) } { total }
			</div>
			<div className="epl-pagination__controls">
				<button
					className="epl-pagination__button"
					onClick={ () => onPageChange( page - 1 ) }
					disabled={ page <= 1 }
					aria-label={ __(
						'Previous page',
						'easy-property-listings'
					) }
				>
					<Icon icon={ chevronLeft } size={ 18 } />
				</button>

				{ getPageNumbers().map( ( pageNum, index ) =>
					pageNum === '...' ? (
						<span
							key={ `ellipsis-${ index }` }
							className="epl-pagination__ellipsis"
						>
							...
						</span>
					) : (
						<button
							key={ pageNum }
							className={ `epl-pagination__button ${
								pageNum === page ? 'is-active' : ''
							}` }
							onClick={ () => onPageChange( pageNum ) }
							aria-current={
								pageNum === page ? 'page' : undefined
							}
						>
							{ pageNum }
						</button>
					)
				) }

				<button
					className="epl-pagination__button"
					onClick={ () => onPageChange( page + 1 ) }
					disabled={ page >= totalPages }
					aria-label={ __( 'Next page', 'easy-property-listings' ) }
				>
					<Icon icon={ chevronRight } size={ 18 } />
				</button>
			</div>
		</div>
	);
}
