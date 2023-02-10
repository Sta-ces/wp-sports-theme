<?php
function aj_script(){

    function boucleCreation($ext, $not = array()){
    	$dir = "/assets/".$ext;
    	$tab_script = scandir(get_parent_theme_file_path($dir));

		foreach ($tab_script as $script) {
	        if(!in_array($script, $not) && preg_match("/\.".$ext."$/", $script)){
	    		$file = get_path_uri($dir.'/'.$script);
	    		$custom = 'custom_'.$script;
				$path = get_theme_path($dir."/".$script);
	    		if(is_file($path) && file_exists($path)){
		    		if($ext == "js"){ wp_enqueue_script( $custom, $file ); }
					else if($ext == "css"){ wp_enqueue_style( $custom, $file ); }
					else if($ext == "php"){ require_once $file; }
	    		}
	        }
		}
    }

	wp_enqueue_style( 'meyer-css', 'https://meyerweb.com/eric/tools/css/reset/reset.css' );
	wp_enqueue_script( 'tools-js', 'https://cdn.jsdelivr.net/gh/Sta-ces/stEngine/inc/tools.js' );

	boucleCreation("js", array("custom.js", 'modules.js'));
	boucleCreation("css", array("custom.css", "widget-column"));

	// CUSTOM FILES
	$path_custom_css = '/assets/css/custom.css';
	if(file_exists(get_theme_path($path_custom_css)))
		wp_enqueue_style('custom_file_css', get_path_uri($path_custom_css));

	$path_custom_js = '/assets/js/custom.js';
	if(file_exists(get_theme_path($path_custom_js)))
		wp_enqueue_script('custom_file_js', get_path_uri($path_custom_js));
}
add_action( 'wp_enqueue_scripts', 'aj_script' );

function add_type_attribute($tag, $handle, $src) {
    if ( !in_array($handle, ['custom_modules.js', 'custom_base.js']) ) return $tag;
    $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}
add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);

function add_admin_scripts( $hook ) {
    global $post;

    if ( $hook === 'post-new.php' || $hook === 'post.php' ) {
        if ( '' !== $post->post_type ) {
            wp_enqueue_script( 'admin-post-js', get_path_uri('/assets/js/admin-post.js'), array('jquery') );
			wp_enqueue_style( 'admin-post-css', get_path_uri('/assets/css/admin-post.css') );
        }
    }

	wp_enqueue_script( 'admin-js', get_path_uri('/assets/js/admin.js'), array('jquery') );
	wp_enqueue_style( 'admin-css', get_path_uri('/assets/css/admin.css') );

	wp_localize_script( 'admin-js', 'admin', [ 'includes' => includes_url(), 'includes_media' => includes_url('/images/media') ] );
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );