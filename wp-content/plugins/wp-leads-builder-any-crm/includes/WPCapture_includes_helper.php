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

class WPCapture_includes_helper {

	public $capturedId=0;
	public $ActivatedPlugin;
	public $ActivatedPluginLabel;
	public $Action;
	public $Module;
	public $ModuleSlug;
	public function __construct()
	{
		global $IncludedPlugins;
		$ContactFormPluginsObj = new ContactFormPlugins();
		$this->ActivatedPlugin = $ContactFormPluginsObj->getActivePlugin();
		$this->ActivatedPluginLabel = $IncludedPlugins[$this->ActivatedPlugin];
		if(isset( $_REQUEST['action'] ))
		{
			$this->Action = sanitize_text_field($_REQUEST['action']);
		}
		else
		{
			$this->Action = "";
		}
		if(isset($_REQUEST['module']))
		{
			$this->Module = sanitize_text_field($_REQUEST['module']) ;
		}
		else
		{
			$this->Module = "";
		}

		$this->ModuleSlug = rtrim( strtolower($this->Module) , "s");
	}


	public function leads_create_nonce_key()
	{
		return $nonce_key = wp_create_nonce( 'lead_nonce' );
	}

	public function activate(){
		$WPCapture_includes_helper = new WPCapture_includes_helper();
		$WPCapture_includes_helper->initialmigration();

                global $IncludedPlugins , $DefaultActivePlugin ;
                $index = 0;
                $i = 0;
                foreach($IncludedPlugins as $key => $value)
                {
                        if($DefaultActivePlugin == $key)
                        {
                                update_option('ActivatedPlugin' , $DefaultActivePlugin);
                                $index = 1;
                        }
                        if( $i == 0 )
                        {
                                $firstplugin = $key;
                        }

                        $i++;
                }

                if($index == 0)
                {
                        update_option( 'ActivatedPlugin' , $firstplugin );
                }
	}

	public function deactivate(){
		global $IncludedPlugins;
		foreach( $IncludedPlugins as $key => $value )
		{
			delete_option( "smack_{$key}_lead_post_field_settings" );
			delete_option( "smack_{$key}_lead_widget_field_settings" );

			delete_option( "smack_{$key}_lead_fields-tmp" );
			delete_option( "wp_{$key}_settings" );
		}
	}

	public function initialmigration(){
		$migratable_plugin = array( "wp-tiger-free" => "wp-tiger/wp-tiger.php" , "wp-sugar-free" => "wp-sugar-free/wp-sugar-free.php" , "wp-zoho-free" => "wp-zoho-crm/wp-zoho-crm.php" ,  "wp-leads-builder-crm" => "wp-leads-builder-any-crm/index.php" );

		$active_plugins = get_option("active_plugins");

		foreach( $migratable_plugin as $key => $plugins_path )
		{
			if(in_array( $plugins_path , $active_plugins ))
			{
				$this->processmigrate( $key , $plugins_path );
			}
		}
	}

	public function processmigrate( $key, $plugins_path )
	{
		switch( $key )
		{
			case "wp-tiger-free":
				$this->WPTigerFreeMigrate();
			break;
			case "wp-sugar-free":
				$this->WPSugarFreeMigrate();
			break;
			case "wp-zoho-free":
				$this->WPZohoFreeMigrate();
			break;
		}
	}

