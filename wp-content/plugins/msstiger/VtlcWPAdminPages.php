<?php
class VTLCWPAdminPages {
	function wptiger_rightContent() {
		global $plugin_url_vtlc;
		$rightContent = '<div class="tiger-plugindetail-box" id="wptiger-pluginDetails"><h3>Plugin Details</h3>
			<div class="wptiger-box-inside wptiger-plugin-details">
			<table>	<tbody>
			<tr><td><b>Plugin Name</b></td><td>WP Tiger</td></tr>
			<tr><td><b>Version</b></td><td>3.0.3 <a style="text-decoration:none" href="http://mastersoftwaretechnologies.com/free-wordpress-vtiger-webforms-module.html" target="_blank">( Update Now )</a></td></tr>
			</tbody></table>
			<div class="company-detials" id="company-detials">
			<div class="position" id="position"><a target="_blank" href="http://mastersoftwaretechnologies.com//"><img src=" "></a></div>
			</div>
			</div><!-- end inside div -->
			</div>';
		return $rightContent;
	}
	function topContent(){
		$header_content = '<div style="background-color: #FFFFE0;border-color: #E6DB55;border-radius: 3px 3px 3px 3px;border-style: solid;border-width: 1px;margin: 5px 15px 2px; margin-top:15px;padding: 5px;text-align:center"> Please check out <a href="http://mastersoftwaretechnologies.com//blog/category/free-wordpress-plugins" target="_blank">www.mastersoftwaretechnologies.com</a> for the latest news and details of other great plugins and tools. </div><br/>';
		return $header_content;
	}

	function vts_tiger_listShortcodes() {
		$config = get_option('Vts_vtpl_settings');
		if (!empty($config['hostname']) && !empty($config['dbuser'])) {
			?>
		<div class="list-codes">
	<h2>List of Shortcodes</h2>
	<table cellspacing="0" cellpadding="0">
		<tbody><tr>
		</tr>
		<tr class="smack_alt">
			<th style="width: 5%;" class="list-view-th">#</th>
			<th style="width: 24%;" class="list-view-th">Shortcodes</th>
			<th style="width: 12%;" class="list-view-th">VT Module</th>
			<th style="width: 10%;" class="list-view-th">Assigned To</th>
			<th style="width: 10%;" class="list-view-th">IsWidget</th>
			<th style="width: 18%;" class="list-view-th">Submits</th>
			<th style="width: 18%;" class="list-view-th">Success</th>
			<th style="width: 18%;" class="list-view-th">Failure</th>
			<th style="width: 18%;" class="list-view-th">Action</th>
		</tr>
		<?php
global $wpdb;
			$myrows = $wpdb->get_results("SELECT * FROM create_shortcode");
			$i = 1;
			foreach ($myrows as $list) {
				$resut = json_decode("$list->data", true);
				?>
		<tr class="smack_highlight"><td style="text-align:center;"><?php echo $i;?></td>
		<td style="text-align:center;">[Vts-tiger-New-form name="<?php echo $list->shortcode;?>"]</td>
		<td style="text-align:center;"><?php echo $resut['lead'];?></td>
		<td style="text-align:center;"><?php echo $resut['assigned'];?></td>
		<td style="text-align:center;"><?php if ($resut['iswidget'] == on) {echo "YES";} else {echo "No";}?></td>
		<td style="text-align:center;"><?php echo $list->submit;?></td>
		<td style="text-align:center;"><?php echo $list->success;?></td>
		<td style="text-align:center;"><?php echo $list->failure;?></td>
		<td style="text-align:center;"><?php if ($resut['captcha'] == on) {echo "YES";} else {echo "No";}?></td>
		<td style="text-align:center;">
		<select  onchange="confirmDelete(&quot;<?php echo $list->shortcode;?>&quot;,&quot;<?php echo $resut['lead'];?>&quot;,&quot;<?php echo admin_url();?>&quot;)" name="<?php echo $list->shortcode;?>" id="<?php echo $list->shortcode;?>">
			<option value="Select Action">Select Action</option>
			<option value="edit">Edit</option>
			<option value="delete">Delete</option>
		</select></td></tr>
		<?php $i++;}?>
				</tbody></table><input type="hidden" name="ShortCodeaction" id="ShortCodeaction"></div>
	<?php
		} else {
			echo "<div style='margin-top:20px;font-weight:bold;'>
					Please enter a valid database <a href=" . admin_url() . "admin.php?page=vtlc&action=plugin_settings>settings</a>
					</div>";
		}
	}
	function capture_wp_users() {
		global $plugin_url_vtlc;
		$imagepath = "{$plugin_url_vtlc}/images/";
		if (isset($_POST['submitbtn'])) {
			if (($_POST['vtst_user_capture']) == 'on') {
				$config = get_option('Vts_vtpl_settings');
				if (!empty($config['hostname']) && !empty($config['dbuser'])) {
					$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
					$allowedFields2 = $vtdb->get_results("SELECT firstname,email FROM vtiger_contactdetails");
					foreach ($allowedFields2 as $user) {
						if ($_POST['mmack_capture_duplicates'] == 'skip') {
							$user_id = username_exists($user->firstname);
							if (!$user_id and email_exists($user->email) == false) {
								echo "ok";
								$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
								$user_id = wp_create_user($user->firstname, $random_password, $user->email);
							} else {
								echo "no";
								$random_password = __('User already exists.  Password inherited.');
							}
						}
						if ($_POST['mmack_capture_duplicates'] == 'update') {
							$user_id = username_exists($user->firstname);
							$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
							$user_id = wp_create_user($user->firstname, $random_password, $user->email);
							echo $random_password;
						}
					}
				} else {
					$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

					$allowedFields2 = $vtdb->get_results("SELECT * FROM vtiger_contactdetails");
				}
			}
		}

		?><div class="upgradetopro" id="upgradetopro" style="display:none;">This feature is only available in Pro Version,
			Please <a href="http://mastersoftwaretechnologies.com//wp-vtiger-pro.html">UPGRADE TO PRO</a></div>
		<div class="syncro-codes">
			<div>
				<form id="vtst-vtiger-user-capture-settings-form" action="<?php echo $_SERVER['REQUEST_URI'];?>"
					  method="post">
					<input type="hidden" name="vtst-vtiger-user-capture-settings-form"
						   value="vtst-vtiger-user-capture-settings-form"/>
					<h2>Capture WordPress users</h2>
					<table class="wor-table">
						<tr>
							<td>
								<label>
									<div style='float:left;padding-right: 5px;'>Sync New Registration to VT Contacts
									</div>
									<div style='float:right;'>:</div>
								</label>
							</td>
							<td>
								<input type='checkbox' class='vtst-vtiger-settings-user-capture'
									   name='vtst_user_capture' id='vtst_user_capture'
									<?php
if (@$config['vtst_user_capture'] == 'on') {
			echo "checked";
		}
		?>
									>
							</td>
						</tr>
						<tr>
		<td>
			<label><div style="float:left;padding-right: 5px;">Duplicate handling</div><div style="float:right;">:</div> </label>
		</td>
		<td>
			<input type="radio" checked="" value="skip" id="mmack_capture_duplicates" name="mmack_capture_duplicates" class="smack-vtiger-settings-capture-duplicates"> Skip
		</td>
		<td>
			<input type="radio" value="update" id="mmack_update_records" name="mmack_capture_duplicates" class="smack-vtiger-settings-capture-duplicates"> Update
		</td>
		<td>
			<input type="radio" value="none" id="mmack_update_records" name="mmack_capture_duplicates" class="smack-vtiger-settings-capture-duplicates"> None
		</td>

	</tr>
	</table>
	<table>
		<tr>
		<td>
			<input type="hidden" name="posted" value="<?php echo 'posted';?>">
              <p class="submit">
									<input type="submit" value="<?php _e('Save Settings');?>" name="submitbtn" class="bottom-btn"
										  />
								</p>
							</td>
							<td>
								<input type="button" style="float:left;" value="<?php _e('Sync Now');?>"
									   class="bottom-btn submit-add-to-menu" onclick="captureAlreadyRegisteredUsers('<?php echo admin_Url();?>');"/>
								<img style="display:none; float:left; padding-top:5px; padding-left:5px;"
									 id="loading-image" src="<?php echo $imagepath . 'loading-indicator.gif';?>"/>
							</td>

						</tr>

					</table>
				</form>
			</div>

		</div>
	<?php
}

