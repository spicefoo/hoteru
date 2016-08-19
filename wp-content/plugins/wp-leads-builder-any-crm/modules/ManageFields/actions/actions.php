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

class ManageFieldsActions extends SkinnyActions {

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
		// return an array of name value pairs to send data to the template
		$data = array();
		return $data;
	}

	public function executeview($request)
	{
		$data = array();
		foreach( $request as $key => $REQUESTS )
		{
			foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
			{
				$data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
			}
		}
		$data['HelperObj'] = new WPCapture_includes_helper;
		$data['module'] = $data["HelperObj"]->Module;
		$data['moduleslug'] = $data['HelperObj']->ModuleSlug;
		$data['activatedplugin'] = $data["HelperObj"]->ActivatedPlugin;
		$data['activatedpluginlabel'] = $data["HelperObj"]->ActivatedPluginLabel;
		$data['plugin_url']= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
		$data['onAction'] = 'onCreate';
		$data['siteurl'] = site_url();
		if(isset($data['REQUEST']['formtype']))
		{
			$data['formtype'] = $data['REQUEST']['formtype'];
		}
		else
		{
			$data['formtype'] = "post";
		}
		if(isset($data['REQUEST']['EditShortCode']) && ( $data['REQUEST']['EditShortCode'] == "yes" ) )
		{
			$data['option'] = $data['options'] = "smack_{$data['activatedplugin']}_lead_{$data['formtype']}_field_settings";
		}
		else
		{
			$data['option'] = $data['options'] = "smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp";
		}


		if(isset($data['REQUEST']['EditShortCode']) && ( $data['REQUEST']['EditShortCode'] == "yes" ) )
		{
			$data['onAction'] = 'onEditShortCode';
		}
		else
		{
			$data['onAction'] = 'onCreate';
		}
                return $data;
	}
}
