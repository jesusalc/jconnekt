<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

include_once('lib.php');
include_once('settings.php');

class Endpoint{
	//runs the endpoint
	static function run(){
		$registeredActions=array(
			'deleteUser','getUserCount','getUserDetails',
			'getUsers','loadSysInfo','getPublicView',
			'getPrivateView','getUserGroups'
		);
		$action=mysql_escape_string($_GET['action']);
		
		if($action && in_array($action,$registeredActions)){
			$data=json_decode(stripslashes($_GET['json']),true);
			try{
				$action($data);
			}
			catch(Exception $ex){
				Endpoint::returnException(256,$ex->getMessage());
			}
		}
		else{
			Endpoint::returnException(2,'Invalid Action');
		}
	}
	
	//return result to the endpoint
	/**
	 * @param $result - result as an array
	 */
	static function returnResult($result){
		$res=array('result'=>0,'data'=>$result);
		echo json_encode($res);
		exit(0);
	}
	
	//return an exception to the endpoint
	static function returnException($no,$message){
		$res=array('result'=>1,'data'=>array('no'=>$no,'message'=>$message));
		echo json_encode($res);
		exit(0);
	}
	
}

function deleteUser($data){
	$secKey=$data['hmacHash'];
	$username=$data['username'];
	if(!checkValidity($secKey,array($username),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}
	else if(!$username){
		Endpoint::returnException(126,"username should be valid");
	}
	$res=false;
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->deleteUser($username);
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
	}
	
	Endpoint::returnResult(true);
		
}


/**
 * 
 * Returns the # of users in ExApp
 * @return user count
 */
function getUserCount($data){
	$secKey=$data['hmacHash'];
	if(!checkValidity($secKey,array(),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}
	
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->getUserCount();
		Endpoint::returnResult((int)$res);
	}catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
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
function getUserDetails($data){
	$secKey=$data['hmacHash'];
	$chunkSize=$data['chunkSize'];
	$chunkNo=$data['chunkNo'];
	
	if(!checkValidity($secKey,array($chunkSize,$chunkNo),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}
	else{
		try{
			if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
			$res=JCFactory::$userSync->getUserDetails($chunkSize,$chunkNo);
			Endpoint::returnResult($res);
		}
		catch(Exception $ex){
			$errNo=($ex->getCode())?$ex->getCode():127;
			Endpoint::returnException($errNo,$ex->getMessage());
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
function getUsers($data){
	$secKey=$data['hmacHash'];
	$usernameList=$data['usernameList'];
	if(!checkValidity($secKey,array($usernameList),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}
		
	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$users=JCFactory::$userSync->getUsers($usernameList);		
		Endpoint::returnResult($users);
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
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
function loadSysInfo($data){
	$secKey=$data['hmacHash'];
	$meta=$data['meta'];
	if(!checkValidity($secKey,array($meta),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}
	
	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		JCFactory::$misc->loadSysInfo($meta);	
		Endpoint::returnResult(true);	
	}catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
	}
} 

/**
 * The returning html data will be displayed for public view
 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
 * use above strToIntArray like function
	@return - html data(public) in url encoded
 */
function getPublicView($data){
	$secKey=$data['hmacHash'];
	if(!checkValidity($secKey,array(),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}

	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		$html=JCFactory::$misc->getPublicView();
		Endpoint::returnResult(urlencode($html));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
	}

}

/**
 * The returning html data will be displayed for public view
 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
 * use above strToIntArray like function
	@return - html data(public) in url encoded
 */
function getPrivateView($data){
	$secKey=$data['hmacHash'];
	$username=$data['username'];
if(!checkValidity($secKey,array($username),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}

	try{
		if(!JCFactory::$misc) throw new Exception("Not Implemented",1024);
		$html=JCFactory::$misc->getPrivateView($username);
		Endpoint::returnResult(urlencode($html));
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
	} 
}


function getUserGroups($data){
	$secKey=$data['hmacHash'];
	if(!checkValidity($secKey,array(),$data['salt'])){
		Endpoint::returnException(125,"Secret key invalid!");
	}

	try{
		if(!JCFactory::$userSync) throw new Exception("Not Implemented",1024);
		$res=JCFactory::$userSync->getUserGroups();
		Endpoint::returnResult($res);
	}
	catch(Exception $ex){
		$errNo=($ex->getCode())?$ex->getCode():127;
		Endpoint::returnException($errNo,$ex->getMessage());
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
function checkValidity($hmac_hash,$params,$salt){
	$hmac_hash_local=hmac_gen($params,JCFactory::getAuthKey(),$salt);
	
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
function hmac_gen($params,$key,$salt){
	$message="";
	foreach($params as $param){
		if(is_array($param) || is_object($param)){
			$message.=json_encode($param);
		}
		else{
			$message.=$param;
		}
	}
	return hash_hmac("md5",$message.$salt,$key);
}

function send_hmac($returnVal){
	$hmac_hash=hmac_gen(array($returnVal),JCFactory::getAuthKey());
	return array($hmac_hash,$returnVal);
}

//run REST/JSON endpoint..
if(mysql_escape_string($_GET['action'])){
	Endpoint::run();
}
else{
	
}

?>