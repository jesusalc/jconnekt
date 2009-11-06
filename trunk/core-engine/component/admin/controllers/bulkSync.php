<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * This class controlles handle the bulk synchronization [exAppsList,exAppsForm]
 *
 * @package    JConnect.component.admin.controllers
 */

class JconnectControllerBulkSync extends JController{
	public function __construct(){
		parent::__construct();
	}
	
	public function display(){
		$model=$this->getModel("bulksync");
		$view = & $this->getView("bulksync_list", "html");
		$view->setModel($model,true);
		$view->display();
	}
	
	public function closeSync(){
		$this->setRedirect("index.php?option=com_jconnect");
	}
	
	public function startSync(){
		$model=$this->getModel("bulksync");
		$appNames=JRequest::getVar("cid",array(),"array");
		$result=$model->startSync($appNames[0]);
		
		$model=new JModel();
		$model->set("result",$result);
		$view = & $this->getView("bulksync_results", "html");
		$view->setModel($model,true);
		$view->display();
	}
	
	public function closeResults(){
		$session=JFactory::getSession();
		$session->clear("conflicts");
		$this->setRedirect("index.php?option=com_jconnect&controller=bulkSync");
	}
	
	public function resolveConflicts(){
		$option=JRequest::getVar("conflict_option");
		$appName=JRequest::getVar("appName");
		$appID=JCHelper::getAppID($appName);
		$exApp=new ExApp($appName);
		$session=JFactory::getSession();
		$conflicts=$session->get("conflicts",array());
		$db=JFactory::getDBO();
		foreach($conflicts as $username){
			if($option==BulkSyncConstants::$PRESERVE_JCONNECT){
				$jUser=JUser::getInstance($username);
				$su=new SyncUser($jUser->id,$appID);
				$su->status="OK";
				$su->save();
				
			}else if($option==BulkSyncConstants::$PRESERVE_EXAPP){
				JFactory::getSession()->set("JC_ACTION_ABORT",1);
				$password=substr(md5(rand()),0,25);
				$exUser=$exApp->getUsers(array($username));
				$userGroup=new JCGroupIn($appID,$exUser[0][2]);
				$aclGroup=($userGroup->joomlaGroup)?$userGroup->joomlaGroup:'Registered';
				
				JCHelper::updateJoomlaUser($username,$exUser[0][1],$password,$aclGroup);
				jimport("joomla.user.helper");
				$userID=JUserHelper::getUserId($username);
				
				$su=new SyncUser($userID,$appID);
				$su->status="OK";
				$su->save();
				
				$eu=new ExternalUser($userID);
				$eu->username=$username;
				$eu->ownerAppID=$appID;
				$eu->needSync=1;
				$eu->save();
				
				JFactory::getSession()->clear("JC_ACTION_ABORT");
			}
			else if($option=BulkSyncConstants::$BAN_FOR_EXAPP){
				$jUser=JUser::getInstance($username);
				$su=new SyncUser($jUser->id,$appID);
				$su->status="BAN";
				$su->save();
			}
		}
		
		$session->clear("conflicts");
		$this->setRedirect("index.php?option=com_jconnect&controller=bulkSync","Conflicts resolved for [$appName]");
	}
}

class BulkSyncConstants{
	public static 
		$PRESERVE_JCONNECT=124,
		$PRESERVE_EXAPP=125,
		$BAN_FOR_EXAPP=126
	;
}