<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

	if(isset($skinnyData['display']))
	{
		echo $skinnyData['display'];
	}
	include( WP_CONST_ULTIMATE_CRM_CPT_DIRECTORY . 'modules/'.$skinnyData['action'].'/templates/view.php');
?>

