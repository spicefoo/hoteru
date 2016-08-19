<?php
if ($_REQUEST['check'] = "checkVtigerWebservice") {

	global $plugin_dir_vtlc;
	chdir($plugin_dir_vtlc);
	include_once $plugin_dir_vtlc . "vtwsclib/Vtiger/WSClient.php";
	$url = $_REQUEST['url'];
	$username = $_REQUEST['Vts_host_username'];
	$accessKey = $_REQUEST['VTS_host_access_key'];
	$client = new Vtiger_WSClient($url);
	$login = $client->doLogin($username, $accessKey);
	if ($login) {
		echo "success";
	} else {

		echo "failure";
	}
}
?>
