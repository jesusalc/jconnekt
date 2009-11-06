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
jimport('phpxmlrpc.xmlrpc');
jimport('phpxmlrpc.xmlrpcs');


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
		$this->putServer();
	}

	/**
	 * The OpenID provider is goes here...(the server)
	 * @return void
	 */
	function op(){
		//authentication ExApp and validating
		$appName=$this->getAppName();
			
		//openID server process started!
		$oserver=$this->getOpenIDServer();
		$request = $oserver->decodeRequest();
		$this->setRequestInfo($request);

		$response=null;
		if (in_array($request->mode, array('checkid_immediate','checkid_setup'))) {
			JFactory::getSession()->set('JC_OPENID_APPNAME',$appName);
			if(!$appName) die('integrity has been attacked!');
			$fault=openIDAuthenticate($appName,$secKey);
			if(!is_bool($fault) || $fault==false){
				echo "ERROR: ".$fault->code ."." .$fault->message;
				return;
			}
			if($appName){
				$response=$this->handleExAppOPRequest($request,$appName);
				if(!$response) return;
			}
		} else {
			$response = $oserver->handleRequest($request);
		}

		$webresponse = $oserver->encodeResponse($response);
		$this->dumpResponse($webresponse);
	}

	/**
	 *
	 * get appName from the request with checking the integrity...
	 * @return appName or false if integrity cannot be validated..
	 */
	private function getAppName(){
		$custom=JRequest::getVar('appName');
		$segs=explode("_",$custom);
		$exApp=new ExApp($segs[0]);
		return (md5($segs[0].$exApp->secretKey)==$segs[1])? $segs[0]:false;
	}
		
	/**
		Handle the request if it's coming from a ExApp (JConnect Client)...
		So it get a OpenID request and returns the OpenID response..

		@param $request openID request
		@param $appName ExApp name..

		@return OpenID response...

	 */
	private function handleExAppOPRequest($request,$appName){
		global $mainframe;
		$response=null;

		$stpos=strpos($request->identity,"user=")+5;
		$username=substr($request->identity,$stpos,strlen($request->identity)-$stpos);
		JFactory::getSession()->set("JC_OPENID_USERNAME",$username);


		//actual loggedin user....
		$loggedUser=JFactory::getUser();
		$appID=JCHelper::getAppID($appName);

		//check banning for logged in users..
		if(isset($loggedUser) && $loggedUser->id){
			$syncUser=new SyncUser($loggedUser->id,$appID);
			//check for banning...
			if($syncUser->status=="BAN"){
				echo "You are Banned! <br> <a href='javascript:history.back()'>Back</a>";
				return;
			}
		}
		if($username=='JC_LOGOUT'){
			$res=$mainframe->logout();
			$response = $request->answer($res);
		}
		else if($username=='JC_GENERIC_LOGIN'){
			//give username also here...
			if(isset($loggedUser) && $loggedUser->id){
				//logged in user..
				$this->sendAuth();
			}
			else{
				$this->showLoginForm();
			}
		}
		else if (isset($loggedUser) && $loggedUser->id && JFactory::getUser()->username==$username) {
			$this->sendAuth();
		}
		else {
			//check if user has to be sync by the ExApp (Just after the user-sync)
			// to  get the password..
			$eu=new ExternalUser($user->id);
			if($eu->needSync==1){
				$exApp=new ExApp((int)$eu->ownerAppID);
				echo "You've Not Validated yet (JConnect is not aware about your password)..<br>".
					"Please visit <b>$exApp->appName</b> once and login normally (natively)";
				return;
			}
			$this->showLoginForm();
			return;
		}
		return $response;
	}

	function showLoginForm(){

		JRequest::setVar('view','jconnectlogin');
		parent::display();
	}

	/**
	 *
	 * This function is a task which will be call by user to
	 * cancel the openID authetication....
	 * @return void... insted redirection will be happened...
	 */
	function jconnectCancel(){
		$oserver=$this->getOpenIDServer();
		$request=$this->getRequestInfo();
		$response =$request->answer(false);

		$webresponse =& $oserver->encodeResponse($response);
		$this->dumpResponse($webresponse);
	}

	/**
	 * this send the valid authenticate status with the user-details for exApps.
	 * plus Simple Registration details..
	 * @return unknown_type
	 */
	function sendAuth(){

		$oserver=$this->getOpenIDServer();
		$request=$this->getRequestInfo();
		$response =$request->answer(true, null, $req_url);
		
		$session=JFactory::getSession();
		$appID=JCHelper::getAppID($session->get('JC_OPENID_APPNAME'));
		
		$userGroup=null;
		//we are only sending userGroup for users not owned by current ExApp
		if(!ExternalUser::contains(JFactory::getUser()->id)){
			$userGroup=new JCGroupOut($appID,JFactory::getUser()->usertype);
		}

		// Answer with some sample Simple Registration data.
		//nickName wouldn't be the joomla one but the exApp one..
		//if such user exists...
		$sreg_data = array(
                           'fullname' => JFactory::getUser()->name,
                           'nickname' => JFactory::getUser()->username ,
                           'email' => JFactory::getUser()->email,
						   'dob' => JFactory::getSession()->getId(),
						   'country' => $userGroup->exAppGroup);

		// Add the simple registration response values to the OpenID
		// response message.
		$sreg_request = Auth_OpenID_SRegRequest::fromOpenIDRequest(
		$request);

		$sreg_response = Auth_OpenID_SRegResponse::extractResponse(
		$sreg_request, $sreg_data);

		$sreg_response->toMessage($response->fields);

		// Generate a response to send to the user agent.
		$webresponse =& $oserver->encodeResponse($response);

		//check sync status and apply rules..
		var_dump(JFactory::getUser()->id,$appID);
		$syncUser=new SyncUser(JFactory::getUser()->id,$appID);

		if(!$syncUser->status){
			$syncUser->status="OK";
			$syncUser->save();
		}
			
		$this->dumpResponse($webresponse);
	}

	function jconnectlogin(){
		$username=JFactory::getSession()->get('JC_OPENID_USERNAME');
		$username=($username=='JC_GENERIC_LOGIN')?JRequest::getVar("username"):$username;
		$password=JRequest::getVar('password');
		$token=JRequest::getVar('token');

		if(JFactory::getSession()->getToken()!=$token){
			die("Invalid Token!,$token::".JFactory::getSession()->getToken());
		}

		//check for banning..
		$user=JUser::getInstance($username);
		$appID=JCHelper::getAppID(JFactory::getSession()->get('JC_OPENID_APPNAME'));
		$syncUser=new SyncUser($user->id,$appID);
		if($syncUser->status=="BAN"){
			echo "You are Banned! <br> <a href='javascript:history.go(-2)'>Back</a>";
			return;
		}

		global $mainframe;
		$res=$mainframe->login(array('username'=>$username,'password'=>$password,array()));
		if(is_bool($res) && $res==true){
			$this->sendAuth();
		}
		else{
			JFactory::getSession()->set('JC_OPENID_ERROR',$res->message);
			$this->showLoginForm();
		}
	}


	//this is used to dump the openid response to the header and etc..
	function dumpResponse($webresponse){
		if ($webresponse->code != AUTH_OPENID_HTTP_OK) {
			header(sprintf("HTTP/1.1 %d ", $webresponse->code),
			true, $webresponse->code);
		}

		foreach ($webresponse->headers as $k => $v) {
			header("$k: $v");
		}

		header(header_connection_close);
		print $webresponse->body;
		exit(0);
	}

	/**
	 * Identity (OpenID) for user goes here..
	 * @return unknown_type
	 */
	function user(){
		$username=JRequest::getString('user');
		if(!in_array($username,array('JC_LOGOUT','JC_GENERIC_LOGIN'))){
			$user=JUser::getInstance($username);
			$username=$user->username;
		}
		$openid_server=$this->getEndPoint();
		$model=new JModel();
		$model->set('username',$username);
		$model->set('openid_server',$openid_server);
		$view=$this->getView('openiduser','raw');
		$view->setModel($model,true);
		$view->display();
		return;
	}

	//the xml-rpc server is here...
	private function putServer(){
		global $xmlrpcString,$xmlrpcBoolean;
		$methods=array(
				"jc.host.createUser"=>array(
				'function' => 'Methods::createUser',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString)),
				'docstring'=>'creates a new user'
				),
				"jc.host.updateUser"=>array(
				'function' => 'Methods::updateUser',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString)),
				'docstring'=>'update a existing user'
				),
				"jc.host.deleteUser"=>array(
				'function' => 'Methods::deleteUser',
				'signature' => array(array($xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcString)),
				'docstring'=>'delete a existing user'
				)
				);

				$xmlrpcServer = new xmlrpc_server($methods, false);
				$xmlrpcServer->functions_parameters_type = 'phpvals';
				$xmlrpcServer->service();
	}

	function getOpenIDServer(){
		$serverPath=$this->getEndPoint();
		$data_path="/tmp";
		$store=new JCOpenIDStore();
		$oserver = new Auth_OpenID_Server($store,$serverPath);
		return $oserver;
	}


	function getEndPoint(){
		$serverPath="http://".getenv('SERVER_NAME').":".getenv('SERVER_PORT')."".getenv('SCRIPT_NAME')."/?option=com_jconnect&task=op&format=raw";
		return $serverPath;
	}

	//set the OpenID request var into session
	function getRequestInfo()
	{
		return ((JFactory::getSession()->get('JC_OPENID_REQUEST')))
		? unserialize(JFactory::getSession()->get('JC_OPENID_REQUEST'))
		: false;
	}

	//get the OpenID request var from the session
	function setRequestInfo($info=null)
	{
		if (!isset($info)) {
			JFactory::getSession()->set('JC_OPENID_REQUEST',null);
		} else {
			JFactory::getSession()->set('JC_OPENID_REQUEST',serialize($info));
		}
	}

}