	function captcha_settings() {

		global $wpdb;?>
	<html>
<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
$(document).ready(function(){
$('#checkbox1').change(function(){
if(this.checked)
$('#autoUpdate').fadeIn('slow');
else
$('#autoUpdate').fadeOut('slow');

});
});
</script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<?php
$captchaSettings = $wpdb->get_results("SELECT * FROM `wp_captcha` LIMIT 1");

		if (isset($captchaSettings) && isset($captchaSettings[0])) {
			$publicKey = $captchaSettings[0]->public_key;
			$privateKey = $captchaSettings[0]->private_key;
			$visbileCaptcha = $captchaSettings[0]->captcha_visible;
			$checkCaptcha = $visbileCaptcha;
			$checkCaptcha = 'checked="checked" ';

		}
		if (@$visbileCaptcha == 'no') {
			echo "<script>$(document).ready(function(){ $('#autoUpdate').hide('slow'); });</script>";
		}
		?>

<form name="frm1" class="captcha" method="post" action ="";
?>
Do you need captcha to visible :
<input type="checkbox" id="checkbox1" name="captcha" value="yes" "<?php echo @$checkCaptcha;?>" />
<div id="autoUpdate" class="autoUpdate">
<p>
	 <table>
	<tr><td>Recaptcha Public key :</td>
<td><input type = "text" name = "public_key" value ="<?php echo $publicKey;?>" placeholder="public Key "></td></tr>

<tr><td>Recaptcha private key :</td>
<td><input type = "text" name = "private_key" value ="<?php echo $privateKey;?>" placeholder="Private Key "></td></tr></p>
<tr><td><input type="submit" name = "submit" value ="Save Settings" class="bottom-btn submit-add-to-menu"	></td></tr>
</table>
</form>
</div>
</body>
</html>
<?php
@$private = $_POST['private_key'];
		@$public = $_POST['public_key'];
		@$captcha = $_POST['captcha'];

		/*$res = $wpdb->update('wp_captcha', array('public_key' => $public, 'private_key' => $private, 'captcha_visible' => $captcha), array('%s', '%s', '%s'));

		if (!empty($res)) {
			return true;
		} else {
			return false;
		}*/
	}

	function plugin_settings() {

		$fieldNames = array('hostname' => __('Database Host'), 'dbuser' => __('Database User'), 'dbpass' => __('Database Password'), 'dbname' => __('Database Name'), 'url' => __('URL'), 'Vts_host_username' => __("VTLCt Host Username"), 'VTS_host_access_key' => __("VTLCt Host Access Key"), 'wp_tiger_vtst_user_capture' => __('Capture User'));
		foreach ($_POST as $key => $value) {
			$_POST[$key] = sanitize_text_field($value);
			if (sizeof($_POST) && isset($_POST["vtst_vtlc_hidden"])) {
				$config = get_option("Vts_vtpl_settings");
				foreach ($fieldNames as $field => $value) {
					if (($field != "dbpass") && ($field != "VTS_host_access_key")) {
						@$config[$field] = $_POST[$field];
					} else {
						if (($_POST['dbpass'] != '') && ($field == "dbpass")) {
							$config[$field] = $_POST[$field];
						} elseif (($_POST['VTS_host_access_key'] != '') && ($field == "VTS_host_access_key")) {
							$config[$field] = $_POST[$field];
						}
					}
				}

				$dbvalues = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
				$versions = $dbvalues->get_results("SELECT current_version FROM vtiger_version order by id desc limit 1");
				foreach ($versions as $tmp) {
					$version = $tmp->current_version;
				}
				$config['version'] = $version;
				update_option('Vts_vtpl_settings', $config);
				$wp_tiger_contact_form_attempts = get_option('vts_tiger-contact-form-attempts');
				$wp_tiger_contact_widget_form_attempts = get_option('vts-tiger-contact-widget-form-attempts');
				$successfulAtemptsOption['total'] = 0;
				$successfulAtemptsOption['success'] = 0;

				if (!is_array($wp_tiger_contact_form_attempts)) {
					update_option('vts_tiger-contact-form-attempts', $successfulAtemptsOption);
				}
				if (!is_array($wp_tiger_contact_widget_form_attempts)) {
					update_option('vts-tiger-contact-widget-form-attempts', $successfulAtemptsOption);
				}
			}
		}
		$siteurl = site_url();
		$config = get_option('Vts_vtpl_settings');
		$config_field = get_option("Vts_vtpl_field_settings");

		$content = '<div class="vtiger-database">
				<div>';

		if (!isset($config_field['fieldlist'])) {
			$content .= '<form class="left-side-content" id="vtst_vtlc_form"
							method="post">';
		} else {
			$content .= '<form class="left-side-content" id="vtst_vtlc_form"
							action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		}
		if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Settings') {
			?>
			<script>
				saveSettings();
			</script>
		<?php
}
		//form to test the database connections
		$content .= '<input type="hidden" name="page_options" value="Vts_vtpl_settings" />
						<input type="hidden" name="vtst_vtlc_hidden" value="1" />

						<h2>VtigerCRM sync configuration</h2>

						<div class="crm-first-section">
						<div class="messageBox" id="message-box" style="display:none;" ><b>Settings Successfully Saved!</b></div>
						<h3>VTigerCRM database settings</h3>
						<div id="dbfields">
							<table>
								<tr>
									<td class="vtst_settings_vtst_settings_td_label"><label>Database
											Hostname</label></td>
									<td><input class="vtst_settings_input_text" type="text"
										id="hostname" name="hostname"
										value="' . $config['hostname'] . '" /></td>
								</tr>
								<tr>
									<td class="vtst_settings_td_label"><label>Database Username</label>
									</td>
									<td><input class="vtst_settings_input_text" type="text" id="dbuser"
										name="dbuser" value="' . $config['dbuser'] . '" /></td>
								</tr>
								<tr>
									<td class="vtst_settings_td_label"><label>Database Password</label>
									</td>
									<td><input class="vtst_settings_input_text" type="password" id="dbpass" onblur="enableTestDatabaseCredentials();" name="dbpass" autocomplete="off" /><br /></td>
								</tr>
								<tr>
									<td class="vtst_settings_td_label"><label>Database Name</label></td>
									<td><input class="vtst_settings_input_text" type="text" id="dbname"
										name="dbname" value="' . $config['dbname'] . '" /></td>
								</tr>
							</table>
						</div>
						<table>
							<tr>
								<td class="vtst_settings_td_label"><input type="button" id="Test-Database-Credentials"
									class="button" disabled=disabled value="Test database connection"
									onclick="testDatabaseCredentials(\'' . $siteurl . '\');" /></td>
								<td id="vtst-database-test-results"><p id="database_process" style="display:none;">Processing</p></td>
							</tr>

						</table>
						</div>

						<!--first-part-end-here-->

