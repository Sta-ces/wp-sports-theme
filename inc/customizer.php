<?php
/**
 * Customizer Class
 * Developer : Cedric Staces
 * uri : https://staces.be/
 */
class STHCustomizer
{
    function __construct($wp, $name, $args)
	{
        if(!isset($args['label']) || !isset($args['section'])) return false;
        
        $args = array_merge([
            'default' => '',
            'transport' => 'refresh',
            'type' => 'text',
            'settings' => $name
        ], $args);
		if(!isset($wp->sections()[$args['section']])){
			$section_args = array_merge([
				'title'	=> _st(ucfirst($args['section'])),
				'priority'	=> 150
			], (isset($args['section_args']) ? $args['section_args'] : []));
			$wp->add_section($args['section'], $section_args);
		}
		$wp->add_setting(
			$name,
			array(
				'default'		=> $args['default'],
				'transport'		=> $args['transport']
			)
		);
		switch ($args['type']) {
			case 'color': case 'colors':
				$wp->add_control(new WP_Customize_Color_Control( $wp, $name, $args ));
				break;
			case 'image': case 'images': case 'media':
				$args['mime_type'] = 'image';
				$wp->add_control(new WP_Customize_Media_Control( $wp, $name, $args ));
				break;
			
			default: $wp->add_control($name, $args); break;
		}
        return true;
	}
}
function stth_customize_register( $wp_customize ) {
	// REMOVE CONTROLS
	$wp_customize->remove_control('header_textcolor');
	$wp_customize->remove_control('background_color');
	$wp_customize->remove_control('display_header_text');
	$wp_customize->remove_control('header_text');
	$wp_customize->remove_section('static_front_page');
	$wp_customize->remove_section('header_image');
	// CUSTOMIZERS
	// COLORS
	new STHCustomizer($wp_customize, 'dark_mode', [ 'label' => 'Dark mode', 'section' => 'colors', 'type' => 'checkbox' ]);
	new STHCustomizer($wp_customize, 'primary_color', [ 'label' => 'Couleur principale', 'default' => '#020202', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'secondary_color', [ 'label' => 'Couleur secondaire', 'default' => '#232323', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'third_color', [ 'label' => '3<sup>e</sup> couleur', 'default' => '#232323', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'black_color', [ 'label' => 'Couleur Noir', 'description' => 'Principalement utilisée pour les textes standards', 'default' => '#000000', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'success_color', [ 'label' => 'Couleur Succès', 'default' => '#b3e041', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'error_color', [ 'label' => 'Couleur Erreur', 'default' => '#d93636', 'section' => 'colors', 'type' => 'color']);
	new STHCustomizer($wp_customize, 'color_logo', [ 'label' => 'Couleur logo', 'default' => 'default', 'section' => 'colors', 'type' => 'radio', 'choices' => ['default' => _st('Default'), 'white' => _st('White'), 'black' => _st('Black')] ]);
    new STHCustomizer($wp_customize, 'pattern_color', [ 'label' => 'Couleur du pattern', 'default' => 'lightgray', 'section' => 'colors', 'type' => 'radio', 'choices' => ['white' => _st('White'), 'lightgray' => _st('Lightgray'), 'gray' => _st('Gray'), 'black' => _st('Black')] ]);
	new STHCustomizer($wp_customize, 'header_text_color', [ 'label' => 'Couleur des textes header', 'default' => '#000', 'section' => 'colors', 'type' => 'color' ]);
	// BACKGROUND IMAGE
    new STHCustomizer($wp_customize, 'video_header', [ 'label' => 'Vidéo d\'arrière-plan', 'description' => 'Utilisez l\'<b>url de partage</b> fournie par YouTube.<br/>Exemple : https://youtu.be/xxx', 'section' => 'background_image', 'priority' => 1, 'input_attrs' => [ 'placeholder' => 'URL Youtube' ]]);
	new STHCustomizer($wp_customize, 'height_header', [ 'label' => 'Hauteur header', 'description' => 'Définissez une hauteur en pourcentage.', 'section' => 'background_image', 'type' => 'number', 'default' => 75, 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ]);
    new STHCustomizer($wp_customize, 'pattern_size', [ 'label' => 'Taille du pattern', 'default' => 10, 'section' => 'background_image', 'type' => 'number', 'input_attrs' => [ 'min' => 0 ] ]);
	new STHCustomizer($wp_customize, 'style_header', [ 'label' => 'Style en-tête', 'default' => 'default', 'section' => 'background_image', 'type' => 'select', 'choices' => get_infoth('style-header-list') ]);
	new STHCustomizer($wp_customize, 'gradient_color', [ 'label' => 'Couleur du dégradé', 'default' => '#fff', 'section' => 'background_image', 'type' => 'color' ]);
	// TITLE_TAGLINE
	new STHCustomizer($wp_customize, 'website_description', [ 'label' => 'Description du site', 'description' => 'Tentez d\'être concis dans votre description. (Recommandé maximum 160 caractères)', 'type' => 'textarea', 'section' => 'title_tagline', 'input_attrs' => [ 'maxlength' => 160 ] ]);
	new STHCustomizer($wp_customize, 'address_google_maps', [ 'label' => 'Adresse', 'section' => 'title_tagline' ]);
	// FOOTER
	new STHCustomizer($wp_customize, 'is_social_media', [ 'label' => 'Afficher les réseaux sociaux', 'type' => 'checkbox', 'section' => 'footer' ]);
	new STHCustomizer($wp_customize, 'is_google_maps', [ 'label' => 'Afficher la carte', 'type' => 'checkbox', 'section' => 'footer' ]);
	// FONTSIZE
    new STHCustomizer($wp_customize, 'very_large_font', [ 'label' => 'Très gros titre', 'section' => 'fontsize', 'default' => 26, 'type' => 'number', 'input_attrs' => [ 'min' => 8 ], 'section_args' => [ 'title' => _st('Fonts'), 'description' => 'Valeur définie en point (pt)' ] ]);
    new STHCustomizer($wp_customize, 'large_font', [ 'label' => 'Gros titre', 'section' => 'fontsize', 'default' => 18, 'type' => 'number', 'input_attrs' => [ 'min' => 8 ] ]);
    new STHCustomizer($wp_customize, 'normal_font', [ 'label' => 'Texte', 'section' => 'fontsize', 'default' => 12, 'type' => 'number', 'input_attrs' => [ 'min' => 8 ] ]);
    new STHCustomizer($wp_customize, 'small_font', [ 'label' => 'Petit texte', 'section' => 'fontsize', 'default' => 8, 'type' => 'number', 'input_attrs' => [ 'min' => 8 ] ]);
	// SETTINGS
    new STHCustomizer($wp_customize, 'hide_logo', [ 'label' => 'Cacher le logo', 'section' => 'settings', 'type' => 'checkbox', 'priority' => 8]);
    new STHCustomizer($wp_customize, 'hide_text_header', [ 'label' => 'Afficher les textes d\'en-tête', 'section' => 'settings', 'type' => 'checkbox', 'priority' => 8]);
    new STHCustomizer($wp_customize, 'logo_height', [ 'label' => 'Taille du logo', 'section' => 'settings', 'description' => 'Valeur en pourcentage (%)', 'type' => 'number', 'priority' => 9, 'input_attrs' => [ 'min' => 0 ], 'default' => 100]);
    new STHCustomizer($wp_customize, 'contact_page', [ 'label' => 'Sélectionnez la page de contact', 'section' => 'settings', 'description' => 'Page de contact par mail.', 'type' => 'dropdown-pages' ]);
    new STHCustomizer($wp_customize, 'contact_text', [ 'label' => 'Texte du bouton contact', 'section' => 'settings' ]);
    new STHCustomizer($wp_customize, 'is_close_website', [ 'label' => 'Fermer le site temporairement', 'section' => 'settings', 'type' => 'checkbox', 'priority' => 300 ]);
    new STHCustomizer($wp_customize, 'close_page', [ 'label' => 'Page de maintenance', 'section' => 'settings', 'description' => 'Sélectionnez la page de maintenance qui s\'affichera lors de la maintenance de votre site.', 'type' => 'dropdown-pages' ]);
	new STHCustomizer($wp_customize, 'api_key_google_maps', [ 'label' => 'Google Map (API key)', 'section' => 'settings' ]);
	new STHCustomizer($wp_customize, 'active_commentary', [ 'label' => 'Désactivé les commentaires', 'section' => 'settings', 'type' => 'checkbox', 'default' => 1 ]);
	stth_customizer($wp_customize);
}
add_action( 'customize_register', 'stth_customize_register' );
function style_customize(){
	?>
		<style type="text/css">
			:root{
				--main-color: <?php echo get_theme_mod('primary_color', '#020202'); ?>;
				--main-color-semi: <?php echo hex2rgba(get_theme_mod('primary_color'), .5); ?>;
				--main-color-transparent: <?php echo hex2rgba(get_theme_mod('primary_color'), 0); ?>;
				--secondary-color: <?php echo get_theme_mod('secondary_color', '#232323'); ?>;
				--secondary-color-semi: <?php echo hex2rgba(get_theme_mod('secondary_color'), .5); ?>;
				--secondary-color-transparent: <?php echo hex2rgba(get_theme_mod('secondary_color'), 0); ?>;
				--third-color: <?php echo get_theme_mod('third_color', '#232323'); ?>;
				--third-color-semi: <?php echo hex2rgba(get_theme_mod('third_color'), .5); ?>;
				--third-color-transparent: <?php echo hex2rgba(get_theme_mod('third_color'), 0); ?>;
				--black-color: <?php echo get_theme_mod('black_color', '#000000'); ?>;
				--black-color-semi: <?php echo hex2rgba(get_theme_mod('black_color'), .5); ?>;
				--black-color-transparent: <?php echo hex2rgba(get_theme_mod('black_color'), 0); ?>;
				--success-color: <?php echo get_theme_mod('success_color', '#b3e041'); ?>;
				--error-color: <?php echo get_theme_mod('error_color', '#d93636'); ?>;
				--gradient-color: <?php echo get_theme_mod('gradient_color', '#fff'); ?>;
				--gradient-color-semi: <?php echo hex2rgba(get_theme_mod('gradient_color', '#fff'), .5); ?>;
				--gradient-color-transparent: <?php echo hex2rgba(get_theme_mod('gradient_color', '#fff'), 0); ?>;
				--admin-height: 32px;
				--pattern-size: <?php echo get_theme_mod('pattern_size', '10').'px'; ?>;
				--pattern-color: <?php echo get_theme_mod('pattern_color', '#d3d3d3'); ?>;
				--logo-height: <?php echo get_theme_mod('logo_height', '100').'%'; ?>;
				--video-height: <?php echo get_theme_mod('video_height', '800').'px'; ?>;
				--very-large-font: <?php echo get_theme_mod('very_large_font', '26').'pt'; ?>;
				--large-font: <?php echo get_theme_mod('large_font', '18').'pt'; ?>;
				--normal-font: <?php echo get_theme_mod('normal_font', '12').'pt'; ?>;
				--small-font: <?php echo get_theme_mod('small_font', '8').'pt'; ?>;
				--header-text-color: <?php echo get_theme_mod('header_text_color', get_theme_mod('black_color', '#000')); ?>;
				--height-header: <?php echo get_theme_mod('height_header', 75)."vh"; ?>;
			}
			.wp_color_hover,
			.wp_bgcolor_hover,
			.wp_second_hover,
			.wp_third_hover{
				-webkit-transition: color .25s, background-color .25s;
				-moz-transition: color .25s, background-color .25s;
				-ms-transition: color .25s, background-color .25s;
				-o-transition: color .25s, background-color .25s;
				transition: color .25s, background-color .25s;
			}
		    .wp_color,
		    .wp_color_hover:hover{ color: var(--main-color) !important; }
		    .wp_bgcolor,
		    .wp_bgcolor_hover:hover{ background-color: var(--main-color) !important; }
			.wp_bordercolor{ border-color: var(--main-color) !important; }
		    .wp_second,
		    .wp_second_hover:hover{ color: var(--secondary-color) !important; }
		    .wp_third,
		    .wp_third_hover:hover{ color: var(--third-color) !important; }
			.wp_pattern::after{
				background-image: url(<?php echo wp_get_attachment_url(get_theme_mod('pattern_background')); ?>);
				background-repeat: repeat;
				background-size: <?php echo get_theme_mod('pattern_size', '200')."px"; ?>;
			}
			.wp_default_bgcolor{ background-color: white; }
		</style>
	<?php
}
add_action('wp_head', 'style_customize');