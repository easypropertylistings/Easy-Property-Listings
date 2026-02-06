/**
 * EPL Dashboard - Main App Component
 *
 * Premium dashboard with sales charts and year filtering.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { Spinner, Notice } from '@wordpress/components';

import useDashboardData from './hooks/useDashboardData';
import StatCard from './components/StatCard';
import {
        LineChartWidget,
        BarChartWidget,
        buildSalesCountData,
        buildSalesValueData,
        formatCurrency,
} from './components/ChartWidget';
import RecentListingsWidget from './components/RecentListingsWidget';
import OpenInspectionsWidget from './components/OpenInspectionsWidget';
import AgentLeaderboard from './components/AgentLeaderboard';
import PopularListingsWidget from './components/PopularListingsWidget';
import FavoritesWidget from './components/FavoritesWidget';

export default function App() {
        const { data, isLoading, error, year, changeYear } = useDashboardData();

        if (isLoading) {
                return (
                        <div className="epl-dashboard epl-dashboard--loading">
                                <Spinner />
                        </div>
                );
        }

        if (error) {
                return (
                        <div className="epl-dashboard">
                                <Notice status="error" isDismissible={false}>
                                        {error}
                                </Notice>
                        </div>
                );
        }

        if (!data) {
                return null;
        }

        const { stats, salesData, recentListings, recentSold, openInspections, agentStats, availableYears } = data;

        const salesCountData = buildSalesCountData(salesData);
        const salesValueData = buildSalesValueData(salesData);

        // Build year options
        const yearOptions = (availableYears || []).map((y) => ({
                label: y.toString(),
                value: y.toString(),
        }));

        return (
                <div className="epl-dashboard">
                        {/* Stat Cards */}
                        <div className="epl-stats-row">
                                <StatCard
                                        label={__('Total Listings', 'easy-property-listings')}
                                        value={stats?.total || 0}
                                        subtitle="All properties"
                                />
                                <StatCard
                                        label={__('Active', 'easy-property-listings')}
                                        value={stats?.active || 0}
                                        subtitle="On market"
                                        variant="success"
                                />
                                <StatCard
                                        label={__('Sold This Month', 'easy-property-listings')}
                                        value={stats?.soldThisMonth || 0}
                                        subtitle="Closings"
                                        variant="primary"
                                />
                                <StatCard
                                        label={__('Under Offer', 'easy-property-listings')}
                                        value={stats?.underOffer || 0}
                                        subtitle="Pending"
                                        variant="warning"
                                />
                        </div>

                        {/* Sales Charts Row */}
                        <div className="epl-charts-row">
                                {salesValueData && (
                                        <LineChartWidget
                                                title={__('Total Sales', 'easy-property-listings')}
                                                value={formatCurrency(salesData?.totalValue || 0)}
                                                data={salesValueData}
                                                height={180}
                                                yearOptions={yearOptions}
                                                selectedYear={year}
                                                onYearChange={changeYear}
                                        />
                                )}
                                {salesCountData && (
                                        <BarChartWidget
                                                title={__('Number of Sales', 'easy-property-listings')}
                                                value={salesData?.totalCount || 0}
                                                data={salesCountData}
                                                height={180}
                                                yearOptions={yearOptions}
                                                selectedYear={year}
                                                onYearChange={changeYear}
                                        />
                                )}
                        </div>

                        {/* Popular & Favorites */}
                        <div className="epl-two-col">
                                <PopularListingsWidget listings={recentListings} />
                                <FavoritesWidget listings={recentListings} />
                        </div>

                        {/* Recent Listings */}
                        <div className="epl-two-col">
                                <RecentListingsWidget
                                        listings={recentListings}
                                        title={__('Recent Listings', 'easy-property-listings')}
                                />
                                <RecentListingsWidget
                                        listings={recentSold}
                                        title={__('Recent Sales', 'easy-property-listings')}
                                        showSoldPrice
                                />
                        </div>

                        {/* Inspections & Agent */}
                        <div className="epl-two-col">
                                <OpenInspectionsWidget listings={openInspections} />
                                <AgentLeaderboard agents={agentStats} />
                        </div>
                </div>
        );
}
