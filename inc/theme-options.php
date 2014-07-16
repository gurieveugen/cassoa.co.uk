<?php

function caravan_admin_add_options_page() {

	add_theme_page(

		'Theme Options', // meta title

		'Theme Options', // admin menu title

		8,

		'theme-options',

		'caravan_theme_options_page'

	);

}



function caravan_theme_options_page() {

	$caravan_action_message = '';

	$caravan_theme_options = get_option("caravan_theme_options");

	if ($_POST['caravan_form_submit'] == 'submit') {

		foreach($_POST as $pkey => $pval) { $_POST[$pkey] = stripcslashes($pval); }

		foreach($_POST as $pkey => $pval) {

			if ($pkey != 'caravan_form_submit') {

				$caravan_theme_options[$pkey] = $pval;

			}

		}

		update_option("caravan_theme_options", $caravan_theme_options);

		$caravan_action_message = 'Options Saved.';

	}

	$iapages = get_pages();

?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php echo __('Theme Options'); ?></h2><br>

		<form method="post" method="POST">

		<input type="hidden" name="caravan_form_submit" value="submit">

		<?php if(strlen($caravan_action_message)) { ?><div id="message" class="updated fade"><p><?php _e($caravan_action_message) ?></p></div><?php } ?>

		<table style="width:auto;">

		  <tr>

			<td colspan="2"><strong>Pages:</strong></td>

		  </tr>

		  <tr>

			<td>Members Home page:&nbsp;</td>

			<td><select name="members_home_page">

				<option value="">-- Select page --</option>

				<?php foreach($iapages as $iapage) { $s = ''; if ($iapage->ID == $caravan_theme_options['members_home_page']) { $s = ' SELECTED'; } ?>

					<option value="<?php echo $iapage->ID; ?>"<?php echo $s; ?>><?php if ($iapage->post_parent) { echo '&nbsp;&nbsp;&nbsp;&nbsp;'; } ?><?php echo $iapage->post_title; ?></option>

				<?php } ?>

			</select></td>

		  </tr>

		  <tr>

			<td>Members Search page:&nbsp;</td>

			<td><select name="members_search_page">

				<option value="">-- Select page --</option>

				<?php foreach($iapages as $iapage) { $s = ''; if ($iapage->ID == $caravan_theme_options['members_search_page']) { $s = ' SELECTED'; } ?>

					<option value="<?php echo $iapage->ID; ?>"<?php echo $s; ?>><?php if ($iapage->post_parent) { echo '&nbsp;&nbsp;&nbsp;&nbsp;'; } ?><?php echo $iapage->post_title; ?></option>

				<?php } ?>

			</select></td>

		  </tr>

		  <tr>

			<td>Individual Member Info page:&nbsp;</td>

			<td><select name="individual_member_page">

				<option value="">-- Select page --</option>

				<?php foreach($iapages as $iapage) { $s = ''; if ($iapage->ID == $caravan_theme_options['individual_member_page']) { $s = ' SELECTED'; } ?>

					<option value="<?php echo $iapage->ID; ?>"<?php echo $s; ?>><?php if ($iapage->post_parent) { echo '&nbsp;&nbsp;&nbsp;&nbsp;'; } ?><?php echo $iapage->post_title; ?></option>

				<?php } ?>

			</select></td>

		  </tr>

		  <tr><td colspan="2">&nbsp;</td></tr>

		  <tr>

			<td colspan="2"><strong>Header:</strong></td>

		  </tr>

		  <tr>

			<td>Contact Text:&nbsp;</td>

			<td><input type="text" name="contact_text" value="<?php echo htmlspecialchars($caravan_theme_options['contact_text']); ?>" style="width:400px;"></td>

		  </tr>

		  <tr><td colspan="2">&nbsp;</td></tr>

		  <tr>

			<td colspan="2"><strong>Footer:</strong></td>

		  </tr>

		  <tr>

			<td>Footer Text:&nbsp;</td>

			<td><textarea name="footer_text" style="width:400px; height:100px;"><?php echo htmlspecialchars($caravan_theme_options['footer_text']); ?></textarea></td>

		  </tr>

		  <tr>

			<td>Copyright Text:&nbsp;</td>

			<td><input type="text" name="copyright_text" value="<?php echo htmlspecialchars($caravan_theme_options['copyright_text']); ?>" style="width:400px;"></td>

		  </tr>

		  <tr><td colspan="2">&nbsp;</td></tr>
		  <tr>
			<td colspan="2"><strong>Site settings:</strong></td>
		  </tr>
		  <tr>
			<td>Lost Password Text:&nbsp;</td>
			<td><textarea name="lost_password_text" style="width:400px; height:100px;"><?php echo htmlspecialchars($caravan_theme_options['lost_password_text']); ?></textarea></td>
		  </tr>
		  <tr>
			<td>Find Site results number:&nbsp;</td>
			<td><input type="text" name="find_site_per_page" value="<?php echo htmlspecialchars($caravan_theme_options['find_site_per_page']); ?>" style="width:40px;"></td>
		  </tr>

		</table>

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save') ?>" /></p>

		</form>

	</div>

<?php

}



add_action('admin_menu', 'caravan_admin_add_options_page');

?>