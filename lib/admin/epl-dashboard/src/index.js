/**
 * EPL Dashboard - Entry Point
 *
 * @package EPL
 * @since 4.0.0
 */

import { createRoot } from '@wordpress/element';
import App from './App';
import './styles/dashboard.scss';

const container = document.getElementById('epl-dashboard-root');
if (container) {
        const root = createRoot(container);
        root.render(<App />);
}
