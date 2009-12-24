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

class JconnectModelToken extends JModel {
	public function insert($token,$appID,$timestamp){
		$sql="INSERT INTO #__jc_tokens(token,appID,timestamp) VALUES ".
			"('$token','$appID','$timestamp')";
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		return true;
	}
	
	public  function  delete($token){
		$sql="DELETE FROM #__jc_tokens WHERE token='$token'";
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		return true;	
	}
	
	public  function  get($token){
		$sql="SELECT * FROM #__jc_tokens WHERE token='$token'";
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObject();
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
			
		return $res;
	}
}