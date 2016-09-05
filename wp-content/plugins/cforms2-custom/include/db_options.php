<?php

class DB_Options {
	const  TABLE_NAME = 'hotel_options';
	
	function getAll() {
		global $wpdb; //get wb db instance
		
		$query = "SELECT * FROM " . self::TABLE_NAME;
		return ( $wpdb->get_results($query) );
	}
	
	function getAllArray(){
		$data = array();
		$all = self::getAll();
		foreach($all as $r){
			$data[$r->hotel_id][$r->name] = $r->value;
		}
		return $data;
	}
	
}

global $hotel_options;

$db_options_class = new DB_Options;
$hotel_options = $db_options_class->getAllArray();
