<?php
/**
 * Onlim Admin Functions
 *
 * @package  Onlim/Admin
 * @version  1.0.0
 */


defined( 'ABSPATH' ) || exit;


/**
 * Get all Onlim screen ids.
 *
 * @return array
 */
function onlim_screen_ids() {
	$screen_ids = array(
		'toplevel_page_onlim',
		'onlim_page_onlim_settings'
	);

	return apply_filters( 'onlim_screen_ids', $screen_ids );
}

/**
 *
 *
 * @return string
 */
function onlim_external_url( $endpoint = 'app' ) {
	$a = 'ff8800';
	switch ($endpoint) {
		case 'app':
			return 'https://app.onlim.com/#/?a=' . $a . '&o=wp-onlim&r=' . get_site_url();
			break;
		case 'app-login':
			return 'https://app.onlim.com/#/signin?a=' . $a . '&o=wp-onlim&r=' . get_site_url();
			break;
		case 'app-register':
			return 'https://app.onlim.com/#/register?a=' . $a . '&o=wp-onlim&r=' . get_site_url();
			break;
		case 'web':
			return 'https://onlim.com/?a=' . $a . '&o=wp-onlim&r=' . get_site_url();
			break;
	}
}
