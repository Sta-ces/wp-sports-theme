<?php
function table_infoth(){ return ["author", "sitename", "description", "slogan", "copyright", "twitter", "address"]; }
function infoth($show = '', $only_text = false){ echo get_infoth($show, $only_text); }
function get_infoth($show = '', $only_text = false){
	switch(strtolower($show)){
		case 'is-dark-mode': $output = boolval(get_theme_mod('dark_mode')); break;
		case 'is-close': $output = boolval(get_theme_mod('is_close_website')); break;
		case 'page-close':
			if(get_infoth('is-close')){
				$page = (get_theme_mod('close_page')) ? get_page_link(get_theme_mod('close_page')) : wp_login_url();
				if($page !== get_current_url() && wp_redirect($page, 301)) exit();
			}
			break;
		case 'logo':
			$output = (get_infoth('is-logo')) ? get_custom_logo() : get_infoth('sitename');
			break;
		case 'logo-url':
			$logo = get_theme_mod("custom_logo");
			$image = wp_get_attachment_image_src( $logo , 'full' );
			$output = $image[0];
			break;
		case 'is-logo': $output = !boolval(get_theme_mod("hide_logo")); break;
		case 'logo-height': $output = get_theme_mod('logo_height', 75); break;
		case 'is-text-header': $output = boolval(get_theme_mod('hide_text_header')); break;
		case 'home-url': $output = get_home_url(); break;
		case 'sitename': $output = get_bloginfo("name"); break;
		case 'slogan': $output = get_bloginfo("description"); break;
		case 'description': $output = get_theme_mod("website_description"); break;
		case 'contact-link': $output = get_post_permalink(get_theme_mod('contact_page')); break;
		case 'contact-id': $output = get_theme_mod('contact_page'); break;
		case 'contact-text': $output = get_theme_mod('contact_text'); break;
		case 'author': $output = get_theme_mod('author_name'); break;
		case 'author-photo': $output = get_theme_mod('author_photo'); break;
		case 'video-header': $output = get_theme_mod('video_header'); break;
		case 'style-header': $output = (get_theme_mod('style_header') != 'default') ? get_theme_mod('style_header') : ''; break;
		case 'style-header-list': $output = ['default' => 'Default', 'arrow-down' => 'Arrow', 'increase-shape' => 'Increase', 'decrease-shape' => 'Decrease', 'gradient-header' => 'Gradient']; break;
		case 'date-format': $output = get_option('date_format'); break;
		case 'hour-format':
		case 'time-format': $output = get_option('time_format'); break;
		case 'full-date-format': $output = get_option('date_format')." ".get_option('time_format'); break;
		case 'is-commentary': $output = !boolval(get_theme_mod('active_commentary')); break;
		case 'is-google-maps': $output = boolval(get_theme_mod('is_google_maps')); break;
		case 'api-key-google-maps': $output = get_theme_mod('api_key_google_maps', ''); break;
		case 'address':
		case 'address-google-maps': $output = get_theme_mod('address_google_maps', ''); break;
		case 'is-social-media-footer': $output = boolval(get_theme_mod('is_social_media')); break;
		case 'keywords': $output = get_option('meta-keywords'); break;
		case 'tagmanager': $output = get_option('meta-google-tagmanager'); break;
		case 'twitter': $output = get_option('twitter-user'); break;
		case 'copyright': $output = apply_filters('the_content', get_option('copyright-info')); break;
		case 'thumbnail-url': $output = get_the_post_thumbnail_url(get_the_ID()); break;
        case 'date-single':
        case 'date-article': $output = boolval(get_option('display-date-single')); break;
		case 'filter-color': $output = get_theme_mod('color_logo'); break;
		default:
			if(get_theme_mod($show)) $output = get_theme_mod($show);
			elseif(get_option($show)) $output = get_option($show);
			else $output = "";
			break;
	}
	return $output;
}