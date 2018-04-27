<?php

if ( ! defined( 'WPINC' ) ) die();

add_action( 'widgets_init', 'lmg_register_widget_lorem_ipsum' );
function lmg_register_widget_lorem_ipsum() {
	register_widget( 'LMG_Widget_Lorem_Ipsum' );
}

class LMG_Widget_Lorem_Ipsum extends WP_Widget {
	private $widget_dir_url  = '';                // str e.g. https://www.example.com/wp-content/plugins/lmg-widgets/widgets/lorem-ipsum/ (with trailing slash)
	private $classes         = array();           // array
	private $images          = array();           // array
	private $all_lines       = array();           // array
	private $num_lines       = 0;                 // int
	private $prev_lines      = array( 0 );        // array
	private $add_flair       = false;             // bool
	public  $use_images      = true;              // bool
	public  $test_overflow   = false;             // bool
	private $element         = '';                // str
	public  $length          = 1;                 // int
	public  $widget_class    = 'lmg_lorem_ipsum'; // str

	/**
	 * Constructs the widget object.
	 *
	 * @return null
	 */
	public function __construct() {
		$this->widget_dir_url  = plugin_dir_url( __FILE__ );
		$this->all_lines       = $this->all_lines();
		$this->num_lines       = count( $this->all_lines );

		$widget_options = array(
			'classname'   => $this->widget_class,
			'description' => 'Displays a random assortment of HTML elements filled with placeholder text.',
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
		$instance[ 'title' ]         = strip_tags( $new_instance[ 'title'    ] );
		$instance[ 'length' ]        = absint(     $new_instance[ 'length'   ] );
		$instance[ 'use_images' ]    = (bool) $new_instance[ 'use_images'    ];
		$instance[ 'test_overflow' ] = (bool) $new_instance[ 'test_overflow' ];
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

		$length = is_int( $instance[ 'length' ] ) ? $instance[ 'length' ] : $this->length;
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'length' ); ?>">Length:</label>
				<input class="tiny-text" type="number" id="<?php echo $this->get_field_id( 'length' ); ?>" name="<?php echo $this->get_field_name( 'length' ); ?>" value="<?php echo $length; ?>" />
			</p>
		<?php

		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'use_images' ); ?>">Use Images:</label>
				<input class="tiny-text" type="checkbox" id="<?php echo $this->get_field_id( 'use_images' ); ?>" name="<?php echo $this->get_field_name( 'use_images' ); ?>" value="true" <?php checked( $instance[ 'use_images' ], true, true ); ?> />
			</p>
		<?php

		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'test_overflow' ); ?>">Test Overflow:</label>
				<input class="tiny-text" type="checkbox" id="<?php echo $this->get_field_id( 'test_overflow' ); ?>" name="<?php echo $this->get_field_name( 'test_overflow' ); ?>" value="true" <?php checked( $instance[ 'test_overflow' ], true, true ); ?> />
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
		$title               = apply_filters( 'widget_title', $instance[ 'title' ] );
		$this->use_images    = $instance[ 'use_images' ];
		$this->test_overflow = $instance[ 'test_overflow' ];
		$class               = sanitize_html_class( $title, $this->widget_class );

		$output = '';
		$output .= $this->css( $class );
		$output .= $args[ 'before_widget' ];
		$output .= ! empty( $title ) ? '<h2>' . $title . '</h2>' : '';

		$length = is_int( $instance[ 'length' ] ) ? $instance[ 'length' ] : $this->length;
		for ( $i = 1; $i <= $length; $i++ ) {
			$output .= $this->get_p();
			$output .= $this->get_h( 3 );
			$output .= $this->get_p();
			$output .= $this->get_ul();
			$output .= $this->get_h( 4 );
			$output .= $this->get_p();
			$output .= $this->get_ol();
			$output .= $this->get_p();
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
		$this->element = 'h';

		$n = 1;
		for ( $i = 0; $i < $n; $i++ ) {
			$h .= $this->get_line();
		}

		$h = '<h' . $l . '>' . $h . '</h' . $l . '>';

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
		$this->element = 'li';
		
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
		$this->element = 'p';
		
		$n = rand( 3, 8 );
		for ( $i = 0; $i < $n; $i++ ) {
			$end = rand( 1, 15 ) === 1 ? '? ' : '. ';
			$p .= $this->get_line() . $end;
		}

		$p = '<p>' . $this->get_img() . rtrim( $p, ' ' ) . '</p>';

		return $p;
	}

	/**
	 * Returns one randomly aligned placeholder image.
	 *
	 * @return str $img
	 */
	private function get_img() {
		if ( ! $this->use_images || 1 !== rand( 1, 4 ) ) return '';

		$images = $this->get_images();

		$file  = $images[ rand( 0, count( $images ) - 1 ) ];
		$sizes = explode( 'x', end( explode( '-', $file ) ) );

		$aligns = array( 'left', 'center', 'right', 'none' );
		$align  = $aligns[ rand( 0, count( $aligns ) - 1 ) ];

		$img = '<img src="' . $this->widget_dir_url . 'images/' . $file . '" width="' . $sizes[ 0 ] . '" height="' . $sizes[ 1 ] . '" alt="" class="align' . $align . '" />';

		return $img;
	}

	/**
	 * Returns an array of images.
	 *
	 * @return array $images
	 */
	private function get_images() {
		if ( ! empty( $this->images ) ) return $this->images;

		$images = glob( __DIR__ . '/images/*.jpg' );
		$dupes  = array();

		foreach ( $images as $k => $v ) {
			$v = end( explode( '/', $v ) );
			$images[ $k ] = $v;

			$dupe = explode( 'x', end( explode( '-', $v ) ) );
			if ( '300' === $dupe[ 0 ] ) {
				$dupes[] = $v;
				$dupes[] = $v;
			}
		}

		$images = array_merge( $images, $dupes );

		return $this->images = $images;
	}

	/**
	 * Returns one line of random placeholder text.
	 *
	 * @param bool $add_flair
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

		$line = $this->add_flair( $this->all_lines[ $n ] );

		$line = $this->test_overflow && 1 === rand( 1, 10 ) ? str_replace( ' ', '', $line ) : $line;

		return $line;
	}

	/**
	 * Randomly adds emphasis to some of the words.
	 *
	 * "People can get a cheeseburger anywhere, okay. They come to Chotchkies for the atmosphere and the attitude."
	 *
	 * @link https://www.youtube.com/watch?v=KJtrLKGZZFg
	 *
	 * @return str $str
	 */
	private function add_flair( $str ) {
		if ( 'h' === $this->element ) return $str; // bail early if element isn't supposed to have flair

		// determines if flair should be added to this string
		if ( ! $this->add_flair ) {
			$this->add_flair = 1 === rand( 1, 9 ) ? true : false;
			return $str;
		}
		$this->add_flair = false;

		// assigns probabilities to tags
		$tags = array(
			10 => 'a href="#"',
			5  => 'strong',
			1  => 'em',
			0  => 'code',
		);

		// determines which tag to use
		$tag2 = '';
		$tag = rand( 0, 12 );
		foreach ( $tags as $k => $v ) {
			if ( $tag >= $k ) {
				$tag = '<' . $v . '>';
				// adds another (nested) tag
				// TODO: make this a recursive function // this is bad code // will never result in <strong><em>
				if ( 1 === rand( 1, 3 ) ) {
					$tag2 = rand( 0, 9 );
					foreach ( $tags as $u => $o ) {
						if ( $o !== $v && $tag2 >= $u ) {
							$tag2 = '<' . $o . '>';
						}
						break;
					}
				}
				break;
			}
		}
		if ( is_int( $tag2 ) ) $tag2 = '';

		$r = explode( ' ', $str );
		$c = count( $r );  // total number of words in this line
		$n = rand( 3, 5 ); // number of words to emphasize

		// determines the position of the tag within the string
		$end = $c - 1;
		$start = 0;
		if ( $n - 1 < $c ) {
			$end   = $end - rand( 0, $c - $n );
			$start = $end - $n + 1;
		}
		
		// places the opening and closing tags
		$r[ $start ] = $tag . $tag2 . $r[ $start ];
		$r[ $end ] = $r[ $end ] . str_replace( '<', '</', $tag2 ) . str_replace( '<', '</', $tag );
		$str = implode( ' ', $r );

		return $str;
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
				
				<?php /* // for testing flair ?>
				.<?php echo $this->widget_class; ?> strong {
					background-color: #f00;
				}

				.<?php echo $this->widget_class; ?> strong em {
					background-color: yellow;
				}
				
				.<?php echo $this->widget_class; ?> em {
					background-color: #0f0;
				}
				
				.<?php echo $this->widget_class; ?> em strong {
					background-color: #111;
					color: #fff;
				}
				
				.<?php echo $this->widget_class; ?> code {
					background-color: #00f;
				}
				<?php */ ?>

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

	/**
	 * Returns an array of placeholder strings.
	 *
	 * Each line should be at least two words long and start with a capital letter.
	 * Don't add any puntuation mark at the end of a line.
	 *
	 * @return array $lines
	 */
	private function all_lines() {
		$lines = array(
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
			"Quod autem antiquatur, et caseum in frusta comminuti",
			"Ubi est bubulae",
			"Usquam populus can adepto a prandium",
			"Ego similis cornu magnum et non mentior",
			"Damnare suus sentit bonum esse perfide",
			"Quae differentia sit inter stultitiam et ingenio ingenio quod habet terminum",
			"Tum quod tesca snowmobile amorem subito flips transiliens super te nudatum supter; nocte venturi glaciali mustelis",
			"Conversusque fecit illud et tentas iterum",
			"Quanta vis videre lepus foramen ingreditur",
			"Se sublimis",
			"Et hoc modo mundus noster",
			"Non est genus non cutem color, sive religionis studium",
			"Et gerere bella, caedes, fallere, usque tentant faciunt mendacium est ad nos, et nos credimus propter quod suus 'bono nostro, quod sumus adhuc scelestos",
			"Ita ego sum criminalibus, curiositas est sceleris",
			"Te potest prohibere ad me, sed potest non prohibere omnium nostrum",
			"Non est fas et nefas, illic 'tantum fun atque odiosis",
			"Non quasi fortes angusta animo non tenerent peius militant pro desperatione causarum",
			"Lepus currere curre, ut foderent puteum cadet, obliviscamur solem",
			"Post mille annos tenebras, et veniet, et induti caeruleo agros cincta est aurum de hominibus nexum restituere de terra, quae esset illico",
			"Dominare unum numero, non possum occidere aliorum, ita quod non postulatis",
			"Praeesset numerus duo, non possum non amare aliorum cum quo alio communicet",
			"Regula ternarius non possum de mortuis hominibus, non satis pictura non placet facere",
			"Omnia simul in te video existit delicata statera",
			"Sicut regem, debes intelligere quia statera, omnes creaturae honorent et reptans de formica ad trepidant rotantes vertice oryx illaqueatus",
			"Est captiosus eorum volant noctuae, uti ligula",
			"In fonte aperta verus, non habes ius control tua fata docebo",
			"Tunc vicis vos luna territi magna super terram, et vide flectere super eam inter crura, et vadit effectus totaliter auferat",
			"Respice item eo dat, quod hic domi id, quod nobis",
			"Ad quod omnes amatis, vos omnes nostis, et omnis umquam audistis, omni homini qui unquam fuit, fuit de animabus suis",
			"Sunt, iiqui eiusdem moderamen nostrum gaudium et adfectae civitati milia sperabo religionibus, ideologias inque economic doctrina, omne venandi et eros, omnis heros et ignave perierunt, omni creator atque delevit civilization, omnis rex, et rusticum se potius et omnes iuvenes copulabis in amore: omnis mater et patre bonae spei puer, et inventor rimor, magister mores omnis, omnis corrumpere politicus, omnis superstar, summum ducem omnis, omnis sanctus in historia nostra et peccator species fuit, ibi super trabem in a pulveris suspensus ab exiguo radio",
			"Terra est valde parva scaena est in amplis opibus mundanis arenam",
			"Supplicia aeterna cogitantur visitavit incolas paene in angulo hoc pixel incolae alio distinguitur angulus ignorationibus eorum quotiens quid cupiant occidere Quam fervens odia",
			"Cogita tot fluminum duces imperatoresque ex sanguinibus ut honore et triumpho magistri fieri possent momentanea fractionem dot",
			"Posturings noster nobis imaginatur ipsum momenti habere aliquem praestantem in phantasia mundi, hoc provocatur pallida lucem",
			"Aciem nostrae telluris sola punctum magna caligine mundi",
			"In obscuro sit, in hac vasta, non est admonitus, ut de auxilio venit ut salvum nos ipsi alibi",
			"Terra sola in mundo notum est, et quantum est suscipere vitae",
			"Nusquam aliud est: in near posterus saltem, quae ad nostram species migrare possent",
			"Visita, quod sic",
			"Consedisset, non tamen",
			"Simile sit necne, quo praesens obstandum terrae",
			"Hoc est quod locutus est haec humili Astronomia aedificationem usus, et ingenii",
			"Forsitan nihil est melius, quam ipsos hanc demonstrationem ex vanitate humana distant nostra minima imagine mundi",
			"Ut me hoc est curam sapien inter se maiorem misericordiam tecum et serva, et foveam, nolentem trahunt, in tantum ut quaecumque in domum nota",
			"Spatio, et ultima fuit",
			"Hi sunt maritimum emporium fuisse de Enterprise starship",
			"Quinque annorum missionem suam: ad explorandum novis novum mundos, ad quaerere novum vitam et novos cultus ingenii, diversi, ut fortiter quo nemo ante iit",
		);
		
		return $lines;
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
		'title'         => '',
		'length'        => $widget->length,
		'use_images'    => $widget->use_images,
		'test_overflow' => $widget->test_overflow,
	), $atts, $widget->widget_class );

	// sanitize user input from shortcode independently of the widget
	$instance[ 'title' ]         = strip_tags( $instance[ 'title'    ] );
	$instance[ 'length' ]        = absint( $instance[ 'length'       ] );
	$instance[ 'use_images' ]    = (bool) $instance[ 'use_images'    ];
	$instance[ 'test_overflow' ] = (bool) $instance[ 'test_overflow' ];

	return $widget->widget( $args, $instance, true );
}

?>
