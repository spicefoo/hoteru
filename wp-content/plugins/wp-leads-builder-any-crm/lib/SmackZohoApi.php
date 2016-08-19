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

if( !class_exists( "SmackZohoApi" ) )
{
	class SmackZohoApi{
			
		public $zohocrmurl;
		public function __construct()
		{
			$this->zohocrmurl = "https://crm.zoho.com/crm/private/xml/";
		}

		public function APIMethod($modulename, $methodname, $authkey , $param="", $recordId = "")
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			return $result_array;
		}

		public function insertRecord( $modulename, $methodname, $authkey , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			if($extraParams != "")
			{
				$postContent .= $extraParams;
			}

			$postContent .= "&xmlData={$xmlData}";
			$postContent .= "&wfTrigger=true";
			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			return $result_array;
		}

		public function getRecords( $modulename, $methodname, $authkey , $selectColumns ="" , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken

			if($selectColumns == "")
			{
				$postContent .= "&selectColumns=All";
			}
			else
			{
				$postContent .= "&selectColumns={$modulename}( {$selectColumns} )";
			}

			if($extraParams != "")
			{
				$postContent .= $extraParams;
			}

			$postContent .= "&xmlData={$xmlData}";

			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			curl_close($ch);
			return $result_array;
		}

		public function getAuthenticationToken( $username , $password  )
		{
			$param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$username."&PASSWORD=".$password;
			$ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			/*This part of the code below will separate the Authtoken from the result. 
			Remove this part if you just need only the result*/
			$anArray = explode("\n",$result);
			$authToken = explode("=",$anArray['2']);
			$cmp = strcmp($authToken['0'],"AUTHTOKEN");
			if ($cmp == 0)
			{
				$return_array['authToken'] = $authToken['1'];
			}
			$return_result = explode("=" , $anArray['3'] );
			$cmp1 = strcmp($return_result['0'],"RESULT");
			if($cmp1 == 0)
			{
				$return_array['result'] = $return_result['1'];
			}
			if($return_result[1] == 'FALSE'){
				$return_cause = explode("=",$anArray[2]);
				$cmp2 = strcmp($return_cause[0],'CAUSE');
				if($cmp2 == 0)
					$return_array['cause'] = $return_cause[1];
			}
			curl_close($ch);
			return $return_array;
		}
	}
}
?>
