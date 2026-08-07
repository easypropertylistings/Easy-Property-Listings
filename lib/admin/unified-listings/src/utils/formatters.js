/**
 * EPL Unified Listings - Formatters
 *
 * Utility functions for formatting data.
 *
 * @package EPL
 * @since 4.0.0
 */

import { dateI18n, getSettings } from '@wordpress/date';

// Get config
const getConfig = () => window.eplAdminListings || {};

/**
 * Format currency amount
 *
 * @param {number|string} amount   Amount to format
 * @param {boolean}       showZero Whether to show $0 or empty
 * @return {string} Formatted amount
 */
export function formatCurrency( amount, showZero = false ) {
	const config = getConfig();
	const { symbol = '$', position = 'before' } = config.currency || {};

	const numAmount = parseFloat( amount );

	if ( isNaN( numAmount ) || ( numAmount === 0 && ! showZero ) ) {
		return '';
	}

	const formatted = numAmount.toLocaleString( undefined, {
		minimumFractionDigits: 0,
		maximumFractionDigits: 0,
	} );

	return position === 'before'
		? `${ symbol }${ formatted }`
		: `${ formatted }${ symbol }`;
}

/**
 * Format date for display
 *
 * @param {string} dateString ISO date string
 * @param {string} format     Date format (default: site setting)
 * @return {string} Formatted date
 */
export function formatDate( dateString, format = null ) {
	if ( ! dateString ) {
		return '';
	}

	try {
		const dateFormat = format || getSettings().formats.date;
		return dateI18n( dateFormat, dateString );
	} catch ( e ) {
		// Fallback to simple format
		const date = new Date( dateString );
		return date.toLocaleDateString();
	}
}

/**
 * Format date and time for display
 *
 * @param {string} dateString ISO date string
 * @return {string} Formatted date and time
 */
export function formatDateTime( dateString ) {
	if ( ! dateString ) {
		return '';
	}

	try {
		const settings = getSettings();
		const format = `${ settings.formats.date } ${ settings.formats.time }`;
		return dateI18n( format, dateString );
	} catch ( e ) {
		const date = new Date( dateString );
		return date.toLocaleString();
	}
}

/**
 * Format relative time (e.g., "2 hours ago")
 *
 * @param {string} dateString ISO date string
 * @return {string} Relative time string
 */
export function formatRelativeTime( dateString ) {
	if ( ! dateString ) {
		return '';
	}

	const date = new Date( dateString );
	const now = new Date();
	const diffMs = now - date;
	const diffDays = Math.floor( diffMs / ( 1000 * 60 * 60 * 24 ) );

	if ( diffDays === 0 ) {
		const diffHours = Math.floor( diffMs / ( 1000 * 60 * 60 ) );
		if ( diffHours === 0 ) {
			const diffMinutes = Math.floor( diffMs / ( 1000 * 60 ) );
			if ( diffMinutes < 1 ) {
				return 'Just now';
			}
			return `${ diffMinutes }m ago`;
		}
		return `${ diffHours }h ago`;
	}

	if ( diffDays === 1 ) {
		return 'Yesterday';
	}

	if ( diffDays < 7 ) {
		return `${ diffDays } days ago`;
	}

	return formatDate( dateString );
}

/**
 * Truncate text with ellipsis
 *
 * @param {string} text      Text to truncate
 * @param {number} maxLength Maximum length
 * @return {string} Truncated text
 */
export function truncateText( text, maxLength = 50 ) {
	if ( ! text || text.length <= maxLength ) {
		return text || '';
	}

	return text.substring( 0, maxLength ).trim() + '...';
}

/**
 * Get status color class
 *
 * @param {string} status Property status
 * @return {string} CSS class name
 */
export function getStatusColorClass( status ) {
	const statusColors = {
		current: 'status-current',
		sold: 'status-sold',
		leased: 'status-leased',
		withdrawn: 'status-withdrawn',
		offmarket: 'status-offmarket',
		deleted: 'status-deleted',
	};

	return statusColors[ status ] || 'status-default';
}

/**
 * Get status label from config
 *
 * @param {string} status Property status
 * @return {string} Status label
 */
export function getStatusLabel( status ) {
	const config = getConfig();
	const labels = config.statusLabels || {};

	return labels[ status ] || status;
}

/**
 * Get post type label from config
 *
 * @param {string} postType Post type slug
 * @return {string} Post type label
 */
export function getPostTypeLabel( postType ) {
	const config = getConfig();
	const labels = config.postTypeLabels || {};

	return labels[ postType ] || postType;
}
