/**
 * EPL Unified Listings - Advanced Filters Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useMemo } from '@wordpress/element';
import {
	Button,
	Modal,
	TextControl,
	Flex,
	FlexItem,
	ComboboxControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { funnel } from '@wordpress/icons';
import { useConfig } from '../store';

export default function AdvancedFilters( { filters, onFilterChange } ) {
	const [ isOpen, setIsOpen ] = useState( false );
	const config = useConfig();

	// Get categories and suburbs from config
	const categories = config?.categories || [];
	const suburbs = config?.suburbs || [];

	// Local state for filters to batch changes
	const [ localFilters, setLocalFilters ] = useState( { ...filters } );

	const openModal = () => {
		// Reset local state to current global filters when opening
		setLocalFilters( { ...filters } );
		setIsOpen( true );
	};

	const closeModal = () => {
		setIsOpen( false );
	};

	const handleChange = ( key, value ) => {
		setLocalFilters( ( prev ) => ( {
			...prev,
			[ key ]: value,
		} ) );
	};

	const applyFilters = () => {
		onFilterChange( localFilters );
		closeModal();
	};

	const clearAdvanced = () => {
		const cleared = {
			...localFilters,
			price_min: '',
			price_max: '',
			rent_min: '',
			rent_max: '',
			beds_min: '',
			baths_min: '',
			property_category: '',
			location: '',
		};
		setLocalFilters( cleared );
	};

	// Build category options for ComboboxControl
	const categoryOptions = useMemo(
		() =>
			categories.map( ( cat ) => ( {
				value: cat.value,
				label: cat.label,
			} ) ),
		[ categories ]
	);

	// Build suburb options for ComboboxControl
	const suburbOptions = useMemo(
		() =>
			suburbs.map( ( suburb ) => ( {
				value: suburb.value,
				label: suburb.label,
			} ) ),
		[ suburbs ]
	);

	// Check if any advanced filters are active
	const hasActiveAdvanced =
		filters.price_min ||
		filters.price_max ||
		filters.rent_min ||
		filters.rent_max ||
		filters.beds_min ||
		filters.baths_min ||
		filters.property_category ||
		filters.location;

	return (
		<>
			<Button
				icon={ funnel }
				onClick={ openModal }
				className={ `epl-advanced-filters-toggle ${
					hasActiveAdvanced ? 'has-active-filters' : ''
				}` }
				label={ __( 'More Filters', 'easy-property-listings' ) }
			/>

			{ isOpen && (
				<Modal
					title={ __( 'Advanced Filters', 'easy-property-listings' ) }
					onRequestClose={ closeModal }
					className="epl-advanced-filters-modal"
				>
					<div className="epl-filter-section">
						<h4>
							{ __( 'Price Range', 'easy-property-listings' ) }
						</h4>
						<Flex gap={ 3 }>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Min',
										'easy-property-listings'
									) }
									value={ localFilters.price_min || '' }
									onChange={ ( v ) =>
										handleChange( 'price_min', v )
									}
									type="number"
									placeholder="0"
								/>
							</FlexItem>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Max',
										'easy-property-listings'
									) }
									value={ localFilters.price_max || '' }
									onChange={ ( v ) =>
										handleChange( 'price_max', v )
									}
									type="number"
									placeholder="Any"
								/>
							</FlexItem>
						</Flex>
					</div>

					<div className="epl-filter-section">
						<h4>
							{ __( 'Rent Range', 'easy-property-listings' ) }
						</h4>
						<Flex gap={ 3 }>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Min',
										'easy-property-listings'
									) }
									value={ localFilters.rent_min || '' }
									onChange={ ( v ) =>
										handleChange( 'rent_min', v )
									}
									type="number"
									placeholder="0"
								/>
							</FlexItem>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Max',
										'easy-property-listings'
									) }
									value={ localFilters.rent_max || '' }
									onChange={ ( v ) =>
										handleChange( 'rent_max', v )
									}
									type="number"
									placeholder="Any"
								/>
							</FlexItem>
						</Flex>
					</div>

					<div className="epl-filter-section">
						<h4>
							{ __(
								'Bedrooms & Bathrooms',
								'easy-property-listings'
							) }
						</h4>
						<Flex gap={ 3 }>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Min Beds',
										'easy-property-listings'
									) }
									value={ localFilters.beds_min || '' }
									onChange={ ( v ) =>
										handleChange( 'beds_min', v )
									}
									type="number"
									min={ 0 }
									placeholder="Any"
								/>
							</FlexItem>
							<FlexItem isBlock>
								<TextControl
									label={ __(
										'Min Baths',
										'easy-property-listings'
									) }
									value={ localFilters.baths_min || '' }
									onChange={ ( v ) =>
										handleChange( 'baths_min', v )
									}
									type="number"
									min={ 0 }
									placeholder="Any"
								/>
							</FlexItem>
						</Flex>
					</div>

					<div className="epl-filter-section">
						<h4>{ __( 'Category', 'easy-property-listings' ) }</h4>
						<ComboboxControl
							value={ localFilters.property_category || '' }
							options={ categoryOptions }
							onChange={ ( v ) =>
								handleChange( 'property_category', v )
							}
							allowReset
							__nextHasNoMarginBottom
						/>
					</div>

					<div className="epl-filter-section">
						<h4>{ __( 'Suburb', 'easy-property-listings' ) }</h4>
						<ComboboxControl
							value={ localFilters.location || '' }
							options={ suburbOptions }
							onChange={ ( v ) => handleChange( 'location', v ) }
							allowReset
							__nextHasNoMarginBottom
						/>
					</div>

					<div className="epl-modal-footer">
						<Flex justify="space-between">
							<FlexItem>
								<Button
									variant="tertiary"
									onClick={ clearAdvanced }
								>
									{ __( 'Reset', 'easy-property-listings' ) }
								</Button>
							</FlexItem>
							<FlexItem>
								<Flex gap={ 2 }>
									<FlexItem>
										<Button
											variant="secondary"
											onClick={ closeModal }
										>
											{ __(
												'Cancel',
												'easy-property-listings'
											) }
										</Button>
									</FlexItem>
									<FlexItem>
										<Button
											variant="primary"
											onClick={ applyFilters }
										>
											{ __(
												'Apply Filters',
												'easy-property-listings'
											) }
										</Button>
									</FlexItem>
								</Flex>
							</FlexItem>
						</Flex>
					</div>
				</Modal>
			) }
		</>
	);
}
