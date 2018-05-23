<?php
/**
 * Plugin Name:   LMG Widgets
 * Plugin URI:    https://github.com/lmgnow/lmg-widgets/
 * Description:   A collection of custom widgets.
 * Version:       1.1.3
 * Author:        Jeremy Kozan
 * Author URI:    https://www.lmgnow.com/
 */

if ( ! defined( 'WPINC' ) ) die();

$lmgw = new LMG_Widgets();
class LMG_Widgets {
	private $plugin_file_path = ''; // str Absolute path to this file.      (with trailing slash)
	private $plugin_dir_path  = ''; // str Absolute path to this directory. (with trailing slash)

	/**
	 * Constructs object.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->plugin_file_path = __FILE__;
		$this->plugin_dir_path  = plugin_dir_path( $this->plugin_file_path );

		require_once $this->plugin_dir_path . 'includes/pallazzio-wpghu/pallazzio-wpghu.php';
		new Pallazzio_WPGHU( $this->plugin_dir_path . wp_basename( $this->plugin_file_path ), 'lmgnow' );

		require_once $plugin_dir_path . 'widgets/lorem-ipsum/lorem-ipsum.php';
		require_once $plugin_dir_path . 'widgets/term-masonry/term-masonry.php';

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueues styles and scripts on front end.
	 *
	 * @return void
	 */
	public function enqueue() {
		//wp_enqueue_style(  'lmg-widgets', plugin_dir_url( __FILE__ ) . 'style.css',  array(),           '1.0.0', 'screen' );
		//wp_enqueue_script( 'lmg-widgets', plugin_dir_url( __FILE__ ) . 'script.css', array( 'jquery' ), '1.0.0', true     );
	}

}

?>
