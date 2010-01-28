<?php
/**
 * This handles all the authentication stuff
 */
include "api.php";


//used in auto active single sign on
if($_GET['action']=='check_token'){
	$request_token=JCFactory::getJConnect()->getLocalToken();

	//if jconnekt_token is there and is the same that means Joomla! is logged in and no any changes
	//made in user activities....
	//so in the same domain we dont need to request following rest thing to check sso state
	$rtn=array('valid'=>true);
	if(false){
		$jconnekt_token=$_COOKIE['jconnekt_token'];
		if($jconnekt_token!=$request_token) $rtn['valid']=false;
	}
	else{
		$access_token=hash_hmac("md5",$request_token,JCFactory::getAuthKey());
		$valid=JCFactory::getJoomla()->check_token($access_token);

		//we check $valid's availability to fix looping page refreshes..
		if(!$valid['valid']){
			//if($valid && !$valid['valid']){
			$rtn['valid']=false;
		}
	}

	echo json_encode($rtn);
}
else if($_GET['action']=='logout_return'){
	header("Location: ".JCFactory::$app_url);
	echo "header sent";
}
else{
	//used to login by JConnekt Login or via SSO Login
	$response=json_decode(stripslashes($_GET['json']),true);
	JCFactory::getJConnect()->createLocalToken($response['request_token']);
	if($response['state']=="online"){
		$joomla_url=JCFactory::getJConnect()->joomla_path;
		$access_token=hash_hmac("md5",$response['request_token'],JCFactory::getAuthKey());

		$res=JCFactory::getJoomla()->query($access_token);

		//if queried user is not banned!
		if(!(isset($res['ban']) && $res['ban']==true)){
			//indicate that JConnekt session is started
			//indicate jconnekt session is started
			setcookie("JCONNEKT_SESSION",true,null,"/");
			JCFactory::$auth->login(true,$res);
		}

	}else {

		JCFactory::$auth->logout();
	}
}
