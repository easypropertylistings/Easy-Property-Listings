/**
 * EPL Dashboard - StatCard Component
 *
 * Clean, minimal stat card.
 *
 * @package EPL
 * @since 4.0.0
 */

export default function StatCard({ label, value, subtitle, variant = 'default' }) {
        return (
                <div className={`epl-stat-card epl-stat-card--${variant}`}>
                        <div className="epl-stat-card__label">{label}</div>
                        <div className="epl-stat-card__value">{value.toLocaleString()}</div>
                        {subtitle && <div className="epl-stat-card__subtitle">{subtitle}</div>}
                </div>
        );
}
