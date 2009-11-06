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
include_once('aes-lib.php');

class Joomla{
	public $appName;
	public $secretKey;
	public $host='localhost';
	public $path='/gsoc/JConnect/exApps/fake/server.php';
	public $port=80;
	public $authKey;
	public $cryptKey;

	//the xmlrpc client...
	private $client;
	private $cookies;

	public function __construct($url,$appName,$secKey){
		$this->host=preg_replace(array('/http(s)*:\/\//','/:[a-zA-Z0-9\/\.]*/'),'',$url);
		$this->port=preg_replace(array('/http(s)*:\/\//',"/$this->host:/","/\/[a-zA-Z0-9\/\.]*/"),'',$url);
		$this->path="/".preg_replace(array("/http(s)*:\/\//","/$this->host/","/:{$this->port}[\/]*/"),'',$url).
			"/?option=com_jconnect&format=raw";
		
		$this->appName=$appName;
		$this->secretKey=$secKey;

		$this->client=new xmlrpc_client($this->path,$this->host,$this->port);
		$this->client->return_type="phpvals";
		$this->client->setDebug(0);
		
		$keys=explode("::",$secKey);
		$this->authKey=$keys[0];
		$this->cryptKey=$keys[1];
	}


	public function createUser($username,$email,$password,$group='user'){
		$password=AESEncryptCtr($password,$this->cryptKey,256);
		return $this->callMethod("jc.host.createUser",array($username,$email,$password,$group));
	} 

	public function updateUser($username,$email,$password,$group='admin'){
		//encrypting password..
		$password=AESEncryptCtr($password,$this->cryptKey,256);
		return $this->callMethod("jc.host.updateUser",array($username,$email,$password,$group));
	}

	public function deleteUser($username){

		return $this->callMethod("jc.host.deleteUser",array($username));  
	}
	
	
	private function callMethod($methodName,$paramArray){
		//generating hash value and send-it...
		$hmac_hash=$this->hmac_gen($paramArray);
		$msg=new xmlrpcmsg($methodName);
		$msg->addParam(php_xmlrpc_encode($this->appName));
		$msg->addParam(php_xmlrpc_encode($hmac_hash));
		for($lc=0;$lc<sizeof($paramArray);$lc++){
			$msg->addParam(php_xmlrpc_encode($paramArray[$lc]));
		}

		$res =& $this->client->send($msg, 0, '');

		if($res->faultCode){
			throw new Exception("xmlrpc Error :: ".$res->faultString, $res->faultCode);
		}
		else if($res->errno){
			throw new Exception("user Error :: ".$res->errstr,$res->errno);
		}

		$varU=$res->value();
		
		if(!is_string($varU) && isset($varU['code'])){
			throw new Exception("Joomla Error :: ".$varU['message'],(int)$varU['code']);
		}
		
		//validating hash for return value..
		$hmac_hash=$this->hmac_gen(array($varU[1]));
		if($varU[0]!=$hmac_hash) throw new Exception ("ExApp Error: Authentication or Integrity failed!");
		$varU=$varU[1];

		return $varU;

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
