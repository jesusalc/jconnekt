<?php
 include_once 'Registrar.php';
 include_once 'JCListener.php';
 
 class TestL1 extends JCListener{
	function createUser($params,$chainResponse){
		return implode(" ",$params);
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
 $pull->registerListener(new TestL1(),"UserSync",1);
 $pull->server();