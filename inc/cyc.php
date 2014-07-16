<?php
global $pagenow;

session_start();

add_filter('login_redirect', 'cyc_redirect', 10, 3);
function cyc_redirect($redirect_to, $request_redirect_to, $user) {
	if (is_a($user, 'WP_User') && $user->has_cap('edit_posts') === false) {
		return get_bloginfo('wpurl').'/wp-admin/profile.php'; 
	}
	return $redirect_to;
}

add_action('wp_logout', 'cyc_logout');
function cyc_logout() {
	wp_redirect(get_option('home'));
	wp_exit();
}

if ( $pagenow == "wp-login.php"  && $_GET['action'] != 'logout' && !isset($_GET['key']) ) {
	add_action('init', 'cyc_login_init', 98);
	add_filter('wp_title','cyc_title');
	add_action('wp_head', 'cyc_login_css');
}

function cyc_login_init() {
	require( ABSPATH . '/wp-load.php' );
		
	if (isset($_REQUEST["action"])) {
		$action = $_REQUEST["action"];
	} else {
		$action = 'login';
	}
	switch($action) {
		case 'lostpassword' :
		case 'retrievepassword' :
			cyc_password();
			break;
		case 'register':
			cyc_show_registerform();
			break;
		case 'login':
		default:
			cyc_show_loginform();
			break;
	}
	die();
}

function cyc_title($title) {
	global $pagenow;
	if ($pagenow == "wp-login.php") {
		switch($_GET['action']) {
			case 'register':
				$title = "Register at ";
				break;
			case 'lostpassword':
				$title = "Retrieve your lost password for ";
				break;
			case 'login':
			default:
				$title = "Login at ";
				break;
		}
	} else if ($pagenow == "profile.php") {
		$title = "Your Profile at ";
	}
	return $title;
}

function cyc_login_css ( ) {
?>
	<style type="text/css">
	form.loginform p label {
		width: 150px;
		display: block;
		float: left;
		clear: both;
	}
	form.loginform p input.input {
		width: 150px;
		float: left;
		clear: right;
	}
	form.loginform p img {
		width: 155px;
		float: left;		
	}
	form.loginform, form.loginform p {
		clear: both;
	}
	p.message, p#login_error {
		padding: 3px 5px;
	}
	p.message {
		background-color: lightyellow;
		border: 1px solid yellow;
	}
	p#login_error {
		background-color: lightcoral;
		border: 1px solid red;
		color: #000;
	}
	</style>
<?php
}

function cyc_show_errors($wp_error) {
	global $error;
	
	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( !empty($wp_error) ) {
		if ( $wp_error->get_error_code() ) {
			$errors = '';
			$messages = '';
			foreach ( $wp_error->get_error_codes() as $code ) {
				$severity = $wp_error->get_error_data($code);
				foreach ( $wp_error->get_error_messages($code) as $error ) {
					if ( 'message' == $severity )
						$messages .= '	' . $error . "<br />\n";
					else
						$errors .= '	' . $error . "<br />\n";
				}
			}
			if ( !empty($errors) )
				$admin_email = get_bloginfo('admin_email');
				echo '<p id="login_error">' . apply_filters('login_errors', $errors); 
				echo 'If you have lost or forgotten your password please email <a href="mailto:'.$admin_email.'">'.$admin_email.'</a> or call 0843 216 5802';				
				echo "</p>\n";
				
			if ( !empty($messages) )
				echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
		}
	}
}

function cyc_head($cyc_msg) {
	global $cyc_options;
	get_header(); ?>
	<div id="main" class="full-width main-page">
		<h1><?php echo $cyc_msg; ?></h1>
		<div id="content" role="main">
	<?php
}

function cyc_footer() {
	global $pagenow, $user_ID, $cyc_options;

	if ($pagenow == "wp-login.php") { ?>
		<ul id="cycnav">
			<?php if (isset($user_ID) && $user_ID > 0) { ?>
				<li><a href="<?php echo wp_logout_url(); ?>">Log out</a></li>
			<?php } else { ?>
				<?php if (isset($_GET['action']) && $_GET['action'] != 'login') { ?>
					<li><a href="<?php echo wp_login_url(); ?>">Log in</a></li>
				<?php } ?>
				<?php if ($_GET['action'] != 'lostpassword') { ?>
					<li><a href="<?php echo wp_login_url(); ?>?action=lostpassword">Lost your password?</a></li>
				<?php } ?>
			<?php } ?>
		</ul>
	<?php } ?>
		</div>
	</div>
	<?php get_footer();
}

