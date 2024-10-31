<?php
/**
 * Onlim Functions
 *
 * General functions available on both the front-end and admin.
 *
 * @package Onlim
 * @version 1.0.0
 */


defined( 'ABSPATH' ) || exit;


/**
 * Define a constant if it is not already defined.
 *
 * @param string $name  Constant name.
 * @param string $value Value.
 */
function onlim_define( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}


/**
 * Wrapper for onlim_wrong.
 *
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function onlim_wrong( $function, $message, $version ) {
	if ( is_doing_ajax() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
}


/**
 * is_doing_ajax - Returns true when the page is loaded via ajax.
 *
 * @return bool
 */
function is_doing_ajax() {
	return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' );
}
