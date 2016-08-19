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

require_once(plugin_dir_path(__FILE__).'/../ConfigureIncludedPlugins.php');
class ContactFormPlugins
{
	public function getActivePlugin()
	{
		return get_option('ActivatedPlugin');
	}

	public function getAllPlugins()
	{

	}

	public function getInactivePlugins()
	{

	}

	public function getPluginActivationHtml( )
	{
		global $IncludedPlugins;
		global $crmdetails;
		$html = "";
		$select_option = "";
		$html .= '<span style ="position:relative;left:21%;"><select name = "pluginselect" id ="pluginselect" onchange="selectedPlug( this )">';

		foreach($IncludedPlugins as $pluginslug => $pluginlabel)
		{
			if($this->getActivePlugin() == $pluginslug )
			{
				
				$select_option .= "<option value='{$pluginslug}' selected=selected > {$pluginlabel} </option>";
			}
			else
			{
				$select_option .= "<option value='{$pluginslug}' > {$pluginlabel} </option>" ;
			}
		}
		$html .= $select_option;
		$html .= "</select></span>";
		return $html;
	}
	 public function getThirdpartyPLugins( ) {
        }

	public function getCustomFieldPlugins( ) {
        }
}
?>
