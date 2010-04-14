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
	$storeStr=$id . '::'. $value . '::'.$message;
	fwrite($file,$storeStr);
	fclose($file);
}

function store_recieve($value){
	$file=fopen('datastore.txt','a');
	fwrite($file,'::'.$value."\n");
	fclose($file);
}

function store_pop(){
	$content=file('datastore.txt'); 
	$rtn=array();
	foreach ($content as $line){
		$pragments=explode("::",$line);
		$rtn[]=array
		(
			'id'=>$pragments[0],
			'value_send'=>$pragments[1],
			'value_received'=>substr($pragments[3],0,-1), //inorder to remove last endline
			'message'=>$pragments[2]
		);
	}
	
	//deleting the store
	$file=fopen('datastore.txt','w');
	fwrite($file,'');
	fclose($file);
	
	echo urlencode(json_encode($rtn));
}

$action=$_GET['action'];
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
	store_pop($action['value']);
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
