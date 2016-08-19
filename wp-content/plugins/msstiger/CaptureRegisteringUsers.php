<?php

function vtst_vts_tiger_capture_registering_users($user_id) {
	$siteurl = site_url();
	$config = get_option('Vts_vtpl_settings');
	if ($config['wp_tiger_vtst_user_capture'] == 'on') {
		$user_data = get_userdata($user_id);
		$user_email = $user_data->data->user_email;
		$user_lastname = get_user_meta($user_id, 'last_name', 'true');
		$user_firstname = get_user_meta($user_id, 'first_name', 'true');
		if (empty($user_lastname)) {
			$user_lastname = $user_data->data->display_name;
		}
		$post['firstname'] = $user_firstname;
		$post['lastname'] = $user_lastname;
		$post['email'] = $user_email;
		$post['moduleName'] = 'Contacts';
		if (!empty($config['appkey'])) {
			$post['appKey'] = $config['appkey'];
		}
		foreach ($post as $key => $value) {
			$fields_string .= $key . '=' . $value . '&';
		}
		rtrim($fields_string, '&');
		$url = trim($config['url'], "/") . '/modules/Webforms/post.php';

		$version_string = $config['version'];
		$version_array = explode('.', $version_string);
		$version = $version_array[0];
		$module = "Contacts";

		if ($version == 6) {
			global $plugin_dir_vts_tiger;
			chdir($plugin_dir_vts_tiger);
			include_once $plugin_dir_vts_tiger . "vtwsclib/Vtiger/WSClient.php";

			$url = $config['url'];
			$username = $config['Vts_host_username'];
			$accesskey = $config['VTS_host_access_key'];
			$client = new Vtiger_WSClient($url);
			$login = $client->doLogin($username, $accesskey);
			if (!$login) {
				return 'Login Failed';
			} else {
				$record = $client->doCreate($module, $post);
				if ($record) {
					$data = "/{$module} entry is added to vtiger CRM./";
				}
			}
		} else {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
		}

		if ($data) {
			if (preg_match("/$module entry is added to vtiger CRM./", $data)) {
				$content = "<span style='color:green'>Thank you for submitting</span>";
			} else {
				$content = "<span style='color:red'>Submitting Failed</span>";
			}
		}
	}
}