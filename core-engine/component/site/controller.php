<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'initServer.php' );


jimport('joomla.application.component.controller');


/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */


class JconnectController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	function __construct(){
		parent::__construct();
	}

	function display()
	{
		Endpoint::run();
	}

	
}

class Endpoint{
	//runs the endpoint
	static function run(){
		$registeredActions=array('createUser','updateUser','deleteUser','check_token','query');
		$action=JRequest::getCmd('action');
		if(in_array($action,$registeredActions)){
			$data=json_decode(JRequest::getString('json'),true);
			try{
				Methods::$action($data);
			}
			catch(Exception $ex){
				Endpoint::returnException(256,$ex->getMessage());
			}
		}
		else{
			Endpoint::returnException(2,'Invalid Action');
		}
	}
	
	//return result to the endpoint
	/**
	 * @param $result - result as an array
	 */
	static function returnResult($result){
		$res=array('result'=>0,'data'=>$result);
		echo json_encode($res);
		exit(0);
	}
	
	//return an exception to the endpoint
	static function returnException($no,$message){
		$res=array('result'=>1,'data'=>array('no'=>$no,'message'=>$message));
		echo json_encode($res);
		exit(0);
	}
	
}


class Methods{

	static function createUser($data){
		$appName=$data['appName'];
		$hmac_hash=$data['hmacHash'];
		$username=$data['username'];
		$email=$data['email'];
		$password=$data['password'];
		$group=$data['group'];
	
		
		$fault=authenticate($appName,$hmac_hash,array($username,$email,$password,$group),$data['salt']);
		if(!is_bool($fault) || $fault==false){
			Endpoint::returnException($fault->code,$fault->message);
		}

		//var_dump($username,$email,$password);
		if(!$username || !$email || !$password || !$group){
			Endpoint::returnException(2,"all parameters should be have a value");
		}

		//check for existing user
		$user=JUser::getInstance($username);
		if($user){
			Endpoint::returnException(8,"username already existed");
		}


		//use this because user-plugins could throw some Exceptions
		try{
			//decrypting the password...
			$exApp=new ExApp($appName);
			$password=AESDecryptCtr($password,$exApp->cryptKey,256);
			
			$appID=JCHelper::getAppID($appName);
			//check for recursive_insert option
			$is_create_user=JCHelper::getMeta($appName,"create_user");
			if($is_create_user!="allow") {
				Endpoint::returnException(64,"Not Allowed to Create Users");
			}
				
			jimport("joomla.user.helper");
			//get the group
			$userGroup=new JCGroupIn($appID,$group);
			$aclGroup=($userGroup->joomlaGroup)?$userGroup->joomlaGroup:'Registered';
			JCHelper::createJoomlaUser($username,$email,$password,$aclGroup);
			$userID=JUserHelper::getUserId($username);
			$db=JFactory::getDBO();
			//insert into External User..
			$sql='INSERT INTO #__jc_externalUsers (JID,username,ownerAppID,needSync) '.
					'VALUES ('.
					(int)$userID.','.
					$db->quote($username).','.
					(int)$appID.',0'.
					')';
			$db->Execute($sql);
			if($db->getErrorNum()) throw new Exception($db->getErrorMsg());

			//insert into Sync User
			$sql='INSERT INTO #__jc_syncUsers (JID,appID,status) VALUES ('.
				(int)$userID.','.(int)$appID.",'OK')";
			$db->Execute($sql);
			if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		}
		catch(Exception $ex){
			Endpoint::returnException(128,$ex->getMessage());
		}

		Endpoint::returnResult(true);

	}

	static function updateUser($data){
		$appName=$data['appName'];
		$hmac_hash=$data['hmacHash'];
		$username=$data['username'];
		$email=$data['email'];
		$password=$data['password'];
		$group=$data['group'];
		
		$fault=authenticate($appName,$hmac_hash,array($username,$email,$password,$group),$data['salt']);
		if(!is_bool($fault) || $fault==false){
			Endpoint::returnException($fault->code,$fault->message);
		}

		//check for recursive_delete option
		//means whether allow to delete joomla + other users when a exApp user deleted.
		$is_update_user=JCHelper::getMeta($appName,"update_user");
		if($is_update_user!="allow") {
			Endpoint::returnException(64,"Not Allowed to Update users.");
		}
		
		//check for this user is synced already
		jimport("joomla.user.helper"); 
		try{
			$userID=JUserHelper::getUserId($username);
			if(!$userID) throw new Exception("user not found on joomla",256);
			if(!SyncUser::contains($userID,$appName)){
				Endpoint::returnException(128,"the user is not a synchronized user!");
			}
				
			// get the gid from exApp user Group....$group='user'
			$appID=JCHelper::getAppID($appName);
			
			//check the user is banned or not..
			if(Methods::isUserBan($userID,$appID)){
				Endpoint::returnException(512,"You are banned! not  allowed to update!");
			}
			
			$externalUser=new ExternalUser($userID);
			$aclGroup=null;
			if($externalUser->ownerAppID==$appID){
				$userGroup=new JCGroupIn($appID,$group);
				$aclGroup=($userGroup->joomlaGroup)?$userGroup->joomlaGroup:'Registered';
			}
			
			//decrypting the password...
			$exApp=new ExApp($appName);
			$password=AESDecryptCtr($password,$exApp->cryptKey,256);
			JCHelper::updateJoomlaUser($username,$email,$password,$aclGroup);

			//if the password is a valid one update the exteralUsers needSync into 0
			//and the we need double to check the user is a externalUser
			//and not a native joomla user
			if(ExternalUser::contains($userID)){
				$eu=new ExternalUser($userID);
				$eu->needSync=0;
				$eu->save();
			}
				
		}
		catch(Exception $ex){
			Endpoint::returnException($ex->getCode(),$ex->getMessage());
		}

		Endpoint::returnResult(true);
	}

