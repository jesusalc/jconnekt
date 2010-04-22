<?php
/**
 * @version		2.0
 * @package		jconnekt
 * @subpackage	transport
 * @copyright	Team JConnekt
 * @license 	GNU/GPL v3, see http://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * The implementation of the Invoker as a Standard PHP Invoker
 * 
 */

include_once 'Invoker.php';

class StandardInvoker extends Invoker{
	public static 
		$METHOD_NOT_FOUND=2,
		$PARAM_COUNT_NOT_MATCHED=4,
		$NO_VALUE_FOR_ID=8;

	var $def; //as an Array
	var $endpoint;
	var $responses;
	
	/**
	 * @param $endpoint the url where webservice stored
	 */
	public function __construct($endpoint){
		$this->endpoint=$endpoint;
	}
	
	public function registerDef($def){
		$this->def=json_decode($def,true);
	}
	
	public function invoke($method,$params){
		//check method against the definition
		$validateResponse=$this->validateMethod($method,$params);
		
		if(is_int($validateResponse)) throw new Exception("Method Signature Error",$errorCode);
		
		//create json-rpc syntax
		$id=null;
		//no id for method which there is no response
		if($validateResponse['response']){
			$id='StandardInvoker::'.rand(0,100);
		}
		$json=$this->getJsonRpc($method,$params,$id);
		
		//call the method
		$response=$this->sendRequest($this->endpoint,$json);
		//get the response store it on the array
		$this->handleResponse($response);
		//return the latest index of the array
		return $id;
	}
	
	public function getResponse($id){
		//check whether id is a valid array index
		//if the response is a error throw the exception
		
		if(!isset($id) || !array_key_exists($id,$this->responses)){
			throw new Exception("invalid method id");
		}
		
		//@todo do we want to remove the response from the array
		//return the response
		return $this->responses[$id];
	}
	
	/**
		This will do the actual communication
		@param $endpoint request endpoint url
		@param $json json-rpc request
		
	 */
	private function sendRequest($endpoint,$json){
		//ready the endpoint to added to the parameters
		if(strpos($endpoint,'?')===false) $endpoint.="?";
		else $endpoint.="&";
		
		$res;
		if(function_exists('curl_init')){
			$ch = curl_init("$endpoint");
			$params=urlencode("json").'='.urlencode($json);
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $params);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        $res = stripcslashes(curl_exec($ch));       
	        curl_close($ch);
	        
		}
		else{
			$res=file("{$endpoint}json=$json");
			$res=stripslashes(implode("\n",$res));
			
		}
		
		return $res;
	}
	
	/**
		validate method requested with the definition we're having
		@return a collection of bitwise error codes (as a sum) if there is a error
			unless the signature object
	 */
	private function validateMethod($method,$params){
		if(!isset($this->def)) throw new Exception('Method Definition not supplied');
		$match;
		foreach ($this->def as $signature){
			if($method==$signature['method']){
				$match=$signature;
				break;
			}
		}
		
		$errorCode=0;
		
		if(!isset($match)) $errorCode+=StandardInvoker::$METHOD_NOT_FOUND;
		
		if(count($match['params'])!=count($params)) {
			$errorCode+=StandardInvoker::$PARAM_COUNT_NOT_MATCHED;
		}
	
		if($errorCode==0){
			return $match;
		}else{
			return $errorCode;
		}
		
	}
	
	/**
		generates the json-rpc signature
		@return json-rpc signature in json 
	 */
	private function getJsonRpc($method,$params,$id){
		$rpc=array
		(
			'method'=>$method,
			'params'=>$params,
			'id'=>$id
		);
		
		return json_encode($rpc);
	}
	
	/*
	 * This will store the response in the array index as the id
	 * and throws exception any error occured
	 */
	private function handleResponse($response){
		//if response is not provided that's a notification
		//so we don't handle it.
		if(!isset($response) || $response=="") return;
		
		$rpc=json_decode(stripslashes($response),true);
		if(isset($rpc['error'])) throw new Exception($rpc['error']);
		
				
		$this->responses[$rpc['id']]=$rpc['result'];
	}
}