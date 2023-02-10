<?php
function get_theme_path($path){
	if(file_exists(get_stylesheet_directory() . $path)) return get_theme_file_path($path);
	elseif(file_exists(get_template_directory() . $path)) return get_parent_theme_file_path($path);
}

function get_path_uri($path){
	return file_exists(get_stylesheet_directory().$path) ? get_stylesheet_directory_uri().$path : get_template_directory_uri().$path;
}

function require_theme_path($path){ require_once(get_theme_path($path)); }

define("DIR", __DIR__);
define("ISLOG", is_user_logged_in());
define("ASSETS", get_theme_path("/assets"));
define("IMAGES", get_theme_path("/assets/images"));
define("MODALS", get_theme_path("/modals"));

require_theme_path( '/inc/tools.php' );
require_theme_path( '/inc/infoth-manager.php' );
require_theme_path( '/inc/icon-functions.php' );
require_theme_path( '/inc/new-post-type/new_post_type.php' );
require_theme_path( '/inc/widgets/button-widget-register.php' );
require_theme_path( '/inc/widgets/infoth-widget-register.php' );
require_theme_path( '/inc/widgets/shortcodes-widget-register.php' );
require_theme_path( '/inc/customizer.php' );
require_theme_path( '/inc/shortcodes.php' );
require_theme_path( '/inc/settings.php' );
require_theme_path( '/inc/params/category-param.php' );
require_theme_path( '/inc/ajoutScripts.php' );
require_theme_path( '/inc/media-upload.php' );
require_theme_path( '/child-functions.php' );

$description_website = new CustomSettings("head-meta-description", "Renseigner les informations pour le SEO", "html-meta-tag", "SEO");
$description_website->add_fields(array(
	[
		"name"=>"meta-keywords",
		"title"=>"Mots-clés",
		"type"=>"textarea",
		"placeholder"=>"design,belgique,web,..."
	],
	[
		"name"=>"meta-google-tagmanager",
		"title"=>"Google TagManager",
		"placeholder"=>"GTM-xxxxxx"
	],
	[
		"name"=>"twitter-user",
		"title"=>"Twitter",
		"placeholder"=>"@Stacesbe"
	],
	[
		"name"=>"copyright-info",
		"title"=>"Pied de page",
		"description"=>"Footer",
		"type"=>"WYSWYG",
		"placeholder"=>get_years_copyright()
	]
));

$single_settings = new CustomSettings("singles-settings", "", "reading");
$single_settings->add_fields(array(
	[
		"name"=>"display-date-single",
		"title"=>"Afficher la date des articles",
		"type"=>"checkbox"
	]
));

function stath_custom_header_setup(){
	/** THEME SUPPORT */
	//wp_create_category("name-category", 0);
    add_theme_support( 'custom-logo', array(
		'header-text' => array( 'site-title', 'site-description' )
	) );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'custom-background' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
	add_post_type_support('page', 'excerpt');
	add_post_type_support('post', 'page-attributes');
	remove_theme_support('widgets-block-editor');
	if(!get_infoth('is-commentary')) remove_theme_support( 'comments' );
	load_theme_textdomain( 'stacesbe', get_template_directory() . '/lang' );
	/** END THEME SUPPORT */

	// change role capacities
	$role_object = get_role( 'editor' );
	$role_object->add_cap( 'edit_theme_options' );
}
add_action('after_setup_theme', 'stath_custom_header_setup');

