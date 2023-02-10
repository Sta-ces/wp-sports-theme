<?php
/**
 * INFOTH widget
 * A simple widget to select informations for your sidebars.
 *
 * @package         infoth-widget
 * @since           1.2.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Registering INFOTH Widget - Class
 */
class INFOTH_Widget_Register extends WP_Widget {

	/**
	 * Defaults
	 *
	 * @since   1.2.1
	 * @var     defaults
	 */
	private $defaults;

	/**
	 * Setup class.
	 *
	 * @since   1.2.1
	 * @return  void
	 */
	public function __construct() {
		parent::__construct(
			'infoth_widget',
			apply_filters( 'infoth_widget_name', _x( 'Theme informations', 'widget name', 'infoth-widget' ) ),
			array(
				'description'                 => _x( 'Display a simple selecter widget in your sidebar areas.', 'widget description', 'infoth-widget' ),
				'customize_selective_refresh' => true,
			)
		);

		$this->defaults = array(
			'option'	=> 'date',
			'classes'		=> ''
		);
	}

	/**
	 * Widget Front End.
	 *
	 * @since   1.2.1
	 * @param   mixed $args       Arguments.
	 * @param   mixed $instance   Instance.
	 * @return  void
	 */
	public function widget( $args, $instance ) {
		$instance     = wp_parse_args( (array) $instance, $this->defaults );
		$get_option	  = isset( $instance['option'] ) ? $instance['option'] : $this->defaults['option'];
		$get_class	  = isset( $instance['classes'] ) ? $instance['classes'] : $this->defaults['classes'];
		$output = "<p class='".str_replace(" ", "", strtolower($get_option))." ".$get_class."' style='margin-bottom:7.5px;'>".get_infoth($get_option, true)."</p>";
		echo apply_filters( 'infoth_widget_output', $output );
	}

	/**
	 * Widget Settings.
	 *
	 * @since   1.2.1
	 * @param   mixed $instance   Instance.
	 * @return  void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$all_options = table_infoth();
		?>
		<div class="widget_infoth">
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'option' ) ); ?>">
					<?php echo esc_html_x( 'Information', 'field label', 'infoth-widget' ); ?>
				</label>
				<select name='<?php echo esc_attr( $this->get_field_name( 'option' ) ); ?>' id="<?php echo esc_attr( $this->get_field_id( 'option' ) ); ?>">
				<?php foreach($all_options as $opt): ?>
						<option value='<?php echo esc_attr( $opt ); ?>' <?php selected( $instance['option'], $opt ); ?>>
							<?php $infoth_info = wp_trim_words(get_infoth($opt, true),6); ?>
							<?php echo trim(apply_filters('the_content', $infoth_info)." [".$opt."]"); ?>
						</option>
				<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'classes' )); ?>">
					<?php echo esc_html_x( "Class", "field label", "infoth-widget" ); ?>
				</label>
				<input type="text" placeholder="Class" value="<?php echo $instance['classes']; ?>" name='<?php echo esc_attr( $this->get_field_name( 'classes' ) ); ?>' id="<?php echo esc_attr( $this->get_field_id( 'classes' ) ); ?>">
			</p>
		</div>
		<?php
		$this->widget_stylesheet();
	}

	/**
	 * Update Widget Settings.
	 *
	 * @since   1.2.1
	 * @param   mixed $new_instance   New Instance.
	 * @param   mixed $old_instance   Old Instance.
	 * @return  Instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance				= array();
		$instance['option']		= sanitize_text_field( $new_instance['option'] );
		$instance['classes']	= sanitize_text_field( $new_instance['classes'] );
		return $instance;
	}

	/**
	 * Stylesheet
	 * 
	 * @since	1.2.1
	 */

	private function widget_stylesheet(){
		?>
		<style>
			.widget_infoth label,
			.widget_infoth input,
			.widget_infoth select{ display: block; width: 100%; }
			.widget_infoth label{ margin-bottom: 5px; }
			.widget_infoth > p{ margin-bottom: 20px; }
		</style>
		<?php
	} 

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 *
	 * @since   1.2.1
	 * @param   string $input   String to convert.
	 * @return  bool
	 */
	public static function string_to_bool( $input ) {
		return is_bool( $input ) ? $input : ( 'yes' === $input || 1 === $input || 'true' === $input || 'TRUE' === $input || '1' === $input );
	}

}