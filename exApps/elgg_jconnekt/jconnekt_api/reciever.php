<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
include_once 'jconnect.php';
include_once 'lib.php';
include_once 'settings.php';


function jc_remote_login($status,$data){
	if(JCFactory::$auth)
		return JCFactory::$auth->login($status,$data);
	else{
		echo "JCAuth::login not implemented!";
	}
}

function jc_remote_logout(){
	if(JCFactory::$auth)
	JCFactory::$auth->logout();
	else{
		echo "JCAuth::logout not implemented!";
	}
}


$jc=JCFactory::getJConnect();
$jc->reciever('jc_remote_login','jc_remote_logout');


