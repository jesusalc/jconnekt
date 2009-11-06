<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

include_once('xmlrpc/xmlrpc.inc.php');
include_once('xmlrpc/xmlrpcs.inc.php');

include_once('lib.php');
include_once('settings.php');

global $xmlrpcString,$xmlrpcInt,$xmlrpcArray,$xmlrpcStruct,$xmlrpcBoolean;
$methods=array(
			"jc.deleteUser"=>array(
				'function' => 'deleteUser',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcString)),
				'docstring'=>'delete the given user'
			),
			"jc.getUserDetails"=>array(
				'function' => 'getUserDetails',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcInt,$xmlrpcInt)),
				'docstring'=>'return the usernames by the chunk size and chunk number'
			),
			"jc.getUsers"=>array(
				'function' => 'getUsers',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcArray)),
				'docstring'=>'return users as array given in the user array\n' .
							'\nif userarray is null return all users.. '
			),
			"jc.getUserCount"=>array(
				'function' => 'getUserCount',
				'signature' => array(array($xmlrpcString,$xmlrpcString)),
				'docstring'=>'get the # of users'
			),
			"jc.getPublicView"=>array(
				'function' => 'getPublicView',
				'signature' => array(array($xmlrpcString,$xmlrpcString)),
				'docstring'=>'get the html data for activity module(public)'
			),
			"jc.getPrivateView"=>array(
				'function' => 'getPrivateView',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcString)),
				'docstring'=>'get the html data for activity module(private)'
			),
			"jc.loadSysInfo"=>array(
				'function' => 'loadSysInfo',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcStruct)),
				'docstring'=>'send jconenct system info to the exApp'
			),
			"jc.getUserGroups"=>array(
				'function' => 'getUserGroups',
				'signature' => array(array($xmlrpcString,$xmlrpcString)),
				'docstring'=>'get usergroups used in elgg'
			)
		);

$xmlrpcServer = new xmlrpc_server($methods, false);

$xmlrpcServer->functions_parameters_type = 'phpvals';

$xmlrpcServer->service();

function deleteUser($secKey,$username){
	if(!checkValidity($secKey,array($username))){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(125,"Secret key invalid!")));
	}
	else if(!$username){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(126,"username should be valid")));
	}
	$res=false;
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->deleteUser($username);
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}
	
	return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac(true)));
		
}


/**
 * 
 * Returns the # of users in ExApp
 * @return user count
 */
function getUserCount($secKey){
	if(!checkValidity($secKey,array())){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(125,"Secret key invalid!")));
	}
	
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->getUserCount();
		return new xmlrpcresp(php_xmlrpc_encode(
			send_hmac((int)$res->cnt)));
	}catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}
	
	
}

/** (this is used for user-sync)
	Here usernames and email are return as the blocks, size  by @see $chunksize and
	the $chunkNo th block
	eg:- if we have 1500 users @see $chunksize 500, $chunkNo 2 will send user from 501-1000..
	
	@return a 2d array containing usernames and email for users..
		eg: rtn[$lc][0]='username';
			rtn[$lc][1]='email';
 */
function getUserDetails($secKey,$chunkSize,$chunkNo){
	if(!checkValidity($secKey,array($chunkSize,$chunkNo))){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(125,"Secret key invalid!")));
	}
	else{
		try{
			if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
			$res=JCFactory::$userSync->getUserDetails($chunkSize,$chunkNo);
			return new xmlrpcresp(php_xmlrpc_encode(
				send_hmac($res)));
		}
		catch(Exception $ex){
			$errNo=($ex->getCode())?$ex->getCode():127;
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
		}
	}	
}

/**
	this function is used in bulk-sync where if we need resolve conflicts..
	@param $usernameList array of usernames..
	@return user-details of given usernames.. in template shown below..
			eg: rtn[$lc][0]='username';
					rtn[$lc][1]='email';
 */
function getUsers($secKey,$usernameList){
	if(!checkValidity($secKey,array($usernameList))){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(125,"Secret key invalid!")));
	}
		
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$users=JCFactory::$userSync->getUsers($usernameList);		
		return new xmlrpcresp(php_xmlrpc_encode(
		      send_hmac($users)));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}
}





/**
	This is the function probabaly called by the JConnect to inform exApp about jconnect details...
	like host,port,path it's xmlrpc server stayes..
	
	@param $secKey -security key
	@parama $meta - array containing jconnect info...
				possible keys are..
				JOOMLA_URL,JC_APPNAME
 */
function loadSysInfo($secKey,$meta){
	if(!checkValidity($secKey,array($meta))){
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(125,"Secret key invalid!")));
	}
	
	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		JCFactory::$misc->loadSysInfo($meta);	
		return new xmlrpcresp(php_xmlrpc_encode(
		      send_hmac(true)));	
	}catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}
} 

/**
 * The returning html data will be displayed for public view
 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
 * use above strToIntArray like function
	@return - html data(public) in a intArray
 */
function getPublicView($secKey){
	if(!checkValidity($secKey,array())){
		return new xmlrpcresp(php_xmlrpc_encode(
		new Fault(125,"Secret key invalid!")));
	}

	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		$html=JCFactory::$misc->getPublicView();
		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac(strToIntArray($html))));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}

}

/**
 * The returning html data will be displayed for public view
 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
 * use above strToIntArray like function
	@return - html data(public) in a intArray
 */
function getPrivateView($secKey,$username){
if(!checkValidity($secKey,array($username))){
		return new xmlrpcresp(php_xmlrpc_encode(
		new Fault(125,"Secret key invalid!")));
	}

	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		$html=JCFactory::$misc->getPrivateView($username);
		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac(strToIntArray($html))));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	} 
}

function strToIntArray($string){
	$intArray=array();
	for($lc=0;$lc<strlen($string);$lc++){
		array_push($intArray,ord(substr($string,$lc,1)));
	}
	return $intArray;
}


function getUserGroups($secKey){
	if(!checkValidity($secKey,array())){
		return new xmlrpcresp(php_xmlrpc_encode(
		new Fault(125,"Secret key invalid!")));
	}

	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->getUserGroups();
		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac($res)));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($errNo,$ex->getMessage())));
	}
}


//HELPERS>>>>>>>>>>

class Fault{
	public $code;
	public $message;

	function __construct($c,$m){
		$this->code=$c;
		$this->message=$m;
	}
}

/**
	@param $hmac_hash - hmac hash for the values in the parameters
	@param $params - array of parameters
 */
function checkValidity($hmac_hash,$params){
	$hmac_hash_local=hmac_gen($params,JCFactory::getAuthKey());
	if($hmac_hash!=$hmac_hash_local){
		return false;
	}
	else{
		return true;
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

function send_hmac($returnVal){
	$hmac_hash=hmac_gen(array($returnVal),JCFactory::getAuthKey());
	return array($hmac_hash,$returnVal);
}
?>