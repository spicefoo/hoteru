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

$content1='';
$content='
	<input type="hidden" name="field-form-hidden" value="field-form" />
	<div>';
	$i=0;
	if(!isset($config_fields['fields'][0]))
	{
		$content.='<p style="text-align:center;font-size:20px;color:red;">Crm fields are not yet synchronised</p>';
	}
	else
	{
		$content .='<div id="fieldtable">';
		$content.='<table style="background-color: #F1F1F1; border: 1px solid #dddddd;width:85%;margin-bottom:26px;margin-top:5px"><tr class="smack_highlight smack_alt" style="border-bottom: 1px solid #dddddd;"><th class="smack-field-td-middleit" align="left" style="width: 40px;"><input type="checkbox" name="selectall" id="selectall" onclick="selectAll'."('field-form','".$module."')".';"/></th><th align="left" style="width: 100px;"><h5>Field Name</h5></th><th class="smack-field-td-middleit" align="left" style="width: 100px;"><h5>Show Field</h5></th><th class="smack-field-td-middleit" align="left" style="width: 150px;"><h5>Order</h5></th></tr>';
		$imagepath=WP_CONST_ULTIMATE_CRM_CPT_DIR.'images/';
		for($i=0;$i<count($config_fields['fields']);$i++)
		{
			if( isset( $config_fields['fields'][$i]['wp_mandatory'] ) && ( $config_fields['fields'][$i]['wp_mandatory']==1 ))
			{
				$madantory_checked='checked="checked"';
			}
			else
			{
				$madantory_checked="";
			}
			if(isset( $config_fields['fields'][$i]['mandatory'] ) && ($config_fields['fields'][$i]['mandatory'] == 2 ))
			{
				if($i % 2 == 1)
				$content1.='<tr class="smack_highlight smack_alt">';
				else
				$content1.='<tr class="smack_highlight">';

				$content1.='
				<td class="smack-field-td-middleit"><input type="checkbox" name="select'.$i.'" id="select'.$i.'" disabled="disabled" ></td>
				<td>'.$config_fields['fields'][$i]['label'].' *</td>
				<td class="smack-field-td-middleit">';
				if($config_fields['fields'][$i]['publish'] == 1){
					$content1.='<a class="smack_pointer" name="publish'.$i.'" id="publish'.$i.'" onclick="'."alert('This field is mandotory, cannot hide')".'">
					<img src="' . WP_CONST_ULTIMATE_CRM_CPT_DIR . 'images/tick_strict.png"/>
					</a>';
				}
				$content1.='</td>
				<td class="smack-field-td-middleit">';
				$content1.= "<input class='position-text-box' type='textbox' name='position{$i}' value='".($i+1)."' >";
				$content1.='</td></tr>';
			}
			else
			{
				if($i % 2 == 1)
				$content1.='<tr class="smack_highlight smack_alt">';
				else
				$content1.='<tr class="smack_highlight">';
				$content1.='<td class="smack-field-td-middleit">';
                                $content1.= '<input type="checkbox" name="select'.$i.'" id="select'.$i.'">';
				$content1.= '</td>
				<td>'.$config_fields['fields'][$i]['label'].'</td>
				<td class="smack-field-td-middleit">';
				if($config_fields['fields'][$i]['publish'] == 1){
					$content1.='<a class="smack_pointer" name="publish'.$i.'" id="publish'.$i.'" onclick="published('.$i.',0,'."'$siteurl'".','."'$module'".','."'$options'".','."'$onAction'".');">
					<img src="' . WP_CONST_ULTIMATE_CRM_CPT_DIR . 'images/tick.png"/>
					</a>';
				}
				else{
					$content1.='<a class="smack_pointer" name="publish'.$i.'" id="publish'.$i.'" onclick="published('.$i.',1,'."'$siteurl'".','."'$module'".','."'$options'".','."'$onAction'".');">
					<img src="' . WP_CONST_ULTIMATE_CRM_CPT_DIR . 'images/publish_x.png"/>
					</a>';
				}
				$content1.='</td>
				<td class="smack-field-td-middleit">';
				$content1.= "<input class='position-text-box' type='textbox' name='position{$i}' value='".($i+1)."' >";
				$content1.='</td></tr>';
			}
		}
		$content1.="<input type='hidden' name='no_of_rows' id='no_of_rows' value={$i} />";

		$content1.= "</table></div>";
	}
		$content.=$content1;
$content .='</div>';
echo $content;
?>
