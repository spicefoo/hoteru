<?php
/*********************************************************************************
 *
 * Plugin Name: Vtiger Lead Capture
 * Plugin URI: http://mastersoftwaresolutions.com/
 * Description: Vtiger Lead Capture Plugin allows to fetch your VTiger crm leads and contacts form fields and let you generate forms for your wordpress website.
 * Version: 1.1.1
 * Author: mastersoftwaresolutions
 * Author URI: http://mastersoftwaresolutions.com/

 ********************************************************************************/

global $plugin_url_vtlc;
$plugin_url_vtlc = plugins_url('', __FILE__);
global $plugin_dir_vtlc;
$plugin_dir_vtlc = plugin_dir_path(__FILE__);

require_once "{$plugin_dir_vtlc}/vtlcWp.php";
require_once "{$plugin_dir_vtlc}/vtlc_genratershortcodes.php";
require_once "{$plugin_dir_vtlc}/navMenu.php";
require_once "{$plugin_dir_vtlc}/VtlcWPAdminPages.php";
require_once "{$plugin_dir_vtlc}/CaptureRegisteringUsers.php";

add_action('init', array('VTSWPVT', 'init'));

register_activation_hook(__FILE__, 'vtst_vtstiger_activate');

register_deactivation_hook(__FILE__, 'vtst_vtstiger_deactivate');

// Admin menu settings
function vtst_Vtstigermenu() {
	global $plugin_url_vtlc;
	add_menu_page('Vtiger Lead Capture Settings', 'Vtiger Lead Capture', 'manage_options', 'vtlc', 'vtst_vtstiger_settings', "{$plugin_url_vtlc}/images/icon.png");
}

function vtst_LoadVtsTigerScript() {
	global $plugin_url_vtlc;
	wp_enqueue_script("vtlc-script", "{$plugin_url_vtlc}/js/vtlc_script.js", array("jquery"));
	wp_enqueue_style("vtlc-css", "{$plugin_url_vtlc}/css/VtlcStyle.css");
}
/*
function vtst_vtstiger_activate() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;
	//create table for shortcodes
	$sql = "CREATE TABLE create_shortcode (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `shortcode` varchar(255) NOT NULL,
		  `data` longtext NOT NULL,
		  `assign` varchar(255) NOT NULL,
		  `select_field` longtext NOT NULL,
		  `captcha` varchar(255) NOT NULL,
 		  `submit` varchar(255) NOT NULL,
 		  `success` varchar(255) NOT NULL,
		  `failure` varchar(255) NOT NULL,
		  `error_message` varchar(255) NOT NULL,
		  `success_message` varchar(255) NOT NULL,
		  `url_redirection` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		);";
	dbDelta($sql);

	//create table for captcha settings
	$sqlCaptcha = "CREATE TABLE IF NOT EXISTS `wp_captcha` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `public_key` varchar(255) NOT NULL,
				  `private_key` varchar(255) NOT NULL,
				  `captcha_visible` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

			--
			-- Dumping data for table `wp_captcha`
			--

			INSERT INTO `wp_captcha` (`id`, `public_key`, `private_key`, `captcha_visible`) VALUES
			(1, 'YOUR_PUBLIC_KEY', 'YOUR_PRIVATE_KEY', 'no'),";

	dbDelta($sql);

}*/
function vtst_vtstiger_activate() 
   {
       global$wpdb;
   
			 $sql = "CREATE TABLE IF NOT EXISTS create_shortcode (
			  ID int(11) unsigned NOT NULL AUTO_INCREMENT,
			  shortcode varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			  data varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			  assign varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  select_field varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  captcha varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  submit varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  success varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  failure varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  error_message varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  url_redirection varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			  PRIMARY KEY (ID)
			  
			)";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$sqlCaptcha = 'CREATE TABLE IF NOT EXISTS wp_captcha (
			ID int(11)  NOT NULL AUTO_INCREMENT,
			public_key varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			private_key varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			captcha_visible varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL ,
			PRIMARY KEY (ID)
			)';


			//$sqlInsert = 'INSERT INTO wp_captcha (ID, public_key, private_key, captcha_visible) VALUES (1, "YOUR_PUBLIC_KEY", "YOUR_PRIVATE_KEY", "no")';

		   	$sqlInsert = 'INSERT INTO wp_captcha (ID, public_key, private_key, captcha_visible) VALUES (1, "YOUR_PUBLIC_KEY", "YOUR_PRIVATE_KEY", "no") ON DUPLICATE KEY UPDATE public_key="YOUR_PUBLIC_KEY", private_key = "YOUR_PRIVATE_KEY", captcha_visible = "no" ';  
		
		dbDelta($sql);
		dbDelta($sqlCaptcha);
		dbDelta($sqlInsert);

        
    }
      register_activation_hook(__FILE__,'vtst_vtstiger_activate');
       