	static function deleteUser($data){
		$appName=$data['appName'];
		$hmac_hash=$data['hmacHash'];
		$username=$data['username'];
		
		$appID=JCHelper::getAppID($appName);	
		$fault=authenticate($appName,$hmac_hash,array($username),$data['salt']);
		if(!is_bool($fault) || $fault==false){
			Endpoint::returnException($fault->code,$fault->message);
		}

		$user=JUser::getInstance($username);
		if(!$user){
			//check for this user is synced already
			if(!SyncUser::contains((int)$user->id,$appName)){
				Endpoint::returnException(16,"the user is not a synchronized user!");
			}
			Endpoint::returnException(256,"username should be valid");
		}
		
		if(Methods::isUserBan($user->id,$appID)){
				Endpoint::returnException(512,"You are banned! not  allowed to delete!");
		}
		
		//sync_user should be deleted anyway!
		$db=JFactory::getDBO();
		//delete user from the JConnect records..
		$sql='DELETE FROM #__jc_syncUsers WHERE JID='.(int)$user->id;
		
		$db->Execute($sql);
		if($db->getErrorNum()) Endpoint::returnException(4,$db->getErrorMsg());
		
		//check for recursive_delete option
		//means whether allow to delete joomla + other users when a exApp user deleted.
		$is_delete_user=JCHelper::getMeta($appName,"delete_user");
		if($is_delete_user!="allow") {
			Endpoint::returnException(64,"Not Allowed to Delete users.");
		}
		
		if(!$user->delete()){
				Endpoint::returnException(128,$user->getError());
		}
		
		//external user-deleted if the exApp has powers to delete the users..
		$sql='DELETE FROM #__jc_externalUsers WHERE JID='.(int)$user->id;
		$db->Execute($sql);
		if($db->getErrorNum()) Endpoint::returnException(4,$db->getErrorMsg());

		Endpoint::returnResult(true);
	}
	
	/**
		check the access token created using request token for the validity
		if the token available nothing has been changed in the user - level
		otherwise something has happened
	 */
	static function check_token($data){
		$access_token=$data['access_token'];
		$rtn=array('valid'=>Methods::validate_token($access_token));
		Endpoint::returnResult($rtn);
	}
	
	/**
		Query User information if logged in..
		if the user is banned we send array['ban']=true;
	 */
	static function query($info){
		$access_token=$info['access_token'];
		$appName=$info['appName'];
		$rtn=array();
		if(Methods::validate_token($access_token)){
			$model=JModel::getInstance("token","JConnectModel");
			$data=$model->get($access_token);
			$user=JUser::getInstance((int)$data->user_id);
			$userGroup=null;
			
			//check user for he is banned or not!
			$appID=JCHelper::getAppID($appName);
			$syncUser=new SyncUser((int)$data->user_id,$appID);
			if($syncUser->status=="BAN"){
				$rtn=array("ban"=>true);
				Endpoint::returnResult($rtn);
			}
			
			//we are only sending userGroup for users not owned by current ExApp
			if(!ExternalUser::contains($user->id)){
				$userGroup=new JCGroupOut($data->app_id,$user->usertype);
			}
			$rtn=array(
				'username'=>$user->username,
				'email'=>$user->email,
				'user_group'=>$userGroup->exAppGroup,
				'name'=>$user->name
			);
		}
		
		Endpoint::returnResult($rtn);
	}
	
	private static function validate_token($access_token){
		$model=JModel::getInstance("token","JConnectModel");
		$data=$model->get($access_token);
		$rtn=false;
		$token_valid_time=10000000;
		if(isset($data)){
			$old_time=(int)$data->timestamp;
			if((time()-$old_time)<$token_valid_time){
				$rtn=true;
			}
		}
		
		return $rtn;
	}
	/**
	 * 
	 * check the given user is banned or not!
	 * @param $JID-Joomla user ID
	 * @param $appID - exApp ID
	 * @return boolean
	 */
	private static function isUserBan($JID,$appID){
		$su=new SyncUser($JID,$appID);
		return (isset($su) && $su->status=="BAN")?true:false;
	}
	
}
?>
