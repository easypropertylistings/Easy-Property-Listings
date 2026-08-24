/**
 * EPL Unified Listings - List View Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useState, useMemo, useCallback } from '@wordpress/element';
import { CheckboxControl, Button, Spinner, Icon } from '@wordpress/components';
import {
        edit,
        external,
        chevronUp,
        chevronDown,
        starFilled,
        starEmpty,
        pencil,
} from '@wordpress/icons';
import { useStore } from '../store';
import { updateListing } from '../utils/api';
import QuickEditModal from '../components/QuickEditModal';

// Decode HTML entities
const decodeHtmlEntities = (str) => {
        if (!str) return '';
        const textarea = document.createElement('textarea');
        textarea.innerHTML = str;
        return textarea.value;
};

// Format date
const formatDate = (dateString) => {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
        });
};

// Get status class
const getStatusClass = (status) => {
        const statusMap = {
                current: 'current',
                sold: 'sold',
                leased: 'leased',
                withdrawn: 'withdrawn',
                offmarket: 'offmarket',
                deleted: 'deleted',
        };
        return statusMap[status] || 'current';
};

// Column definitions - keys match settings
const allColumns = [
        {
                key: 'id',
                label: __('ID', 'easy-property-listings'),
                sortable: true,
                sortKey: 'ID',
                width: '60px',
        },
        {
                key: 'thumbnail',
                label: __('Image', 'easy-property-listings'),
                width: '72px',
        },
        {
                key: 'title',
                label: __('Title', 'easy-property-listings'),
                sortable: true,
                sortKey: 'title',
        },
        {
                key: 'heading',
                label: __('Heading', 'easy-property-listings'),
                sortable: true,
                sortKey: 'title',
        },
        {
                key: 'address',
                label: __('Address', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_address_display',
        },
        {
                key: 'suburb',
                label: __('Suburb', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_address_suburb',
        },
        {
                key: 'listingId',
                label: __('ID', 'easy-property-listings'),
                sortable: true,
                sortKey: 'ID',
                width: '60px',
        },
        {
                key: 'postType',
                label: __('Type', 'easy-property-listings'),
                sortable: true,
                sortKey: 'post_type',
        },
        {
                key: 'propertyStatus',
                label: __('Status', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_status',
        },
        {
                key: 'priceDisplay',
                label: __('Price', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_price',
        },
        {
                key: 'bedrooms',
                label: __('Beds', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_bedrooms',
        },
        {
                key: 'bathrooms',
                label: __('Baths', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_bathrooms',
        },
        { key: 'featured', label: __('Featured', 'easy-property-listings') },
        { key: 'features', label: __('Features', 'easy-property-listings') },
        {
                key: 'author',
                label: __('Author', 'easy-property-listings'),
                sortable: true,
                sortKey: 'author',
        },
        {
                key: 'agent',
                label: __('Agent', 'easy-property-listings'),
                sortable: true,
                sortKey: 'property_agent',
        },
        {
                key: 'date',
                label: __('Date', 'easy-property-listings'),
                sortable: true,
                sortKey: 'date',
        },
        {
                key: 'modified',
                label: __('Modified', 'easy-property-listings'),
                sortable: true,
                sortKey: 'modified',
        },
];

export default function ListView({
        listings,
        loading,
        selectedIds,
        onSelect,
        orderby,
        order,
        onSort,
        onRefresh,
        visibleColumns = [],
        imageSize,
}) {
        const { config } = useStore();
        const labels = config?.labels || {};
        const statusLabels = config?.statusLabels || {};

        // Track toggling listings
        const [togglingFeatured, setTogglingFeatured] = useState([]);

        // Quick edit modal state
        const [quickEditListing, setQuickEditListing] = useState(null);

        // Handle featured toggle
        const handleFeaturedToggle = useCallback(
                async (id, currentStatus) => {
                        setTogglingFeatured((prev) => [...prev, id]);

                        try {
                                const response = await updateListing(id, {
                                        property_featured: !currentStatus,
                                });

                                if (response.success && onRefresh) {
                                        onRefresh();
                                }
                        } catch (err) {
                                console.error('Error toggling featured:', err);
                        } finally {
                                setTogglingFeatured((prev) =>
                                        prev.filter((i) => i !== id)
                                );
                        }
                },
                [onRefresh]
        );

        // Filter columns and preserve order based on visible columns setting
        const columns = useMemo(() => {
                let keys = visibleColumns;
                if (!keys || keys.length === 0) {
                        keys = [
                                'thumbnail',
                                'heading',
                                'address',
                                'priceDisplay',
                                'propertyStatus',
                                'author',
                                'modified',
                        ];
                }

                // Map keys to column definitions to preserve order
                return keys
                        .map((key) => allColumns.find((col) => col.key === key))
                        .filter(Boolean);
        }, [visibleColumns]);

        // Handle select all
        const allSelected =
                listings.length > 0 &&
                listings.every((l) => selectedIds.includes(l.id));
        const someSelected = selectedIds.length > 0 && !allSelected;

        const handleSelectAll = (checked) => {
                if (checked) {
                        onSelect(listings.map((l) => l.id));
                } else {
                        onSelect([]);
                }
        };

        const handleSelectRow = (id, checked) => {
                if (checked) {
                        onSelect([...selectedIds, id]);
                } else {
                        onSelect(selectedIds.filter((i) => i !== id));
                }
        };

        // Handle sort
        const handleSort = (column) => {
                if (!column.sortable) return;
                const sortKey = column.sortKey || column.key;
                const newOrder =
                        orderby === sortKey && order === 'desc' ? 'asc' : 'desc';
                onSort(sortKey, newOrder);
        };

        // Render cell content based on column key
        const renderCellContent = (listing, column) => {
                switch (column.key) {
                        case 'id':
                                return (
                                        <span className="epl-listing-id">{listing.id}</span>
                                );

                        case 'thumbnail':
                                return (
                                        <div
                                                className={`epl-thumbnail epl-thumbnail--${imageSize || 'thumbnail'
                                                        }`}
                                        >
                                                {listing.thumbnail ? (
                                                        <img src={listing.thumbnail} alt="" />
                                                ) : (
                                                        <span className="epl-thumbnail--placeholder">
                                                                —
                                                        </span>
                                                )}
                                        </div>
                                );

                        case 'title':
                                return (
                                        <div className="epl-heading-cell">
                                                <span className="epl-listing-title">
                                                        {listing.featured && (
                                                                <Icon
                                                                        icon={starFilled}
                                                                        size={14}
                                                                        className="epl-featured-star"
                                                                />
                                                        )}
                                                        <a href={listing.editLink}>
                                                                {decodeHtmlEntities(listing.title)}
                                                        </a>
                                                </span>
                                                <span className="epl-listing-type">
                                                        {listing.postTypeLabel}
                                                </span>
                                        </div>
                                );

                        case 'heading':
                                return (
                                        <div className="epl-heading-cell">
                                                <span className="epl-listing-title">
                                                        {listing.featured && (
                                                                <Icon
                                                                        icon={starFilled}
                                                                        size={14}
                                                                        className="epl-featured-star"
                                                                />
                                                        )}
                                                        <a href={listing.editLink}>
                                                                {decodeHtmlEntities(
                                                                        listing.heading || listing.title
                                                                )}
                                                        </a>
                                                </span>
                                                <span className="epl-listing-type">
                                                        {listing.postTypeLabel}
                                                </span>
                                        </div>
                                );

                        case 'address':
                                return (
                                        <div className="epl-address-cell">
                                                <a
                                                        href={listing.editLink}
                                                        className="epl-address-link"
                                                >
                                                        {listing.address ||
                                                                decodeHtmlEntities(listing.title) ||
                                                                '—'}
                                                </a>
                                                <div className="epl-address-meta">
                                                        <span className="epl-post-type">
                                                                {listing.postTypeLabel}
                                                        </span>
                                                        <span
                                                                className={`epl-status-badge epl-status-badge--${getStatusClass(
                                                                        listing.propertyStatus
                                                                )}`}
                                                        >
                                                                {statusLabels[listing.propertyStatus] ||
                                                                        listing.propertyStatus}
                                                        </span>
                                                        {listing.underOffer && (
                                                                <span className="epl-status-badge epl-status-badge--underoffer">
                                                                        {__(
                                                                                'Under Offer',
                                                                                'easy-property-listings'
                                                                        )}
                                                                </span>
                                                        )}
                                                </div>
                                        </div>
                                );

                        case 'suburb':
                                // Make suburb clickable to filter by location
                                if (
                                        listing.locations &&
                                        listing.locations.length > 0 &&
                                        listing.locationSlugs &&
                                        listing.locationSlugs.length > 0
                                ) {
                                        return (
                                                <span className="epl-suburb epl-clickable-filter">
                                                        {listing.locations.map((loc, idx) => (
                                                                <Button
                                                                        key={idx}
                                                                        variant="link"
                                                                        className="epl-filter-link"
                                                                        onClick={() => {
                                                                                if (window.eplSetLocationFilter) {
                                                                                        window.eplSetLocationFilter(
                                                                                                listing.locationSlugs[idx]
                                                                                        );
                                                                                }
                                                                        }}
                                                                >
                                                                        {loc}
                                                                </Button>
                                                        ))}
                                                </span>
                                        );
                                }
                                return (
                                        <span className="epl-suburb">
                                                {listing.suburb || '—'}
                                        </span>
                                );

                        case 'features':
                                if (listing.features && listing.features.length > 0) {
                                        return (
                                                <span className="epl-features">
                                                        {listing.features.slice(0, 3).join(', ')}
                                                        {listing.features.length > 3 &&
                                                                ` +${listing.features.length - 3}`}
                                                </span>
                                        );
                                }
                                return <span className="epl-features">—</span>;

                        case 'listingId':
                                return <span className="epl-listing-id">#{listing.id}</span>;

                        case 'postType':
                                return (
                                        <span className="epl-post-type">
                                                {listing.postTypeLabel}
                                        </span>
                                );

                        case 'propertyStatus':
                                return (
                                        <>
                                                <span
                                                        className={`epl-status-badge epl-status-badge--${getStatusClass(
                                                                listing.propertyStatus
                                                        )}`}
                                                >
                                                        {statusLabels[listing.propertyStatus] ||
                                                                listing.propertyStatus}
                                                </span>
                                                {listing.underOffer && (
                                                        <span className="epl-status-badge epl-status-badge--underoffer">
                                                                {__(
                                                                        'Under Offer',
                                                                        'easy-property-listings'
                                                                )}
                                                        </span>
                                                )}
                                        </>
                                );

                        case 'priceDisplay':
                                return decodeHtmlEntities(listing.priceDisplay) || '—';

                        case 'bedrooms':
                                return (
                                        <span className="epl-bedrooms">
                                                {listing.bedrooms || '—'}
                                        </span>
                                );

                        case 'bathrooms':
                                return (
                                        <span className="epl-bathrooms">
                                                {listing.bathrooms || '—'}
                                        </span>
                                );

                        case 'featured':
                                const isToggling = togglingFeatured.includes(listing.id);
                                return (
                                        <div
                                                className={`epl-featured-cell ${listing.featured ? 'is-featured' : ''
                                                        }`}
                                        >
                                                <button
                                                        type="button"
                                                        className="epl-featured-toggle_icon"
                                                        onClick={() =>
                                                                handleFeaturedToggle(
                                                                        listing.id,
                                                                        listing.featured
                                                                )
                                                        }
                                                        disabled={isToggling}
                                                        title={
                                                                listing.featured
                                                                        ? __(
                                                                                'Remove from featured',
                                                                                'easy-property-listings'
                                                                        )
                                                                        : __(
                                                                                'Mark as featured',
                                                                                'easy-property-listings'
                                                                        )
                                                        }
                                                >
                                                        {isToggling ? (
                                                                <Spinner size={16} />
                                                        ) : (
                                                                <Icon
                                                                        icon={
                                                                                listing.featured
                                                                                        ? starFilled
                                                                                        : starEmpty
                                                                        }
                                                                        size={20}
                                                                />
                                                        )}
                                                </button>
                                        </div>
                                );

                        case 'author':
                                return (
                                        <span className="epl-author">
                                                {listing.author?.name || '—'}
                                        </span>
                                );

                        case 'agent':
                                return (
                                        <span className="epl-agent">{listing.agent || '—'}</span>
                                );

                        case 'date':
                                return (
                                        <span className="epl-date">
                                                {formatDate(listing.date)}
                                        </span>
                                );

                        case 'modified':
                                return (
                                        <span className="epl-date">
                                                {formatDate(listing.modified)}
                                        </span>
                                );

                        default:
                                return '—';
                }
        };

        // Loading state
        if (loading) {
                return (
                        <div className="epl-loading-state">
                                <Spinner />
                                <span>
                                        {labels.loading ||
                                                __('Loading...', 'easy-property-listings')}
                                </span>
                        </div>
                );
        }

        // Empty state
        if (!listings.length) {
                return (
                        <div className="epl-empty-state">
                                <svg
                                        className="epl-empty-state__icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        strokeWidth="1.5"
                                >
                                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                                        <path d="M9 9h6M9 13h6M9 17h4" />
                                </svg>
                                <h3 className="epl-empty-state__title">
                                        {labels.noListings ||
                                                __('No listings found', 'easy-property-listings')}
                                </h3>
                                <p className="epl-empty-state__text">
                                        {__(
                                                'Try adjusting your filters or search terms',
                                                'easy-property-listings'
                                        )}
                                </p>
                        </div>
                );
        }

        return (
                <div className="epl-list-view">
                        <table className="epl-listings-table">
                                <thead>
                                        <tr>
                                                <th className="epl-col-checkbox">
                                                        <CheckboxControl
                                                                checked={allSelected}
                                                                indeterminate={someSelected}
                                                                onChange={handleSelectAll}
                                                                aria-label={__(
                                                                        'Select all',
                                                                        'easy-property-listings'
                                                                )}
                                                        />
                                                </th>
                                                {columns.map((column) => (
                                                        <th
                                                                key={column.key}
                                                                className={`epl-col-${column.key} ${column.sortable ? 'is-sortable' : ''
                                                                        } ${orderby === column.sortKey
                                                                                ? 'is-sorted'
                                                                                : ''
                                                                        }`}
                                                                style={
                                                                        column.width ? { width: column.width } : {}
                                                                }
                                                                onClick={() =>
                                                                        column.sortable && handleSort(column)
                                                                }
                                                        >
                                                                <span className="epl-col-header">
                                                                        {column.label}
                                                                        {column.sortable &&
                                                                                orderby === column.sortKey && (
                                                                                        <Icon
                                                                                                icon={
                                                                                                        order === 'asc'
                                                                                                                ? chevronUp
                                                                                                                : chevronDown
                                                                                                }
                                                                                                size={16}
                                                                                        />
                                                                                )}
                                                                </span>
                                                        </th>
                                                ))}
                                                <th className="epl-col-actions">
                                                        {__('Actions', 'easy-property-listings')}
                                                </th>
                                        </tr>
                                </thead>
                                <tbody>
                                        {listings.map((listing) => (
                                                <tr
                                                        key={listing.id}
                                                        className={
                                                                selectedIds.includes(listing.id)
                                                                        ? 'is-selected'
                                                                        : ''
                                                        }
                                                >
                                                        <td className="epl-col-checkbox">
                                                                <CheckboxControl
                                                                        checked={selectedIds.includes(
                                                                                listing.id
                                                                        )}
                                                                        onChange={(checked) =>
                                                                                handleSelectRow(listing.id, checked)
                                                                        }
                                                                />
                                                        </td>
                                                        {columns.map((column) => (
                                                                <td
                                                                        key={column.key}
                                                                        className={`epl-col-${column.key}`}
                                                                >
                                                                        {renderCellContent(listing, column)}
                                                                </td>
                                                        ))}
                                                        <td className="epl-col-actions">
                                                                <div className="epl-row-actions">
                                                                        <Button
                                                                                icon={pencil}
                                                                                onClick={() =>
                                                                                        setQuickEditListing(listing)
                                                                                }
                                                                                label={__(
                                                                                        'Quick Edit',
                                                                                        'easy-property-listings'
                                                                                )}
                                                                                size="small"
                                                                        />
                                                                        <Button
                                                                                icon={edit}
                                                                                href={listing.editLink}
                                                                                label={
                                                                                        labels.edit ||
                                                                                        __(
                                                                                                'Edit',
                                                                                                'easy-property-listings'
                                                                                        )
                                                                                }
                                                                                size="small"
                                                                        />
                                                                        <Button
                                                                                icon={external}
                                                                                href={listing.viewLink}
                                                                                target="_blank"
                                                                                label={
                                                                                        labels.view ||
                                                                                        __(
                                                                                                'View',
                                                                                                'easy-property-listings'
                                                                                        )
                                                                                }
                                                                                size="small"
                                                                        />
                                                                </div>
                                                        </td>
                                                </tr>
                                        ))}
                                </tbody>
                        </table>

                        {quickEditListing && (
                                <QuickEditModal
                                        listing={quickEditListing}
                                        onClose={() => setQuickEditListing(null)}
                                        onSave={onRefresh}
                                />
                        )}
                </div>
        );
}
