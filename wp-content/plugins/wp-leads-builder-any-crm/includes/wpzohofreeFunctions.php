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

include_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'lib/SmackZohoApi.php');
class Functions{
	public $username;
	public $accesskey;
	public $authtoken;
	public $url;
	public $result_emails;
	public $result_ids;

	public function __construct()
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		$this->username = $SettingsConfig['username'];
		$this->accesskey = $SettingsConfig['password'];
		$this->url = "";//$SettingsConfig['url'];
		$this->authtoken = $SettingsConfig['authtoken'];
	}

	public function login()
	{
		$client = new SmackZohoApi();
		return $client;
	}

	public function getAuthenticationKey( $username , $password )
	{
		$client = $this->login();
		$return_array = $client->getAuthenticationToken( $username , $password  );
		return $return_array;
	}

	public function getCrmFields( $module )
	{
                $client = $this->login();
		$recordInfo = $client->APIMethod( $module , "getFields" , $this->authtoken );
		$config_fields = array();
		$AcceptedFields = Array( 'TextArea' => 'text' , 'Text' => 'string' , 'Email' => 'email' , 'Boolean' => 'boolean', 'Pick List' => 'picklist' , 'varchar' => 'string' , 'Website' => 'url' , 'Phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'Currency' => 'currency' , 'DateTime' => 'date' , 'datetime' => 'date' , 'Integer' => 'string' );

		$j = 0;

		foreach($recordInfo['section'] as $section ){
			if(!empty($section['FL']))
			foreach($section['FL'] as $key => $fields )
			{
				if( ($key === '@attributes') )
				{
					if( $fields['req'] == 'true' )
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
						$config_fields['fields'][$j]['mandatory'] = 2;
					}
					else
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 0;
					}
                                        if(($fields['type'] == 'Pick List') || ($fields['type'] == 'Multi Pick List') || ($fields['type'] == 'Radio')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($fields['val'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option ;
                                                        $picklistValues[$optionindex]['value'] = $option;
                                                        $optionindex++;
                                                }
//                                              $recordInfo['module_fields'][$i]['type']['picklistValues'] = 
                                                $config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['type']] , 'picklistValues' => $picklistValues );
                                        }
					else
					{
						$config_fields['fields'][$j]['type'] = array("name" => $AcceptedFields[$fields['type']]);
					}

					$config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['dv']);
					$config_fields['fields'][$j]['fieldname'] = $fields['dv'];
					$config_fields['fields'][$j]['label'] = $fields['label'];
					$config_fields['fields'][$j]['display_label'] = $fields['label'];
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['order'] = $j;
					$j++;
				}
				elseif( $fields['@attributes']['isreadonly'] == 'false' && ( $fields['@attributes']['type'] != 'Lookup' ) && ( $fields['@attributes']['type'] != 'OwnerLookup' ) && ( $fields['@attributes']['type'] != 'Lookup' ) )
				{
					if( $fields['@attributes']['req'] == 'true' )
					{
                                                $config_fields['fields'][$j]['mandatory'] = 2;
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
					}
					else
					{
                                                $config_fields['fields'][$j]['wp_mandatory'] = 0;
					}

                                        if(($fields['@attributes']['type'] == 'Pick List') || ($fields['@attributes']['type'] == 'Multi Pick List') || ($fields['@attributes']['type'] == 'Radio')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($fields['val'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option;
                                                        $picklistValues[$optionindex]['value'] = $option;
                                                        $optionindex++;
                                                }
//                                              $recordInfo['module_fields'][$i]['type']['picklistValues'] = 
                                                $config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['@attributes']['type']] , 'picklistValues' => $picklistValues );
                                        }
                                        else
                                        {
                                                $config_fields['fields'][$j]['type'] = array( 'name' => $AcceptedFields[$fields['@attributes']['type']] );
                                        }

                                        $config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['@attributes']['dv']);
                                        $config_fields['fields'][$j]['fieldname'] = $fields['@attributes']['dv'];
					$config_fields['fields'][$j]['label'] = $fields['@attributes']['label'];
                                        $config_fields['fields'][$j]['display_label'] = $fields['@attributes']['label'];
                                        $config_fields['fields'][$j]['publish'] = 1;
                                        $config_fields['fields'][$j]['order'] = $j;
					$j++;
				}
			}
		}
		$config_fields['check_duplicate'] = 0;
                $config_fields['isWidget'] = 0;
                $users_list = $this->getUsersList();
                $config_fields['assignedto'] = $users_list['id'][0];
                $config_fields['module'] = $module;
		return $config_fields;
	}

	public function getUsersList()
	{
                $client = $this->login();
		$extraparams = "&type=ActiveUsers";
		$records = $client->getRecords( "Users" , "getUsers" , $this->authtoken , "" , "" , $extraparams );
                if( isset( $records['user']['@attributes'] ) ) {
			{
                                $user_details['user_name'][] = $records['user']['@attributes']['email'];
                                $user_details['id'][] = $records['user']['@attributes']['id'];
                                $user_details['first_name'][] = $records['user']['@attributes']['email'];
                                $user_details['last_name'][] = ""; 
                        }
                }
		else
		{
                        foreach($records['user'] as $record) {
                                $user_details['user_name'][] = $record['@attributes']['email'];
                                $user_details['id'][] = $record['@attributes']['id'];
                                $user_details['first_name'][] = $record['@attributes']['email']; 
                                $user_details['last_name'][] = ""; 
                        }
		}

                return $user_details;
	}

	
	public function getUsersListHtml( )
	{
		$HelperObj = new WPCapture_includes_helper;
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$option = "smack_{$activatedplugin}_{$moduleslug}_fields-tmp";
		$config_fields = get_option($option);
		$users_list = $this->getUsersList();
		$html = "";
		$html = '<select name="assignedto" id="assignedto" onchange="saveAssignedTo(\''.site_url().'\',\''.$module.'\',\''.$option.'\',\'onCreate\');">';
                $content_option = "";
                if(isset($users_list['user_name']))
                for($i = 0; $i < count($users_list['user_name']) ; $i++)
                {
			$content_option.="<option id='{$users_list['user_name'][$i]}' value='{$users_list['user_name'][$i]}'";
			if($users_list['user_name'][$i] == $config_fields["assignedto"])
			{
				$content_option.=" selected";
			}
			$content_option.=">{$users_list['user_name'][$i]}</option>";
		}
		$html .= $content_option;
		$html .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		return $html;
	}
	
	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['First_Name'] = $user_firstname;
		$post['Last_Name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}


        public function assignedToFieldId()
        {
                return "Lead_Owner";
        }

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		$client = $this->login();
		$post_fields['First Name'] = $module_fields['First_Name'];
		$post_fields['Last Name'] = $module_fields['Last_Name'];
		$post_fields[$this->duplicateCheckEmailField()] = $module_fields[$this->duplicateCheckEmailField()];
                $postfields = "<{$module}>\n<row no=\"1\">\n";
                if(isset($post_fields))
                {
                        foreach($post_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                else
                {
                        foreach($module_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                $postfields .= "</row>\n</$module>";
                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields );
		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" ) )
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}

	public function createRecord( $module , $module_fields )
	{
		$client = $this->login();
		$module = "Leads";
		global $HelperObj;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$moduleslug = $this->ModuleSlug = rtrim( strtolower($module) , "s");
		$config_fields = get_option("smack_{$activateplugin}_{$moduleslug}_fields-tmp");
		$underscored_field = "";
		foreach($config_fields['fields'] as $key => $fields)  //      To add _ for field with spaces to capture the REQUEST
		{
			if( count($exploded_fields = explode(' ', $fields['fieldname'] )) > 1 )
			{
				foreach( $exploded_fields as $exploded_field )
				{
					$underscored_field .= $exploded_field."_";
				}
				$underscored_field = rtrim($underscored_field, "_");
			}
			else
			{
				$underscored_field = $fields['fieldname'];
			}
			$config_underscored_fields[$underscored_field] = $fields['fieldname'];
			$underscored_field = "";
		}

		if($module_fields['Email_Opt_Out']=='on') 
		{ 
			$module_fields['Email_Opt_Out']='true'; 
		}
 
		foreach($module_fields as $field => $value)
		{
			if( array_key_exists($field , $config_underscored_fields) )
			{
				$post_fields[$config_underscored_fields[$field]]=$value;//urlencode($value);
			}
		}

                $postfields = "<{$module}>\n<row no=\"1\">\n";
                if(isset($post_fields))
                {
                        foreach($post_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                else
                {
                        foreach($module_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                $postfields .= "</row>\n</$module>";
                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields );
		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" ) )
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}
	
	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$client = $this->login();
		global $HelperObj;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$moduleslug = $this->ModuleSlug = rtrim( strtolower($module) , "s");
		$config_fields = get_option("smack_{$activateplugin}_{$moduleslug}_fields-tmp");
		foreach($config_fields['fields'] as $key => $fields)
		{
			if( count($exploded_fields = explode(' ', $fields['fieldname'] )) > 1 )
			{
				foreach( $exploded_fields as $exploded_field )
				{
					$underscored_field .= $exploded_field."_";
				}
				$underscored_field = rtrim($underscored_field, "_");
			}
			else
			{
				$underscored_field = $fields['fieldname'];
			}
			$config_underscored_fields[$underscored_field] = $fields['fieldname'];
			$underscored_field = "";
		}

                foreach($module_fields as $field => $value)
                {
                        if( array_key_exists($field , $config_underscored_fields) )
                        {
                                $post_fields[$config_underscored_fields[$field]]=$value;//urlencode($value);
                        }
                }

                $postfields = "<{$module}>\n<row no=\"1\">\n";
		if(isset($post_fields))
		{
			foreach($post_fields as $key => $value)
			{
				$postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
		}
		else
		{
			foreach($module_fields as $key => $value)
			{
				$postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
		}
                $postfields .= "</row>\n</$module>";
		$config_fields = get_option("smack_{$HelperObj->ActivatedPlugin}_fields_shortcodes");
		$extraparams = "&id={$ids_present}";
		$record = $client->insertRecord( $module , "updateRecords" , $this->authtoken ,  $postfields , $extraparams );
                if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) updated successfully" ) )
                {
                        $data['result'] = "success";
                        $data['failure'] = 0;
                }
                else
                {
                        $data['result'] = "failure";
                        $data['failure'] = 1;
                        $data['reason'] = "failed adding entry";
                }
                return $data;
	}

	public function checkEmailPresent( $module , $email )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
		$client = $this->login();
	        $email_present = "no";
		$extraparams = "&searchCondition=(Email|=|{$email})";
                $records = $client->getRecords( $module , "getSearchRecords" , $this->authtoken , "Id , Email" , "" , $extraparams );
		if(isset( $records['result'][$module]['row']['@attributes'] ))
		{
                        $result_lastnames[] = "Last Name";
                        $result_emails[] = $email; 
                        $result_ids[] = $records['result'][$module]['row']['FL'];
                        $email_present = "yes";
		}
		else
		{
			if(is_array($records['result'][$module]['row']))
			{
				foreach( $records['result'][$module]['row'] as $key => $record )
				{
					$result_lastnames[] = "Last Name";
					$result_emails[] = $email; 
					$result_ids[] = $record['FL'];
					$email_present = "yes";
				}
			}
		}
		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
			return true;
		else
			return false;
	}

	public function duplicateCheckEmailField()
	{
		return "Email";
	}
}
