<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/


/**
 * This is the class which overlooked the Incoming user groups....jos_jc_groups_in
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class JCGroupIn{
	var $appID;
	var $exAppGroup;
	var $joomlaGroup;
	private $db;

	public function __construct($appID=null,$exAppGroup=null){
		$this->db=JFactory::getDBO();
		if($exAppGroup==null || $appID==null) return;
		$sql="SELECT * FROM #__jc_groups_in WHERE exAppGroup='$exAppGroup' AND appID=$appID";
		$this->db->setQuery($sql);
		$res=$this->db->loadObject();
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

		$this->appID=$appID;
		$this->exAppGroup=$exAppGroup;
		$this->joomlaGroup=$res->joomlaGroup;
	}

	public function save(){
		$sql="INSERT INTO #__jc_groups_in (appID,exAppGroup,joomlaGroup) VALUES (".
		"$this->appID,'$this->exAppGroup','$this->joomlaGroup') ON DUPLICATE KEY UPDATE ".
		"joomlaGroup=VALUES(joomlaGroup)";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

	}

	public function delete(){
		$sql="DELETE FROM #__jc_groups_in WHERE exAppGroup='$this->exAppGroup' AND appID=$this->appID";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
	}
}

/**
 * This is the class which overlooked the Outgoing user groups....jos_jc_groups_in
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class JCGroupOut{
	var $appID;
	var $exAppGroup;
	var $joomlaGroup;
	private $db;

	public function __construct($appID=null,$joomlaGroup=null){
		$this->db=JFactory::getDBO();
		if($joomlaGroup==null || $appID==null) return;
		$sql="SELECT * FROM #__jc_groups_out WHERE joomlaGroup='$joomlaGroup' AND appID=$appID";
		$this->db->setQuery($sql);
		$res=$this->db->loadObject();
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

		$this->appID=$appID;
		$this->exAppGroup=$res->exAppGroup;
		$this->joomlaGroup=$joomlaGroup;
	}

	public function save(){
		$sql="INSERT INTO #__jc_groups_out (appID,exAppGroup,joomlaGroup) VALUES (".
		"$this->appID,'$this->exAppGroup','$this->joomlaGroup') ON DUPLICATE KEY UPDATE ".
		"exAppGroup=VALUES(exAppGroup)";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

	}

	public function delete(){
		$sql="DELETE FROM #__jc_groups_out WHERE joomlaGroup='$this->joomlaGroup' AND appID=$this->appID";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
	}
}
