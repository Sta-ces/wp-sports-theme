<?php $type = (isset($args['type'])) ? get_post_type() : "all"; ?>
<?php get_header(); ?>
<section id="main-wrap" class="page-template">
    <?php get_template_part("template-parts/main-menu"); ?>
    <header class="main-header bg_image bg_image_fixed <?php infoth('style-header'); ?>" style="background-image: url('<?php echo get_background_image(); ?>');">
        <h1 class="center title-posts upper" style="margin-left: 0;"><?php _ste(get_post_type()); ?></h1>
    </header>
    <main id="main-content" class="main-content <?php echo $args['type'].'-content'; ?>" data-section-scrolling="main-container">
        <article class="post">
            <?php echo do_shortcode("[".$args['type']."_posts type='".$type."' style='list']"); ?>
        </article>
    </main>
</section>
<?php get_footer(); ?>