<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * This class controlles do the JConnect authentication from ExApp..
 *
 * @package    JConnect.component.admin.controllers
 */

class JconnectControllerAuth extends JController{
	public function __construct(){
		parent::__construct();
	}

	function display(){
		
		$callback=JRequest::getVar('return_to');
		$appName=JRequest::getVar('app_name');
		$session=JFactory::getSession();

		$fault=loginAuthenticate($appName);
		if(!is_bool($fault) || $fault==false){
			echo "ERROR: ".$fault->code ."." .$fault->message;
			return;
		}

		$session->set('callback',$callback,'jconnect.auth');
		$session->set('appName',$appName,'jconnect.auth');
		
		$user=JFactory::getUser();
		if($user->id){
			//already logged in
			$this->sendPublicKey($appName,$user->id);
		}
		else{
			$this->showLoginForm();
		}
	}

	private function showLoginForm(){
		JRequest::setVar('view','auth');
		parent::display();
	}

	function cancel(){
		$this->setRedirect(JFactory::getSession()->get('callback','','jconnect.auth'));
	}
	

	function login(){
		$username=JRequest::getVar("username");
		$password=JRequest::getVar('password');
		$persistant=JRequest::getVar("persistant");
		$token=JRequest::getVar('token');

		if(JFactory::getSession()->getToken()!=$token){
			die(JText::_('INVALID_TOKEN').",$token::".JFactory::getSession()->getToken());
		}

		//check for banning..
		$user=JUser::getInstance($username);
		$appName=JFactory::getSession()->get('appName','','jconnect.auth');
		$appID=JCHelper::getAppID($appName);
		if(!$appID) die("appName: $appName is not in use..");

		$syncUser=new SyncUser($user->id,(int)$appID);
		if($syncUser->status=="BAN"){
			echo JText::_('YOU_BANNED')." <br> <a href='javascript:history.go(-2)'>Back</a>";
			return;
		}

		global $mainframe;
		
		$options=array();
		if(isset($persistant)) $options['remember']=true;
		$res=$mainframe->login(array('username'=>$username,'password'=>$password),$options);
		if(is_bool($res) && $res==true){
			$syncUser=new SyncUser($user->id,(int)$appID);
			$syncUser->status="OK";
			$syncUser->save();
			$this->sendPublicKey($appName,$user->id);
			
		}
		else{
			JFactory::getSession()->set('LOGIN_ERROR',$res->message);
			$this->showLoginForm();
		}
		
	}

	/**
	 * This will get the privateKey from the Request variable and authenticate it..
	 * if so this will print a json of username,email,usergroup,session_id
	 * @return unknown_type
	 */
	function authenticate(){
		$privateKey=JRequest::getVar('privateKey');
		$res=$this->getModel('auth')->validate($privateKey);
		if($res){
			$user=JUser::getInstance($res->userID);
			$userGroup=null;
			//we are only sending userGroup for users not owned by current ExApp
			if(!ExternalUser::contains($user->id)){
				$userGroup=new JCGroupOut($res->appID,$user->usertype);
			}
			$data=array(
				'username'=>$user->username,
				'email'=>$user->email,
				'user_group'=>$userGroup->exAppGroup,
				'session_id'=>$res->session_id
			);

			echo json_encode($data);
		}
	}
	
	/**
	 * 
	 * Register the ExApp and take the request token and redirect to the given return_to url with.
	 * the json='actual data as json'
	 * @return unknown_type
	 */
	function request_token($data){
		$appName=JRequest::getVar('app_name');
		$return_to=JRequest::getVar('return_to');
		try{
			$exApp=new ExApp($appName);
			$model=JModel::getInstance("token","JConnectModel");
			$request_token=$model->get_request_token();
			
			$access_token=$model->generate_access_token($request_token,$exApp);
			
			$user=JFactory::getUser();
			$state=($user->id)?"online":"offline";
			$model->insert($access_token,$request_token,JCHelper::getAppID($appName),time(),$user->id);
			
			$rtn=array('state'=>$state,'request_token'=>$request_token);
			
			//sending redirect
			if(strstr($return_to,"?")) $return_to.="&";
			else $return_to.="?";
			
			header("Location: {$return_to}json=".json_encode($rtn));
			
		}
		catch(Exception $ex){
			var_dump(4,"Invalid External Application");
		}
	}
	
	/**
	 * this will send the publicKey to the callBack URL;
	 * @return unknown_type
	 */
	private function sendPublicKey($appName,$userID){
		//delete existing tokens
		$jconnekt_token=$_COOKIE['jconnekt_token'];
		if(isset($jconnekt_token)){
			$model=JModel::getInstance("token","JConnectModel");
			
			//delete all token set by all exApps
			$model->delete_by_request_token($jconnekt_token);
			setcookie('jconnekt_token',0,time()-3600,"/");
			unset($_COOKIE['jconnekt_token']);
		}
			
		$return_to=JFactory::getSession()->get('callback','','jconnect.auth');		
		try{
			$exApp=new ExApp($appName);
			$model=JModel::getInstance("token","JConnectModel");
			$request_token=$model->get_request_token();
			
			$access_token=$model->generate_access_token($request_token,$exApp);
			
			$state=($userID)?"online":"offline";
			$model->insert($access_token,$request_token,JCHelper::getAppID($appName),time(),$userID);
			
			$rtn=array('state'=>$state,'request_token'=>$request_token);
			
			//sending redirect
			if(strstr($return_to,"?")) $return_to.="&";
			else $return_to.="?";
			
			header("Location: {$return_to}json=".json_encode($rtn));
			
		}
		catch(Exception $ex){
			var_dump(4,"Invalid External Application");
		}
	}
	
	/**
	 * get the user by given session id...
	 * session_id should come via Request variable
	 */
	public function getUserBySession(){
		$session_id=JRequest::getCmd('session_id');
		$sql="SELECT username FROM #__session WHERE  ".
			"session_id='$session_id' AND client_id=0";
		$db=JFactory::getDBO();
		$db->setQuery($sql);
		if($db->getErrorNum()) throw new Exception($db->getErrorMsg());
		$res=$db->loadObject();

		$rtn=($res && $res->username)?$res->username:null;
		echo $rtn;
	}
	
	/*
	 * Logout the current user
	 */
	function logout(){
		$callback=JRequest::getVar("callback");		
		global $mainframe;
		$mainframe->logout();
		$this->setRedirect($callback);
	}
}