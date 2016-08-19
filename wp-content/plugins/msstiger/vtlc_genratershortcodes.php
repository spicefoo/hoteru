<?php

add_shortcode('display_contact_page', array('VTLCtWPTigerShortcodes', 'vtst_display_page'));

add_shortcode('display_widget_area', array('VTLCtWPTigerShortcodes', 'vtst_display_widget'));

add_shortcode('Vts-tiger-New-form name=', array('VTLCtWPTigerShortcodes', 'vtst_genrate_code'));
?>
<?php
class VTLCtWPTigerShortcodes {
	public static function vtst_display_page($atts) {

		$fields_string = "";
		$config = get_option("Vts_vtpl_settings");
		$config_field = get_option("Vts_vtpl_field_settings");
		$module = "Leads";
		$config_widget_field = get_option("Vts_vtlc_widget_field_settings");

		if (!empty($config['hostname']) && !empty($config['dbuser'])) {
			if (!empty($config_field['fieldlist']) && is_array($config_field['fieldlist'])) {
				$field_list = implode(',', $config_field['fieldlist']);
			}
			$dbvalues = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

			// $selectedFields = $dbvalues->get_results("SELECT fieldname, fieldlabel, typeofdata, uitype FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 AND fieldid in ({$field_list}) ORDER BY block, sequence");
			$selectedFields = $dbvalues->get_results("SELECT fieldname, fieldlabel, typeofdata, uitype FROM vtiger_field WHERE fieldid in ({$field_list}) ORDER BY block, sequence");
		}

		$action = trim($config['url'], "/") . '/modules/Webforms/post.php';
		// show frentend display contact shortcode
		$content = "<form id='contactform' name='contactform' method='post'>";
		$content .= "<table>";


		$version_string = $config['version'];
		$version_array = explode('.', $version_string);
		$version = $version_array[0];
		$action = trim($config['url'], "/") . '/modules/Webforms/post.php';

		if (isset($_REQUEST['page_contactform'])) {
			extract($_POST);

			foreach ($_POST as $field => $value) {
				if ($version == 6) {
					$post_fields[$field] = $value;
				} else {
					$post_fields[$field] = urlencode($value);
				}
			}

			if ($version < 6) {
				if (!empty($config['appkey'])) {
					$post_fields['appKey'] = $config['appkey'];
				}
			}
			foreach ($post_fields as $key => $value) {
				$fields_string .= $key . '=' . $value . '&';

			}
			rtrim($fields_string, '&');

			if ($version == 6) {
				global $plugin_dir_vtlc;
				chdir($plugin_dir_vtlc);
				include_once $plugin_dir_vtlc . "vtwsclib/Vtiger/WSClient.php";

				$url = $config['url'];
				$username = $config['Vts_host_username'];
				$accesskey = $config['VTS_host_access_key'];

				$client = new Vtiger_WSClient($url);
				$login = $client->doLogin($username, $accesskey);
				if (!$login) {
					return 'Login Failed';

				} else {
					$record = $client->doCreate($module, $post_fields);
					if ($record) {
						$data = "/{$module} entry is added to vtiger CRM./";
					}
				}
			} else {
				$url = $action;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
			}
			$successfulAtemptsOption = get_option("vts_tiger-contact-form-attempts");
			$total = $successfulAtemptsOption['total'];
			$success = $successfulAtemptsOption['success'];
			$failure = $total - $success;

			$existingCount = $wpdb->get_results("SELECT * FROM create_shortcode WHERE shortcode = '" . $atts[0] . "'");
			if (isset($existingCount) && isset($existingCount[0])) {
				$existingSubmit = $existingCount[0]->submit;
				$existingSuccess = $existingCount[0]->success;
				$existingSubmit++;
				$existingSuccess++;
				$newSubmit = $existingSubmit;
				$newSucces = $existingSuccess;
			}

			$newFailure = $newSubmit - $newSucces;

			$rows_affected = $wpdb->query($wpdb->prepare("UPDATE create_shortcode SET  success  = '" . $newSucces . "', submit = '" . $newSubmit . "', failure = '" . $newFailure . "' WHERE shortcode = '" . $atts[0] . "'"));

			// $exist = $wpdb->get_results("SELECT error_message , success_message FROM create_shortcode WHERE shortcode = '" . $atts[0] . "'");
			if ($data) {
				$total++;
				//$content.= $data;   //remove the comment to see the result from vtiger.
				if (preg_match("/$module entry is added to vtiger CRM./", $data)) {
					$success++;
					//	if (isset($exist)) {
					//		$error_message = $exist[0]->error_message;
					//		$success_message = $exist[0]->success_message;
					$content .= "<tr><td colspan='2' style='text-align:center;color:green;font-size: 1.2em;font-weight: bold;'>Thank you </td></tr>";
				} else {
					$content .= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Failed</td></tr>";
				}
			}
			$successfulAtemptsOption['total'] = $total;
			$successfulAtemptsOption['success'] = $success;
			update_option('vts_tiger-contact-form-attempts', $successfulAtemptsOption);

		}
		if (is_array($config_field['fieldlist'])) {
			foreach ($selectedFields as $field) {
				$content1 = "<p>";
				$content1 .= "<tr>";
				$content1 .= "<td>";
				$content1 .= "<label for='" . $field->fieldname . "'>" . $field->fieldlabel . "</label>";
				$typeofdata = explode('~', $field->typeofdata);
				if ($typeofdata[1] == 'M') {
					$content1 .= "<span  style='color:red;'>*</span>";
				}
				$content1 .= "</td><td>";
				$content1 .= "<input type='hidden' value='" . $typeofdata[1] . "' id='" . $field->fieldname . "_type'>";
				if ($typeofdata[0] == 'E') {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='email' size='30' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='email' size='30' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				} elseif ($field->uitype == 11) {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='30' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='30' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				} elseif ($field->uitype == 17) {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='30' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='30' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}

				} else {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='30' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='30' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				}
				$content1 .= "</td></tr>";

				$content .= $content1;
			}
		}
		$content .= "<tr><td></td><td>";
		$content .= "<p>";
		$content .= "<input type='submit' value='Submit' id='submit' name='submit'></p></td></tr></table>";
		$content .= "<input type='hidden' value='contactform' name='page_contactform'>";
		$content .= "<input type='hidden' value='Leads' name='moduleName' />
		</form>";
		return $content;
	}

	public static function vtst_display_widget($atts) {

		$fields_string = "";
		$config = get_option("Vts_vtpl_settings");
		$config_field = get_option("Vts_vtpl_field_settings");
		$module = "Leads";
		$config_widget_field = get_option("Vts_vtlc_widget_field_settings");
		if (!empty($config['hostname']) && !empty($config['dbuser'])) {
			if (!empty($config_widget_field['widgetfieldlist']) && is_array($config_widget_field['widgetfieldlist'])) {
				$field_list = implode(',', $config_widget_field['widgetfieldlist']);
			}
			$dbvalues = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);
			$selectedFields = $dbvalues->get_results("SELECT fieldname, fieldlabel, typeofdata, uitype FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 AND fieldid in ({$field_list}) ORDER BY block, sequence");

		}

		$version_string = $config['version'];
		$version_array = explode('.', $version_string);
		$version = $version_array[0];
		$action = trim($config['url'], "/") . '/modules/Webforms/post.php';
		// show  on widget franetend page  form
		$content = "<form id='contactform'  method='post'>";
		$content .= "<table>";
		if (isset($_REQUEST['widget_contactform'])) {
			extract($_POST);

			foreach ($_POST as $field => $value) {
				if ($version == 6) {
					$post_fields[$field] = $value;
				} else {
					$post_fields[$field] = urlencode($value);
				}
			}
			if ($version < 6) {
				if (!empty($config['appkey'])) {
					$post_fields['appKey'] = $config['appkey'];
				}
			}
			foreach ($post_fields as $key => $value) {
				$fields_string .= $key . '=' . $value . '&';
			}
			rtrim($fields_string, '&');
			if ($version == 6) {
				global $plugin_dir_vtlc;
				chdir($plugin_dir_vtlc);
				include_once $plugin_dir_vtlc . "vtwsclib/Vtiger/WSClient.php";

				$url = $config['url'];
				$username = $config['Vts_host_username'];
				$accesskey = $config['VTS_host_access_key'];
				$client = new Vtiger_WSClient($url);
				$login = $client->doLogin($username, $accesskey);
				if (!$login) {
					return 'Login Failed';
				} else {
					$record = $client->doCreate($module, $post_fields);
					if ($record) {
						$data = "/{$module} entry is added to vtiger CRM./";
					}
				}
			} else {
				$url = $action;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
			}
			$successfulAtemptsOption = get_option("vts-tiger-contact-widget-form-attempts");

			$total = $successfulAtemptsOption['total'];
			$success = $successfulAtemptsOption['success'];
			$failure = $total - $success;

			$existingCount = $wpdb->get_results("SELECT * FROM create_shortcode WHERE shortcode = '" . $atts[0] . "'");
			if (isset($existingCount) && isset($existingCount[0])) {
				$existingSubmit = $existingCount[0]->submit;
				$existingSuccess = $existingCount[0]->success;
				$existingSubmit++;
				$existingSuccess++;
				$newSubmit = $existingSubmit;
				$newSucces = $existingSuccess;
			}

			$newFailure = $newSubmit - $newSucces;

			$rows_affected = $wpdb->query($wpdb->prepare("UPDATE create_shortcode SET  success  = '" . $newSucces . "', submit = '" . $newSubmit . "', failure = '" . $newFailure . "' WHERE shortcode = '" . $atts[0] . "'"));

			$result = $wpdb->get_results("SELECT * FROM `create_shortcode` WHERE `shortcode`='$atts[0]'");

			foreach ($result as $key => $value) {
				$res = $value->success_message;
			}
			if ($data) {
				//$content.= $data;   //remove the comment to see the result from vtiger.
				$total++;
				if (preg_match("/$module entry is added to vtiger CRM./", $data)) {
					$success++;
					$content .= "<tr><td colspan='2' style='text-align:center;color:green;font-size: 1.2em;font-weight: bold;'>Thank you for submitting</td></tr>";
				} else {
					$content .= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Submitting Failed</td></tr>";
				}
			}
			$successfulAtemptsOption['total'] = $total;
			$successfulAtemptsOption['success'] = $success;
			update_option('vts-tiger-contact-widget-form-attempts', $successfulAtemptsOption);

		} // Fredrick Marks Code ends here
		if (is_array($config_widget_field['widgetfieldlist'])) {
			foreach ($selectedFields as $field) {

				$content1 = "<p>";
				$content1 .= "<tr>";
				$content1 .= "<td>";
				$content1 .= "<label for='" . $field->fieldname . "'>" . $field->fieldlabel . "</label>";
				$typeofdata = explode('~', $field->typeofdata);
				if ($typeofdata[1] == 'M') {
					$content1 .= "<span style='color:red;'>*</span>";
				}
				$content1 .= "</td><td>";
				$content1 .= "<input type='hidden' value='" . $typeofdata[1] . "' id='" . $field->fieldname . "_type'>";
				if ($typeofdata[0] == 'E') {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='email' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='email' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				} elseif ($field->uitype == 11) {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				} elseif ($field->uitype == 17) {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}

				} else {
					if ($typeofdata[1] == 'M') {
						$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					} else {
						$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
					}
				}

				//			$content1.="<input type='text' class='VTLC-tiger-widget-area-text' size='20' value='' name='".$field->fieldname."' id='".$field->fieldname."'></p>";
				$content1 .= "</td></tr>";
				$content .= $content1;
			}
		}
		$content .= "<tr><td></td><td>";
		$content .= "<p>";
		$content .= "<input type='submit' class='VTLC-tiger-widget-area-submit' value='Submit' id='submit' name='submit'></p></td></tr>";
		$content .= "</table>";
		$content .= "<input type='hidden' value='contactform' name='widget_contactform'>";
		$content .= "<input type='hidden' value='Leads' name='moduleName'/>
		</form>";
		return $content;
	}
	public static function vtst_genrate_code($atts) {

		$pattern = wp_parse_args($atts);
		$kay = $pattern['0'];
		$moudleIds = array("lead" => array("module" => "Leads", "id" => "7"), "widget" => array("module" => "Contacts", "id" => "4"));
		global $wpdb;
		$current_user = wp_get_current_user();
		$newuser = $current_user->user_login;
		$myrows = $wpdb->get_results("SELECT * FROM `create_shortcode` WHERE `assign`='$newuser' and`shortcode`='$kay'");

		$newlabel = array();
		foreach ($myrows as $shortcut) {
			array_push($newlabel, $shortcut->assign);
			$resut = json_decode("$shortcut->data", true);

		}

		$resultLead = $resut['lead'];

		$config_widget_field['widgetfieldlist'] = array();
		$fieldArr = array();
		for ($i = 0; $i <= count($resut); $i++) {

			if (isset($resut["vtst_vtlc_field$i"])) {
				array_push($fieldArr, $resut["vtst_vtlc_field_hidden$i"]);
			}
		}
		$config_widget_field['widgetfieldlist'] = $fieldArr;
		if (in_array($newuser, $newlabel)) {
			$fields_string = "";
			$config = get_option("Vts_vtpl_settings");
			$config_field = get_option("Vts_vtpl_field_settings");
			//$module = "Leads";
			$module = $moudleIds[$resultLead]['module'];

			if (!empty($config['hostname']) && !empty($config['dbuser'])) {

				if (!empty($config_widget_field['widgetfieldlist']) && is_array($config_widget_field['widgetfieldlist'])) {
					$field_list = implode(',', $config_widget_field['widgetfieldlist']);
				}

				$moduleId = $moudleIds[$resultLead]['id'];
				$dbvalues = new wpdb($config['dbuser'], $config['dbpass'], $config['dbname'], $config['hostname']);

				$selectedFields = $dbvalues->get_results("SELECT fieldname, fieldlabel, typeofdata, uitype, fieldid FROM vtiger_field WHERE tabid = $moduleId AND tablename != 'vtiger_crmentity' AND uitype != 4 AND fieldid in ({$field_list}) ORDER BY block, sequence");

			}

			$version_string = $config['version'];
			$version_array = explode('.', $version_string);
			$version = $version_array[0];
			$action = trim($config['url'], "/") . '/modules/Webforms/post.php';
			// show  on widget franetend page  form
			$content = "<form id='contactform'  method='post' action= ''>";
			$content .= "<table>";

			if (isset($_REQUEST['widget_contactform'])) {
				extract($_POST);

				foreach ($_POST as $field => $value) {
					if ($version == 6) {
						$post_fields[$field] = $value;
					} else {
						$post_fields[$field] = urlencode($value);
					}
				}
				if ($version < 6) {
					if (!empty($config['appkey'])) {
						$post_fields['appKey'] = $config['appkey'];
					}
				}
				foreach ($post_fields as $key => $value) {
					$fields_string .= $key . '=' . $value . '&';
				}
				rtrim($fields_string, '&');
				if ($version == 6) {
					global $plugin_dir_vtlc;
					chdir($plugin_dir_vtlc);
					//echo $plugin_dir_vtlc . "vtwsclib/Vtiger/WSClient.php";
					require_once $plugin_dir_vtlc . "vtwsclib/Vtiger/WSClient.php";

					$url = $config['url'];
					$username = $config['Vts_host_username'];
					$accesskey = $config['VTS_host_access_key'];
					$client = new Vtiger_WSClient($url);

					$login = $client->doLogin($username, $accesskey);
					//print_r($login);die();
					if (!$login) {
						return 'Login Failed';
					} else {
						$record = $client->doCreate($module, $post_fields);
						// echo "<pre>";
						// print_r($record);
						// die();
						// print_r($module);die();
						// die();

						if ($record) {
							$data = "/{$module} entry is added to vtiger CRM./";
						}
					}
				} else {

					$url = $action;
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec($ch);
					curl_close($ch);
				}
				$successfulAtemptsOption = get_option("vts-tiger-contact-widget-form-attempts");

				$total = $successfulAtemptsOption['total'];
				$success = $successfulAtemptsOption['success'];
				$failure = $total - $success;

				$existingCount = $wpdb->get_results("SELECT * FROM create_shortcode WHERE shortcode = '" . $atts[0] . "'");
				if (isset($existingCount) && isset($existingCount[0])) {
					$existingSubmit = $existingCount[0]->submit;
					$existingSuccess = $existingCount[0]->success;
					$existingSubmit++;
					$existingSuccess++;
					$newSubmit = $existingSubmit;
					$newSucces = $existingSuccess;
					$error_message = $existingCount[0]->error_message;
					$success_message = $existingCount[0]->success_message;
					$url_redirection = $existingCount[0]->url_redirection;

				}

				$newFailure = $newSubmit - $newSucces;

				$rows_affected = $wpdb->query("UPDATE create_shortcode SET  success  = '" . $newSucces . "', submit = '" . $newSubmit . "', failure = '" . $newFailure . "' WHERE shortcode = '" . $atts[0] . "'");
				if ($data) {

					//$content .= $data; //remove the comment to see the result from vtiger.
					$total++;
					if (preg_match("/$module entry is added to vtiger CRM./", $data)) {
						$success++;
						$content .= "<tr><td colspan='2' style='text-align:center;color:green;font-size: 1.2em;font-weight: bold;'>" . $success_message . "</td></tr>";

						if (isset($url_redirection) && !empty($url_redirection)) {

							/*echo '<script>
							window.location.href ="' . site_url() . $url_redirection . '";</script>';
						*/}
					} else {

						$content .= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>" . $error_message . "</td></tr>";
						?>
						<script>
						window.location.href ="http://localhost/wordpress/<?php echo $url_redirection?>";
						<?php
}
				}
				$successfulAtemptsOption['total'] = $total;
				$successfulAtemptsOption['success'] = $success;
				update_option('vts-tiger-contact-widget-form-attempts', $successfulAtemptsOption);

			} // Fredrick Marks Code ends here
			if (is_array($config_widget_field['widgetfieldlist'])) {
				foreach ($selectedFields as $field) {

					$content1 = "<p>";
					$content1 .= "<tr>";
					$content1 .= "<td>";
					$content1 .= "<label for='" . $field->fieldname . "'>" . $field->fieldlabel . "</label>";
					$typeofdata = explode('~', $field->typeofdata);
					if ($typeofdata[1] == 'M') {
						$content1 .= "<span style='color:red;'>*</span>";
					}
					$content1 .= "</td><td>";
					$content1 .= "<input type='hidden' value='" . $typeofdata[1] . "' id='" . $field->fieldname . "_type'>";
					if ($typeofdata[0] == 'E') {
						if ($typeofdata[1] == 'M') {
							$content1 .= "<input type='email' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						} else {
							$content1 .= "<input type='email' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						}
					} elseif ($field->uitype == 11) {
						if ($typeofdata[1] == 'M') {
							$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						} else {
							$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						}
					} elseif ($field->uitype == 17) {
						if ($typeofdata[1] == 'M') {
							$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						} else {
							$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						}

					} else {
						if ($typeofdata[1] == 'M') {
							$content1 .= "<input type='text' size='20' value='' required name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						} else {
							$content1 .= "<input type='text' size='20' value='' name='" . $field->fieldname . "' id='" . $field->fieldname . "'></p>";
						}
					}

					//			$content1.="<input type='text' class='VTLC-tiger-widget-area-text' size='20' value='' name='".$field->fieldname."' id='".$field->fieldname."'></p>";
					$content1 .= "</td></tr>";
					$content .= $content1;
				}
			}
			$content .= "<tr><td></td><td>";
			$content .= "<p>";

			if ($config['wp_tiger_vtst_user_capture'] == 'on') {
				if (function_exists('gglcptch_display')) {
					$content .= gglcptch_display();
				}

			}
			$exist = $wpdb->get_results("SELECT `captcha_visible` ,`public_key`  FROM wp_captcha ");

			if (isset($exist) && isset($exist[0])) {
				if ($myrows[0]->captcha == 'yes' && $exist[0]->captcha_visible == 'yes') {
					$content .= '<div class="g-recaptcha" style="width:50%" data-sitekey="' . $exist[0]->public_key . '"></div>';
				}
			}
			$content .= "<input type='submit' class='VTLC-tiger-widget-area-submit' value='Submit' id='submit' name='submit'></p></td></tr>";
			$content .= "</table>";
			$content .= "<input type='hidden' value='contactform' name='widget_contactform'>";
			$content .= "<input type='hidden' value='$module' name='moduleName'/>
		</form>";

			return $content;
		} else {
			echo "You don't have access";
		}
	}
}
