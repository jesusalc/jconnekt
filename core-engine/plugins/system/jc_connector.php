<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/


define(JC_API,1);

/**
 * This is the actual class contains the Connector API for JConnect ExApps
 *
 * @package		JConnect.plugins.system
 * @since 		1.0
 */
class ExApp{
	public $appName;
	public $secretKey='99027k';
	public $host='localhost';
	public $path='/gsoc/JConnect/exApps/fake/server.php';
	public $port=80;
	public $authKey;
	public $cryptKey;

	private $client;
	private $cookies ;

	public function __construct($appname){
		$this->loadExApp($appname);
	}

	private function loadExApp($appName){
		$appID=0;
		if(is_int($appName)) $appID=$appName;
		else if(is_string($appName)) $appID=JCHelper::getAppID($appName);

		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__jc_exApps WHERE appID=$appID";
		$db->setQuery($query);
		$app=$db->loadObject();

		if(!$app) throw new Exception("ExApp name is invalid");

		$this->appName=$app->appName;
		$this->secretKey=$app->secretKey;
		$this->host=$app->host;
		$this->path=$app->path;
		$this->port=$app->port;
		
		$keys=explode("::",$app->secretKey);
		
		$this->authKey=$keys[0];
		$this->cryptKey=$keys[1];
	}


	public function deleteUser($username){

		return $this->callMethod("deleteUser",array('username'=>$username));
	}

	public function getUserDetails($chunkSize,$chunkNo){
		return $this->callMethod("getUserDetails",array(
			'chunkSize'=>$chunkSize,
			'chunkNo'=>$chunkNo));
	}

	public function getUsers($usernameList=array()){
		return $this->callMethod("getUsers",array(
			'usernameList'=>$usernameList));
	}


	public function  getUserCount(){
		return $this->callMethod("getUserCount",array());
	}

	/**
	 * The returning html data will be displayed for public view
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 @return - html data(public) in a intArray
	 */
	public function  getPublicView(){
		return $this->callMethod("getPublicView",array());
	}

	/**
	 * The returning html data will be displayed when particular user-logged in..
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 @return - html data(private) in a intArray
	 */
	public function  getPrivateView($username){
		return $this->callMethod("getPrivateView",array('username'=>$username));
	}

	/**
	 This is the function probabaly called by the JConnect to inform exApp about jconnect details...
	 like host,port,path it's xmlrpc server stayes..

	 @parama $meta - array containing jconnect info...
	 possible keys are..
	 JC_PATH,JC_HOST,JC_PORT,APPNAME,PARAMS
	 PARAMS -contains array of parameters for the path
	 */
	public function loadSysInfo($meta){
		return $this->callMethod('loadSysInfo',array('meta'=>$meta));
	}

	/**
		get user group from the ExApp
	 */
	public function getUserGroups(){
		return $this->callMethod('getUserGroups',array());
	}

	private function callMethod($action,$paramArray){
		//generating hash value and send-it...
		$hmac_hash=$this->hmac_gen($paramArray);
		
		$paramArray['hmacHash']=$hmac_hash;
		$endpoint="http://{$this->host}:{$this->port}{$this->path}";
		//$call=$endpoint."?&action=$action&json=".json_encode($paramArray);
		$res=$this->sendRequest("$endpoint?",$action,json_encode($paramArray));
		$res=json_decode($res,true);
		if($res->result==0){
			return $res['data'];
		}
		else{
			throw new Exception($res->message,$res->no);
		}
	}

	/**
		This will do the actual request (with the channel)
		@param $endpoint request endpoint url (with ? )
		@param $action action to calling
		@param $json json data..
		
	 */
	private function sendRequest($endpoint,$action,$json){
		$res;
		if(function_exists('curl_init')){
			$ch = curl_init("$endpoint");
			$params=urlencode("action").'='.urlencode($action)."&";
			$params.=urlencode("json").'='.urlencode($json);
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $params);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        $res = stripcslashes(curl_exec($ch));       
	        curl_close($ch);
	        
		}
		else{
			$res=file("{$endpoint}&action=$action&json=$json");
			$res=stripslashes(implode("\n",$res));
			
		}
		
