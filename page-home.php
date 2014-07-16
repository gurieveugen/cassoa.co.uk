<?php
/*
Template Name: Home Page
*/
?>

<?php get_header(); ?>

<section class="visual" id="home-slider">
	<?php $home_slides = get_posts('post_type=home-slide&posts_per_page=-1&orderby=menu_order&order=asc');
	if ($home_slides) : $sn = 1; ?>
	<ul class="slider">
		<?php foreach($home_slides as $home_slide) { $hs_image = get_post_thumbnail_id($home_slide->ID); ?>
		<li class="slide-<?php echo $sn; ?>">
			<?php if ($hs_image) { ?><img src="<?php echo get_thumb($hs_image, 1020, 280, true); ?>" alt=" "><?php } ?>
			<div class="text">
				<?php echo apply_filters('the_content', $home_slide->post_content); ?>
			</div>
		</li>
		<?php $sn++; } ?>
	</ul>
	<ul class="switcher"></ul>
	<?php endif; ?>
</section>
<section class="info-blocks">
	<?php if ( is_active_sidebar('home-blocks') ) : ?>
		<?php dynamic_sidebar('home-blocks'); ?>
	<?php endif; ?>
</section>
<article class="home-text">
	<?php the_content(); ?>
</article>

<?php get_footer(); ?>
