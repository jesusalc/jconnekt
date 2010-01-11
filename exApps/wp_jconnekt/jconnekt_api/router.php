<?php
		
if($_GET['go']=='auth_daemon.php'){
	include_once 'auth_daemon.php';
}
else{
	include_once 'server.php';
}