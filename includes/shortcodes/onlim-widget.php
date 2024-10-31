<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: onlim-widget
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts ['el_class'] string Extra class name
 */

$options = get_option( 'onlim_general_settings' );

if ( isset( $options['onlim_settings_active'] ) &&
     isset( $options['onlim_settings_inclusion'] ) &&
     $options['onlim_settings_inclusion'] == 'shortcode' ) {
  $classes = '';
  if ( ! empty( $atts['el_class'] ) ) {
    $classes .= $atts['el_class'];
  }
  $classes = trim( apply_filters( 'onlim_widget_classes', $classes ) );
  $classes = empty( $classes ) ? 'onlim-widget' : 'onlim-widget ' . $classes;

  $output  = '<div class="' . $classes . '">';
  $output .= $options['onlim_settings_widget_code'];
  $output .= "</div>\n";
  echo $output;
}
