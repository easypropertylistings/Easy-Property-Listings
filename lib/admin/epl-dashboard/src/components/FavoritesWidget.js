/**
 * EPL Dashboard - FavoritesWidget Component
 *
 * Clean design without red hearts.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';

// Generate dummy data for favorite listings
function generateDummyFavoriteListings(realListings = []) {
        return realListings.slice(0, 5).map((listing) => ({
                ...listing,
                favorites: Math.floor(Math.random() * 200) + 20,
                todayFavorites: Math.floor(Math.random() * 10) + 1,
        }));
}

export default function FavoritesWidget({ listings }) {
        const favoriteListings = useMemo(() => {
                return generateDummyFavoriteListings(listings || []);
        }, [listings]);

        return (
                <div className="epl-widget epl-favorites-widget">
                        <div className="epl-widget__header">
                                <h3 className="epl-widget__title">
                                        <span className="dashicons dashicons-star-filled"></span>
                                        {__('Most Favorited', 'easy-property-listings')}
                                </h3>
                        </div>
                        <div className="epl-widget__content">
                                {favoriteListings.length === 0 ? (
                                        <div className="epl-widget__empty">
                                                {__('No favorites yet', 'easy-property-listings')}
                                        </div>
                                ) : (
                                        <div className="epl-favorites-list">
                                                {favoriteListings.map((listing, idx) => (
                                                        <a
                                                                key={listing.id}
                                                                href={listing.editUrl}
                                                                className="epl-favorites-item"
                                                        >
                                                                <div className="epl-favorites-item__rank">
                                                                        {idx + 1}
                                                                </div>
                                                                <div className="epl-favorites-item__thumb">
                                                                        {listing.thumbnail ? (
                                                                                <img src={listing.thumbnail} alt="" />
                                                                        ) : (
                                                                                <div className="epl-favorites-item__placeholder">
                                                                                        <span className="dashicons dashicons-admin-home"></span>
                                                                                </div>
                                                                        )}
                                                                </div>
                                                                <div className="epl-favorites-item__info">
                                                                        <div className="epl-favorites-item__title">
                                                                                {listing.address || listing.title || `#${listing.id}`}
                                                                        </div>
                                                                        <div className="epl-favorites-item__meta">
                                                                                <span className="epl-favorites-item__count">
                                                                                        {listing.favorites} saves
                                                                                </span>
                                                                                <span className="epl-favorites-item__today">
                                                                                        +{listing.todayFavorites} today
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
