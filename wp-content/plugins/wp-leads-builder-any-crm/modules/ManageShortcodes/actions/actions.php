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

class ManageShortcodesActions extends SkinnyActions {

	public function __construct()
	{
	}
	/**
	 * The actions index method
	 * @param array $request
	 * @return array
	 */
	public function executeIndex($request)
	{
		// return an array of name value pairs to send data to the template
		$data = array();
		return $data;
	}

	public function executeView($request)
	{
		// return an array of name value pairs to send data to the template
		$data = array();
		return $data;
	}

	public function getUsersListHtml()
	{

	}

	public function executeManageFields($request)
	{
		$data = array();
		foreach( $request as $key => $REQUESTS )
		{
			foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
			{
				$data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
			}
		}
		$data['HelperObj'] = new WPCapture_includes_helper;
		$data['module'] = $data["HelperObj"]->Module;
		$data['moduleslug'] = $data['HelperObj']->ModuleSlug;
		$data['activatedplugin'] = $data["HelperObj"]->ActivatedPlugin;
		$data['activatedpluginlabel'] = $data["HelperObj"]->ActivatedPluginLabel;
		$data['plugin_url']= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
		$data['onAction'] = 'onCreate';
		$data['siteurl'] = site_url();
		if(isset($data['REQUEST']['formtype']))
		{
			$data['formtype'] = $data['REQUEST']['formtype'];
		}
		else
		{
			$data['formtype'] = "post";
		}
		if(isset($data['REQUEST']['EditShortCode']) && ( $data['REQUEST']['EditShortCode'] == "yes" ) )
		{
			$data['option'] = $data['options'] = "smack_{$data['activatedplugin']}_lead_{$data['formtype']}_field_settings"; 
		}
		else
		{
			$data['option'] = $data['options'] = "smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp"; 
		}


		if(isset($data['REQUEST']['EditShortCode']) && ( $data['REQUEST']['EditShortCode'] == "yes" ) )
		{
			$data['onAction'] = 'onEditShortCode';
		}
		else
		{
			$data['onAction'] = 'onCreate';
		}

		if (isset ($_POST['formtype']) && isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) == 'formFieldsConfiguration') {
			$SaveFields = new SaveFields();
			$formFields = $SaveFields->saveFormFields( $data['option'] , $data['onAction'] , $data['REQUEST']['EditShortCode'] , $data , $data['formtype'] );
			if( isset($formFields['display']) )
			{
				echo $formFields['display'];
			}
		}

