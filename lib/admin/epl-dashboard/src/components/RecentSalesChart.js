/**
 * EPL Dashboard - RecentSalesChart Component
 *
 * Compact sales widget for charts row.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';

function formatPrice(price) {
        if (!price) return null;
        const num = parseFloat(price);
        if (isNaN(num)) return price;
        if (num >= 1000000) {
                return '$' + (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
                return '$' + (num / 1000).toFixed(0) + 'K';
        }
        return '$' + num.toLocaleString();
}

export default function RecentSalesChart({ listings, year }) {
        // Filter to max 5 items
        const recentSales = (listings || []).slice(0, 5);

        return (
                <div className="epl-chart-card epl-recent-sales-card">
                        <div className="epl-chart-card__header">
                                <div className="epl-chart-card__info">
                                        <div className="epl-chart-card__label">
                                                {__('Recent Sales', 'easy-property-listings')}
                                        </div>
                                        <div className="epl-chart-card__value">{recentSales.length}</div>
                                </div>
                        </div>
                        <div className="epl-chart-card__body epl-recent-sales-list">
                                {recentSales.length === 0 ? (
                                        <div className="epl-recent-sales-empty">
                                                {__('No recent sales', 'easy-property-listings')}
                                        </div>
                                ) : (
                                        recentSales.map((listing) => (
                                                <a
                                                        key={listing.id}
                                                        href={listing.editUrl}
                                                        className="epl-recent-sale-item"
                                                >
                                                        <div className="epl-recent-sale-item__info">
                                                                <div className="epl-recent-sale-item__title">
                                                                        {listing.address || listing.title || `#${listing.id}`}
                                                                </div>
                                                                {listing.soldPrice && (
                                                                        <div className="epl-recent-sale-item__price">
                                                                                {formatPrice(listing.soldPrice)}
                                                                        </div>
                                                                )}
                                                        </div>
                                                        <span className="dashicons dashicons-arrow-right-alt2"></span>
                                                </a>
                                        ))
                                )}
                        </div>
                </div>
        );
}
