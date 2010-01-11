<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

class JCElggUserSync extends JCUserSync{

	public function getUserCount(){
		global $wpdb; 
		$user_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users;"));
	  	return $user_count;
	}

	public function getUserGroups(){
	
		return array('administrator','editor','author','contributor','subscriber');
	}

	public function getUserDetails($chunkSize,$chunkNo){
		
	}
	
	public function getUsers($usernameList){
		
	}
}