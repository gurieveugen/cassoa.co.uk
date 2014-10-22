<?php
/*
Template Name: Home Page
*/

require_once 'inc/TwitterOAuth.php';

$twitter = new TwitterOAuth(
	'FoRJZBenKUFmIQFLDp2gQ',
	'Kudk8D5ZAxb5tWAoXRO21T47gp6EXRplJ82MEUiqc',
	'532546390-23aT4nDlWpYLA543yUfmExBqFs0RDb9AZBRbNFTd',
	'Mt9Hj9aocqQ7qSQGzowzUkFWpvJx8kyBoLAV9GGfV9kvL'
);

$user   = get_option("caravan_theme_options");	
$user   = $user['twitter_account'];	
$query  = sprintf('https://api.twitter.com/1.1/statuses/user_timeline.json?count=4&screen_name=%s', urlencode($user));
$tweets = $twitter->get($query);		
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
<div class="home-twocolumns">
	<article class="home-text">
		<?php the_content(); ?>
	</article>
	<aside class="aside-twitter">
		<h3>We're on Twitter</h3>
		<ul class="tweet-list">
			
			<?php
			if(is_array($tweets) AND count($tweets))
			{
				foreach ($tweets as $t) 
				{
					$link    = sprintf(
						'https://twitter.com/%s/status/%s', 
						$t->user->screen_name, 
						$t->id_str
					);
					?>
					<li>
						<h4><a href="<?php echo $link; ?>">@<?php echo $t->user->screen_name; ?></a></h4>
						<p><?php echo $t->text; ?> </p>
					</li>
					<?php
				}
			}
			?>
		</ul>
		<div class="btn-holder">
			<a href="https://twitter.com/<?php echo $user;?>">view more</a>
		</div>
	</aside>
</div>

<?php get_footer(); ?>
