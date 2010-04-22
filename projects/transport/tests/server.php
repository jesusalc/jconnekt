<?php
 include_once '../Registrar.php';
 include_once '../JCListener.php';
 
 class TestL extends JCListener{
 	public function createUser($params,$chainResponse){
 		return implode("_",$params);
 	}
 	
 	public function addStatus($params,$chainResponse){
 		if($params[0]==null) throw new Exception("params null exception");
 		return implode("_",$params);
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
 $pull->registerListener(new TestL(),"UserSync",1);
 $pull->registerListener(new TestL(),"MuBlog",1);
 $pull->server();
 
 