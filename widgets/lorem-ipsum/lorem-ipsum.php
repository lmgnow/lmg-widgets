<?php

if ( ! defined( 'WPINC' ) ) die();

add_action( 'widgets_init', 'lmg_register_widget_lorem_ipsum' );
function lmg_register_widget_lorem_ipsum() {
	register_widget( 'LMG_Widget_Lorem_Ipsum' );
}

class LMG_Widget_Lorem_Ipsum extends WP_Widget {
	private $widget_dir_url = '';                 // str e.g. https://www.example.com/wp-content/plugins/lmg-widgets/widgets/lorem-ipsum/ (with trailing slash)
	private $classes        = array();            // array
	private $all_lines      = array();            // array
	private $num_lines      = 0;                  // int
	private $prev_lines     = array( 0 );         // array
	public  $widget_class   = 'lmg_lorem_ipsum';  // str

	/**
	 * Constructs the widget object.
	 *
	 * @return null
	 */
	public function __construct() {
		$this->widget_dir_url = plugin_dir_url( __FILE__ );
		$this->all_lines      = $this->all_lines();
		$this->num_lines      = count( $this->all_lines );

		$widget_options = array(
			'classname'   => $this->widget_class,
			'description' => 'Displays a random assortment of HTML elements filled with placeholder text.'
		);
		parent::__construct( $this->widget_class, 'LMG Lorem Ipsum', $widget_options );
	}

	/**
	 * Applies settings to the widget instance.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array $instance
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ]     = strip_tags( $new_instance[ 'title' ]     );
		return $instance;
	}

	/**
	 * Creates the admin area widget settings form.
	 *
	 * @param array $instance
	 *
	 * @return null
	 */
	public function form( $instance ) {
		$title = ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>
		<?php
	}

	/**
	 * Creates the widget output.
	 *
	 * @param array $args
	 * @param array $instance
	 * @param bool  $is_shortcode
	 *
	 * @return null
	 */
	public function widget( $args, $instance, $is_shortcode = false ) {
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );
		$class = sanitize_html_class( $title, $this->widget_class );

		$output = '';
		$output .= $this->css( $class );
		$output .= $args[ 'before_widget' ];
		$output .= ! empty( $title ) ? $args[ 'before_title' ] . $title . $args[ 'after_title' ] : '';

		for ( $i = 0; $i < 999; $i++ ) {
			$output .= $this->get_h( rand( 1, 6 ) );
		}

		$output .= $args[ 'after_widget' ];

		if ( $is_shortcode ) return $output;
		echo $output;
	}

	/**
	 * Returns one heading of random placeholder text.
	 *
	 * @param int $l
	 *
	 * @return str $h
	 */
	private function get_h( $l ) {
		$n = rand( 1, 2 );

		for ( $i = 0; $i < $n; $i++ ) {
			$h .= $this->get_line() . '. ';
		}

		$h = '<h' . $l . '>' . rtrim( $h, ' ' ) . '</h' . $l . '>';

		return $h;
	}

	/**
	 * Returns one ordered list of random placeholder text.
	 *
	 * @return str $ol
	 */
	private function get_ol() {
		$n = rand( 3, 7 );

		for ( $i = 0; $i < $n; $i++ ) {
			$ol .= $this->get_li();
		}

		$ol = '<ol>' . $ol . '</ol>';

		return $ol;
	}

	/**
	 * Returns one unordered list of random placeholder text.
	 *
	 * @return str $ul
	 */
	private function get_ul() {
		$n = rand( 3, 7 );

		for ( $i = 0; $i < $n; $i++ ) {
			$ul .= $this->get_li();
		}

		$ul = '<ul>' . $ul . '</ul>';

		return $ul;
	}

	/**
	 * Returns one list item of random placeholder text.
	 *
	 * @return str $li
	 */
	private function get_li() {
		$n = rand( 1, 3 );

		for ( $i = 0; $i < $n; $i++ ) {
			$li .= $this->get_line() . '. ';
		}

		$li = '<li>' . rtrim( $li, ' ' ) . '</li>';

		return $li;
	}

	/**
	 * Returns one paragraph of random placeholder text.
	 *
	 * @return str $p
	 */
	private function get_p() {
		$n = rand( 3, 8 );

		for ( $i = 0; $i < $n; $i++ ) {
			$p .= $this->get_line() . '. ';
		}

		$p = '<p>' . rtrim( $p, ' ' ) . '</p>';

		return $p;
	}

	/**
	 * Returns one line of random placeholder text.
	 *
	 * @return str $line
	 */
	private function get_line() {
		$n = 0;
		while ( in_array( $n, $this->prev_lines ) ) {
			$n = rand( 0, $this->num_lines - 1 );
		}
		
		$this->prev_lines[] = $n;
		if ( 10 < count( $this->prev_lines ) ) {
			array_shift( $this->prev_lines );
		}

		$line = $this->all_lines[ $n ];
		return $line;
	}

	/**
	 * Returns an array of placeholder strings.
	 *
	 * @return array $lines
	 */
	private function all_lines() {
		$lines = array(
			'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas',
			'Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante',
			'Donec eu libero sit amet quam egestas semper',
			'Aenean ultricies mi vitae est',
			'Mauris placerat eleifend leo',
			'Quisque sit amet est et sapien ullamcorper pharetra',
			'Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi',
			'Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui',
			'Donec non enim in turpis pulvinar facilisis',
			'Ut felis',
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			'Vivamus magna',
			'Cras in mi at felis aliquet congue',
			'Ut a est eget ligula molestie gravida',
			'Curabitur massa',
			'Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam',
			'Vivamus pretium ornare est',
			'Aliquam tincidunt mauris eu risus',
		);
		
		return $lines;
	}

	/**
	 * Converts an array of objects to an array of arrays.
	 *
	 * @return array $taxes
	 */
	private function convert_to_arrays( $array ) {
		foreach ( $array as $k => $v ) {
			$array[ $k ] = (array) $v;
		}

		return $array;
	}

	/**
	 * Returns css.
	 *
	 * @param str $class
	 *
	 * @return $output
	 */
	private function css( $class ) {
		if ( in_array( $class, $this->classes ) ) return;
		$this->classes[] = $class;

		$class = ! empty( $class ) ? '.' . $class : '';
		ob_start();
		?>
			<style>
				.<?php echo $this->widget_class; ?> div {
					display: block;
				}

				@media only screen and (max-width: 768px) {
					div.widget_<?php echo $this->widget_class; ?>.<?php echo $this->widget_class; ?> div {
						display: block;
					}
				}

				.<?php echo $this->widget_class; ?> .row<?php echo $class; ?>::after {
					content: '';
					display: table;
					clear: both;
				}
			</style>
		<?php

		$output = ob_get_clean();
		$output = str_replace( array( "\r", "\n", "\t" ), '', $output );
		return $output;
	}

}

add_shortcode( 'lmg_lorem_ipsum', 'lmg_lorem_ipsum' );
function lmg_lorem_ipsum( $atts ) {
	$widget = new LMG_Widget_Lorem_Ipsum();

	$args = array(
		'before_widget' => '<div class="widget_' . $widget->widget_class . ' ' . $widget->widget_class . '">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	);

	$instance = shortcode_atts( array(
		'title'     => '',
	), $atts, $widget->widget_class );

	$instance[ 'title' ]     = esc_attr( $instance[ 'title' ] );

	return $widget->widget( $args, $instance, true );
}

?>
