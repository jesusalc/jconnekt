<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

//include openID

require_once 'aes-lib.php';

/////////////////////

class Fault{
	public $code;
	public $message;

	function __construct($c,$m){
		$this->code=$c;
		$this->message=$m;
	}
}

/**
 * Do the authentication for OpenID incomming request
 * check for IP security...
 * check for Enability...
 * @return Fault|true - true when authenticated otherwise
 * Fault Object with Error Details
 */
function loginAuthenticate($appName){
	try{
		//check for IP security
		$exAppIP=JCHelper::getMeta($appName,"IP");
		$IPValid=true;
		if($exAppIP){
			$clientIP=getip();
			if($clientIP!=$exAppIP) $IPValid=false;
		}
		//check for enability...
		$enabeled=JCHelper::isExAppEnabled($appName,JCHelper::$INCOMING);
		$valid=$enabeled && $IPValid;
	}
	catch(Exception $ex){
		return new Fault(4,$ex->getMessage());
	}

	if($valid){
		return true;
	}
	else{
		if(!$IPValid) return new Fault(32,"IP(".getip().") is not eligible...");
		if(!$enabeled) return new Fault(64,"You are not allowed to Enter by JConnect)");
	}
}

/**
 this function authenticate the user based on secKey & appName basically..
 and if enabled IP checking is there..
 and enability of the application also checked..
 @param $params - parameters of the webservice
 @return Fault|true - true when authenticated otherwise
 Fault Object with Error Details
 */
function authenticate($appName,$hmac_hash,$params){
	$valid=false;
	try{
		$exApp=new ExApp($appName);
		$hmac_hash_local=hmac_gen($params,$exApp->authKey);
		$secValid=$hmac_hash==$hmac_hash_local;
		//IP Checking....
		$exAppIP=JCHelper::getMeta($appName,"IP");
		$IPValid=true;
		if($exAppIP){
			$clientIP=getip();
			if($clientIP!=$exAppIP) $IPValid=false;
		}

		//checkExApps Enability...
		$enabeled=JCHelper::isExAppEnabled($appName,JCHelper::$INCOMING);

		$valid=$IPValid && $secValid && $enabeled;
	}
	catch(Exception $ex){
		return new Fault(4,$ex->getMessage());
	}

	if($valid){
		//add the owner to the session
		$session=JFactory::getSession();
		$session->set("JC_ACTION_OWNER",$appName);
		return true;
	}
	else{
		if(!$secValid) return new Fault(1,"Secret Key or AppName not Valid");
		if(!$IPValid) return new Fault(32,"IP is not eligible...");
		if(!$enabeled) return new Fault(64,"You are not allowed to Enter by JConnect)");
	}

}

/**
@param $params - the parameters work as the message in HMAC
@return string - hmac hash
*/
function hmac_gen($params,$key){
	$message="";
	foreach($params as $param){
		if(is_array($param) || is_object($param)){
			$message.=json_encode($param);
		}
		else{
			$message.=$param;
		}
	}
	return hash_hmac("md5",$message,$key);
}

function send_hmac($appName,$returnVal){
	$exApp=new ExApp($appName);
	$hmac_hash=hmac_gen(array($returnVal),$exApp->authKey);
	return array($hmac_hash,$returnVal);
}


function validip($ip) {

	if (!empty($ip) && ip2long($ip)!=-1) {

		$reserved_ips = array (

		array('0.0.0.0','2.255.255.255'),

		array('10.0.0.0','10.255.255.255'),

		array('127.0.0.0','127.255.255.255'),

		array('169.254.0.0','169.254.255.255'),

		array('172.16.0.0','172.31.255.255'),

		array('192.0.2.0','192.0.2.255'),

		array('192.168.0.0','192.168.255.255'),

		array('255.255.255.0','255.255.255.255')

		);


		foreach ($reserved_ips as $r) {

			$min = ip2long($r[0]);

			$max = ip2long($r[1]);

			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;

		}

		return true;

	} else {

		return false;

	}
}

function getip() {

	if (validip($_SERVER["HTTP_CLIENT_IP"])) {

		return $_SERVER["HTTP_CLIENT_IP"];

	}

	foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {

		if (validip(trim($ip))) {

			return $ip;

		}

	}

	if (validip($_SERVER["HTTP_X_FORWARDED"])) {

		return $_SERVER["HTTP_X_FORWARDED"];

	} elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) {

		return $_SERVER["HTTP_FORWARDED_FOR"];

	} elseif (validip($_SERVER["HTTP_FORWARDED"])) {

		return $_SERVER["HTTP_FORWARDED"];

	} elseif (validip($_SERVER["HTTP_X_FORWARDED"])) {

		return $_SERVER["HTTP_X_FORWARDED"];

	} else {

		return $_SERVER["REMOTE_ADDR"];

	}
}