		if (isset ($_POST['formtype']) && isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) == 'formOptions') {
			$SaveFields = new SaveFields();
                        $Save_formFields = $SaveFields->save_form( $data['option'] , $data['onAction'] , $data['REQUEST']['EditShortCode'] , $data , $data['formtype'] );
			if( isset( $Save_formFields['display'] ) )
			{
				echo $Save_formFields['display'];
			}
	}
		return $data;
	}


}
class SaveFields {
	function saveFormFields( $options , $onAction , $editShortCodes ,  $request , $formtype = "post" )
	{
		$HelperObj = new WPCapture_includes_helper();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$save_field_config = array();
		if( isset($request['REQUEST']['bulkaction']))
		{
			$action = $request['REQUEST']['bulkaction'];
			$SaveFields = new SaveFields();
			switch($action)
			{
				case 'enable_field':
					$save_field_config = $SaveFields->enableField( $request );
					break;

				case 'disable_field':
					$save_field_config = $SaveFields->disableField($request);
					break;


				case 'update_order':
					$save_field_config = $SaveFields->updateOrder($request);
					break;
			}
			$i =0;
			if(!empty($save_field_config)) {	
				foreach($save_field_config as $key=>$field_value)
				{
					if( is_array($field_value) && !empty($field_value)) {
						foreach($field_value as $key=>$value)
						{
							$i++;
							if($value['publish'] == 1)
							{
								$enable_fields[$i]['label'] = $value['label'];
								$enable_fields[$i]['name'] = $value['name'];
								$enable_fields[$i]['wp_mandatory'] = $value['wp_mandatory'];
								foreach($value['type'] as $key=>$val)
								{
									if($key == 'name')
									{
										$enable_fields[$i]['type'] = $val;
									}
								}
								if( !empty( $value['type']['picklistValues'] ) ) {
									foreach($value['type']['picklistValues'] as $key=>$valuee )
									{
										$enable_fields[$i]['pickvalue'] = $value['type']['picklistValues'];
									}
								}
							}

						}
					}
				}
			}
			$WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
			$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
			$contact = get_option("thirdparty_free");
			if($contact == 'contactform')
			{
				$obj = new SaveFields();
				$obj->formatContactFields($enable_fields,$activateplugin,$formtype);
			}
		}
		$data['display'] = "<p class='display_success'> Field Settings Saved </p>";
		return $data;
	}

function save_form( $options , $onAction , $editShortCodes ,  $request , $formtype = "post" )
{
	$HelperObj = new WPCapture_includes_helper();
        $module = $HelperObj->Module;
        $moduleslug = $HelperObj->ModuleSlug;
        $activatedplugin = $HelperObj->ActivatedPlugin;
        $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;

        $save_field_config = array();

        $config_fields = get_option( "smack_{$activatedplugin}_lead_{$formtype}_field_settings" );

        if( !is_array( $config_fields ) )
        {
                $config_fields = get_option("smack_{$activatedplugin}_{$moduleslug}_fields-tmp");
        }
        $extra_fields = array( "enableurlredirection" , "redirecturl" , "errormessage" , "successmessage");
        foreach( $extra_fields as $extra_field )
        {
                if(isset( $_POST[$extra_field]))
                {
                        $config_fields[$extra_field] = $_POST[$extra_field];
                }
                else
                {
                        unset($config_fields[$extra_field]);
                }
        }
        update_option("smack_{$activatedplugin}_lead_{$formtype}_field_settings", $config_fields);
        update_option("smack_{$activatedplugin}_{$moduleslug}_fields-tmp" , $config_fields);
        $config_fields['display'] = "<p class='display_success'> Field Settings Saved </p>";
        return $config_fields;
}

