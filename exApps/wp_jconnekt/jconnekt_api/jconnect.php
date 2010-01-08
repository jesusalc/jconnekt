<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

/**
 * JConnect OpenID Wrapper
 *
 * This is a Wrapper class Based on PHP OpenID Library which is extended to used with
 * JConnect OpenID Server...
 *
 * @author 		Arunoda Susiripala
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 */
class JConnect{
	var $joomla_path;
	var $appName;
	var $secret;
	var $nonceManager;
	/**
	@param $joomla_path the full URL of the Joomla site...
	*/
	public function __construct($joomla_path,$appName,$secret,$nonceManager){
		$this->joomla_path=$joomla_path;
		$this->appName=$appName;
		$this->secret=$secret;
		$this->nonceManager=$nonceManager;
	}

	/**
		and then this will redirect into Joomla for login
		and then it'll redirect into recieve.php
		and it should handle all the things..
	 */
	public function login(){
		$nonce=md5(rand());

		$return_to=$this->getReturnTo()."?nonce=";
		if($this->nonceManager){
			$this->nonceManager->bind($nonce,time(),0);
			$this->nonceManager->save();
			$return_to.="$nonce";
		}
		
		$return_to="http://localhost/jconnekt/elgg/mod/elgg_jconnect/jconnect_api/auth_daemon.php?goto={$_SERVER['REQUEST_URI']}";
		$redirect_to=$this->joomla_path.
			"/?option=com_jconnect&controller=auth&format=raw&app_name={$this->appName}".
			"&return_to=$return_to";
		header("Location: $redirect_to");
		exit(0);
	}
	
	public function logout(){
		$return_to=$this->getReturnTo()."?logout=true";
		$redirect_to=$this->joomla_path."/?option=com_jconnect&controller=auth&".
			"task=logout&callback=$return_to";
		header("Location: $redirect_to");
		exit(0);
	}
	

	/**
	 *
	 * The response coming from the redirection will handle this..
	 * and authenticate the ExApp using publicKey
	 * and get the user-details and call the hooks...
	 * @return unknown_type
	 */
	public function reciever($loginHook,$logoutHook){
		//check for logout
		if(isset($_REQUEST['logout'])){
			$logoutHook(); 
			return;
		}
		//nonceHandling
		$this->handleNonce();

		$publicKey=$_REQUEST['publicKey'];
		//generate private key
		$privateKey=hash_hmac("md5",$publicKey.$this->appName,JCFactory::getAuthKey());
		$call_url=$this->joomla_path."/?option=com_jconnect".
			"&controller=auth&task=authenticate&format=raw&privateKey=$privateKey";

		$redirect=null;

		$res=file($call_url);
		if($res[0]){
			$data=json_decode($res[0],true);
			$redirect=$loginHook(true,$data);
		}
		else{
			$redirect=$loginHook(false,null);
		}

		if($redirect) header("Location: $redirect");

	}

	/**
	 * This handles the nonce provide in the Request variable..
	 * if nonce is not-valid this function will call die();
	 * @return unknown_type
	 */
	private function handleNonce(){
		if(!$this->nonceManager) return;
		$timePeriod=time()+1000*60*5;
		$nonce=$_REQUEST['nonce'];
		$this->nonceManager->load($nonce);

		if(!$this->nonceManager->nonce ||
		$this->nonceManager->used!=0 ||
		$this->nonceManager->timestamp >$timePeriod
		){
			die('nonce outdated!');
		}
		$this->nonceManager->used=1;
		$this->nonceManager->save();
	}

	private function getReturnTo() {
		return sprintf("%s://%s:%s%s/reciever.php",
		$this->getScheme(), $_SERVER['SERVER_NAME'],
		$_SERVER['SERVER_PORT'],
		$this->getPath());
	}

	private  function getScheme() {
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
			$scheme .= 's';
		}
		return $scheme;
	}

	private function getPath(){
		$dr= $_SERVER['DOCUMENT_ROOT'];
		$fd=dirname(__FILE__);
		$fd=str_replace("\\","/",$fd);
		$path=str_replace($dr,"",$fd);
		return (substr($path,0,1)=="/")?$path:"/$path";
	}

}
