<?php

/**
 * @version		2.0
 * @package		jconnekt
 * @subpackage	transport
 * @copyright	Team JConnekt
 * @license 	GNU/GPL v3, see http://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * This is the Abstract class of Invoker which invokes JSON-RPC webservice
 * filtered by our filtering system
 * and Possible implementation would be
 * 	1. Standard PHP based invokers
 *  2. PHP CLI invokers
 *  3. Event Queue invokers
 *  4. Cron Invokers
 */
abstract class Invoker{
	
	/**
	 	Used to register an definition of method lists
	 	@param $def definition of params given as a json provided as below
		 	[
			 {
			 	method:'UserSync::createUser',
			 	params:['username','password','email'],
			 	filters:['encrypt','authenticate'],
			 	response:'true'
			 },
			 {
			 	method:'MuBlog::addStatus',
			 	params:['username','status'],
			 	filters:['authenticate'],
			 	response:'false'
			 },
			]
	 */
	abstract public function registerDef($def);
	
	/**
		Invokes a specific method in the registered method list
		@param $method method name prefixed by the group
			ex:- groupName::methodName
		@param $params parameter list given as a array
		@return integer an id which used to get the response. This is used for asynchronus
			and multiple web-service calls
	 */
	abstract public function invoke($method,$params);
	
	/**
		Get the response by a given id
		If there is an error throws an Exception
		
		@param $id an id which returned from an invoker
		@return Array 
	 */
	abstract public function getResponse($id);
}