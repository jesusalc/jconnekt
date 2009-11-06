<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
//This will show the latest activities in the External Application

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * This class is used to get the view from the ExApp
 * depending on whether a userhas logged into system or not...
 * 
 * @author arunoda Susiripala
 * *
 */
class ModJconnectActivityHelper{
	public static function getView($params){
		if(JC_API!=1) return "JConnet API is not enabled!";
		if(!JCHelper::isExAppEnabled($params->get('appName')))
			return "This JConnect ExApp is not Enabled yet!";
		$user=JFactory::getUser();
		$exApp=new ExApp($params->get('appName'));
		$intArray;
		if($user->id){
			$intArray=$exApp->getPrivateView($user->username);
		}
		else{
			$intArray=$exApp->getPublicView();
		}
		
		$html="";
		foreach($intArray as $intCharVal){
			$html.=chr($intCharVal);
		}
		
		return $html;
	}
}