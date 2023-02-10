<?php
/**
 * The template for displaying the footer
 *
 * @version 1.0
 */
?>
	<section class="footer-wrap">
		<nav class="footer-social-media center"><?php if(get_infoth('is-social-media-footer')) get_template_part('template-parts/social-media'); ?></nav>
		<?php get_template_part('template-parts/content', 'maps'); ?>
		<?php echo getWidget('sponsors-container') ?>
		<footer id="colophon" class="site-footer center white" role="contentinfo">
			<section class="copyright"><?php infoth('copyright'); ?></section>
			<a class="logo footer-logo" href="<?php infoth('home-url'); ?>"><img src="<?php infoth('logo-url'); ?>" alt="Logo - <?php infoth('sitename'); ?>"></a>
		</footer>
	</section>
	<?php wp_footer(); ?>
</body>
</html>
