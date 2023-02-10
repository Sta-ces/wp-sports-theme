<?php
function echo_r($content){ echo "<pre data-pre='dev_info_echo_r'>"; var_dump($content); echo "</pre>"; }
function isDev(){ return isset($_GET["dev"]) && $_GET["dev"] == "1"; }
function dev_r($msg){ if(isDev()) echo_r($msg); }
function _st($word){
	$t = __($word, "stacesbe");
	if($word === $t) return __($word);
	return $t;
}
function _ste($word){ echo _st($word); }

function modif_link($id){
	edit_post_link(
		__('Modified'),
		'<footer class="entry-footer"><span class="edit-link modified">',
		'</span></footer>',
		$id
	);
}

function create_date($date, $is_text = false){
	$date = str_replace("/", "-", $date);
	$result = ($is_text) ? "l - d F Y" : "d-m-Y";
	return date($result, strtotime($date));
}

function get_menu_id_by_name( $name ) {
	$location = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object($location[$name]);
	return $menu->term_id;
}

function get_mainmenu($args){
	$container = (isset($args['container'])) ? $args['container'] : 'nav';
	$nocontainer = (isset($args['nocontainer'])) ? $args['nocontainer'] : true;
	$namemenu = (isset($args['namemenu'])) ? $args['namemenu'] : 'main-menu';
	$id = (isset($args['id'])) ? $args['id'] : '';
	$classes = (isset($args['class'])) ? $args['class'] : '';
	$container_id = (isset($args['container_id'])) ? $args['container_id'] : '';
	$container_classes = (isset($args['container_class'])) ? $args['container_class'] : '';
	$li_color_hover = (isset($args['class_color_hover'])) ? $args['class_color_hover'] : 'wp_second_hover';
	$element_classes = ( (!$nocontainer) ? $classes : '' ).' '.$li_color_hover.' '.(isset($args["element_class"]) ? $args["element_class"] : '');
	$isicon = isset($args['icon']) ? boolval($args['icon']) : false;
	$target = isset($args['target']) ? $args['target'] : '';
	$add_items = isset($args['add_items']) ? $args['add_items'] : '';
	$pos_items = isset($args['position_items']) && $args['position_items'] === 'before' ? 'before' : 'after';

	if ( has_nav_menu( $namemenu ) ) :
		$menu_items = wp_get_nav_menu_items(get_menu_id_by_name($namemenu));
		if($menu_items && count($menu_items) > 0): ?>
			<?php if($nocontainer): ?>
				<?php echo "<".$container." id='".$container_id."' class='".$container_classes."'>"; ?>
					<ul <?php echo 'id="'.$id.'"'; ?> <?php echo 'class="'.$classes.'"'; ?>>
			<?php endif; ?>
						<?php
							$li = "";
							if($pos_items === 'before') $li .= $add_items;
							foreach ($menu_items as $key => $value) {
								$title_lower = strtolower($value->title);
								$li .= "<li><a href='".$value->url."' ";
								if($target !== '') $li .= "target='".$target."' ";
								$li .= 'class="'.$element_classes."'>";
								if($isicon) $li .= stth_get_svg([ "icon" => $title_lower ]);
								else $li .= $value->title;
								$li .= "</a></li>";
							}
							if($pos_items === 'after') $li .= $add_items;
							echo $li;
						?>
			<?php if($nocontainer): ?>
					</ul>
				<?php echo "</".$container.">"; ?>
			<?php endif; ?>
		<?php endif;
		return $menu_items ? count($menu_items) : 0;
	endif;
	return null;
}

function link_post($post, $content, $class = ""){
	?>
	<a href="<?php echo get_post_permalink($post->ID); ?>" title='<?php echo __("Voir")." ".$post->post_title; ?>' class='<?php echo $class; ?>'><?php echo $content; ?></a>
	<?php
}

function get_current_url(){
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
    $url.= $_SERVER['HTTP_HOST'];
    $url.= $_SERVER['REQUEST_URI'];
	return $url;
}

function get_years_copyright($debut_date = "now"){
	$now_date = date("Y");
	if($debut_date == 'none' || $debut_date == 0) return "&copy;";
	else if($debut_date == 'now') return "&copy; ".$now_date;
	else if($now_date != $debut_date) return "&copy; ".$debut_date."-".$now_date;
	else return "&copy; ".$debut_date;
}

