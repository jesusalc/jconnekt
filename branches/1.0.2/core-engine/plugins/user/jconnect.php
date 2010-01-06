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


	function onAfterStoreUser($user, $isnew, $success, $msg)
	{
		global $mainframe;	
			
		if (!$isnew)
		{
			//when password changes...
			// that user now is not a External User. Now he's JConnekt user
			if(JRequest::getString('password')!=""){
				$exAppNames=ExApp::getExAppList();
				//deleting External User info..
				for($lc=0;$lc<sizeof($exAppNames);$lc++){
						$appID=JCHelper::getAppID($exAppNames[$lc]);
						$su=new SyncUser($user['id'],$appID);
						$su->delete();
				}
				
				if(ExternalUser::contains($user['id'])){
					$eu=new ExternalUser($user['id']);
					$eu->delete();
				}
			}
		}
	}
	
	/**
	 * this will delete the syncUser from the syncUser table 
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		//if No meaning of going ahead is not loaded(jconnect system plugin)
		if($this->noGoAhead || !$succes) return;

		//get the current session trigger of this action
		$actionOwner=JFactory::getSession()->get("JC_ACTION_OWNER");
		$appID=JCHelper::getAppID($exAppNames[$lc]);
		
		//request ExApps to delete the users..
		$exAppNames=ExApp::getExAppList();
		//sending data to exApp
		for($lc=0;$lc<sizeof($exAppNames);$lc++){
				$appID=JCHelper::getAppID($exAppNames[$lc]);
				$su=new SyncUser($user['id'],$appID);
				$su->delete();
		}
		
		if(ExternalUser::contains($user['id'])){
			$eu=new ExternalUser($user['id']);
			$eu->delete();
		}
	}
	
	private function drop_jconnekt_token(){
		@include_once(JPATH_BASE.DS.'components/com_jconnect/models'.DS.'token.php');
		
		//added to overcome executing this when admin logout /login proceeds..
		if(!class_exists('JModel')) return; 
		$jconnekt_token=$_COOKIE['jconnekt_token'];
		if(isset($jconnekt_token)){
			$model=JModel::getInstance("token","JConnectModel");
			
			//delete all token set by all exApps
			$model->delete_by_request_token($jconnekt_token);
			setcookie('jconnekt_token',0,time()-3600,"/");
		}
	}
	
	function onLoginUser($user, $options){
		$this->drop_jconnekt_token();	
	}
	
	function onLogoutUser($user){
		$this->drop_jconnekt_token();	
	}
}


