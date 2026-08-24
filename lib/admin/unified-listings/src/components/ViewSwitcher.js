/**
 * EPL Unified Listings - ViewSwitcher Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { Button, Tooltip } from '@wordpress/components';
import { list, grid, menu } from '@wordpress/icons';
import { useStore } from '../store';

const views = [
	{
		key: 'list',
		icon: list,
		label: __( 'List View', 'easy-property-listings' ),
	},
	{
		key: 'grid',
		icon: grid,
		label: __( 'Grid View', 'easy-property-listings' ),
	},
	{
		key: 'compact',
		icon: menu,
		label: __( 'Compact View', 'easy-property-listings' ),
	},
];

export default function ViewSwitcher( { currentView, onViewChange } ) {
	return (
		<div className="epl-view-switcher">
			{ views.map( ( view ) => (
				<Tooltip key={ view.key } text={ view.label }>
					<Button
						icon={ view.icon }
						onClick={ () => onViewChange( view.key ) }
						className={
							currentView === view.key ? 'is-active' : ''
						}
						aria-pressed={ currentView === view.key }
					/>
				</Tooltip>
			) ) }
		</div>
	);
}
