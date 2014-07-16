<?php
/**
 *
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<?php get_header(); ?>

<div id="main">
	<?php if (is_user_logged_in()) { ?>
		<h1>
			<?php global $post;
			/* If this is a category archive */ if (is_category()) { ?>
			Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category
			<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
			Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;
			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			Archive for <?php the_time('F jS, Y'); ?>
			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			Archive for <?php the_time('F, Y'); ?>
			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			Archive for <?php the_time('Y'); ?>
			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			Author Archive
			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			Blog Archives
			<?php } ?>
		</h1>
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
