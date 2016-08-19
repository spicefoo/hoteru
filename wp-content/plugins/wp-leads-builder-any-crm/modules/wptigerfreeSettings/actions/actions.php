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

class WptigerSettingsActions extends SkinnyActions {

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

	public function executeView($request)
	{
		$data = array();
		return $data;
	}


	public function saveSettings($sett_array)
	{
		$fieldNames = array(
			'url' => __('Vtiger Url'),
			'username' => __('Vtiger User Name'),
			'accesskey' => __('Vtiger Access Key'),
		);

		foreach ($fieldNames as $field=>$value){
			if(isset($sett_array[$field]))
			{
				$config[$field] = trim($sett_array[$field]);
			}
		}
		$FunctionsObj = new Functions( );
		$testlogin_result = $FunctionsObj->testLogin( $sett_array['url'] , $sett_array['username'] , $sett_array['accesskey'] );
		if($testlogin_result == 1)
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
			$vtigercrmerror = "<p  class='display_failure' style='color:red;' >Please Verify Username and Password.</p>";
			$result['error'] = 1;
			$result['errormsg'] = $vtigercrmerror ;
			$result['success'] = 0;
		}
		return $result;
	}
}
