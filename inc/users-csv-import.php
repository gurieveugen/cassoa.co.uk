<?php
ini_set("max_execution_time", 7200);

add_action('admin_menu', 'users_csv_import_menu');
function users_csv_import_menu() {
    // Add a new submenu under Users
	add_submenu_page('users.php', 'CSV Import', 'CSV Import', 10, 'users-csv-import', 'users_csv_import_page');
}

function users_csv_import_page() {
    $hidden_field_name = 'glasses_upload_hidden';
	$hidden_field_name2 = 'glasses_import_hidden';
	$upload_folder = ABSPATH . 'wp-content/uploads/users-csv-import-files/';
	
	// Uploading file
    if ($_POST['users_csv_import_upload'] == 'true') {
		if (!empty($_FILES['import']['name'])) {
			$new_file_name = 'import-'.date("Y-m-d-H-i-s").'.csv';
			$target = $upload_folder . $new_file_name;
			
			if(move_uploaded_file($_FILES['import']['tmp_name'], $target)) {
				echo '<div class="updated"><p><strong>The file '. basename( $_FILES['import']['name']). ' has been uploaded and saved like '.$new_file_name.'</strong></p></div>';
			} else {
				echo '<div class="updated"><p><strong>Sorry, there was a problem uploading your file.</strong></p></div>';
			}				
		}
    }

	// import users
    if ($_POST['users_csv_import_action'] == 'true') {
		if(!empty($_POST['csv_import_file'])) {
			ini_set("auto_detect_line_endings", true);

			$file_path = $upload_folder.$_POST['csv_import_file'];
			$row = 1;
			if (($handle = fopen($file_path, "r")) !== false) {
				$sep = users_get_csv_sep($handle);
				while (($data = fgetcsv($handle, 1000, $sep)) !== false) {
					$username          = trim($data[0]);
					$email             = trim($data[1]);
					$password          = trim($data[2]);
					$membership_no     = trim($data[3]);
					$site_name         = trim($data[4]);
					$address1          = trim($data[5]);
					$address2          = trim($data[6]);
					$address3          = trim($data[7]);
					$address4          = trim($data[8]);
					$postcode          = trim($data[9]);
					$accreditation     = trim($data[10]);
					$phone             = trim($data[11]);
					$website           = trim($data[12]);
					$contact_email     = trim($data[13]);
					$alternative_email = trim($data[14]);

					$new_stockist = array();
					if (strlen($username) && $username != 'username') {
						$cassoa_award = strtolower($accreditation);
						if (strpos($email, ' ') !== false) {
							$emails = explode(' ', $email);
							$email = $emails[0];
						}
						if (strlen($website) && substr($website, 0, 4) != 'http') {
							$website = 'http://'.$website;
						}
						// check user
						$user_id = email_exists($email);
						if (!$user_id) { // create new user
							$user_id = wp_create_user($username, $password, $email);
							/*$u = new WP_User($user_id);
							$u->remove_role('subscriber');
							$u->add_role('s2member_level1');
							s2member_level2*/
							echo '<br><strong>User created: User ID: '. $user_id.' - '.$username.' - '.$email.'</strong>';
						} else {
							echo '<br>User updated: User ID: '. $user_id.' - '.$username.' - '. $email;
						}

						if($user_id) {
							wp_update_user(array('ID' => $user_id, 'user_url' => $website));

							update_user_meta($user_id, 'membership_no', $membership_no);
							update_user_meta($user_id, 'site_name', $site_name);
							update_user_meta($user_id, 'address1', $address1);
							update_user_meta($user_id, 'address2', $address2);
							update_user_meta($user_id, 'address3', $address3);
							update_user_meta($user_id, 'address4', $address4);
							update_user_meta($user_id, 'postcode', $postcode);
							update_user_meta($user_id, 'cassoa_award', $cassoa_award);
							update_user_meta($user_id, 'phone', $phone);
							update_user_meta($user_id, 'contact_email', $contact_email);
							update_user_meta($user_id, 'alternative_email', $alternative_email);
						} else {
							echo '<br><strong style="color:#FF0000;">User ERROR: '.$username.' - '.$user_email.'</strong>';
						}
					}
				}
				fclose($handle);
			}			
		
		} else {
			echo '<div class="updated"><p><strong>Select file for import.</strong></p></div>';
		}
	
	}
	$csv_fields = users_get_csv_fields();
    ?>
	<div class="wrap">
		<h2>Users Import</h2>	
		<h3>CSV Format</h3>
		<p><?php echo implode(", ", $csv_fields); ?></p>
		<hr />
		<form name="upload-form" method="post" action="users.php?page=users-csv-import" enctype="multipart/form-data">
			<input type="hidden" name="page" value="users-csv-import">
			<input type="hidden" name="users_csv_import_upload" value="true">

			<h3>Upload CSV file</h3>
			<p><input type="file" name="import" value="" size="50">&nbsp;<input type="submit" name="Submit" value="&nbsp;Upload&nbsp;" /></p>
		</form>
		<hr />

		<?php $imported_files = array();
		if ($handle = opendir($upload_folder)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$file_name = basename ($file,".csv");
					$imported_files[] = '<input type="radio" name="csv_import_file" value="'.$file.'">&nbsp;<b>'.$file.'</b>';
				}
			}
			rsort($imported_files);
			closedir($handle);
		} ?>
		<?php if (count($imported_files)) { ?>
		<h3>Select files for import users</h3>
		<form name="import-form" method="post" action="users.php?page=users-csv-import" enctype="multipart/form-data">
			<input type="hidden" name="page" value="users-csv-import">
			<input type="hidden" name="users_csv_import_action" value="true">
			<ul>
				<?php foreach ($imported_files as $imported_file) { ?>
				<li><?php echo $imported_file; ?></li>
				<?php } ?>
			</ul>
			<input type="submit" name="Submit" value="&nbsp;&nbsp;Import&nbsp;&nbsp;" />
		</form>	
		<?php } ?>
	</div>
<?php
}

function users_get_csv_fields() {
	return array(
		'Username',
		'Registration email',
		'Password',
		'Membership no',
		'Site name',
		'Address line 1',
		'Address line 2',
		'Address line 3',
		'Address line 4',
		'Postcode',
		'Accreditation',
		'Contact Number',
		'Website Address',
		'Contact email',
		'Alternative email'
	);
}

function users_get_level($cassoa_award) {
	$levels = array('bronze' => 1, 'silver' => 2, 'gold' => 3);
	return $levels[$cassoa_award];
}

function users_get_csv_sep($hndl) {
	while (($data = fgetcsv($hndl, 1000, ",")) !== false) {
		if (count($data) == 1) {
			return ';';
		} else {
			return ',';
		}
	}
}
?>