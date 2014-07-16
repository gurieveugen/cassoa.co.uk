<?php
/*
Template Name: Members Inner
*/
?>

<?php get_header(); ?>

<div id="main" class="member-home circle">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<div class="twocolumns">
			<?php include(TEMPLATEPATH.'/sidebar-left.php'); ?>
			<div id="content">
				<?php the_post_thumbnail(array(244, 209),  array('class' => 'alignright featured')); ?> 
				<?php the_content(); ?>
			</div>
		</div>
	<?php endwhile; ?>
	</div>

<?php get_footer(); ?>
