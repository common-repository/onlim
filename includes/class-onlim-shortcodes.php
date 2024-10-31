<?php
/**
 * Shortcodes registration and dispatch.
 *
 * @package Onlim
 */


defined( 'ABSPATH' ) || exit;


class Onlim_Shortcodes {

  /**
	 * @var Onlim_Shortcodes
	 */
	protected static $instance;

  /**
	 * @var array shortcodes
	 */
  protected $shortcodes;

  /**
	 * Singleton pattern: Onlim_Shortcodes::instance()->[SHORTCODE-METHOD].
	 *
	 * @return Onlim_Shortcodes
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

  /**
	 * Constructor of Onlim_Shortcodes.
	 */
	protected function __construct() {
		$this->shortcodes = array(
      /**
    	 * Registering new shortcodes.
    	 */
  		'onlim-widget' => array(
    		'atts' => array(
  				'el_class' => ''
    		)
    	)
    );

    add_action( 'init', array( $this, 'init' ), 20 );
  }

  /**
   * Initialize Onlim_Shortcodes.
   */
  public function init() {
    /**
     * Adding new shortcodes.
     */
    foreach ( $this->shortcodes as $shortcode => $shortcode_params ) {
      if ( shortcode_exists( $shortcode ) ) {
        remove_shortcode( $shortcode );
      }
      add_shortcode( $shortcode, array( $this, str_replace( '-', '_', $shortcode ) ) );
    }
  }

  /**
	 * Handling shortcodes
	 *
	 * @param string $shortcode Shortcode name
	 * @param array  $args
	 *
	 * @return string Generated shortcode output
	 *
	 */
	public function __call( $shortcode, $args ) {
		$_output = '';
    $shortcode = str_replace( '_', '-', $shortcode );
		if ( ! isset( $this->shortcodes[$shortcode] ) ) {
			return $_output;
		}

		/* Preparing params for shortcodes (can be used inside of the input). */
		$atts = isset( $args[0] ) ? $args[0] : array();
		$content = isset( $args[1] ) ? $args[1] : '';

		$atts = shortcode_atts( $this->shortcodes[$shortcode]['atts'], $atts, $shortcode );

		/* Call the shortcode itself and render. */
		$_filename = ONLIM_ABSPATH . 'includes/shortcodes/' . $shortcode . '.php';
		ob_start();
		require( $_filename );
		$_output .= ob_get_clean();

		return $_output;
	}

  /**
   * Get Onlim_Shortcodes config.
   */
  public function config() {
    return $this->shortcodes;
  }
}

Onlim_Shortcodes::instance();
