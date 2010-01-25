<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

admin_gatekeeper();
set_context('admin');

// Set admin user for user block
set_page_owner($_SESSION['guid']);
$names=array("appName","secKey","joomla_url");
if(isset($_REQUEST['submit'])){
	foreach ($names as $name){
		$value=trim($_REQUEST[$name]);
		if(strlen($value)>0) datalist_set($name,$value);
	}
}

$values=array();
foreach ($names as $name){
	$values[$name]=datalist_get($name);
}

$form=elgg_view("config/form",$values);
$title = elgg_view_title("JConnect Configurations");
page_draw("JConnect Config",elgg_view_layout("two_column_left_sidebar", '', $title . $form));

?>