<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<?php get_header(); ?>
<div id="main">
	<?php if (is_user_logged_in()) { ?>
		<h1><?php echo get_the_title(get_option('page_for_posts')); ?></h1>
		<div class="twocolumns">
			<?php include(TEMPLATEPATH.'/sidebar-left.php'); ?>
			<?php if ( have_posts() ) : ?>
				<div id="content" class="posts" role="main">
					<?php include("loop.php"); ?>
				</div>
			<?php else : ?>
				<p><?php _e( 'Nothing Found', 'theme' ); ?></p>
			<?php endif; ?>
		</div>
		<?php wp_pagenavi(); ?>
	<?php } else { ?>
		<p>You are not allowed to view this page.</p>
	<?php } ?>
</div>
<?php get_footer(); ?>
