<?php
/**
 * Plugin Name: Onlim
 * Plugin URI: http://wordpress.org/plugins/onlim
 * Description: Onlim Livechat and Chatbot plugin. Awesome and free Livechat in Wordpress for everyone.
 * Version: 1.0.0
 * Author: Onlim
 * Author URI: https://onlim.com/?a=ff8800&o=wordpress.org
 * Tested up to: 5.5
 * Requires at least: 3.8
 * Text Domain: onlim
 * Domain Path: /languages
 * Copyright: © 2020 Phlegx Systems OG
 * License: GPL-2.0+
 * License URI: hhttp://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @link https://phlegx.com
 * @package Onlim
 * @version 1.0.0
*/

defined( 'ABSPATH' ) || exit;


/* Handy debugger helper. */
if ( ! function_exists( 'opr' ) ) {
	function opr( $msg ) {
		error_log( print_r( $msg, true ) );
	}
}


/**
 * Global base functions.
 */
include_once( dirname( __FILE__ ) . '/includes/onlim-functions.php' );


/**
 * Define constants.
 */
onlim_define( 'ONLIM_PLUGIN_FILE', __FILE__ );


/**
 * Load Onlim Core class.
 */
if ( ! class_exists( 'Onlim' ) ) {
	include_once( dirname( __FILE__ ) . '/includes/class-onlim.php' );
}


/**
 * Main instance of Onlim.
 *
 * Returns the main instance of Onlim to prevent the need to use globals.
 *
 * @return Onlim
 */
function onlim() {
	return Onlim::instance();
}


$GLOBALS['onlim'] = onlim();