function stath_remove_menus(){
    global $submenu;

	if(!get_infoth('is-commentary')){
		remove_menu_page( 'edit-comments.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	}

	add_submenu_page( 'options-general.php', _st('Staces Tricks'), _st('Staces Tricks'), 'administrator', 'staces-tricks', function(){ settings_staces_callback(); } );
}
add_action('admin_menu', 'stath_remove_menus');

function settings_staces_callback(){
	$content_class = [
		"none" => "Cache l'élément HTML",
		"title" => "Modifie le style typographique du texte",
		"italic" => "Met le texte en italic",
		"bold" => "Met le texte en gras",
		"lowercase" => "Met le texte en minuscule",
		"uppercase" => "Met le texte en majuscule",
		"txt-color" => "Change la couleur de texte en la couleur primaire choise dans Apparence",
		"bg-color" => "Change la couleur de fond en la couleur primaire choisie dans Apparence",
		"shape-bg" => "Ajoute la forme lignée de la charte graphique en fond"
	];
	$content_btn_class = [
		"button" => "Aspect visuel d'un bouton standard.",
		"outline" => "Aspect visuel d'un bouton juste avec des contours.",
		"second" => "Force la couleur d'un bouton dans la couleur secondaire.",
		"third" => "Force la couleur d'un bouton dans la 3<sup>e</sup> couleur.",
		"white" => "Force la couleur d'un bouton en blanc.",
		"black" => "Force la couleur d'un bouton en noir."
	];
	$content_shortcode = [
		"copyright" => "<strong>[copyright date='2022']</strong> : Affiche une année de début suivi de l'année active avec le symbole copyright (&copy;).<br/><em>Par défaut : si la date n'est pas spécifié, elle renverra automatique l'année active.</em>",
		"widget" => "<strong>[widget id='{id-widget}']</strong> : Affiche le widget possédant l'ID spécifié.<br/><em>Pour voir l'ID en question, allez sur la page Apparence > Widget</em>"
	];
	$contents = ["Class" => $content_class, "Buttons" => $content_btn_class, "Shortcode" => $content_shortcode];
	?>
	<div class="wrap">
		<h2><?php _ste("Trucs et astuces de Staces"); ?></h2>
		<?php foreach($contents as $ks => $vs): ?>
			<h3><?php echo $ks; ?></h3>
			<table class="wp-list-table widefat fixed striped table-view-list pages">
				<thead>
					<tr>
						<th width="100"><strong>Nom</strong></th>
						<th><strong>Description</strong></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($vs as $k => $v): ?>
						<tr><td><strong><?php echo $k; ?></strong></td><td><?php _ste($v); ?></td></tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endforeach; ?>
	</div>
	<?php
}

function stath_widgets_init() {
	$number = intval(get_infoth('widget-count', 6));
	for($i = 1; $i <= $number; $i++){
		register_sidebar(
			array(
				'name'          => _st('Widget '.$i),
				'id'            => 'column-'.$i,
				'description'   => _st('Shortcode: [widget id="column-'.$i.'"]'),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
	register_sidebar(
		array(
			'name'          => _st('Header info'),
			'id'            => 'header-info',
			'description'   => _st('Ajouter les éléments relatifs à l\'en-tête'),
			'before_widget' => '<section id="%1$s" class="widget %2$s header-info-container">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => _st('Sponsors'),
			'id'            => 'sponsors-container',
			'description'   => _st('Listes des sponsors'),
			'before_widget' => '<section id="%1$s" class="widget %2$s sponsors-container">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	if(class_exists('Button_Widget_Register')) register_widget( 'Button_Widget_Register' );
	if(class_exists('INFOTH_Widget_Register')) register_widget( 'INFOTH_Widget_Register' );
	if(class_exists('Shortcodes_Widget_Register')) register_widget( 'Shortcodes_Widget_Register' );
}
add_action( 'widgets_init', 'stath_widgets_init' );

function stath_custom_nav_menu($items, $args){
	if($args->menu->term_id === get_menu_id_by_name('social'))
		get_mainmenu([
			'container' => 'nav',
			'namemenu' => 'social',
			'class' => 'flex',
			'element_class' => 'menu-icon',
            'fa' => true,
            'target' => '_blank'
        ]);
	else return $items;
}
add_filter('wp_nav_menu_items', 'stath_custom_nav_menu', 10, 2);

function stath_register_my_menu(){
	/** REGISTER MENU */
    register_nav_menus( array(
        'main-menu' => _st('Main Menu'),
        'social' => _st('Social Media'),
        'menu-right' => _st('Menu Right')
    ) );
	/** END REGISTER MENU */

	/** ADD EXCERPT ARTICLE */
	$tabExcerpt = array('page');
	foreach ($tabExcerpt as $excerpt){ add_post_type_support( $excerpt, 'excerpt' ); }
	/** END ADD EXCERPT ARTICLE */
	
	if(!get_infoth('is-commentary')){
		remove_post_type_support( 'post', 'comments' );
		remove_post_type_support( 'page', 'comments' );
	}
}
add_action( 'init', 'stath_register_my_menu' );

function stath_custom_login_stylesheet() {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$uri_logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
	?>
	<style type="text/css">
		:root{
			--main-color: <?php echo get_theme_mod('primary_color', '#020202'); ?>;
			--secondary-color: <?php echo get_theme_mod('secondary_color', '#232323'); ?>;
			--third-color: <?php echo get_theme_mod('third_color', '#232323'); ?>;
			--black-color: <?php echo get_theme_mod('black_color', '#000000'); ?>;
			--success-color: #b3e041;
			--error-color: #d93636;
		}
		body.login{
			background-color: var(--second-color);
			background-image: url(<?php echo get_background_image(); ?>);
			background-size: cover;
			background-position: center;
			position: relative;
			display: flex;
		}
		body.login::before{
			position: absolute;
			top: 0; left: 0; right: 0; bottom: 0;
			content: "";
			display: block;
			background-color: gray;
			opacity: .85;
			z-index: -1;
		}
		body.login div#login{
			background-color: white;
			border: solid .25em var(--main-color);
			padding: 2em;
			margin: auto;
			position: relative;
		}
		body.login div#login h1,
		body.login div#login::before{
			position: absolute;
			top: 0; left: 50%;
			-webkit-transform: translate(-50%, -50%);
			-moz-transform: translate(-50%, -50%);
			-ms-transform: translate(-50%, -50%);
			-o-transform: translate(-50%, -50%);
			transform: translate(-50%, -50%);
			border-radius: 50%;
			width: 100px; height: 100px;
			padding: .75em;
			background-color: white;
		}
		body.login div#login::before{
			content: "";
			display: block;
			padding: 1.25em;
			z-index: -1;
			background-color: var(--main-color);
			border: solid .5em var(--main-color);
		}
		body.login div#login h1 a{
			background-image: url(<?php echo $uri_logo[0]; ?>);
			background-position: center;
			background-size: contain;
		}
		body.login div#login form#loginform{
			background-color: transparent;
			padding: 0;
			margin-top: 4em;
			border: none;
		}
		body.login div#login form#loginform *{ float: none; }
		body.login div#login form#loginform > *{ text-align: center; }
		body.login div#login form#loginform input[type=text],
		body.login div#login form#loginform input[type=password]{
			border: none;
			border-bottom: solid 2px var(--main-color);
			font-size: 20px;
			margin-bottom: 24px;
			box-shadow: none;
		}
		body.login div#login form#loginform input[type=text]:focus,
		body.login div#login form#loginform input[type=password]:focus{
			border: solid 2px var(--main-color);
			box-shadow: none;
		}
		body.login div#login form#loginform input[type=checkbox]{ border-color: var(--main-color); }
		body.login div#login form#loginform input[type=checkbox]::before{ color: var(--main-color); }
		body.login div#login form#loginform .forgetmenot{ margin-bottom: 1em; }
		body.login div#login form#loginform .submit input[type=submit]{
			border-radius: 0;
			background-color: var(--main-color);
			border: none;
			text-shadow: none;
			text-transform: uppercase;
		}
		body.login div#login #nav,
		body.login div#login #backtoblog{ text-align: center; }
		body.login div#login .password-input + .button,
		body.login div#login .password-input + .button-secondary,
		body.login div#login .password-input + .button:hover,
		body.login div#login .password-input + .button-secondary:hover,
		body.login div#login .password-input + .button:focus,
		body.login div#login .password-input + .button-secondary:focus{
			color: var(--secondary-color);
			border: none;
			box-shadow: none;
		}
		body.login div#login #login_error{
			border: solid 4px var(--error-color);
			margin-top: 50px;
			text-align: center;
		}
	</style>
	<script>
		addEventListener("load", function(){
			if(document.querySelector("body.login #login a") != null)
				document.querySelector("body.login #login a").setAttribute("href", "<?php echo get_home_url(); ?>");
		});
	</script>
	<?php
}
add_action( 'login_enqueue_scripts', 'stath_custom_login_stylesheet' );

// Removes from admin bar
function stth_admin_bar_render() {
    global $wp_admin_bar;
    if(!get_infoth('is-commentary')) $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'stth_admin_bar_render' );

function custom_shortcode_atts_wpcf7_filter( $out, $pairs, $atts ) {
	$my_attr = 'destination-email';
	if ( isset( $atts[$my_attr] ) ) $out[$my_attr] = $atts[$my_attr];
	return $out;
}
add_filter( 'shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3 );

function add_new_contactmethods( $contactmethods ) {
	$contactmethods['twitter'] = 'Twitter'; 
	$contactmethods['facebook'] = 'Facebook';
	$contactmethods['linkedin'] = 'Linkedin';
	return $contactmethods;
}
add_filter('user_contactmethods','add_new_contactmethods',10,1);

function remove_body_classes( $classes ) { 
    $remove_classes = ['custom-background'];
    $classes = array_diff($classes, $remove_classes);
    return $classes;
}
add_filter('body_class', 'remove_body_classes');