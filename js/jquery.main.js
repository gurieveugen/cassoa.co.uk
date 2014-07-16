jQuery(function() {
	jQuery('.select, #your-profile select').jqTransform();
	jQuery('#home-slider ul.slider').cycle({
		speed: 1500,
		timeout:4000,
		pager: '#home-slider ul.switcher',
		activePagerClass: 'active',
		pagerAnchorBuilder: function(index, dom) {
			return '<li><a href="#"></a></li>';
		}
	});
});

function member_contact_presubmit() {
	var error = false;
	var name = jQuery('#mc-name').val();
	var email = jQuery('#mc-email').val();
	var phone = jQuery('#mc-phone').val();
	var enquiry = jQuery('#mc-enquiry').val();
	var member = jQuery('#mc-member').val();

	jQuery('.member-contact-success').hide();
	jQuery('.member-profile-contact input, .member-profile-contact textarea').removeClass('error');

	if (name == '') { jQuery('#mc-name').addClass('error'); error = true; }
	if (email == '') { jQuery('#mc-email').addClass('error'); error = true; }
	else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email)) { jQuery('#mc-email').addClass('error'); error = true; }
	//if (phone == '') { jQuery('#mc-phone').addClass('error'); error = true; }
	if (enquiry == '') { jQuery('#mc-enquiry').addClass('error'); error = true; }
	if (!error) {
		jQuery.post(
			js_siteurl,
			{
				AjaxAction: 'member-contact',
				name: name,
				email: email,
				phone: phone,
				enquiry: enquiry,
				member: member
			},
			function(data) {
				jQuery('.member-contact-success').animate({height: 'show'}, 300);
				document.member_profile_contact.reset();
			}
		);
	}
	return false;
}

function profile_presubmit() {
	var error = '';
	//var email = jQuery('#your-profile #email input').val();
	var contact_email = jQuery('#your-profile #contact_email input').val();
	var address1 = jQuery('#your-profile #address1 input').val();
	var postcode = jQuery('#your-profile #postcode input').val();
	var phone = jQuery('#your-profile #phone input').val();
	var site_name = jQuery('#your-profile #site-name input').val();

	jQuery('#your-profile table tr').removeClass('error');

	//if (email == '') { if (error != '') { error += ';'; } error += 'email'; }
	//else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email)) { if (error != '') { error += ';'; } error += 'email'; }

	if(contact_email != ''){	
		if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(contact_email)) { if (error != '') { error += ';'; } error += 'contact_email'; }
	}
	if (address1 == '') { if (error != '') { error += ';'; } error += 'address1'; }
	if (postcode == '') { if (error != '') { error += ';'; } error += 'postcode'; }
	if (phone == '') { if (error != '') { error += ';'; } error += 'phone'; }
	if (site_name == '') { if (error != '') { error += ';'; } error += 'site-name'; }

	if (error != '') {
		var errors = error.split(';');
		for (var e=0; e<errors.length; e++) {
			erroid = errors[e];
			jQuery('#your-profile #'+erroid).parent().addClass('error');
		}
		return false;
	}
	return true;
}

function automatic_logout() {
	jQuery.post(js_siteurl, { autologout: 'true' });
	setTimeout('automatic_logout()', 10000);
}