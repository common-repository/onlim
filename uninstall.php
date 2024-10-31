<?php
/**
 * Onlim Uninstall
 *
 * @link https://phlegx.com
 * @package Onlim
 * @version 1.0.0
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

/*
 * Only remove data if WP_ONLIM_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WP_ONLIM_REMOVE_ALL_DATA' ) && true === WP_ONLIM_REMOVE_ALL_DATA ) {
  /* Drop or delete some data here. */
	wp_cache_flush();
}
