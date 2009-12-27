<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
include_once 'jconnect_api/api.php';


function jconnect_init(){ 

	register_page_handler('jconnect','jconnect_page_handler');
	jc_set_sessoins();
	//jc_login_check();
	extend_view('css','config/css');
	
	//add the jconnect login form to the login area...
	extend_view('account/forms/login','config/login');
	
	//the ajax version of login check...
	extend_view('page_elements/footer','config/check');
	
	extend_view('metatags','config/metatags');

	return true;
}

//this checks the login state of the JOOMLA_SESSION in elgg session and do the
//necessary actions.
// this is depricated but need to use coz ajax redirect pretty much slow..
// (slow in user processing)
function jc_login_check(){
	global $CONFIG;
	
	$jSession=$_SESSION['JOOMLA_SESSION'];
	
	if($jSession){
		//user currently logged into joomla in JOOMLA_SESSION sessionid value
		$user=$CONFIG->joomla->getUserBySession("$jSession");
		$elggUser=$_SESSION['user']->username;
		if($user==null){
			header('Location: action/logout');
		}
		else if($user!=$elggUser){
			header('Location: pg/jconnect/login');
		}
	}
}

function jconnect_page_handler($page){
	global $CONFIG;
	if(strlen($page[0])>0) require_once($CONFIG->pluginspath . "elgg_jconnect/$page[0].php");
	else require_once($CONFIG->pluginspath . "elgg_jconnect/jconnect_api/server.php");
}

function jconnect_pagesetup()
{
	if (get_context() == 'admin' && isadminloggedin()) {
		global $CONFIG;
		add_submenu_item(elgg_echo('JConnect Config'), $CONFIG->wwwroot . 'pg/jconnect/config');
	}
} 


function jc_set_sessoins(){
	try{
		if (empty($_SESSION['guid'])) {
			if (isset($_COOKIE['jc_elgg'])) {
				$code = $_COOKIE['jc_elgg'];
				$code = md5($code);
				unset($_SESSION['guid']);//$_SESSION['guid'] = 0;
				unset($_SESSION['id']);//$_SESSION['id'] = 0;
				if ($user = get_user_by_code($code)) {
					$_SESSION['user'] = $user;
					$_SESSION['id'] = $user->getGUID();
					$_SESSION['guid'] = $_SESSION['id'];
					$_SESSION['code'] = $_COOKIE['jc_elgg'];
				}
				
				setcookie('jc_elgg',0,time()-3600,"/");
			}
		}
	}
	catch(Exception $x){
		var_dump($x);
	}

	return true;
}


// Initialise JConnect....
register_elgg_event_handler('init','system','jconnect_init');

//add the configuration page to the admin menu..
register_elgg_event_handler('pagesetup','system','jconnect_pagesetup');


//-------------------------------------------------------
//plugin actions for sending information back to Joomla...
//-------------------------------------------------------

//login using PAM

function jconnect_logout($event,$obj_type,$user){
	//clear the token
	setcookie('jconnekt_request_token',0,null,"/");
	unset($_COOKIE['jconnekt_request_token']);
	
	//logging out is done using a logout function provided by 
	global $CONFIG;
	JCFactory::getJConnect()->logout();	
}

function jconnect_create_user($event,$obj_type,$user){
	try{
		global $CONFIG;
		$username = get_input('username');
		$password = get_input('password');
		$email = get_input('email');
		 
		$user=get_user_by_username($username);
		$group=(JCElggHelper::isAdmin($user->guid))?'admin':'user';
		
		$rtn=JCFactory::getJoomla()->createUser($username,$email,$password,$group);
		return $rtn;
	}
	catch(Exception $ex){
		if($ex->getCode()==64) return true;
		register_error($ex->getMessage());
	}
	return true;
}

function jconnect_delete_user($event,$obj_type,$user){
	try{
		global $CONFIG;
		//var_dump($user->username,$user->email,md5(rand()));
		$rtn=JCFactory::getJoomla()->deleteUser($user->username);
		return $rtn;
	}
	catch(Exception $ex){
		if($ex->getCode()==64) return true;
		register_error($ex->getMessage());
	}

	return true;
}


//all the information get from the $_POST via get_input method
//password is get from the getPassword() it checks the password for validity
function jconnect_update_user($hook,$obj_type,$rtn,$param){
	try{
		global $CONFIG;
		$guid=get_input('guid',0);
		if(!$guid) return;
		$user=get_user($guid);
		$group=(JCElggHelper::isAdmin($guid))?'admin':'user';
		if(!$user) return;
		$password=getPassword();
		JCFactory::getJoomla()->updateUser($user->username,$user->email,$password,$group);
	}
	catch(Exception $ex){
		if($ex->getCode()==64) return true;
		register_error($ex->getMessage());
	}
}

//this function is used to send the password to the Joomla after a user-sync...
function jconnect_login($event,$obj_type,$user){
	
	try{
		if(!$_SESSION['JC_LOGIN']){
			JCFactory::getJoomla()->updateUser(
			$user->username,$user->email,$_POST['password']);
		}
		
	}
	catch(Exception $ex){
		if($ex->getCode()==64) return true;
		register_error($ex->getMessage());
	} 
}

register_elgg_event_handler('logout','user','jconnect_logout');
register_elgg_event_handler('login','user','jconnect_login');
register_elgg_event_handler('create','user','jconnect_create_user');
register_elgg_event_handler('delete','user','jconnect_delete_user');

//used to update user..
register_plugin_hook("usersettings:save","user","jconnect_update_user",510);


//HELPER METHODS....
//-----------------------------

/*
 * this will get the password from $_POST and validate it return
 *
 * @return string|null(if password is not validate)
 *
 */
function getPassword(){
	$password = get_input('password');
	$password2 = get_input('password2');
	$user_id = get_input('guid');
	$user = "";

	if (!$user_id)
	$user = $_SESSION['user'];
	else
	$user = get_entity($user_id);

	if (($user) && ($password!=""))
	{
		if (strlen($password)>=4)
		{
			if ($password == $password2)
			{
				return $password;
			}
		}
	}

	return null;
}
?>