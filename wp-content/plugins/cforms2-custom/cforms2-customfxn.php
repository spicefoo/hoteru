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

// ##
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
// ## @deprecated
// ##
require_once (plugin_dir_path ( __FILE__ ) . 'include/db_rooms.php');
require_once (plugin_dir_path ( __FILE__ ) . 'include/db_roomrates.php');
function my_cforms_logic($cformsdata, $oldvalue, $setting) {
	
	// ## If you're unsure how to reference $cformsdata use the below mail call to send you the data array
	// ## wp_mail('you@example.com', 'my_cforms_logic test', print_r($cformsdata,1), 'From: you@example.com');
	
	// ##
	// ## example: the below code modifies the REPLY-TO address (submitter)
	if ($setting == "ReplyTo" && $oldvalue != '') {
		
		// ## only form #2 should be affected (note: form #1 would be '' empty!!):
		if ($cformsdata ['id'] == '2' && $cformsdata ['data'] ['Your Name'] != '') {
			
			return '"' . $cformsdata ['data'] ['Your Name'] . '"' . ' <' . $oldvalue . '>'; // ## This requires the form to have field labeled "Your Name" !
		}
	}
	
	// ##
	// ## example: the below code changes the original Success Message
	
	if ($setting == "successMessage" && $oldvalue != '') {
		
		// ## only form #1 (default form) should be affected:
// 		if ($cformsdata ['id'] == '') {
			
// 			return $oldvalue . '<br />Form submitted on ' . date ( 'D, d M Y H:i:s' );
// 		}
		
		//if form has compu field, it means the form has data to be computed and a quotation to show
		if(form_is_compu($cformsdata)){
			return compute_quote($cformsdata);
		}
	}
	
	// ## example: the below code changes a user-variable in both the Text & HTML part of
	// ## the admin email & auto confirmation email
	
	if ($setting == "adminEmailTXT" || $setting == "adminEmailHTML" || $setting == "autoConfTXT" || $setting == "autoConfHTML" || $setting == "adminEmailDataTXT" || $setting == "adminEmailDataHTML") {
		
		// ## it's only changed though for form #2
		// ## and requires "{CustomSalutation}" to be in the message(s)
		if ($cformsdata ['id'] == 2) {
			
			// ## Returned message depends on user choosing the radio option "Mrs" or "Mr" (field value!)
			if ($cformsdata ['data'] ['Salutation'] == 'Mrs')
				return str_replace ( '{CustomSalutation}', 'Dear Mrs. ', $oldvalue );
			else
				return str_replace ( '{CustomSalutation}', 'Dear Mr. ', $oldvalue );
		}
	}
	
	// ## example: the below code replaces the custom var {DateFuture=Nd} in the subject
	// ## field of the admin email & auto confirmation email
	// ## Code Contribution by Regis Villemin
	
	if ($setting == "autoConfSUBJ" || $setting == "autoConfSUBJ") {
		$m = preg_replace_callback ( '/{DateFuture=([0-9]+)d}/i', create_function ( '$days', '

							$datefuture = strtotime ("+$days[1] days");
							
							return strtoupper( strftime( "%A %d %B %Y",  $datefuture ) );
							' ), $oldvalue );
		
		return $m;
	}
	
	// ## example: changes the next form to be form ID 5 (which is multi form page enabled)
	
	if ($setting == "nextForm") {
		
		// ## the below only triggers when the configured "next form" would have been 4
		// ## and the user did not check extended option checkbox
		if ($oldvalue == '4' && $cformsdata ['data'] ['extended options'] == 'on')
			return 5;
	}
	
	// ## example: changes the admin email address to "test123 <my@dif..." if placeholder 'placeholder' is found
	
	if ($setting == "adminTO") {
		
		if ($oldvalue == 'placeholder')
			return 'test123 <my@different-email.example>';
	}
	
	// ## example: changes the name of the uploaded file in the email (adding a prefix taken form a form field)
	
	if ($setting == "filename") {
		return $_POST ['filetype'] . $oldvalue;
	}
	
	// ## example: allows the final destination file path & name to be modified, return result = a full, absolute path
	// ## NOTE: changing the path or filename may cause the file links on the tracking page to not function anymore!
	
	if ($setting == "fileDestination") {
		
		$submissionID = $oldvalue ['subID']; // ## submission ID
		
		$newArray = array ();
		$newArray ['name'] = $submissionID . '-' . $oldvalue ['name']; // ## filename only
		$newArray ['path'] = rtrim ( $oldvalue ['path'], '/' ); // ## path (may or may not have trailing slash!)
		
		$newArray ['modified'] = true; // ## must set
		return $newArray; // ## TRIPPLE check that this array always! returns valid path + name info
	}
	
	// ## this allows to modify the file path shown on the tracking page and for downloads
	// ## you may only needs the below in case the final upload dir deviates from the form's configured one
	
	if ($setting == "fileDestinationTrackingPage") {
		
		$submissionID = $oldvalue ['subID']; // ## submission ID
		
		$newArray = array ();
		$newArray ['name'] = $submissionID . '-' . $oldvalue ['name']; // ## filename only
		$newArray ['path'] = rtrim ( $oldvalue ['path'], '/' ); // ## path (may or may not have trailing slash!)
		
		$newArray ['modified'] = true; // ## must set
		return $newArray; // ## TRIPPLE check that this array always! returns valid path + name info
	}
	
	// ## example: changes redirection address based on user input field
	
	if ($setting == "redirection") {
		
		// ## note: '$$$mypick' references the ID of the HTML element and has been assigned
		// ## to the drop down field in the form configuration, with [id:mypick] !
		
		$userfield = $cformsdata ['data'] [$cformsdata ['data'] ['$$$mypick']];
		
		if ($userfield == 'abc')
			return 'http://my.new.url.example';
		
		if ($userfield == 'def')
			return 'http://my.other.url.example';
	}
	
	return $oldvalue;
}

// ##
// ##
// ## Your custom user data input filter
// ## @deprecated
// ##
function my_cforms_filter($formIDOrPostData) {
	global $track;
	
	// ## $track stores all user input
	// ## Note: $formID = '' (empty) for the first form!
	
	// ## triggers on your third form
	if ($formIDOrPostData == '3') {
		
		// ## Do something with the data or not, up to you
		$track ['Your Name'] = 'Mr./Mrs. ' . $track ['Your Name'];
		
		// ## Send to 3d party or do something else
		wp_mail ( 'you@example.com', 'cforms my_filter test', print_r ( $track, 1 ), 'From: you@example.com' );
	}	

	// ## triggers on your third form before any cforms processing
	elseif (isset ( $formIDOrPostData ['sendbutton3'] )) {
		
		// ## do something with field name 'cf3_field_3'
		// ## (! check you HTML source to properly reference your form fields !)
		$formIDOrPostData ['cf3_field_3'] = 'Mr./Mrs. ' . $formIDOrPostData ['cf3_field_3'];
		
		// ## perhaps send an email or do something different
		wp_mail ( 'you@example.com', 'cforms my_filter_nonAjax test', 'Form data array (nonAjax):' . print_r ( $formIDOrPostData, 1 ), 'From: you@example.com' );
	}
}

// ##
// ##
// ## Your custom user data input filter
// ## @deprecated
// ##
function my_cforms_ajax_filter($formID) {
	
	// ## See my_cforms_filter
}

/**
 * Your custom user data action routine
 * gets triggered just before sending the admin email
 */

function form_is_compu($cformsdata){
	//check first if this form is for compu, if none, then there's no need to run further
	$formID = $cformsdata ['id'];
	$form = $cformsdata ['data'];
	
	return isset($form['compu']);
}

function add_tax($val, $percentage){
	return $val + ($val * $percentage);
}

/*
 * Computes the quotation of the reservation accdg to the submitted data and form values.
 *
 */
function compute_quote($cformsdata) {
	global $wpdb, $cformsSettings;
	$formID = $cformsdata ['id'];
	$form = $cformsdata ['data'];
	
	$db_rooms = new DB_Rooms ();
	$rooms = $db_rooms->getAll ();
	
	$db_roomrates = new DB_RoomRates ();
	$roomrates = $db_roomrates->getHighestRates ();
	
	$date_diff = date_diff ( date_create($form[$form ['$$$check_in']]), date_create($form[$form ['$$$check_out']] ));
	$total_days = $date_diff->format ( '%a' ); // days
	
	$room_data = array();
	$total_quote = 0;
	
	foreach ( $rooms as $r ) {
		if (isset ( $form ['$$$room-' . $r->id] ) && ($form [$form ['$$$room-' . $r->id]] > 0)) {
			$room_id = $r->id;
			$units = $form [$form ['$$$room-' . $r->id]];
			$total_price = $units * $roomrates[$r->id];
			
			
			//for the display
			$room_data['rooms'][] = array(
				'name' => $r->name,
				'units'=> $form [$form ['$$$room-' . $r->id]],
				'unit_price' => $roomrates[$r->id],
				'total_price' => $total_price,
			);
			
			$total_quote += $total_price;
		}
	}

	//computing total before tax
	$room_data['total_wo_days'] = $total_quote;
	$total_quote *= $total_days;
	
	//for the display
	$room_data['days'] = $total_days;
	$room_data['total_wo_tax'] = $total_quote;
	$room_data['tax_10'] = $total_quote * 0.10;
	$room_data['tax_12'] = $total_quote * 0.12;
	
	//adding tax
	$total_quote += $room_data['tax_10'] + $room_data['tax_12']; 
	
	//for the display
	$room_data['total'] = $total_quote;
	
	//setup display
	ob_start();
	require (plugin_dir_path ( __FILE__ ) . 'include/quote.php');
	return ob_get_clean();
}

function sendto_vtiger($cformsdata){
	global $cformsSettings;
	//check first if this form is for crm, if none, then there's no need to run further
	$formID = $cformsdata ['id'];
	$form = $cformsdata ['data'];
	
	if(!isset($form['crm']) || !isValidURL($cformsSettings['form'.$formID]['cforms'.$formID.'_action_page']) || !isValidPublicid($cformsSettings['form'.$formID]['cforms'.$formID.'_redirect_page'])) return;
	
	//prep data
	$form['quote'] = strip_tags(compute_quote($cformsdata));
	$post_data = format_postdata($form);
	
	// post url and vtiger webform publicid
	$post_url = $cformsSettings['form'.$formID]['cforms'.$formID.'_action_page'];
	$post_data['publicid'] = $cformsSettings['form'.$formID]['cforms'.$formID.'_redirect_page'];

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
	
	
	curl_close($curl);
	
	return json_decode($json_response, true);
}

# @TODO
function isValidURL($url){
	return true;
}

# @TODO
function isValidPublicid($id){
	return true;
}

function format_postdata($data){
	//sigh cform's form data format
	// to get the value of a specific field, we need to get the field name first using the pattern $$$<field_id>
	// then get the actual field value by using the field name
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
 * Add all the custom actions needed
 */
// add_action ( 'cforms2_after_processing_action', 'compute_quote' );
add_action ( 'cforms2_after_processing_action', 'sendto_vtiger' );
