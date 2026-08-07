/**
 * EPL Dashboard - RecentListingsWidget Component
 *
 * Listings with pills aligned right on title row.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';

function formatPrice(price) {
        if (!price) return null;
        const num = parseFloat(price);
        if (isNaN(num)) return price;
        return new Intl.NumberFormat('en-AU', {
                style: 'currency',
                currency: 'AUD',
                maximumFractionDigits: 0,
        }).format(num);
}

function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
        });
}

function getStatusColor(status) {
        const colors = {
                current: '#10b981',
                sold: '#ef4444',
                leased: '#f59e0b',
                withdrawn: '#6b7280',
                offmarket: '#8b5cf6',
        };
        return colors[status] || '#6b7280';
}

function getStatusLabel(status) {
        const labels = {
                current: 'Active',
                sold: 'Sold',
                leased: 'Leased',
                withdrawn: 'Withdrawn',
                offmarket: 'Off Market',
        };
        return labels[status] || status;
}

function getTypeLabel(postType) {
        const labels = {
                property: 'Property',
                rental: 'Rental',
                land: 'Land',
                commercial: 'Commercial',
                commercial_land: 'Comm. Land',
                business: 'Business',
                rural: 'Rural',
        };
        return labels[postType] || postType;
}

export default function RecentListingsWidget({ listings, title, showSoldPrice = false }) {
        return (
                <div className="epl-widget">
                        <div className="epl-widget__header">
                                <h3 className="epl-widget__title">{title}</h3>
                        </div>
                        <div className="epl-widget__content">
                                {(!listings || listings.length === 0) ? (
                                        <div className="epl-widget__empty">
                                                {__('No listings to display', 'easy-property-listings')}
                                        </div>
                                ) : (
                                        <div className="epl-listing-list">
                                                {listings.map((listing) => (
                                                        <a
                                                                key={listing.id}
                                                                href={listing.editUrl}
                                                                className="epl-listing-item"
                                                        >
                                                                <div className="epl-listing-item__thumb">
                                                                        {listing.thumbnail ? (
                                                                                <img src={listing.thumbnail} alt="" />
                                                                        ) : (
                                                                                <div className="epl-listing-item__placeholder">
                                                                                        <span className="dashicons dashicons-admin-home"></span>
                                                                                </div>
                                                                        )}
                                                                </div>
                                                                <div className="epl-listing-item__content">
                                                                        <div className="epl-listing-item__row">
                                                                                <div className="epl-listing-item__title">
                                                                                        {listing.address || listing.title || `#${listing.id}`}
                                                                                </div>
                                                                                <div className="epl-listing-item__pills">
                                                                                        <span
                                                                                                className="epl-status-pill"
                                                                                                style={{ backgroundColor: getStatusColor(listing.status) }}
                                                                                        >
                                                                                                {getStatusLabel(listing.status)}
                                                                                        </span>
                                                                                        {listing.postType && (
                                                                                                <span className="epl-type-pill">
                                                                                                        {getTypeLabel(listing.postType)}
                                                                                                </span>
                                                                                        )}
                                                                                </div>
                                                                        </div>
                                                                        <div className="epl-listing-item__row">
                                                                                {showSoldPrice && listing.soldPrice ? (
                                                                                        <span className="epl-listing-item__price epl-listing-item__price--sold">
                                                                                                {formatPrice(listing.soldPrice)}
                                                                                        </span>
                                                                                ) : listing.price ? (
                                                                                        <span className="epl-listing-item__price">
                                                                                                {formatPrice(listing.price)}
                                                                                        </span>
                                                                                ) : (
                                                                                        <span></span>
                                                                                )}
                                                                                <span className="epl-listing-item__date">
                                                                                        {showSoldPrice && listing.soldDate
                                                                                                ? listing.soldDate
                                                                                                : formatDate(listing.date)}
                                                                                </span>
                                                                        </div>
                                                                </div>
                                                        </a>
                                                ))}
                                        </div>
                                )}
                        </div>
                </div>
        );
}
