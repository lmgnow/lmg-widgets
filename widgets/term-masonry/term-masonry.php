<?php

if ( ! defined( 'WPINC' ) ) die();

add_action( 'widgets_init', 'lmg_register_widget_term_masonry' );
function lmg_register_widget_term_masonry() {
	register_widget( 'LMG_Widget_Term_Masonry' );
}

class LMG_Widget_Term_Masonry extends WP_Widget {
	private $widget_dir_url = '';                 // str e.g. https://www.example.com/wp-content/plugins/lmg-widgets/widgets/term-masonry/ (with trailing slash)
	private $classes        = array();            // array
	public  $widget_class   = 'lmg_term_masonry'; // str
	public  $type_default   = 'post';             // str
	public  $tax_default    = 'category';         // str
	public  $gutter_default = 3;                  // int

	/**
	 * Constructs the widget object.
	 *
	 * @return null
	 */
	public function __construct() {
		$this->widget_dir_url = plugin_dir_url( __FILE__ );

		$widget_options = array(
			'classname'   => $this->widget_class,
			'description' => 'Displays a masonry of terms for the selected taxonomy.'
		);
		parent::__construct( $this->widget_class, 'LMG Term Masonry', $widget_options );
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
		$instance[ 'post_type' ] = esc_attr(   $new_instance[ 'post_type' ] );
		$instance[ 'taxonomy' ]  = esc_attr(   $new_instance[ 'taxonomy' ]  );
		$instance[ 'gutter' ]    = absint(     $new_instance[ 'gutter' ]    );
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

		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_type' ); ?>">Post Type:</label>
				<select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
					<?php
						$types = $this->post_types();
						foreach ( $types as $type ) {
							?><option value="<?php echo $type[ 'name' ]; ?>" <?php selected( $instance[ 'post_type' ], $type[ 'name' ], 1 ); ?>><?php echo $type[ 'label' ]; ?></option><?php
						}
					?>
				</select>
			</p>
		<?php

		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>">Taxonomy:</label>
				<select id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
					<?php
						$taxes = $this->taxonomies();
						foreach ( $taxes as $tax ) {
							?><option value="<?php echo $tax[ 'name' ]; ?>" <?php selected( $instance[ 'taxonomy' ], $tax[ 'name' ], 1 ); ?>><?php echo $tax[ 'label' ]; ?></option><?php
						}
					?>
				</select>
			</p>
		<?php

		$gutter = is_int( $instance[ 'gutter' ] ) ? $instance[ 'gutter' ] : $this->gutter_default;
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'gutter' ); ?>">Gutter (%):</label>
				<input class="tiny-text" type="number" id="<?php echo $this->get_field_id( 'gutter' ); ?>" name="<?php echo $this->get_field_name( 'gutter' ); ?>" value="<?php echo $gutter; ?>" />
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
		$terms = get_terms( array(
			'taxonomy' => $instance[ 'taxonomy' ],
		) );

		if ( empty( $terms ) && ! $is_shortcode ) return _e( 'Nothing Found.', 'lmg-widgets' );
		if ( empty( $terms ) )                 return __( 'Nothing Found.', 'lmg-widgets' );

		$title  = apply_filters( 'widget_title', $instance[ 'title' ] );
		$gutter = is_int( $instance[ 'gutter' ] ) ? $instance[ 'gutter' ] : $this->gutter_default;
		$class  = 'g' . $gutter;

		$output = '';
		$output .= $this->css( $class, $gutter );
		$output .= $args[ 'before_widget' ];
		$output .= ! empty( $title ) ? $args[ 'before_title' ] . $title . $args[ 'after_title' ] : '';
		$output .= '<div class="row odd ' . $class . '">';

		foreach ( $terms as $n => $term ) {
			$params = array(
				'post_type'      => $instance[ 'post_type' ],
				'posts_per_page' => 1,
				'tax_query'      => array(
					array(
						'taxonomy' => $instance[ 'taxonomy' ],
						'field'    => 'term_id',
						'terms'    => array( $term->term_id ),
					),
				),
				'meta_query'     => array(
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					),
				),
			);
			$query = new WP_Query( $params );

			$thumbnail_url = $this->widget_dir_url . 'images/placeholder.png';
			while ( $query->have_posts() ) {
				$query->the_post();
				$thumbnail_url = get_the_post_thumbnail_url( $post, 'large' );
			}
			
			if ( 0 === $n % 10 && 0 !== $n ) {
				$output .= '</div><div class="row odd ' . $class . '">';
			} else if ( 0 === $n % 5 && 0 !== $n ) {
				$output .= '</div><div class="row even ' . $class . '">';
			}

