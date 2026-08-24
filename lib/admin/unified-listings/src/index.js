/**
 * EPL Unified Listings - Entry Point
 *
 * @package EPL
 * @since 4.0.0
 */

import { createRoot, StrictMode } from '@wordpress/element';
import { StoreProvider } from './store';
import App from './App';

import './styles/main.scss';

// Wait for DOM ready
document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById( 'epl-unified-listings-root' );

	if ( container ) {
		const root = createRoot( container );
		root.render(
			<StrictMode>
				<StoreProvider>
					<App />
				</StoreProvider>
			</StrictMode>
		);
	}
} );
