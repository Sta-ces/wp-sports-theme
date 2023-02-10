<?php
function wpd_shortcode($atts){
   $tp_atts = shortcode_atts(array( 
      'name' =>  null,
   ), $atts);
   ob_start();
   echo "<section id='".$tp_atts['name']."' class='entry-post-type'><article>";
   get_template_part('template-parts/contents/content', $tp_atts['name']);
   echo "<article></section>";
   return ob_get_clean();
}

function content_shortcode($atts){
   $tp_atts = shortcode_atts(array( 
      'name' =>  null,
   ), $atts);
   ob_start();
   get_template_part('template-parts/page/content', $tp_atts['name']);
   return ob_get_clean();
}

function info_shortcode($atts){
   $tp_atts = shortcode_atts(array( 
      'name' =>  null,
   ), $atts);
   echo "<p class='".str_replace(" ", "", strtolower($tp_atts['name']))."'>";
   infoth($tp_atts['name']);
   echo "</p>";
}

function get_copyright($atts){
   $tp_atts = shortcode_atts(array( 
      'date' =>  "2021",
   ), $atts);
   return get_years_copyright($tp_atts["date"]);
}

function widget_shortcode($atts){
   $tp_atts = shortcode_atts(array(
      'id' => null
   ), $atts);
   ob_start();
   echo "<section class='widget-item' id='".$tp_atts["id"]."'>";
   dynamic_sidebar($tp_atts["id"]);
   echo "</section>";
   return ob_get_clean();
}

function sitename_shortcode($atts){ return get_infoth('sitename', true); }

function shortcodes_init(){
   add_shortcode('post_type', 'wpd_shortcode');
   add_shortcode('content', 'content_shortcode');
   add_shortcode('info', 'info_shortcode');
   add_shortcode('copyright', 'get_copyright');
   add_shortcode('widget', 'widget_shortcode');
   add_shortcode('sitename', 'sitename_shortcode');
   shortcodes_child();
}
add_action('init', 'shortcodes_init');