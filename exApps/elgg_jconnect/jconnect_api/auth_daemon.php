<?php
include "api.php";

if($_GET['action']=='check_token'){
	$request_token=$_COOKIE['jconnekt_request_token'];
	
	//if jconnekt_token is there and is the same that means Joomla! is logged in and no any changes 
	//made in user activities....
	//so in the same domain we dont need to request following rest thing to check sso state
	$rtn=array('valid'=>true);
	if(isset($_COOKIE['jconnekt_token'])){
		$jconnekt_token=$_COOKIE['jconnekt_token'];
		if($jconnekt_token!=$request_token) $rtn['valid']=false;
	}
	else{
		$access_token=hash_hmac("md5",$request_token,JCFactory::getAuthKey());
		$valid=JCFactory::getJoomla()->check_token($access_token);
		
		//we check $valid's availability to fix looping page refreshes..
		if($valid && !$valid['valid']){
			$rtn['valid']=false;
		}
	}
	
	
	echo json_encode($rtn);
}
else{
	$response=json_decode(stripslashes($_GET['json']),true);
	setcookie('jconnekt_request_token',$response['request_token'],null,"/");
	
	//force headers to be sent
	echo 'force headers to be sent';
	
	if($response['state']=="online"){
		$joomla_url=JCFactory::getJConnect()->joomla_path;
		$access_token=hash_hmac("md5",$response['request_token'],JCFactory::getAuthKey());
		$res=file($joomla_url . '?option=com_jconnect&action=query&json={"access_token":"'.$access_token.'"}');
		$res=json_decode($res[0],true);
		$res=$res['data'];
		
		JCFactory::$auth->login(true,$res);
	}else {
		JCFactory::$auth->logout();
	}
}
