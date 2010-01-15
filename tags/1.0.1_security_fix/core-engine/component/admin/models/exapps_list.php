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

class JconnectModelExapps_list extends JModel{
	/**
	 * 
	 * get the list of ExApps to display in the grid...
	 * @return list of ExApps (appName,host,path,port)
	 */
	public function getExApps(){
		$sql="SELECT appID,appName,host,path,port,published FROM #__jc_exApps ORDER BY appID ASC";
		return $this->_getList($sql);
	}
	
	public function remove(){
		$appID=JRequest::getVar("cid",array(),"post","array");
		$idList="";
		//making a list of id's seperated by comma.
		foreach($appID as $id=>$val){
			$idList.=(int)$val.",";
		}
		//remove the last comma
		if($idList) $idList=substr($idList,0,strlen($idList)-1);
		
		//delete in ExApps
		$sql="DELETE FROM #__jc_exApps WHERE appID in ($idList)";
		$this->_db->Execute($sql);
		if($this->_db->getErrorMsg()){
			$this->setError(array($this->_db->getErrorMsg()));
			return false;
		}
		
		//delete in meta
		$sql="DELETE FROM #__jc_meta WHERE appID in ($idList)";
		$this->_db->Execute($sql);
		if($this->_db->getErrorMsg()){
			$this->setError(array($this->_db->getErrorMsg()));
			return false;
		}
		
		//delete in syncUsers
		$sql="DELETE FROM #__jc_syncUsers WHERE appID in ($idList)";
		$this->_db->Execute($sql);
		if($this->_db->getErrorMsg()){
			$this->setError(array($this->_db->getErrorMsg()));
			return false;
		}
		return true;
	}
}