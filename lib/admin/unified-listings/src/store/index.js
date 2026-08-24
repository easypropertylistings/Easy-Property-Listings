/**
 * EPL Unified Listings - Store
 *
 * Simple React context-based state management.
 *
 * @package EPL
 * @since 4.0.0
 */

import {
	createContext,
	useContext,
	useReducer,
	useMemo,
} from '@wordpress/element';

// Get config from WordPress
const config = window.eplAdminListings || {};

// Initial state
const initialState = {
	viewMode: 'list',
	filters: {
		page: 1,
		perPage: 25,
		search: '',
		status: '',
		author: '',
		postType: [],
		orderby: 'modified',
		order: 'desc',
	},
	selectedIds: [],
	isSettingsOpen: false,
	config: {
		restUrl: config.restUrl || '/wp-json/epl/v1/admin/',
		nonce: config.nonce || '',
		postTypes: config.postTypes || [],
		postTypeLabels: config.postTypeLabels || {},
		statusLabels: config.statusLabels || {},
		authors: config.authors || [],
		categories: config.categories || [],
		suburbs: config.suburbs || [],
		imageSizes: config.imageSizes || [],
		quickEditFields: config.quickEditFields || [],
		currency: config.currency || { symbol: '$', position: 'before' },
		labels: config.labels || {},
		adminUrl: config.adminUrl || '/wp-admin/',
	},
};

// Action types
const ActionTypes = {
	SET_VIEW_MODE: 'SET_VIEW_MODE',
	SET_FILTERS: 'SET_FILTERS',
	SET_SELECTED: 'SET_SELECTED',
	CLEAR_SELECTED: 'CLEAR_SELECTED',
	TOGGLE_SETTINGS: 'TOGGLE_SETTINGS',
	UPDATE_CONFIG: 'UPDATE_CONFIG',
};

// Reducer
function reducer( state, action ) {
	switch ( action.type ) {
		case ActionTypes.SET_VIEW_MODE:
			return { ...state, viewMode: action.payload };

		case ActionTypes.SET_FILTERS:
			return {
				...state,
				filters: { ...state.filters, ...action.payload },
				// Reset to page 1 when filters change (except page itself)
				...( action.payload.page === undefined && {
					filters: { ...state.filters, ...action.payload, page: 1 },
				} ),
			};

		case ActionTypes.SET_SELECTED:
			return { ...state, selectedIds: action.payload };

		case ActionTypes.CLEAR_SELECTED:
			return { ...state, selectedIds: [] };

		case ActionTypes.TOGGLE_SETTINGS:
			return { ...state, isSettingsOpen: ! state.isSettingsOpen };

		case ActionTypes.UPDATE_CONFIG:
			return { ...state, config: { ...state.config, ...action.payload } };

		default:
			return state;
	}
}

// Context
const StoreContext = createContext( null );

/**
 * Store Provider Component
 */
export function StoreProvider( { children } ) {
	const [ state, dispatch ] = useReducer( reducer, initialState );

	const value = useMemo( () => ( { state, dispatch } ), [ state ] );

	return (
		<StoreContext.Provider value={ value }>
			{ children }
		</StoreContext.Provider>
	);
}

/**
 * Hook to access the store
 */
export function useStore() {
	const context = useContext( StoreContext );
	if ( ! context ) {
		throw new Error( 'useStore must be used within a StoreProvider' );
	}
	return context;
}

/**
 * Hook to access just the config
 */
export function useConfig() {
	const { state } = useStore();
	return state.config;
}

export { ActionTypes };
