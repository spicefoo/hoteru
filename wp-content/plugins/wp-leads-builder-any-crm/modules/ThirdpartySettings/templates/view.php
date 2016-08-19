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
$config = get_option("wp_mail_config_free");

/* define the plugin folder url */
define('WP_PLUGIN_URL', plugin_dir_url(__FILE__));
$help_img = $siteurl."/wp-content/plugins/wp-leads-builder-any-crm/images/FormSettings.png";
$help="<img src='$help_img'>";
?>

</script>
<!--<?php
$obj = new WPCapture_includes_helper();
echo $obj->renderMenu();
?>
-->
<!--  Start -->
<form id="smack-thirdparty-settings-form" action="" method="post">
	<input type="hidden" name="smack-thirdparty-settings-form" value="smack-thirdparty-settings-form" />
	<input type="hidden" id="plug_URL" value="<?php echo esc_url(WP_CONST_ULTIMATE_CRM_CPT_PLUG_URL);?>" />
	<div class="wp-common-crm-content" style="width:100%;float: left;">
	<?php
	$ContactFormPluginsObj = new ContactFormPlugins();
	echo $ContactFormPluginsObj->getThirdpartyPLugins();
	?>
	</table>
	<table class="settings-table">
	<tr>
	<td>
	<h5 id="inneroptions" style="font-weight:bold;">Debug and Notification</h5>
</td>
</tr>
<tr>

<td  style="width:250px;padding-left:40px;">
	<label id="innertext" ><?php echo esc_html__('Which log do you need?' , 'wp-leads-builder-any-crm-pro' );?> </label>
	<div style="float:right">:</div>
</td>
<td>
<span id="circlecheck">
	<select name="smack_email" onchange="smack_email_check(this.id)">
	<option value="none" id='smack_email'
	<?php
	if(isset($config['smack_email']) && sanitize_text_field( $config['smack_email'] ) == 'none')
	{
		echo "selected=selected";
	}
	?>
	>None</option>
	<option value = "success" id= 'successemailcondition'
	<?php
	if(isset($config['smack_email']) && sanitize_text_field( $config['smack_email'] ) == 'success')
	{
		echo "selected=selected";
	}
	?>
	>Success</option>
	<option value=""   id = 'failureemailcondition' disabled="disabled"
	<?php
	if(isset($config['emailcondition']) && $config['emailcondition'] == 'failure')
	{
		echo "selected=selected";
	}
	?>
	>Failure</option>
	<option value="both" id = 'bothemailcondition' disabled="disabled"
	<?php
	if(isset($config['emailcondition']) && $config['emailcondition'] == 'both')
	{
		echo "selected=selected";
	}
	?>
	>Both</option>
	</select>
	</td>
	</tr>

	<tr>
	<td style='width:160px;padding-left:40px;'>
	<label id="innertext"><div style='float:left;'>Specify Email</div></label>
<div style='float:right;'> : </div>
</td>
<td>
<input type='text' class='smack-sugar-pro-settings-text' name='email' id='email'value="<?php if(isset($config['email'])) { echo $config['email']; } ?>" <?php if( !isset( $config['email'] ) ){ ?> disabled="disabled" <?php } ?>/>
	</td>
	</tr>
	<tr>
	<td style='width:160px;padding-left:40px;'>
	<label id="innertext"><div style='float:left;'>Enable Debug Mode</div></label>
<div style="float:right;">:</div>
</td>
<td>
<input type='checkbox' class='smack-vtiger-settings-text cmn-toggle cmn-toggle-yes-no' name='debug_mode' id='debug_mode' value="on" <?php if(isset($config['debug_mode']) && sanitize_text_field( $config['debug_mode'] ) == 'on') { echo "checked=checked"; } ?> onclick="debugmod(this.id)"/>
	<label for="debug_mode" id="innertext" data-on="On" data-off="Off"></label>
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



