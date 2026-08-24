/**
 * EPL Dashboard - OpenInspectionsWidget Component
 *
 * Black uniform theme with date pills.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useState, useMemo } from '@wordpress/element';

function parseInspectionDates(inspectionTimes) {
        if (!inspectionTimes) return [];

        const parts = inspectionTimes.split(/\s+(?=\d{2}-[A-Za-z]{3}-\d{4})/);
        return parts.filter(Boolean).map((part) => {
                const match = part.match(/^(\d{2}-[A-Za-z]{3}-\d{4})\s*(.*)$/);
                if (match) {
                        return { date: match[1], time: match[2] || '' };
                }
                return { date: '', time: part };
        });
}

function getAllDatesWithListings(listings) {
        const dateMap = {};

        listings.forEach((listing) => {
                const dates = parseInspectionDates(listing.inspectionTimes);
                dates.forEach(({ date, time }) => {
                        if (date) {
                                if (!dateMap[date]) {
                                        dateMap[date] = [];
                                }
                                dateMap[date].push({
                                        ...listing,
                                        time,
                                });
                        }
                });
        });

        const sortedDates = Object.keys(dateMap).sort((a, b) => {
                try {
                        return new Date(a) - new Date(b);
                } catch {
                        return 0;
                }
        });

        return { dateMap, sortedDates };
}

export default function OpenInspectionsWidget({ listings }) {
        const { dateMap, sortedDates } = useMemo(() => {
                if (!listings || listings.length === 0) {
                        return { dateMap: {}, sortedDates: [] };
                }
                return getAllDatesWithListings(listings);
        }, [listings]);

        const [selectedDate, setSelectedDate] = useState(sortedDates[0] || null);

        const filteredListings = selectedDate ? (dateMap[selectedDate] || []) : [];

        return (
                <div className="epl-widget epl-inspections-widget">
                        <div className="epl-widget__header">
                                <h3 className="epl-widget__title">
                                        <span className="dashicons dashicons-calendar-alt"></span>
                                        {__('Open Inspections', 'easy-property-listings')}
                                </h3>
                        </div>
                        <div className="epl-widget__content">
                                {sortedDates.length === 0 ? (
                                        <div className="epl-widget__empty">
                                                {__('No upcoming inspections', 'easy-property-listings')}
                                        </div>
                                ) : (
                                        <div className="epl-inspections-content">
                                                {/* Date Pills */}
                                                <div className="epl-date-pills">
                                                        {sortedDates.map((date) => (
                                                                <button
                                                                        key={date}
                                                                        type="button"
                                                                        className={`epl-date-pill ${selectedDate === date ? 'epl-date-pill--active' : ''}`}
                                                                        onClick={() => setSelectedDate(date)}
                                                                >
                                                                        {date}
                                                                </button>
                                                        ))}
                                                </div>

                                                {/* Listings for selected date */}
                                                <div className="epl-inspection-listings">
                                                        {filteredListings.map((item, idx) => (
                                                                <a
                                                                        key={`${item.id}-${idx}`}
                                                                        href={item.editUrl}
                                                                        className="epl-inspection-row"
                                                                >
                                                                        <div className="epl-inspection-row__info">
                                                                                <div className="epl-inspection-row__title">
                                                                                        {item.address || item.title || `#${item.id}`}
                                                                                </div>
                                                                                {item.time && (
                                                                                        <div className="epl-inspection-row__time">
                                                                                                <span className="dashicons dashicons-clock"></span>
                                                                                                {item.time}
                                                                                        </div>
                                                                                )}
                                                                        </div>
                                                                        <span className="dashicons dashicons-arrow-right-alt2"></span>
                                                                </a>
                                                        ))}
                                                </div>
                                        </div>
                                )}
                        </div>
                </div>
        );
}