						<div class="crm-first-section">
						<h3>VtigerCRM settings</h3>
						<div id=vtigersettings>
							<table>
								<tr>
									<td class="vtst_settings_td_label"><label>Vtiger URL</label></td>
									<td><input class="vtst_settings_input_text" type="text" id="url"
										name="url" value="' . $config['url'] . '" /></td>
								</tr>
								<tr>
									<td class="vtst_settings_td_label"><label>Vtiger Username</label></td>
									<td><input class="vtst_settings_input_text" type="text" id="Vts_host_username"
										name="Vts_host_username" value="' . $config['Vts_host_username'] . '" /></td>
								</tr>
								<tr>
									<td class="vtst_settings_td_label"><label>Vtiger AccessKey</label></td>
									<td><input class="vtst_settings_input_text" type="password" id="VTS_host_access_key" onblur="enableTestVtigerCredentials();" autocomplete="off"
										name="VTS_host_access_key" /></td>
								</tr>
							</table>
							<table>
								<tr>
									<td class="vtst_settings_td_label"><input type="button"
										class="button" disabled=disabled value="Test Vtiger Credentials" id="Test-Vtiger-Credentials"
										onclick="testVtigerCredentials(\''. $siteurl . '\');" /></td>
									<td id="vtst-vtiger-test-results"> <p id="vtiger_process"style="display:none">Processing</p></td>
								</tr>

							</table>
							<table>
								<tr>
									<td class="vtst_settings_td_label"><label>Application Key</label></td>
									<td><input class="vtst_settings_input_text" type="text" id="appkey"
										name="appkey" value="" /></td>
								</tr>
							</table>
							</div>
							</div>
							<!--second section close here-->


							<div class="crm-first-section">
							<h3>Capturing WordPress users</h3>
							<table>
								<tr>
									<td><label>
											<div style="float: left">Do you need to capture the registering
												users</div>
											<div style="float: right; padding-left: 5px;">:</div>
									</label></td>
									<td><input type="checkbox"
										class="vtst-vtiger-settings-user-capture" onclick="recature();"
										name="wp_tiger_vtst_user_capture" id="wp_tiger_vtst_user_capture"';

		if ($config['wp_tiger_vtst_user_capture'] == 'on') {
			$content .= "checked";
		}
		$content .= '>
						</td></tr><tr>
						<td><div id="capture"></div></td>
						</tr>
						<!--<tr>
							<td>
								<div style="float: left">Sync WP members to VtigerCRM contacts</div>
								<div style="float: right; padding-left: 5px;">:</div>
							</td>
							<td><input type="button" value="Sync"
								class="bottom-btn submit-add-to-menu"
								onclick="captureAlreadyRegisteredUsersWpTiger();" />
								<div id="please-upgrade" style="position: absolute; z-index: 100;"></div>
							</td>
						</tr>-->

						</table>


						<input type="hidden" name="posted" value="Posted">
						<p class="submit">
							<input name="Submit" type="submit" value="Save Settings" class="bottom-btn last-btn" />
						</p>
						<div id="vt_fields_container"></div>

						</div>

						</form></div>

	</div>';
		echo $content;
	}

	function widget_fields() {

		global $plugin_url_vtlc;
		global $wpdb;

		$config = get_option('Vts_vtpl_settings');
		@$edit_fild = $_REQUEST['EditShortCode'];
		$siteurl = site_url();

		foreach ($_REQUEST as $key => $value) {
			if (!preg_match('/^[A-Za-z0-9_-]*$/', $value)) {
				$_REQUEST[$key] = '';
			}
		}

		if (($_REQUEST['action'] = 'widget_fields') && (empty($_REQUEST['EditShortCode']))) {
			$config = get_option('Vts_vtpl_settings');
			$config_widget_field = get_option("Vts_vtlc_widget_field_settings");

		} else {
			$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
			$new_data = json_decode("$alloweded2->select_field", true);
			$config_widget_field['widgetfieldlist'] = $new_data;

		}

		//code for save mandatory fields leads from
		if ($_REQUEST['action'] == 'widget_fields' && empty($_REQUEST['EditShortCode']) && isset($_POST['make_mandatory_contact']) && $_POST['make_mandatory_contact'] == 'Save Mandatory Fields') {
			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

			foreach ($_POST as $key => $value) {
				$_POST[$key] = sanitize_text_field($value);
			}
			if (isset($_POST['no_of_vt_fields'])) {
				$fieldArr = array();
				for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
					$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");
					if (isset($_POST["check_mandatory$i"])) {
						array_push($fieldArr, $_POST["check_mandatory_hidden$i"]);
						$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");
					}
				}
				update_option('vtiger_db_fields_contact_mandatoryfields', $fieldArr);
			}
		}

