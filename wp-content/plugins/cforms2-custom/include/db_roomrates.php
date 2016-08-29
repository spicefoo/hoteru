<?php

class DB_RoomRates {
	const  TABLE_NAME = 'hotel_roomrates';
	
	function getAll() {
		global $wpdb; //get wb db instance
		
		$query = "SELECT * FROM " . self::TABLE_NAME . " ORDER BY hotel_rooms_id";
		return ( $wpdb->get_results($query) );
	}
	
	function getLowestRates(){
		global $wpdb; //get wb db instance
		
		$query = "SELECT hotel_roomrates.hotel_rooms_id as room_id, MIN(hotel_roomrates.rate) as rate
				from
				hotel_roomrates 
				group by hotel_roomrates.hotel_rooms_id";
		$rates = $wpdb->get_results($query);
		
		$rates_array = array();
		foreach($rates as $r){
			$rates_array[$r->room_id] = $r->rate;
		}
		return $rates_array;
	}
	
	function getHighestRates(){
		global $wpdb; //get wb db instance
	
		$query = "SELECT hotel_roomrates.hotel_rooms_id as room_id, MAX(hotel_roomrates.rate) as rate
				from
				hotel_roomrates
				group by hotel_roomrates.hotel_rooms_id";
		$rates = $wpdb->get_results($query);
	
		$rates_array = array();
		foreach($rates as $r){
			$rates_array[$r->room_id] = $r->rate;
		}
		return $rates_array;
	}
	
	function getRatesArray(){
		$rates = array();
		$rooms = $this->getAll();
		foreach ($rooms as $r){
			$rates[$r->hotel_rooms_id][$r->capacity] = array($r->rate, $r->allow_extra);
		}
		return $rates;
	}
}