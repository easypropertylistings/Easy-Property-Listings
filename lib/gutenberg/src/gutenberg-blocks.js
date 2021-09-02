/**
 * EPL Block Functions
 * 
 * This is a collection of common functions used within the EPL blocks
 * 
 * @since 3.5.0
 * @package EPL
 */

/**
 * Will retrive meta information about the post being edited. For now
 * this is only loaded on post edit screen for Gutenberg. So no checks 
 * are made to ensure that a post is being edited. 
 * @param string token Token to return from meta array. If not provided will array is returned. 
 */
 export function epl_get_post_edit_meta( token ) {	
	if ( ( typeof token !== 'undefined') && (token != '') ) {
		if (typeof epl_settings['meta']['post'][token] !== 'undefined') {
			return epl_settings['meta']['post'][token];
		}
	} else {
		if (typeof epl_settings['meta']['post'] !== 'undefined') {
			return epl_settings['meta']['post'];
		}
	}
}

/**
 * Returns EPL Settings
 * @param string token Token to return from meta array. If not provided will array is returned. 
 */
export function epl_get_setting(token, default_value) {
	if ( ( typeof token !== 'undefined' ) && ( token != '' ) && ( typeof epl_settings['settings'][token] !== 'undefined' ) ) {
		var token_value = epl_settings['settings'][token];
		return epl_settings['settings'][token];
	} 
	return default_value;
}

/**
 * Returns EPL Post Types
 * @param string token Token to return from meta array. If not provided will array is returned. 
 */
 export function epl_get_post_types() {
	
	return epl_settings['post_types'];
}

/**
 * Returns EPL Settings
 * @param string token Token to return from meta array. If not provided will array is returned. 
 */
 export function epl_get_search_attributes() {

	return epl_settings['search_attributes'];
}