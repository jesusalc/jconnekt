<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

class JCElggMisc extends JCMisc{

	public function getPublicView(){
		
	}
	
	public function getPrivateView($username){
		
	}
	
	public function loadSysInfo($meta){
		
		if(isset($meta['JOOMLA_URL'])) update_option('jconnekt_joomla_url',$meta['JOOMLA_URL']."");
		if(isset($meta['JC_APPNAME'])) update_option('jconnekt_app_name',$meta['JC_APPNAME']."");
		
	}
}