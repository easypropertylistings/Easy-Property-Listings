/**
 * EPL Dashboard - ChartWidget Component
 *
 * Premium charts with year selector.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useMemo } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import {
        Chart as ChartJS,
        CategoryScale,
        LinearScale,
        BarElement,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        Filler,
} from 'chart.js';
import { Bar, Line } from 'react-chartjs-2';

// Register Chart.js components
ChartJS.register(
        CategoryScale,
        LinearScale,
        BarElement,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        Filler
);

// Shared chart options
const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
                legend: { display: false },
                tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                },
        },
        scales: {
                x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' },
                },
                y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' },
                },
        },
};

// Bar Chart Widget
export function BarChartWidget({ title, value, data, height = 200, yearOptions, selectedYear, onYearChange }) {
        const options = useMemo(() => ({
                ...baseOptions,
                scales: {
                        ...baseOptions.scales,
                        y: {
                                ...baseOptions.scales.y,
                                ticks: { ...baseOptions.scales.y.ticks, stepSize: 1 },
                        },
                },
        }), []);

        return (
                <div className="epl-chart-card">
                        <div className="epl-chart-card__header">
                                <div className="epl-chart-card__info">
                                        <div className="epl-chart-card__label">{title}</div>
                                        <div className="epl-chart-card__value">{value}</div>
                                </div>
                                {yearOptions && yearOptions.length > 1 && (
                                        <div className="epl-chart-card__filter">
                                                <SelectControl
                                                        value={selectedYear?.toString() || ''}
                                                        options={yearOptions}
                                                        onChange={(val) => onYearChange(parseInt(val, 10))}
                                                        __nextHasNoMarginBottom
                                                />
                                        </div>
                                )}
                        </div>
                        <div className="epl-chart-card__body" style={{ height }}>
                                <Bar data={data} options={options} />
                        </div>
                </div>
        );
}

// Line Chart Widget
export function LineChartWidget({ title, value, data, height = 200, yearOptions, selectedYear, onYearChange }) {
        const options = useMemo(() => ({
                ...baseOptions,
                elements: {
                        line: { tension: 0.3 },
                        point: { radius: 0, hoverRadius: 4 },
                },
                scales: {
                        ...baseOptions.scales,
                        y: {
                                ...baseOptions.scales.y,
                                ticks: {
                                        ...baseOptions.scales.y.ticks,
                                        callback: (val) => val >= 1000000 ? (val / 1000000).toFixed(1) + 'M' : val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val,
                                },
                        },
                },
        }), []);

        return (
                <div className="epl-chart-card">
                        <div className="epl-chart-card__header">
                                <div className="epl-chart-card__info">
                                        <div className="epl-chart-card__label">{title}</div>
                                        <div className="epl-chart-card__value">{value}</div>
                                </div>
                                {yearOptions && yearOptions.length > 1 && (
                                        <div className="epl-chart-card__filter">
                                                <SelectControl
                                                        value={selectedYear?.toString() || ''}
                                                        options={yearOptions}
                                                        onChange={(val) => onYearChange(parseInt(val, 10))}
                                                        __nextHasNoMarginBottom
                                                />
                                        </div>
                                )}
                        </div>
                        <div className="epl-chart-card__body" style={{ height }}>
                                <Line data={data} options={options} />
                        </div>
                </div>
        );
}

// Build bar chart data for listings
export function buildMonthlyChartData(monthlyActivity) {
        if (!monthlyActivity) return null;

        return {
                labels: monthlyActivity.map((m) => m.month),
                datasets: [
                        {
                                label: 'Listings',
                                data: monthlyActivity.map((m) => m.listings),
                                backgroundColor: '#3b82f6',
                                borderRadius: 4,
                                maxBarThickness: 28,
                        },
                ],
        };
}

// Build bar chart for sales count
export function buildSalesCountData(salesData) {
        if (!salesData?.months) return null;

        return {
                labels: salesData.months.map((m) => m.month),
                datasets: [
                        {
                                label: 'Sales',
                                data: salesData.months.map((m) => m.count),
                                backgroundColor: '#111827',
                                borderRadius: 4,
                                maxBarThickness: 28,
                        },
                ],
        };
}

// Build line chart for sales value
export function buildSalesValueData(salesData) {
        if (!salesData?.months) return null;

        return {
                labels: salesData.months.map((m) => m.month),
                datasets: [
                        {
                                label: 'Value',
                                data: salesData.months.map((m) => m.value),
                                borderColor: '#111827',
                                backgroundColor: 'rgba(17, 24, 39, 0.05)',
                                borderWidth: 2,
                                fill: true,
                        },
                ],
        };
}

// Format currency
export function formatCurrency(value) {
        if (value >= 1000000) {
                return '$' + (value / 1000000).toFixed(2) + 'M';
        }
        if (value >= 1000) {
                return '$' + (value / 1000).toFixed(0) + 'K';
        }
        return '$' + value.toLocaleString();
}

// Default export for backwards compatibility
export default function ChartWidget({ title, subtitle, data, height = 220 }) {
        return (
                <div className="epl-chart">
                        <div className="epl-chart__header">
                                <h3 className="epl-chart__title">{title}</h3>
                                {subtitle && <span className="epl-chart__subtitle">{subtitle}</span>}
                        </div>
                        <div className="epl-chart__body" style={{ height }}>
                                <Bar data={data} options={baseOptions} />
                        </div>
                </div>
        );
}
