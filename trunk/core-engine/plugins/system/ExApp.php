<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/


/**
 * This is the actual class contains the Connector API for JConnect ExApps
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class ExApp{
	public $appName;
	public $secretKey='99027k';
	public $host='localhost';
	public $path='/gsoc/JConnect/exApps/fake/server.php';
	public $port=80;
	public $authKey;
	public $cryptKey;

	private $client;
	private $cookies ;

	public function __construct($appname){
		$this->loadExApp($appname);
	}

	private function loadExApp($appName){
		$appID=0;
		if(is_int($appName)) $appID=$appName;
		else if(is_string($appName)) $appID=JCHelper::getAppID($appName);

		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__jc_exApps WHERE appID='.(int)$appID;
		$db->setQuery($query);
		$app=$db->loadObject();

		if(!$app) throw new Exception("ExApp name($appName) is invalid");

		$this->appName=$app->appName;
		$this->secretKey=$app->secretKey;
		$this->host=$app->host;
		$this->path=$app->path;
		$this->port=$app->port;
		
		$keys=explode("::",$app->secretKey);
		
		$this->authKey=$keys[0];
		$this->cryptKey=$keys[1];
	}


	public function deleteUser($username){

		return $this->callMethod("deleteUser",array('username'=>$username));
	}

	public function getUserDetails($chunkSize,$chunkNo){
		return $this->callMethod("getUserDetails",array(
			'chunkSize'=>$chunkSize,
			'chunkNo'=>$chunkNo));
	}

	public function getUsers($usernameList=array()){
		return $this->callMethod("getUsers",array(
			'usernameList'=>$usernameList));
	}


	public function  getUserCount(){
		return $this->callMethod("getUserCount",array());
	}

	/**
	 * The returning html data will be displayed for public view
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 @return - html data(public) in a intArray
	 */
	public function  getPublicView(){
		return $this->callMethod("getPublicView",array());
	}

	/**
	 * The returning html data will be displayed when particular user-logged in..
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 @return - html data(private) in a intArray
	 */
	public function  getPrivateView($username){
		return $this->callMethod("getPrivateView",array('username'=>$username));
	}

	/**
	 This is the function probabaly called by the JConnect to inform exApp about jconnect details...
	 like host,port,path it's xmlrpc server stayes..

	 @parama $meta - array containing jconnect info...
	 possible keys are..
	 JC_PATH,JC_HOST,JC_PORT,APPNAME,PARAMS
	 PARAMS -contains array of parameters for the path
	 */
	public function loadSysInfo($meta){
		return $this->callMethod('loadSysInfo',array('meta'=>$meta));
	}

	/**
		get user group from the ExApp
	 */
	public function getUserGroups(){
		return $this->callMethod('getUserGroups',array());
	}

	private function callMethod($action,$paramArray){
		//generating hash value and send-it...
		$salt=substr(md5(rand()),0,25);
		$hmac_hash=$this->hmac_gen($paramArray,$salt);
		
		$paramArray['hmacHash']=$hmac_hash;
		$paramArray['salt']=$salt;
		$endpoint="http://{$this->host}:{$this->port}{$this->path}";
		//$call=$endpoint."?&action=$action&json=".json_encode($paramArray);
		$res=$this->sendRequest($endpoint,$action,json_encode($paramArray));
		
		$res=json_decode($res,true);
		
		if(isset($res) && $res['result']==0){
			return $res['data'];
		}
		else if((isset($res))){
			throw new Exception($res['data']['message'],$res['data']['no']);
		} 
		else{
			throw new Exception("Connection Details Invalid!",2);
		}
	}

	/**
		This will do the actual request (with the channel)
		@param $endpoint request endpoint url (with ? )
		@param $action action to calling
		@param $json json data..
		
	 */
	private function sendRequest($endpoint,$action,$json){
		$res;
		if(function_exists('curl_init2')){
			$ch = curl_init("$endpoint");
			$params=urlencode("action").'='.urlencode($action)."&";
			$params.=urlencode("json").'='.urlencode($json);
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $params);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        $res = stripcslashes(curl_exec($ch));       
	        curl_close($ch);
	        
		}
		else{
			@$res=file("{$endpoint}?&action=$action&json=$json");
			@$res=stripslashes(implode("\n",$res));
			
		}
		
		return $res;
	}
	/**
	@param $params - the parameters work as the message in HMAC
	@return string - hmac hash
	*/
	private function hmac_gen($params,$salt){
		$message="";
		foreach($params as $param){
			if(is_array($param) || is_object($param)){
				$message.=json_encode($param);
			}
			else{
				$message.=$param;
			}
		}
		
		return hash_hmac("md5",$message.$salt,$this->authKey);
	}

	public static function getExAppList(){
		$db =& JFactory::getDBO();
		$query = 'SELECT appName FROM #__jc_exApps';
		$db->setQuery($query);
		return $db->loadResultArray();
	}

}