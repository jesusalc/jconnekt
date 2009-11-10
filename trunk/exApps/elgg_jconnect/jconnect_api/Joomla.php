<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

include_once('aes-lib.php');

class Joomla{
	private $appName;
	private $secretKey;
	private $authKey;
	private $cryptKey;
	private $endpoint;


	public function __construct($url,$appName,$secKey){	
		$this->appName=$appName;
		$this->secretKey=$secKey;
		$this->endpoint=$url;
		
		$keys=explode("::",$secKey);
		$this->authKey=$keys[0];
		$this->cryptKey=$keys[1];
	}


	public function createUser($username,$email,$password,$group='user'){
		$password=AESEncryptCtr($password,$this->cryptKey,256);
		return $this->callMethod("createUser",array(
			'username'=>$username,
			'email'=>$email,
			'password'=>$password,
			'group'=>$group));
	} 

	public function updateUser($username,$email,$password,$group='admin'){
		$password=AESEncryptCtr($password,$this->cryptKey,256);
		return $this->callMethod("updateUser",array(
			'username'=>$username,
			'email'=>$email,
			'password'=>$password,
			'group'=>$group));
	}

	public function deleteUser($username){

		return $this->callMethod("deleteUser",array(
			'username'=>$username));  
	}
	
	
	private function callMethod($action,$paramArray){
		//generating hash value and send-it...
		$hmac_hash=$this->hmac_gen($paramArray);
		
		$paramArray['hmacHash']=$hmac_hash;
		$paramArray['appName']=$this->appName;
		$res=$this->sendRequest($this->endpoint."?option=com_jconnect&format=raw&",$action,json_encode($paramArray));
		$res=json_decode($res,true);
		if($res->result==0){
			return $res['data'];
		}
		else{
			throw new Exception($res->message,$res->no);
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
		if(function_exists('curl_init')){
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
			$res=file("{$endpoint}&action=$action&json=$json");
			$res=stripslashes(implode("\n",$res));
			
		}
		
		return $res;
	}
	
	/**
		@param $params - the parameters work as the message in HMAC
		@return string - hmac hash
	 */
	private function hmac_gen($params){
		$message="";
		foreach($params as $param){
			if(is_array($param) || is_object($param)){
				$message.=json_encode($param);
			}
			else{
				$message.=$param;
			}
		}
		return hash_hmac("md5",$message,$this->authKey);
	}

}
