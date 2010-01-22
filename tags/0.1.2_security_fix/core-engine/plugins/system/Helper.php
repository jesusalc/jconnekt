<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/


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
		$appID=(int)$appID;
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
		
		
		$appID=(int)$appID;

		$metaKey=$db->quote($metaKey);
		$value=$db->quote($value);
		
		if(isset($meta)){
			//update
			$sql="UPDATE #__jc_meta SET value=$value WHERE appID=$appID AND metaKey=$metaKey";
			$db->Execute($sql);
		}
		else{
			//insert
			$sql="INSERT INTO #__jc_meta(appID,metaKey,value) VALUES ".
				"($appID,$metaKey,$value)";
			$db->Execute($sql);
		}

		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}
	}

	public static function getAppID($appName){
		
		$db =& JFactory::getDBO();
		
		$appName=$db->quote($appName);
		$sql="SELECT appID FROM #__jc_exApps WHERE " .
			"appName=$appName";
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
			"appID=".(int)$appID;
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
					"VALUES ("
						.$db->quote($username).","
						.$db->quote($username).",".
						$db->quote($email).",".
						$db->quote($password).",$gid,".
						$db->quote($groupname).",1)";
		//$userId=JUserHelper::getUserId()
		$db->Execute($sql);
		//2345 indicates a fatal error
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		//get again the userID
		$userID=JUserHelper::getUserId($username);

		//add to acl
		$sql="INSERT INTO #__core_acl_aro(section_value,value,name) ".
					"VALUES ('users',$userID,".$db->quote($username).")";
		$db->Execute($sql);
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		$sql="SELECT id FROM #__core_acl_aro WHERE value=$userID";
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

		$sql="UPDATE #__users SET username=".$db->quote($username);
		if($email) $sql.=", email=".$db->quote($email);
		if($password) $sql.=", password=".$db->quote($joomlaPassword);
		if($groupname) $sql.=", gid=$gid, usertype=".$db->quote($groupname);
		$sql.="WHERE id=$userID";

		$db->Execute($sql);
		if($db->getErrorMsg()) throw new Exception($db->getErrorMsg(),2345);

		if($groupname){
			//update acl group mappings...
			$sql="SELECT id FROM #__core_acl_aro WHERE value=".(int)$userID;
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