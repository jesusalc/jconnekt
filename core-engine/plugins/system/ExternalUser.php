<?php 
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

/**
 * This is the class which overlooked the userSync table
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class ExternalUser{
	var $ownerAppID;
	var $JID;
	var $username;
	var $needSync;
	private $db;

	public function __construct($JID=null){
		$this->db=JFactory::getDBO();
		if($JID==null ) return;
		$sql="SELECT * FROM #__jc_externalUsers WHERE JID=$JID";
		$this->db->setQuery($sql);
		$res=$this->db->loadObject();
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

		$this->ownerAppID=$res->ownerAppID;
		$this->JID=$JID;
		$this->username=$res->username;
		$this->needSync=$res->needSync;
	}

	public function save(){
		$sql="INSERT INTO #__jc_externalUsers (JID,ownerAppID,username,needSync) VALUES (".
		"$this->JID,$this->ownerAppID,'$this->username',$this->needSync) ON DUPLICATE KEY UPDATE ".
		"username=VALUES(username),ownerAppID=VALUES(ownerAppID),needSync=VALUES(needSync)";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

	}

	public function delete(){
		$sql="DELETE FROM #__jc_externalUsers WHERE JID=$this->JID";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
	}

	public static function  contains($userID){
		$db = JFactory::getDBO();
		$query = "SELECT eu.* FROM #__jc_externalUsers eu WHERE eu.JID=$userID";

		$db->setQuery($query);
		$externalUser=$db->loadObject();
		if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		if($externalUser){
			return true;
		}
		else{
			return false;
		}
	}
}