			$output .= '<div class="tile" style="background-image: url(\'' . $thumbnail_url . '\');">';
			$output .= '<a href="' . get_term_link( $term, $instance[ 'taxonomy' ] ) . '"><span>#' . $term->name . '</span></a>';
			$output .= '</div>';
		}
		wp_reset_query();

		$output .= '</div>';
		$output .= $args[ 'after_widget' ];

		if ( $is_shortcode ) return $output;
		echo $output;
	}

	/**
	 * Returns an array of usable post types.
	 *
	 * @return array $types
	 */
	public function post_types() {
		$args = array(
			'public'             => true,
			'publicly_queryable' => true,
		);
		$types = get_post_types( $args, 'objects' );
		unset( $types[ 'attachment' ] );

		return $this->convert_to_arrays( $types );
	}

	/**
	 * Returns an array of usable taxonomies.
	 *
	 * @return array $taxes
	 */
	public function taxonomies() {
		$args = array(
			'public'             => true,
			'publicly_queryable' => true,
		);
		$taxes = get_taxonomies( $args, 'objects' );
		unset( $taxes[ 'post_format' ] );

		return $this->convert_to_arrays( $taxes );
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
	 * @param int $gutter
	 *
	 * @return $output
	 */
	private function css( $class, $gutter ) {
		if ( in_array( $class, $this->classes ) ) return;
		$this->classes[] = $class;

		$class = ! empty( $class ) ? '.' . $class : '';
		ob_start();
		?>
			<style>
				.<?php echo $this->widget_class; ?> .tile > a {
					display: block;
					text-align: center;
					width: 100%;
					height: 100%;
					background-color: rgba(0,0,0,0.4);
					text-transform: uppercase;
					font-weight: bold;
					text-decoration: none;
					font-size: 1.2em;
				}

				.<?php echo $this->widget_class; ?> .tile > a > span {
					position: relative;
					top: 45%;
				}

				.<?php echo $this->widget_class; ?> <?php echo $class; ?> .tile {
					background-size: cover;
					background-position: center center;
					background-repeat: no-repeat;
					width: <?php echo 28 - $gutter / 2; ?>%;
					margin-right: <?php echo $gutter; ?>%;
					height: 200px;
					float: left;
				}

				.<?php echo $this->widget_class; ?> .row {
					margin-left: 0;
					margin-right: 0;
				}

				.<?php echo $this->widget_class; ?> .row.odd<?php echo $class; ?> .tile:first-child {
					width: <?php echo 44 - $gutter; ?>%;
					margin-right: <?php echo $gutter; ?>%;
					height: <?php echo 400 + $gutter * 10; ?>px;
				}

				.<?php echo $this->widget_class; ?> .row.odd .tile:nth-child(3),
				.<?php echo $this->widget_class; ?> .row.odd .tile:nth-child(5) {
					margin-right: 0;
				}

				.<?php echo $this->widget_class; ?> .row.even .tile:nth-child(3) {
					clear: left;
				}

				.<?php echo $this->widget_class; ?> .row.even<?php echo $class; ?> .tile:nth-child(5) {
					width: <?php echo 44 - $gutter; ?>%;
					margin-right: 0;
					height: <?php echo 400 + $gutter * 10; ?>px;
					float: right;
					margin-top: -<?php echo 200 + $gutter * 10; ?>px;
				}

				@media only screen and (max-width: 768px) {
					div.widget_<?php echo $this->widget_class; ?>.<?php echo $this->widget_class; ?> div.row<?php echo $class; ?> > div.tile:nth-child(n) {
						float: none;
						margin-bottom: <?php echo $gutter * 10; ?>px;
						width: 100%;
						height: 300px;
						margin-top: 0;
					}

					div.<?php echo $this->widget_class; ?> div.row<?php echo $class; ?>::after {
							margin-bottom: 0;
					}
				}

				.<?php echo $this->widget_class; ?> .row.odd<?php echo $class; ?>  .tile:nth-child(2),
				.<?php echo $this->widget_class; ?> .row.odd<?php echo $class; ?>  .tile:nth-child(3),
				.<?php echo $this->widget_class; ?> .row.even<?php echo $class; ?> .tile:nth-child(1),
				.<?php echo $this->widget_class; ?> .row.even<?php echo $class; ?> .tile:nth-child(2) {
					margin-bottom: <?php echo $gutter * 10; ?>px;
				}

				.<?php echo $this->widget_class; ?> .row<?php echo $class; ?>::after {
					content: '';
					display: table;
					clear: both;
					margin-bottom: <?php echo $gutter * 10; ?>px;
				}
			</style>
		<?php

		$output = ob_get_clean();
		$output = str_replace( array( "\r", "\n", "\t" ), '', $output );
		return $output;
	}

}

add_shortcode( 'lmg_term_masonry', 'lmg_term_masonry' );
function lmg_term_masonry( $atts ) {
	$widget = new LMG_Widget_Term_Masonry();

	$args = array(
		'before_widget' => '<div class="widget_' . $widget->widget_class . ' ' . $widget->widget_class . '">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	);

	$instance = shortcode_atts( array(
		'title'     => '',
		'post_type' => $widget->type_default,
		'taxonomy'  => $widget->tax_default,
		'gutter'    => $widget->gutter_default,
	), $atts, $widget->widget_class );

	$instance[ 'title' ]     = esc_attr( $instance[ 'title' ] );
	$instance[ 'post_type' ] = in_array( $instance[ 'post_type' ], array_column( $widget->post_types(), 'name' ) ) ? $instance[ 'post_type' ] : $widget->type_default;
	$instance[ 'taxonomy' ]  = in_array( $instance[ 'taxonomy' ],  array_column( $widget->taxonomies(), 'name' ) ) ? $instance[ 'taxonomy' ]  : $widget->tax_default;
	$instance[ 'gutter' ]    = absint( $instance[ 'gutter' ] );

	return $widget->widget( $args, $instance, true );
}

?>
