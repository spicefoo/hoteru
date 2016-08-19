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

	$skinnyObj = CallManageShortcodesCrmObj::getInstance();
	$HelperObj = new WPCapture_includes_helper;
	$nonce_key = $HelperObj->leads_create_nonce_key();
	if( !wp_verify_nonce( $nonce_key , 'lead_nonce' ) )
	{
		die('You are not allowed to do this operation');
	}
	$module = $HelperObj->Module;
	$moduleslug = $HelperObj->ModuleSlug;
	$activatedplugin = $HelperObj->ActivatedPlugin;
	$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
	$plugin_url= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
	$onAction= 'onCreate';
	$siteurl= site_url();
	$options = "smack_{$activatedplugin}_fields_shortcodes";
	$url = site_url();
	$module = "Leads";
	$content="";
	$content1="";
	$content .= "
	<h2 style='margin-left:15px;'>Forms and Shortcodes</h2> <br>
	<div class='wp-common-crm-content'>
	<table style='margin-right:20px;margin-bottom:20px;border: 1px solid #dddddd;'>
		<tr style='border-top: 1px solid #dddddd;'>
		</tr>
		<tr class='smack-crm-pro-highlight smack-crm-pro-alt' style='border-top: 1px solid #dddddd;'>
			<th class='smack-crm-free-list-view-th' style='width: 100px;'>Form No</th>
			<th class='smack-crm-free-list-view-th' style='width: 300px;'>Shortcode</th>
			<th class='smack-crm-free-list-view-th' style='width: 90px;'>Type</th>
			<th class='smack-crm-free-list-view-th' style='width: 90px;'>Assignee</th>
			<th class='smack-crm-free-list-view-th' style='width: 200px;'>Module</th>
			<th class='smack-crm-free-list-view-th' style='width: 150px;'>Submits</th>
			<th class='smack-crm-free-list-view-th' style='width: 150px;'>Success</th>
			<th class='smack-crm-free-list-view-th' style='width: 150px;'>Failure</th>
			<th class='smack-crm-free-list-view-th' style='width: 200px;'>Action</th>
		</tr>";
	$shortcodes = get_option($options);
	$number = 1;
	$site_url = site_url();
	$admin_url = get_admin_url();
	$formtypes = array('post' => "Post" , 'widget' => "Widget");
	foreach( $formtypes as $formtype_key => $formtype_value )
	{
		if($number % 2 == 1)
		{
			$content1 .= "<tr class='smack-crm-pro-highlight'>";
		}
		else
		{
			$content1 .= "<tr class='smack-crm-pro-highlight smack-crm-pro-alt'>";
		}
		$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'>{$number}</td>";
		$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'>[{$activatedplugin}-web-form type='{$formtype_key}']</td>";
		$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'>{$formtype_key}</td>";
		$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'>Admin</td>";
		$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'>Leads</td>";
			$successfulAttemptsOption = get_option( "wp-{$activatedplugin}-contact-{$formtype_key}-form-attempts" );
			$total = $successfulAttemptsOption['total'];
			if( $total !=  0 ){} else { $total = 0; }
			$success = $successfulAttemptsOption['success'];
			if( $success != 0 ) { } else { $success = 0 ; }			
			$failure = $total - $success;			
if(isset($total));
{
$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'><span style='color:#000;'>$total</span></td>";
$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'><span style='color:green;'>$success</span></td>";
$content1 .= "<td style='text-align:center; border-top: 1px solid #dddddd;'><span style='color:red;'>$failure</span></td>";

}
 		$content1 .= "<td style='text-align:center;border-top: 1px solid #dddddd;'>";
		$Helper_obj = new WPCapture_includes_helper;
        	$Edit_nonce_key = $Helper_obj->leads_create_nonce_key();
		$edit_url =  WP_CONST_ULTIMATE_CRM_CPT_PLUG_URL ;
		$content1 .= "<a href='".esc_url( add_query_arg( array( '__module' => 'ManageShortcodes', '__action' => 'ManageFields', 'module' => 'Leads', 'EditShortCode' => 'yes', 'formtype' =>$formtype_key , '__wpnonce' => $Edit_nonce_key ),$edit_url ) )."'style='padding-right:10px;'> Edit </a>";    	
		$content1 .="<div class='tooltipd'><label style='color: #3399ff;'>Delete</label><span class='tooltiptext'><a href='https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html' target='__blank'>Upgrade To Pro</a></span></div>";
                $content1 .= "</td>";
		$content1 .= "</tr>";
		$number++;
	}
	$content .= $content1;
	$content .= "</table>";
	$content .= "<input type='hidden' id='ShortCodeaction' name='ShortCodeaction'></div>";
	echo $content;
?>
	<br>
<div style="float:left;marginThirdparty1px;margin-left:25%;">
			<input type="button" size="50" class="button-primary" value="Create Lead Form"></div>

<div style="float:left;margin-top:-1px;margin-left:50px;">
                        <input type="button" size="50" class="button-primary" value="Create Contact Form"></div>
<div class="tooltipp" style="float:left;margin-top:-1px;margin-left:65px;">
                        <input type="button" size="50" class="button-primary" value="Use Existing Forms"></div>

