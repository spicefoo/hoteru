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

	if(!session_id()) {
		session_start();
	}
	if(isset($_SESSION['generated_forms']))
	{
		unset($_SESSION['generated_forms']);
	}
global $HelperObj;
$HelperObj = new WPCapture_includes_helper;
$activatedplugin = $HelperObj->ActivatedPlugin;
$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;

add_filter('widget_text', 'do_shortcode');
add_shortcode( $activatedplugin."-web-form" ,'smackContactFormGenerator');
global $plugin_dir;
$plugin_dir = WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
$plugin_url = WP_CONST_ULTIMATE_CRM_CPT_DIR;

$onAction = 'onCreate';
$siteurl = site_url();

global $config;
global $post;

$config = get_option("wp_{$activatedplugin}_settings");

$post = array();
global $module_options, $module , $isWidget, $assignedto, $check_duplicate, $update_record;

function smackContactFormGenerator($attr){
	global $HelperObj;
	global $module_options, $module, $isWidget, $assignedto, $check_duplicate, $update_record;
	$module_options = 'Leads';
	$shortcodes = get_option("smack_{$HelperObj->ActivatedPlugin}_lead_{$attr['type']}_field_settings");
	if(is_array($shortcodes))
	{
		$config_fields = $shortcodes['fields'];
		$module = $shortcodes['module'];
		$isWidget = $shortcodes['isWidget'];
		$assignedto = $shortcodes['assignedto'];
		$module_options = $module;
		$check_duplicate = $shortcodes['check_duplicate'];
		if(isset($shortcodes['update_record']))
		{
			$update_record = $shortcodes['update_record'];
		}
	}

	if($attr['type'] == "post")
	{
		return normalContactForm( $module, $config_fields, $module_options , "post" );
	}
	else
	{
		return widgetContactForm($module, $config_fields, $module_options , "widget" );
	}
}

