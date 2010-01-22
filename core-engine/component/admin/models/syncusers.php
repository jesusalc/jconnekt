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

class JconnectModelSyncusers extends JModel{

	public function __construct(){
		parent::__construct();
		global $mainframe,$option;
		$limit=$mainframe->getUserStateFromRequest("$option.syncusers.limit",
			"limit",$mainframe->getCfg('list_limit'),"int");
		$limitstart=JRequest::getInt("limitstart",0);
		$this->setState("limit",$limit);
		$this->setState("limitstart",$limitstart);

		//Filter states...
		$usernameFilter=$mainframe->getUserStateFromRequest("$option.syncusers.usernameFilter",
			"usernameFilter","","string"); 
		$appNameFilter=$mainframe->getUserStateFromRequest("$option.syncusers.appNameFilter",
			"appNameFilter","*All*","string");
		$stateFilter=$mainframe->getUserStateFromRequest("$option.syncusers.stateFilter",
			"stateFilter","*All*","string");
		$banFilter=$mainframe->getUserStateFromRequest("$option.syncusers.banFilter",
			"banFilter","*All*","string");

		$this->setState("usernameFilter",$usernameFilter);
		$this->setState("appNameFilter",$appNameFilter);
		$this->setState("stateFilter",$stateFilter);
		$this->setState("banFilter",$banFilter);
	}
	/**
	 * This will return the data in the syncUser table align with the pagination details
	 *
	 */
	public function getData(){
		$sql="SELECT SQL_CALC_FOUND_ROWS su.JID,su.appID, u.username,appName,status,needSync FROM ((#__jc_syncUsers su LEFT JOIN #__jc_externalUsers eu ON su.JID=eu.JID) INNER JOIN #__users u ON su.JID=u.id) INNER JOIN #__jc_exApps ea ON ea.appID=su.appID ";

		$where="";
		if($this->getState("usernameFilter")!=""){
			$where.=" u.username LIKE '%".$db->getEscaped( $this->getState("usernameFilter"),true)."%' AND";
		}
		if($this->getState("appNameFilter")!="*All*"){
			$where.=" appName LIKE '%".$db->getEscaped( $this->getState("appNameFilter"),true)."%' AND";
		}
		
		if($this->getState('banFilter')!="*All*"){
			$state=$this->getState('banFilter');
			$where.=" status='$state' AND";
		}
		
		if($this->getState('stateFilter')!="*All*"){
			$state=$this->getState('stateFilter');
			if($state=='exApp') $where.=" needSync=1 AND";
			else if($state=='OK') $where.=" needSync=0 AND";
			else if($state=='NA') $where.=" needSync=null AND";
			else $where.=" AND";
		}

		if(strlen($where)>0) {
			$where=substr($where,0,strlen($where)-3);
			$sql.="WHERE " . $where;
		}

		if($this->getState('limit')>0){
			$limitText="LIMIT ".(int)$this->getState('limitstart').",".(int)$this->getState('limit');
			$sql.=$limitText;
		}
		
		$data=$this->_getList($sql);
		$this->_db->setQuery("SELECT FOUND_ROWS();");
		$total=$this->_db->loadResult();
		if($this->_db->getErrorNum()) throw new Exception($this->_db->getErrorMsg());

		$this->setState("total",$total);
		return $data;
	}

	public function getPagination(){
		jimport('joomla.html.pagination');
		$pagi=new JPagination(
		(int)$this->getState("total"),
		(int)$this->getState("limitstart"),
		(int)$this->getState("limit")
		);

		return $pagi;
	}
}

/*SELECT SQL_CALC_FOUND_ROWS u.username,appName,su.status FROM 
((jos_jc_syncUsers su INNER JOIN jos_jc_exApps ea ON ea.appID=su.appID)
INNER JOIN jos_users u ON su.JID=u.id )


 * 
 */