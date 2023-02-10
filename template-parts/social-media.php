<?php
/**
 * Template Social Media menu
 *
 * @version 1.0
 */
    $location = get_nav_menu_locations();
    if(!array_key_exists("social", $location)) return;
    $social_menu = wp_get_nav_menu_object($location['social']);
    $social_menu_items = wp_get_nav_menu_items($social_menu->term_id);
    $pattern = '/('.$_SERVER["HTTP_HOST"].')/';
?>
<?php if($social_menu_items && count($social_menu_items)): ?>
<ul class="social-media-wrap">
    <?php foreach($social_menu_items as $key => $value): ?>
        <li>
            <?php $target = (preg_match($pattern, $value->url)) ? "_self" : "_blank"; ?>
            <a href="<?php echo $value->url; ?>" target="<?php echo $target; ?>" title="<?php echo $value->title; ?>"><?php echo stth_get_svg(array("icon" => strtolower($value->title))); ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>