function vtst_vtstiger_deactivate() {
	delete_option('Vts_vtpl_settings');
	delete_option('Vts_vtpl_field_settings');
	delete_option('Vts_vtlc_widget_field_settings');
	delete_option('vtlc-contact-form-attempts');
	delete_option('vtlc-contact-widget-form-attempts');
}

function vtst_VtsTigertestAccess() {
	global $plugin_dir_vtlc;
	require_once "{$plugin_dir_vtlc}/test-access.php";
	die;
}

add_action('wp_ajax_VtsTigertestAccess', 'vtst_VtsTigertestAccess');

function vtst_VtstestVtigerAccess() {
	global $plugin_dir_vtlc;
	require_once "{$plugin_dir_vtlc}/test-vtiger-access.php";
	die;
}

add_action('wp_ajax_VtstestVtigerAccess', 'vtst_VtstestVtigerAccess');
function vtst_adminActions() {

	global $plugin_url_vtlc;
	global $wpdb;
	$config = get_option('Vts_vtpl_settings');
	//$topContent = $this->topContent();
	$imagepath = "{$plugin_url_vtlc}/images/";
	$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
	foreach ($_POST as $key => $value) {
		//empty the value if sanitize fails
		$_POST[$key] = sanitize_text_field($value);
	}
	if ($_POST['module'] == 'widget') {
		$config_widget_field = get_option("Vts_vtlc_widget_field_settings");
		$modeled = 'widget';
		if ($_POST['savedisplayname'] == 'save_display_name') {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,sequence FROM vtiger_field WHERE tabid = 4  ORDER BY block, sequence");
			for ($i = 0; $i < ($_POST['no_of_fields']); $i++) {
				$label = $_POST['display_label' . $i];
				$field = $allowedFields[$i]->fieldid;
				// sanitize field label
				$label = sanitize_text_field($label);
				$query1 = $vtdb->query("UPDATE `vtiger_field` SET `fieldlabel`='$label' WHERE `fieldid`='$field'");

			}
		}
		if (!empty($config['hostname']) && !empty($config['dbuser'])) {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,sequence FROM vtiger_field WHERE tabid = 4  ORDER BY block, sequence");

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		}
	}
	if ($_POST['module'] == 'lead') {
		$config_widget_field = get_option("Vts_vtpl_field_settings");
		$config_widget_field['widgetfieldlist'] = $config_widget_field['fieldlist'];
		$modeled = 'lead';
		if ($_POST['savedisplayname'] == 'save_display_name') {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,sequence FROM vtiger_field WHERE tabid = 7  ORDER BY block, sequence");
			for ($i = 0; $i < ($_POST['no_of_fields']); $i++) {
				$label = $_POST['display_label' . $i];
				$field = $allowedFields[$i]->fieldid;
				// sanitize field label
				if (preg_match('/^[A-Za-z0-9_-]*$/', $label)) {
					$query1 = $vtdb->query("UPDATE `vtiger_field` SET `fieldlabel`='$label' WHERE `fieldid`='$field'");
				}
			}
		}

		if (!empty($config['hostname']) && !empty($config['dbuser'])) {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,sequence FROM vtiger_field WHERE tabid = 7  ORDER BY block, sequence");

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		}
	}

	if (!empty($_POST['shortcode'])) {
		if ($_POST['module'] == 'widget') {
			$add = $_POST['option'];
			$edit_fild = $_REQUEST['shortcode'];

			// sanitize the shortcode
			if (preg_match('/^[A-Za-z0-9_-]*$/', $edit_fild)) {
				$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
				$new_data = json_decode("$alloweded2->select_field", true);
				if ($_POST['publish'] == '1') {
					if ($new_data == null) {
						$new_data = array();
						array_push($new_data, $add);
					} else {
						array_push($new_data, $add);
					}
					$newdata = json_encode($new_data);
					echo $wpdb->update('create_shortcode', array('select_field' => $newdata), array('shortcode' => $edit_fild), array('%s'), array('%s'));
				}
			}

			if ($_POST['publish'] == '0') {
				// sanitize the shortcode
				if (preg_match('/^[A-Za-z0-9_-]*$/', $edit_fild)) {
					$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
					$new_data = json_decode("$alloweded2->select_field", true);
					$key = array_search($add, $new_data);
					$unsetproduct = $key;

					unset($new_data[$unsetproduct]);
					$newdata = json_encode($new_data);
					echo $wpdb->update('create_shortcode', array('select_field' => $newdata), array('shortcode' => $edit_fild), array('%s'), array('%s'));
				}
			}
			$config_widget_field['widgetfieldlist'] = $new_data;

		}
		if ($_POST['module'] == 'lead') {

			$add = $_POST['option'];
			$edit_fild = $_REQUEST['shortcode'];
			// sanitize the shortcode
			if (preg_match('/^[A-Za-z0-9_-]*$/', $edit_fild)) {
				$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
				$new_data = json_decode("$alloweded2->select_field", true);
				if ($_POST['publish'] == '1') {
					if ($new_data == null) {
						$new_data = array();
						array_push($new_data, $add);
					} else {
						array_push($new_data, $add);
					}
					$newdata = json_encode($new_data);
					echo $wpdb->update('create_shortcode', array('select_field' => $newdata), array('shortcode' => $edit_fild), array('%s'), array('%s'));
				}
			}
			if ($_POST['publish'] == '0') {
				// sanitize the shortcode
				if (preg_match('/^[A-Za-z0-9_-]*$/', $edit_fild)) {
					$alloweded2 = $wpdb->get_row("SELECT * FROM `create_shortcode` WHERE `shortcode`='$edit_fild'");
					$new_data = json_decode("$alloweded2->select_field", true);
					$key = array_search($add, $new_data);
					$unsetproduct = $key;

					unset($new_data[$unsetproduct]);
					$newdata = json_encode($new_data);
					$wpdb->update('create_shortcode', array('select_field' => $newdata), array('shortcode' => $edit_fild), array('%s'), array('%s'));
				}

			}
			$config_widget_field['widgetfieldlist'] = $new_data;
		}

	}
	if ($_POST['direction'] == 'down') {

		$j = 1;
		for ($i = 0; $i < count($allowedFields); $i++) {
			$field = $allowedFields[$i]->fieldid;
			$seq = $j;
			$query1 = $vtdb->query("UPDATE `vtiger_field` SET `sequence`='$seq' WHERE `fieldid`='$field'");
			$j++;
		}
		$movedown = $_POST['shortcode1'];
		$sequence1 = $_POST['position'];
		$sequence2 = $_POST['position'] + 1;
		if ($allowedFields) {
			$seq1 = $allowedFields[$sequence1]->sequence;
			$seq2 = $allowedFields[$sequence1]->sequence + 1;
			$field1 = $allowedFields[$sequence1]->fieldid;
			$field2 = $allowedFields[$sequence2]->fieldid;
		}

		if ($field1) {
			$query1 = $vtdb->query("UPDATE `vtiger_field` SET `sequence`='$seq2' WHERE `fieldid`='$field1'");
		}
		if ($field2) {
			$query2 = $vtdb->query("UPDATE `vtiger_field` SET `sequence`='$seq1' WHERE `fieldid`='$field2'");
		}
	}
	if ($_POST['direction'] == 'up') {
		if ($allowedFields) {
			$j = 1;
			for ($i = 0; $i < count($allowedFields); $i++) {
				$field = $allowedFields[$i]->fieldid;
				$seq = $j;
				$query1 = $vtdb->query("UPDATE vtiger_field SET `sequence`='$seq' WHERE `fieldid`='$field'");
				$j++;

			}
		}
		$movedown = $_POST['shortcode1'];
		$sequence1 = $_POST['position'];

		$sequence2 = $_POST['position'] - 1;

		if ($allowedFields) {
			$seq1 = $allowedFields[$sequence1]->sequence;
			$seq2 = $allowedFields[$sequence1]->sequence - 1;
			$field1 = $allowedFields[$sequence1]->fieldid;
			$field2 = $allowedFields[$sequence2]->fieldid;
		}

		$chandeid = $_POST['option'];

		$allowedFields1 = $vtdb->get_results("SELECT * FROM `vtiger_field` WHERE `fieldid`='$moveid'");

		if ($field1) {
			$query1 = $vtdb->query("UPDATE `vtiger_field` SET `sequence`='$seq2' WHERE `fieldid`='$field1'");
		}
		if ($field2) {
			$query2 = $vtdb->query("UPDATE `vtiger_field` SET `sequence`='$seq1' WHERE `fieldid`='$field2'");
		}

	}

	if (!empty($allowedFields)) {
		global $wpdb;
		if (isset($_POST['create_shortcode']) && $_POST['create_shortcode'] == 'Generate Shortcode') {
			echo "</br>";
			foreach ($_POST as $key => $value) {
				//empty the value if sanitize fails
				$_POST[$key] = sanitize_text_field($value);
			}
			$datanew = json_encode($_POST);
			$shortcode = createRandomPassword();
			echo '<h3>[Vts-tiger-New-form name="' . $shortcode . '"]</h3>';
			if ($datanew) {
				$wpdb->insert('create_shortcode', array('shortcode' => $shortcode, 'data' => $datanew, 'assign' => $_POST['assigned']), array('%s', '%s'));
			}
		}
		if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings') {

			?>
				<script>
					saveSettings();
				</script>
			<?php
}

		$wp_tiger_contact_form_attempts = get_option('vts-tiger-contact-widget-form-attempts');

		echo '<div id="fieldtable">
							<table class="tableborder">
								<tr class="vtst_alt">
									<th style="width: 50px;"><input type="checkbox" name="selectall" id="selectall"
										onclick="select_allfields(\'vtst_vtlc_field_form\',\'widget\')" /></th>
									<th style="width: 200px;"><h5>Field Name</h5></th>
									<th style="width: 100px;"><h5>Show Field</h5></th>
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
									<input type="hidden" value="' . $field->fieldlabel . '"id="field_label' . $key . '">
									<input type="hidden" value="' . $typeofdata[1] . '" id="field_type' . $key . '">
									<input type="hidden" id="vts_label' . $key . '" name="vtst_vtlc_field_hidden' . $key . '"value="' . $field->fieldid . '" />';

			if ($typeofdata[1] == 'M') {
				$checked = '';
			} else {
				$checked = "";
			}

			if ($typeofdata[1] == 'M') {
				$widgetContent .= '<input type="hidden" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
										name="vtst_vtlc_field' . $key . '" />
									<input type="checkbox" value="' . $field->fieldname . '"' . $checked . '>';
			} else {
				$widgetContent .= '<input type="checkbox" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
									name="vtst_vtlc_field' . $key . '" ' . $checked . '>';
			}

			$widgetContent .= '</td>
								<td>' . $field->fieldlabel;
			if ($typeofdata[1] == 'M') {
				$widgetContent .= '<span style="color: #FF4B33">&nbsp;*</span>';
			}

			$widgetContent .= '</td>
								<td class="vtst-field-td-middleit">';

			if (in_array($field->fieldid, $config_widget_field['widgetfieldlist'])) {
				if ($typeofdata[1] == 'M') {
					$widgetContent .= '<img id="vtst-field-td-middleit" src="' . $imagepath . 'tick_strict.png" onclick="upgradetopro()" />';
				} else {
					$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="published(' . $key . ',&quot;0&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'tick.png"  /></a>';
				}
			} else {
				$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="published(' . $key . ',&quot;1&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'publish_x.png"  /></a>';
			}

			$widgetContent .= '</td>	<td class="vtst-field-td-middleit">';

			if ($inc == 1) {
				$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>';
			} elseif ($inc == $nooffields) {
				$widgetContent .= '<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
			} else {
				$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>
									<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
			}

			$widgetContent .= '</td>	<td class="vtst-field-td-middleit">
								<input type="checkbox" name="check' . $key . '" id="check' . $key . '"';

			if ($typeofdata[1] == 'M') {
				$widgetContent .= '  ';
			}

			$widgetContent .= '/></td>	<td class="vtst-field-td-middleit" id="field_label_display_td' . $key . '">
								<input type="text" id="field_label_display_textbox' . $key . '" class="readonly-text"
										value="' . $field->fieldlabel . '"  /></td>
								</tr>';
			$inc++;
		}

		$widgetContent .= '</td>
										</tr>
									</tbody>
								</table></div>


								</form>
							</div>';

		echo $widgetContent;
	} else {
		$widgetContent = "<div style='margin-top:20px;font-weight:bold;'>
					Please enter a valid database <a href=" . admin_url() . "admin.php?page=vtlc&action=plugin_settings>settings</a>
					</div>";
		echo $widgetContent;
	}
}

