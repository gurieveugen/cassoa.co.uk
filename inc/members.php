<?php
function members_contactmethods($usercontactmethods) {
	$ucontactmethods['contact_email'] = 'Contact email';
	$ucontactmethods['alternative_email'] = 'Alternative contact email';
	$ucontactmethods['membership_no'] = 'Membership No.';
	$ucontactmethods['address1'] = 'Address Line 1';
	$ucontactmethods['address2'] = 'Address Line 2';
	$ucontactmethods['address3'] = 'Address Line 3';
	$ucontactmethods['address4'] = 'Address Line 4';
	$ucontactmethods['postcode'] = 'Postcode';
	/*$ucontactmethods['city'] = 'City';
	$ucontactmethods['county'] = 'County';
	$ucontactmethods['country'] = 'Country';*/
	$ucontactmethods['phone'] = 'Telephone';
	$ucontactmethods['mobile'] = 'Mobile';
	return $ucontactmethods;
}
add_filter("user_contactmethods", "members_contactmethods");

function is_member_logged() {
	global $current_user;
	if (is_user_logged_in() && (in_array('s2member_level1', $current_user->roles) || in_array('s2member_level2', $current_user->roles) || in_array('s2member_level3', $current_user->roles) || in_array('s2member_level4', $current_user->roles))) {
		return true;
	}
	return false;
}

function is_admin_logged() {
	global $current_user;
	if (is_user_logged_in() && (in_array('administrator', $current_user->roles))) {
		return true;
	}
	return false;
}

function get_cassoa_awards() {
	return array('bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold');
}

function member_get_level($member_customs) {
	$mcapabilities = unserialize($member_customs['wp_capabilities']);
	foreach($mcapabilities as $ck => $cv) {
		switch($ck) {
			case 's2member_level1':
				return 'bronze';
			break;
			case 's2member_level2':
				return 'silver';
			break;
			case 's2member_level3':
				return 'gold';
			break;
			case 's2member_level4':
				return 'platinum';
			break;
		}
		if ($ck) {
		}
	}
}

add_action('init', 'members_init');
function members_init() {
	add_role('customer', 'Customer');
}
?>