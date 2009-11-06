<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableExapps extends JTable{
	var $appID;
	var $appName;
	var $secretKey;
	var $host;
	var $path;
	var $port;
	
	public function TableExapps(&$db){
		parent::__construct("#__jc_exApps","appID",$db);
	}
}