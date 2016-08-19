<?php

/*********************************************************************************
 * WP Leads Builder For CRM is a tool to capture leads from WordPress to CRM.
 * plugin developed by Smackcoder. Copyright (C) 2016 Smackcoders.
 *
 * WP Leads Builder For CRM is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Leads 
 * Builder For CRM, WP Leads Builder For CRM DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * WP Leads Builder For CRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * WP Leads Builder For CRM copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2016. All rights reserved".
 ********************************************************************************/

if(!function_exists("Getaccess_token"))
{
function Getaccess_token( $config , $code ) {
	$token_url = "https://login.salesforce.com/services/oauth2/token";
	if (!isset($code) || $code == "") {
	    die("Error - code parameter missing from request!");
	}
	$params = "code=" . $code
	    . "&grant_type=authorization_code"
	    . "&client_id=" . $config['key']
	    . "&client_secret=" . $config['secret']
	    . "&redirect_uri=" . urlencode($config['callback']);
	$curl = curl_init($token_url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if ( $status != 200 ) {
	    die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}
	curl_close($curl);
	$response = json_decode($json_response, true);
	return $response;
}

function refresh_token( $config ) {

        $token_url = "https://login.salesforce.com/services/oauth2/token";
        $params = "grant_type=refresh_token"
            . "&client_id=" . $config['key']
            . "&client_secret=" . $config['secret']
            . "&refresh_token=" . $config['access_token'];
        $curl = curl_init($token_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) {
            die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        $response = json_decode($json_response, true);
        return $response;
}


function GetCrmModuleFields( $instance_url, $access_token , $module = "Lead" )
{
    $url = "$instance_url/services/data/v20.0/sobjects/{$module}/describe/";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token"));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $json_response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($json_response, true);
    return $response;
}

function Getuser( $instance_url, $access_token , $module = "Lead" )
{
    $url = "$instance_url/services/data/v20.0/sobjects/user/";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token"));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $json_response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($json_response, true);
    return $response;
}

function create_record( $data_array, $instance_url, $access_token , $module = "Lead" ) {

    $url = "$instance_url/services/data/v20.0/sobjects/{$module}/";
    $content = json_encode($data_array);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token",
                "Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ( $status != 201 ) {
        die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
    curl_close($curl);
    $response = json_decode($json_response, true);
return $response;
}
}

?>
