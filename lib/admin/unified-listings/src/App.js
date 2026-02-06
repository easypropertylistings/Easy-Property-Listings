/**
 * EPL Unified Listings - Main App Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useEffect, useCallback } from '@wordpress/element';
import { Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useStore } from './store';
import { useListings } from './hooks/useListings';
import { useViewSettings } from './hooks/useViewSettings';

import Header from './components/Header';
import FilterBar from './components/FilterBar';
import ViewSwitcher from './components/ViewSwitcher';
import Pagination from './components/Pagination';
import BulkActions from './components/BulkActions';
import ViewSettingsModal from './components/ViewSettingsModal';

import ListView from './views/ListView';
import GridView from './views/GridView';
import CompactView from './views/CompactView';

export default function App() {
        const { state, dispatch, config } = useStore();

        // State
        const [viewMode, setViewMode] = useState('list');
        const [filters, setFilters] = useState({
                search: '',
                status: [],
                postType: [],
                author: [],
                price_min: '',
                price_max: '',
                rent_min: '',
                rent_max: '',
                beds_min: '',
                baths_min: '',
                property_category: '',
                location: '',
        });
        const [page, setPage] = useState(1);
        const [orderby, setOrderby] = useState('modified');
        const [order, setOrder] = useState('desc');
        const [selectedIds, setSelectedIds] = useState([]);
        const [isSettingsOpen, setIsSettingsOpen] = useState(false);
        const [error, setError] = useState(null);

        // Hooks
        const { settings, updateSettings, saveSettings } = useViewSettings();
        const perPage = settings?.[viewMode]?.perPage || 25;
        const imageSize = settings?.[viewMode]?.imageSize || 'thumbnail';

        const {
                listings,
                total,
                totalPages,
                postTypeCounts,
                loading,
                error: listingsError,
                refetch,
        } = useListings({
                ...filters,
                page,
                perPage,
                orderby,
                order,
                image_size: imageSize,
        });

        // Handle filter changes
        const handleFilterChange = useCallback((newFilters) => {
                setFilters((prev) => ({ ...prev, ...newFilters }));
                setPage(1);
                setSelectedIds([]);
        }, []);

        // Handle sort
        const handleSort = useCallback((newOrderby, newOrder) => {
                setOrderby(newOrderby);
                setOrder(newOrder);
                setPage(1);
        }, []);

        // Handle page change
        const handlePageChange = useCallback((newPage) => {
                setPage(newPage);
                setSelectedIds([]);
        }, []);

        // Handle view change
        const handleViewChange = useCallback((newView) => {
                setViewMode(newView);
                setSelectedIds([]);
        }, []);

        // Handle bulk action complete
        const handleBulkActionComplete = useCallback(() => {
                setSelectedIds([]);
                refetch();
        }, [refetch]);

        // Set up global function for filtering by location from suburb links
        useEffect(() => {
                window.eplSetLocationFilter = (locationSlug) => {
                        handleFilterChange({ location: locationSlug });
                };
                return () => {
                        delete window.eplSetLocationFilter;
                };
        }, [handleFilterChange]);

        // Clear error
        useEffect(() => {
                if (listingsError) {
                        setError(listingsError);
                }
        }, [listingsError]);

        // Render current view
        const renderView = () => {
                const viewProps = {
                        listings,
                        loading,
                        selectedIds,
                        onSelect: setSelectedIds,
                        orderby,
                        order,
                        onSort: handleSort,
                        onRefresh: refetch,
                        imageSize: imageSize,
                };

                // Get view-specific settings
                const listColumns = settings?.list?.columns || [];
                const compactFields = settings?.compact?.fields || [];
                const gridSettings = settings?.grid || {};

                switch (viewMode) {
                        case 'grid':
                                return (
                                        <GridView {...viewProps} gridSettings={gridSettings} />
                                );
                        case 'compact':
                                return (
                                        <CompactView
                                                {...viewProps}
                                                visibleFields={compactFields}
                                        />
                                );
                        default:
                                return (
                                        <ListView {...viewProps} visibleColumns={listColumns} />
                                );
                }
        };

        return (
                <div className="epl-unified-listings">
                        {error && (
                                <Notice status="error" onDismiss={() => setError(null)}>
                                        {error}
                                </Notice>
                        )}

                        <Header
                                total={total}
                                postTypeCounts={postTypeCounts}
                                selectedPostType={filters.postType?.[0] || ''}
                                onPostTypeChange={(pt) => handleFilterChange({ postType: pt ? [pt] : [] })}
                                onOpenSettings={() => setIsSettingsOpen(true)}
                        />

                        <div className="epl-listings-toolbar">
                                <FilterBar
                                        filters={filters}
                                        onFilterChange={handleFilterChange}
                                />
                                <ViewSwitcher
                                        currentView={viewMode}
                                        onViewChange={handleViewChange}
                                />
                        </div>

                        {selectedIds.length > 0 && (
                                <BulkActions
                                        selectedIds={selectedIds}
                                        onClearSelection={() => setSelectedIds([])}
                                        onActionComplete={handleBulkActionComplete}
                                />
                        )}

                        <div className="epl-listings-content">{renderView()}</div>

                        <Pagination
                                page={page}
                                totalPages={totalPages}
                                total={total}
                                perPage={perPage}
                                onPageChange={handlePageChange}
                        />

                        {isSettingsOpen && (
                                <ViewSettingsModal
                                        settings={settings}
                                        viewMode={viewMode}
                                        onClose={() => setIsSettingsOpen(false)}
                                        onSave={saveSettings}
                                />
                        )}
                </div>
        );
}
