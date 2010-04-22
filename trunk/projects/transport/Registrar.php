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


class Registrar{
	public static
	$METHOD_NOT_FOUND=2,
	$PARAM_COUNT_NOT_MATCHED=4,
	$NO_VALUE_FOR_ID=8;

	var $def;

	/**
	 * is where listeners are stored and it is stored like below
	 * $listeners[GROUP][PRIORITY]=OBJECT;
	 * @var Array
	 */
	var $listeners=array();
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
		try{
			//get the message from the get or post decode json-rpc
			$json = $_REQUEST['json'];
			$request=json_decode(stripslashes($json),true);

			//validate it with the def file
			$validateResponse=$this->validateMethod($request);
			if(is_int($validateResponse) && $validateResponse>0){
				$this->returnResponse(null,"method signature invalid",$request['id']);
			}

			//@todo use filters
			
			//invoke the method using listners
			$response=$this->invokeListeners($request['method'],$request['params']);
			if($validateResponse['response']){
				$this->returnResponse($response,null,$request['id']);
			}
		}
		catch(Exception $ex){
			$this->returnResponse(null,$ex->getMessage(),$request['id']);
		}
	}

	/**
		This is used to register a listener with the registrar
		@param $listenerObject is the Object where contains methods
		@param $group is the group of the Method which the registered methods
		are going to listen
		@param $priority is the priority of when there are multiple listeners
		attached to the same group
		there should be only one listener for one priority
	 */
	public function registerListener($listenerObject,$group,$priority){
		//@todo validate $listenerObject for it's method signatures
		
		//check whether there is existing entry for group
		//if not add one
		if(!isset($this->listeners[$group])){
			$this->listeners[$group]=array();
		}

		//check whether there is a entry for the $priority
		//if so throw some exception
		if(isset($this->listeners[$group][$priority])){
			throw new Exception('There is an existing registered Listener for' .
			$group." @ priority" . $priority);
		}

		//add the register
		$this->listeners[$group][$priority]=$listenerObject;
	}

	/**
		This will validate request objects according to the method defs
		@param $request is the request object in array format
		@return a errorCode based on binary addition for errors
		or $match - matched method signature
	 */
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

		return ($errorCode>0)?$errorCode:$match;
	}

	private function returnResponse($result,$error,$id){
		echo json_encode(array(
			'result'=>$result,
			'error'=>$error,
			'id'=>$id
		));
		exit(0);
	}

	/**
		This method invokes the listeners assciated $method
		@param $method is the json-rpc method we need to invoke
		eg:- GroupName:method
		MuBlog::addStatus
		@return the response as normally
		@throws Exception when some error occured
	 */
	private function invokeListeners($method,$params){
		//get the method and group
		$methodSplits=explode("::",$method);
		//if invliad throw Exception
		if(!isset($methodSplits[0]) && !isset($methodSplits[1])){
			throw new Exception("Method signature not valid");
		}
		//get the array of listeners
		$listeners=$this->listeners[$methodSplits[0]];
		
		//@todo Validate listeners to check whether there is at least one object listening
		
		//invoke them in a row
		ksort($listeners);
		$chainResponse=null;
		foreach ($listeners as $listener){
			$chainResponse=$listener->$methodSplits[1]($params,$chainResponse);
		}
		
		return $chainResponse;
	}
}
