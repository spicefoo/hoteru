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

class WpsugarSettingsActions extends SkinnyActions {

	public function __construct()
	{
	}

	/**
	* The actions index method
	* @param array $request
	* @return array
	*/
	public function executeIndex($request)
	{
		$data = array();
		return $data;
	}

	public function saveSettings($sugarSettArray)
	{
		$fieldNames = array(
			'url' => __('Sugar Host Address'),
			'username' => __('Sugar Username'),
			'password' => __('Sugar Password'),
		//	'user_capture' => __('User Capture'),
	//		'contact_form' => __('Contact Form'),
	//		'smack_email' => __('Smack Email'),
          //             'email' => __('Email id'),
	//		'debug_mode' => __('Debug Mode'),
			);
		foreach ($fieldNames as $field=>$value){
		if(isset($sugarSettArray[$field]))
		{
			$config[$field] = $sugarSettArray[$field];
		}
		}	
		$FunctionsObj = new Functions( );
                $testlogin_result = $FunctionsObj->testlogin( $config['url'] , $config['username'] , $config['password'] );
                if(isset($testlogin_result['login']) && ($testlogin_result['login']['id'] != -1) && is_array($testlogin_result['login']))
                {
                        $successresult = "<p  class='display_success' style='color: green;'> Settings Saved </p>";
                        $result['error'] = 0;
                        $result['success'] = $successresult;
                        $WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
                        $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
                        update_option("wp_{$activateplugin}_settings", $config);
                }
                else
                {
                        $sugarcrmerror = "<p  class='display_failure' style='color:red;' >Please Verify Username and Password.</p>";
                        $result['error'] = 1;
                        $result['errormsg'] = $sugarcrmerror ;
                        $result['success'] = 0;
                }
                return $result;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
                update_option("wp_{$activateplugin}_settings", $config);
	}	
}

class CallSugarSettingsCrmObj extends WpsugarSettingsActions
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) )  
			self::$_instance = new WpsugarSettingsActions();
		return self::$_instance;
	}
}// CallSugarSettingsCrmObj Class Ends
