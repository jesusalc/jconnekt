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

JCFactory::load_js_library();

//draw auot active sso component
if(!isloggedin() || JCFactory::isJConnektSession()){
		echo "<div id='jconnekt_sso_box'></div>";
		echo "<script type='text/javascript'>setTimeout(\""."jconnekt.ajax_validator('jconnekt_sso_box','". $_SERVER['REQUEST_URI'] ."'),500"."\");</script>";//
}

