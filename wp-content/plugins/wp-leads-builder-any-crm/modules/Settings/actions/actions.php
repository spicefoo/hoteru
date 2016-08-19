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

class SettingsActions extends SkinnyActions {

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
	foreach( $request as $key => $REQUESTS )
	{
		foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
		{
			$data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
		}
	}
        $data['HelperObj'] = new WPCapture_includes_helper();
        $data['module'] = $data['HelperObj']->Module;
        $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
        $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
        $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
	$crmslug = str_replace( "free" , "" , $data['activatedplugin'] );
	$crmslug = str_replace( "wp" , "" , $crmslug );
	$data['crm'] = $crmslug;
        $data['action'] = $data['activatedplugin']."Settings";
	if( isset($data['REQUEST']["posted"]) && ($data['REQUEST']["posted"] == "posted") )
	{
		$result = $this->saveSettings( $data );
		if($result['error'] == 1)
		{
			$data['display'] = "<p class='display_error'> ".$result['errormsg']." </p>";
		}
		else if( $result['error'] == 11 )
            	{
                	$data['display'] = "<p class='display_error'>". $result['errormsg']." </p>";
            	}
		else
		{
			$data['display'] = "<p class='display_success'> Settings Successfully Saved </p>";
		}
	}
        return $data;
    }

    public function saveSettings( $request )
    {
	include( WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY . 'modules/'.$request['action'].'/actions/actions.php');
	$SettingsActionsClass = "Wp{$request['crm']}SettingsActions";
	$SettingsActions = new $SettingsActionsClass();
	$result = $SettingsActions->saveSettings( $request['REQUEST'] );
	return $result;
    }
}

