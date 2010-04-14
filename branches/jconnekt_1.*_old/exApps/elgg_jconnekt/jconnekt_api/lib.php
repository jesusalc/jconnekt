<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

/**
 * This file contains some classes which will be used by developers by extending
 * and using (for development..)
 */

/**
 *
 * This class contains user-defined set of functions..
 * (will be add later on by developer..)
 * And will be used by jconnect API
 *   in,
 *    server.php
 *    reciever.php
 */
class JCFactory{

	/**
	 * Basic details..
	 */
	public static $secKey;
	public static $appName;
	public static $joomla_url;
	public static $app_url;
	public static $api_url;
	/**
	 * if we cannot load the Engine outside the site root we go through this script
	 */
	public static $caller;

	/**
	 * Object of JCUserSync class
	 * @var JCUserSync
	 */
	public static $userSync;

	/**
	 * Object of JCAuth class
	 * @var JCAuth
	 */
	public static $auth;

	/**
	 * Object of JCMisc class..
	 * @var JCMisc
	 */
	public static $misc;

	/**
	 * Used for managing nonces authentication callback url
	 * @var NonceManager
	 */
	public static $nonceManager;

	/**
	@param $var- name of the variable
	possible values (userSync,auth,misc)

	@param $val- the value..
	*/
	public static function register($var,$val){
		JCFactory::$$var=$val;
	}

	private static $joomla;
	/**
	 * returns a object of Joomla
	 * @return Joomla
	 */
	public static function getJoomla(){
		if(!JCFactory::$joomla){
			if(JCFactory::$secKey && JCFactory::$appName && JCFactory::$joomla_url){
				JCFactory::$joomla=new Joomla(
				JCFactory::$joomla_url,
				JCFactory::$appName,
				JCFactory::$secKey
				);
			}
		}
		return JCFactory::$joomla;
	}
	
	public static function getAuthKey(){
		if(JCFactory::$secKey){
			$keys=explode("::",JCFactory::$secKey);
			return $keys[0];
		}
		
		return null;
	}
	
	public static function getCryptKey(){
		if(JCFactory::$secKey){
			$keys=explode("::",JCFactory::$secKey);
			return $keys[1];
		}
		
		return null;
	}

	private static $jconnect;
	/**
	 * returns a object of JConnect
	 * @return JConnect
	 */
	public static function getJConnect(){
		if(!JCFactory::$jconnect){
			if(JCFactory::$secKey && JCFactory::$appName && JCFactory::$joomla_url){
				JCFactory::$jconnect=new JConnect(
				JCFactory::$joomla_url,
				JCFactory::$appName,
				JCFactory::$secKey,
				JCFactory::$nonceManager
				);

			}
		}
		return JCFactory::$jconnect;
	}
	
	public static function isJConnektSession(){
		if(isset($_COOKIE['JCONNEKT_SESSION'])){
			return true;
		}
		
		return false;
	}
	
	public static function isOnCrossDomain(){
		$joomla_data=parse_url(JCFactory::$joomla_url);
		$app_data=parse_url(JCFactory::$api_url);
		
		//set the port for 80 if not assigned
		if(!isset($joomla_data['port'])) $joomla_data['port']=80;
		if(!isset($app_data['port'])) $app_data['port']=80;
	
		$onCross=false;
		if($joomla_data['host']!=$app_data['host']){
			$onCross=true;
		}
		else if($joomla_data['port']!=$app_data['port']){
			$onCross=true;
		}
		
		return $onCross;
	}
	
	/**
	 * 
	 * This will load Javascript library getting all the parameters from the API
	 * 
	 * @param $caller_filename some times in some apps we don't have to goto jconnekt API directly
	 * so at those we go through our own file located in the app root . so this is that file's anme
	 */
	public static function load_js_library(){
		$caller_filename=JCFactory::$caller;
		$url=JCFactory::$app_url;
		$jconnekt_api_url=JCFactory::$api_url;
		$joomla_url=JCFactory::getJConnect()->joomla_path;
		$app_name=JCFactory::getJConnect()->appName;
		$app_url=JCFactory::$app_url;
		
		if(!substr($joomla_url,strlen($joomla_url)-1,1)=="/") $joomla_url.="/";
		if(!substr($jconnekt_api_url,strlen($jconnekt_api_url)-1,1)=="/") $jconnekt_api_url.="/";
		if(!substr($app_url,strlen($app_url)-1,1)=="/") $app_url.="/";
		
		$caller_url='';
		if($caller_filename){
			$caller_url=$app_url.$caller_filename;
		}
				
		echo "<script type='text/javascript' src='{$jconnekt_api_url}jconnekt.js'></script>";
		echo 
			"<script type='text/javascript'>".
			"var jconnekt=new JConnekt('$app_name','$jconnekt_api_url','$joomla_url','$caller_url')".
			"</script>";
	}
}

