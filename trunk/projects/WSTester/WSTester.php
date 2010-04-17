<?php

class WSTester{
	private $dataStore;
	private $id=10;
	private $summary;
	/**
	 * 
	 * @var WSTester
	 */
	public static $tester;
	public function __construct($dataStore){
		$this->dataStore=$dataStore;
	}

	public function assertSame($value,$message=""){
		$params=array
		(
			'method'=>'store_send',
			'id'=>$this->id,
			'value'=>$value,
			'message'=>$message
		);

		$this->sendRequest($this->dataStore,json_encode($params));
	}

	public function assertResponse($value){
		$params=array
		(
			'method'=>'store_response_recieve',
			'value'=>$value,
		);

		$this->sendRequest($this->dataStore,json_encode($params));
	}

	public function dump($value){
		$params=array
		(
			'method'=>'store_recieve',
			'value'=>$value,
		);

		$this->sendRequest($this->dataStore,json_encode($params));
	}
	
	public function dumpResponse($value){
		$params=array
		(
			'method'=>'store_response_send',
			'value'=>$value,
		);

		$this->sendRequest($this->dataStore,json_encode($params));
	}

	private function sendRequest($endpoint,$json){
		if(function_exists('curl_init')){
			$ch = curl_init($endpoint.'?action='.urlencode($json));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = stripcslashes(curl_exec($ch));
			curl_close($ch);
		}
		else{
			$res=file($endpoint.'?action='.urlencode($json));
			$res=stripslashes(implode("\n",$res));

		}

		return $res;
	}



	public function execute(){
		//display heading
		 
		$this->displayHeading();
		 
		$methods=get_class_methods($this);
		//setUp,tearDown support
		$isSetUp=(array_search("setUp",$methods)===false)?false:true;
		$isTearDown=(array_search("tearDown",$methods)===false)?false:true;
		
		foreach ($methods as $method){
			if(substr($method,0,4)=='test'){
				//setting up the id
				$this->id=$method;
				
				if($isSetUp) $this->setUp(); //invoke Setup
				$this->$method(); //invoke the method
				if($isTearDown) $this->tearDown(); //invoke TearDown
				
				$this->displayExecute($method);
			}
		}
		 
		$response=$this->sendRequest($this->dataStore,json_encode(array('method'=>'store_pop')));
		 
		$tests=json_decode(urldecode($response),true);
		//var_dump($tests);
		$this->displayResultTopic();
		 
		//evaluate tests
		$this->evalTestResults($tests);
		 
		//display summary
		$this->displaySummary();
	}
	
	/**
	 * Evaluate test results
	 * @param $tests as an array
	 */
	private function evalTestResults($tests){
	//initiate summary;
		$this->summary=array('passed'=>0,'failed'=>0);
		 
		foreach ($tests as $testItem){
			$failed=false;
			$request_failed=false;
			$response_failed=false;
			if($testItem['value_send']!=$testItem['value_received']){
				$request_failed=true;
			}
			
			if($testItem['value_response_send']!=$testItem['value_response_received']){
				$response_failed=true;
			}
			
			if($request_failed || $response_failed) {
				$this->summary['failed']++;
				$this->displayFailure($testItem,$request_failed,$response_failed);
			}
			else{
				$this->summary['passed']++;
				$this->displaySuccess($testItem);
			}
		}
	}

	private function displayExecute($method){
		$str= 'executing '.substr($method,4) .'...<br>';
		echo '<div style="color:rgb(100,100,100);padding:1px">'.
		$str.
    	'</div>';
	}

	private function displaySuccess($testItem){
		$str=substr($testItem['id'],4)." passed!";
		echo '<div style="background-color:green;color:white;padding:10px">'.
		$str.
    	'</div>';
	}

	private function displayFailure($testItem,$request,$response){
		$requestStr=substr($testItem['id'],4).' failed! [ REQUEST ]'.
	    		' [  '.htmlentities($testItem['message']). '  ]<br>'. 
	    		'sent:: '.htmlentities($testItem['value_send']).'<br>'.
	    		'recieved:: '.htmlentities($testItem['value_received']);
		
		$responseStr=substr($testItem['id'],4).' failed! [ RESPONSE ]'.
    		' [  '.htmlentities($testItem['message']). '  ]<br>'. 
    		'sent:: '.htmlentities($testItem['value_response_send']).'<br>'.
    		'recieved:: '.htmlentities($testItem['value_response_received']);
		
	 	echo '<div style="background-color:red;color:white;padding:10px">';
	 	if($request){
	 		echo '<div style="width:50%;float:left;background-color:pink;color:black">'.$requestStr.'</div>';
	 	}
	 	
		if($response){
	 		echo '<div style="width:50%;float:left;background-color:purple;">'.$responseStr.'</div>';
	 	}
	 	echo '<div style="clear:both"></div></div>';			
	}

	private function displayResultTopic(){
		echo "<br/><h2>Displaying Results</h2>";
	}

	private function displaySummary(){
		echo "<h2>Summary</h2>";
		echo 'Total Tests: '.($this->summary['passed']+$this->summary['failed']).'<br/>';
		echo 'Passed: '.$this->summary['passed'].'<br/>';
		echo '<span style="color:red">Failed: '.$this->summary['failed'].'</span><br/>';
	}

	private function displayHeading(){
		$className=get_class($this);
		echo '<h1>Testing <i>'.$className.'</i></h1>';
	}

	//singleton functions
	public static function setStore($datastore){
		WSTester::$tester=new WSTester($datastore);
	}
	
	/**
	 * Get the singleton object
	 * @return WSTester 
	 */
	public static function instance(){
		if(!isset(WSTester::$tester)) throw new Exception("setStore first");
		return WSTester::$instance;
	}

}


