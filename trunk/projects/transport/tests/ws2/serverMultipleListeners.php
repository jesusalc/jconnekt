<?php
include_once 'Registrar.php';
include_once 'JCListener.php';
 
class TestL1 extends JCListener{
	function createUser($params,$chainResponse){
		return $chainResponse . "1 ";
	}
}

class TestL2 extends JCListener{
	function createUser($params,$chainResponse){
		return $chainResponse . "2 ";
	}
}
 
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
 
 $pull=new Registrar();
 $pull->registerDef($json);
 $pull->registerListener(new TestL1(),"UserSync",10);
 $pull->registerListener(new TestL2(),"UserSync",2);
 $pull->server();