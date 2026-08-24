/**
 * EPL Unified Listings - useInlineEdit Hook
 *
 * Custom hook for inline editing functionality.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useCallback } from '@wordpress/element';
import { updateListing } from '../utils/api';

/**
 * useInlineEdit Hook
 *
 * @param {Function} onSuccess Callback after successful save
 * @return {Object} Inline edit state and functions
 */
export function useInlineEdit( onSuccess ) {
	const [ editingCell, setEditingCell ] = useState( null ); // { id, field }
	const [ editValue, setEditValue ] = useState( '' );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ error, setError ] = useState( null );

	// Start editing a cell
	const startEdit = useCallback( ( id, field, value ) => {
		setEditingCell( { id, field } );
		setEditValue( value ?? '' );
		setError( null );
	}, [] );

	// Cancel editing
	const cancelEdit = useCallback( () => {
		setEditingCell( null );
		setEditValue( '' );
		setError( null );
	}, [] );

	// Save the edited value
	const saveEdit = useCallback( async () => {
		if ( ! editingCell ) {
			return;
		}

		setIsSaving( true );
		setError( null );

		try {
			const response = await updateListing( editingCell.id, {
				[ editingCell.field ]: editValue,
			} );

			if ( response.success ) {
				setEditingCell( null );
				setEditValue( '' );
				if ( onSuccess ) {
					onSuccess( response.listing );
				}
			} else if ( response.errors && response.errors.length > 0 ) {
				setError( response.errors[ 0 ] );
			}
		} catch ( err ) {
			console.error( 'Error saving field:', err );
			setError( err.message || 'Failed to save' );
		} finally {
			setIsSaving( false );
		}
	}, [ editingCell, editValue, onSuccess ] );

	// Check if a specific cell is being edited
	const isEditing = useCallback(
		( id, field ) => {
			return editingCell?.id === id && editingCell?.field === field;
		},
		[ editingCell ]
	);

	// Update the edit value
	const updateValue = useCallback( ( value ) => {
		setEditValue( value );
	}, [] );

	return {
		editingCell,
		editValue,
		isSaving,
		error,
		startEdit,
		cancelEdit,
		saveEdit,
		isEditing,
		updateValue,
	};
}

export default useInlineEdit;