class Methods{

	function createUser($appName,$hmac_hash,$username,$email,$password,$group){
		$fault=authenticate($appName,$hmac_hash,array($username,$email,$password,$group));
		if(!is_bool($fault) || $fault==false){
			return new xmlrpcresp(php_xmlrpc_encode(
			$fault));
		}

		//var_dump($username,$email,$password);
		if(!$username || !$email || !$password || !$group){
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(2,"all parameters should be have a value")));
		}

		//check for existing user
		$user=JUser::getInstance($username);
		if($user){
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(8,"username already existed")));
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
				throw new Exception("Not Allowed to Create Users");
			}
				
			jimport("joomla.user.helper");
			//get the group
			$userGroup=new JCGroupIn($appID,$group);
			$aclGroup=($userGroup->joomlaGroup)?$userGroup->joomlaGroup:'Registered';
			JCHelper::createJoomlaUser($username,$email,$password,$aclGroup);
			$userID=JUserHelper::getUserId($username);
			$db=JFactory::getDBO();
			//insert into External User..
			$sql="INSERT INTO #__jc_externalUsers (JID,username,ownerAppID,needSync) ".
					"VALUES (".
					"$userID,'$username',$appID,0".
					")";
			$db->Execute($sql);
			if($db->getErrorNum()) throw new Exception($db->getErrorMsg());

			//insert into Sync User
			$sql="INSERT INTO #__jc_syncUsers (JID,appID,status) VALUES (".
				"$userID,$appID,'OK')";
			$db->Execute($sql);
			if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		}
		catch(Exception $ex){
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(128,$ex->getMessage())));
		}

		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac($appName,true)));

	}

	function updateUser($appName,$hmac_hash,$username,$email,$password,$group=null){
		$fault=authenticate($appName,$hmac_hash,array($username,$email,$password,$group));
		if(!is_bool($fault) || $fault==false){
			return new xmlrpcresp(php_xmlrpc_encode($fault)); 
		}

		//check for recursive_delete option
		//means whether allow to delete joomla + other users when a exApp user deleted.
		$is_update_user=JCHelper::getMeta($appName,"update_user");
		if($is_update_user!="allow") {
			return new xmlrpcresp(php_xmlrpc_encode(
				"Not Allowed to Update users."));
		}
		
		//check for this user is synced already
		jimport("joomla.user.helper"); 
		try{
			$userID=JUserHelper::getUserId($username);
			if(!$userID) throw new Exception("user not found on joomla",256);
			if(!SyncUser::contains($userID,$appName)){
				throw new Exception("the user is not a synchronized user!",128);
			}
				
			// get the gid from exApp user Group....$group='user'
			$appID=JCHelper::getAppID($appName);
			
			//check the user is banned or not..
			if(Methods::isUserBan($userID,$appID)){
				throw new Exception("You are banned! not  allowed to update!",512);
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
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault($ex->getCode(),$ex->getMessage())));
		}

		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac($appName,true)));
	}

	function deleteUser($appName,$hmac_hash,$username){
		$appID=JCHelper::getAppID($appName);

		$fault=authenticate($appName,$hmac_hash,array($username));
		if(!is_bool($fault) || $fault==false){
			return new xmlrpcresp(php_xmlrpc_encode(
			$fault));
		}

		$user=JUser::getInstance($username);
		if(!$user){
			//check for this user is synced already
			if(!SyncUser::contains((int)$user->id,$appName)){
				return new xmlrpcresp(php_xmlrpc_encode(
				new Fault(16,"the user is not a synchronized user!")));
			}
			return new xmlrpcresp(php_xmlrpc_encode(
			new Fault(256,"username should be valid")));
		}
		
		if(Methods::isUserBan($user->id,$appID)){
				throw new Exception("You are banned! not  allowed to delete!",512);
		}
		
		//sync_user should be deleted anyway!
		$db=JFactory::getDBO();
		//delete user from the JConnect records..
		$sql="DELETE FROM #__jc_syncUsers WHERE JID={$user->id}";
		
		$db->Execute($sql);
		if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		
		//check for recursive_delete option
		//means whether allow to delete joomla + other users when a exApp user deleted.
		$is_delete_user=JCHelper::getMeta($appName,"delete_user");
		if($is_delete_user!="allow") {
			return new xmlrpcresp(php_xmlrpc_encode(
				"Not Allowed to Delete users."));
		}
		
		if(!$user->delete()){
				return new xmlrpcresp(php_xmlrpc_encode(
				new Fault(128,$user->getError())));
		}
		
		//external user-deleted if the exApp has powers to delete the users..
		$sql="DELETE FROM #__jc_externalUsers WHERE JID={$user->id}";
		$db->Execute($sql);
		if($db->getErrorNum()) throw new Exception($db->getErrorMsg());

		return new xmlrpcresp(php_xmlrpc_encode(
		send_hmac($appName,true)));
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
