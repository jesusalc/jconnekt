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
 * The User Plugin which overrids user-actions in the joomla to the ExApps
 *
 * @package		JConnect.plugins.user
 * @since 		1.0
 */


class plgUserJconnect extends JPlugin {

	//used to indicate whether JConnect API is loaded or not..
	private $noGoAhead=false;

	function plgUserJconnect(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//if JConnect Api is not loaded(jconnect system plugin)
		$this->noGoAhead=(JC_API!=1)?true:false;
	}


	/**
	 * this will delete the syncUser from the syncUser table and trigger ExApps for deletion...
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		//if No meaning of going ahead is not loaded(jconnect system plugin)
		if($this->noGoAhead || !$succes) return;

		//get the current session trigger of this action
		$actionOwner=JFactory::getSession()->get("JC_ACTION_OWNER");
		
		//which stores the exceptions
		$exceptions=array();
		//request ExApps to delete the users..
		$exAppNames=ExApp::getExAppList();
		//sending data to exApp
		for($lc=0;$lc<sizeof($exAppNames);$lc++){
			//stop recursivly sending user-info for triggered app
			if($actionOwner==$exAppNames[$lc]) continue;
			//check for enablity of the ExApp
			
			if(!JCHelper::isExAppEnabled($exAppNames[$lc],JCHelper::$OUTGOING)) continue;
			
			//check for early sync
			$ans=SyncUser::contains($user['id'],$exAppNames[$lc]);
			
			if($ans){
				try{
					//already sync user
					$exApp=new ExApp($exAppNames[$lc]);
					//only send for not banned users..
					$appID=JCHelper::getAppID($exAppNames[$lc]);
					$su=new SyncUser($user['id'],$appID);
					var_dump($su);
					if(!($su->status=='BAN')){
						$exApp->deleteUser($user['username']);
					}
					$su->delete();
				}
				catch(Exception $ex){
					$exceptions[sizeof($exceptions)]=$ex;
				}
			}
		}
		

		//print the exceptions as warrning / error
		foreach($exceptions as $id=>$val){
			JError::raiseWarning(0,"JConnect(delete) - " . $val->getMessage());
		}
	}
	
	
}


