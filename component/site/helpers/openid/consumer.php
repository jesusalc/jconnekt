<?php
$path_extra = dirname(__FILE__);
$path = ini_get('include_path');
$path = $path_extra . DIRECTORY_SEPARATOR . $path;
var_dump($path);
ini_set('include_path', $path);

require_once 'Auth/OpenID/Server.php';
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/SReg.php";
require_once "Auth/OpenID/Interface.php";
require_once "Auth/OpenID/PAPE.php";
require_once "JCOpenIDStore.php";
