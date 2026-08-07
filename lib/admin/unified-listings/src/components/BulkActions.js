/**
 * EPL Unified Listings - BulkActions Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { SelectControl, Button, Icon } from '@wordpress/components';
import { close } from '@wordpress/icons';
import { bulkAction } from '../utils/api';
import { useStore } from '../store';

export default function BulkActions( {
	selectedIds,
	onClearSelection,
	onActionComplete,
} ) {
	const { config } = useStore();
	const labels = config?.labels || {};
	const statusLabels = config?.statusLabels || {};

	const [ action, setAction ] = useState( '' );
	const [ statusValue, setStatusValue ] = useState( '' );
	const [ isProcessing, setIsProcessing ] = useState( false );

	const actionOptions = [
		{ value: '', label: __( 'Bulk Actions', 'easy-property-listings' ) },
		{
			value: 'trash',
			label:
				labels.trash || __( 'Move to Trash', 'easy-property-listings' ),
		},
		{
			value: 'set_status',
			label: __( 'Set Status', 'easy-property-listings' ),
		},
		{
			value: 'set_featured',
			label: __( 'Mark Featured', 'easy-property-listings' ),
		},
		{
			value: 'unset_featured',
			label: __( 'Unmark Featured', 'easy-property-listings' ),
		},
	];

	const statusOptions = Object.entries( statusLabels ).map(
		( [ value, label ] ) => ( {
			value,
			label,
		} )
	);

	const handleApply = async () => {
		if ( ! action || selectedIds.length === 0 ) return;

		// Confirmation for destructive actions
		if ( action === 'trash' ) {
			const confirmed = window.confirm(
				labels.confirmTrash ||
					__(
						'Move selected listings to trash?',
						'easy-property-listings'
					)
			);
			if ( ! confirmed ) return;
		}

		setIsProcessing( true );

		try {
			let value = null;

			if ( action === 'set_status' && statusValue ) {
				value = statusValue;
			} else if ( action === 'set_featured' ) {
				value = true;
			} else if ( action === 'unset_featured' ) {
				await bulkAction( 'set_featured', selectedIds, false );
				setIsProcessing( false );
				onActionComplete();
				return;
			}

			await bulkAction( action, selectedIds, value );
			onActionComplete();
		} catch ( error ) {
			console.error( 'Bulk action failed:', error );
		} finally {
			setIsProcessing( false );
			setAction( '' );
			setStatusValue( '' );
		}
	};

	return (
		<div className="epl-bulk-actions">
			<div className="epl-bulk-actions__info">
				<span>
					{ selectedIds.length }{ ' ' }
					{ labels.selected ||
						__( 'selected', 'easy-property-listings' ) }
				</span>
				<Button
					icon={ <Icon icon={ close } size={ 16 } /> }
					onClick={ onClearSelection }
					label={ __( 'Clear selection', 'easy-property-listings' ) }
					isSmall
					style={ { color: 'white', marginLeft: '8px' } }
				/>
			</div>
			<div className="epl-bulk-actions__controls">
				<SelectControl
					value={ action }
					options={ actionOptions }
					onChange={ setAction }
					__nextHasNoMarginBottom
				/>

				{ action === 'set_status' && (
					<SelectControl
						value={ statusValue }
						options={ [
							{
								value: '',
								label: __(
									'Select Status',
									'easy-property-listings'
								),
							},
							...statusOptions,
						] }
						onChange={ setStatusValue }
						__nextHasNoMarginBottom
					/>
				) }

				<Button
					variant="primary"
					onClick={ handleApply }
					disabled={
						! action ||
						isProcessing ||
						( action === 'set_status' && ! statusValue )
					}
					isBusy={ isProcessing }
				>
					{ labels.apply || __( 'Apply', 'easy-property-listings' ) }
				</Button>
			</div>
		</div>
	);
}
