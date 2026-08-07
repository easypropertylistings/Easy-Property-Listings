/**
 * EPL Unified Listings - useListings Hook
 *
 * Custom hook for fetching and managing listings data.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useEffect, useCallback, useRef } from '@wordpress/element';
import { fetchListings } from '../utils/api';

/**
 * useListings Hook
 *
 * @param {Object} filters Filter parameters
 * @return {Object} Listings data and state
 */
export function useListings(filters) {
        const [listings, setListings] = useState([]);
        const [total, setTotal] = useState(0);
        const [totalPages, setTotalPages] = useState(0);
        const [postTypeCounts, setPostTypeCounts] = useState({});
        const [isLoading, setIsLoading] = useState(true);
        const [error, setError] = useState(null);

        // Create a serialized version of filters for dependency comparison
        const filtersJson = JSON.stringify(filters);

        // Fetch function
        const doFetch = useCallback(async () => {
                setIsLoading(true);
                setError(null);

                try {
                        const response = await fetchListings(filters);
                        setListings(response.listings || []);
                        setTotal(response.total || 0);
                        setTotalPages(response.totalPages || 0);
                        setPostTypeCounts(response.postTypeCounts || {});
                } catch (err) {
                        console.error('Error fetching listings:', err);
                        setError(err.message || 'Failed to load listings');
                        setListings([]);
                        setTotal(0);
                        setTotalPages(0);
                } finally {
                        setIsLoading(false);
                }
                // eslint-disable-next-line react-hooks/exhaustive-deps
        }, [filtersJson]);

        // Effect to fetch when filters change
        useEffect(() => {
                doFetch();
        }, [doFetch]);

        // Manual refetch function
        const refetch = useCallback(() => {
                doFetch();
        }, [doFetch]);

        return {
                listings,
                total,
                totalPages,
                postTypeCounts,
                isLoading,
                error,
                refetch,
        };
}

export default useListings;
