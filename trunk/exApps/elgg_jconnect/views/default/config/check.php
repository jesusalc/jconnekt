<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2

 * This is used to check login state of given JOOMLA_SESSION and do the things 
 * accrodingly...
 */

$url=$vars['url'];
$jcLoginUrl=(substr($url,strlen($url)-1,1)=="/")? $url."action/logout": 
	$url."/action/logout";
	
 $jSession=$_COOKIE['jc_elgg_j_session'];
 $elggUser=$_SESSION['user']->username;
 JCFactory::getJConnect()->autoActiveSSO($jSession,$elggUser,$jcLoginUrl);
 
 ?>
 
 <?php if(!isloggedin()){?>
 <iframe height=0 width=0 src='http://localhost/jconnekt/joomla/?option=com_jconnect&controller=auth&task=request_token&app_name=Elgg&return_to=http://localhost/jconnekt/elgg/mod/elgg_jconnect/jconnect_api/auth_daemon.php?goto=<?php echo $_SERVER['REQUEST_URI']?>'></iframe>
<?php }?>

