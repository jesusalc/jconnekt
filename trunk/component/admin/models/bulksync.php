<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JconnectModelBulksync extends JModel {

	function getExAppUserCount($appName){
		$exApp= new ExApp($appName);
		return $exApp->getUserCount();
	}

	function getSyncUserCount($appName){
		
		$sql="SELECT COUNT(*) as cnt FROM #__jc_syncUsers su INNER JOIN #__jc_exApps e ".
			"ON e.appID=su.appID WHERE appName='$appName'";
		$this->_db->setQuery($sql);
		if($this->_db->getErrorNum()){
			throw new Exception($this->_db->getErrorNum() ." :: ". $this->_db->getErrorMsg());
		}
		return $this->_db->loadObject()->cnt;
	}

	function getJoomlaUserCount(){
		$sql="SELECT COUNT(*) as cnt FROM #__users";
		$this->_db->setQuery($sql);
		if($this->_db->getErrorNum()){
			throw new Exception($this->_db->getErrorNum() ." :: ". $this->_db->getErrorMsg());
		}
		return $this->_db->loadObject()->cnt;
	}
	


	/** Synchronize given set of users to the exApp
	 * @todo transaction handling should goes here..
	 * @access public
	 * @param $userDetails - array of users. format => array(array("kamal","kamal@yahoo.com"),...);
	 * @return true on success
	 */
	function syncToExApp($appName,$userDetails){
		$exApp=new ExApp($appName);
		$exception=$exApp->bulkSync($userDetails);
		
		//remove the users with exceptions to update syncUser table...
		$res=JCHelper::array_diff_2d_2d($userDetails,$exception);
		foreach($res as $id => $user){
			$syncUser=new SyncUser();
			$syncUser->appName=$appName;
			$syncUser->username=$user[0];
			//we do both this becuase to indicate a flag to startSync to not to get these users....
			$syncUser->needSync=1;
			$syncUser->needSyncWithExApp=1;
			$syncUser->save();
		}

		return $exception;
	}


	/** Synchronize given set of users to the Joomla
	 * @todo include user-validation
	 * @access public
	 * @param $userDetails - array of users. format => array(array("kamal","kamal@yahoo.com"),...);
	 * @return true on success
	 */
	function syncToJoomla($appID,$userDetails){
		$acl =& JFactory::getACL();
		$gid = $acl->get_group_id( '', 'Registered', 'ARO' );

		$exceptions=array();
		for($lc=0;$lc<sizeof($userDetails);$lc++){
			try{
				jimport("joomla.user.helper");
				$username=$userDetails[$lc][0];
				$email=$userDetails[$lc][1];
				$group=$userDetails[$lc][2];
				$password=substr(md5(rand()),0,25);
				$userGroup=new JCGroupIn($appID,$group);
				$aclGroup=($userGroup->joomlaGroup)?$userGroup->joomlaGroup:'Registered';
				
				JCHelper::createJoomlaUser($username,$email,$password,$aclGroup);
				
				$userID=JUserHelper::getUserId($username);
				$db=JFactory::getDBO();
				//insert into External User..
				
				$eu=new ExternalUser($userID);
				$eu->username=$username;
				$eu->ownerAppID=$appID;
				$eu->needSync=1;
				$eu->save();
				
				//insert into Sync User
				$su=new SyncUser($userID,$appID);
				$su->status="OK";
				$su->save();
			}
			catch(Exception $ex){
				//if($ex->getCode()==2345) throw $ex; //if an a fatal error
				/// @todo below would be no use...
				$size=sizeof($exceptions);
				$exceptions[$size][0]=$username;
				$exceptions[$size][1]=$ex->getMessage();
			}
		}

		return $exceptions;
	}

	/**
	 *
	 * This function is used to get information about sync for each ExApp
	 * this is done via SyncInfo Object
	 * @return array Of SyncInfo objetcs @see SyncInfo
	 */
	public function getInfoList(){
		$exApps=ExApp::getExAppList();
		$synInfoList=array();
		$exception=array();
		foreach($exApps as $id=>$appName){
			try{
				$syncSize=$this->getSyncUserCount($appName);
				$appSize=$this->getExAppUserCount($appName);
				$jSize=$this->getJoomlaUserCount();
				$syncInfo=new SyncInfo(
				$appName,
				$appSize,
				$appSize-$syncSize,
				$jSize-$syncSize
				);

				array_push($synInfoList,$syncInfo);
			}
			catch(Exception $ex){
				$err=array($appName,$ex->getMessage());
				array_push($exception,$err);
			}
		}

		foreach($exception as $errObj){
			JError::raiseWarning(0,"Error loading [$errObj[0]] => $errObj[1]");
		}

		return $synInfoList;
	}

	/**
	 *@todo toEx Synchronization (do syncUser altering)...
	 * This will do the synchronization based on the parameters
	 * @param $appName the ExApp name.
	 * @param $toJFlag a boolean saying allow to sync to Joomla
	 * @param $toExFlag a boolean saying allow to sync to ExApp
	 * @return the conflicts as array()
	 */
	public function startSync($appName){
		$conflicts=array();
		$exceptions=array();
		$exApp=new ExApp($appName);
		$toJCount=0;
		$toExCount=0;
		$DIVIDER=500;
		$appID=JCHelper::getAppID($appName);
		
		for($lc=1;true;$lc++){
			$users=$exApp->getUserDetails($DIVIDER,$lc);
			if(sizeof($users)==0) break;
			
			$sql="SELECT u.username FROM #__jc_syncUsers su INNER JOIN #__users u ON su.JID=u.id WHERE appID=$appID";
			$this->_db->setQuery($sql);
			if($this->_db->getErrorNum()) throw new Exception($this->_db->getErrorMsg());
			$syncedUsers=$this->_db->loadResultArray();
			
			$nonSyncUsers=JCHelper::array_diff_2d_1d($users,$syncedUsers);
			
			//get conflict users
			$sql="SELECT username FROM #__users WHERE username IN ('"
			.(JCHelper::implodeArray2D("','",$nonSyncUsers,0))."')";
			$this->_db->setQuery($sql);
			if($this->_db->getErrorNum()) throw new Exception($this->_db->getErrorMsg());
			$conflictsTmp=$this->_db->loadResultArray();
			
			//new users..
			$newUsers=JCHelper::array_diff_2d_1d($nonSyncUsers,$conflictsTmp);
			$exceptionsTmp=$this->syncToJoomla($appID,$newUsers);
			$toJCount=sizeof($newUsers) - sizeof($exceptionsTmp);
			$exceptions=array_merge($exceptions,$exceptionsTmp);
			
			//total conflicts..
			$conflicts =array_merge($conflicts,$conflictsTmp);
		}
		
		//Conflicts will be set-into the Session Variable
		$session=JFactory::getSession();
		$session->set("conflicts",$conflicts);
		$result=array("appName"=>$appName,"toJCount"=>$toJCount,
			"exceptions"=>$exceptions);

		return $result;
	}

	

	
}

/**
 * This class is used to transport sync-info to the view
 * @package		JConnect.component.admin.models
 * @author Arunoda Susiripala
 *
 */
class SyncInfo{
	public $appName;
	public $totalUsers;
	/**
	 * show total no of users in ExApp not sync with Joomla
	 * @var int
	 */
	public $newToJoomlaUsers;
	/**
	 * show total no of users in Joomla not sync with ExApp
	 * @var int
	 */
	public $newToExAppUsers;


	public function __construct($appName,$total,$toJ,$toEx){
		$this->appName=$appName;
		$this->totalUsers=$total;
		$this->newToJoomlaUsers=$toJ;
		$this->newToExAppUsers=$toEx;
	}
}