<?php
require_once 'PHPUnit/Framework.php';
include_once '../StandardInvoker.php';
 
class StandardInvokerTest extends PHPUnit_Framework_TestCase{
	var $invoker;
	public function setUP(){
		$this->invoker=new StandardInvoker(null);
	}
	
	public function testRegisterDef(){
		$json='['.
			' {'.
			' 	"method":"UserSync::createUser",'.
			' 	"params":["username","password","email"],'.
			' 	"filters":["encrypt","authenticate"],'.
			' 	"response":"true"'.
			' },'.
			' {'.
			' 	"method":"MuBlog::addStatus",'.
			' 	"params":["username","status"],'.
			' 	"filters":["authenticate"],'.
			' 	"response":"false"'.
			' }'.
			']';
		
		$this->invoker->registerDef($json);
		$this->assertNotNull($this->invoker->def);
		$this->assertSame($this->invoker->def[0]['method'],"UserSync::createUser");
		$this->assertSame(count($this->invoker->def),2);
	}
}