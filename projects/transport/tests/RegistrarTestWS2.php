<?php

include_once "../../WSTester/WSTester.php";
include_once "ws2/StandardInvoker.php";

class InvokerTestWS extends WSTester{
	public function __construct(){
		parent::__construct('http://localhost/test/datastore.php');
	}
	
	
	public function getInvoker($server){
		$invoker=new StandardInvoker
		('http://localhost/jconnekt/trunk/projects/transport/tests/ws2/' . $server);
		$invoker->registerDef($this->getDef());
		return $invoker;
	}
	
	public function testCheckSingleListener(){
		
			$invoker=$this->getInvoker("server.php");
			$this->assertSame("aaa_bbb_ccc");
			$id=$invoker->invoke("UserSync::createUser",array("aaa","bbb","ccc"));
			$resp=$invoker->getResponse($id);
			$this->assertResponse('aaa bbb ccc');
	}
	
	public function testCheckMultipleListener(){
		
			$invoker=$this->getInvoker("serverMultipleListeners.php");
			$this->assertSame("aaa_bbb_ccc");
			$id=$invoker->invoke("UserSync::createUser",array("aaa","bbb","ccc"));
			//$resp=$invoker->getResponse($id);
			$this->assertResponse("2 1 ");
	}
	
	public function testCheckMultipleInvokes(){
		
			$invoker=$this->getInvoker("serverMultipleLInvokes.php");
			$this->assertSame("aaa_bbb_ccc");
			$id=$invoker->invoke("UserSync::createUser",array("aaa","bbb","ccc"));
			$this->assertResponse("createUser");
			
			$this->assertSame("aaa_bbb");
			$id=$invoker->invoke("MuBlog::addStatus",array("aaa","bbb"));
			$this->assertResponse("addStatus");
	}
	
	public function testCheckExceptions(){
		
			try{
				$invoker=$this->getInvoker("serverException.php");
				$this->assertSame("aaa_bbb_ccc");
				$id=$invoker->invoke("UserSync::createUser",array("aaa","bbb","ccc"));
			}
			catch(Exception $ex){
				$this->assertResponse("SD");
			}
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
		' 	"response":true'.
		' }'.
		']';
		return $json;
	}
	
}

$wst=new InvokerTestWS();
$wst->execute();



