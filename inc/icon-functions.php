<?php
/**
 * SVG icons related functions and filters
 */

function stth_include_svg_icons() {
	$svg_icons = get_theme_path( '/assets/svg-icons.svg' );
	if ( file_exists( $svg_icons ) ) require_once $svg_icons;
}
add_action( 'wp_footer', 'stth_include_svg_icons', 9999 );

/**
 * Return SVG markup.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 *     @type string $icon  Required SVG icon filename.
 *     @type string $title Optional SVG title.
 *     @type string $desc  Optional SVG description.
 * }
 * @return string SVG markup.
 */
function stth_get_svg( $args = array() ) {
	if ( empty( $args ) ) return _st( 'Please define default parameters in the form of an array.' );
	if ( false === array_key_exists( 'icon', $args ) ) return _st( 'Please define an SVG icon filename.' );
	$defaults = array(
		'icon'     => '',
		'title'    => '',
		'desc'     => '',
		'fallback' => false,
	);
	$args = wp_parse_args( $args, $defaults );
	$aria_hidden = ' aria-hidden="true"';
	$aria_labelledby = '';
	if ( $args['title'] ) {
		$aria_hidden     = '';
		$unique_id       = stth_unique_id();
		$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';
		if ( $args['desc'] ) $aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
	}
	if( strpos(strtolower($args['icon']), 'mail') !== false ) $args['icon'] = 'envelope-o';
	$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';
	if ( $args['title'] ) {
		$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';
		if ( $args['desc'] ) $svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
	}
	$svg .= ' <use href="#icon-' . esc_html( $args['icon'] ) . '" xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use> ';
	if ( $args['fallback'] ) $svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
	$svg .= '</svg>';
	return $svg;
}

/**
 * Display SVG icons in social links menu.
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of the menu. Used for padding.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 * @return string The menu item output with social icon.
 */
function stth_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	$social_icons = stth_social_links_icons();
	if ( 'social' === $args->theme_location ) {
		foreach ( $social_icons as $attr => $value ) {
			echo_r($value);
			if ( false !== strpos( $item_output, $attr ) ) $item_output = str_replace( $args->link_after, '</span>' . stth_get_svg( array( 'icon' => esc_attr( $value ) ) ), $item_output );
		}
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'stth_nav_menu_social_icons', 10, 4 );

/**
 * Add dropdown icon if menu item has children.
 *
 * @param string   $title The menu item's title.
 * @param WP_Post  $item  The current menu item.
 * @param stdClass $args  An object of wp_nav_menu() arguments.
 * @param int      $depth Depth of menu item. Used for padding.
 * @return string The menu item's title with dropdown icon.
 */
function stth_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
	if ( 'top' === $args->theme_location ) {
		foreach ( $item->classes as $value ) {
			if ( 'menu-item-has-children' === $value || 'page_item_has_children' === $value )
				$title = $title . stth_get_svg( array( 'icon' => 'angle-down' ) );
		}
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'stth_dropdown_icon_to_menu_link', 10, 4 );

/**
 * Returns an array of supported social links (URL and icon name).
 *
 * @return array Array of social links icons.
 */
function stth_social_links_icons() {
	$social_links_icons = array(
		'behance.net'     => 'behance',
		'codepen.io'      => 'codepen',
		'deviantart.com'  => 'deviantart',
		'digg.com'        => 'digg',
		'docker.com'      => 'dockerhub',
		'dribbble.com'    => 'dribbble',
		'dropbox.com'     => 'dropbox',
		'facebook.com'    => 'facebook',
		'flickr.com'      => 'flickr',
		'foursquare.com'  => 'foursquare',
		'plus.google.com' => 'google-plus',
		'github.com'      => 'github',
		'instagram.com'   => 'instagram',
		'linkedin.com'    => 'linkedin',
		'mailto:'         => 'envelope-o',
		'medium.com'      => 'medium',
		'pinterest.com'   => 'pinterest-p',
		'pscp.tv'         => 'periscope',
		'getpocket.com'   => 'get-pocket',
		'reddit.com'      => 'reddit-alien',
		'skype.com'       => 'skype',
		'skype:'          => 'skype',
		'slideshare.net'  => 'slideshare',
		'snapchat.com'    => 'snapchat-ghost',
		'soundcloud.com'  => 'soundcloud',
		'spotify.com'     => 'spotify',
		'stumbleupon.com' => 'stumbleupon',
		't.me'            => 'telegram',
		'telegram.me'     => 'telegram',
		'tumblr.com'      => 'tumblr',
		'twitch.tv'       => 'twitch',
		'twitter.com'     => 'twitter',
		'vimeo.com'       => 'vimeo',
		'vine.co'         => 'vine',
		'vk.com'          => 'vk',
		'whatsapp.com'    => 'whatsapp',
		'wordpress.org'   => 'wordpress',
		'wordpress.com'   => 'wordpress',
		'yelp.com'        => 'yelp',
		'youtube.com'     => 'youtube'
	);
	return apply_filters( 'stth_social_links_icons', $social_links_icons );
}