function cyc_show_registerform() {
	global $cyc_pluginpath, $cyc_options;
	//if ( !get_option('users_can_register') ) {
		wp_redirect(get_bloginfo('wpurl').'/wp-login.php?registration=disabled');
		exit();
	//}

	$user_login = '';
	$user_email = '';
   
	if ( isset($_POST['user_login']) ) {
		if( !$cyc_options['captcha'] || ( $cyc_options['captcha'] && ($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code']) ) ) 
			) {
			unset($_SESSION['security_code']);
			require_once( ABSPATH . WPINC . '/registration.php');

			$user_login = $_POST['user_login'];
			$user_email = $_POST['user_email'];
			$errors = register_new_user($user_login, $user_email);
			if ( !is_wp_error($errors) ) {
				wp_redirect('wp-login.php?checkemail=registered');
				exit();
			}
		} else {
			$user_login = $_POST['user_login'];
			$user_email = $_POST['user_email'];
			$errors = new WP_error();
			$errors->add('captcha', __("<strong>ERROR</strong>: You didn't correctly enter the captcha, please try again."));		
		}
	}
	
	cyc_head("Register");
	cyc_show_errors($errors);
?>
	<form class="loginform" name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">
		<p>
			<label><?php _e('Username') ?>:</label>
			<input tabindex="1" type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" size="20" tabindex="10" />
			<label><?php _e('E-mail') ?>:</label>
			<input tabindex="2" type="text" name="user_email" id="user_email" class="input" value="<?php echo attribute_escape(stripslashes($user_email)); ?>" size="25" tabindex="20" />
<?php if ($cyc_options['captcha']) { ?>
			<label>&nbsp;</label>
			<img alt="captcha" width="155" height="30" src="<?php echo $cyc_pluginpath; ?>captcha.php?width=155&amp;height=30&amp;characters=5" /><br/>
			<label for="security_code">Type the code above:</label>
			<input tabindex="3" id="security_code" name="security_code" class="input" type="text" />
<?php } ?>
		</p>
		<?php do_action('register_form'); ?>
		<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
		<p class="submit"><input tabindex="4" type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Register'); ?>" tabindex="100" /></p>
	</form>
<?php
	cyc_footer();
}

function cyc_show_loginform() {
	if ( isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = $_REQUEST['redirect_to'];
	else
		$redirect_to = admin_url();

	if ( is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;
	else
		$secure_cookie = '';

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) ) {
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' ) )
			$redirect_to = admin_url('profile.php');
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) )
		$errors = new WP_Error();

	cyc_head("Login");	

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));		
	if	( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )			$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )	$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');

	cyc_show_errors($errors);

	// login form
	?>
	<form class="loginform" action="<?php bloginfo('wpurl'); ?>/wp-login.php" method="post" >
		<p>
			<label for="user_login"><?php _e('Username:') ?></label>
			<input name="log" value="<?php echo attribute_escape(stripslashes($_POST['log'])); ?>" class="mid" id="user_login" type="text" />
			<br/>
			<label for="user_pass"><?php _e('Password:') ?></label>
			<input name="pwd" class="mid" id="user_pass" type="password" />
			<br/>
			<input name="rememberme" class="checkbox" id="rememberme" value="forever" type="checkbox" checked="checked"/>
			<label for="rememberme"><?php _e('Remember me'); ?></label>
		</p>
		<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Login'); ?> &raquo;" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
	</form>
	<?php	
	cyc_footer();
}

function cyc_password() {
	global $caravan_theme_options;
	$errors = new WP_Error();
	if ( $_POST['user_login'] ) {
		$errors = retrieve_password();
		if ( !is_wp_error($errors) ) {
			wp_redirect('wp-login.php?checkemail=confirm');
			exit();
		}
	}
	
	if ( 'invalidkey' == $_GET['error'] ) 
		$errors->add('invalidkey', __('Sorry, that key does not appear to be valid.'));

	$errors->add('registermsg', __('Please enter your username or e-mail address. You will receive a new password via e-mail.'), 'message');
	do_action('lost_password');
	do_action('lostpassword_post');
	cyc_head("Lost Password");

	//cyc_show_errors($errors);
?>
	<p><?php echo $caravan_theme_options['lost_password_text']; ?></p>
	<!--<form class="loginform" name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post">
		<p>
			<label><?php _e('Username or E-mail:') ?></label>
			<input type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes($_POST['user_login'])); ?>" size="20" tabindex="10" />
		</p>
		<br/>
		<?php do_action('lostpassword_form'); ?>
		<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Get New Password'); ?>" tabindex="100" /></p>
	</form>-->