//code for save mandatory fields leads from
		if (isset($_POST['make_mandatory_contact']) && $_POST['make_mandatory_contact'] == 'Save Mandatory Fields') {

			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

			foreach ($_POST as $key => $value) {

				$_POST[$key] = sanitize_text_field($value);

			}

			if (isset($_POST['no_of_vt_fields'])) {
				$fieldArr = array();
				for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {

					$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");

					if (isset($_POST["check_mandatory$i"])) {

						array_push($fieldArr, $_POST["check_mandatory_hidden$i"]);
						$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");
					}
				}

				update_option('vtiger_db_fields_contact_mandatoryfields', $fieldArr);

			}
		}

		$topContent = $this->topContent();
		$imagepath = "{$plugin_url_vtlc}/images/";
		//code to save contact form fields
		if (($_REQUEST['action'] = 'widget_fields') && (empty($_REQUEST['EditShortCode']))) {
			// if (isset($_POST['widget_field_posted'])) {
			// 	$config_widget_field['widgetfieldlist'] = array();
			// 	if (isset($_POST['no_of_vt_fields'])) {
			// 		$fieldArr = array();
			// 		for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
			// 			if (isset($_POST["vtst_vtlc_field$i"]) && $_POST["vtst_vtlc_field_hidden$i"] != 0) {

			// 				array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
			// 			}
			// 		}
			// 		$config_widget_field['widgetfieldlist'] = $fieldArr;

			// 	}
			// 	update_option('Vts_vtlc_widget_field_settings', $config_widget_field);
			// }

			if (isset($_POST['saveContactFields']) && $_POST['saveContactFields'] == 'Save Field Settings') {

				foreach ($_POST as $key => $value) {
					$_POST[$key] = sanitize_text_field($value);
				}
				$config_widget_field['widgetfieldlist'] = array();
				if (isset($_POST['no_of_vt_fields'])) {
					$fieldArr = array();
					for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
						if (isset($_POST["vtst_vtlc_field$i"])) {
							array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
						}
					}

					$data_array = json_encode($config_widget_field['widgetfieldlist'] = $fieldArr);

				}

				$update = update_option('Vts_vtlc_widget_field_settings', $config_widget_field);
				$linkUrl = site_url() . '/wp-admin/admin.php?page=vtlc&action=widget_fields';
				echo "<script>window.location.href= '" . $linkUrl . "'; </script>";

			}
		}
		$widgetContent = '<div >
					 <div  class="left-side-content" class="upgradetopro" id="upgradetopro" style="display:none;">This feature is only available in Pro Version, Please <a href="http://mastersoftwaretechnologies.com//wp-vtiger-pro.html">UPGRADE TO PRO</a> </div>
					<div class="messageBox" id="message-box" style="display:none;" ><b>Successfully Saved!</b></div>
		<form id="wiget fiels" action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="">';

		if (!empty($config['hostname']) && !empty($config['dbuser'])) {
			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,block FROM vtiger_field WHERE tabid = 4  ORDER BY block, sequence");

			if (($_REQUEST['action'] = 'widget_fields') && (!empty($_REQUEST['EditShortCode']))) {
				echo '<h3>[Vts-tiger-New-form name="' . $edit_fild . '"]</h3>';
			}

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		} elseif (!empty($_POST['hostname']) && (!empty($_POST['dbuser']))) {
			$vtdb = new wpdb($_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['hostname']);
			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 ORDER BY block, sequence");

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		}

		if (!empty($allowedFields)) {
			global $wpdb;
			if (($_REQUEST['action'] = 'widget_fields') && (!empty($_REQUEST['EditShortCode']))) {

				if (isset($_POST['saveContactFields']) && $_POST['saveContactFields'] == 'Save Field Settings') {

					foreach ($_POST as $key => $value) {
						$_POST[$key] = sanitize_text_field($value);
					}

					$fieldArr = array();
					if (isset($_POST['widget_field_posted'])) {
						if (isset($_POST['no_of_vt_fields'])) {

							for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
								if (isset($_POST["vtst_vtlc_field$i"])) {
									array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
								}
							}
						}
					}
					$datanew = json_encode($_POST);
					$data_array = json_encode($fieldArr);
					if ($datanew) {

						$_POST['assigned'] = sanitize_text_field($_POST['assigned']);
						$captcha = $_POST['generatecaptcha'];
						$rows_affected = $wpdb->query(
							$wpdb->prepare(
								"UPDATE create_shortcode SET data = '" . json_encode($_POST) . "', assign = '" . $_POST['assigned'] . "', select_field = '" . $data_array . "',
								captcha = '" . $captcha . "'
						        WHERE shortcode = '" . $edit_fild . "'"));
					}?>

				<script>

window.location.href ="admin.php?page=vtlc&action=widget_fields&EditShortCode=<?php echo $edit_fild?>";
</script>

	<?php	}

			} else {

				if (isset($_POST['create_shortcode']) && $_POST['create_shortcode'] == 'Generate Shortcode') {

					foreach ($_POST as $key => $value) {
						$_POST[$key] = sanitize_text_field($value);
					}
					$datanew = json_encode($_POST);
					$shortcode = createRandomPassword();
					echo '<h3>[Vts-tiger-New-form name="' . $shortcode . '"]</h3>';

					$fieldArr = array();
					if (isset($_POST['widget_field_posted'])) {
						if (isset($_POST['no_of_vt_fields'])) {

							for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
								if (isset($_POST["vtst_vtlc_field$i"]) && $_POST["vtst_vtlc_field_hidden$i"] != 0) {

									array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
								}
							}

						}
					}

					$addval = json_encode($fieldArr);

					if ($datanew) {
                        // print_r($_POST);
						$error_message = $_POST['error_message'];
						$success_message = $_POST['success_message'];
						$enable_url = $_POST['enable_url'];
						@$captcha = $_POST['generatecaptcha'];
						$success = $_POST['success'];
						$submit = $_POST['submit'];
						$failure = $_POST['failure'];

						$_POST['assigned'] = sanitize_text_field($_POST['assigned']);

						$res = $wpdb->insert('create_shortcode', array('shortcode' => $shortcode, 'data' => $datanew,
							'assign' => $_POST['assigned'], 'select_field' => $addval, 'captcha' =>
							$captcha, 'success' => 0,
							'submit' => 0, 'failure' => 0, 'error_message' => $error_message,
							'success_message' => $success_message, 'url_redirection' => $enable_url), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));
					}
				}
			}
			$widgetContent .= '<form name="frm" method="post" action="admin.php?page=vtlc&action=widget_fields">
			            <input type="hidden" name="submit" value="0">
			 	        <input type="hidden" name="success" value="0">
			 	        <input type="hidden" name="failure" value="0">';

			$widgetContent .= '</select></div>
							</div><br/>
							<input type="hidden" name="lead" value="widget">

							<input type="button" class="bottom-btn submit-add-to-menu" name="sync_crm_fields" value="Fetch CRM Fields"
							 onclick="goToTop(); syncCrmFields(&quot;' . admin_Url() . '&quot;,&quot;widget&quot;,&quot;smack_wp_vtiger_contact_fields-tmp&quot;,&quot;onCreate&quot;);"/>
							<input type="submit" value="Save Field Settings" class="bottom-btn submit-add-to-menu"
								name="saveContactFields"/>
							<input type="Submit" class="bottom-btn submit-add-to-menu" name="make_mandatory_contact"
								id="make_mandatory" value="Save Mandatory Fields" onclick="makeMandatory()" />
							<input type="button" class="bottom-btn submit-add-to-menu" name="save_display_name"
								id="save_display_name" value="Save Labels" onclick="goToTop(); saveDisplayName(&quot;' . admin_Url() . '&quot;,&quot;saveDisplayName&quot;,&quot;save_display_name&quot;,&quot;widget&quot;,&quot;smack_wp_vtiger_lead_fields-tmp&quot;,&quot;onCreate&quot;);" />';
			// if (($_REQUEST['action'] = 'widget_fields') && (empty($_REQUEST['EditShortCode']))) {
			// 	$widgetContent .= '<input type="submit" class="button-create-shortcode" name="create_shortcode" id="create_shortcode" value="Generate Shortcode"  />';
			// }
			$widgetContent .= '<img id="loading-image" style="display: none; float: right; padding-top: 5px; padding-left: 5px;" src="">