function callCurlFREE( $formtype )
{
	global $HelperObj;
	global $plugin_dir;
	global $plugin_url;
	global $config;
	global $post;
	global $module_options, $module , $isWidget, $assignedto, $check_duplicate, $update_record;
	$plugin_dir=WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
	
	$globalvariables = Array( 'plugin_dir' => $plugin_dir , 'plugin_url' => $plugin_url , 'post' => $post , 'module_options' => $module_options , 'module' => $module , 'isWidget' => $isWidget , 'assignedto' => $assignedto , 'check_duplicate' => $check_duplicate , 'update_record' => $update_record , 'HelperObj' => $HelperObj );

	$CapturingProcessClass = new CapturingProcessClass();
	$data = $CapturingProcessClass->CaptureFormFields($globalvariables); 
							
	$smacklog='';

        $HelperObj = new WPCapture_includes_helper();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = $HelperObj->ActivatedPlugin;
        $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;

	$config_fields = get_option("smack_{$activatedplugin}_lead_{$formtype}_field_settings");

/* new code for check duplicates before posting 26-11-2012 ENDS */
	if(isset($data) && $data) {

		if(isset($_REQUEST['submitcontactform']))
		{
			$submit_form = sanitize_text_field( $_REQUEST['formnumber'] );
			$submitcontactform = "smackLogMsg{$submit_form}";
		}
		if(isset($_REQUEST['submitcontactformwidget']))
		{
			$submit_form_widget = sanitize_text_field( $_REQUEST['formnumber'] );
			$submitcontactform = "widgetSmackLogMsg{$submit_form_widget}";
		}
		$successfulAttemptsOption = get_option( "wp-{$activatedplugin}-contact-{$formtype}-form-attempts" );
			$total=0;
			$success=0;               
		if(!isset($successfulAttemptsOption['total']) && ($successfulAttemptsOption['success'] ))
		{
			$successfulAttemptsOption['total'] = 0;
                        $successfulAttemptsOption['success'] = 0;
		}
		else{       
		 	$total= $successfulAttemptsOption['total'];
                        $success= $successfulAttemptsOption['success'];
	      	}
		$total++;

	$contenttype = "\n";
	foreach($config_fields['fields'] as $key => $value)
	{
		$config_field_label[$value['name']] = $value['display_label'];
	}

	foreach( $post as $key => $value )
	{
		if(($key != 'formnumber') && ($key != 'submitcontactformwidget') && ($key != 'moduleName') && ($key != "submit" ) && ( $key != "") &&($key != 'submitcontactform'))
		if(isset($config_field_label[$key]))
		{
			$contenttype.= "{$config_field_label[$key]} : $value"."\n";
		}
		else
		{
			$contenttype.= "$key : $value"."\n";
		}
	}

	$config = get_option("wp_{$activatedplugin}_settings");

	if(preg_match("/{$config_fields['module']} entry is added./",$data)) {

			$success++;
		
			$successfulAttemptsOption['total'] = $total;
			$successfulAttemptsOption['success'] = $success;
			$sendmail = mailsend( $config,$activatedplugin,$formtype,$plugin_url, "Success" , $contenttype );
			update_option( "wp-{$activatedplugin}-contact-{$formtype}-form-attempts",$successfulAttemptsOption );
			if( isset($config_fields['enableurlredirection']) && ($config_fields['enableurlredirection'] == "on") && isset($config_fields['redirecturl']) && ( $config_fields['redirecturl'] !== "" ) && is_numeric($config_fields['redirecturl']) )
			{
				wp_redirect(get_permalink($config_fields['redirecturl']));
				exit;
			}
        
			$smacklog.="<script>";
			if(isset( $config_fields['successmessage'] ) && ($config_fields['successmessage'] != "") )
			{
                        	$smacklog.="document.getElementById('{$submitcontactform}').innerHTML=\"<p class='smack_logmsg' style='color:green;'>{$config_fields['successmessage']}</p>\"";
			}
			else
			{
                        	$smacklog.="document.getElementById('{$submitcontactform}').innerHTML=\"<p class='smack_logmsg' style='color:green;'>Thank you for submitting</p>\"";

			}
	                $smacklog.="</script>";

			return $smacklog;
	}
	else
	{
		$sendmail =  mailsend( $config,$activatedplugin,$formtype,$plugin_url, "Failure" ,$contenttype );
		update_option( "wp-{$activatedplugin}-contact-{$formtype}-form-attempts",$successfulAttemptsOption );
	//	return("failed");
		$smacklog.="<script>";
		if( isset( $config_fields['errormessage'] ) && ($config_fields['errormessage'] != "") )
		{
			$smacklog.="document.getElementById('{$submitcontactform}').innerHTML=\"<p class='smack_logmsg' style='color:red;'>{$config_fields['errormessage']}</p>\"";
		}
		else
		{
			$smacklog.="document.getElementById('{$submitcontactform}').innerHTML=\"<p class='smack_logmsg' style='color:red;'>Submitting Failed</p>\"";
		}
		$smacklog.="</script>";
		$successfulAttemptsOption['total'] = $total;
		$successfulAttemptsOption['success'] = $success;		

		return $smacklog;
	}
	}	
}

