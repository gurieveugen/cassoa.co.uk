<?php
/*
Template Name: Member Search
*/
global $wpdb, $caravan_theme_options, $wp_query;
?>

<?php get_header(); ?>

<?php
$miles_vals = array(1, 5, 10, 20, 50); // , 20, 50
$s_miles = 10;
$find_site_per_page = $caravan_theme_options['find_site_per_page'];
if (!$find_site_per_page) { $find_site_per_page = 10; }

if ($_GET['member-serach'] == 'true') 
{
	$s_postcode = trim($_GET['s_postcode']);
	$s_miles    = (int)$_GET['s_miles'];
	$s_sitename = trim($_GET['s_sitename']);
	$s_county   = trim($_GET['s_county']);
	$s_city     = trim($_GET['s_city']);

	$params = array(
		's_postcode' => $s_postcode,
		's_miles'    => $s_miles,
		's_sitename' => $s_sitename,
		's_county'   => $s_county,
		's_city'     => $s_city,
		'per_page'   => $find_site_per_page,
		'miles'      => $miles_vals
	);
	$members = get_search_members($params);	

	$members_data = array();
	if ($members) 
	{
		foreach($members as $member) 
		{
			$member_customs = $wpdb->get_results(sprintf("SELECT * FROM %susermeta WHERE user_id = %s", $wpdb->prefix, $member->ID));
			foreach($member_customs as $member_custom) 
			{
				$members_data[$member->ID][$member_custom->meta_key] = $member_custom->meta_value;
			}
		}
	}
}
$members_data = sortMembersData($members_data, 'site_name');
echo '<!-- <pre>';
var_dump($members);
echo '</pre> -->';


?>

<script type="text/javascript">
var gaddresses = new Array();
var mtitles = new Array();
<?php if ($members) { $jind = 0; ?>
	<?php foreach($members as $member) {
	$gaddress = $members_data[$member->ID]['address1'];
	$mtitle = $members_data[$member->ID]['address1'];
	if (strlen($members_data[$member->ID]['address2'])) {
		$gaddress .= ', '.$members_data[$member->ID]['address2'];
		$mtitle .= ',<br>'.$members_data[$member->ID]['address2'];
	}
	if (strlen($members_data[$member->ID]['address3'])) {
		$gaddress .= ', '.$members_data[$member->ID]['address3'];
		$mtitle .= ',<br>'.$members_data[$member->ID]['address3'];
	}
	if (strlen($members_data[$member->ID]['address4'])) {
		$gaddress .= ', '.$members_data[$member->ID]['address4'];
		$mtitle .= ',<br>'.$members_data[$member->ID]['address4'];
	}
	$gaddress .= ' '.$members_data[$member->ID]['postcode'];
	$mtitle .= ',<br>'.$members_data[$member->ID]['postcode'];
	?>
	gaddresses[<?php echo $jind; ?>] = "<?php echo $gaddress; ?>";
	mtitles[<?php echo $jind; ?>] = "<strong><?php echo $members_data[$member->ID]['site_name']; ?></strong><br><?php echo $mtitle; ?>";
	<?php $jind++; } ?>
<?php } ?>
</script>
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=" type="text/javascript"></script>
<script src="<?php echo TDU ?>/js/msearch.js" type="text/javascript"></script>

<div id="main" class="member-search">
	<?php if ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="form-enter-postcode">
		<h3>Enter your <span class="decoration">FULL</span> postcode here to find your nearest CaSSOA site:</h3>
		<form id="member-search-form">
			<div class="row-search">
				<input type="hidden" name="member-serach" value="true">
				<input type="text" name="s_postcode" placeholder="Enter postcode..." value="<?php echo $_GET['s_postcode']; ?>">
				<label>Within</label>
				<div class="select">
					<select name="s_miles">
						<option value="">-- Select miles --</option>
						<?php foreach($miles_vals as $miles_val) { $s = ''; if ($s_miles == $miles_val) { $s = ' SELECTED'; } ?>
						<option value="<?php echo $miles_val; ?>"<?php echo $s; ?>><?php echo $miles_val; ?> mile<?php if ($miles_val > 1) { echo 's'; } if ($miles_val == 50) { echo ' +'; } ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<p>Or search by site name, county <span class="decoration">or</span> city:</p>
			<div class="row-search site-name-row">
				<input style="width: 230px" type="text" name="s_sitename" placeholder="Site name" value="<?php echo $_GET['s_sitename']; ?>">
				<input style="width: 230px" type="text" name="s_county" placeholder="County" value="<?php echo $_GET['s_county']; ?>">
				<input type="text" name="s_city" placeholder="Town/City" value="<?php echo $_GET['s_city']; ?>">
			  <input type="submit" value="Search">
			</div>
		</form>
	</div>
	<div class="wide-map">
		<div id="map" style="width: 940px; height: 583px; border: 1px solid #eeeeee;"></div>
	</div>
	<?php the_content(); ?>
	<?php endif; ?>
	<?php if ($_GET['member-serach'] == 'true') { ?>
	<section class="result-boxes">
		<h3>Here are your results</h3>
		<?php if ($members && $members_data) { ?>
			<?php 
			foreach($members_data as $id => $member_data) { 
				$member = getMemberByID($members, $id);
			?>
			<div class="box">
				<img src="<?php echo TDU ?>/images/ico-<?php echo $members_data[$member->ID]['cassoa_award']; ?>-award.png" alt=" ">
				<div class="text">
					<h4><a href="<?php echo get_permalink($caravan_theme_options['individual_member_page']); ?>?member=<?php echo $member->ID; ?>"><?php echo $members_data[$member->ID]['site_name']; ?></a></h4>
					<p>
						<?php if (strlen($members_data[$member->ID]['address1'])) { echo $members_data[$member->ID]['address1'].',<br>'; } ?>
						<?php if (strlen($members_data[$member->ID]['address2'])) { echo $members_data[$member->ID]['address2'].',<br>'; } ?>
						<?php if (strlen($members_data[$member->ID]['address3'])) { echo $members_data[$member->ID]['address3'].',<br>'; } ?>
						<?php if (strlen($members_data[$member->ID]['address4'])) { echo $members_data[$member->ID]['address4'].',<br>'; } ?>
						<?php echo $members_data[$member->ID]['postcode']; ?>
					</p>
					<?php if (strlen($members_data[$member->ID]['phone'])) { ?><span>Tel: <?php echo $members_data[$member->ID]['phone']; ?></span><?php } ?>
				</div>
			</div>
			<?php } ?>
		<?php } else { ?>
			<p>Sorry there are no results for your search. Please try again with a wider mileage parameter or contact us on 0843 216 5802 or email <a href="mailto:enquiries@cassoa.co.uk">enquiries@cassoa.co.uk</a> so we can let you know your nearest CaSSOA approved site.</p>
		<?php } ?>
	</section>
	<?php } ?>
</div>

<?php get_footer(); ?>
