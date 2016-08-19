<?php
/******************************
 * filename:    modules/wpfreshsalesfreeSettings/actions/actions.php
 * description:
 */

class WpfreshsalesSettingsActions extends SkinnyActions {

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

	public function saveSettings($sett_array)
        {
                $fieldNames = array(
                        'username' => __('Username'),
                        'password' => __('Password'),
                        'domain_url' => __('Domain URL'),
                );

                foreach ($fieldNames as $field=>$value){
                        if(isset($sett_array[$field]))
                        {
                                $config[$field] = trim($sett_array[$field]);
                        }
                }
                $FunctionsObj = new Functions( );
                $testlogin_result = $FunctionsObj->testLogin( $sett_array['domain_url'] , $sett_array['username'] , $sett_array['password'] );
		$check_is_valid_login = json_decode($testlogin_result);
                if(isset($check_is_valid_login->login) && $check_is_valid_login->login == 'success')
                {
                        $successresult = "<p  class='display_success' style='color: green;'> Settings Saved </p>";
                        $result['error'] = 0;
                        $result['success'] = $successresult;
                        $WPCapture_includes_helper_Obj = new WPCapture_includes_helper();
                        $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
			$config['auth_token'] = $check_is_valid_login->auth_token;
                        update_option("wp_{$activateplugin}_settings", $config);
                }
                else
                {
                        $vtigercrmerror = "<p  class='display_failure' style='color:red;' >Please Verify Username and Password.</p>";
                        $result['error'] = 1;
                        $result['errormsg'] = $vtigercrmerror ;
                        $result['success'] = 0;
                }
                return $result;
        }	
}


