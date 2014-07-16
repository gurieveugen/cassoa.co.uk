<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
?>

<?php if ( have_posts() ) : ?>
	<div class="posts-holder">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-meta">
					<a href="<?php the_permalink() ?>" rel="bookmark">
						<span class="entry-date"><?php the_time('d/m/Y') ?></span>
					</a> 
				</div>
				<div class="text">
					<?php the_post_thumbnail('archive-thumbnail'); ?> 
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<?php 
							if($pos=strpos($post->post_content, '<!--more-->')){
								echo '<div class="entry-content">';
								the_content('read more »');
								echo '</div>';
							}
							else{
								echo '<div class="entry-summary">';
								the_excerpt();
								echo '</div>';
							}
						?>
				</div>
			</article> <!-- .post-holder -->
		<?php endwhile; ?>
	</div>
	<?php else: ?>
		<div id="post-0" class="post no-results not-found">
			<div class="entry-header">
				<h1 class="entry-title"><?php _e( 'Nothing Found', 'theme' ); ?></h1>
			</div>
			<div class="entry-content">
				<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'theme' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div>
<?php endif; ?>