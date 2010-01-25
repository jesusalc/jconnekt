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
	public function insert($access_token,$request_token,$app_id,$timestamp,$user_id){
		$sql='INSERT INTO #__jc_tokens(access_token,request_token,app_id,timestamp,user_id) VALUES '.
			'('.$this->_db->quote($access_token).
			','.$this->_db->quote($request_token).
			','.(int)$app_id.','.(int)$timestamp.','.(int)$user_id.') '.
			'ON DUPLICATE KEY UPDATE '.
			'request_token=values(request_token), '.
			'timestamp=values(timestamp), '.
			'app_id=values(app_id), '.
			'user_id=values(user_id)';
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		return true;
	}
	
	public  function  delete($access_token){
		$sql='DELETE FROM #__jc_tokens WHERE access_token='.
			$this->_db->quote($access_token);
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		return true;	
	}
	
	public  function  get($access_token){
		$sql='SELECT * FROM #__jc_tokens WHERE access_token='.
			$this->_db->quote($access_token);
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObject();
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
			
		return $res;
	}
	
	/**
	 * @param $request_token - the request token
	 * @param $exApp - exApp Object
	 *
	 */
	public function generate_access_token($request_token,$exApp){
		return hash_hmac('md5',$request_token,$exApp->authKey);
	}
	
	public function delete_by_request_token($request_token){
		$sql='DELETE FROM #__jc_tokens WHERE request_token='.
			$this->_db->quote($request_token);
		$this->_db->Execute($sql);
		if($this->_db->getErrorNum()) 
			throw new Exception($this->_db->getErrorMsg());
		return true;	
	}
	
	//generate token and stores in the cookie..
	public function get_request_token(){
		$request_token=$_COOKIE['jconnekt_token'];
		if(!isset($request_token)){
			$request_token=md5(rand());
			setcookie('jconnekt_token',$request_token,null,"/");
		}
		
		return $request_token;
	}
}