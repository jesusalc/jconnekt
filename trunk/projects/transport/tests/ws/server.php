<?php
 include_once 'Registar.php';
 
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
 $pull->server();