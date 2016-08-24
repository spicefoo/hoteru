<?php

class DB_Rooms {
	const  TABLE_NAME = 'hotel_rooms';
	
	function getAll() {
		global $wpdb; //get wb db instance
		
		$query = "SELECT * FROM " . self::TABLE_NAME;
		return ( $wpdb->get_results($query) );
	}
}