function normalContactForm($module, $config_fields, $module_options , $formtype)
{
	global $plugin_dir;
	global $plugin_url;
	$siteurl=site_url();
	global $config;
	global $post;
	$script='';
	$post=$_POST;
	if( !isset( $_SESSION["generated_forms"] ) )
	{
		$_SESSION["generated_forms"] = 1;
	}
	else
	{
		$_SESSION["generated_forms"]++;
	}

	$activatedplugin = get_option( 'ActivatedPlugin' );
if(isset($_POST['submitcontactform']) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) )
	{
		$count_error=0;
		for($i=0; $i<count($config_fields); $i++)
		{
			if(array_key_exists($config_fields[$i]['name'],$_POST))
			{
				if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "" )
				{
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'integer' && !preg_match('/^[\d]*$/', $_POST[$config_fields[$i]['name']]) && ($_POST[$config_fields[$i]['name']] != ""))
				{
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'double'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', $_POST[$config_fields[$i]['name']]) && ($_POST[$config_fields[$i]['name']] != ""))
				{
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'currency'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))
				{
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',sanitize_text_field($_POST[$config_fields[$i]['name']]))) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))
				{
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/',sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != "")))
				{
					if(sanitize_text_field($_POST[$config_fields[$i]['name']]) == "")
					{
					}
					else
					{
					$count_error++;
					}
					
				}
				elseif($config_fields[$i]['type']['name'] == 'multipicklist' )
				{
					$concat = "";
					for( $index=0; $index<count(sanitize_text_field($_POST[$config_fields[$i]['name']])); $index++)
					{
					$concat.=$_POST[$config_fields[$i]['name']][$index]." |##| ";

					}
					$concat=substr($concat,0,-6);
					$post[$config_fields[$i]['name']]=$concat;

				}
				elseif($config_fields[$i]['type']['name'] == 'phone' && !preg_match('/^[2-9]{1}[0-9]{2}-[0-9]{3}-[0-9]{4}$/', sanitize_text_field($_POST[$config_fields[$i]['name']])))
				{
					
				}
			}
		}
	}

	$content = "<form id='contactform{$_SESSION["generated_forms"]}' name='contactform{$_SESSION["generated_forms"]}' method='post'>";
	$content.= "<table>";
	$content.= "<div id='smackLogMsg{$_SESSION["generated_forms"]}'></div>";
	$content1="";
	$count_selected=0;
		for($i=0; $i<count($config_fields);$i++) {
			$content2 = "";
			$fieldtype = $config_fields[$i]['type']['name'];
			if( $config_fields[$i]['publish']==1)
			{
				if($config_fields[$i]['wp_mandatory']==1)
				{
					$content1.="<tr><td>".$config_fields[$i]['display_label']." *</td>";
					$M=' mandatory';
				}
				else
				{
					$content1.="<tr><td>".$config_fields[$i]['display_label']."</td>";
					$M='';
				}
				if($fieldtype == "string")
				{
					$content1.="<td><input type='text' class='string{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0) 
						$content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
					else
						$content1 .= '';
$content1 .= "'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactform']) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field( $_POST[$config_fields[$i]['name']] ) == "" )
	{
		$content1 .="This field is mandatory";
	}
}
	$content1 .="</span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == "text")
				{
					$content1.="<td><textarea class='textarea{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'></textarea><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></td></tr>";
					$count_selected++;
				}

                                elseif($fieldtype == 'radioenum')
                                {
                                        $content1 .= "<td>";
                                        $picklist_count = count($config_fields[$i]['type']['picklistValues']);
                                        for($j=0 ; $j<$picklist_count ; $j++)
                                        {
                                                $content2.="<input type='radio' name='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['label']}'>{$config_fields[$i]['type']['picklistValues'][$j]['value']}";
                                        }
                                        $content1.=$content2;
                                        $content1.="<script>document.getElementById('{$config_fields[$i]['name']}').value='".sanitize_text_field($_POST[$config_fields[$i]['name']])."'</script>";
                                        $content1 .= "<br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span>";
                                        $content1 .= "</td>";
                                        $count_selected++;
                                }
				elseif($fieldtype == 'multipicklist')
				{
					$picklist_count = count($config_fields[$i]['type']['picklistValues']);
					$content1.="<td><select class='multipicklist{$M}' name='{$config_fields[$i]['name']}[]' multiple='multiple' id='{$module_options}_{$config_fields[$i]['name']}' >";
					for($j=0 ; $j<$picklist_count ; $j++)
					{
						$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
					}
					$content1.=$content2;
					$content1.="</select><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == 'picklist')
				{
					$picklist_count = count($config_fields[$i]['type']['picklistValues']);
					$content1.="<td><select class='picklist{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'  value='";
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';

$content1.="'>";
					for($j=0 ; $j<$picklist_count ; $j++)
					{
						if( $activatedplugin == 'wpfreshsalesfree' )
						{
							$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['id']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
						}
						else
						{
							$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
						}
					}
					$content1.=$content2;
					$content1.="</select><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></tr>";

					$count_selected++;
				}
				elseif($fieldtype == 'integer')
				{
					$content1.="<td><input type='text' class='integer{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';
$content1.="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "" )
{
	$content1 .="This field is mandatory";
}
elseif( isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'integer' && !preg_match('/^[\d]*$/', sanitize_text_field($_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))
{
	$content1 .="This field is integer";
}
	$content1 .= "</span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == 'double')
				{
					$content1.="<td><input type='text' class='double{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='".sanitize_text_field($_POST[$config_fields[$i]['name']])."'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == 'currency')
				{
					$content1.="<td><input type='text' class='currency{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';
$content1.="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field( $_POST[$config_fields[$i]['name']] ) == "" )
{
	$content1 .="This field is mandatory";
}
elseif(  isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'currency'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', sanitize_text_field( $_POST[$config_fields[$i]['name']]))&& (sanitize_text_field($_POST[$config_fields[$i]['name']]) != ""))
{
	$content1 .="This field is integer";
}
	$content1 .= "</span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == 'email')
				{
					$content1.="<td><input type='text' class='email{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field( $_POST['formnumber'] ) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';

$content1.="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";

if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field( $_POST[$config_fields[$i]['name']] ) == "" )
{
	$content1 .="This field is mandatory";
}
elseif( isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', sanitize_text_field( $_POST[$config_fields[$i]['name']])) && (sanitize_text_field($_POST[$config_fields[$i]['name']] != ""))))
{	
	$content1 .="Invalid Email";
}

	$content1 .="</span></td></tr>";
					$count_selected++;
				}
				elseif($fieldtype == 'date')
				{
?>
				<script> 
					jQuery(function() {
						jQuery( "#<?php echo $module_options.'_'.$config_fields[$i]['name'].'_'.$_SESSION['generated_forms'];?>" ).datepicker({
							dateFormat: "yy-mm-dd",
							changeMonth: true,
							changeYear: true,
							showOn: "button",
							buttonImage: "<?php echo $plugin_url; ?>/images/calendar.gif",
							buttonImageOnly: true,
							yearRange: '1900:2050'
						});
					});
				</script>
<?php
					$content1.='<td><input type="text" class="date'.$M.'" name='.$config_fields[$i]['name'].' id="'.$module_options.'_'.$config_fields[$i]['name'].'_'.$_SESSION['generated_forms'].'" value="';
					if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field( $_POST['formnumber'] ) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';

$content1.='" readonly="readonly" /> <span class="smack_field_error" id="'.$config_fields[$i]['name'].'error'.$_SESSION["generated_forms"].'"></span></td></tr>';

					$count_selected++;
				}
				elseif($fieldtype == 'boolean')
				{
					$content1.='<td><input type="checkbox'.$M.'" class="boolean" name='.$config_fields[$i]['name'].' id="'.$module_options.'_'.$config_fields[$i]['name'].'" value="on"/><br/><span class="smack_field_error" id="'.$config_fields[$i]['name'].'error'.$_SESSION["generated_forms"].'"></span></td></tr>';
					$count_selected++;
				}
				elseif($fieldtype == 'url')
				{
					$content1.="<td><input type='text' class='url{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && ( sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';
$content1.="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactform']) && ( sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field($_POST[$config_fields[$i]['name']]) == "" )
	{
		$content1 .="This field is mandatory";
	}
	elseif(  isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/',sanitize_text_field($_POST[$config_fields[$i]['name']])) && ( sanitize_text_field($_POST[$config_fields[$i]['name']]) != "")))
	{
		$content1 .="Invalid URL";
	}
}
		$content1 .="</span></td></tr>";	
				$count_selected++;
				}
				elseif($fieldtype == 'phone')
				{
					$content1.="<td><input type='text' class='phone{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= sanitize_text_field($_POST[$config_fields[$i]['name']]);
                                        else
                                                $content1 .= '';
$content1.="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactform']) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && sanitize_text_field( $_POST[$config_fields[$i]['name']] ) == "" )
	{
		$content1 .="This field is mandatory";
	}
}
		$content1 .="</span></td></tr>";
				$count_selected++;
				}
				else
				{
					$content1.="<td><input type='text' class='others{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='".sanitize_text_field($_POST[$config_fields[$i]['name']])."'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></td></tr>";
					$count_selected++;
				}
			}
		}

	if($count_selected==0)
	{
		$content.="<h3>You have selected no fields</h3>";
	}
	else
	{
		$content.=$content1;
	}
	$content.="<tr><td></td><td>";
	if($count_selected==0)
	{
	}
	else
	{
		$content.="<p class='contact-form-comment'>
		<p class='form-submit'>";
		$content.="<input type='hidden' name='formnumber' value='{$_SESSION['generated_forms']}'>";
		$content.="<input type='hidden' name='submitcontactform' value='submitcontactform{$_SESSION['generated_forms']}'/>";
		$content.='<input type="submit" value="Submit" id="submit" name="submit"></p>';
	}
	$content.="</td></tr></table>";
	$content.="<input type='hidden' value='".$module."' name='moduleName' /></p></form>";
	if(isset($_POST['submitcontactform']) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) )
	{
		if($count_error==0)
		{
			$content.= callCurlFREE( $formtype );
		}
	}
	return $content;
}

function widgetContactForm($module, $config_fields, $module_options , $formtype)
{
global $plugin_dir;
global $plugin_url;
$siteurl=site_url();
global $config;
//global $action;
global $post;
$post=array();
$post=$_POST;

        if( !isset( $_SESSION["generated_forms"] ) )
        {

                $_SESSION["generated_forms"] = 1;
        }
        else
        {
                $_SESSION["generated_forms"]++;
        }
$activatedplugin = get_option( 'ActivatedPlugin' );
if(isset($_POST['submitcontactformwidget']) && ($_POST['submitcontactformwidget'] == 'submitwidgetcontactform'.$_SESSION['generated_forms'])  && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
	
{
		$content = "";
		$script = "";
		$count_error=0;
		for($i=0; $i<count($config_fields); $i++)
		{
			if(array_key_exists($config_fields[$i]['name'],$_POST))
			{
				if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
				{
					$script="<script> oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}']; oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML= '<div style=\'color:red;\'>This field is mandatory</div>'; </script>";
					$content .= $script;
					$script="";
					$count_error++;
				}
				elseif(  isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'integer' && !preg_match('/^[\d]*$/', $_POST[$config_fields[$i]['name']]))
				{
					$script="<script>oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}'];oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML='enter valid ".$config_fields[$i]['name']."'; </script>";
					$content .= $script;
					$script="";
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'double'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', $_POST[$config_fields[$i]['name']]) )
				{
					$script="<script>oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}'];oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML='enter valid ".$config_fields[$i]['name']."';</script>";
					$content .= $script;
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'currency'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', $_POST[$config_fields[$i]['name']]) )
				{
					$script="<script>oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}'];oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML='enter valid ".$config_fields[$i]['name']."';</script>";
					$content .= $script;
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^([a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4}))?$/',$_POST[$config_fields[$i]['name']])))
				{
					$script="<script>oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}'];oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML='<font color=\'red\'>Enter valid ".$config_fields[$i]['name']."</font>';</script>";
					$content .= $script;
					$count_error++;
				}
				elseif($config_fields[$i]['type']['name'] == 'phone' && !preg_match('/^[2-9]{1}[0-9]{2}-[0-9]{3}-[0-9]{4}$/', $_POST[$config_fields[$i]['name']]))
				{
					
				}
				elseif($config_fields[$i]['type']['name'] == 'multipicklist' )
				{
$concat ="";
for( $index=0; $index<count($_POST[$config_fields[$i]['name']]); $index++)
{
$concat.=$_POST[$config_fields[$i]['name']][$index]." |##| ";

}
$concat=substr($concat,0,-6);
$post[$config_fields[$i]['name']]=$concat;

				}
				elseif($config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/',$_POST[$config_fields[$i]['name']])))
				{
					if($_POST[$config_fields[$i]['name']] == "")
					{
					}
					else
					{
						$script="<script>oFormObject = document.forms['contactform{$_SESSION["generated_forms"]}'];oformElement = oFormObject.elements['".$config_fields[$i]['name']."']; document.getElementById('".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}').innerHTML='enter valid ".$config_fields[$i]['name']."'</script>";
					$count_error++;
					}
					$content .= $script;
				}
			}
		}
}
	$content = "<form id='contactform{$_SESSION["generated_forms"]}' name='contactform{$_SESSION["generated_forms"]}' method='post'>";
	$content.= "<div id='widgetSmackLogMsg{$_SESSION["generated_forms"]}'></div>";
	$content1="";
	$count_selected=0;
		for($i=0; $i<count($config_fields);$i++) {
			if(isset( $_POST[$config_fields[$i]['name']] ) && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
			{
				$field_value = $_POST[$config_fields[$i]['name']]; 
			}
			else
			{
				$field_value = "";
			}
			$content2 = "";
			$fieldtype = $config_fields[$i]['type']['name'];
			if($config_fields[$i]['publish']==1)
			{
				if($config_fields[$i]['wp_mandatory']==1)
				{
					$content1.=$config_fields[$i]['display_label']." *";
					$M=' mandatory';
				}
				else
				{
					$content1.="<label for='".$config_fields[$i]['display_label']."'>".$config_fields[$i]['display_label']."</label>";
					$M='';
				}
				if($fieldtype == "string")
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='string{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
					if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0) 
					$content1 .= $field_value;
					else
						$content1 .= '';
$content1 .= "'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactformwidget']) && ($_POST['submitcontactformwidget'] == 'submitwidgetcontactform'.$_SESSION['generated_forms'])  && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}
}
		$content1 .="</span></div>";
					$count_selected++;
				}
				elseif($fieldtype == "text")
				{
					$content1.='<div class="div_texbox">'."<textarea class='textarea{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'></textarea><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></div>";
					$count_selected++;
				}
                                elseif($fieldtype == 'radioenum')
                                {
                                        $content1 .= '<div class="div_texbox">';
                                        $picklist_count = count($config_leads_fields[$i]['type']['picklistValues']);
                                        for($j=0 ; $j<$picklist_count ; $j++)
                                        {
                                                $content2.="<input type='radio' name='{$config_leads_fields[$i]['name']}' value='{$config_leads_fields[$i]['type']['picklistValues'][$j]['label']}'>{$config_leads_fields[$i]['type']['picklistValues'][$j]['value']}<br/>";
                                        }
                                        $content1.=$content2;
                                        $content1 .= "<br/><span class='smack-field_error' id='".$config_leads_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span>";
                                        $content1 .= "</div>";
                                        $count_selected++;
                                }
				elseif($fieldtype == 'multipicklist')
				{
					$picklist_count = count($config_fields[$i]['type']['picklistValues']);
					$content1.='<div class="div_texbox">'."<select class='multipicklist{$M}' name='{$config_fields[$i]['name']}[]' multiple='multiple' id='{$module_options}_{$config_fields[$i]['name']}'  value='{$field_value}'>";
					for($j=0 ; $j<$picklist_count ; $j++)
					{
						$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
					}
					$content1.=$content2;
					$content1.="</select><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></div>";
					$count_selected++;
				}
				elseif($fieldtype == 'picklist')
				{
					$picklist_count = count($config_fields[$i]['type']['picklistValues']);
					$content1.='<div class="div_texbox">'."<select class='picklist{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}'  value='";
					if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';

$content1.="'>";
					for($j=0 ; $j<$picklist_count ; $j++)
					{
						if( $activatedplugin == 'wpfreshsalesfree' )
						{
							$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['id']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
						}
						else
						{
							$content2.="<option id='{$config_fields[$i]['name']}' value='{$config_fields[$i]['type']['picklistValues'][$j]['value']}'>{$config_fields[$i]['type']['picklistValues'][$j]['label']}</option>";
						}
					}
					$content1.=$content2;
					$content1.="</select><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></div>";
					$count_selected++;
				}
				elseif($fieldtype == 'integer')
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='integer{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';
$content1 .="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}

elseif( isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'integer' && !preg_match('/^[\d]*$/', $_POST[$config_fields[$i]['name']]))
{
	$content1 .="This field is integer";
}
	$content1 .="</span></div>";
					$count_selected++;
				}
				elseif($fieldtype == 'double')
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='double{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='{$field_value}'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></div>";
					$count_selected++;
				}
				elseif($fieldtype == 'currency')	
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='currency{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';
$content1 .="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}
elseif( isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'currency'  && !preg_match('/^([\d]{1,8}.?[\d]{1,2})?$/', $_POST[$config_fields[$i]['name']]) )
{
	$content1 .="This field is integer";
}
		$content1 .="</span></div>";
					$count_selected++;
				}
				elseif($fieldtype == 'email')
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='email{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && (sanitize_text_field($_POST['formnumber']) == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';
$content1 .="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}
elseif(  isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'email' && (!preg_match('/^([a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4}))?$/',$_POST[$config_fields[$i]['name']]) && ($_POST[$config_fields[$i]['name']] != "") ))
{	
	$content1 .="Invalid Email";
}
	$content1 .="</span></div>";		
			$count_selected++;
				}
				elseif($fieldtype == 'date')
				{
?>
				<script> 
					jQuery(function() {
						jQuery( "#<?php echo $module_options.'_'.$config_fields[$i]['name'].'_'.$_SESSION['generated_forms'];?>" ).datepicker({
							dateFormat: "yy-mm-dd",
							changeMonth: true,
							changeYear: true,
							showOn: "button",
							buttonImage: "<?php echo $plugin_url; ?>/images/calendar.gif",
							buttonImageOnly: true
						});
					});
				</script>
<?php
					$content1.='<div class="div_texbox">'.'<input type="text" class="date'.$M.' smack_widget_textbox_date_picker" name='.$config_fields[$i]['name'].' id="'.$module_options.'_'.$config_fields[$i]['name'].'_'.$_SESSION['generated_forms'].'" value="';
					 if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $_POST[$config_fields[$i]['name']];
                                        else
                                                $content1 .= '';
$content1 .='" readonly="readonly" /> <span class="smack_field_error" id="'.$config_fields[$i]['name'].'error'.$_SESSION["generated_forms"].'"></span></div>';
					$count_selected++;
				}
				elseif($fieldtype == 'boolean')
				{
					$content1.='<div class="div_texbox">'.'<input type="checkbox'.$M.'" class="boolean" name='.$config_fields[$i]['name'].' id="'.$module_options.'_'.$config_fields[$i]['name'].'" value="on"/><br/><span class="smack_field_error" id="'.$config_fields[$i]['name'].'error'.$_SESSION["generated_forms"].'"></span><div>';
					$count_selected++;
				}
				elseif($fieldtype == 'url')
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='url{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';
$content1 .="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactformwidget']) && ($_POST['submitcontactformwidget'] == 'submitwidgetcontactform'.$_SESSION['generated_forms'])  && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}

	elseif( isset($_POST[$config_fields[$i]['name']]) && $config_fields[$i]['type']['name'] == 'url' && (!preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-=#]+\.([a-zA-Z0-9\.\/\?\:@\-=#])*/',$_POST[$config_fields[$i]['name']]))  && ($_POST[$config_fields[$i]['name']] != "") )
	{
		$content1 .="Invalid URL";
	}
}
		$content1 .="</span></div>";
				$count_selected++;
				}
				elseif($fieldtype == 'phone')
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='phone{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='";
				if(isset($_POST[$config_fields[$i]['name']]) && ($_POST['formnumber'] == $_SESSION['generated_forms']) && $count_error!=0)
                                                $content1 .= $field_value;
                                        else
                                                $content1 .= '';
$content1 .="'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'>";
if(isset($_POST['submitcontactformwidget']) && ($_POST['submitcontactformwidget'] == 'submitwidgetcontactform'.$_SESSION['generated_forms'])  && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
{
	if($config_fields[$i]['wp_mandatory'] == 1 && $_POST[$config_fields[$i]['name']] == "")
	{
		$content1 .="This field is mandatory";
	}
}
		$content1 .="</span></div>";
					$count_selected++;
				}
				else
				{
					$content1.='<div class="div_texbox">'."<input class='smack_widget_textbox' type='text' class='others{$M}' name='{$config_fields[$i]['name']}' id='{$module_options}_{$config_fields[$i]['name']}' value='{$field_value}'/><br/><span class='smack_field_error' id='".$config_fields[$i]['name']."error{$_SESSION["generated_forms"]}'></span></div>";
					$count_selected++;
				}
			}
		}

	if($count_selected==0)
	{
		$content.="<h3>You have selected no fields</h3>";
	}
	else
	{
		$content.=$content1;
	}
	if($count_selected==0)
	{
	}
	else
	{
                $content.="<p class='contact-form-comment'>
		<p class='form-submit'>";
		$content.="<input type='hidden' name='formnumber' value='{$_SESSION['generated_forms']}'>";
		$content.="<input type='hidden' name='submitcontactformwidget' value='submitwidgetcontactform{$_SESSION["generated_forms"]}'/>";
		$content.='<input class="smack_widget_buttons" type="submit" value="Submit" id="submit" name="submit"></p>';
	}

	if(isset($_POST['submitcontactformwidget']) && ($_POST['submitcontactformwidget'] == 'submitwidgetcontactform'.$_SESSION['generated_forms'])  && ($_POST['formnumber'] == $_SESSION['generated_forms']) )
	{

		if($count_error==0)
		{
			$content .= callCurlFREE( $formtype );
		}
	}
	$content.="<input type='hidden' value='".$module."' name='moduleName' /></p></form>";
	return $content;
}

function getip()
                {
                $ip = $_SERVER['REMOTE_ADDR'];
                return $ip;
                }

function mailsend( $config,$activatedplugin,$formtype,$plugin_url,$data,$contenttype )
{
	$mail_config = get_option( "wp_mail_config_free" );
        $to = "{$mail_config['email']}";
        $subject = 'Form Details';
        $message = "Shortcode : " . "[$activatedplugin-web-form type='$formtype']" ."\n" . "URL: " . $plugin_url ."\n" . "Type:".$formtype ."\n". "Form Status:".$data . "\n" . "FormFields and Values:"."\n".$contenttype ."\n"."User IP:".getip();
	$current_user = wp_get_current_user();
       	$admin_email = $current_user->user_email;
        $headers = "From: Administrator <$admin_email>" . "\r\n\\";
        if(isset($mail_config['smack_email']) && $mail_config['smack_email'] == 'success')
        {
	      wp_mail( $to, $subject, $message,$headers );
        }
}
?>
