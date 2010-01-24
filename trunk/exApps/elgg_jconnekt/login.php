<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
include_once 'jconnekt_api/api.php';

if(true){

	try{  
		JCFactory::getJConnect()->login();
	}
	catch(Exception $ex){
		register_error($ex->getMessage());
		global $CONFIG;
		header("Location: $CONFIG->url");
	}
}

