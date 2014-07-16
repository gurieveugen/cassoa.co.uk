<?php
add_action('init', 'ajax_actions_init');
function ajax_actions_init() {
	$AjaxAction = $_POST['AjaxAction'];
	if (strlen($AjaxAction)) {
		if ($AjaxAction == 'member-contact') {
			$name = $_POST['name'];
			$email = $_POST['email'];
			$phone = $_POST['phone'];
			$enquiry = $_POST['enquiry'];
			$member = $_POST['member'];

			$member_data = get_userdata($member);

			$subject = 'Enquiry from '.$name;
			$message = 'Name: '.$name.chr(10);
			$message .= 'Email: '.$email.chr(10);
			$message .= 'Phone: '.$phone.chr(10);
			$message .= 'Enquiry:'.chr(10).$enquiry.chr(10);
			$headers = "From: ".$name." <".$email.">\r\n";
			wp_mail($member_data->user_email, $subject, $message, $headers);
		}
		exit;
	}
}
?>