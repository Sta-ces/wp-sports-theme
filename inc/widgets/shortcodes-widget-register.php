<?php
/**
 * Shortcodes widget
 * A simple shortcodes widget for your sidebars.
 *
 * @package         shortcodes-widget
 * @author          MyPreview (Github: @mahdiyazdani, @mypreview)
 * @since           1.2.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Registering Shortcodes Widget - Class
 */
class Shortcodes_Widget_Register extends WP_Widget {

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
			'shortcodes_widget',
			apply_filters( 'shortcodes_widget_name', _x( 'Shortcodes', 'widget name', 'shortcodes-widget' ) ),
			array(
				'description'                 => _x( 'Display a simple shortcodes widget in your sidebar areas.', 'widget description', 'shortcodes-widget' ),
				'customize_selective_refresh' => true,
			)
		);

		$this->defaults = array(
			'id'   	=> '',
			'attr'	=> ''
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
		$get_id       = isset( $instance['id'] ) ? $instance['id'] : $this->defaults['id'];
		$get_attr     = isset( $instance['attr'] ) ? $instance['attr'] : $this->defaults['attr'];

		$output = sprintf( '[%s]', trim($get_id . " " .$get_attr) );
		echo apply_filters( 'the_content', $output );
	}

	/**
	 * Widget Settings.
	 *
	 * @since   1.2.1
	 * @param   mixed $instance   Instance.
	 * @return  void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>
		<p>
			<select id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" class="widefat" style="width:100%;">
				<?php foreach(get_shortcodes() as $key => $value): ?>
					<option <?php selected( $instance['id'], $key ); ?> value="<?php echo $key; ?>"><?php echo $key; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'attr' ) ); ?>">
			<?php echo esc_html_x( 'Attributes:', 'field label', 'shortcodes-widget' ); ?>
			</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'attr' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'attr' ) ); ?>"
				value="<?php echo esc_attr( $instance['attr'] ); ?>"
			/>
		</p>
		<?php
	}

	/**
	 * Update Widget Settings.
	 *
	 * @since   1.2.1
	 * @param   mixed $new_instance   New Instance.
	 * @param   mixed $old_instance   Old Instance.
	 * @return  Instance.
	 */
	public function update( $new_instance, $old_instance ){
		$instance                     = array();
		$instance['id']               = sanitize_text_field( $new_instance['id'] );
		$instance['attr']            = sanitize_text_field( $new_instance['attr'] );

		return $instance;
	}

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 *
	 * @since   1.2.1
	 * @param   string $input   String to convert.
	 * @return  bool
	 */
	public static function string_to_bool( $input ) {
		return is_bool( $input ) ? $input : ( 'yes' === $input || 1 === $input || 'true' === $input || 'TRUE' === $input || '1' === $input || 'on' === $input );
	}

}