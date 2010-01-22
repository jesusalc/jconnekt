<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

include_once 'helper.php';

class JCElggAuth extends JCAuth{

	public function login($status,$data){
		try{
			if($status){
				$user=get_user_by_username($data['username']);
				if(!$user){
					JCElggHelper::addUser($data['username'],$data['email'],md5(rand()));
					$user=get_user_by_username($data['username']);
				}else{
					JCElggHelper::updateUser($data['username'],$data['email']);
					$user->email=$data['email'];
				}

				//to prevent call's joomla's updateUser in the login-hook..
				$_SESSION['JC_LOGIN']=true;
				$res=login($user,false);
				unset($_SESSION['JC_LOGIN']);
				if($data['user_group']){
					$type=($data['user_group']=='admin')?'yes':null;
					$user->admin=$type;
					$user->save();
				}
				//to tell elgg in the next time that I've logged in...
				setcookie("jc_elgg",$_SESSION['code'],0,"/");
				setcookie("jc_elgg_j_session",$data['session_id'],0,"/");
				var_dump($data['session_id']);

				// this will close the popup and
				// reload the elgg homepage in the parent window...
				global $CONFIG;
				echo "<script type='text/javascript'>".
			"opener.location.href='$CONFIG->url';".
			"</script>";
			}
		}
		catch(Exception $ex){
			register_error($ex->getMessage());
		}


		echo "<script type='text/javascript'>window.close();</script>";
		return false;
	}

	public function logout(){
		global $CONFIG;

		if (isset($_SESSION['user'])) {
			$_SESSION['user']->code = "";
			$_SESSION['user']->save();
		}

		unset($_SESSION['username']);
		unset($_SESSION['name']);
		unset($_SESSION['code']);
		unset($_SESSION['guid']);
		unset($_SESSION['id']);
		unset($_SESSION['user']);

		setcookie("elggperm", "", (time()-(86400 * 30)),"/");

		session_destroy();
		setcookie("jc_elgg_j_session", "", (time()-(86400 * 30)),"/");

		header("Location: $CONFIG->url");
		return true;
	}
}