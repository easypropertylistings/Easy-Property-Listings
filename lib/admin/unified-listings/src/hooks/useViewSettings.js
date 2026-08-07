/**
 * EPL Unified Listings - useViewSettings Hook
 *
 * Custom hook for managing user view settings.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useEffect, useCallback } from '@wordpress/element';
import {
	fetchViewSettings,
	saveViewSettings as apiSaveSettings,
} from '../utils/api';

// Default settings
const defaultSettings = {
	viewMode: 'list',
	list: {
		columns: [
			'thumbnail',
			'heading',
			'address',
			'priceDisplay',
			'propertyStatus',
			'author',
			'modified',
		],
		perPage: 25,
	},
	grid: {
		cardsPerRow: 4,
		showPrice: true,
		showStatus: true,
		showBeds: true,
		showBaths: true,
	},
	compact: {
		fields: [ 'address', 'priceDisplay', 'propertyStatus' ],
		perPage: 50,
	},
};

/**
 * Deep merge settings to preserve nested defaults
 *
 * @param {Object} defaults Default settings object.
 * @param {Object} user     User settings from API.
 * @return {Object} Merged settings.
 */
function mergeSettings( defaults, user ) {
	const merged = { ...defaults };

	for ( const key in user ) {
		if ( Object.prototype.hasOwnProperty.call( user, key ) ) {
			if (
				typeof user[ key ] === 'object' &&
				user[ key ] !== null &&
				! Array.isArray( user[ key ] )
			) {
				// Recursively merge nested objects
				merged[ key ] = {
					...defaults[ key ],
					...user[ key ],
				};
			} else {
				// Directly assign arrays and primitives
				merged[ key ] = user[ key ];
			}
		}
	}

	return merged;
}

/**
 * useViewSettings Hook
 *
 * @return {Object} Settings and save function
 */
export function useViewSettings() {
	const [ settings, setSettings ] = useState( defaultSettings );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ error, setError ] = useState( null );

	// Load settings on mount
	useEffect( () => {
		const loadSettings = async () => {
			try {
				const response = await fetchViewSettings();
				if ( response && Object.keys( response ).length > 0 ) {
					// Deep merge to preserve nested defaults
					setSettings( mergeSettings( defaultSettings, response ) );
				}
			} catch ( err ) {
				console.error( 'Error loading view settings:', err );
				// Use defaults on error
			} finally {
				setIsLoading( false );
			}
		};

		loadSettings();
	}, [] );

	// Save settings
	const saveSettings = useCallback(
		async ( newSettings ) => {
			setIsSaving( true );
			setError( null );

			// Deep merge for saving too
			const merged = mergeSettings( settings, newSettings );
			setSettings( merged );

			try {
				await apiSaveSettings( merged );
			} catch ( err ) {
				console.error( 'Error saving view settings:', err );
				setError( err.message || 'Failed to save settings' );
				// Could revert here, but we'll keep the optimistic update
			} finally {
				setIsSaving( false );
			}
		},
		[ settings ]
	);

	// Update single view's settings
	const updateViewSettings = useCallback(
		( viewMode, viewSettings ) => {
			saveSettings( {
				[ viewMode ]: { ...settings[ viewMode ], ...viewSettings },
			} );
		},
		[ settings, saveSettings ]
	);

	return {
		settings,
		isLoading,
		isSaving,
		error,
		saveSettings,
		updateViewSettings,
	};
}

export default useViewSettings;
