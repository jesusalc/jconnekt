<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');


/**
 * The System plugin which is the  Connector API for JConnect ExApps
 * This plugin loads the ExApp class which reside in the jc_connector.php
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class plgSystemJconnect extends JPlugin {

	function plgSystemJconnect(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise(){
		define(JC_API,1);
		require_once( dirname(__FILE__).DS.'Helper.php' );
		require_once( dirname(__FILE__).DS.'ExApp.php' );
		require_once( dirname(__FILE__).DS.'ExternalUser.php' );
		require_once( dirname(__FILE__).DS.'Groups.php' );
		require_once( dirname(__FILE__).DS.'SyncUser.php' );
	}
}