add_action('wp_ajax_adminActions', 'vtst_adminActions');
function vtst_captureWpUsers() {
	$config = get_option('Vts_vtpl_settings');
	if (!empty($config['hostname']) && !empty($config['dbuser'])) {
		$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

		$Contactuser = $vtdb->get_results("SELECT firstname,email FROM vtiger_contactdetails");
		$wpcantactuser = new WP_User_Query(array('role' => '', 'user_email' => ''));
		$wpuserdata = array();
		foreach ($wpcantactuser->results as $key => $wpuserget) {
			$newarray = array(
				"firstname" => $wpuserget->data->user_login,
				"email" => $wpuserget->data->user_email,
			);
			array_push($wpuserdata, (object) $newarray);

		}

		for ($i = 0; $i < count($Contactuser); $i++) {
			$user_id = username_exists($Contactuser[$i]->firstname);
			if (!$user_id and email_exists($Contactuser[$i]->email) == false) {
				$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
				$user_id = wp_create_user($Contactuser[$i]->firstname, $random_password, $Contactuser[$i]->email);
			} else {
				$random_password = __('User already exists.  Password inherited.');
			}
		}
	} else {
		echo "Conection Failed";
	}
}
add_action('wp_ajax_captureWpUsers', 'vtst_captureWpUsers');
function vtst_getCrmFields() {
	global $plugin_url_vtlc;
	$config = get_option('Vts_vtpl_settings');
	//$topContent = $this->topContent();
	$imagepath = "{$plugin_url_vtlc}/images/";
	$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
	if ($_POST['module'] == 'widget') {
		$config_widget_field = get_option("Vts_vtlc_widget_field_settings");

		$modeled = 'widget';
		if (!empty($config['hostname']) && !empty($config['dbuser'])) {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,block FROM vtiger_field WHERE tabid = 4  ORDER BY block, sequence");

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		}
	}
	if ($_POST['module'] == 'lead') {
		$config_widget_field = get_option("Vts_vtpl_field_settings");
		$config_widget_field['widgetfieldlist'] = $config_widget_field['fieldlist'];
		$modeled = 'lead';

		if (!empty($config['hostname']) && !empty($config['dbuser'])) {

			$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata,block FROM vtiger_field WHERE tabid = 7  ORDER BY block, sequence");

			if (!is_array($config_widget_field['widgetfieldlist'])) {
				$config_widget_field['widgetfieldlist'] = array();
			}
		}

	}

	if (!empty($allowedFields)) {
		global $wpdb;
		if (isset($_POST['create_shortcode']) && $_POST['create_shortcode'] == 'Generate Shortcode') {
			echo "</br>";
			foreach ($_POST as $key => $value) {
				//empty the value if sanitize fails
				$_POST[$key] = sanitize_text_field($value);
			}
			$datanew = json_encode($_POST);
			$shortcode = createRandomPassword();
			echo '<h3>[Vts-tiger-New-form name="' . $shortcode . '"]</h3>';
			if ($datanew) {
				$wpdb->insert('create_shortcode', array('shortcode' => $shortcode, 'data' => $datanew, 'assign' => $_POST['assigned']), array('%s', '%s'));
			}
		}
		if (isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings') {

			?>
				<script>
					saveSettings();
				</script>
			<?php
}

		$wp_tiger_contact_form_attempts = get_option('vts-tiger-contact-widget-form-attempts');

		echo '<div id="fieldtable">
							<table class="tableborder">
								<tr class="vtst_alt">
									<th style="width: 50px;"><input type="checkbox" name="selectall" id="selectall"
										onclick="select_allfields(\'vtst_vtlc_field_form\',\'widget\')" /></th>
									<th style="width: 200px;"><h5>Field Name</h5></th>
									<th style="width: 100px;"><h5>Show Field</h5></th>
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
									<input type="hidden" value="' . $field->fieldlabel . '"id="field_label' . $key . '">
									<input type="hidden" value="' . $typeofdata[1] . '" id="field_type' . $key . '">
									<input type="hidden" id="vts_label' . $key . '" name="vtst_vtlc_field_hidden' . $key . '"value="' . $field->fieldid . '" />';

			if ($typeofdata[1] == 'M') {
				$checked = '';
			} else {
				$checked = "";
			}

			if ($typeofdata[1] == 'M') {
				$widgetContent .= '<input type="hidden" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
										name="vtst_vtlc_field' . $key . '" />
									<input type="checkbox" value="' . $field->fieldname . '"' . $checked . '>';
			} else {
				$widgetContent .= '<input type="checkbox" value="' . $field->fieldname . '"id="vtst_vtlc_field' . $key . '"
									name="vtst_vtlc_field' . $key . '" ' . $checked . '>';
			}

			$widgetContent .= '</td>
								<td>' . $field->fieldlabel;
			if ($typeofdata[1] == 'M') {
				$widgetContent .= '<span style="color: #FF4B33">&nbsp;*</span>';
			}

			$widgetContent .= '</td>
								<td class="vtst-field-td-middleit">';

			if (in_array($field->fieldid, $config_widget_field['widgetfieldlist'])) {
				if ($typeofdata[1] == 'M') {
					$widgetContent .= '<img id="vtst-field-td-middleit" src="' . $imagepath . 'tick_strict.png" onclick="upgradetopro()" />';
				} else {
					$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="published(' . $key . ',&quot;0&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'tick.png"  /></a>';
				}
			} else {
				$widgetContent .= '<a id="publish3" class="smack_pointer" onclick="published(' . $key . ',&quot;1&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;onCreate&quot;);" name="publish3"><img  id="vtst-field-td-middleit" src="' . $imagepath . 'publish_x.png"  /></a>';
			}

			$widgetContent .= '</td>	<td class="vtst-field-td-middleit">';

			if ($inc == 1) {
				$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>';
			} elseif ($inc == $nooffields) {
				$widgetContent .= '<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
			} else {
				$widgetContent .= '<a class="vtst_pointer" id="down' . $key . '" onclick="move(' . $key . ',&quot;down&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;);">
									<img src="' . $imagepath . 'downarrow.png" /></a>
									<a class="vtst_pointer" id="up' . $key . '" onclick="move(' . $key . ',&quot;up&quot;,&quot;' . admin_url() . '&quot;,&quot;' . $modeled . '&quot;,&quot;' . $field->fieldid . '&quot;,&quot;' . $field->block . '&quot;)">
									<img src="' . $imagepath . 'uparrow.png" /></a>';
			}

			$widgetContent .= '</td>	<td class="vtst-field-td-middleit">
								<input type="checkbox" name="check' . $key . '" id="check' . $key . '"';

			if ($typeofdata[1] == 'M') {
				$widgetContent .= ' checked="" ';
			}

			$widgetContent .= '/></td>	<td class="vtst-field-td-middleit" id="field_label_display_td' . $key . '">
								<input type="text" id="field_label_display_textbox' . $key . '" class="readonly-text"
										value="' . $field->fieldlabel . '"  /></td>
								</tr>';
			$inc++;
		}

		$widgetContent .= '</td>
										</tr>
									</tbody>
								</table></div>


								</form>
							</div>';

		echo $widgetContent;
	} else {
		$widgetContent = "<div style='margin-top:20px;font-weight:bold;'>
					Please enter a valid database <a href=" . admin_url() . "admin.php?page=vtlc&action=plugin_settings>settings</a>
					</div>";
		echo $widgetContent;
	}

}
add_action('wp_ajax_getCrmFields', 'vtst_getCrmFields');
function vtst_recatuerget() {
	if ($_POST['captueval'] == 'checked') {
		if (function_exists('gglcptch_display')) {
			echo "Success";
		} else {
			echo "Please install and Activated Google Captcha (reCAPTCHA) Plugin";
		}
	}

}
add_action('wp_ajax_recatuerget', 'vtst_recatuerget');

function vtst_update_from_fields() {

	$config = get_option('Vts_vtpl_settings');
	$vtdb = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
	$fields = ($_POST['fields']);
	//print_r($fields);
	//$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O'");
	for ($i = 0; $i < sizeof($fields); $i++) {
		// sanitize the shortcode
		if (preg_match('/^[A-Za-z0-9_-]*$/', $fields[$i])) {
			if ($fields[$i] != 0) {
				//echo "UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='$fields[$i]'";
				//$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldid`='$fields[$i]'");
				//$query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='65'");
			}
		}
		// 		else
		// 		{
		// $query = $vtdb->query("UPDATE `vtiger_field` SET `typeofdata`='V~O' WHERE `fieldid`='$fields[$i]'");
		// 		}

	}
	//echo "from function";
}
add_action('wp_ajax_update_from_fields', 'vtst_update_from_fields');

function vtst_vtstiger_settings() {
	$AdminPages = new VTLCWPAdminPages();

	$action = vtst_getActionVtsTiger();
	?>
	<div id="main-page">

		<?php echo vtst_topnavmenu();?>
		<div>
			<?php $AdminPages->$action();?>
		</div>
	</div>
<?php
}
