<?php
/**
 * @author		Arunoda Susiripala
 * @package		jconnect
 * @subpackage	elgg
 * @copyright	Arunoda Susiripala
 * @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 */
class JCElggHelper{

	public static function addUser($username,$email,$password_clr){
		global $CONFIG;

		$id=register_user($username,$password_clr,$username,$email);
		var_dump($id);
		$user=get_user($id);
		$user->admin_created = true;
		$user->created_by_guid = 2;

		return $id;
	}

	public static function updateUser($username,$email,$password=null){
		global $CONFIG;

		$user=get_user_by_username($username);
		$user->email=$email;

		$db_password=generate_user_password($user, $password);
		if(isset($password) && $user->password!=$db_password){
			$user->salt = generate_random_cleartext_password(); // Reset the salt
			$user->password = generate_user_password($user, $password);
			if (!$user->save())
			register_error(elgg_echo('user:password:fail'));
		}
			
	}

	public static function isAdmin($userID){
		global $CONFIG;
		$user=get_user($userID);
		return ($user->admin)?true:false;
	}
}

