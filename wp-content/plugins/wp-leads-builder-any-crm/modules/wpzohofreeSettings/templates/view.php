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

$siteurl = site_url();
$config = get_option("wp_{$skinnyData['activatedplugin']}_settings");

if( $config == "" )
{
        $config_data = 'no';
}
else
{
        $config_data = 'yes';
}
?>
<input type="hidden" id="get_config_free" value="<?php echo $config_data ?>" >
<input type='hidden' id='revert_old_crm' value='wpzohofree'>
<form id="smack-zoho-settings-form" action="<?php echo esc_url_raw($_SERVER['REQUEST_URI']); ?>" method="post">
	<input type="hidden" name="smack-zoho-settings-form" value="smack-zoho-settings-form" />
	<input type="hidden" id="plug_URL" value="<?php echo esc_url(WP_CONST_ULTIMATE_CRM_CPT_PLUG_URL);?>" />
	<div class="wp-common-crm-content" style="width: 100%;float: left;">
		<table>
			<tr>
				<td><label id="inneroptions" style="font-weight:bold;">Select The CRM You use</label></td>
				<td style='padding-left:46px;'>
					<?php
					$ContactFormPluginsObj = new ContactFormPlugins();
					echo $ContactFormPluginsObj->getPluginActivationHtml();
					?>
				</td>
			</tr>
			<tr><td> <br /></tr></td>
		</table>
		<label id="inneroptions" style="font-weight:bold;">Zoho CRM Settings</label>
		<table class="settings-table">
			<tr><td></td></tr>
			<tr>
				<td style='width:160px;padding-left:40px;'>
					<label id="innertext"> Username </label><div style='float:right;'> : </div></label>
				</td>
				<td>
					<input type='text' class='smack-sugar-pro-settings-text' name='username' id='smack_host_username' value="<?php echo sanitize_text_field($config['username']) ?>"/>
				</td>
			</tr>
			<tr>
				<td style='width:160px;padding-left:40px;'>
					<label id="innertext"> Password </label><div style='float:right;'> : </div>
				</td>
				<td>
					<input type='password' class='smack-sugar-pro-settings-text' name='password' id='smack_host_access_key' value="<?php echo sanitize_text_field($config['password']) ?>"/>
				</td>
			</tr>
		<!-- TWO FACTOR AUTHENTICATION -->
                <tr>
                <td style='width:250px;padding-left:40px;'>
                <label id="innertext" style="float:left">Two factor Authentication </label><div style="float:right;">:</div>
                </td>
                <td> <input type='checkbox' class="smack-vtiger-settings cmn-toggle cmn-toggle-yes-no" name='TFA_check' id='TFA_check' <?php if(isset($config['TFA_check']) && sanitize_text_field($config['TFA_check']) == 'on') { echo "checked=checked"; }  ?> onclick="enablesmackTFA(this.id)" />

                <label class="TFA_check" for="TFA_check" id="innertext" data-on="On" data-off="Off" ></label>
 </td>
                </tr>

                <tr id="TFA_tr_show_hide"><td style="width:160px;padding-left:40px;" >
                        <label id="innertext"> <div style="float:left;">Specify Authtoken  </div> </label>
			<div style="float:right;">:</div>
			</td>
			<td>
             
                        <input type="text" id="TFA_authkey" onblur="TFA_Authkey_Save_free(this.value)" value="<?php echo get_option('TFA_zohofree_authtoken');?>" <?php if( !isset( $config['TFA_check'] ) || sanitize_text_field($config['TFA_check']) != 'on' ){ ?> disabled="disabled" <?php } ?> >
                                
                </td>
                </tr>
<!-- END TFA -->
		</table>
		<br/>
		<input type="hidden" name="posted" value="<?php echo 'posted';?>">
		<input class="smack_settings_input_text" type="hidden" id="authkey" name="authkey" value="" />
		<p class="submit">
			<input type="submit" id="save_zoho_config" value="<?php _e('Save CRM Configuration');?>" class="button-primary" onclick="document.getElementById('loading-image').style.display = 'block'" style="float: right;"/>
			<input type="button" name="save_crm_config" id="save_crm_config" value="Progressing" style="display:none; float:right;" class="button-primary" disabled="disabled" />
			<img src="<?php echo esc_url(WP_CONST_ULTIMATE_CRM_CPT_DIR); ?>images/loading-indicator.gif" id="loading-image" style="display: none; position:absolute; left:880px; padding-top:10px;">
		</p>
	</div>
</form>
<script type="text/javascript">
jQuery("#save_zoho_config").click(function(){
	jQuery('#save_zoho_config').css('display', 'none');
	jQuery('#save_crm_config').css('display', '');
});
</script>

