<?php
/**
 *
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<?php get_header(); ?>
<div id="main" class="circle main-page">
	<h1 class="entry-title"><?php the_title(); ?></h1>
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
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
