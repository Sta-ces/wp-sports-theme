<section class="navigation">
    <?php if(get_infoth('is-logo')): ?>
        <figure id="main-logo" class="main-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
                <img src="<?php infoth("logo-url"); ?>" alt="<?php infoth('author'); ?>" style="height: <?php echo get_infoth( "logo-height" )."%"; ?>; width: auto;" class="<?php echo 'filter-'.get_infoth('filter-color'); ?>">
                <figcaption class="indent"><?php infoth('sitename'); ?></figcaption>
            </a>
        </figure>
    <?php endif; ?>
    <div class="burger" data-menu-scrolling="main-container"></div>
</section>
<section class="main-menu-container">
    <ul id="mainmenu" class="main-menu">
        <?php
            get_mainmenu([
                "nocontainer" => false,
                "class_color_hover" => "wp_color_hover"
            ]);
        ?>
        <li class="social"><?php get_template_part( 'template-parts/social-media' ); ?></li>
    </ul>
</section>