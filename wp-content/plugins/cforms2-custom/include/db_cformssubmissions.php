<?php

class DB_CformsSubmissions {
	const  TABLE_NAME = 'wp_cformssubmissions';
	
	function get($id) {
		global $wpdb; //get wb db instance
		
		$query = "SELECT * FROM " . self::TABLE_NAME . ' WHERE id = ' . $id;
		return ( $wpdb->get_row($query) );
	}
}