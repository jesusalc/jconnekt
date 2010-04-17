<?php
require_once 'PHPUnit/Framework.php';
include_once '../StandardInvoker.php';

class StandardInvokerTest extends PHPUnit_Framework_TestCase{
	var $invoker;
	public function setUP(){
		$this->invoker=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/server.php');
		$json='['.
			' {'.
			' 	"method":"UserSync::createUser",'.
			' 	"params":["username","password","email"],'.
			' 	"filters":["encrypt","authenticate"],'.
			' 	"response":true'.
			' },'.
			' {'.
			' 	"method":"MuBlog::addStatus",'.
			' 	"params":["username","status"],'.
			' 	"filters":["authenticate"],'.
			' 	"response":false'.
			' }'.
			']';

		$this->invoker->registerDef($json);
	}

	public function testRegisterDef(){

		$this->assertNotNull($this->invoker->def);
		$this->assertSame($this->invoker->def[0]['method'],"UserSync::createUser");
		$this->assertSame(count($this->invoker->def),2);
	}

	public function testBasicInvoke(){
		$id=$this->invoker->invoke("UserSync::createUser",array("u","p","e"));
		$this->assertNotNull($id);
		$response=$this->invoker->getResponse($id);
		$this->assertNotNull($response,"UserSync::createUser");
	}

	public function testMethodSignature(){
		try{
			$this->invoker->invoke("UserSyn::createUser",array("u","p","e"));
			$this->invoker->invoke("UserSync::createUser",array("p","e"));
			$this->invoker->invoke("Userc::createUser",array("p","e"));
			$this->invoker->invoke(null,null);
		}
		catch(Exception $ex){
			return;
		}
		$this->fail("Method Signature Invalid exception not throwed");
	}
	
	public function testDefinitionInvalidException(){
		try{
			$i1=new StandardInvoker
				('http://localhost/jconnekt/trunk/projects/transport/tests/server.php');
			$i1->invoke("UserSync::createUser",array("u","p","e"));
		}
		catch(Exception $ex){
			return;
		}
		
		$this->fail("not throws Exception When definition is not provided ");
	}
	

	/**
		@dataProvider getinvokers
	 */
	public function testEndpointCheck($invoker){

		$id=$invoker->invoke("UserSync::createUser",array("u","p","e"));
		$this->assertNotNull($id);
		$res=$invoker->getResponse($id);
		$this->assertNotNull($res);

	}
	

	public function getinvokers(){
		$i1=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/server.php');
		$i1->registerDef($this->getDef());
		
		$i2=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/server.php?abc=100');
		$i2->registerDef($this->getDef());
		
		$i3=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/server.php?a=10&d=10');
		$i3->registerDef($this->getDef());
		return array(
			array($i1),
			array($i2),
			array($i3)
		);
	}
	
	public function testGettingIdForNotifications(){
		$id=$this->invoker->invoke("MuBlog::addStatus",array("a","a"));
		$this->assertNull($id);
	}

	private function getDef(){
		$json='['.
			' {'.
			' 	"method":"UserSync::createUser",'.
			' 	"params":["username","password","email"],'.
			' 	"filters":["encrypt","authenticate"],'.
			' 	"response":true'.
			' },'.
			' {'.
			' 	"method":"MuBlog::addStatus",'.
			' 	"params":["username","status"],'.
			' 	"filters":["authenticate"],'.
			' 	"response":false'.
			' }'.
			']';
		return $json;
	}
}