	public function formatContactFields($enable_fields,$activateplugin,$formtype)
	{
		update_option( $activateplugin.'_contact_enable_fields',$enable_fields);
		if( !empty( $enable_fields ) )  {
		foreach($enable_fields as $key=>$value)
		{
			$type = $value['type'];
			$labl = $value['label'];
			$label = preg_replace('/\/| |\(|\)|\?/','_',$labl);
			$mandatory =$value['wp_mandatory'];
			$cont_array = $value['pickvalue'];
			$string ="";
			if( !empty( $cont_array ) ) {
			foreach($cont_array as $val) {

				$string .= "\"{$val['label']}\" ";
			}
			}
			$str = rtrim($string,',');
			if($mandatory == 0)
			{
				$man ="";
			}
			else
			{
				$man ="*";
			}
			switch($type)
			{
				case 'phone':
				case 'currency':
				case 'text':
				case 'integer':
				case 'string':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                                 [text".$man." ".  $label."] </p>" ;
					break;

				case 'email':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                                [email".$man." ". $label."] </p>" ;
					break;
				case 'url':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                                [url".$man." ". $label."] </p>" ;
					break;
				case 'picklist':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                                [select".$man." ". $label." " .$str."] </p>" ;
					$str ="";
					break;
				case 'boolean':
					$contact_array .= "<p>
                                                [checkbox".$man." ". $label." "."label_first "."\" $label\""."] </p>" ;
					break;
				case 'date':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                            [date".$man." ". $label." min:1950-01-01 max:2050-12-31 placeholder \"YYYY-MM-DD\"] </p>" ;
					break;
				case '':
					$contact_array .= "<p>".  $label ."".$man. "<br />
                                                 [text".$man." ".  $label."] </p>" ;
					break;

				default:

					break;

			}

		}
		}
		$contact_array .= "<p><br /> [submit "." \"Submit\""."]</p>";
		$meta = $contact_array;
		$shortcode = "[{$activateplugin}-web-form type='{$formtype}']";
		$title = "{$activateplugin}-web-form type='{$formtype}'";
		
		global $wpdb;	
		$test_query = $wpdb->prepare("select * from {$wpdb->prefix}posts where post_title=%s and post_status=%s",$shortcode , 'publish');
                $checkid = $wpdb->get_results($test_query);
		$checkid = $checkid[0]->ID;
		if(empty($checkid))
		{
			$contform = array (
					'post_title'  => $shortcode,
					'post_content'=> $contact_array,
					'post_type'   => 'wpcf7_contact_form',
					'post_status' => 'publish',
					'post_name'   => $shortcode
			);
			$id = wp_insert_post($contform);
			$content2 = "[contact-form-7 id=\"$id\" title=\"$title\"]";
			$contform2 = array (
					'post_title'  => $id,
					'post_content'=> $content2,
					'post_type'   => 'post',
					'post_status' => 'publish',
					'post_name'   => $id
			);
			wp_insert_post($contform2);
			$post_id = $id;
			$meta_key ='_form';
			$meta_value = $meta;
			update_post_meta($post_id,$meta_key,$meta_value);
			update_option($shortcode,$id);
		}
		else
		{ 
			global $wpdb;
			$aa = $wpdb->update( $wpdb->posts , array( 'post_content' => $contact_array ), array( 'ID' => $checkid )  );
			$bb = $wpdb->update( $wpdb->postmeta , array( 'meta_value' => $meta ) , array( 'post_id' => $checkid , 'meta_key' => '_form')  );
		}
	}

	public function enableField($data)
	{
		$config_fields = get_option( "smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings" );
		if( !is_array( $config_fields['fields'] ) )
		{
			$config_fields = get_option("smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp");

		}
		foreach( $config_fields as $shortcode_attributes => $fields )
		{
			if($shortcode_attributes == "fields")
			{
				foreach( $fields as $key => $field )
				{
					$save_field_config["fields"][$key] = $field;
					if( !isset($field['mandatory']) || $field['mandatory'] != 2 )
					{
						if(isset($_POST['select'.$key]))
						{
							$save_field_config['fields'][$key]['publish'] = 1;
						}

					}
					else
					{
						$save_field_config['fields'][$key]['publish'] = 1;
					}
				}
			}
			else
			{
				$save_field_config[$shortcode_attributes] = $fields;
			}

		}

		update_option("smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings", $save_field_config);
		update_option("smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp" , $save_field_config);
		return $save_field_config;
	}

	public function disableField($data)
	{
		$config_fields = get_option( "smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings" );
		if( !is_array( $config_fields ) )
		{
			$config_fields = get_option("smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp");
		}
		foreach( $config_fields as $shortcode_attributes => $fields )
		{
			if($shortcode_attributes == "fields")
			{
				foreach( $fields as $key => $field )
				{
					$save_field_config["fields"][$key] = $field;
					if( !isset($field['mandatory']) || $field['mandatory'] != 2 )
					{
						if(isset($data['REQUEST']['select'.$key]))
						{

							$save_field_config['fields'][$key]['publish'] = 0;
						}

					}
					else
					{

						$save_field_config['fields'][$key]['publish'] = 1;
					}
				}
			}
			else
			{
				$save_field_config[$shortcode_attributes] = $fields;
			}
		}
		update_option("smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings", $save_field_config);
		update_option("smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp" , $save_field_config);
		return $save_field_config;
	}


	public function updateOrder($data)
	{
		$save_field_config = get_option( "smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings" );
		for( $i = 0; $i < $data['REQUEST']['no_of_rows']; $i++ )
		{
			$REQUEST_DATA[$i] = $data['REQUEST']['position'.$i];
		}
		asort($REQUEST_DATA);
		$i = 0;
		foreach( $REQUEST_DATA as $key => $value )
		{
			$Ordered_field_config['fields'][$i] = $save_field_config['fields'][$key];
			$i++;
		}
		$save_field_config['fields'] = $Ordered_field_config['fields'];
		update_option("smack_{$data['activatedplugin']}_lead_{$data['REQUEST']['formtype']}_field_settings", $save_field_config);
		update_option("smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp" , $save_field_config);
		return $save_field_config;
	}

}

class CallManageShortcodesCrmObj extends ManageShortcodesActions {
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) )
			self::$_instance = new CallManageShortcodesCrmObj();
		return self::$_instance;
	}
}// CallSugarShortcodeCrmObj Class Ends
