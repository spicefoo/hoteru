<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

$siteurl = site_url();
$siteurl = esc_url( $siteurl );
$activateplugin = get_option("ActivatedPlugin");
$config = get_option("wp_{$activateplugin}_usersync");
/* define the plugin folder url */
define('WP_PLUGIN_URL', plugin_dir_url(__FILE__));
$help_img = $siteurl."/wp-content/plugins/wp-leads-builder-any-crm/images/syncuser.png";
$help="<img src='$help_img'>";
?>
<!--  Start -->
<form id="smack-thirdparty-settings-form" action="" method="post">
	<input type="hidden" name="smack-thirdparty-settings-form" value="smack-thirdparty-settings-form" />
	<input type="hidden" id="plug_URL" value="<?php echo esc_url(WP_CONST_ULTIMATE_CRM_CPT_PLUG_URL);?>" />
	<div class="wp-common-crm-content" style="width:100%;float: left;">
		<?php
		$ContactFormPluginsObj = new ContactFormPlugins();
		echo $ContactFormPluginsObj->getCustomFieldPlugins();
		?>
		<table class="settings-table">
			<tr>
				<td style='width:160px;' colspan="3">
					<label id="innertext"><div style='float:left;font-size: medium;font-weight: 500;'> Do you want to capture all WP Users as  Contacts ? </div></label>
				</td>
				<td>
					<input type='checkbox' class='smack-vtiger-settings-text cmn-toggle cmn-toggle-yes-no' name='user_capture' id='user_capture' value="on" <?php if(isset($config['user_capture']) && sanitize_text_field( $config['user_capture'] ) == 'on') { echo "checked=checked"; } ?>/>
					<label for="user_capture" id="innertext" data-on="On" data-off="Off"></label>
				</td>
			</tr>
		</table>

		<table class="settings-table">
			<tr><td></td></tr>
			<tr>
				<td>
					<div class="tooltipp">
						<?php echo $help ?>
						<span class="tooltiptext">
			<a href ="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" target="_blank"><h4> Upgrade To Pro </h4>
			</a>
			</span>
					</div>
				</td>
			</tr>
		</table>
		<table style="float:right;margin-right:30px;">
			<tr>
				<td>
					<input type="hidden" name="posted" value="<?php echo 'posted';?>">
					<p class="submit">
						<input type="submit" value="<?php _e('Save');?>" class="button-primary" onclick="document.getElementById('loading-image').style.display = 'block'" style="float: right;"/>
					</p>
				</td>
			</tr>
		</table>
	</div>
</form>