<?php
	cyc_footer();
}

if ( !isset($_POST['from']) && $_POST['from'] != 'profile' ) {
	add_action('load-profile.php', 'cyc_profile_init', 98);
}
function cyc_profile_init() {
	global $current_user, $wpdb;
	$user_id = $current_user->ID;
	if ( !$user_id ) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	}
	// If current user can see more of the admin area then just his profile, doing all this makes no sense.
	if ($current_user->has_cap('edit_posts') === false) {
		$is_profile_page = true;
	    add_filter('wp_title','cyc_title');
		
		wp_reset_vars(array('action', 'redirect', 'profile', 'user_id', 'wp_http_referer'));
		$wp_http_referer = remove_query_arg(array('update', 'delete_count'), stripslashes($wp_http_referer));
	
		if ( !current_user_can('edit_user', $user_id) ) {
			wp_die(__('You do not have permission to edit this user.'));
		}

		// user fields
		$member_customs = array();
		$member_custom_fields = $wpdb->get_results(sprintf("SELECT * FROM %susermeta WHERE user_id = %s", $wpdb->prefix, $user_id));
		foreach($member_custom_fields as $member_custom_field) {
			$member_customs[$member_custom_field->meta_key] = $member_custom_field->meta_value;
		}
		
		if ($_POST['profile_action'] == 'update') {
			$email = $_POST['email'];
			$contact_email = $_POST['contact_email'];
			$address1 = $_POST['address1'];
			$address2 = $_POST['address2'];
			$address3 = $_POST['address3'];
			$city = $_POST['city'];
			$county = $_POST['county'];
			$postcode = $_POST['postcode'];
			$country = $_POST['country'];
			$phone = $_POST['phone'];
			$mobile = $_POST['mobile'];
			$website = $_POST['url'];
			$site_name = $_POST['site_name'];
			$contact_names = $_POST['contact_names'];
			$introduction = $_POST['introduction'];
			$directions = $_POST['directions'];
			$pricing = $_POST['pricing'];
			$cassoa_award = $_POST['cassoa_award'];
			$reservations = $_POST['reservations'];
			$on_site_information = $_POST['on_site_information'];
		} else {
			$email = $current_user->user_email;
			$contact_email = $member_customs['contact_email'];
			$address1 = $member_customs['address1'];
			$address2 = $member_customs['address2'];
			$address3 = $member_customs['address3'];
			$city = $member_customs['city'];
			$county = $member_customs['county'];
			$postcode = $member_customs['postcode'];
			$country = $member_customs['country'];
			$phone = $member_customs['phone'];
			$mobile = $member_customs['mobile'];
			$website = $current_user->user_url;
			$site_name = $member_customs['site_name'];
			$contact_names = $member_customs['contact_names'];
			$introduction = $member_customs['introduction'];
			$directions = $member_customs['directions'];
			$pricing = $member_customs['pricing'];
			$reservations = $member_customs['reservations'];
			$on_site_information = $member_customs['on_site_information'];
		}
		
		if( wp_attachment_is_image($member_customs['photo'])){
			$photo = $member_customs['photo'];
		}
		
		$photos = array();
		if ($member_customs['photos']) {
			$member_photos = unserialize($member_customs['photos']);
			if (is_array($member_photos)) {
				foreach($member_photos as $photo_id){
					if( wp_attachment_is_image($photo_id)){ 
						$photos[] = $photo_id;
					}
				}
			}
		}
		
		$cassoa_award = $member_customs['cassoa_award'];
		if (!$photos && $photo) { $photos[] = $photo; }

		cyc_head(__('My account'));
		if ($_GET['updated'] == true) {
			echo '<p class="message" style="padding: 5px 7px; background-color: lightyellow; border: 1px solid #EEE;">Your account has been updated. <a href="'.get_bloginfo('url').'/individual-member-info/?member='.$user_id.'" target="_blank">Please preview your listing</a></p>';
		}
 		?>
		<style>
		#main ul.member-photos {
			width:240px;
		}
		#main ul.member-photos li {
			list-style:none;
			background:none;
			padding:0;
			margin:0 10px 10px 0;
			float:left;
		}
		</style>
		<form name="profile" id="your-profile" method="POST" enctype="multipart/form-data" onsubmit="return profile_presubmit();">
		<p class="none-editable">*These fields are non-editable</p>
		<div class="profile-column">
			<table class="form-table">
				<tr>
					<th><label for="site_name">Site Name*</label></th>
					<td id="site-name"><input type="text" name="site_name" readonly="true" value="<?php echo $site_name; ?>" /></td>
				</tr>				
				<tr>
					<th><label for="address1">Address Line 1</label></th>
					<td id="address1"><input type="text" name="address1" value="<?php echo $address1; ?>" /></td>
				</tr>
				<tr>
					<th><label for="address2">Address Line 2</label></th>
					<td><input type="text" name="address2" value="<?php echo $address2; ?>" /></td>
				</tr>
				<tr>
					<th><label for="address3">Address Line 3</label></th>
					<td><input type="text" name="address3" value="<?php echo $address3; ?>" /></td>
				</tr>
				<tr>
					<th><label for="address3">Address Line 4</label></th>
					<td><input type="text" name="address4" value="<?php echo $address4; ?>" /></td>
				</tr>
				<tr>
					<th><label for="postcode">Postcode</label></th>
					<td id="postcode"><input type="text" name="postcode" value="<?php echo $postcode; ?>" /></td>
				</tr>
				<tr>
					<th><label for="phone">Telephone</label></th>
					<td id="phone"><input type="text" name="phone" value="<?php echo $phone; ?>" /></td>
				</tr>
				<tr>
					<th><label for="mobile">Mobile</label></th>
					<td id="mobile"><input type="text" name="mobile" value="<?php echo $mobile; ?>" /></td>
				</tr>
				<tr>
					<th><label for="website">Website</label></th>
					<td><input type="text" name="url" value="<?php echo $website; ?>" /></td>
				</tr>
				<tr>
					<th><label for="contact_email">Contact Email</label></th>
					<td id="contact_email"><input type="text" name="contact_email" value="<?php echo $contact_email; ?>" /></td>
				</tr>
				<tr>
					<th><label for="new_pass">New Password</label></th>
					<td id="new_pass"><input type="password" name="pass1" value="" /></td>
				</tr>
				<tr>
					<th><label for="new_pass2">Confirm Password</label></th>
					<td id="new_pass2"><input type="password" name="pass2" value="" /></td>
				</tr>
				<tr>
					<th><label for="cassoa_award">CaSSOA Award*</label></th>
					<td><?php if (strlen($cassoa_award)) { ?><img src="<?php echo TDU ?>/images/ico-<?php echo $cassoa_award; ?>-award.png" alt=" "><?php } ?></td>
				</tr>				
				<?php if ($photos) { ?>
				<tr>
					<th>&nbsp;</th>
					<td><ul class="member-photos">
						<?php foreach($photos as $photoid) { ?>
						<li><img src="<?php echo get_thumb((int)$photoid, 110, 90, true); ?>" /><br><input type="radio" name="mainphoto" value="<?php echo $photoid; ?>"<?php if ($photo == $photoid) { echo ' CHECKED'; } ?>> main photo<br><input type="checkbox" name="delphoto[]" value="<?php echo $photoid; ?>"> <font style="color:#FF0000;">delete photo</font></li>
						<?php } ?>
					</ul></td>
				</tr>
				<?php } ?>
				<tr>
					<th><label for="photo">Upload Photo</label></th>
					<td><input type="file" name="photo" /></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<td><input type="submit" value="Submit" /></td>
				</tr>
			</table>
		</div>
		<div class="profile-column">
			<table class="form-table">
				<tr>
					<th><label for="introduction">Introduction</label></th>
					<td id="introduction"><textarea name="introduction" cols="10" style="height: 140px;"><?php echo $introduction; ?></textarea></td>
				</tr>
				<tr>
					<th><label for="directions">Directions</label></th>
					<td id="directions"><textarea name="directions" cols="10" style="height: 140px;"><?php echo $directions; ?></textarea></td>
				</tr>
				<tr>
					<th><label for="pricing">Pricing</label></th>
					<td id="pricing"><textarea name="pricing" cols="10" style="height: 80px;"><?php echo $pricing; ?></textarea></td>
				</tr>

				<tr>
					<th><label for="reservations">Reservations</label></th>
					<td id="reservations"><textarea name="reservations" cols="10" style="height: 140px;"><?php echo $reservations; ?></textarea></td>
				</tr>
				<tr>
					<th><label for="on_site_information">On-site information</label></th>
					<td id="on-site-information"><textarea name="on_site_information" cols="10" style="height: 140px;"><?php echo $on_site_information; ?></textarea></td>
				</tr>
			</table>
		</div>
		<?php wp_nonce_field('update-user_' . $user_id) ?>
		<?php if ( $wp_http_referer ) : ?>
			<input type="hidden" name="wp_http_referer" value="<?php echo clean_url($wp_http_referer); ?>" />
		<?php endif; ?>
		<input type="hidden" name="from" value="profile" />
		<input type="hidden" name="checkuser_id" value="<?php echo $user_id ?>" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="profile_action" value="update" />
		<input type="hidden" name="email" value="<?php echo $email; ?>" />
		<?php
		do_action('profile_personal_options');
		do_action('show_user_profile');
		?>
		</form>
	<?php
		cyc_footer();
		die();
	}
}

