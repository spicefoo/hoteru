<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

class ThirdpartyActions extends SkinnyActions {

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
	foreach( $request as $key => $REQUESTS )
	{
		foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
		{
			$data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
		}
	}
		$data['activatedplugin'] = 'Thirdparty';
        	$data['action'] = $data['activatedplugin']."Settings";
		$data['HelperObj'] = new WPCapture_includes_helper;
                $data['module'] = $data['HelperObj']->Module;
                $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
                $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
                $data['plugin_dir']= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY;
                $data['plugins_url'] = WP_CONST_ULTIMATE_CRM_CPT_DIR;
                $data['siteurl'] = site_url();
	if( isset($data['REQUEST']["posted"]) && ($data['REQUEST']["posted"] == "posted") )
	{
		$result = $this->saveSettings( $data );
		if($result['error'] == 1)
		{
			$data['display'] = "<p class='display_failure'> Please Fill all details </p>";
		}
		else
		{
			$data['display'] = "<p class='display_success'> Settings Successfully Saved </p>";
		}
	}
		return $data;
    }

    public function saveSettings( $data )
    {
		$request['action'] = 'ThirdParty';
		$HelperObj = $data['HelperObj'];
                $module = $HelperObj->Module;
                $moduleslug = $HelperObj->ModuleSlug;
                $activatedplugin = $HelperObj->ActivatedPlugin;
                $activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		 $fieldName = array(
		 'smack_email' => __('Smack Email'),
                  'email' => __('Email id'),
                  'debug_mode' => __('Debug Mode'),
             );

                foreach ($fieldName as $field=>$value){
                if(isset($data['REQUEST'][$field]))
                        {
                                $config[$field] = $data["REQUEST"][$field];
                        }
                        else
                        {
                                $config[$field] = "";
                        }
                }
        	update_option("wp_mail_config_free", $config);
		return $result ;
    } 
}