<!--<p>Please use the short code <b>[display_widget_area]</> in widgets</p>!--><br>
<br/>
						<div id="fieldtables">
							<table class="tableborder">
								<tr class="vtst_alt">
									<th style="width: 20px;"><input type="hidden" name="" id="selectall"
										onclick="select_allfields(\'vtst_vtlc_field_form\',\'widget\')" /></th>
									<th style="width: 140px;"><h5>Field Name</h5></th>
									<th style="width: 80px;"><h5>Show Field</h5></th>
									<th style="width: 100px;"><h5>Order</h5></th>
									<th style="width: 120px;"><h5>Mandatory Fields</h5></th>
									<th style="width: 200px;"><h5>Field Label Display</h5></th>
								</tr>
								<tbody>
								<tr valign="top">
									<td><input type="hidden" id="no_of_vt_fields" name="no_of_vt_fields" value="' . sizeof($allowedFields) . '">';

			$nooffields = count($allowedFields);
			$inc = 1;
			foreach ($allowedFields as $key => $field) {
				if ($inc % 2 == 1) {
					$widgetContent .= '<tr class="vtst_highlight">';
				} else {
					$widgetContent .= '<tr class="vtst_highlight vtst_alt">';
				}
				$typeofdata = explode('~', $field->typeofdata);
				$widgetContent .= '<td class="vtst-field-td-middleit">
									<input type="hidden"  value="' . $field->fieldlabel . '"id="field_label' . $key . '">
									<input type="hidden" value="' . $typeofdata[1] . '" id="field_type' . $key . '">
									<input type="hidden" class="checkBoxSelect_' . $key . '" id="vts_label' . $key . '" name="vtst_vtlc_field_hidden' . $key . '"value="' . $field->fieldid . '" />';
				if ($typeofdata[1] == 'M') {
					$checked = 'checked="checked" ';
					$mandatory = 'checked="checked" ';
					foreach ($config_widget_field['widgetfieldlist'] as $key2 => $value) {
						# code...
						if ($value == $field->fieldid) {
							$checked = 'checked="checked"';
							$mandatory = '';
							break;
						} else {
							$checked = '';
							$mandatory = '';
						}
					}
				} else {
					foreach ($config_widget_field['widgetfieldlist'] as $key2 => $value) {
						# code...
						if ($value == $field->fieldid) {
							$checked = 'checked="checked"';
							$mandatory = '';
							break;
						} else {
							$checked = '';
							$mandatory = '';
						}
					}
				}                  
				if ($typeofdata[1] == 'M') {
					$mandatory = 'checked="checked"';
					$checked = 'checked="checked"';
					$widgetContent .= '<input type="hidden" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
										name="vtst_vtlc_field' . $key . '" />
									<input type="checkbox" class="' . $key . '" onclick="return checkBoxSelect(this)"  value="' . $field->fieldname . '"' . $checked . '>';
				} else {
					$widgetContent .= '<input type="checkbox" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
									name="vtst_vtlc_field' . $key . '" ' . @$checked . '>';
				}
				$widgetContent .= '</td>
								<td>' . $field->fieldlabel;
				if ($typeofdata[1] == 'M') {
					$mandatory = 'checked="checked"';
					$checked = 'checked="checked"';
					$widgetContent .= '<span style="color: #FF4B33">&nbsp;*</span>';
				}
				$widgetContent .= '</td>
								<td class="vtst-field-td-middleit">';
				if (in_array($field->fieldid, $config_widget_field['widgetfieldlist'])) {
					if ($typeofdata[1] == 'M') {
						$mandatory = 'checked="checked"';
						$checked = 'checked="checked"';
						$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="published(' . $key . ',&quot;1&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img id="vtst-field-td-middleit" src="' . $imagepath . 'tick_strict.png"  /></a>';
					} else {
						$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="publisheds(' . $key . ',&quot;0&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'tick.png"  /></a>';
					}
				} else {
					$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="publisheds(' . $key . ',&quot;1&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'publish_x.png"  /></a>';
				}

				$widgetContent .= '</td>	<td class="vtst-field-td-middleit">';

				if ($inc == 1) {
					$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>';
				} elseif ($inc == $nooffields) {
					$widgetContent .= '<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
				} else {
					$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>
									<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;widget&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
				}

				$widgetContent .= '</td>	<input type="hidden" name="check_mandatory_hidden' . $key . '" value="' . $field->fieldid . '"><td class="vtst-field-td-middleit">
								<input type="checkbox" value="' . $field->fieldlabel . '" name="check_mandatory' . $key . '" id="check' . $key . '"' . @$mandatory;

				$widgetContent .= '</td>	<td class="vtst-field-td-middleit" id="field_label_display_td' . $key . '">
								<input type="text" id="field_label_display_textbox' . $key . '" class="readonly-text"
										value="' . $field->fieldlabel . '"  /></td>
								</tr>';
				$inc++;
			}

			$widgetContent .= '</td>
										</tr>
									</tbody>
								</table></div>
								<!--<p class="submit">Please use the short code <b> [display_widget_area]</b> in widgets
								</p>!-->
								<input type="hidden" name="widget_field_posted" value="Posted" />
								<input type="hidden" name="Shortcode" value="' . $edit_fild . '" id="shortcode"/>
                                                               <input type="button" class="bottom-btn submit-add-to-menu" name="sync_crm_fields" value="Fetch CRM Fields"
							 onclick="goToTop(); syncCrmFields(&quot;' . admin_Url() . '&quot;,&quot;widget&quot;,&quot;smack_wp_vtiger_contact_fields-tmp&quot;,&quot;onCreate&quot;);"/>
                     <input type="submit" value="Save Field Settings" class="bottom-btn submit-add-to-menu"
								name="saveContactFields"/>
                        <input type="Submit" class="bottom-btn submit-add-to-menu" name="make_mandatory_contact"
								id="make_mandatory" value="Save Mandatory Fields" onclick="makeMandatory()" />
								<input type="button" class="bottom-btn submit-add-to-menu" name="save_display_name"
								id="save_display_name" value="Save Labels" onclick="goToTop(); saveDisplayName(&quot;' . admin_Url() . '&quot;,&quot;saveDisplayName&quot;,&quot;save_display_name&quot;,&quot;widget&quot;,&quot;smack_wp_vtiger_lead_fields-tmp&quot;,&quot;onCreate&quot;);" />
<table class="form_setting">
<tr><td class="f_text">Form Settings:</td></tr>
<tr>
<td><div style="padding:2px;">Assign Leads to User:</div></td>
<td>
<div>';
//$widgetContent .= '</select>';
			global $wpdb;
			if (!empty($_REQUEST['EditShortCode']) && isset($_POST['create_shortcode'])) {
				$error_message = $_POST['error_message'];
				$success_message = $_POST['success_message'];
				$enable_url = $_POST['enable_url'];
				$captcha = $_POST['generatecaptcha'];
				$success = $_POST['success'];
				$submit = $_POST['submit'];
				$failure = $_POST['failure'];
				$assign = $_POST['assigned'];
				$edit_fild = $_REQUEST['EditShortCode'];

				$ress = $wpdb->query(
					$wpdb->prepare(
						"UPDATE create_shortcode SET error_message = '" . $error_message . "',success_message ='" . $success_message . "', url_redirection = '" . $enable_url . "', captcha = '" . $captcha . "', assign = '" . $assign . "'
											        WHERE `shortcode` = '" . $edit_fild . "'"
					));

			}

			if (!empty($_REQUEST['EditShortCode'])) {
				$shortCode = sanitize_text_field($_REQUEST['EditShortCode']);
				//get the form settings from database
				$formSettings = $wpdb->get_results($wpdb->prepare("SELECT * FROM `create_shortcode` WHERE `shortcode` = '" . $shortCode . "'"));

				if (isset($formSettings) && isset($formSettings[0])) {

					$sucMessage = $formSettings[0]->success_message;
					$assignUser = $formSettings[0]->assign;
					$errMessage = $formSettings[0]->error_message;
					$redirection = $formSettings[0]->url_redirection;
					$captcha = $formSettings[0]->captcha;
				}
			}

			if (@$captcha == 'yes') {
				$checkCaptcha = 'checked="checked"';
			}

			$widgetContent .= '<select id="assignto" name="assigned" class="assign-leads"><option>admin</option>';
			$blogusers = get_users(array('fields' => array('display_name', 'user_login')));
			foreach ($blogusers as $user) {
				$selected = 0;
				if ($user->user_login == @$assignUser && $selected == 0) {
					$check = "selected='selected'";
					$selected++;
				} else {
					$check = "";
				}
				$widgetContent .= '<option ' . $check . ' ' . @$assignUser . ' value=' . esc_html($user->user_login) . '>' . esc_html($user->display_name) . '</option>';
			}

			$widgetContent .= '</div>
				</div></td></tr>

<tr><td>Error Message Submission :</td>
<td><input type="text" name="error_message" value="' . @$errMessage . '" placeholder="Error Message"</td></tr>

<tr><td>Success Message Submission :</td>
<td><input type="text" name="success_message" value="' . @$sucMessage . '" placeholder="Success Message"</td></tr>

<tr><td>Enable URl Redirection :</td>
<td><input type="text" name="enable_url" value="' . @$redirection . '" placeholder="Page id or Post id"</td></tr>

