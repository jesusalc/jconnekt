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

class JconnectModelExapps_form extends JModel{
	
	public function getExApp(){
		$appIDs=JRequest::getVar("cid",array(),'','array');
		if(isset($appIDs[0])){
			//for edit...
			$sql="SELECT * FROM #__jc_exApps where appID='$appIDs[0]'";
			$db=JFactory::getDBO();
			$db->setQuery($sql);
			return $db->loadObject();
		}	
		else{
			//for a new one
			$exApp=new stdClass();
			$exApp->appName="";
			$exApp->secretKey="";
			$exApp->host="";
			$exApp->path="";
			$exApp->port=80;
			
			return $exApp;
		}
	}
	
	public function getAllMeta($appID){
		$appID=(int)$appID;
		$meta=new stdClass();
		$meta->IP=JCHelper::getMeta($appID,"IP");
		$meta->create_user=JCHelper::getMeta($appID,"create_user");
		$meta->delete_user=JCHelper::getMeta($appID,"delete_user");
		$meta->update_user=JCHelper::getMeta($appID,"update_user");
		$meta->allow_incoming=JCHelper::getMeta($appID,"allow_incoming");
		$meta->allow_outgoing=JCHelper::getMeta($appID,"allow_outgoing");
		
		if(isset($meta->recursive_insert)) $meta->recursive_insert="allow"; //deny
		if(isset($meta->recursive_delete)) $meta->recursive_delete="deny"; //allow
		if(isset($meta->username_conflict)) $meta->username_conflict="update"; //ignore
		
		return $meta;
	}
	
	protected function setAllMeta(){
		$appID=JRequest::getInt("appID");
		
		$meta=JRequest::getVar("meta",array(),"post","array");
		if(empty($meta)){
			$meta=array();
			$meta['allow_outgoing']=1;
			$meta['allow_incoming']=1;
			$meta['update_user']='allow';
			$meta['create_user']='allow';
			$meta['delete_user']='allow';
			$meta['IP']='';
		}
		
		foreach($meta as $metaKey=>$metaVal){
			JCHelper::setMeta((int)$appID,$metaKey,$metaVal);
		}
		
	}
	public function store(){
		$record=$this->getTable("exapps");
		$data=JRequest::get("post");
		
		
		if(!$record->save($data)){
			$this->setError($record->getErrors());
			return false;
		}
		//set the newly created records ID to request variable
		//this will be used in the controller...wer
		
		JRequest::setVar("appID",$record->appID);
		
		//set Meta Values
		$this->setAllMeta();
		
		//set userGroup..
		$this->setUserGroups();
		return true;
	}
	
	private function setUserGroups(){
		$appID=JRequest::getInt('appID');
		$sql="DELETE FROM #__jc_groups_in WHERE appID=$appID";
		$this->_db->Execute($sql);
		
		$jcGroupsIn=JRequest::getVar('jcGroupsIn',array(),'',"array");
		foreach ($jcGroupsIn as $exApp=>$joomla){
			$ug=new JCGroupIn($appID,$exApp);
			$ug->joomlaGroup=$joomla;
			$ug->save();
		}
		
		$sql="DELETE FROM #__jc_groups_out WHERE appID=$appID";
		$this->_db->Execute($sql);
		$jcGroupsOut=JRequest::getVar('jcGroupsOut',array(),'',"array");
		foreach ($jcGroupsOut as $joomla=>$exApp){
			$ug=new JCGroupOut($appID,$joomla);
			$ug->exAppGroup=$exApp;
			$ug->save();
		}
	}
	
	
	/**
	 * this is used to create the check box for joomla Groups..
	 * @return array('usergroup'=>'label');
	 */
	public function getJoomlaGroups(){
		return array('Registered'=>'Registered',
		'Author'=>'  -Author',
		'Editor'=>'    -Editor',
		'Publisher'=>'      -Publisher',
		'Manager'=>'-Manager',
		'Administrator'=>'  -Administrator',
		'Super Administrator'=>'  -Super Administrator');
	}
	
	/**
	 * this is used to create the check box for exApp Groups..
	 * @return array('usergroup'=>'label');
	 */
	public function getExAppGroups($appID){
		$exApp=new ExApp((int)$appID);
		$groups=$exApp->getUserGroups();
		$rtn=array();
		if(is_array($groups) && count($groups) >0){
			foreach ($groups as $group){
				$rtn["$group"]=$group;
			}
		}
		
		return $rtn;
	}
	
	/**
	 * return exApp userGroups with their current joomla group mapping..
	 * @return array as array('joomlaGroups'=>'exAppGroup');
	 */
	public function getJCGroupOutMap($appID){
		$jGroups=array('Registered','Author','Editor','Publisher',
			'Manager','Administrator','Super Administrator');
		$mappings=array();
			foreach ($jGroups as $group){
				$userGruop=new JCGroupOut($appID,$group);
				$exAppGroup=($userGruop->exAppGroup)?$userGruop->exAppGroup:null;
				$mappings["$group"]=$exAppGroup;
			}
		
		return $mappings;
	}
	
	/**
	 * return joomla userGroups with their current exApp group mapping..
	 * Enter description here...
	 * @return array as array('exAppGruop'=>'joomlaGroup');
	 */
	public function getJCGroupInMap($appID){
		try{
			$exApp=new ExApp((int)$appID);
			$exAppGroups=$exApp->getUserGroups();
			$mappings=array();
			
			if(is_array($exAppGroups) && count($exAppGroups) > 0){
				foreach ($exAppGroups as $group){
					$userGruop=new JCGroupIn($appID,$group);
					$joomlaGroup=($userGruop->joomlaGroup)?$userGruop->joomlaGroup:'Registered';
					$mappings["$group"]=$joomlaGroup;
				}
			}
		
			return $mappings;
		}
		catch(Exception $ex){
			JError::raiseWarning(0,$ex->getMessage());
		}
	}
	
	protected function setMeta($appID,$metaKey,$value){
		$meta=$this->getMeta($appID,$metaKey);
		if($meta){
			//update	
			$sql="UPDATE #__jc_meta SET value='$value' WHERE appID=$appID AND metaKey='$metaKey'";
			$this->_db->Execute($sql);
		}
		else{
			//insert
			$sql="INSERT INTO #__jc_meta(appID,metaKey,value) VALUES ".
				"($appID,'$metaKey','$value')";
			$this->_db->Execute($sql);
		}
		
		if($this->_db->getErrorNum()){
			throw new Exception($this->_db->getErrorNum() ." :: ". $this->_db->getErrorMsg());
		}
	}
	
	public function test(){
		$this->setMeta(12,"recursive_insert","deny");
		echo $this->getMeta(12,"recursive_insert");
		ddd();
	}
}