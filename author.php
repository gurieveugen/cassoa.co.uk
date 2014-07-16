<?php
/**
 *
 * @package WordPress
 * @subpackage Base_theme
 */
?>
<?php get_header(); ?>

		<div id="content" role="main">

			<?php global $post; setup_postdata($post); ?>

			<h1 class="page-title author">
				Author Archives: 
				<span class="vcard">
					<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?> " title="<?php echo esc_attr( get_the_author() ) ?>" rel='me'><?php the_author() ?></a>
				</span>
			</h1>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<div id="entry-author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?>
				</div>
				<div id="author-description">
					<h2>About <?php the_author() ?></h2>
					<?php the_author_meta( 'description' ); ?>
				</div>
			</div>
		<?php endif; ?>

			<?php include('loop.php'); ?>
		</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
