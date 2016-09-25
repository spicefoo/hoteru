<?php

class DB_CformsData {
	const  TABLE_NAME = 'wp_cformsdata';
	
	function get($where=NULL) {
		global $wpdb; //get wb db instance

		$where = $where ? ' WHERE ' . $where : '';
		
		$query = "SELECT * FROM " . self::TABLE_NAME . $where;
		return ( $wpdb->get_results($query) );
	}
	
	function roomRqstGen($id){
		global $wpdb; //get wb db instance
		
		$query = "SELECT wp_cformsdata.*, rawdata.field_name as field_id FROM (
						SELECT *	FROM " . self::TABLE_NAME. "
						WHERE
							sub_id = $id
							AND 
							(field_name like 'check_in' 
							OR field_name like 'check_out' 
							OR field_name like 'num_guests')
						) rawdata JOIN wp_cformsdata ON
						wp_cformsdata.field_name = rawdata.field_val
						WHERE wp_cformsdata.sub_id = $id";
		
		return ( $wpdb->get_results($query) );
	}
	
	function roomRqstList($id){
		global $wpdb; //get wb db instance
	
		$query = "SELECT wp_cformsdata.*, rawdata.field_name as field_id FROM (
						SELECT *	FROM " . self::TABLE_NAME. "
							WHERE
							sub_id = $id
							AND
							(field_name like 'room-%') 
						) rawdata JOIN wp_cformsdata ON
							wp_cformsdata.field_name = rawdata.field_val
							WHERE wp_cformsdata.sub_id = $id";
	
		return ( $wpdb->get_results($query) );
	}
	
	function hasConfirmField($id){
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE sub_id = $id AND field_name = 'confirm'" );
		return $count > 0;
	}
	
	function add($sub_id, $field_name, $field_val){
		global $wpdb;
		$query = $wpdb->prepare("INSERT INTO " . self::TABLE_NAME . "(sub_id, field_name, field_val) VALUES (%s, %s, %s)", $sub_id, $field_name, $field_val);
		return $wpdb->query($query);
	}
	
}