<tr><td>Enable Google Captcha :</td>
<td><input type="checkbox" name="generatecaptcha" ' . @$checkCaptcha . ' id="generatecaptcha"  value="yes"/></td>
</tr>
<tr><td><input type="submit" name="create_shortcode" class="bottom-btn submit-add-to-menu"  value="Generate Shortcode"></td></tr>
	</table>
		</form>
	</div>

	<div class="right-side-content" >

	</div>';
			echo $widgetContent;
		} else {
			$widgetContent = "<div style='margin-top:20px;font-weight:bold;'>
					Please enter a valid database <a href=" . admin_url() . "admin.php?page=vtlc&action=plugin_settings>settings</a>
					</div>";
			echo $widgetContent;
		}
	}

	/**
	 *
	 * Function to get vtiger fields from the database
	 */
	function vtiger_db_fields() {
		global $plugin_url_vtlc;
		global $wpdb;
		$config = get_option('Vts_vtpl_settings');
		if (isset($_POST['hostname'])) {
			$config['hostname'] = $_POST['hostname'];
			$config['dbuser'] = $_POST['dbuser'];
			$config['dbname'] = $_POST['dbname'];
			$config['dbpass'] = $_POST['dbpass'];
			//	$config ['appkey'] = $_POST ['appkey'];

			update_option('Vts_vtpl_settings', $config);
		} else {
			@$edit_fild = $_REQUEST['EditShortCode'];

			if (($_REQUEST['action'] = 'vtiger_db_fields') && (empty($_REQUEST['EditShortCode']))) {
				$config = get_option('Vts_vtpl_settings');
				$config_field = get_option("Vts_vtpl_field_settings");

			} else {
				$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
				$new_data = json_decode("$alloweded2->select_field", true);
				$config_field['fieldlist'] = $new_data;
			}
		}

		//code for save mandatory fields leads from
		if ($_REQUEST['action'] == 'vtiger_db_fields' && empty($_REQUEST['EditShortCode']) && isset($_POST['make_mandatory_leads']) && $_POST['make_mandatory_leads'] == 'Save Mandatory Fields') {

			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

			foreach ($_POST as $key => $value) {

				$_POST[$key] = sanitize_text_field($value);

			}

			$vtiger_db_fields_leads['vtiger_db_fields_leads_mandatoryfields'] = array();
			if (isset($_POST['no_of_vt_fields'])) {
				$fieldArr = array();
				for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {

					$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");

					if (isset($_POST["check_mandatory$i"])) {

						array_push($fieldArr, $_POST["check_mandatory_hidden$i"]);
						$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");
					}
				}

				update_option('vtiger_db_fields_leads_mandatoryfields', $fieldArr);

			}
		}

		//code for save mandatory fields leads from
		if ($_REQUEST['action'] == 'vtiger_db_fields' && !empty($_REQUEST['EditShortCode']) && isset($_POST['make_mandatory_leads']) && $_POST['make_mandatory_leads'] == 'Save Mandatory Fields') {

			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

			foreach ($_POST as $key => $value) {

				$_POST[$key] = sanitize_text_field($value);

			}

			$vtiger_db_fields_leads['vtiger_db_fields_leads_mandatoryfields'] = array();
			if (isset($_POST['no_of_vt_fields'])) {
				$fieldArr = array();
				for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {

					$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");

					if (isset($_POST["check_mandatory$i"])) {
						array_push($fieldArr, $_POST["check_mandatory_hidden$i"]);
						$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='" . $_POST["check_mandatory_hidden$i"] . "'");
					}
				}

				update_option('vtiger_db_fields_leads_mandatoryfields', $fieldArr);

			}
		}

		if (!empty($config['hostname']) && !empty($config['dbuser'])) {
			$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,block FROM vtiger_field WHERE tabid = 7  ORDER BY block, sequence");

			if (!is_array($config_field['fieldlist'])) {
				$config_field['fieldlist'] = array();
			}

		} elseif (!empty($_POST['hostname']) && !empty($_POST['dbuser'])) {
			$vtdb = new wpdb($_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['hostname']);
			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 ORDER BY block, sequence");

			if (!is_array($config_field['fieldlist'])) {
				$config_field['fieldlist'] = array();
			}
		}

		{
			$content = '';
		}

		$content .= '<div>
	<div   class="left-side-content" class="upgradetopro" id="upgradetopro" style="display:none;">This feature is only available in Pro Version, Please <a href="http://mastersoftwaretechnologies.com//wp-vtiger-pro.html">UPGRADE TO PRO</a></div>
	<div class="messageBox" id="message-box" style="display:none;" ><b>Successfully Saved!</b></div>
		<form id="vtst_vtlc_field_form"
			action="' . $_SERVER['REQUEST_URI'] . '" method="post">';

		if (($_REQUEST['action'] = 'vtiger_db_fields') && (!empty($_REQUEST['EditShortCode']))) {
			echo '<h3>[Vts-tiger-New-form name="' . $edit_fild . '"]</h3>';

			if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings') {

				foreach ($_POST as $key => $value) {

					$_POST[$key] = sanitize_text_field($value);

				}

				$config_widget_field['widgetfieldlist'] = array();
				if (isset($_POST['no_of_vt_fields'])) {
					$fieldArr = array();
					for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
						if (isset($_POST["vtst_vtlc_field$i"])) {

							array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
						}
					}

					$data_array = json_encode($config_widget_field['widgetfieldlist'] = $fieldArr);

				}

				$datanew = json_encode($data_array);

				if ($datanew) {

					$_POST['assigned'] = sanitize_text_field($_POST['assigned']);
					$captcha = $_POST['generatecaptcha'];
					$error_message = $_POST['error_message'];
					$success_message = $_POST['success_message'];
					$enable_url = $_POST['enable_url'];
					//changed to prepare
					$rows_affected = $wpdb->query(
						$wpdb->prepare(
							"UPDATE create_shortcode SET data = '" . json_encode($_POST) . "', assign = '" . $_POST['assigned'] . "', select_field = '" . $data_array . "' , captcha = '" . $_POST['generatecaptcha'] . "' , error_message = '" . $error_message . "', success_message = '" . $success_message . "', url_redirection = '" . $enable_url . "'
									        WHERE `shortcode` = '" . $edit_fild . "'"
						));
				}

				?>

<script>
window.location.href ="admin.php?page=vtlc&action=vtiger_db_fields&EditShortCode=<?php echo $edit_fild?>";
</script>;
	<?php
      }

		}

		if (!empty($allowedFields)) {
			global $wpdb;
			if (($_REQUEST['action'] = 'vtiger_db_fields') && (empty($_REQUEST['EditShortCode']))) {

				//code for create shortcode for leads form
				if (isset($_POST['create_shortcode']) && $_POST['create_shortcode'] == 'Generate Shortcode') {

					foreach ($_POST as $key => $value) {
						$_POST[$key] = sanitize_text_field($value);
					}
					$datanew = json_encode($_POST);
					$shortcode = createRandomPassword();
					echo '<h3>[Vts-tiger-New-form name="' . $shortcode . '"]</h3>';
					if (isset($_POST['field_posted'])) {

						if (isset($_POST['no_of_vt_fields'])) {
							$fieldArr = array();
							for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
								if (isset($_POST["vtst_vtlc_field$i"])) {
									array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
								}
							}

						}
					}
					$addval = json_encode($fieldArr);

					if ($datanew) {

						$error_message = $_POST['error_message'];
						$success_message = $_POST['success_message'];
						$enable_url = $_POST['enable_url'];
						$captcha = $_POST['generatecaptcha'];
						$success = $_POST['success'];
						$submit = $_POST['submit'];
						$failure = $_POST['failure'];

						$_POST['assigned'] = sanitize_text_field($_POST['assigned']);

						$res = $wpdb->insert('create_shortcode', array('shortcode' => $shortcode, 'data' => $datanew,
							'assign' => $_POST['assigned'], 'select_field' => $addval, 'captcha' =>
							$captcha, 'success' => 0,
							'submit' => 0, 'failure' => 0, 'error_message' => $error_message,
							'success_message' => $success_message, 'url_redirection' => $enable_url), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));

					}

				}
				//code to save the leads form fields
				if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings') {

					foreach ($_POST as $key => $value) {
						$_POST[$key] = sanitize_text_field($value);

					}

					$config_widget_field['fieldlist'] = array();

					if (isset($_POST['no_of_vt_fields'])) {
						$fieldArr = array();
						for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
							if (isset($_POST["vtst_vtlc_field$i"])) {

								array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
							}
						}

						$data_array = json_encode($config_widget_field['fieldlist'] = $fieldArr);

					}
					$update = update_option('Vts_vtpl_field_settings', $config_widget_field);

					$linkUrl = site_url() . '/wp-admin/admin.php?page=vtlc&action=vtiger_db_fields';
					echo "<script>window.location.href= '" . $linkUrl . "'; </script>";

					
}
			} else if (($_REQUEST['action'] = 'vtiger_db_fields') && (!empty($_REQUEST['EditShortCode']))) {
				if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings') {

					foreach ($_POST as $key => $value) {

						$_POST[$key] = sanitize_text_field($value);

					}

					$config_widget_field['fieldlist'] = array();

					if (isset($_POST['no_of_vt_fields'])) {
						$fieldArr = array();
						for ($i = 0; $i <= $_POST['no_of_vt_fields']; $i++) {
							if (isset($_POST["vtst_vtlc_field$i"])) {

								array_push($fieldArr, $_POST["vtst_vtlc_field_hidden$i"]);
							}
						}

						$data_array = json_encode($config_widget_field['fieldlist'] = $fieldArr);

					}

					$datanew = json_encode($data_array);

					if ($datanew) {

						$_POST['assigned'] = sanitize_text_field($_POST['assigned']);
						$captcha = $_POST['generatecaptcha'];

						//changed to prepare
						$rows_affected = $wpdb->query(
							$wpdb->prepare(
								"UPDATE create_shortcode SET data = '" . json_encode($_POST) . "', assign = '" . $_POST['assigned'] . "', select_field = '" . $data_array . "' , captcha = '" . $_POST['generatecaptcha'] . "'
						        WHERE `shortcode` = '" . $edit_fild . "'"
							));
						?>

<script>

window.location.href ="admin.php?page=vtlc&action=vtiger_db_fields&EditShortCode=<?php echo $edit_fild?>";

</script>;
	<?php

					}
				}

			}

			if (!empty($_REQUEST['EditShortCode']) && isset($_POST['create_shortcode'])) {

				$error_message = $_POST['error_message'];
				$success_message = $_POST['success_message'];
				$enable_url = $_POST['enable_url'];
				$captcha = $_POST['generatecaptcha'];
				$success = $_POST['success'];
				$submit = $_POST['submit'];
				$failure = $_POST['failure'];
				$assign = $_POST['assigned'];
				$edit_fild = $_REQUEST['EditShortCode'];

				$ress = $wpdb->query(
					$wpdb->prepare(
						"UPDATE create_shortcode SET error_message = '" . $error_message . "',success_message ='" . $success_message . "', url_redirection = '" . $enable_url . "', captcha = '" . $captcha . "', assign = '" . $assign . "'
											        WHERE `shortcode` = '" . $edit_fild . "'"
					));

			}

			$wp_tiger_contact_form_attempts = get_option('vts_tiger-contact-form-attempts');
			$res = $wp_tiger_contact_form_attempts;
			$total = $res['total'];
			$success = $res['success'];
			$failure = $total - $success;

			?>

