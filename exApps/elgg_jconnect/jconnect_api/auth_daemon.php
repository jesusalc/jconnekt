<?php
include "api.php";
$response=json_decode(stripslashes($_GET['json']),true);
var_dump($response);
if($response['state']=="online"){
	$access_token=hash_hmac("md5",$response['request_token'],JCFactory::getAuthKey());
	$res=file('http://localhost/jconnekt/joomla/?option=com_jconnect&action=query&json={"access_token":"'.$access_token.'"}');
	$res=json_decode($res[0],true);
	$res=$res['data'];
	//var_dump($res);
	/*$data=array();
	$data['username']="admin";$data['email']="axx@aa.com";
	$data['user_group']="admin";*/
	JCFactory::$auth->login(true,$res);
}