<?php

class Usage extends WSTester{
	public function __construct(){
		parent::__construct('url of datastore');
	}
	
	public function testAbc(){
		assertTrue('abc','message');
		//call the webservice
		assertLocalTrue('return of the web service','message');
	}
}

//in the service end

function web_service(){
	//value we get
	$tester=new WSTester('url of the datastores');
	$tester->dump('value we get');
	//return the web service.
}

