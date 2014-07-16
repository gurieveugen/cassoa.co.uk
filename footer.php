<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
global $caravan_theme_options, $current_user;
?>
		<footer id="footer">
			<?php wp_nav_menu( array(
			'container' => 'nav',
			'container_class'    => 'nav-footer',
			'theme_location' => 'footer_nav'
			)); ?>
			<?php echo wpautop($caravan_theme_options['footer_text']); ?>
			<p class="copy"><?php echo $caravan_theme_options['copyright_text']; ?></p>
		</footer>
	<?php wp_footer(); ?>
	</div>
	<?php if (is_user_logged_in() && in_array('subscriber', $current_user->roles)) { ?>
	<script type="text/javascript">automatic_logout();</script>
	<?php } ?>
</body>
</html>
