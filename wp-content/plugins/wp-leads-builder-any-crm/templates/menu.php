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

$HelperObj = new WPCapture_includes_helper;
$nonce_key = $HelperObj->leads_create_nonce_key();
$plugin_url= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
$onAction= 'onCreate';
$siteurl= site_url();
$module = $HelperObj->Module;
$moduleslug = $HelperObj->ModuleSlug;

// Check CRM configuration done by FredrickMarks
$activeCRM = get_option('ActivatedPlugin');
$crmSettings = get_option("wp_{$activeCRM}_settings");
if($crmSettings == '' && $_REQUEST['__module'] != 'Settings') {
	$configurationURL = admin_url() . 'admin.php?page=wp-leads-builder-any-crm/index.php&__module=Settings&__action=view';
	require_once (ABSPATH . 'wp-includes/pluggable.php');
	wp_safe_redirect($configurationURL);
}
$disabledMenu = '';
if($crmSettings == '') {
	$disabledMenu = "style='pointer-events:none;opacity:0.7;'";
}

?>
<nav class='navbar navbar-default' role='navigation'>
	<div>
		<?php
		$old_url = 'admin.php?';
		$Manage_shortcode_page = add_query_arg(array('page'=>WP_CONST_ULTIMATE_CRM_CPT_SLUG .'/index.php','__module' => 'ManageShortcodes','__action' => 'view','__wpnonce' => $nonce_key), $old_url);
		$Settings_page = add_query_arg(array('page'=>WP_CONST_ULTIMATE_CRM_CPT_SLUG .'/index.php','__module' => 'Settings','__action' => 'view','__wpnonce' => $nonce_key), $old_url);
		$Thirdparty = add_query_arg( array( 'page' =>WP_CONST_ULTIMATE_CRM_CPT_SLUG.'/index.php' , '__module' => 'Thirdparty' , '__action' => 'view','__wpnonce'=>$nonce_key) , $old_url );
		$Wpsyncuser = add_query_arg( array( 'page' =>WP_CONST_ULTIMATE_CRM_CPT_SLUG.'/index.php' , '__module' => 'Wpsyncuser' , '__action' => 'view','__wpnonce'=>$nonce_key ) , $old_url );

		?>
		<ul class='nav navbar-nav'>
			<li class="<?php if( (sanitize_text_field($_REQUEST['__module']) =='ManageShortcodes' ) &&( ( sanitize_text_field($_REQUEST['__action']) =='view' ) ||( sanitize_text_field($_REQUEST['__action']) =='ManageFields'  )) ){ echo 'activate'; }else { echo 'deactivate'; }?>" <?php echo $disabledMenu; ?> >
				<a href='<?php echo esc_url($Manage_shortcode_page);?>'><span id='shortcodetab' > CRM Forms</span></a>
			</li>
			<li class="<?php if( (sanitize_text_field($_REQUEST['__module'])=='Thirdparty' ) && ( sanitize_text_field($_REQUEST['__action'])=='view' ) ){ echo 'activate'; }else{ echo 'deactivate'; }?>" <?php echo $disabledMenu; ?> >
				<a href='<?php echo esc_url( $Thirdparty ) ?>'><span id='settingstab'>Form Settings </span></a>
			</li>
			<li class="<?php if( sanitize_text_field($_REQUEST['__module']) =='Wpsyncuser' ) { echo 'activate'; }else{ echo 'deactivate'; }?>" <?php echo $disabledMenu; ?> >
				<a href='<?php echo esc_url( $Wpsyncuser ) ?>'><span id='settingstab'>WP Users Sync</span></a>
			</li>
			<li class="<?php if( (sanitize_text_field($_REQUEST['__module'])=='Settings' ) && ( sanitize_text_field($_REQUEST['__action']) =='view' ) ){ echo 'activate'; }else{ echo 'deactivate'; }?>">
				<a href='<?php echo esc_url($Settings_page) ;?>'><span id='settingstab'> CRM Configuration</span></a>
			</li>
		</ul>
	</div>
</nav>
<?php if( !is_plugin_active('wp-leads-builder-any-crm/index.php')) { ?>
	<p style="text-align:center;font-size:15px;color:red;"> Alert: Old plugin is still active, deactivate and delete it now.</p>
<?php } ?>
