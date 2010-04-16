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
	
	/**
	 * @param $endpoint the url where webservice stored
	 */
	public function __construct($endpoint){
		
	}
	
	public function registerDef($def){
		$this->def=json_decode($def,true);
	}
	
	public function invoke($method,$params){
		//check method against the definition
		//create json-rpc syntax
		//call the method
		//get the response store it on the array
		//return the latest index of the array
	}
	
	public function getResponse($id){
		//check whether id is a valid array index
		//if the response is a error throw the exception
		//return the response
	}
	
	/**
		This will do the actual communication
		@param $endpoint request endpoint url (with ? )
		@param $json json-rpc request
		
	 */
	private function sendRequest($endpoint,$json){
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
	
	private function validateMethod($request){
		$match;
		foreach ($this->def as $signature){
			if($request['method']==$signature['method']){
				$match=$signature;	
				break;
			}
		}
		
		$errorCode=0;
		
		if(!isset($match)) $errorCode+=Registrar::$METHOD_NOT_FOUND;
		
		if(count($match['params'])!=count($request['params'])) {
			$errorCode+=Registrar::$PARAM_COUNT_NOT_MATCHED;
		}
		
		if($match['response'] && !isset($request['id'])) {
			$errorCode+=Registrar::$NO_VALUE_FOR_ID;
		}
		
		return $errorCode;
	}
}