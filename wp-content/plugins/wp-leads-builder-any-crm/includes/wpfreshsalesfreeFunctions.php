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

//include_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'lib/SmackSalesForceApi.php');
class Functions{

	public $domain = null;
        public $auth_token = null;
        public $username = null;
        public $password = null;
	public $result_emails;
	public $result_ids;
	public function __construct()
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$get_freshsales_settings_info = get_option("wp_{$activateplugin}_settings");
                $this->auth_token = $get_freshsales_settings_info['auth_token'];
                $this->domain = $get_freshsales_settings_info['domain_url'];
                $this->username = $get_freshsales_settings_info['username'];
                $this->password = $get_freshsales_settings_info['password'];
	}

	public function testLogin( $domain_url , $login, $password )
        {
                $domain_url = $domain_url . '/api/sign_in';
                $process = curl_init($domain_url);
                curl_setopt($process, CURLOPT_POST, true);
                curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($process, CURLOPT_USERPWD, "$login:$password");
                curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
                $login = curl_exec($process);
                return $login;
        }

	public function getCrmFields($module) {
                #$this->getUsersList();
                #Fetch all fields based on the module
                $url = $this->domain . '/api/settings/' . strtolower($module) . '/fields';
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";
                curl_setopt_array($ch, array(
                        CURLOPT_HTTPGET        => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                ));
                $response  = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_status != 200){
                        throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
                }
                $fieldsArray = json_decode($response);
                $config_fields = array();
                if(!empty($fieldsArray)) {
                        $i = 0;
                        foreach ( $fieldsArray->fields as $item => $fieldInfo ) {
                                if($fieldInfo->required == 1) {
                                        $config_fields['fields'][$i]['wp_mandatory'] = 1;
                                        $config_fields['fields'][$i]['mandatory'] = 2;
                                } else {
                                        $config_fields['fields'][$i]['wp_mandatory'] = 0;
                                        $config_fields['fields'][$i]['mandatory'] = 0;
                                }
                                if($fieldInfo->type == 'dropdown') {
                                        $optionindex = 0;
                                        $picklistValues = array();
                                        foreach($fieldInfo->choices as $option)
                                        {
                                                $picklistValues[$optionindex]['id'] = $option->id;
                                                $picklistValues[$optionindex]['label'] = $option->value;
                                                $picklistValues[$optionindex]['value'] = $option->value;
                                                $optionindex++;
                                        }
                                        $config_fields['fields'][$i]['type'] = Array ( 'name' => 'picklist', 'picklistValues' => $picklistValues );
                                } elseif($fieldInfo->type == 'checkbox') {
                                        $config_fields['fields'][$i]['type'] = array("name" => 'boolean');
                                } elseif($fieldInfo->type == 'number') {
					$config_fields['fields'][$i]['type'] = array("name" => 'integer');
                                } else {
                                        $config_fields['fields'][$i]['type'] = array("name" => $fieldInfo->type);
                                }
                                if($fieldInfo->base_model == 'LeadCompany') {
                                        $field_name = 'company_' . $fieldInfo->name;
                                } elseif($fieldInfo->base_model == 'LeadDeal') {
                                        $field_name = 'deal_' . $fieldInfo->name;
                                } else {
                                        $field_name = $fieldInfo->name;
                                }
                                $config_fields['fields'][$i]['name'] = str_replace(" " , "_", $field_name);
                                $config_fields['fields'][$i]['fieldname'] = $field_name;
                                $config_fields['fields'][$i]['label'] = $fieldInfo->label;
                                $config_fields['fields'][$i]['display_label'] = $fieldInfo->label;
                                $config_fields['fields'][$i]['publish'] = 1;
                                $config_fields['fields'][$i]['order'] = $fieldInfo->position;
                                $config_fields['fields'][$i]['base_model'] = $fieldInfo->base_model;
                                $i++;
                        }
                        $config_fields['check_duplicate'] = 0;
                        $config_fields['isWidget'] = 0;
                        $users_list = $this->getUsersList();
                        $config_fields['assignedto'] = $users_list['id'][0];
                        $config_fields['module'] = $module;
                        return $config_fields;
                }
        }


	public function getUsersList( $module = 'users')
	{
		$url = $this->domain . '/settings/' . strtolower($module);
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";
                curl_setopt_array($ch, array(
                        CURLOPT_HTTPGET        => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                ));
                $response  = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_status != 200){
                        throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
                }
                $userInfo = json_decode($response);
                $user_details = array();
                foreach($userInfo->users as $data) {
                        $user_details['user_name'][] = $data->email;
                        $user_details['id'][] = $data->id;
                        $user_details['first_name'][] = '';
                        $user_details['last_name'][] = $data->display_name;
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
		$post['first_name'] = $user_firstname;
		$post['last_name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}


        public function assignedToFieldId()
        {
                return "owner_id";
        }

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		$record = $this->createRecord( $module , $module_fields );
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

	public function createRecord($module, $lead_info )
        {
                $module = strtolower($module);
                $url = $this->domain . '/api/' . $module;
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";
                if($module == 'leads') {
                        $index = 'lead';
                } elseif ($module == 'contacts') {
                        $index = 'contact';
                }
                $data_array = array();
                foreach($lead_info as $key => $val) {
                        if(strpos($key, 'company_') !== false) {
                                $key = str_replace('company_', '', $key);
                                $data_array[$index]['company'][$key] = $val;
                        } elseif(strpos($key, 'deal_') !== false) {
                                if($key === 'deal_deal_product_id') {
                                        $key = 'deal_product_id';
                                } else {
                                        $key = str_replace( 'deal_', '', $key );
                                }
                                $data_array[$index]['deal'][$key] = $val;
                        } else {
                                $data_array[$index][ $key ] = $val;
                        }
                }
                $data_array = json_encode($data_array);
                curl_setopt_array($ch, array(
                        CURLOPT_POST           => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_POSTFIELDS     => $data_array,
                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
                ));
                $response  = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_status != 200){
                        #throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
                }
                $records = json_decode($response);
		if( $records->errors->code == '500' && $records->errors->message[0] == 'Contact with this email already exists' )
                {
                        $one = $this->checkEmailPresent( 'Contacts' , $lead_info['email'] );
                        $contact_id = $this->result_ids[0];
                        $records = $this->updateEmailPresentRecord( 'Contacts' , $contact_id , $data_array);
                }

                if($records->{$index}->id) {
                        $data['result'] = "success";
			$data['failure'] = 0;
                } else {
                        $data['result'] = "failure";
                        $data['failure'] = 1;
                        $data['reason'] = "Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response; #"failed adding entry";
                }
                return $data;
        }

	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$leadId = $this->result_ids;
                $module = strtolower($module);
                $url = $this->domain . '/api/' . $module . '/' . $leadId[0];
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";
                if($module == 'leads') {
                        $index = 'lead';
                } elseif ($module == 'contacts') {
                        $index = 'contact';
                }
                $data_array = array();
                foreach($lead_info as $key => $val) {
                        if(strpos($key, 'company_') !== false) {
                                $key = str_replace('company_', '', $key);
                                $data_array[$index]['company'][$key] = $val;
                        } elseif(strpos($key, 'deal_') !== false) {
                                if($key === 'deal_deal_product_id') {
                                        $key = 'deal_product_id';
                                } else {
                                        $key = str_replace( 'deal_', '', $key );
                                }
                                $data_array[$index]['deal'][$key] = $val;
                        } else {
                                $data_array[$index][ $key ] = $val;
                        }
                }

                $data_array = json_encode($data_array);
                curl_setopt_array($ch, array(
                        CURLOPT_CUSTOMREQUEST  => "PUT",
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_POSTFIELDS     => $data_array,
                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
                ));
                $response  = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_status != 200){
                        #throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
			}
                $records = json_decode($response);
                if($records->{$index}->id) {
                        $data['result'] = "success";
                        $data['failure'] = 0;
                } else {
                        $data['result'] = "failure";
                        $data['failure'] = 1;
                        $data['reason'] = "Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response; #"failed adding entry";
                }
                return $data;	
	}

	public function checkEmailPresent( $module , $email )
	{
		$module = strtolower($module);
                $activateplugin = "wpfreshsalesfree";
                $result_emails = array();
                $result_ids = array();
                if($module == 'leads') {
                        $search_filter = 'filtered_search/lead';
                        $postArray = array(
                                'filter_rule' => json_encode(array(
                                                0 => array(
                                                        'attribute' => 'lead_email.email',
                                                        'operator'  => 'is_in',
                                                        'value'     => $email,
                                                )
                                        )
                                ));
                } else if($module == 'contacts') {
                        $search_filter = 'filtered_search/contact';
                        $postArray = array(
                                'filter_rule' => json_encode(array(
                                                0 => array(
                                                        'attribute' => 'contact_email.email',
                                                        'operator'  => 'is_in',
                                                        'value'     => $email,
                                                )
                                        )
                                ));
                }
                $url = $this->domain . '/api/' . $search_filter;
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";

                curl_setopt_array($ch, array(
                        CURLOPT_POST           => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_POSTFIELDS     => $postArray,
                ));
                $response  = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
                if ($http_status != 200){
                        throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
                }
                $records = json_decode($response);
                $email_present = "no";
                if( $records->meta->total >= 0 ) {
                        $result_lastnames[] = $records->{$module}[0]->display_name; //"Last Name";
                        $result_emails[] = $email;
                        $result_ids[] = $records->{$module}[0]->id;
                        if($email == $records->{$module}[0]->email)
                                $email_present = "yes";
                }
                $this->result_emails = $result_emails;
                $this->result_ids = $result_ids;
                if($email_present == 'yes')
                        return true;
                else
                        return false;		
	}

	public function updateEmailPresentRecord( $module , $contact_id , $contact_info)
        {
                $module = strtolower($module);
                $url = $this->domain . '/api/' . $module . '/' . $contact_id;
                $ch = curl_init($url);
                $auth_string = "$this->username:$this->password";
                if($module == 'leads') {
                        $index = 'lead';
                } elseif ($module == 'contacts') {
                        $index = 'contact';
                }
                $data_array = $contact_info;
                curl_setopt_array($ch, array(
                        CURLOPT_CUSTOMREQUEST  => "PUT",
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_USERPWD        => $auth_string,
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_POSTFIELDS     => $data_array,
                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
                ));
                $response  = curl_exec($ch);
                curl_close($ch);
                $records = json_decode($response);
                return $records;
        }
	
	public function duplicateCheckEmailField()
	{
		return "email";
	}
}
