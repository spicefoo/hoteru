<?php
/*
 * Plugin Name: cforms2-customfxn
 * Description: cforms2 Custom Functions
 * Find below examples for your custom routines. Do not change the function names.
 * The old way of extending cformsII is deprecated.
 * The preferred way of doing it is via WordPress actions and filters.
 * You find their 'cforms2_' prefixed names at the apply_filters and do_action calls.
 * For some of these new actions and filters examples are given below.
 */

// ## TO USE THE FUNCTIONS:
// ## 1) copy this file to your plugin directory
// ## 2) rename it ending with extension *.php
// ## 3) remove the functions you do not need and customize the needed ones
// ## 4) activate this plugin

/*
// ## Your custom application logic features
// ##
// ## "successMessage" $cformsdata = cforms datablock
// ## "redirection" $cformsdata = cforms datablock
// ## "filename" $cformsdata = $_REQUEST
// ## "fileDestination" $cformsdata = $oldvalue = array!
// ## "fileDestinationTrackingPage" $cformsdata = all SQL data, $oldvalue = array!
// ## "adminTO" $cformsdata = cforms datablock
// ## "nextForm" $cformsdata = cforms datablock
// ##
// ## "adminEmailTXT" $cformsdata = cforms datablock
// ## "adminEmailHTML" $cformsdata = cforms datablock
// ## "autoConfTXT" $cformsdata = cforms datablock
// ## "autoConfHTML" $cformsdata = cforms datablock
// ## "adminEmailSUBJ" $cformsdata = cforms datablock
// ## "autoConfSUBJ" $cformsdata = cforms datablock
// ##
// ## "textonly" $cformsdata = html code of the form (author: angge)
 * 		When field is a textonly field, added a functionality where you can 
 * 		replace it with whatever html code you want
// ## @deprecated
// ##
 */
require_once (plugin_dir_path ( __FILE__ ) . 'include/db_rooms.php');
require_once (plugin_dir_path ( __FILE__ ) . 'include/db_roomrates.php');
require_once (plugin_dir_path ( __FILE__ ) . 'include/db_options.php');
require_once (plugin_dir_path(__FILE__) . 'include/lib_custommsgs.php');

function my_cforms_logic($cformsdata, $oldvalue, $setting) {
	if (  $setting == "autoConfTXT" || $setting == "autoConfHTML" ){
		
		//replace {quote} placeholder with the actual quotations if the quote_email flag is set in the form fields
		if(isset($cformsdata['data']['cf_form'.$cformsdata['id'].'_quote_email'])){
			if(!getFromSession($cformsdata['id'], 'quote')) return $oldvalue;
			
			$quote = getFromSession($cformsdata['id'], 'quote');
			
			//unsetCustomSession(); //cleanup session
			if($setting == "autoConfHTML"){
				$oldvalue = str_replace('{quote}', $quote, $oldvalue);
			}else{
				$oldvalue = str_replace('{quote}', strip_tags($quote), $oldvalue);
			}
		}
		
		//replace room_request placeholder
		$oldvalue = str_replace('{room_request}', 'Room request here', $oldvalue);
		
		//replace confirm_request_url placeholder
		$oldvalue = str_replace('{confirm_request_url}', 'URL here', $oldvalue);
		
		//send multiple emails through autoConf 
		// NOTE: make sure that when this runs, there are no other special {} pairs left to be processed since it uses {} in regex. 
		$oldvalue = processAddtnlEmails($oldvalue);
		
		return $oldvalue;
		
	}
	
	if( $setting == 'textonly' && $oldvalue != '' ){
		//if form has compu field, it means the form has data to be computed and a quotation to show
		if($oldvalue == 'quote'){
			
			if(getFromSession($cformsdata['id'], 'quote')) return getFromSession($cformsdata['id'], 'quote');
			
			if(form_is_compu($cformsdata['data'])){
				$quote = compute_quote($cformsdata['data']);
				addToSession($cformsdata['id'], 'quote', $quote);
				return $quote;
			}
		}
	}
	
	return $oldvalue;
}


/**
 * Session functions for implementing custom session vars
 * addToSession, getFromSession, unsetCustomSession
 */
function addToSession($formid, $key, $val){
	if(session_id()){ //wp session
		$_SESSION['cforms-custom'][$formid][$key] = $val;
	}
}

function getFromSession($formid, $key){
	if(session_id()){ //wp session
		return $_SESSION['cforms-custom'][$formid][$key];
	}
	return NULL;
}

function unsetCustomSession(){
	unset($_SESSION['cforms-custom']);
}


/**
 * Computes the quotation of the reservation accdg to the submitted data and form values.
 * @param array $cformsdata
 */
