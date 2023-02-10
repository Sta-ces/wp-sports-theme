<?php $activity = get_activity(get_the_ID(), get_post_type()); ?>
<?php get_header(); ?>
<section id="main-wrap" class="page-template">
    <?php get_template_part("template-parts/main-menu"); ?>
    <header class="main-header bg_image bg_image_fixed <?php infoth('style-header'); ?>" style="background-image: url('<?php infoth("thumbnail-url"); ?>');">
        <h1 class="center title-posts" style="margin-left: 0;">
            <?php
                echo ($activity->isMatch()) ? "<span class='carybe-font' style='font-size: .65em; margin-right: 10px;'>vs</span>{$activity->getOpponent()}" : get_the_title();
            ?>
        </h1>
        <h2 class="center"><?php echo $activity->getFullDate(false); ?></h2>
        <h3 class="center small italic"><?php echo ($activity->isHome()) ? get_infoth('address') :$activity->getLocation(); ?></h3>
        <table class="post-informations">
            <tbody>
                <tr class="center">
                    <td>
                        <em class="small block"><?php _ste("Type"); ?></em>
                        <strong class="capitalize"><?php echo !empty($activity->getPrefix()) ? $activity->getPrefix() : preg_replace("/s$/","",_st(get_post_type())); ?></strong>
                    </td>
                    <td>
                        <em class="small block"><?php _ste("Date"); ?></em>
                        <strong class="capitalize"><?php createDate($activity->getDateStart(), "d F Y"); ?></strong>
                    </td>
                    <?php if($activity->isMatch()): ?>
                        <td>
                            <em class="small block"><?php _ste("Team"); ?></em>
                            <strong class="capitalize"><?php echo $activity->getTeamName(); ?></strong>
                        </td>
                        <td>
                            <em class="small block"><?php _ste("Opponent"); ?></em>
                            <strong class="capitalize"><?php echo $activity->getOpponent(); ?></strong>
                        </td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
    </header>
    <main id="main-content" class="main-content activities-content" data-section-scrolling="main-container">
        <article class="post">
            <?php if(get_the_content() !== ""): ?>
                <div class="post-content"><?php the_content(); ?></div>
            <?php endif; ?>
            <?php
                $team_id = $activity->getTeam() ? $activity->getTeam()->term_id : null;
                echo do_shortcode("[activities_posts type='".get_post_type()."' style='grid' excludes='{$activity->getID()}' team_id='{$team_id}']");
            ?>
        </article>
    </main>
</section>
<?php get_footer(); ?>