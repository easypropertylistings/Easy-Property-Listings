/**
 * EPL Unified Listings - Header Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { Button, Icon } from '@wordpress/components';
import { settings } from '@wordpress/icons';
import { useStore } from '../store';

export default function Header({ total, postTypeCounts = {}, selectedPostType, onPostTypeChange, onOpenSettings }) {
        const { config } = useStore();
        const labels = config?.labels || {};
        const postTypeLabels = config?.postTypeLabels || {};

        // Calculate total across all post types
        const allTotal = Object.values(postTypeCounts).reduce((sum, count) => sum + count, 0);

        // Get post types with counts > 0
        const activePostTypes = Object.entries(postTypeCounts).filter(([, count]) => count > 0);

        return (
                <div className="epl-listings-header">
                        <div className="epl-listings-tabs">
                                <button
                                        type="button"
                                        className={`epl-tab ${!selectedPostType ? 'epl-tab--active' : ''}`}
                                        onClick={() => onPostTypeChange('')}
                                >
                                        {labels.allListings || __('All Listings', 'easy-property-listings')}
                                        <span className="epl-tab-count">({allTotal})</span>
                                </button>

                                {activePostTypes.map(([postType, count]) => (
                                        <button
                                                key={postType}
                                                type="button"
                                                className={`epl-tab ${selectedPostType === postType ? 'epl-tab--active' : ''}`}
                                                onClick={() => onPostTypeChange(postType)}
                                        >
                                                {postTypeLabels[postType] || postType}
                                                <span className="epl-tab-count">({count})</span>
                                        </button>
                                ))}
                        </div>

                        <div className="epl-header-actions">
                                <Button
                                        className="epl-settings-button"
                                        onClick={onOpenSettings}
                                        icon={<Icon icon={settings} size={20} />}
                                        label={
                                                labels.settings ||
                                                __('View Settings', 'easy-property-listings')
                                        }
                                />
                        </div>
                </div>
        );
}
