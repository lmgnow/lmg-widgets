<?php
/**
 * Plugin Name:   LMG Widgets
 * Plugin URI:    https://www.lmgnow.com/
 * Description:   A collection of custom widgets.
 * Version:       1.0.0
 * Author:        Jeremy Kozan
 * Author URI:    https://www.lmgnow.com/
 */

if ( ! defined( 'WPINC' ) ) die();

add_action( 'wp_enqueue_scripts', 'lmg_widgets_enqueue' );
function lmg_widgets_enqueue() {
	wp_enqueue_style(  'lmg-widgets', plugin_dir_url( __FILE__ ) . 'style.css', array(), '1.0.0', 'screen' );
}

require_once 'widgets/term-masonry/term-masonry.php';

?>
