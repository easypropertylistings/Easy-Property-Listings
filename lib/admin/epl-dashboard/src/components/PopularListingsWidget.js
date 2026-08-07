/**
 * EPL Dashboard - PopularListingsWidget Component
 *
 * Slim horizontal layout with sparkline on right.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';

// Generate sparkline data
function generateSparkline(totalViews) {
        const bars = 16;
        const data = [];
        const baseHeight = Math.max(10, Math.min(40, totalViews / 50));

        for (let i = 0; i < bars; i++) {
                const variance = Math.random() * 0.6 + 0.4;
                data.push(Math.round(baseHeight * variance));
        }
        return data;
}

// Generate dummy data for popular listings
function generateDummyPopularListings(realListings = []) {
        return realListings.slice(0, 5).map((listing) => ({
                ...listing,
                views: Math.floor(Math.random() * 3000) + 500,
                todayViews: Math.floor(Math.random() * 50) + 5,
                sparkline: generateSparkline(Math.floor(Math.random() * 3000) + 500),
        }));
}

function Sparkline({ data }) {
        const maxHeight = Math.max(...data);

        return (
                <div className="epl-sparkline">
                        {data.map((height, idx) => (
                                <div
                                        key={idx}
                                        className="epl-sparkline__bar"
                                        style={{ height: `${(height / maxHeight) * 100}%` }}
                                />
                        ))}
                </div>
        );
}

export default function PopularListingsWidget({ listings }) {
        const popularListings = useMemo(() => {
                return generateDummyPopularListings(listings || []);
        }, [listings]);

        return (
                <div className="epl-widget epl-popular-widget">
                        <div className="epl-widget__header">
                                <h3 className="epl-widget__title">
                                        <span className="dashicons dashicons-chart-bar"></span>
                                        {__('Popular Listings', 'easy-property-listings')}
                                </h3>
                        </div>
                        <div className="epl-widget__content">
                                {popularListings.length === 0 ? (
                                        <div className="epl-widget__empty">
                                                {__('No listings available', 'easy-property-listings')}
                                        </div>
                                ) : (
                                        <div className="epl-popular-list">
                                                {popularListings.map((listing) => (
                                                        <a
                                                                key={listing.id}
                                                                href={listing.editUrl}
                                                                className="epl-popular-item"
                                                        >
                                                                <div className="epl-popular-item__info">
                                                                        <div className="epl-popular-item__title">
                                                                                {listing.address || listing.title || `#${listing.id}`}
                                                                        </div>
                                                                        <div className="epl-popular-item__stats">
                                                                                <span className="dashicons dashicons-visibility"></span>
                                                                                <span className="epl-popular-item__views">
                                                                                        {listing.views.toLocaleString()}
                                                                                </span>
                                                                                <span className="epl-popular-item__today">
                                                                                        +{listing.todayViews} today
                                                                                </span>
                                                                        </div>
                                                                </div>
                                                                <Sparkline data={listing.sparkline} />
                                                                <span className="dashicons dashicons-arrow-right-alt2"></span>
                                                        </a>
                                                ))}
                                        </div>
                                )}
                        </div>
                </div>
        );
}
