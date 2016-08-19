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

/*

Cases : 
1) CreateNewFieldShortcode		Will create new field shortcode
2) FetchCrmFields			Will Fetch crm fields from the the crm
3) FieldSwitch				Enable/Disable single field
4) DuplicateSwitch			Change Duplicate handling settings 
5) MoveFields				Change the order of the fields
6) MandatorySwitch			Make Mandatory or Remove Mandatory
7) SaveDisplayLabel			Save Display Label
8) SwitchMultipleFields			Enable/Disable multiple fields
9) SwitchWidget				Enable/Disable widget  form
10) SaveAssignedTo			Save Assignee of the form leads 
11) CaptureAllWpUsers			Capture All wp users
*/

class OverallFunctions {

	public function doFieldAjaxAction()
	{
		$module = sanitize_text_field($_REQUEST['module']) ;
		$module_options = $module;
		$options = sanitize_text_field($_REQUEST['option']);
		$onAction = sanitize_text_field($_REQUEST['onAction']);
		$siteurl = site_url();
		$HelperObj = new WPCapture_includes_helper;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$FunctionsObj = new Functions();
		$tmp_option = "smack_{$activatedplugin}_{$moduleslug}_fields-tmp";
		if($onAction == 'onEditShortCode');
		{
			$original_options = "smack_{$activatedplugin}_fields_shortcodes";
			$original_config_fields = get_option($original_options);
		}
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		if($onAction == 'onCreate')
		{
			$config_fields = get_option($options);
		}
		else
		{
			$config_fields = get_option($options);
		}
		$FieldCount = 0;
		if(isset($config_fields['fields']))
		{
			$FieldCount =count($config_fields['fields']);
		}
		if(isset($config_fields)){
			$error[0] = 'no fields';
		}
		switch($_REQUEST['doaction'])
		{
			case "FetchCrmFields":

				$config_fields = $FunctionsObj->getCrmFields( $module );
       
				if($options != 'getSelectedModuleFields')
				{
					include(WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY.'templates/crm-fields-form.php');
				}

                                if($onAction == 'onCreate')
                                {
                                        update_option($options, $config_fields);
                                }
                                else
                                {
					update_option($options, $config_fields);
					update_option("smack_{$activatedplugin}_{$moduleslug}_fields-tmp", $config_fields);
                                }
				break;
			default:
				break;
		}
	}
}

class AjaxActionsClass
{
	public static function adminAllActions()
	{

		$OverallFunctionObj = new OverallFunctions();
		if( isset($_REQUEST['operation']) && (sanitize_text_field($_REQUEST['operation']) == "NoFieldOperation") )
		{
			$OverallFunctionObj->doNoFieldAjaxAction( );
		}
		else
		{
			$OverallFunctionObj->doFieldAjaxAction(  );
		}
		die;
	}
}
add_action('wp_ajax_adminAllActions', array( "AjaxActionsClass" , 'adminAllActions' ));

class CapturingProcessClass
{
	function CaptureFormFields( $globalvariables )
	{
		$HelperObj = new WPCapture_includes_helper;
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$duplicate_inserted = 0;
		$module = $globalvariables['module'];
		$post = $globalvariables['post'];
		$FunctionsObj = new Functions();
		if(is_array($post))
		{
			foreach($post as $key => $value)
			{
				if(($key != 'moduleName') && ($key != 'submitcontactform') && ($key != 'submitcontactformwidget') && ($key != '') && ($key != 'submit'))
				{
					$module_fields[$key] = $value;
				}
			}
		}
		unset($module_fields['formnumber']);
		unset($module_fields['IsUnreadByOwner']);
		$module = "Leads";
		$record = $FunctionsObj->createRecord( $module , $module_fields);
		$data = "";
		if($record['result'] == "success")
		{
			$duplicate_inserted++;
			$data = "/$module entry is added./";
		}

		return $data;
	}

//	Capture wordpress user on registration or creating a user from Wordpress Users

	function capture_registering_users($user_id)
	{
		$HelperObj = new WPCapture_includes_helper;
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$usersync_config = get_option("wp_{$activatedplugin}_usersync");
		if( isset($usersync_config['user_capture']) && ($usersync_config['user_capture'] == "on") )
		{
			$module = "Contacts";
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$successful = 0;
			$failed = 0;
			$url = $SettingsConfig['url'];
			$username = $SettingsConfig['username'];
			$accesskey = $SettingsConfig['accesskey'];
			$FunctionsObj = new Functions();
			$config_user_capture = get_option("smack_{$activatedplugin}_user_capture_settings");
			$user_data = get_userdata( $user_id );
			$user_email = $user_data->data->user_email;
			$user_lastname = get_user_meta( $user_id, 'last_name', 'true' );
			$user_firstname = get_user_meta( $user_id, 'first_name', 'true' );
			if(empty($user_lastname))
			{
				$user_lastname = $user_data->data->display_name;
			}
			$post = $FunctionsObj->mapUserCaptureFields( $user_firstname , $user_lastname , $user_email );
			$config_fields = get_option("smack_{$activatedplugin}_lead_fields-tmp");
			$post[$FunctionsObj->assignedToFieldId()] = $config_fields['assignedto'];
			$record = $FunctionsObj->createRecordOnUserCapture( $module , $post );
			if($record)
			{
				$data = "/$module entry is added./";
			}
		}
	}
}
?>
