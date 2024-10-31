<?php
/**
 * Onlim setup
 *
 * @package Onlim
 */


defined( 'ABSPATH' ) || exit;


/**
 * Onlim class
 */
final class Onlim {
  /**
	 * Onlim version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

  /**
	 * The single instance of the class.
	 *
	 * @var Onlim
	 */
	protected static $_instance = null;

  /**
   * Cloning is forbidden.
   */
  public function __clone() {
    onlim_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'onlim' ), '1.0.0' );
  }

  /**
   * Unserializing instances of this class is forbidden.
   */
  public function __wakeup() {
    onlim_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'onlim' ), '1.0.0' );
  }

  /**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', ONLIM_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( ONLIM_PLUGIN_FILE ) );
	}

  /**
	 * Main Onlim instance.
	 *
	 * Ensures only one instance of Onlim is loaded or can be loaded.
	 *
	 * @static
	 * @return Onlim - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

  /**
	 * Constructor
	 */
	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'onlim_loaded' );
	}

  /**
	 * Define Onlim constants.
	 */
	private function define_constants() {
		onlim_define( 'ONLIM_VERSION', $this->version );
		onlim_define( 'ONLIM_ABSPATH', dirname( ONLIM_PLUGIN_FILE ) . '/' );
		onlim_define( 'ONLIM_BODY_OPEN', function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2' , '>=' ) );
	}


  /**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
    /**
     * Core classes.
     */
    include_once( ONLIM_ABSPATH . 'includes/onlim-functions.php' );
    include_once( ONLIM_ABSPATH . 'includes/class-onlim-install.php' );

    if ( $this->is_request( 'admin' ) ) {
      $this->admin_includes();
    }

    if ( $this->is_request( 'frontend' ) ) {
      $this->frontend_includes();
    }
  }

  /**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook( ONLIM_PLUGIN_FILE, array( 'Onlim_Install', 'install' ) );
		register_deactivation_hook( ONLIM_PLUGIN_FILE, array( $this, 'deactivate' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

  /**
	 * Init Onlim when WordPress Initialises.
	 */
	public function init() {
		do_action( 'before_onlim_init' );

		$this->load_plugin_textdomain();

		do_action( 'onlim_init' );
	}

  /**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/onlim/onlim-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/onlim-LOCALE.mo
	 */
	private function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'onlim' );

		unload_textdomain( 'onlim' );
		load_textdomain( 'onlim', WP_LANG_DIR . '/onlim/onlim-' . $locale . '.mo' );
		load_plugin_textdomain( 'onlim', false, plugin_basename( dirname( ONLIM_PLUGIN_FILE ) ) . '/languages' );
	}

  /**
   * Cleanup on plugin deactivation.
   */
  private function deactivate() {}

  /**
	 * Include admin.
	 */
	public function admin_includes() {
		include_once( ONLIM_ABSPATH . 'includes/admin/class-onlim-admin.php' );
	}

	/**
	 * Include frontend.
	 */
	public function frontend_includes() {
		include_once( ONLIM_ABSPATH . 'includes/class-onlim-shortcodes.php' );
		include_once( ONLIM_ABSPATH . 'includes/class-onlim-frontend.php' );
	}

  /**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}
