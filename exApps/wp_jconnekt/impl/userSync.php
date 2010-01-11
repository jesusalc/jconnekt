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
		global $wpdb;
		$myrows = $wpdb->get_results( 
			"SELECT user_login FROM $wpdb->users ORDER BY user_login ASC LIMIT ".(($chunkNo-1)*$chunkSize).",".$chunkSize."");
		
		$users=array();
		foreach($myrows as $user){
			$val=new WP_User($user->user_login);
		
			$userGroup=null;
			if($val->roles[0]){
				$userGroup=$val->roles[0];
			}
			else if($val->roles[1]){
				$userGroup=$val->roles[1];
			}
			else{
				$userGroup='subscriber';
			}
			
			$arr=array($val->user_login,$val->user_email,$userGroup);
			array_push($users,$arr);
		}
		
		return $users;
	}
	
	public function getUsers($usernameList){
		global $wpdb;
		$userList=array();
		$myrows = $wpdb->get_results("SELECT user_login FROM $wpdb->users WHERE user_login " .
			"IN ('".implode("','",$usernameList)."')");
		
			
		$users=array();
		foreach($myrows as $user){
			$val=new WP_User($user->user_login);
		
			$userGroup=null;
			if($val->roles[0]){
				$userGroup=$val->roles[0];
			}
			else if($val->roles[1]){
				$userGroup=$val->roles[1];
			}
			else{
				$userGroup='subscriber';
			}
			
			$arr=array($val->user_login,$val->user_email,$userGroup);
			array_push($users,$arr);
		}
		
		return $users;
	}
}