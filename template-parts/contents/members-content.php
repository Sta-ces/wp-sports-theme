<?php $member = get_member(get_the_ID()); ?>
<?php get_header(); ?>
<section id="main-wrap" class="page-template">
    <?php get_template_part("template-parts/main-menu"); ?>
    <header class="main-header member-header bg_image bg_image_fixed <?php infoth('style-header'); ?>" style="background-image: url('<?php echo get_background_image(); ?>');">
        <section class="flex justify-between">
            <figure class="flex justify-start">
                <img class="member-profil-image" src="<?php echo $member->getImgUrl(); ?>" alt="<?php echo get_the_title(); ?>">
                <figcaption>
                    <?php if(!empty($member->getRole())): ?>
                        <h3 class="role"><?php echo $member->getRole(); ?></h3>
                    <?php endif; ?>
                    <?php if(!empty($member->getFullname(false))): ?>
                        <h1 class="fullname"><?php echo $member->getFirstname()."<br>".$member->getLastname(); ?></h1>
                    <?php endif; ?>
                    <?php if(!empty($member->getNickname())): ?>
                        <h2 class="nickname italic"><?php echo $member->getNickname(); ?></h2>
                    <?php endif; ?>
                </figcaption>
            </figure>
            <?php if($member->hasTeam()): ?>
                <div class="member-teams-list right">
                    <?php $number = !$member->hasTeam() ? 1 : count($member->getTeams()); ?>
                    <h4 class="small"><?php echo ngettext(_st("Team"), _st("Teams"), $number); ?></h4>
                    <ul>
                        <?php foreach($member->getTeams() as $key => $team): ?>
                            <li><strong><?php echo $team->name; ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>
        <table class="post-informations">
            <tbody>
                <tr class="center">
                    <td>
                        <em class="small block"><?php _ste("Tall"); ?></em>
                        <strong><?php echo $member->getHeight(); ?></strong>
                    </td>
                    <td>
                        <em class="small block"><?php _ste("Weight"); ?></em>
                        <strong><?php echo $member->getWeight(); ?></strong>
                    </td>
                    <td>
                        <em class="small block"><?php _ste("Age"); ?></em>
                        <strong><?php echo $member->getAge(); ?></strong>
                    </td>
                    <td>
                        <em class="small block"><?php _ste("Country"); ?></em>
                        <strong><?php echo $member->getCountry(); ?></strong>
                    </td>
                    <td>
                        <?php $number = $member->getExperience() === 0 ? 1 : $member->getExperience(); ?>
                        <em class="small block"><?php echo ngettext(_st("Experience"), _st("Experiences"), $number); ?></em>
                        <strong><?php echo $member->getExperience()." ".ngettext(_st("year"), _st("years"), $number); ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>
    <main id="main-content" class="main-content member-content" data-section-scrolling="main-container">
        <article class="post">
            <?php if(get_the_content() !== ""): ?>
                <div class="post-content"><?php the_content(); ?></div>
            <?php endif; ?>
            <?php
                if($member->hasTeam()){
                    foreach($member->getTeams() as $key => $team)
                        echo do_shortcode("[members_posts type='teams' team_id='{$team->term_id}' excludes='{$member->getID()}']");
                }
            ?>
        </article>
    </main>
</section>
<?php get_footer(); ?>