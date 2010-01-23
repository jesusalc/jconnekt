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

class JconnectModelAuth extends JModel {
	
	function getPublicKey($appName,$userID){
		$exApp=new ExApp($appName);
		$appID=JCHelper::getAppID($appName);
		$publicKey=md5(rand());
		$privateKey=hash_hmac("md5",$publicKey.$appName,$exApp->authKey);
		$timestamp=time();
		$session_id=JFactory::getSession()->getId();
		$sql="INSERT INTO #__jc_auth_key(userID,appID,privateKey,used,timestamp,session_id) VALUES ".
			"($userID,$appID,'$privateKey',0,$timestamp,'$session_id')";
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
		 throw new Exception($this->_db->getErrorMsg());
		 
		return $publicKey;
	}
	
	/**
		Validate the privateKey on these criterias...
		  availability,already used,time
		  
		 @return colum of #__jc_auth_key table if success else false
	 */
	function validate($privateKey){
		$VALIDITY_PERIOD=1000*60*5;
		$sql="SELECT * FROM #__jc_auth_key WHERE privateKey='$privateKey'";
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObject();
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		//availability check
		$availability=($res->privateKey)?true:false;
		
		//already used check
		$notUsed=($res->used==0)?true:false;
		
		//time check
		$timeValidity=($res->timestamp<time()+$VALIDITY_PERIOD)?true:false;
		
		$validity=$availability && $notUsed && $timeValidity;
		if($validity){
			$sql="UPDATE #__jc_auth_key SET used=1 WHERE privateKey='$privateKey'";
			$this->_db->Execute($sql);
			if($this->_db->getErrorNum()) 
				throw new Exception($this->_db->getErrorMsg());
			return $res;
		}
		else{
			return false;
		}
	}
}