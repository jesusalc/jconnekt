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

				//to tell system that user logged via JConnekt
				$_SESSION['JCONNEKT_LOGIN']=true;
				$res=login($user,false);
				
				if($data['user_group']){
					$type=($data['user_group']=='admin')?'yes':null;
					$user->admin=$type;
					$user->save();
				}
				
				// this will close the popup and
				// reload the elgg homepage in the parent window...
				//opener will be used if popup is there
				//parent will be used for when iframe is used
				global $CONFIG;
				$goto=($_GET['goto'])?$_GET['goto']:$CONFIG->url;
				echo "<script type='text/javascript'>".
			"if(opener)opener.location.href='$CONFIG->url';".
			"else if(parent)parent.window.location.href='".$goto."'".
			"</script>";
			}
		}
		catch(Exception $ex){
			register_error($ex->getMessage());
		}


		echo "<script type='text/javascript'>window.close();</script>";
		return false;
	}

	//if the user is not logout we shlould not do this..
	//it'll affect the autoactive user-sync
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

		echo "<script type='text/javascript'>".
			"if(parent)parent.window.location.href='".$CONFIG->url."'".
			"</script>";
		return true;
	}
}