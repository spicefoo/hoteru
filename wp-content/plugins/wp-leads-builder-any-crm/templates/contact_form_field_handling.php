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

require_once('SmackContactFormGenerator.php');
    add_action('wpcf7_submit','contact_forms_example');

function contact_forms_example()
{
	global $wpdb,$HelperObj;
        $post_id = sanitize_text_field($_POST['_wpcf7']);
	$all_fields = $_POST;

        foreach($all_fields as $key=>$value)    {
        if(preg_match('/^_wp/',$key))
        unset($all_fields[$key]);
        }
	
	$activateplugin = $HelperObj->ActivatedPlugin;
	$enable_fields = get_option( $activateplugin.'_contact_enable_fields');
	foreach($enable_fields as $key=>$value)
	{
		$cont_labl = $value['label'];
		$cont_label = preg_replace('/\/| |\(|\)|\?/','_',$cont_labl);
		$cont_label = rtrim($cont_label,':');
		$cont_label = rtrim($cont_label,'_');
		$cont_name  = $value['name'];
		foreach($all_fields as $field_id=>$user_value)
		{	
			$field_id = rtrim($field_id,':');
			$field_id = rtrim($field_id,'_');
			if($field_id == $cont_label)
			{
				$ArraytoApi[$value['name']] = $user_value;
			}
		}
	}
	$formtype = get_option('form_type');
	$shortcode = "[{$activateplugin}-web-form type='{$formtype}']";
	$code['name'] = $shortcode;
        $ArraytoApi['moduleName'] = 'Leads';
        $ArraytoApi['formnumber'] = $post_id;
        $ArraytoApi['submit'] = 'Submit';
        foreach($ArraytoApi as $key=>$value)
                {
                if($key=='')
                {
                $noe = $key;
                }
                        if(is_array($ArraytoApi[$key]))
                        {
                                switch($activateplugin)
                                {
                                        case 'wptigerfree':
                                        $ArraytoApi[$key] ='1';
                                        break;

                                        case 'wpsugarfree':
                                        $ArraytoApi[$key] ='on';
                                        break;

                                        case 'wpzohofree':
                                        $ArraytoApi[$key] ='true';
                                        break;

					case 'wpsalesforcefree':
                                        $ArraytoApi[$key] ='on';
                                        break;
	
					case 'wpfreshsalesfree':
                                        $ArraytoApi[$key] ='1';
                                        break;

                                }
                        }
                }
        unset($ArraytoApi[$noe]);
	if( $activateplugin == 'wpfreshsalesfree' )
	{
		$freshsales_option = get_option( "smack_wpfreshsalesfree_lead_fields-tmp" );
		foreach( $freshsales_option['fields'] as $fs_key => $fs_option )
                {
                        foreach( $ArraytoApi as $field_name => $posted_val ) {
                        if( $fs_option['type']['name'] == 'picklist' && $fs_option['fieldname'] == $field_name )
                        {
                                        foreach( $fs_option['type']['picklistValues'] as $pick_key => $pick_val )
                                        {
                                                if( $pick_val['label'] == $posted_val )
                                                {
                                                        $ArraytoApi[$field_name] = $pick_val['id'];
                                                }
                                        }

                        }
                        if( $fs_option['type']['name'] == 'boolean' && $fs_option['fieldname'] == $field_name && $posted_val == "" )
                        {
                                        $ArraytoApi[$field_name] = '0';
                        }
                        }
                }
	}
        global $_POST;
                $_POST = array();
                $_POST = $ArraytoApi;
	smackContactFormGenerator($code);
        callCurlFREE('post');
        return true;
}

?>
