<?php
/**
 * Frontend handling.
 *
 * @package Onlim
 */


defined( 'ABSPATH' ) || exit;


class Onlim_Frontend {

  /**
	 * @var Onlim_Frontend
	 */
	protected static $instance;

  /**
	 * @var array options
	 */
  protected $options;

  /**
	 * Singleton pattern: Onlim_Frontend::instance().
	 *
	 * @return Onlim_Frontend
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

  /**
   * Constructor of Onlim_Frontend.
   */
  protected function __construct() {
    $this->options = get_option( 'onlim_general_settings' );

    if ( isset( $this->options['onlim_settings_active'] ) &&
         isset( $this->options['onlim_settings_inclusion'] ) &&
         $this->options['onlim_settings_inclusion'] != 'shortcode' ) {

      /* Add widget below <body>. */
      if ( ONLIM_BODY_OPEN && $this->options['onlim_settings_inclusion'] == 'header' ) {
        add_action( 'wp_body_open', array( $this, 'onlim_header' ), 1 );

      /* Add widget above </body>. */
      } elseif ( $this->options['onlim_settings_inclusion'] == 'footer' ) {
        add_action( 'wp_footer', array( $this, 'onlim_footer' ), 999 );
      }
    }
  }

  public function onlim_header() {
    $this->onlim_widget_output();
  }

  public function onlim_footer() {
    $this->onlim_widget_output();
  }

  private function onlim_widget_output() {
    /* Ignore feed, robots or trackbacks. */
    if ( is_feed() || is_robots() || is_trackback() ) return;

    /* Provide the opportunity to ignore Onlim widget output. */
		if ( apply_filters( 'onlim_disable_widget', false ) ) return;

    /* Ignore ouput if widget code setting is not set. */
    if ( !isset( $this->options['onlim_settings_widget_code'] ) ) return;

    /* Ignore ouput if widget code setting is empty. */
    if ( empty( $this->options['onlim_settings_widget_code'] ) ) return;

    /* Ignore output if widget code setting has no string length. */
    if ( trim( $this->options['onlim_settings_widget_code'] ) == '' ) return;

    echo $this->options['onlim_settings_widget_code'];
  }
}

Onlim_Frontend::instance();
