<?php
/**
 *
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<?php get_header(); ?>

<div id="main" class="single-page">
	<?php if (is_user_logged_in()) { ?>
		<h1><?php echo get_the_title(get_option('page_for_posts')); ?></h1>
		<div class="twocolumns">
			<?php include(TEMPLATEPATH.'/sidebar-left.php'); ?>
			<div id="content-gray" role="main">
				<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-meta">
						<span class="entry-date"><?php the_date('d/m/Y') ?></span>
					</div>
					<div class="entry-content">
						<?php the_post_thumbnail('single-thumbnail', array('class' => 'alignright')); ?> 
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
					</div>
				</article>
			
				<div id="nav-below" class="navigation nav-single">
					<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&#60;&#60;</span> Previous Article', 'theme' ) ); ?></span>
					<span class="nav-next"><?php next_post_link( '%link', __( 'Next Article <span class="meta-nav">&#62;&#62;</span>', 'theme' ) ); ?></span>
				</div>
				<?php endwhile; ?>
			</div>
		</div>
	<?php } else { ?>
		<p>You are not allowed to view this page.</p>
	<?php } ?>
</div>

<?php get_footer(); ?>
