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
class SyncUser{
	var $appID;
	var $JID;
	var $status;
	private $db;

	public function __construct($JID=null,$appID=null){
		$this->db=JFactory::getDBO();
		if($JID==null || $appID==null) return;
		$sql='SELECT * FROM #__jc_syncUsers WHERE JID='.(int)$JID.' AND appID='.(int)$appID;
		$this->db->setQuery($sql);
		$res=$this->db->loadObject();
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

		$this->appID=$appID;
		$this->JID=$JID;
		$this->status=$res->status;
	}

	public function save(){
		$sql='INSERT INTO #__jc_syncUsers (JID,appID,status) VALUES ('.
		(int)$this->JID.','.(int)$this->appID.','.$this->db->quote($this->status).') ON DUPLICATE KEY UPDATE '.
		'status=VALUES(status)';
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

	}

	public function delete(){
		$sql='DELETE FROM #__jc_syncUsers WHERE JID='.(int)$this->JID.' AND appID='.(int)$this->appID;
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
	}

	public static function  contains($userID,$appname){
		$db = JFactory::getDBO();
		$query = 'SELECT su.* FROM #__jc_syncUsers su INNER JOIN #__jc_exApps e ON su.appID=e.appID '.
		' WHERE su.JID='.(int)$userID.' AND e.appName='.$db->quote($appname);

		$db->setQuery($query);
		$syncUser=$db->loadObject();
		if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		if($syncUser){
			return true;
		}
		else{
			return false;
		}
	}
}