function cyc_update_profile() {
	global $current_user, $wpdb;
	$user_id = $current_user->ID;

	$site_name = trim($_POST['site_name']);
	$contact_names = trim($_POST['contact_names']);
	$contact_email = trim($_POST['contact_email']);
	$introduction = trim($_POST['introduction']);
	$directions = trim($_POST['directions']);
	$pricing = trim($_POST['pricing']);
	$reservations = trim($_POST['reservations']);
	$on_site_information = trim($_POST['on_site_information']);
	$mainphoto = $_POST['mainphoto'];
	$delphoto = $_POST['delphoto'];

	if (!is_array($delphoto)) { $delphoto = array(); }
	if (in_array($mainphoto, $delphoto)) { $mainphoto = ''; }

	update_user_meta($user_id, 'site_name', $site_name);
	update_user_meta($user_id, 'contact_names', $contact_names);
	update_user_meta($user_id, 'contact_email', $contact_email);
	update_user_meta($user_id, 'introduction', $introduction);
	update_user_meta($user_id, 'directions', $directions);
	update_user_meta($user_id, 'pricing', $pricing);
	update_user_meta($user_id, 'reservations', $reservations);
	update_user_meta($user_id, 'on_site_information', $on_site_information);

	$member_photos = get_user_meta($user_id, 'photos', true);
	$photos = array();
	if ($member_photos) {
		foreach($member_photos as $mph) {
			if (!in_array($mph, $delphoto)) {
				$photos[] = $mph;
			}
		}
	}

	// photo upload
	if (strlen($_FILES['photo']['name'])) {
		require_once('includes/image.php');
		require_once('includes/file.php');
		require_once('includes/media.php');
		$photoid = media_handle_upload('photo', 0);
		if ($photoid > 0) {
			$photos[] = $photoid;
		}
	}
	if (!$mainphoto) { $mainphoto = $photos[0]; }

	update_user_meta($user_id, 'photo', $mainphoto);
	update_user_meta($user_id, 'photos', $photos);

	// delete photos
	if ($delphoto) {
		foreach($delphoto as $dph) {
			wp_delete_attachment($dph, true);
		}
	}
}
if ($pagenow == "profile.php" && $_POST['action'] == 'update') {
	add_action('init', 'cyc_update_profile');
}

add_action('init', 'automatic_logout_init');
function automatic_logout_init() {
	global $current_user;
	if (is_user_logged_in() && in_array('subscriber', $current_user->roles)) {
		$ctime = (int)date('YmdHis');
		if ($_POST['autologout'] == 'true') {
			$ltime = $ctime;
			if ($_SESSION['logged_activity']) {
				$ltime = (int)$_SESSION['logged_activity'];
			}
			$tdiff = $ctime - $ltime;
			if ($tdiff >= 500) {
				unset($_SESSION['logged_activity']);
				wp_logout();
			}
			exit;
		} else {
			$_SESSION['logged_activity'] = $ctime;
		}
	}
}
?>