<?php

			$content .= '<form name="frm" method="post" action="admin.php?page=vtlc&action=vtiger_db_fields">
				<input type="hidden" name="submit" value="0" />
				<input type="hidden" name="success" value="0" />
				<input type="hidden" name="failure" value="0" /><div style="padding:2px;">';

			$content .= '</select></div>
				</div><br/>

			<input type="hidden" name="lead" value="lead" />
			<input type="button" class="bottom-btn submit-add-to-menu"
				name="sync_crm_fields" value="Fetch CRM Fields"
				onclick="goToTop(); syncCrmFields(&quot;' . admin_Url() . '&quot;,&quot;lead&quot;,&quot;smack_wp_vtiger_contact_fields-tmp&quot;,&quot;onCreate&quot;);" />

			<input type="submit"  value="Save Field Settings"
				class="bottom-btn submit-add-to-menu" name="Submit" />
			<input type="submit" class="bottom-btn submit-add-to-menu"
				name="make_mandatory_leads" id="make_mandatory"
				value="Save Mandatory Fields"  /> ';

			if (!empty($_REQUEST['EditShortCode'])) {

				$content .= '<input type="button" class="bottom-btn submit-add-to-menu" name="save_display_name"
								id="save_display_name" value="Save Labels" onclick="goToTop(); saveDisplayNameLeadsShortCode(&quot;' . admin_Url() . '&quot;,&quot;saveDisplayName&quot;,&quot;save_display_name&quot;,&quot;lead&quot;,&quot;smack_wp_vtiger_lead_fields-tmp&quot;,&quot;onCreate&quot;, &quot;' . $_REQUEST['EditShortCode'] . '&quot;);" />';
			} else {

				$content .= '<input type="button" class="bottom-btn submit-add-to-menu" name="save_display_name"
								id="save_display_name" value="Save Labels" onclick="goToTop(); saveDisplayNameLeads(&quot;' . admin_Url() . '&quot;,&quot;saveDisplayName&quot;,&quot;save_display_name&quot;,&quot;lead&quot;,&quot;smack_wp_vtiger_lead_fields-tmp&quot;,&quot;onCreate&quot;);" />';
			}

			$content .= '<img id="loading-image" style="display: none; float: right; padding-top: 5px; padding-left: 5px;" src="">
					<!--<p>Please use the short code <b>[display_contact_page]</b> in page or post</p>!--><br/>
		<div id="fieldtable">
			<table class="tableborder">
				<tr class="vtst_alt">
					<th style="width: 20px;"><input type="hidden" name="selectall"
						id="selectall"
						onclick="select_allfields(\'vtst_vtlc_field_form\',\'lead\')" /></th>
					<th style="width: 140px;"><h5>Field Name</h5></th>
					<th style="width: 80px;"><h5>Show Field</h5></th>
					<th style="width: 100px;"><h5>Order</h5></th>
					<th style="width: 120px;"><h5>Mandatory Fields</h5></th>
					<th style="width: 200px;"><h5>Field Label Display</h5></th>
				</tr>
				<tbody>
					<tr valign="top">

						<td><input type="hidden" id="no_of_vt_fields"
							name="no_of_vt_fields" value="' . sizeof($allowedFields) . '">';

			$nooffields = count($allowedFields);
			$inc = 1;
			foreach ($allowedFields as $key => $field) {
				?>
				<?php if ($inc % 2 == 1) {

					$content .= '<tr class="vtst_highlight">';
				} else {

					$content .= '<tr class="vtst_highlight vtst_alt">';
				}
				$typeofdata = explode('~', $field->typeofdata);

				$content .= '<td class="vtst-field-td-middleit"><input type="hidden"
							value="' . $field->fieldlabel . '"id="field_label' . $key . '">
				<input type="hidden" value="' . $typeofdata[1] . '"	id="field_type' . $key . '">
				<input type="hidden" class="checkBoxSelect_' . $key . '"  id="vts_label' . $key . '"
							name="vtst_vtlc_field_hidden' . $key . '"
							value="' . $field->fieldid . '" />';
				// echo "<pre>";
				// print_r($config_field['fieldlist']);
				if ($typeofdata[1] == 'M') {

					$checked = 'checked="checked" ';
					$mandatory = 'checked="checked" ';

					foreach ($config_field['fieldlist'] as $key2 => $value) {
						# code...

						if ($value == $field->fieldid) {

							$checked = 'checked="checked"';
							$mandatory = 'checked="checked" ';
							break;
						} else {
							$checked = '';
							$mandatory = '';
						}
						//$checked = '';
						//$mandatory = '';
					}

				} else {
					$checked = '';
					$mandatory = '';

					foreach ($config_field['fieldlist'] as $key2 => $value) {
						# code...

						if ($value == $field->fieldid) {

							$checked = 'checked="checked"';
							$mandatory = 'checked="checked" ';
							break;
						} else {
							$checked = '';
							$mandatory = '';
						}
						//$checked = '';
						//$mandatory = '';
					}

				}
				if ($typeofdata[1] == 'M') {
					$mandatory = 'checked="checked"';
					$checked = 'checked="checked"';
					$content .= '<input type="hidden" value="' . $field->fieldname . '"
							id="vtst_vtlc_field' . $key . '" name="vtst_vtlc_field' . $key . '" />
							<input type="checkbox"   class="' . $key . '" onclick="return checkBoxSelect(this)"   value="' . $field->fieldname . '"' . $checked . ' />';
				} else {
					$content .= '<input type="checkbox"
								value="' . $field->fieldname . '"
									id="vtst_vtlc_field' . $key . '"
									name="vtst_vtlc_field' . $key . '"' . $checked . '/>';
				}
				$content .= "</td>
						<td>$field->fieldlabel";
				if ($typeofdata[1] == 'M') {
					$mandatory = 'checked="checked"';
					$checked = 'checked="checked"';
					$content .= '<span style="color: #FF4B33">&nbsp;*</span>';
				}
				$content .= '</td>';
				$contentUrl = WP_CONTENT_URL;
				$imagepath = "{$plugin_url_vtlc}/images/";
				$content .= '<td class="vtst-field-td-middleit">';
				if (in_array($field->fieldid, $config_field['fieldlist'])) {

					if ($typeofdata[1] == 'M') {
						$mandatory = 'checked="checked"';
						$checked = 'checked="checked"';
						$content .= '<img  id="vtst-field-td-middleit"src="' . $imagepath . 'tick_strict.png"
							onclick="upgradetopro()" />';
					} else {
						$content .= '<a id="publish3" class="smack_pointer" onclick="publisheds(' . $key . ',&quot;0&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit"src="' . $imagepath . 'tick.png" /></a>';
					}
				} else {
					$content .= '<a id="publish3" class="smack_pointer" onclick="publisheds(' . $key . ',&quot;1&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'publish_x.png" /></a>';
				}
				$content .= '</td>
					<td class="vtst-field-td-middleit">';
				if ($inc == 1) {
					$content .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);"><img
								src="' . $imagepath . 'downarrow.png" /></a>';
				} elseif ($inc == $nooffields) {
					$content .= '<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;)"><img
								src="' . $imagepath . 'uparrow.png" /></a>';
				} else {
					$content .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);"><img
								src="' . $imagepath . 'downarrow.png" /></a> <a
							class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;lead&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;)"><img
								src="' . $imagepath . 'uparrow.png" /></a>';
				}
				$content .= '</td>
						<input type="hidden" name="check_mandatory_hidden' . $key . '" value="' . $field->fieldid . '">
						<td class="vtst-field-td-middleit"><input type="checkbox"
							name="check_mandatory' . $key . '" value="' . $field->fieldlabel . '" id="check' . $key . '"';

				if ($typeofdata[1] == 'M') {
					$mandatory = 'checked="checked"';
					$checked = 'checked="checked"';
					$content .= 'checked="checked" ';

				}
				$content .= ' /></td>
						<td class="vtst-field-td-middleit"
							id="field_label_display_td' . $key . '"><input type="text"
							id="field_label_display_textbox' . $key . '" class="readonly-text"
							value="' . $field->fieldlabel . '"  /></td>
					</tr>';
				$inc++;
			}

			$content .= '</td>
					</tr>
				</tbody>
			</table></div>
                  <input type="hidden" name="field_posted" value="posted" />
			<input type="hidden" name="Shortcode" value="'.$edit_fild . '" id="shortcode"/>
                        <!--<p> Please use the short code <b>[display_contact_page]</b> in page or post</p>!--> <br/>
                       <input type="button" class="bottom-btn submit-add-to-menu"
				name="sync_crm_fields" value="Fetch CRM Fields"
				onclick="goToTop(); syncCrmFields(&quot;' . admin_Url() . '&quot;,&quot;lead&quot;,&quot;smack_wp_vtiger_contact_fields-tmp&quot;,&quot;onCreate&quot;);" />
                        <input type="submit" value="Save Field Settings"
                                class="bottom-btn submit-add-to-menu" name="Submit" />
                        <input type="submit" class="bottom-btn submit-add-to-menu"
				name="make_mandatory_leads" id="make_mandatory"
				value="Save Mandatory Fields"  />
			<input type="button" class="bottom-btn submit-add-to-menu" name="save_display_name" id="save_display_name" value="Save Labels" onclick="goToTop(); saveDisplayNameLeadsShortCode(&quot;' . admin_Url() . '&quot;,&quot;saveDisplayName&quot;,&quot;save_display_name&quot;,&quot;lead&quot;,&quot;smack_wp_vtiger_lead_fields-tmp&quot;,&quot;onCreate&quot;, &quot;' . $_REQUEST['EditShortCode'] . '&quot;);" />

