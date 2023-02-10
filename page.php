<?php get_header(); ?>
<section id="main-wrap" class="page-template">
      <?php get_template_part( 'template-parts/header', 'content', ['page' => (isset($args['page']) ? $args['page'] : null)] ); ?>
      <main id="main-content" class="main-content" data-section-scrolling="main-container">
		<article class="post">
                  <?php if(get_the_content() !== ""): ?>
                        <div class="post-content"><?php the_content(); ?></div>
                  <?php endif; ?>
		</article>
	</main>
</section>
<?php get_footer(); ?>