<?php
/*
Template Name: Individual Member
*/
global $wpdb;
$member = (int)$_GET['member'];
$member_data = get_userdata($member);
?>

<?php get_header(); ?>

<div id="main" class="member-listing individual-member">
	<div id="content-gray">
		<?php if ($member_data) {
		$member_customs = array();
		$member_custom_fields = $wpdb->get_results(sprintf("SELECT * FROM %susermeta WHERE user_id = %s", $wpdb->prefix, $member));
		foreach($member_custom_fields as $member_custom_field) {
			$member_customs[$member_custom_field->meta_key] = $member_custom_field->meta_value;
		}
		$photo = $member_customs['photo'];
		$mphotos = array();
		if ($member_customs['photos']) {
			$mphotos = unserialize($member_customs['photos']);
		}
		if ($photo && !in_array($photo, $mphotos)) {
			$mphotos[] = $photo;
		}
		$photos = array();
		if ($mphotos) {
			foreach($mphotos as $mphoto) {
				$photodata = get_post($mphoto);
				if ($photodata) {
					$photos[] = $mphoto;
				}
			}
		}
		?>
		<script type="text/javascript">
		var gaddresses = new Array();
		var mtitles = new Array();
		<?php
		$gaddress = $member_customs['address1'];
		$mtitle = $member_customs['address1'];
		if (strlen($member_customs['address2'])) {
			$gaddress .= ', '.$member_customs['address2'];
			$mtitle .= ',<br>'.$member_customs['address2'];
		}
		if (strlen($member_customs['address3'])) {
			$gaddress .= ', '.$member_customs['address3'];
			$mtitle .= ',<br>'.$member_customs['address3'];
		}
		if (strlen($member_customs['address4'])) {
			$gaddress .= ', '.$member_customs['address4'];
			$mtitle .= ',<br>'.$member_customs['address4'];
		}
		$gaddress .= ' '.$member_customs['postcode'];
		$mtitle .= ',<br>'.$member_customs['postcode'];
		?>
		gaddresses[0] = '<?php echo $gaddress; ?>';
		mtitles[0] = '<strong><?php echo $member_customs['site_name']; ?></strong><br><?php echo $mtitle; ?>';
		</script>
		<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=" type="text/javascript"></script>
		<script src="<?php echo TDU ?>/js/msearch.js" type="text/javascript"></script>
		<h1><?php echo $member_customs['site_name']; ?></h1>
		<div class="map-holder"><!-- 587 x 234 -->
			<div id="map" style="width: 587px; height: 234px; border: 1px solid #eeeeee;"></div>
		</div>
		<h5><a href="https://maps.google.com/maps?q=<?php echo str_replace(' ', '+', $gaddress); ?>&oe=utf-8" target="_blank">Get directions</a></h5>
		<div class="listing-columns">
			<div class="column">
				<?php if (strlen($member_customs['introduction'])) { ?>
					<h4>Introduction</h4>
					<?php echo apply_filters('the_content', $member_customs['introduction']); ?>
				<?php } ?>
				<?php if (strlen($member_customs['directions'])) { ?>
					<h4>Directions</h4>
					<?php echo apply_filters('the_content', $member_customs['directions']); ?>
				<?php } ?>
				<?php if (strlen($member_customs['pricing'])) { ?>
					<h4>Pricing</h4>
					<?php echo apply_filters('the_content', $member_customs['pricing']); ?>
				<?php } ?>
				<?php if (strlen($member_customs['cassoa_award'])) { ?>
				<h4>CaSSOA Award</h4>
					<img src="<?php echo TDU ?>/images/ico-<?php echo $member_customs['cassoa_award']; ?>-award.png" alt=" ">
				<?php } ?>
				<?php if (strlen($member_customs['reservations'])) { ?>
					<h4>Reservations</h4>
					<?php echo apply_filters('the_content', $member_customs['reservations']); ?>
				<?php } ?>
				<?php if (strlen($member_customs['on_site_information'])) { ?>
					<h4>On Site Information</h4>
					<?php echo apply_filters('the_content', $member_customs['on_site_information']); ?>
				<?php } ?>
			</div>
			<div class="column">
				<?php if ($photos) { ?>
					<?php
					$w = 137; $h = 97;
					if (count($photos) == 1) { $w = 283; $h = 196; }
					?>
					<div class="member-photos">
						<div class="photos">
							<?php foreach($photos as $photo) { ?>
							<a class="photo" href="<?php echo get_thumb($photo, 800, 600); ?>"><img src="<?php echo get_thumb($photo, $w, $h, true); ?>" alt=" "></a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<h4>Contact Information</h4>
				<div class="location">
					<strong>Location:</strong> 
					<p>
						<?php if (strlen($member_customs['address1'])) { echo $member_customs['address1'].',<br>'; } ?>
						<?php if (strlen($member_customs['address2'])) { echo $member_customs['address2'].',<br>'; } ?>
						<?php if (strlen($member_customs['address3'])) { echo $member_customs['address3'].',<br>'; } ?>
						<?php if (strlen($member_customs['address4'])) { echo $member_customs['address4'].',<br>'; } ?>
						<?php echo $member_customs['postcode']; ?>
					</p>
				</div>
				<?php if (strlen($member_customs['phone'])) { ?><p><strong>Telephone:</strong> <?php echo $member_customs['phone']; ?></p><?php } ?>
				<?php if (strlen($member_data->user_url)) { ?><p><strong>Website:</strong><br><a href="<?php echo $member_data->user_url; ?>" target="_blank"><?php echo str_replace(array('http://', 'https://'), '', $member_data->user_url); ?></a></p><?php } ?>
				<?php if (strlen($member_customs['contact_names'])) { $contact_names = str_replace(chr(13), '', trim($member_customs['contact_names'])); ?><p><strong>Contact names:</strong><br><?php echo str_replace(chr(10), ', ', $contact_names); ?></p><?php } ?>
				<?php if (strlen($member_customs['membership_no'])) { ?><p><strong>Membership No.</strong> <?php echo $member_customs['membership_no']; ?></p><?php } ?>
				<?php if (strlen($member_customs['contact_email'])) { ?><p><strong>Contact email:</strong> <?php echo $member_customs['contact_email']; ?></p><?php } ?>
				<?php /* if (strlen($member_customs['alternative_email'])) { ?><p><strong>Alternative contact email:</strong> <?php echo $member_customs['alternative_email']; ?></p><?php } */ ?>
				<div class="form-contact ti">
					<strong>Contact:</strong>
					<div class="member-contact-success" style="margin-bottom:10px;font-size:12px;color:#339900;display:none;">Your enquiry was successfully sent.</div>
					<form name="member_profile_contact" method="POST" class="member-profile-contact" onsubmit="return member_contact_presubmit();">
						<input type="hidden" name="member" id="mc-member" value="<?php echo $member; ?>" placeholder="Name" />
						<input type="text" name="name" id="mc-name" value="" placeholder="Name" />
						<input type="email" name="email" id="mc-email" value="" placeholder="Email" />
						<input type="text" name="phone" id="mc-phone" value="" placeholder="Phone (optional)" />
						<textarea name="enquiry" id="mc-enquiry" placeholder="Enquiry"></textarea>
						<input type="submit" value="Submit">
					</form>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<p>Incorrect member data.</p>
		<?php } ?>
	</div>
</div>

<?php get_footer(); ?>