<table class="form_setting">
<tr><td class="f_text">Form Settings:</td></tr>

<tr>
<td><div style="padding:2px;">Assign Leads to User:</div></td>
<td>
<div>
<select id="assignto" name="assigned" class="assign-leads"><option>admin</option>';
			$blogusers = get_users(array('fields' => array('display_name', 'user_login')));
			foreach ($blogusers as $user) {
				$content .= '<option value=' . esc_html($user->user_login) . '>' . esc_html($user->display_name) . '</option>';
			}?><?php
global $wpdb;

			if (!empty($_REQUEST['EditShortCode'])) {
				$shortCode = sanitize_text_field($_REQUEST['EditShortCode']);
				//get the form settings from database
				$formSettings = $wpdb->get_results($wpdb->prepare("SELECT * FROM `create_shortcode` WHERE `shortcode` = '" . $shortCode . "'"));

				if (isset($formSettings) && isset($formSettings[0])) {

					$sucMessage = $formSettings[0]->success_message;
					$errMessage = $formSettings[0]->error_message;
					$redirection = $formSettings[0]->url_redirection;
					$captcha = $formSettings[0]->captcha;
				}
			}

			if ($captcha == 'yes') {
				$checkCaptcha = 'checked="checked"';
			}
			$content .= '</select></div>
				</div></td></tr>

<tr><td>Error Message Submission :</td>
<td><input type="text" name="error_message" value="' . $errMessage . '" placeholder="Error Message"</td></tr>

<tr><td>Success Message Submission :</td>
<td><input type="text" name="success_message" value="' . $sucMessage . '" placeholder="Success Message"</td></tr>

<tr><td>Enable URl Redirection :</td>
<td><input type="text" name="enable_url" value="' . $redirection . '" placeholder="Page id or Post id"</td></tr>

<tr><td>Enable Google Captcha :</td>
<td><input type="checkbox" name="generatecaptcha" ' . $checkCaptcha . ' id="generatecaptcha"  value="yes"/></td>
</tr>
<tr><td><input type="submit" name="create_shortcode"  class="bottom-btn submit-add-to-menu" value="Generate Shortcode"></td></tr>
	</table>





		</form>
	</div>

	<div class="right-side-content" >

	</div>';

			echo $content;

		} else {
			$Content = "<div style='margin-top:20px;font-weight:bold;'>
					Please enter a valid database <a href=" . admin_url() . "admin.php?page=vtlc&action=plugin_settings>settings</a>
					</div>";
			echo $Content;
		}
		// if (($_REQUEST['action'] = 'vtiger_db_fields') && (empty($_REQUEST['EditShortCode'])) && isset($_POST['Generate Shortcode']) && $_POST['Generate Shortcode'] == 'Generate Shortcode') {
		// 	echo "<pre>";
		// 	print_r($_REQUEST['action']);die;
		// 	$content .= '<input type="submit"
		// 		class="button-create-shortcode" name="create_shortcode"
		// 		id="create_shortcode" value="Generate Shortcodess"
		// 		 />';
		// }

		?>
<?php	}

	function deleteShortCodes() {
		global $wpdb;
		$req_del = $_GET['deleteShortCode'];
		if ($_REQUEST['action'] == 'deleteShortCodes') {
			$delte_reqst = $wpdb->query($wpdb->prepare("DELETE FROM create_shortcode WHERE shortcode = %s ", $req_del));
			if ($delte_reqst) {?>
	<script>window.location.href ="admin.php?page=vtlc&action=vts_tiger_listShortcodes";</script>;
	<?php
}
		}
	}

}
function createRandomPassword() {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double) microtime() * 1000000);
	$i = 0;
	$pass = '';

	while ($i <= 5) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}
?>
