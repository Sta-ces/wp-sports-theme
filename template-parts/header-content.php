<?php get_template_part("template-parts/main-menu"); ?>
<header class="main-header bg_image bg_image_fixed <?php infoth('style-header'); ?>" style="background-image: url('<?php infoth("thumbnail-url"); ?>');">
    <?php if($args['page'] === "homepage"): ?>
        <?php YTVideo(get_infoth('video-header')); ?>
        <?php if(get_infoth('is-text-header')): ?>
            <h1 class="right"><?php infoth("sitename"); ?></h1>
            <h2 class="right"><?php infoth("slogan"); ?></h2>
            <hr data-align="right">
            <section class='header-social-media'><?php get_template_part( 'template-parts/social-media' ); ?></section>
        <?php endif; ?>
        <?php echo getWidget('header-info'); ?>
    <?php else: ?>
        <h1 class="center"><?php the_title(); ?></h1>
        <?php if($args['page'] === "single" && get_infoth('date-article')): ?>
            <p class="date-post center italic marg-20"><?php echo get_the_date(); ?></p>
        <?php endif; ?>
    <?php endif; ?>
</header>