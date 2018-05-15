<?php
/**
 * Plugin Name:   LMG Widgets
 * Plugin URI:    https://github.com/lmgnow/lmg-widgets/
 * Description:   A collection of custom widgets.
 * Version:       0.1.0
 * Author:        Jeremy Kozan
 * Author URI:    https://www.lmgnow.com/
 */

if ( ! defined( 'WPINC' ) ) die();

//require_once __DIR__ . '/includes/pallazzio-wpghu/pallazzio-wpghu.php';
//new Pallazzio_WPGHU( __FILE__, 'lmgnow' );

add_action( 'wp_enqueue_scripts', 'lmg_widgets_enqueue' );
function lmg_widgets_enqueue() {
	//wp_enqueue_style(  'lmg-widgets', plugin_dir_url( __FILE__ ) . 'style.css',  array(),           '1.0.0', 'screen' );

	//wp_enqueue_script( 'lmg-widgets', plugin_dir_url( __FILE__ ) . 'script.css', array( 'jquery' ), '1.0.0', true     );
}

require_once 'widgets/lorem-ipsum/lorem-ipsum.php';
require_once 'widgets/term-masonry/term-masonry.php';

?>
