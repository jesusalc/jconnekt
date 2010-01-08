<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/



class JCWPAuth extends JCAuth{
	

	public function login($status,$data){
		//$data['username'],$data['email'],$data['user_group']
		require_once(ABSPATH . WPINC . '/registration.php');
		require_once(ABSPATH . WPINC . '/pluggable.php');
		
		$user_name=$data['username'];
		$user_email=$data['email'];
		
		//creating users if needed
		$user_id = username_exists( $user_name );
		if ( !$user_id ) {
			$random_password = wp_generate_password( 12, false );
			$user_id = wp_create_user( $user_name, $random_password, $user_email );
		} 
		
		//login here
		if($user_id){
			wp_set_auth_cookie($user_id);
			$_SESSION['JCONNEKT_LOGIN']=true;
		}
		
		//the application url
		$url=JCFactory::$app_url;
		
		//refresh the page or redirect in AutoActive SSO
		$iframe_redirect=($_GET['goto'])?$_GET['goto']:$url;
		
		$this->end_login($url,$iframe_redirect);
		
	}

	public function logout(){
		require_once(ABSPATH . WPINC . '/pluggable.php');
		wp_logout();
		
		//the application url
		$url=JCFactory::$app_url;
		
		//redirect in auto Active SSO
		$this->end_logout($url);
		return true;
	}
}