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

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );
 
try{
	$viewHTML=ModJconnectActivityHelper::getView($params);
	require( JModuleHelper::getLayoutPath( 'mod_jconnect_activity' ) ); 
}
catch(Exception $ex){
	echo JText::_('CANNOT_LOAD_EXAPP')." <b>{$params->get('appName')}</b>";
}