function the_designer(){ echo "<a href='".get_uri_designer()."' target='_blank' title='vers le site du Web Developer ".get_name_designer()."' class='wp_color_hover'>".get_name_designer()."</a>"; }
function get_name_designer(){ return "Staces.be"; }
function get_uri_designer(){ return "https://staces.be/"; }

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function replace_between($str, $delimiter, $replacement) {
    $pos = strpos($str, $delimiter);
    $start = $pos === false ? 0 : $pos + strlen($delimiter);

    $pos = strpos($str, $delimiter, $start);
    $end = $start === false ? strlen($str) : $pos;

    $result = substr_replace($str,$replacement,  $start, $end - $start);
	$result = str_replace($delimiter, "", $result);
	return $result;
}

function create_tag($delimiter, $tag, $subject){
	if($subject == null) return "";
	if(strpos($subject, $delimiter) == false) return $subject;
	return replace_between($subject, $delimiter, "<".$tag.">".get_string_between($subject, "**", "**")."</".$tag.">");
}

function html_map($array, $tag = "span", $classes = "", $attr = ""){
	if(!count(array_filter($array))) return "";
	if(!empty($classes)) $classes = "class='{$classes}'";
	return "<{$tag} {$classes} {$attr}>".implode("</{$tag}><{$tag} {$classes}>", array_map("trim", array_filter($array)))."</{$tag}>";
}

function str_attr($string){ return str_replace("'", "&apos;", $string); }
function stth_convert_string($string){ return create_tag("**", "em", $string); }

function stth_get_the_category($id, $exclude = []){
	$cats = get_the_category($id);
	foreach($cats as $key => $value){
		if(!in_array($value->name, $exclude)) return $value;
	}
}

function YTVideo($url, $echo = true){
	$frame = "";
	$id = null;
	switch(true){
		case boolval(preg_match('/(youtu\.)/', $url)):
			$id = preg_replace('/(^https:\/\/youtu\..+\/)/', '', $url);
			break;
		case boolval(preg_match('/(youtube\.)/', $url)):
			parse_str(parse_url($url, PHP_URL_QUERY), $vars);
			$id = $vars['v'];
			break;
	}
	if($id !== null) $frame = '<iframe src="https://youtube.com/embed/'.$id.'?controls=0&autoplay=1&mute=1&loop=1&showinfo=0&feature=oembed&rel=0&playlist='.$id.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" class="video-iframe" style="pointer-events: none;" allowfullscreen></iframe>';
	if($echo && !empty($frame)) echo $frame;
	return $frame;
}

function getCountries(){
    $countries_json = json_decode(file_get_contents("https://raw.githubusercontent.com/fannarsh/country-list/master/data.json"));
    $countries = [];
    foreach($countries_json as $key => $value)
        $countries[$value->code] = $value->name;
	asort($countries);
    return $countries;
}

function getCountry($code){ return getCountries()[$code]; }

function getWidget($id){
	ob_start();
	dynamic_sidebar($id);
	$sidebar = ob_get_clean();
	if ($sidebar) return "<section class='$id'>" . $sidebar . "</section>";
	return "";
}

function hex2rgba($color, $opacity = 1) {
	$default = 'rgb(0,0,0)';

	if(empty($color)) return $default; 
	if ($color[0] == '#' ) $color = substr( $color, 1 );

	if (strlen($color) == 6)
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	elseif ( strlen( $color ) == 3 )
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	else return $default;

	$rgb = array_map('hexdec', $hex);
	return 'rgba('.implode(",",$rgb).','.$opacity.')';
}

function get_shortcodes(){
    global $shortcode_tags;
    $shortcodes = $shortcode_tags;
	ksort($shortcodes);
    return $shortcodes;
}

function get_meta($post_id, $name, $key){
	$post_meta = get_post_meta($post_id);
	$post_key = "_".$name."_services_".$key."_meta_box";
	return (isset($post_meta[$post_key])) ? $post_meta[$post_key][0] : '';
}

if(!function_exists("createDate")){
	function createDate($date, $format = "", $echo = true){
		if(empty($format)) $format = get_infoth("date-format");
		$df = date_i18n($format, strtotime($date));
		if($echo) echo $df;
		return $df;
	}
}