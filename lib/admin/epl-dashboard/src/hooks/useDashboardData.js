/**
 * EPL Dashboard - Custom hook for fetching dashboard data
 *
 * Supports year parameter for timeframe filtering.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useEffect, useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

export default function useDashboardData() {
        const [data, setData] = useState(null);
        const [isLoading, setIsLoading] = useState(true);
        const [error, setError] = useState(null);
        const [year, setYear] = useState(null);

        const fetchData = useCallback(async (selectedYear = null) => {
                setIsLoading(true);
                setError(null);

                try {
                        const params = selectedYear ? `?year=${selectedYear}` : '';
                        const result = await apiFetch({
                                path: `/epl/v1/admin/dashboard${params}`,
                        });
                        setData(result);
                        if (!selectedYear && result.currentYear) {
                                setYear(result.currentYear);
                        }
                } catch (err) {
                        console.error('Dashboard fetch error:', err);
                        setError(err.message || 'Failed to load dashboard data');
                } finally {
                        setIsLoading(false);
                }
        }, []);

        useEffect(() => {
                fetchData();
        }, [fetchData]);

        const changeYear = useCallback((newYear) => {
                setYear(newYear);
                fetchData(newYear);
        }, [fetchData]);

        return { data, isLoading, error, year, changeYear, refetch: fetchData };
}
