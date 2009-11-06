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
 *This is a plugin used to authenticate users using External Applications
 *
 * @package		JConnect.plugins.authentication
 * @since 		1.5
 */

class plgAuthenticationJconnect extends JPlugin
{

	function plgAuthenticationJconnect(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param	array	$credentials	Array holding the user credentials
	 * @param	array	$options		Array of extra options
	 * @param	object	$response		Authentication response object
	 * @return	boolean
	 * @since	1.5
	 */
	function onAuthenticate( $credentials, $options, &$response )
	{
		$sql="SELECT appID FROM #__jc_externalUsers WHERE username='{$credentials['username']}'";
		$db=JFactory::getDBO();
		$db->setQuery($sql);
		$res=$db->loadObject();
		if(!$res){
			$response->status			= JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message	= $db->stderr();
			return false;
		}
		
		
		$success=false;
		try{
			$exApp=new ExApp((int)$res->appID);
			$success = $exApp->authenticate($credentials['username'],$credentials['password']);	
		}
		catch(Exception $ex){
			$response->status			= JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message	= $ex->getMessage();
			return false;
		}
		
		
		if ($success)
		{
			$response->status			= JAUTHENTICATE_STATUS_SUCCESS;
			$response->error_message	= '';
			return true;
		}
		else
		{
			$response->status			= JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message	= 'Could not authenticate';
			return false;
		}
	}
}
