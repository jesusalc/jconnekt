<?php

/*
 * json_incoming message
 * {
 *  method:'store_send',
 *  value:'the value'
 *  id:'id',
 *  message:''
 * }
 * 
 * {
 * 	method:'store_recieve',
 *  value:'the value'
 * }
 * 
 * //this will return all the data available in the store and reset it.
 * {
 * 	method:'store_pop'
 * }
 * 
 * json outgoing message for store_pop
 * [
 * 	{
 * 		id:'the id',
 * 		value_send:'the value'
 * 		value_recieve:'the_value'
 * 		message:'the_message'
 *  },
 * ]
 * 
 */




function store_send($id,$value,$message){
	$file=fopen('datastore.txt','a');
	$data=array($value,$message);
	$storeStr="\n".$id . '::'. $value . '::'.$message;
	fwrite($file,$storeStr);
	fclose($file);
}

function store_recieve($value){
	$file=fopen('datastore.txt','a');
	fwrite($file,'::'.$value);
	fclose($file);
}

function store_response_send($value){
	$file=fopen('datastore.txt','a');
	fwrite($file,'::'.$value);
	fclose($file);
}

function store_response_recieve($value){
	$file=fopen('datastore.txt','a');
	fwrite($file,'::'.$value);
	fclose($file);
}

function store_pop($callback){
	$content=file('datastore.txt'); 
	$rtn=array();
	foreach ($content as $n=>$line){
		if($n==0)continue; //remove first endline
		if(substr($line,-1,1)=="\n") $line=substr($line,0,-1); //remove endline @each line
		$pragments=explode("::",$line);
		$index=count($rtn);
		$rtn[$index]=array
		(
			'id'=>$pragments[0],
			'value_send'=>$pragments[1],
			'value_received'=>$pragments[3], //inorder to remove last endline
			'message'=>$pragments[2]
		);
		
		//add response details if both dump and assert done in response
		if(isset($pragments[4]) && isset($pragments[5])) {
			$rtn[$index]['value_response_send']=$pragments[4];
			$rtn[$index]['value_response_received']=$pragments[5];
		}
	}
	
	//deleting the store
	$file=fopen('datastore.txt','w');
	fwrite($file,'');
	fclose($file);
	
	//look for jsonp ajax requests.
	if(isset($callback)){
		$json=json_encode($rtn);
		echo "$callback($json)";
	}
	else{
		echo urlencode(json_encode($rtn));
	}
}

$action=$_GET['action'];
//the jsonp callback
$callback=$_GET['callback']; 
//var_dump($action);
$action=json_decode(stripslashes($action),true);

//var_dump($action);

if($action['method']=='store_send'){
//	echo "send";
	store_send($action['id'],$action['value'],$action['message']);
}
else if($action['method']=='store_recieve'){
//	echo "recieve";
	store_recieve($action['value']);
}
else if($action['method']=='store_pop'){
//	echo "pop<hr/>";
	store_pop($callback);
}
else if($action['method']=='store_response_send'){
//	echo "pop<hr/>";
	store_response_send($action['value']);
}
else if($action['method']=='store_response_recieve'){
//	echo "pop<hr/>";
	store_response_recieve($action['value']);
}

//echo '<hr/>';

/*
$data=array(
		'method'=>'store_send',
		'id'=>'21',
		'value'=>'the value',
		'message'=>'the test message'
	);
	echo '<a href=\'?action='.json_encode($data).'\'>Send</a><br>';
	
	$data=array(
		'method'=>'store_recieve',
		'value'=>'the value2 recieve',
	);
	echo '<a href=\'?action='.json_encode($data).'\'>Receive</a><br>';
	
	$data=array(
		'method'=>'store_pop',
	);
	echo '<a href=\'?action='.json_encode($data).'\'>Pop</a><br>';
*/
