<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

	class thirdActions extends SkinnyActions {

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
                $data = array();
                foreach( $request as $key => $REQUESTS )
                        {
                                foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
                                {
                                        $data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
                                }
                        }
		$data['ok'] = "Captcha";
                $data['HelperObj'] = new WPCapture_includes_helper_PRO();
                $data['module'] = $data['HelperObj']->Module;
                $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
                $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
                $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
                $data['plugin_dir']= WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY_PRO;
                $data['plugins_url'] = WP_CONST_ULTIMATE_CRM_CPT_DIR_PRO;
                $data['siteurl'] = site_url();
                if( isset($data['REQUEST']["smack-{$data['activatedplugin']}-third"]) )
		{
                        $this->saveSettingArray($data);
                }
                return $data;
        }
        public function saveSettingArray($data)
        {
		
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
   //     update_option("wp_{$activatedplugin}_third", $config);
}
}

