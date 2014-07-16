<?php
/*
Template Name: Full width Page
*/
?>
<?php get_header(); ?>
<div id="main" class="circle full-width main-page">
	<h1><?php the_title(); ?></h1>
	<div id="content" role="main">
	
	<?php while ( have_posts() ) : the_post(); ?>
	
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_post_thumbnail('244,209', array('class' => 'alignright featured')); ?> 
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'theme' ) . '</span>', 'after' => '</div>' ) ); ?>
				<?php edit_post_link( __( 'Edit', 'theme' ), '<span class="edit-link">', '</span>' ); ?>
			</div>
		</div>
	
	<?php endwhile; ?>
	
	</div>
</div>

<?php get_footer(); ?>