	public function WPZohoFreeMigrate() {

		$smack_zoho_crm_settings = get_option('smack_zoho_crm_settings');
		$smack_zoho_crm_field_settings = get_option( 'smack_zoho_crm_field_settings' );
		$smack_zoho_crm_widget_field_settings = get_option( 'smack_zoho_crm_widget_field_settings' );
		$smack_zoho_crm_total_widget_field_settings = get_option("smack_zoho_crm_total_widget_field_settings");
		$wp_zoho_contact_widget_form_attempts = get_option("wp-zoho-contact-widget-form-attempts");
		$smack_zoho_crm_total_field_settings = get_option("smack_zoho_crm_total_field_settings");
		$smack_zoho_crm_field_settings["fieldlist"] = $smack_zoho_crm_field_settings['fieldlist'];
		$smack_zoho_crm_field_settings['widgetfieldlist'] = $smack_zoho_crm_widget_field_settings['widgetfieldlist'];

		if(isset($smack_zoho_crm_settings)) {
			$username = $smack_zoho_crm_settings['username'];
			$password = $smack_zoho_crm_settings['password'];
			$authtoken = $smack_zoho_crm_settings['authkey'];
			$user_capture = $smack_zoho_crm_settings['wp_zoho_crm_smack_user_capture'];
			$debug_mode = $smack_zoho_crm_settings['debug_mode'];
			$zoho_settings = array();
			if(!empty($username))
				$zoho_settings['username'] = $username;
			if(!empty($password))
                                $zoho_settings['password'] = $password;
			if(!empty($authtoken))
                                $zoho_settings['authtoken'] = $authtoken;
			$zoho_settings['email'] = '';
			if($user_capture == 'on')
				$zoho_settings['user_capture'] = 'on';
			if($debug_mode == 'on')
                                $zoho_settings['debug_mode'] = 'on';
			
			update_option('wp_wpzohofree_settings', $zoho_settings);
		}
		
		if( is_array($smack_zoho_crm_field_settings) )
		{
			global $plugin_dir_wp_zoho_crm;
			$plugin_dir_wp_zoho_crm;

			if(!class_exists('SmackZohoApi'))
			{
				include_once($plugin_dir_wp_zoho_crm.'/SmackZohoApi.php');
			}
			$client = new SmackZohoApi();
			$recordInfo = $client->APIMethod( "Leads" , "getFields" , $smack_zoho_crm_settings['authkey'] );
			$config_fields = array();
	                $AcceptedFields = Array( 'TextArea' => 'text' , 'Text' => 'string' , 'Email' => 'email' , 'Boolean' => 'boolean', 'Pick List' => 'picklist' , 'varchar' => 'string' , 'Website' => 'url' , 'Phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'Currency' => 'currency' , 'DateTime' => 'date' , 'datetime' => 'date' , 'Integer' => 'string' );

	                $j = 0;
		        foreach($recordInfo['section'] as $section ) {
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
		                                $config_fields['fields'][$j]['publish'] = 0;
		                                $config_fields['fields'][$j]['order'] = $j;
		                                $j++;
		                        }
		                }
		        }

			$formtypes_array = array('post' => array( 'optionname' => "smack_zoho_crm_field_settings" , "fieldlistname" => "fieldlist" , "shortcode" => "zoho_crm_lead_page" ) , "widget" => array( 'optionname' => 'smack_zoho_crm_widget_field_settings' , "fieldlistname" => "widgetfieldlist" , "shortcode" => "zoho_crm_lead_widget_area" ) );

			foreach( $formtypes_array as $formtype => $formtype_array )
			{
				$shortcode = $formtype_array['shortcode'];
				
				$config_post_fields = $config_fields;
				foreach($config_fields['fields'] as $key => $values )
				{
					if(in_array($values['fieldname'] , $smack_zoho_crm_field_settings[$formtype_array['fieldlistname']]))
					{
						$config_post_fields['fields'][$key]['publish'] = 1;
					}
				}

				$config_post_fields['check_duplicate'] = "0";
				if($formtype == 'post')
					$config_post_fields['isWidget'] = '0';
				if($formtype == 'widget')
                                        $config_post_fields['isWidget'] = '1';
				$config_post_fields['redirecturl'] = "";
				$config_post_fields['errormessage'] = "";
				$config_post_fields['successmessage'] = "";
				$config_post_fields['assignedto'] = "";
				$config_post_fields['module'] = "Leads";

				if($config_post_fields['isWidget'] == '0') {
					update_option("smack_wpzohofree_lead_post_field_settings" , $config_post_fields);
					update_option("smack_wpzohofree_lead_fields-tmp" , $config_post_fields);
				}
				else {
					update_option("smack_wpzohofree_lead_widget_field_settings" , $config_post_fields);
				}
			}
		}
	}

	public function WPSugarFreeMigrate() {

		$smack_wp_sugar_free_settings = get_option('smack_wp_sugar_free_settings');
		$smack_wp_sugar_free_field_settings = get_option('smack_wp_sugar_free_field_settings');
		$smack_wp_sugar_widget_free_field_settings = get_option( 'smack_wp_sugar_widget_free_field_settings' );
		$smack_wp_sugar_free_field_settings['widgetfieldlist'] = $smack_wp_sugar_widget_free_field_settings['widgetfieldlist'];

		if(isset($smack_wp_sugar_free_settings)) {

			$url = $smack_wp_sugar_free_settings['url'];
			$username = $smack_wp_sugar_free_settings['username'];
			$password = $smack_wp_sugar_free_settings['password'];
			$user_capture = $smack_wp_sugar_free_settings['wp_sugar_free_smack_user_capture'];
			$debug_mode = $smack_wp_sugar_free_settings['wp_sugar_free_smack_debug'];
			$settings = array();
			if(!empty($url))
				$settings['url'] = $url;
			else
				$settings['url'] = '';
			if(!empty($username))
				$settings['username'] = $username;
			else
				$settings['username'] = '';
			if(!empty($password))
                                $settings['password'] = $password;
			else
				$settings['password'] = '';
			$settings['email'] = '';
			if($user_capture == 'on')
				$settings['user_capture'] = 'on';
			if($debug_mode == 'on')
                                $settings['debug_mode'] = 'on';
			update_option('wp_wpsugarfree_settings', $settings);
		}

		if( is_array($smack_wp_sugar_free_field_settings) )
		{
			global $plugin_dir_wp_sugar;
			$plugin_dir_wp_sugar;

			if(!defined('sugarEntry') || !sugarEntry)
			{
				define('sugarEntry', TRUE);
				include_once($plugin_dir_wp_sugar.'nusoap/nusoap.php');
			}

			$url = trim($smack_wp_sugar_free_settings['url'], '/');
			$username = $smack_wp_sugar_free_settings['username'];
			$password = $smack_wp_sugar_free_settings['password'];
			$client = new nusoapclient($url.'/soap.php?wsdl',true);
			$user_auth = array(
				'user_auth' => array(
				'user_name' => $username,
				'password' => md5($password),
				'version' => '0.1'
			),
			'application_name' => 'wp-sugar-free');
			$login = $client->call('login',$user_auth);
			$session_id = $login['id'];

			$recordInfo = $client->call('get_module_fields', array('session' => $session_id, 'module_name' => "Leads"));

			if(isset($recordInfo['error']['number']) && is_array($recordInfo['error']) )
			{
			}
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
						$config_leads_fields['fields'][$j] = $recordInfo['module_fields'][$i];
						$config_leads_fields['fields'][$j]['order'] = $j;
						$config_leads_fields['fields'][$j]['publish'] = 0;
						$config_leads_fields['fields'][$j]['display_label'] = trim($recordInfo['module_fields'][$i]['label'], ':');
						if($recordInfo['module_fields'][$i]['required']==1)
						{
							$config_leads_fields['fields'][$j]['wp_mandatory'] = 1;
							$config_leads_fields['fields'][$j]['mandatory'] = 2;
						}
						else
						{
							$config_leads_fields['fields'][$j]['wp_mandatory'] = 0;
						}
						$j++;
					}
				}

				$formtypes_array = array('post' => array( 'optionname' => "smack_wp_sugar_free_field_settings" , "fieldlistname" => "fieldlist" , "shortcode" => "sugarcrm_webtolead" ) , "widget" => array( 'optionname' => 'smack_wp_sugar_widget_free_field_settings' , "fieldlistname" => "widgetfieldlist" , "shortcode" => "sugarcrm_webtolead_WG" ) );

				foreach( $formtypes_array as $formtype => $formtype_array )
				{
					$config_post_fields = $config_leads_fields;
					
					$shortcode = $formtype_array['shortcode'];

					foreach($config_leads_fields['fields'] as $key => $values )
					{
						if(in_array($values['name'] , $smack_wp_sugar_free_field_settings[$formtype_array['fieldlistname']]))
						{
							$config_post_fields['fields'][$key]['publish'] = 1;
						}
					}

					$config_post_fields['check_duplicate'] = "0";
					if($formtype == 'post') 
						$config_post_fields['isWidget'] = '0';
					if($formtype == 'widget')
						$config_post_fields['isWidget'] = '1';

					$config_post_fields['assignedto'] = "1";
					$config_post_fields['module'] = "Leads";
					$config_post_fields['redirecturl'] = "";
					$config_post_fields['errormessage'] = "";
					$config_post_fields['successmessage'] = "";
					
					if($config_post_fields['isWidget'] == '0') {
						update_option('smack_wpsugarfree_lead_post_field_settings', $config_post_fields);
						update_option('smack_wpsugarfree_lead_fields-tmp', $config_post_fields);
					} else {
						update_option('smack_wpsugarfree_lead_widget_field_settings', $config_post_fields);
					}	
				}
			}
		}		

	}

	public function WPTigerFreeMigrate() {

		$smack_vtlc_settings = get_option('smack_vtlc_settings');
		$smack_vtlc_widget_field_settings = get_option('smack_vtlc_widget_field_settings');
		$smack_vtlc_field_settings = get_option('smack_vtlc_field_settings');
		if(isset($smack_vtlc_settings)) {

			$url = $smack_vtlc_settings['url'];
			$username = $smack_vtlc_settings['smack_host_username'];
			$accesskey = $smack_vtlc_settings['smack_host_access_key'];
			$debug_mode = $smack_vtlc_settings['wp_tiger_smack_debug'];
			$user_capture = $smack_vtlc_settings['wp_tiger_smack_user_capture'];
			$result = array();
			if(!empty($url))
				$result['url'] = $url;
			if(!empty($username))
				$result['username'] = $username;
			if(!empty($accesskey))
                                $result['accesskey'] = $accesskey;
			if($debug_mode == 'on')
				$result['debug_mode'] = 'on';
			if($user_capture == 'on')
				$result['user_capture'] = 'on';

			update_option('wp_wptigerfree_settings', $result);
		
		/* code to migrate widget*/
                        $old_url = getcwd();
                        global $plugin_dir_wp_tiger;
                        chdir($plugin_dir_wp_tiger);
			if(!class_exists("Vtiger_WSClient"))
			{
                        	include_once($plugin_dir_wp_tiger . "vtwsclib/Vtiger/WSClient.php");
			}
			
                        $client = new Vtiger_WSClient($url);
                        $login = $client->doLogin($username, $accesskey);

                        if (!$login) {

                        } else {
                                $record = $recordInfo = $client->doDescribe("Leads");
                                if ($record) {
					$fields = $record['fields'];
					foreach( $fields as $fieldattribute )
					{
						$Fields_by_FieldName[$fieldattribute['name']] = $fieldattribute; 
					}
                                }
                        }       

			if (!empty ($smack_vtlc_settings ['hostname']) && !empty ($smack_vtlc_settings ['dbuser'])) {
				$vtdb = new wpdb ($smack_vtlc_settings ['dbuser'], $smack_vtlc_settings ['dbpass'], $smack_vtlc_settings ['dbname'], $smack_vtlc_settings ['hostname']);
				$allowedFields = $vtdb->get_results("SELECT fieldid, fieldname, fieldlabel, typeofdata FROM vtiger_field WHERE tabid = 7 AND tablename != 'vtiger_crmentity' AND uitype != 4 ORDER BY block, sequence");
			}
			$nooffields = count($allowedFields);
			foreach( $allowedFields as $stdobj )
			{
				$db_fields[$stdobj->fieldname] = $stdobj->fieldid;
			}


			$smack_vtlc_field_settings_array = array( 
						0 => array( "varname" => "smack_vtlc_field_settings" , "arrayname" => "fieldlist" , "isWidget" => "0" ), 
						1 => array( "varname" => "smack_vtlc_widget_field_settings" , "arrayname" => "widgetfieldlist" , "isWidget" => "1" )
					);
		
			foreach( $smack_vtlc_field_settings_array as $smack_vtlc_field_settings_array_key => $smack_vtlc_field_settings_array_value )
			{
				$varname = $smack_vtlc_field_settings_array_value['varname'];
				$arrayname = $smack_vtlc_field_settings_array_value['arrayname'];
				$formtype = $smack_vtlc_field_settings_array_value['isWidget'];
				if($varname == "smack_vtlc_field_settings")
				{
					$fieldlist_array = $smack_vtlc_field_settings[$arrayname];
				}
				else
				{
					$fieldlist_array = $smack_vtlc_widget_field_settings[$arrayname];
				}

				$j=0;
				for($i=0;$i<count($recordInfo['fields']);$i++)
				{
					if($recordInfo['fields'][$i]['nullable']=="" && $recordInfo['fields'][$i]['editable']=="" ){
					}
					elseif($recordInfo['fields'][$i]['type']['name'] == 'reference'){
					}
					elseif($recordInfo['fields'][$i]['name'] == 'modifiedby' || $recordInfo['fields'][$i]['name'] == 'assigned_user_id' ){
					}
					else{
						$config_fields['fields'][$j] = $recordInfo['fields'][$i];
						$config_fields['fields'][$j]['order'] = $j;

						if( in_array( $db_fields[$recordInfo['fields'][$i]['name']] , $fieldlist_array ) )
						{
							$config_fields['fields'][$j]['publish'] = 1;
						}
						else
						{
							$config_fields['fields'][$j]['publish'] = 0;
						}

						$config_fields['fields'][$j]['display_label'] = $recordInfo['fields'][$i]['label'];
						if($recordInfo['fields'][$i]['mandatory']==1)
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

				$config_fields['update_record'] = '0';
				$config_fields['module'] = "Leads";
				$config_fields['isWidget'] = $formtype;
				$config_fields['assignedto'] = "19x1";
				$config_fields['check_duplicate'] = "0";
				$config_fields['redirecturl'] = '';
				$config_fields['errormessage'] = '';
				$config_fields['successmessage'] = '';

				if($config_fields['isWidget'] == '0') {
					update_option('smack_wptigerfree_lead_post_field_settings', $config_fields);
					update_option('smack_wptigerfree_lead_fields-tmp', $config_fields);
				}
				else {
					update_option('smack_wptigerfree_lead_widget_field_settings', $config_fields);
				}
			}
		}
	}
	
	public static function output_fd_page()
	{
		require_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'config/settings.php');
		require_once(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'lib/skinnymvc/controller/SkinnyController.php');	

		$HelperObj = new WPCapture_includes_helper;
        	$mainpage_nonce_key = $HelperObj->leads_create_nonce_key();
		$main_page = get_admin_url() . 'admin.php?';
		$main_page_url = add_query_arg(array( 'page' => WP_CONST_ULTIMATE_CRM_CPT_SLUG .'/index.php' , '__module' => 'Settings' , '__action' => 'view'  ) , $main_page  );

			if (!isset($_REQUEST['__module'])) {
				wp_safe_redirect( $main_page_url );
				exit;
			}
		$c = new SkinnyControllerCommonCrmFree;
                $c->main();
	}



	public function renderMenu()
	{
		include(plugin_dir_path(__FILE__) . '../templates/menu.php');
	}


	public function renderContent()
	{

		if($this->Action == "Settings" || $this->Action=="")
		{
			if($this->Action=="")
			{
				$this->Action = "Settings";
			}
			$action = $this->ActivatedPlugin.$this->Action;
                        $module = $this->Module;
		}
		else
		{
			$action = $this->Action;
			$module = $this->Module;
		}

		include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/actions/actions.php');
		include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/templates/view.php');

	}
}

class CallWPCaptureObj extends WPCapture_includes_helper
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) ) 
			self::$_instance = new WPCapture_includes_helper();
		return self::$_instance;
	}
}// CallWPCaptureObj Class Ends
?>
