<?php

include_once 'WSTester.php';

class Usage extends WSTester{
	private $ws;
	public function __construct(){
		parent::__construct('http://localhost/jconnekt/trunk/projects/webservice-tester/datastore.php');
		$this->ws=new WS();
	}
	
	public function testCorrectOne(){
		$this->assertTrue('abc','This is a Correct One');
		$this->ws->correctOne();
	}

	public function testIncorrectOne(){
		$this->assertTrue('abc2','This is the Incorrect One');
		$this->ws->incorrectOne();
	}
}

//Run the Test.

$usage=new Usage();
$usage->execute();


//sample web services (alternatives for the web services)

class WS{
	private $tester;
	function __construct(){
		$this->tester=new WSTester('http://localhost/jconnekt/trunk/projects/webservice-tester/datastore.php');
		
	}
	
	function correctOne(){
		$this->tester->dump('abc');
	}
	
	function incorrectOne(){
		$this->tester->dump('abc');
	}
}

