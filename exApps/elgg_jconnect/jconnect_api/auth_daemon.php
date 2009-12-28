<?php
include "api.php";

if($_GET['action']=='check_token'){
	$request_token=$_COOKIE['jconnekt_request_token'];
	$access_token=hash_hmac("md5",$request_token,JCFactory::getAuthKey());
	$rtn=array('valid'=>true);
	$valid=JCFactory::getJoomla()->check_token($access_token);
	
	if($valid && !$valid['valid']){
		$rtn['valid']=false;
	}
	
	echo json_encode($rtn);
}
else{
	$response=json_decode(stripslashes($_GET['json']),true);
	setcookie('jconnekt_request_token',$response['request_token'],null,"/");
	
	//force headers to be sent
	echo 'force headers to be sent';
	
	if($response['state']=="online"){
		$access_token=hash_hmac("md5",$response['request_token'],JCFactory::getAuthKey());
		$res=file('http://localhost/jconnekt/joomla/?option=com_jconnect&action=query&json={"access_token":"'.$access_token.'"}');
		$res=json_decode($res[0],true);
		$res=$res['data'];
		
		JCFactory::$auth->login(true,$res);
	}else {
		JCFactory::$auth->logout();
	}
}
