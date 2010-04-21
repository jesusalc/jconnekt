<?php
require_once 'PHPUnit/Framework.php';
include_once '../StandardInvoker.php';
include_once '../Registrar.php';
include_once '../JCListener.php';

class RegistrarTest extends PHPUnit_Framework_TestCase{
	var $registrar;
	public function setUp(){
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
		
		$this->registrar=new Registrar();
		$this->registrar->registerDef($json);
	}
	
	public function testInsertedIntoGroup(){
		$list=new TestListener();
		$this->registrar->registerListener($list,"MuBlog",1);
		$this->registrar->registerListener($list,"MuBlog",2);
		$this->assertNotNull($this->registrar->listeners);
		$this->assertSame(1,count($this->registrar->listeners));
		$this->assertSame(2,count($this->registrar->listeners["MuBlog"]));
	}
	
	public function testDuplicatePriority(){
		try{
			$list=new TestListener();
			$this->registrar->registerListener($list,"MuBlog",1);
			$this->registrar->registerListener($list,"MuBlog",1);	
		}
		catch(Exception $ex){
			return;
		}
		
		$this->fail("Duplicate Accepted:Bad");
	}
	
	
}

class TestListener extends JCListener{
	function addStatus($username,$status){
		
	}
}