<?php

session_start();
/**
 * @package jconnekt.exApps
 * @author JConnekt Team
 * @version 1.0.2
 */
/*
Plugin Name: JConnekt - Wordpress External Application
Plugin URI: http://www.jconnekt.org
Description: JConnekt - Integration Engine on Joomla!
Author: JConnekt Team
Version: 1.0.2
Author URI: http://www.jconnekt.org
*/

include_once 'jconnekt_api/api.php';
 
//load the Javascipt Library
function jconnekt_js(){
	JCFactory::load_js_library();
}

function draw_sso(){	
	//draw auot active sso component
	if(!is_user_logged_in() || JCFactory::isJConnektSession()){
		echo "<div id='jconnekt_sso_box'></div>";
		echo "<script type='text/javascript'>setTimeout(\""."jconnekt.ajax_validator('jconnekt_sso_box','". $_SERVER['REQUEST_URI'] ."'),500"."\");</script>";//

	}
}


function draw_login(){	
	echo "<div id='jconnekt_login_box'></div>";
	echo "<script type='text/javascript'>jconnekt.draw_login('jconnekt_login_box')</script>";
	echo "<br>";
}

function jconnekt_logout(){ 
	if(JCFactory::isJConnektSession()){
		JCFactory::getJConnect()->deleteLocalToken();
		JCFactory::getJConnect()->logout();
		exit(0);
	}	
}

//widget is comming later
/*function widget_jconnekt_login($args) {
	  extract($args);
	  echo $before_widget;
	  echo $before_title;?>JConnekt Login<?php echo $after_title;
	  
	  echo "<div style='margin-top:6px'>";
	  
	  if(!is_user_logged_in()){
		echo "<div id='jconnekt_login_box'></div>";
		echo "<script type='text/javascript'>jconnekt.draw_login('jconnekt_login_box')</script>";
	  }
	  else{
	  	$uri = wp_nonce_url( site_url("wp-login.php", 'login'), 'log-out' );
	  	echo "<ul>";
	  	echo "<li><a href='{$uri}&action=logout'>Logout</a></li>";
	  	echo "<li><a href='".site_url("wp-admin/")."'>Site Admin</a></li>";
		echo "</ul>";
	  }
	  
	  echo "</div>";
	  
	  echo $after_widget;
}*/

function jconnekt_init()
{
	register_sidebar_widget('JConnekt Login', 'widget_jconnekt_login');     
}

function jconnekt_update_user(){
	
	$user=new WP_User($_POST['user_id']);
	$userGroup=null;
	if($user->roles[0]){
		$userGroup=$user->roles[0];
	}
	else if($user->roles[1]){
		$userGroup=$user->roles[1];
	}
	else{
		$userGroup='subscriber';
	}
	
	JCFactory::getJoomla()->updateUser(
		$user->user_login,
		$_POST['email'],
		$_POST['pass1'],
		$userGroup);
		
}

function jconnekt_create_user($data){
	//var_dump($data,$_POST);ss();
	
	JCFactory::getJoomla()->createUser(
		$_POST['user_login'],
		$_POST['email'],
		$_POST['pass1'],
		$_POST['role']);
		
}

function jconnekt_delete_user($user_id){
	$user=new WP_User($user_id);
	JCFactory::getJoomla()->deleteUser($user->user_login);
}

function jconnekt_validate_user($data){
	JCFactory::getJoomla()->updateUser(
		$_POST['log'],
		null,
		$_POST['pwd']);
}


function jconnekt_config_menu() {
	add_options_page("JConnekt Config", "JConnekt Config", 1, "JConnekt Config", "jconnekt_config_view");  
}

function jconnekt_config_view() {
  include('jconnekt_config.php');
}

add_action("wp_login", "jconnekt_validate_user");
add_action("delete_user", "jconnekt_delete_user");
add_action("profile_update", "jconnekt_update_user");
add_action("user_register", "jconnekt_create_user");
add_action("plugins_loaded", "jconnekt_init");
add_action('wp_print_scripts','jconnekt_js');
add_action('login_head','jconnekt_js');
add_action('loop_start','draw_sso');
add_action('login_form','draw_login');
add_action('wp_logout','jconnekt_logout');
add_action('admin_menu', 'jconnekt_config_menu');
