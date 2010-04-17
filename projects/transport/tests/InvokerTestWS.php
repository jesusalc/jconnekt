<?php

include_once "../../WSTester/WSTester.php";
include_once "ws/StandardInvoker.php";



class InvokerTestWS extends WSTester{
	public function __construct(){
		parent::__construct('http://localhost/test/datastore.php');
	}
	
	var $invoker;
	public function setUp(){
		$invoker=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/ws/server.php');
		$invoker->registerDef($this->getDef());
		$this->invoker=$invoker;
	}
	
	public function tearDown(){
		
	}

	public function getInvoker(){
		
	}
	
	public function testBasicInvoke(){
		$invoker=$this->invoker;
		$this->assertSame("username_password_email");
		$id=$invoker->invoke("UserSync::createUser",array("username","password","email"));
		$res=$invoker->getResponse($id);
		$this->assertResponse("UserSync::createUser");
		
	}
	
	public function testBasicSpecialCharInvoke(){
		$invoker=$this->invoker;
		$this->assertSame("username&10%_password_email");
		$id=$invoker->invoke("UserSync::createUser",array("username&10%","password","email"));
		$res=$invoker->getResponse($id);
		$this->assertResponse("UserSync::createUser");
	}
	
	public function testJSONRpcNotifications(){
		$invoker=$this->invoker;
		$this->assertSame("username_status");
		$id=$invoker->invoke("MuBlog::addStatus",array("username","status"));
	}
	
	public function testJSONRpcNotificationsSpecialChar(){
		$invoker=$this->invoker;
		$this->assertSame("^^$%3@#$#^(98\"_status&@5^*((&*^*^#$#$");
		$id=$invoker->invoke("MuBlog::addStatus",array("^^$%3@#$#^(98\"","status&@5^*((&*^*^#$#$"));
	}

	function getDef(){
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

$wst=new InvokerTestWS();
$wst->execute();

