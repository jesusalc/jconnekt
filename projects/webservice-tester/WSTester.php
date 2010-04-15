<?php

class WSTester{
	private $dataStore;
	private $id=10;
	private $summary;
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

	public function assertLocalTrue($value,$message){

	}

	public function dump($value){
		$params=array
		(
			'method'=>'store_recieve',
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
		foreach ($methods as $method){
			if(substr($method,0,4)=='test'){
				//setting up the id
				$this->id=$method;
				$this->$method();
				$this->displayExecute($method);
			}
		}
		 
		$response=$this->sendRequest($this->dataStore,json_encode(array('method'=>'store_pop')));
		 
		$tests=json_decode(urldecode($response),true);
		 
		$this->displayResultTopic();
		 
		//initiate summary;
		$this->summary=array('passed'=>0,'failed'=>0);
		 
		foreach ($tests as $testItem){
			if($testItem['value_send']==$testItem['value_received']){
				$this->summary['passed']++;
				$this->displaySuccess($testItem);
			}
			else{
				$this->summary['failed']++;
				$this->displayFailure($testItem);
			}
		}
		 
		//display summary
		$this->displaySummary();
	}

	private function displayExecute($method){
		$str= 'executing '.substr($method,4) .'...<br>';
		echo '<div style="background-color:orange;color:white;padding:10px">'.
		$str.
    	'</div>';
	}

	private function displaySuccess($testItem){
		$str=substr($testItem['id'],4)." passed!";
		echo '<div style="background-color:green;color:white;padding:10px">'.
		$str.
    	'</div>';
	}

	private function displayFailure($testItem){
		$str=substr($testItem['id'],4).' failed!'.
    		' [  '.$testItem['message']. '  ]<br>'. 
    		'sent:: '.$testItem['value_send'].'<br>'.
    		'recieved:: '.$testItem['value_received'];
		echo '<div style="background-color:red;color:white;padding:10px">'.
		$str.
    	'</div>';	
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

	public static function instance(){
		return WSTester::$tester;
	}
}


