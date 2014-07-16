<?php
/*
Template Name: Members Home
*/
?>

<?php get_header(); ?>

<div id="main" class="member-home circle">
	<?php if (is_user_logged_in()) { ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<div class="twocolumns">
			<?php include(TEMPLATEPATH.'/sidebar-left.php'); ?>
			<div id="content">
				<?php the_content(); ?>
			</div>
		</div>
	<?php endwhile; ?>
	<?php } else { ?>
		<p>You are not allowed to view this page.</p>
	<?php } ?>
</div>

<?php get_footer(); ?>
