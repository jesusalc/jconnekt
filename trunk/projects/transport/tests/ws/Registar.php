<?php
/**
 * @version		2.0
 * @package		jconnekt
 * @subpackage	transport
 * @copyright	Team JConnekt
 * @license 	GNU/GPL v3, see http://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * Registrar is the place where all the web-services are managed
 */

include_once "../../../WSTester/WSTester.php";
WSTester::setStore('http://localhost/test/datastore.php');

class Registrar{
	public static 
		$METHOD_NOT_FOUND=2,
		$PARAM_COUNT_NOT_MATCHED=4,
		$NO_VALUE_FOR_ID=8;
		
	var $def;
	/**
	 	Used to register an definition of method lists
	 	@param $def definition of params given as a json provided as below
		 	[
			 {
			 	method:'UserSync::createUser',
			 	params:['username','password','email'],
			 	filters:['encrypt','authenticate'],
			 	response:true
			 },
			 {
			 	method:'MuBlog::addStatus',
			 	params:['username','status'],
			 	filters:['authenticate'],
			 	response:false
			 },
			]
	 */
	public function registerDef($def){
		$this->def=json_decode(stripslashes($def),true);
	}
	
	public function server(){
		//get the message from the get or post decode json-rpc
		$json = $_REQUEST['json'];
		$request=json_decode(stripslashes($json),true);
		
		//WST
		WSTester::$tester->dump(implode("_",$request['params']));
		//WST
		
		//validate it with the def file
		$errorCode=$this->validateMethod($request);
		if($errorCode>0){
			$this->returnResponse(null,"method signature invalid",$request['id']);
		}
		
		//@todo use filters
		//invoke the method
		//@todo invoke using Listeners
		//return the response as json-rpc
		
		//WST
		WSTester::$tester->dumpResponse($request['method']);
		//WST
		$this->returnResponse($request['method'],null,$request['id']);
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
	
	private function returnResponse($result,$error,$id){
		echo json_encode(array(
			'result'=>$result,
			'error'=>$error,
			'id'=>$id
		));
		exit(0);
	}
}
