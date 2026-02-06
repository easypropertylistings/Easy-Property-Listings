/**
 * EPL Unified Listings - API Utilities
 *
 * @package EPL
 * @since 4.0.0
 */

import apiFetch from '@wordpress/api-fetch';

// Get config
const getConfig = () => window.eplAdminListings || {};

/**
 * Configure apiFetch with the REST nonce
 */
export function configureApi() {
        const config = getConfig();
        if (config.nonce) {
                apiFetch.use(apiFetch.createNonceMiddleware(config.nonce));
        }
}

// Initialize on load
configureApi();

/**
 * Build query string from params object
 *
 * @param {Object} params Query parameters
 * @return {string} Query string
 */
function buildQueryString(params) {
        const searchParams = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
                // Skip null, undefined, or empty strings
                if (value === undefined || value === null || value === '') {
                        return;
                }

                if (Array.isArray(value)) {
                        // Skip empty arrays
                        if (value.length === 0) {
                                return;
                        }
                        // Join array values with commas for WP REST API compatibility
                        searchParams.append(key, value.join(','));
                } else {
                        searchParams.append(key, value);
                }
        });

        return searchParams.toString();
}

/**
 * Fetch listings from the API
 *
 * @param {Object} params Query parameters
 * @return {Promise<Object>} API response
 */
export async function fetchListings(params = {}) {
        const queryString = buildQueryString({
                page: params.page || 1,
                per_page: params.perPage || 25,
                search: params.search,
                status: params.status,
                author: params.author,
                post_type: params.postType,
                orderby: params.orderby || 'modified',
                order: params.order || 'desc',
                suburb: params.suburb,
                featured: params.featured,
                date_after: params.dateAfter,
                date_before: params.dateBefore,
                price_min: params.price_min,
                price_max: params.price_max,
                rent_min: params.rent_min,
                rent_max: params.rent_max,
                beds_min: params.beds_min,
                baths_min: params.baths_min,
                property_category: params.property_category,
                location: params.location,
                image_size: params.image_size,
        });

        const response = await apiFetch({
                path: `/epl/v1/admin/listings?${queryString}`,
                method: 'GET',
        });

        return response;
}

/**
 * Update a single listing field
 *
 * @param {number} id     Post ID
 * @param {Object} fields Fields to update
 * @return {Promise<Object>} API response
 */
export async function updateListing(id, fields) {
        const response = await apiFetch({
                path: `/epl/v1/admin/listings/${id}`,
                method: 'PATCH',
                data: { fields },
        });

        return response;
}

/**
 * Perform bulk action on listings
 *
 * @param {string}   action Action to perform
 * @param {number[]} ids    Post IDs
 * @param {*}        value  Optional value for the action
 * @return {Promise<Object>} API response
 */
export async function bulkAction(action, ids, value = null) {
        const response = await apiFetch({
                path: '/epl/v1/admin/listings/bulk',
                method: 'POST',
                data: { action, ids, value },
        });

        return response;
}

/**
 * Fetch view settings for current user
 *
 * @return {Promise<Object>} View settings
 */
export async function fetchViewSettings() {
        const response = await apiFetch({
                path: '/epl/v1/admin/view-settings',
                method: 'GET',
        });

        return response;
}

/**
 * Save view settings for current user
 *
 * @param {Object} settings View settings
 * @return {Promise<Object>} API response
 */
export async function saveViewSettings(settings) {
        const response = await apiFetch({
                path: '/epl/v1/admin/view-settings',
                method: 'POST',
                data: { settings },
        });

        return response;
}

/**
 * Fetch schema information
 *
 * @return {Promise<Object>} Schema data
 */
export async function fetchSchema() {
        const response = await apiFetch({
                path: '/epl/v1/admin/listings/schema',
                method: 'GET',
        });

        return response;
}