function compute_quote($cformsdata) {
	global $wpdb, $cformsSettings, $hotel_options;
	$formID = $cformsdata ['id'];
	$form = $cformsdata ['data'];
	
	$db_rooms = new DB_Rooms ();
	$rooms = $db_rooms->getAll ();
	
	$db_roomrates = new DB_RoomRates ();
	$roomrates = $db_roomrates->getHighestRates ();
	
	$date_diff = date_diff ( date_create($form[$form ['$$$check_in']]), date_create($form[$form ['$$$check_out']] ));
	$total_days = $date_diff->format ( '%a' ); // days
	
	$room_data = array(); //for the display
	$total_quote = 0;
	
	foreach ( $rooms as $r ) {
		if (isset ( $form ['$$$room-' . $r->id] ) && ($form [$form ['$$$room-' . $r->id]] > 0)) {
			$room_id = $r->id;
			$units = $form [$form ['$$$room-' . $r->id]];
			$total_price = $units * $roomrates[$r->id];
			
			//for the display
			$room_data['rooms'][$r->id] = array(
				'name' => $r->name,
				'units'=> $form [$form ['$$$room-' . $r->id]],
				'unit_price' => $roomrates[$r->id],
				'total_price' => $total_price,
			);
			
			$total_quote += $total_price;
		}
	}
	
	//extra charges
	$extra_peeps = getExtraPeeps($cformsdata['data']);
	$extra_charge = $extra_peeps * $hotel_options[$form['h_id']]['extra_charge'];
	$total_quote += $extra_charge;

	//computing total before tax
	$room_data['total_wo_days'] = $total_quote;
	$total_quote *= $total_days;
	
	//for the display
	$room_data['days'] = $total_days;
	$room_data['total_wo_tax'] = $total_quote;
	$room_data['tax_10'] = $total_quote * 0.10;
	$room_data['tax_12'] = $total_quote * 0.12;
	$room_data['extra_peeps'] = $extra_peeps;
	$room_data['extra_charge_rate'] = $hotel_options[$form['h_id']]['extra_charge'];
	$room_data['tot_extra_charge'] = $extra_charge;
	
	//adding tax
	$total_quote += $room_data['tax_10'] + $room_data['tax_12']; 
	
	//for the display
	$room_data['total'] = $total_quote;
	
	//setup display
	ob_start();
	require (plugin_dir_path ( __FILE__ ) . 'include/quote.php');
	return ob_get_clean();
}

function form_is_compu($cformsdata){
	//check first if this form is for compu, if none, then there's no need to run further
	return isset($cformsdata['data']['compu']);
}

/**
 * Sends form data to vtiger
 * For this to be triggered, the "crm" field must be included in the form.
 * @param array $cformsdata
 */
function sendto_vtiger($cformsdata){
	global $cformsSettings;
	//check first if this form is for crm, if none, then there's no need to run further
	$formID = $cformsdata ['id'];
	$form = $cformsdata ['data'];
	
	if(!isset($form['crm'])) return;
	
	//prep data
	$form['quote'] = strip_tags(compute_quote($cformsdata));
	$post_data = format_postdata($form);
	
	//separate the post url and publicid of vtiger
	$matches = NULL;
	$action_page = $cformsSettings['form'.$formID]['cforms'.$formID.'_action_page'];
	if(preg_match('/^(.*)\?publicid=(.{32})/', $action_page, $matches) && isset($matches[1])){
		$post_url = $matches[1];
		$post_data['publicid'] = $matches[2];
	}else{
		die("Public id cannot be seen.");
	}
	
	return sendto_curl($post_url, $post_data);
}