/**
 * Contains lists of functions to be implement by the developer...
 * used to connect joomla with user-sync capabilities...
 *
 */
class JCUserSync{
	/**
	@return - true or false depending on the result..
	*/
	public function deleteUser($username){
		throw new Exception("Not Implemented",1024);
	}

	/**
	 * @return total number of users...
	 */
	public function getUserCount(){
		throw new Exception("Not Implemented",1024);
	}

	/**
	 * used in user-group mapping..
	 * @return array of user-groups
	 */
	public function getUserGroups(){
		throw new Exception("Not Implemented",1024);
	}

	/** (this is used for bulk)
	 Here usernames and email and userGroup are return as the blocks, size  by @see $chunksize and
	 the $chunkNo th block
	 eg:- if we have 1500 users @see $chunksize 500, $chunkNo 2 will send user from 501-1000..

	 @return a 2d array containing usernames and email for users..
		eg: rtn[$lc][0]='username';
		rtn[$lc][1]='email';
		rtn[$lc][2]='userGroup';
		*/
	public function getUserDetails($chunkSize,$chunkNo){
		throw new Exception("Not Implemented",1024);
	}

	/**
		this function is used in bulk-sync where if we need resolve conflicts..
		@param $usernameList array of usernames..
		@return user-details of given usernames.. in template shown below..
		eg: rtn[$lc][0]='username';
		rtn[$lc][1]='email';
		rtn[$lc][2]='userGroup';
	 */
	public function getUsers($usernameList){
		throw new Exception("Not Implemented",1024);
	}
}

/**
 * some other functions used by JConnect engine...
 * @author arunoda
 *
 */
class JCMisc{
	/**
	 * The returning html data will be displayed for public view
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 * use above strToIntArray like function
	 @return - html
	 */
	public function getPublicView(){
		throw new Exception("Not Implemented",1024);
	}


	/**
	 * The returning html data will be displayed for public view
	 * return data is in a intArray in ascii values because xmlrpc doesn't allow "<>&".. chars
	 * use above strToIntArray like function
	 @return - html data(public) in a intArray
	 */
	public function getPrivateView($username){
		throw new Exception("Not Implemented",1024);
	}

	/**
	 This is the function probabaly called by the JConnect to inform
	 exApp about jconnect details...

	 @parama $meta - array containing jconnect info...
	 possible keys are..
	 JOOMLA_URL,JC_APPNAME
	 */
	public function loadSysInfo($meta){
		throw new Exception("Not Implemented",1024);
	}
}

/**
 * some function used in authentication...
 * in,
 *  reciever.php
 *
 */
class JCAuth{
	/**
	 * Should do the login into the system of given data...
	 *
		@param $status - if login was success or not (boolean)
		@param $data -array containg user-data
		$data['username'],$data['email'],$data['user_group'],$data['session_id']
	 */
	public function login($status,$data){
		throw new Exception("Not Implemented",1024);
	}
	
	/** should do the logout 
	 */
	public function logout(){
		throw new Exception("Not Implemented",1024);
	}
	
	/**
		@param $popup_url- url is going to refresh the opener when used a popup
		@param $self_url - url which is going to refresh the page when goes via iframe
	 */
	protected function end_login($popup_url,$iframe_url){	
		echo "<script type='text/javascript'>".
			"if(opener){".
			"opener.location.href='$popup_url'; ".
			"window.close();".
			"}".
			"else if(parent)parent.window.location.href='".$iframe_url."'".
			"</script>";
	}
	
	protected function end_logout($redirect_url){
		echo "<script type='text/javascript'>".
			"if(parent)parent.window.location.href='".$redirect_url."'".
			"</script>";
	}
	
	
}

class NonceManager{
	var $nonce;
	var $timestamp;
	var $used;

	/**
	 * store the above variables into a datastore..
	 */
	function save(){
		throw new Exception("Not Implemented",1024);
	}
	/**
		load the values for instance variables using $nonce
	 */
	function load($nonce){
		throw new Exception("Not Implemented",1024);
	}
	/**
	 *
	 * delete the values in instance variables from datastore..
	 */
	function delete(){
		throw new Exception("Not Implemented",1024);
	}

	function bind($nonce,$timestamp,$used){
		if(isset($nonce)) $this->nonce=$nonce;
		if(isset($timestamp)) $this->timestamp=$timestamp;
		if(isset($used)) $this->used=$used;
	}

}