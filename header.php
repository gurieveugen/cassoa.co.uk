<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
global $caravan_theme_options, $current_user;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo('name'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>">
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo TDU ?>/css/jqtransform.css">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); 
	wp_head(); ?>
	<script type="text/javascript">var js_siteurl = "<?php echo site_url('/'); ?>";</script>
	<script type="text/javascript" src="<?php echo TDU ?>/js/jquery.jqtransform.js"></script>
	<script type="text/javascript" src="<?php echo TDU ?>/js/jquery.cycle.all.js"></script>
	<script type="text/javascript" src="<?php echo TDU ?>/js/jquery.main.js"></script>
	<!--[if lt IE 9]>
		<script src="<?php echo TDU ?>/js/html5.js"></script>
		<script src="<?php echo TDU ?>/js/pie.js"></script>
		<script src="<?php echo TDU ?>/js/init-pie.js"></script>
	<![endif]-->
	<!--[if lte IE 9]>
		<script type="text/javascript" src="<?php echo TDU ?>/js/jquery.placeholder.js"></script>
		<script type="text/javascript">
			 $(function() {
				$('input, textarea').placeholder();
			});
		</script>
	<![endif]-->
	<?php if (isset($_GET['member'])) { ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo TDU ?>/css/colorbox.css">
	<script type="text/javascript" src="<?php echo TDU ?>/js/jquery.colorbox.min.js"></script>
	<script type="text/javascript">jQuery(function() { jQuery(".member-photos a").colorbox({rel:'photo'}); });</script>
	<?php } ?>
</head>
<body <?php body_class(); ?>>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
 
  ga('create', 'UA-5142885-2', 'cassoa.co.uk');
  ga('send', 'pageview');
 
</script>

	<div id="wrapper">
		<header id="header">
			<?php if(is_front_page()): ?>
				<h1 class="logo"><a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
			<?php else: ?>
				<strong class="logo"><a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a></strong>
			<?php endif; ?>
			<div class="holder">
				<div class="login-row">
					<?php if (is_user_logged_in()) { ?>
						<a href="<?php echo get_permalink($caravan_theme_options['members_home_page']); ?>" class="link-members-home">Members' home</a>
						<a href="<?php echo admin_url('profile.php'); ?>" class="link-account">My account</a>
						<a href="<?php echo wp_logout_url(); ?>" class="link-logout">Logout</a>
					<?php } else { ?>
						<a href="<?php echo wp_login_url(); ?>" class="link-login">CaSSOA Storage Owners login</a>
					<?php } ?>
				</div>
				<div class="contact-row">
					<p><?php echo $caravan_theme_options['contact_text']; ?></p>
				</div>
				<?php wp_nav_menu( array(
				'container' => 'nav',
				'container_id'    => 'nav',
				'theme_location' => 'primary_nav'
				)); ?>
			</div>
		</header>