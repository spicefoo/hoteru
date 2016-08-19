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

if(!defined('sugarEntry') || !sugarEntry)
{
        define('sugarEntry', TRUE);
	include_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'lib/nusoap/nusoap.php');
}
class Functions{
	public $username;
	public $accesskey;
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
		$this->url = $SettingsConfig['url'];
	}

	public function login()
	{
		$client = new nusoapclient($this->url.'/soap.php?wsdl',true);

		$user_auth = array(
				'user_auth' => array(
				'user_name' => $this->username,
				'password' => md5($this->accesskey),
				'version' => '0.1'
			),
			'application_name' => 'wp-sugar-pro'
		);

		$login = $client->call('login',$user_auth);
		$session_id = $login['id'];
		$client_array = array( 'login' => $login , 'session_id' => $session_id , "clientObj" => $client );
		return $client_array;
	}
	
	public function testlogin( $url , $username , $password )
	{
		$this->url = $url;
		$this->username = $username;
		$this->accesskey = $password;

		$login = $this->login();

		return $login;
	}

	public function getCrmFields( $module )
	{
                $client_array = $this->login();
		$client = $client_array['clientObj'];
		$recordInfo = $client->call('get_module_fields', array('session' => $client_array['session_id'], 'module_name' => $module));
		$config_fields = array();
                if(isset($recordInfo))
                {
                        $j=0;
                        $module = $recordInfo['module_name'];
                        $AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' ,'date' => 'date' , 'datetime' => 'date' );
                        for($i=0;$i<count($recordInfo['module_fields']);$i++)
                        {
                                if(array_key_exists($recordInfo['module_fields'][$i]['type'], $AcceptedFields)){
                                        if(($recordInfo['module_fields'][$i]['type'] == 'enum') || ($recordInfo['module_fields'][$i]['type'] == 'multienum') || ($recordInfo['module_fields'][$i]['type'] == 'radioenum')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($recordInfo['module_fields'][$i]['options'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option['name'] ;
                                                        $picklistValues[$optionindex]['value'] = $option['value'];
                                                        $optionindex++;
                                                }
                                                $recordInfo['module_fields'][$i]['type'] = Array ( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']] , 'picklistValues' => $picklistValues );
                                        }
                                        else
                                        {
                                                $recordInfo['module_fields'][$i]['type'] = Array( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']]);
                                        }
                                        $config_fields['fields'][$j] = $recordInfo['module_fields'][$i];
                                        $config_fields['fields'][$j]['order'] = $j;
                                        $config_fields['fields'][$j]['publish'] = 1;
                                        $config_fields['fields'][$j]['display_label'] = trim($recordInfo['module_fields'][$i]['label'], ':');
                                        if($recordInfo['module_fields'][$i]['required'] == 1)
                                        {
                                                $config_fields['fields'][$j]['wp_mandatory'] = 1;
                                                $config_fields['fields'][$j]['mandatory'] = 2;
                                        }
                                        else
                                        {
                                                $config_fields['fields'][$j]['wp_mandatory'] = 0;
                                        }
                                        $j++;
                                }
                        }
                        $config_fields['check_duplicate'] = 0;
                        $config_fields['isWidget'] = 0;

                        $users_list = $this->getUsersList();
                        $config_fields['assignedto'] = $users_list['id'][0];
                        $config_fields['module'] = $module;
		}

		
		return $config_fields;
	}

	public function getUsersList()
	{
		$user_details = array();
		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$recordInfo = $client->call('user_list', array('user_name' => $this->username, 'password' => md5($this->accesskey)));

		$userindex = 0;
		if(is_array($recordInfo))
		foreach($recordInfo as $record)
		{
			$user_details['user_name'][$userindex] = $record['user_name'];
			$user_details['id'][$userindex] = $record['id'];
			$user_details['first_name'][$userindex] = $record['first_name'];
			$user_details['last_name'][$userindex] = $record['last_name'];
			$userindex++;
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
			$content_option.="<option id='{$users_list['id'][$i]}' value='{$users_list['id'][$i]}'";
			if($users_list['id'][$i] == $config_fields["assignedto"])
			{
				$content_option.=" selected";
			}
			$content_option.=">{$users_list['first_name'][$i]} {$users_list['last_name'][$i]}</option>";
		}
		$html .= $content_option;
		$html .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		return $html;
	}
	
        public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
        {
                $post = array();
                $post['first_name'] = $user_firstname;
                $post['last_name'] = $user_lastname;
                $post[$this->duplicateCheckEmailField()] = $user_email;
                return $post;
        }

	public function assignedToFieldId()
	{
		return "assigned_user_id";
	}

        public function createRecordOnUserCapture( $module , $module_fields )
        {
		return $this->createRecord( $module , $module_fields );

	}

	public function createRecord( $module , $module_fields )
	{
		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
		}
		$set_entry_parameters = array(
			 //session id
			 "session" => $client_array['session_id'],
			 //The name of the module from which to retrieve records.
			 "module_name" =>  $module,
			 //Record attributes
			 "name_value_list" => $fieldvalues,
		);

		$response = $client->call('set_entry',  $set_entry_parameters , $this->url );

		if(isset($response['id']))
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

	        $email_present = "no";

		$module_table_name = strtolower($module);

		$client_array = $this->login();
		$client = $client_array['clientObj'];

                        $get_entries_count_parameters = array(
                             //Session id
                             'session' => $client_array['session_id'],
                             //The name of the module from which to retrieve records
                             'module_name' => $module,
                             //The SQL WHERE clause without the word "where".
                             'query' => "{$module_table_name}.id in (SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0)",
                             //If deleted records should be included in results.
                //           'deleted' => false
                        );

                        $result = $client->call('get_entry_list', $get_entries_count_parameters);

                        $entry_list = $result['entry_list'];

                        foreach($entry_list as $entry)
                        {
                                foreach($entry['name_value_list'] as $field)
                                {
                                        if($field['name'] == 'last_name')
                                        {
                                                $result_lastnames[] = $field['value'];
                                        }
                                        if($field['name'] == 'email1')
                                        {
						if($email == $field['value'])
						{
							$email_present = 'yes';
						}
						$result_ids[] = $entry['id'];
                                                $result_emails[] = $field['value'];
                                                $result_emails1[] = $field['value'];
                                        }
                                        if($field['name'] == 'email2')
                                        {
						if($email == $field['value'])
						{
							$email_present = 'yes';
						}
						$result_ids[] = $entry['id'];
						$result_emails[] = $field['value'];
                                                $result_emails2[] = $field['value'];
                                        }
                                }
                        }

		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;

		if($email_present == 'yes')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

        function duplicateCheckEmailField()
        {
                return "email1";
        }
}