function sendto_curl($url, $data){
	$content = http_build_query($data);
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
	array("Content-type: application/x-www-form-urlencoded"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	
	$json_response = curl_exec($curl);
	
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	if ( $status != 200 ) {
		
		die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}
	
	$response = json_decode($json_response);
	if(!$response->success){
		die("Error: " . $response->error->message);
	}
	
	curl_close($curl);
	
	return json_decode($json_response, true);
}

/**
 * sigh cform's form data format
 * to get the value of a specific field, we need to get the field name first using the pattern $$$<field_id>
 * then get the actual field value by using the field name
 * @param array $data
 * @return multitype:array
 */
function format_postdata($data){
	$post_data = array();
	foreach($data as $k => $v){
		$matches = NULL;
		if(preg_match('/^\$\$\$(.*)/', $k, $matches) && isset($matches[1])){ //meaning that it's the field name
			$post_data[$matches[1]] = $data[$v];
		}
	}
	
	return $post_data;
}

/**
 * Custom validation functions for "compu" forms.
 * @param array $postdata
 * @return void|array
 */
function my_cforms_validations($postdata){
	global $cformsSettings, $err_msgs;
	
	if(form_is_compu($postdata)){
		$data['err'] = 0;
		$data['err_txt'] = '';
		
		if(!validDateInterval($postdata)){
			$data['err'] = 1;
			$data['err_txt'] .= '<li>'.$err_msgs['cforms_invalidinterval'].'</li>';
		}
		
		if(!guestsFit($postdata)){
			$data['err'] = 1;
			$data['err_txt'] .= '<li>'.$err_msgs['cforms_invalidnumguest'].'</li>';
		}
		
		return $data;
	}else{
		return;
	}
}

function validDateInterval($form){
	$check_in = date_create($form['check_in']);
	$check_out = date_create($form['check_out']);
	$today = date_create(date("m/d/Y"));
	
	//check if valid date format
	if(!$check_in || !$check_out) return false;
	
	//check if check in date has passed
	$from_today = date_diff ($check_in, $today);
	if (empty($from_today->format ( '%r' ))) return false;
	
	$date_diff = date_diff ($check_in, $check_out);
	$total_days = (int) $date_diff->format ( '%a' );
	if($total_days <= 0) return false;
	
	$total_days = $date_diff->format ( '%r' );
	return empty($total_days);
}

function getMaxCapacity($data){
	global $hotel_options;
	
	$db_rooms = new DB_Rooms ();
	$rooms = $db_rooms->getAll ();
	
	$db_roomrates = new DB_RoomRates ();
	$roomrates = $db_roomrates->getRatesArray();
	
	$max = 0;
	$default_cap = $hotel_options[$data['h_id']]['default_capacity'];
	
	foreach ( $rooms as $r ) {
		if (isset ( $data ['room-' . $r->id] ) && ($data ['room-' . $r->id] > 0)) {
			//echo "room: " . $r->name . " cap: " . $default_cap . " extra: " . $roomrates[$r->id][$default_cap]['allow_extra'] . " x units: " . $data ['room-' . $r->id] . "<br/>";
			$max += $data ['room-' . $r->id] * ($default_cap + $roomrates[$r->id][$default_cap]['allow_extra']);
		}
	}
	
	return $max;
}

function getTotalMinCapacity($form){
	global $hotel_options;
	
	$db_rooms = new DB_Rooms ();
	$rooms = $db_rooms->getAll ();
	
	$db_roomrates = new DB_RoomRates ();
	$roomrates = $db_roomrates->getRatesArray();
	
	$min = 0;
	$default_cap = $hotel_options[$form['h_id']]['default_capacity'];
	
	foreach ( $rooms as $r ) {
		if (isset ( $form ['$$$room-' . $r->id] ) && ($form [$form ['$$$room-' . $r->id]] > 0)) {
			$min += ($form [$form ['$$$room-' . $r->id]]  * ($default_cap));
		}
	}
	
	return $min;
}

function guestsFit($data){
	$n = $data['num_guests'];
	$max = getMaxCapacity($data);

	return $max >= $n;
}

function getExtraPeeps($form){
	$n = $form [ $form ['$$$num_guests']];
	$min = getTotalMinCapacity($form);
	return (int) $n - $min;
}

/**
 * Processing Additional Emails when the autoConf is enabled.
 * @param string $input_lines
 */
function processAddtnlEmails($input_lines){
	$pattern = "/{{([^}]*)}}/";
	if(preg_match_all($pattern, $input_lines, $matches)){
		foreach($matches[1] as $e){
			$e = trim($e);
				
			preg_match("/>>from:((.| )*+)/i", $e, $from);
			$from = isset($from[1]) ? 'From: '.cleanString($from[1]) : '';
				
			preg_match("/>>to:((.| )*+)/i", $e, $to);
			$to = isset($to[1]) ? cleanString($to[1]) : '';
				
			preg_match("/>>subj:((.| )*+)/i", $e, $subj);
			$subj = isset($subj[1]) ? cleanString($subj[1]) : '';
				
			//echo $e;
			preg_match_all("/>>msg:((.|\s)*+)/i", $e, $msg);
			$msg = isset($msg[1][0]) ? $msg[1][0] : '';
				
			$mail_stat = wp_mail($to, $subj, $msg, $from);
			
			if(!$mail_stat) die("Error with sending additional emails");
		}
	}

	return trim(preg_replace($pattern, "", $input_lines));
}

function cleanString($string){
	return stripslashes(trim($string));
}


/**
 * Add all the custom actions needed
 */
// add_action ( 'cforms2_after_processing_action', 'compute_quote' );
add_action ( 'cforms2_after_processing_action', 'sendto_vtiger' );
