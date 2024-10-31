<?php
/**
 * Installation related functions and actions.
 *
 * @package Onlim
 * @version 1.0.0
 */


defined( 'ABSPATH' ) || exit;


/**
 * Onlim_Install Class.
 */
class Onlim_Install {


	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
	}


	/**
	 * Check Onlim version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'onlim_version' ), ONLIM()->version, '<' ) ) {
			self::install();
			do_action( 'onlim_updated' );
		}
	}


	/**
	 * Install Onlim.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		if ( 'yes' === get_transient( 'onlim_installing' ) ) {
			return;
		}

		set_transient( 'onlim_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		onlim_define( 'ONLIM_INSTALLING', true );

		self::create_options();
		self::create_tables();
		self::create_roles();
		self::setup_environment();
		self::create_terms();
		self::update_version();

		delete_transient( 'onlim_installing' );

		do_action( 'onlim_flush_rewrite_rules' );
		do_action( 'onlim_installed' );
	}


	/**
	 * Setup Onlim environment - post types, taxonomies, endpoints.
	 */
	private static function setup_environment() {
		/* Not required for the moment. */
	}


	/**
	 * Is this a brand new Onlim install?
	 *
	 * @return boolean
	 */
	private static function is_new_install() {
		return is_null( get_option( 'onlim_version', null ) ) && is_null( get_option( 'onlim_db_version', null ) );
	}


	/**
	 * Update Onlim version to current.
	 */
	private static function update_version() {
		delete_option( 'onlim_version' );
		add_option( 'onlim_version', ONLIM()->version );
	}


	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	private static function create_options() {
		/* Not required for the moment. */
	}


	/**
	 * Add the default terms for Onlim taxonomies.
	 */
	public static function create_terms() {
		/* Not required for the moment. */
	}


	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *      onlim_[TABLE-NAME] - Description of table
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_schema() );
	}


	/**
	 * Get Table schema.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = ""; /*"
CREATE TABLE {$wpdb->prefix}onlim_api_keys (
  key_id BIGINT UNSIGNED NOT NULL auto_increment
) $collate;
		";*/

		return $tables;
	}


	/**
	 * Return a list of Onlim tables. Used to make sure all Onlim tables
	 * are dropped when uninstalling the plugin in a single site or multi site environment.
	 *
	 * @return array Onlim tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			//"{$wpdb->prefix}onlim_api_keys",
		);

		return $tables;
	}


	/**
	 * Drop Onlim tables.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}


	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @param  array $tables List of tables that will be deleted by WP.
	 * @return string[]
	 */
	public static function wpmu_drop_tables( $tables ) {
		return array_merge( $tables, self::get_tables() );
	}


	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		_x( 'Onlim manager', 'User role', 'onlim' );

		add_role(
			'onlim_manager',
			'Onlim manager',
			array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_users'             => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true,
			)
		);
		$capabilities = self::get_core_capabilities();
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'onlim_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}


	/**
	 * Get capabilities for Onlim - these are assigned to admin/property manager during installation or reset.
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_onlim'
		);

		/* Single objects (singular) */
		/* $capability_types = array( 'onlim_resources' );
		foreach ( $capability_types as $capability_type ) {
			$capabilities[ $capability_type ] = array(
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				// Taxonomies
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			);
		}*/

		/* List objects (plural) */
		/*$capability_types = array( 'onlim_resources' );
		foreach ( $capability_types as $capability_type ) {
			$capabilities[ $capability_type ] = array(
				"edit_{$capability_type}",
				"edit_others_{$capability_type}",
				"publish_{$capability_type}",
				"read_private_{$capability_type}",
				"delete_{$capability_type}",
				"delete_private_{$capability_type}",
				"delete_published_{$capability_type}",
				"delete_others_{$capability_type}",
				"edit_private_{$capability_type}",
				"edit_published_{$capability_type}",
			);
		}*/

		return $capabilities;
	}


	/**
	 * Remove Onlim roles.
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'onlim_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}
		remove_role( 'onlim_manager' );
	}
}


Onlim_Install::init();
