<?php if(in_array(get_post_type(), get_npts())): ?>
    <?php get_template_part("template-parts/contents/".get_post_type(), "content"); ?>
<?php else: ?>
    <?php get_template_part('page', null, ['page' => 'single']); ?>
<?php endif; ?>