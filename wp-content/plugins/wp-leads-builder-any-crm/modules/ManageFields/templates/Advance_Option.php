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

?>

<br>
<input type="checkbox" value="on" checked=checked onclick="jQuery('#mandatory-upgrade').show(); jQuery('#mandatory-upgrade').css('display','inline')"/>	Advanced Mandatory Field Form Settings <span id="mandatory-upgrade" style="color:red; display:none;"> <a href= "https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" target="_blank"> Upgrade To Pro</a> </span>
<br>
<br>
<input type="checkbox" value="on" checked=checked onclick="jQuery('#multiple-shortcode-upgrade').show(); jQuery('#multiple-shortcode-upgrade').css('display','inline')" />	Add Multiple Forms Shortcode Support <span id="multiple-shortcode-upgrade" style="color:red; display:none;"><a href="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" target="_blank"> Upgrade To Pro</a> </span>
<br>
<br/>
<input type="checkbox" value="on" checked=checked onclick="jQuery('#assignedto-upgrade').show(); jQuery('#assignedto-upgrade').css('display','inline')" />	Assign Captured Lead/Contact To A CRM User
	<br><span style="padding-left:30px;">Assign Leads to User: <img src="<?php echo esc_url(WP_CONST_ULTIMATE_CRM_CPT_DIR)."images/assignedto.png"; ?>" onclick="jQuery('#assignedto-upgrade').show(); jQuery('#assignedto-upgrade').css('display','inline') " /> </span><span id="assignedto-upgrade" style="color:red; display:none; "> <a href="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" target="_blank"> Upgrade To Pro </a> </span></span>
<br>
<input type="checkbox" value="on" checked=checked onclick="jQuery('#ecommerce-upgrade').show(); jQuery('#ecommerce-upgrade').css('display','inline')" />      Ecommerce Integration
	<span id="ecommerce-upgrade" style="color:red; display:none;"><a href="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" target="_blank"> Upgrade To Pro</a> </span>
<br>