		return $res;
	}
	/**
	@param $params - the parameters work as the message in HMAC
	@return string - hmac hash
	*/
	private function hmac_gen($params){
		$message="";
		foreach($params as $param){
			if(is_array($param) || is_object($param)){
				$message.=json_encode($param);
			}
			else{
				$message.=$param;
			}
		}
		
		return hash_hmac("md5",$message,$this->authKey);
	}

	public static function getExAppList(){
		$db =& JFactory::getDBO();
		$query = "SELECT appName FROM #__jc_exApps";
		$db->setQuery($query);
		return $db->loadResultArray();
	}

}

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
		$sql="SELECT * FROM #__jc_syncUsers WHERE JID=$JID AND appID=$appID";
		$this->db->setQuery($sql);
		$res=$this->db->loadObject();
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

		$this->appID=$appID;
		$this->JID=$JID;
		$this->status=$res->status;
	}

	public function save(){
		$sql="INSERT INTO #__jc_syncUsers (JID,appID,status) VALUES (".
		"$this->JID,$this->appID,'$this->status') ON DUPLICATE KEY UPDATE ".
		"status=VALUES(status)";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

	}

	public function delete(){
		$sql="DELETE FROM #__jc_syncUsers WHERE JID=$this->JID AND appID=$this->appID";
		$this->db->Execute($sql);
		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
	}

	public static function  contains($userID,$appname){
		$db = JFactory::getDBO();
		$query = "SELECT su.* FROM #__jc_syncUsers su INNER JOIN #__jc_exApps e ON su.appID=e.appID ".
		" WHERE su.JID=$userID AND e.appName='$appname'";

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


class JCHelper{
	public static
	$INCOMING=64,
	$OUTGOING=128;
	/**
		This is used to check the enablity of a ExApp
		depend on the place it'll give the permission using the meta values(allow_incoming,...)
		that will only check if exApp is enabled ....
		@param place - is when we are using this (@ incoming or outgoing)
	 */
	public static function isExAppEnabled($appID,$place=null){
		if(is_string($appID)) $appID=JCHelper::getAppID($appID);
		else if(!isset($appID)) $appID=0;
		$db=JFactory::getDBO();
		$sql="SELECT published FROM #__jc_exApps WHERE appID=$appID";
		$db->setQuery($sql);
		$res=$db->loadObject();
		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}

		//using place
		if($place && $res->published==1){
			if($place==JCHelper::$INCOMING){
				$val=(int)JCHelper::getMeta($appID,"allow_incoming");
				return ($val==0)? false:true;
			}
			else if($place==JCHelper::$OUTGOING){
				$val=(int)JCHelper::getMeta($appID,"allow_outgoing");
				return ($val==0)? false:true;
			}
		}

		return ($res->published==0)?false:true;
	}
	//
	/**
	get the meta values from the meta key..
	@param $appName-string appName or int appId
	*/
	public static function getMeta($appName,$metaKey){
		$db=JFactory::getDBO();
		$appID=0;
		if(is_int($appName)) $appID=$appName;
		else if(is_string($appName)) $appID=JCHelper::getAppID($appName);
		$sql="SELECT value FROM #__jc_meta" .
			" WHERE appID=$appID AND metakey='$metaKey'";
		$db->setQuery($sql);
		$res=$db->loadObject();
		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}

		if($res){
			return $res->value;
		}else{
			return null;
		}
	}

	/**
	@param - string appName corosponding appID
	*/
	static function setMeta($appID,$metaKey,$value){
		if(is_string($appID)) $appID=JCHelper::getAppID($appID);
		else if(!isset($appID)) $appID=0;
		$db=JFactory::getDBO();
		$meta=JCHelper::getMeta($appID,$metaKey);

		if(isset($meta)){
			//update
			$sql="UPDATE #__jc_meta SET value='$value' WHERE appID=$appID AND metaKey='$metaKey'";
			$db->Execute($sql);
		}
		else{
			//insert
			$sql="INSERT INTO #__jc_meta(appID,metaKey,value) VALUES ".
				"($appID,'$metaKey','$value')";
			$db->Execute($sql);
		}

		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}
	}

	public static function getAppID($appName){
		$db =& JFactory::getDBO();
		$sql="SELECT appID FROM #__jc_exApps WHERE " .
			"appName='$appName'";
		$db->setQuery($sql);
		$res=$db->loadObject();
		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}

		if($res){
			return (int)$res->appID;
		}else{
			return false;
		}
	}

	/**
		This get a diff from 2d and 1d array (2d array's 1'st element will be checked)
		@param $arr2D-TwoD array
		@param $arr1D-OneD array
	 */
	public static function array_diff_2d_1d($arr2D,$arr1D){
		$res=array_udiff($arr2D,$arr1D,"JCHelper::comp");
		$rtn=array();
		foreach ($res as $val){
			array_push($rtn,$val);
		}
		return $rtn;
	}

	public static function comp($a,$b){

		if(is_array($a)){
			if(is_array($b)){
				return strcmp($a[0],$b[0]);
			}
			else{
				return strcmp($a[0],$b);
			}
		}
		else{
			if(is_array($b)){
				return strcmp($a,$b[0]);
			}
			else{
				return strcmp($a,$b);
			}
		}
	}

	/**
		This get a diff from 2d and 2d array (2d array's 1'st element will be checked)
		@param $arr2D-TwoD array
		@param $arr2D-OneD array

		@return a One D array of result
	 */
	public static function array_diff_2d_2d($arr2D,$arr1D){
		$res=array_udiff($arr2D,$arr1D,"JCHelper::comp2");
		$rtn=array();
		foreach ($res as $val){
			array_push($rtn,$val);
		}
		return $rtn;
	}

	public static function comp2($a,$b){
		return strcmp($a[0],$b[0]);
	}

	/**
		This is a implode func for 2D array
	 */
	public static function implodeArray2D ($sep, $array, $key){
			
		$num = count($array);
		$str ="";
			
		for ($i = 0; $i < $num; $i++){

			if ($i){
				$str .= $sep;
			}

			$str .= $array[$i][$key];
		}
			
		return $str;
			
	}

	/**
		this is wrapper to php array_diff cause it returns the array with original keys
	 */
	public static  function array_diff($arr1,$arr2){
		$res=array_diff($arr1,$arr2);
		$rtn=array();
		foreach($res as $val){
			array_push($rtn,$val);
		}

		return $rtn;
	}

	/**
		this is wrapper to php array_intersect cause it returns the array with original keys
	 */
	public static function array_intersect($arr1,$arr2){
		$res=array_intersect($arr1,$arr2);
		$rtn=array();
		foreach($res as $val){
			array_push($rtn,$val);
		}

		return $rtn;
	}

	public static function getAppName($appID){
		$db =& JFactory::getDBO();
		$sql="SELECT appName FROM #__jc_exApps WHERE " .
			"appID=$appID";
		$db->setQuery($sql);
		$res=$db->loadObject();
		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}

		if($res){
			return $res->appName;
		}else{
			return false;
		}
	}

	/**
	 * Extract ExApp from the OpenID
	 * @return unknown_type
	 */
	public static function extractExApp($trustRoot){
		$exAppList=ExApp::getExAppList();
		foreach($exAppList as $exAppName){
			$exApp=new ExApp($exAppName);
			$url="{$exApp->host}:{$exApp->port}{$exApp->path}";
			var_dump("$trustRoot :: $url");
		}
	}

	public static function getUserGroupCheckBox($name,$selected){

		$id=md5(rand());
		$rtn= "<select id='$id'name='$name'>";
		$rtn.= "<option value='Registered'>-Registered</option>";
		$rtn.= "<option value='Author'>  -Author</option>";
		$rtn.= "<option value='Editor'>    -Editor</option>";
		$rtn.= "<option value='Publisher'>      -Publisher</option>";
		$rtn.= "<option value='Manager'>-Manager</option>";
		$rtn.= "<option value='Administrator'>  -Administrator</option>";
		$rtn.= "<option value='Super Administrator'>  -Super Administrator</option>";
		$rtn.= "</select>";

		$rtn.= "<script type='text/javascript'>".
		"document.getElementById('$id').value='$selected'".
		"</script>";

		return $rtn;
	}

	/**
	@param $data - array('value'=>'lable')
	@param $name - name of the checkBox
	@param $selected value to be selected intially..
	*/
	public static function getCheckBox($data,$name,$selected){

		$rtn= "<select id='$id'name='$name'>";
		foreach ($data as $value=>$label){
			$rtn.= "<option ";
			if($selected==$value) $rtn.=" selected='true'";
			$rtn.="value='$value'>$label</option>";
		}
		$rtn.= "</select>";

		return $rtn;
	}

	public static function createJoomlaUser($username,$email,$password,$groupname){
		$db=JFactory::getDBO();
		jimport("joomla.user.helper");

		//check for existing user...
		$userID=JUserHelper::getUserId($username);
		if($userID) throw new Exception("`$username` already exists... ");

		$acl =& JFactory::getACL();
		$gid = $acl->get_group_id( '', $groupname, 'ARO' );

		//generate the password..
		$salt  = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($password, $salt);
		$password= $crypt.':'.$salt;

		//earlier we did this using JUser Object but didn't work well....
		//add the users...
		$sql="INSERT INTO #__users(name,username,email,password,gid,usertype,sendEmail) ".
					"VALUES ('$username','$username','$email','$password',$gid,'$groupname',1)";
		//$userId=JUserHelper::getUserId()
		$db->Execute($sql);
		//2345 indicates a fatal error
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		//get again the userID
		$userID=JUserHelper::getUserId($username);

		//add to acl
		$sql="INSERT INTO #__core_acl_aro(section_value,value,name) ".
					"VALUES ('users','$userID','$username')";
		$db->Execute($sql);
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		$sql="SELECT id FROM #__core_acl_aro WHERE value='$userID'";
		$db->setQuery($sql);
		$res=$db->loadObject();
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		//add to acl group map...
		$sql="INSERT INTO #__core_acl_groups_aro_map (group_id,aro_id) ".
					"VALUES($gid,$res->id)";
		$db->Execute($sql);
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);
	}

	public static function updateJoomlaUser($username,$email,$password,$groupname){
		$db=JFactory::getDBO();
		jimport("joomla.user.helper");

		//check for existing user...
		$userID=JUserHelper::getUserId($username);
		if(!$userID) throw new Exception("`$username` does not already exists... ");

		$acl =& JFactory::getACL();
		$gid = $acl->get_group_id( '', $groupname, 'ARO' );

		//generate the password..
		$salt  = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($password, $salt);
		$joomlaPassword= $crypt.':'.$salt;

		$sql="UPDATE #__users SET username='$username' ";
		if($email) $sql.=", email='$email' ";
		if($password) $sql.=", password='$joomlaPassword' ";
		if($groupname) $sql.=", gid=$gid, usertype='$groupname' ";
		$sql.="WHERE id=$userID";

		$db->Execute($sql);
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		if($groupname){
			//update acl group mappings...
			$sql="SELECT id FROM #__core_acl_aro WHERE value='$userID'";
			$db->setQuery($sql);
			$res=$db->loadObject();
				
			if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);
			$sql="UPDATE #__core_acl_groups_aro_map SET group_id=$gid WHERE aro_id=$res->id";
			$db->Execute($sql);
			if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);
		}
	}
	
	public static function isUserBanned($userid,$appId){
		
		$su=new SyncUser($userid,$appId);
		var_dump($su);
		return (isset($su) && $su->status=="BAN")?true:false;
	}

}


?>