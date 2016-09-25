<?php

class DB_CformsSubmissions {
	const  TABLE_NAME = 'wp_cformssubmissions';
	
	function get($id) {
		global $wpdb; //get wb db instance
		
		$query = "SELECT id, form_id, UNIX_TIMESTAMP(sub_date) as sub_date FROM " . self::TABLE_NAME . ' WHERE id = ' . $id;
		return ( $wpdb->get_row($query) );
	}
	
	function getFromTrackingID($trackingid){
		global $wpdb; //get wb db instance
		
		//clean
		if(strlen($trackingid) != 32) return NULL;
		$trackingid = $wpdb->_real_escape(strip_tags($trackingid));
		
		$query = 'SELECT * FROM (
					SELECT id, form_id, email, UNIX_TIMESTAMP(sub_date) as sub_date, md5(concat(form_id,unix_timestamp(sub_date),id)) AS trackingid 
					FROM '.self::TABLE_NAME.'
					) cformsubs 
				WHERE trackingid = "'.$trackingid.'"';
		return ( $wpdb->get_row($query) );
	}
}