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



function widget_jconnekt_login($args) {
	  extract($args);
	  echo $before_widget;
	  echo $before_title;?>JConnekt Login<?php echo $after_title;
	  
	  echo "<div style='margin-top:6px'>";
	  
	  if(!JCFactory::isJConnektSession()){
		echo "<div id='jconnekt_login_box'></div>";
		echo "<script type='text/javascript'>jconnekt.draw_login('jconnekt_login_box')</script>";
	  }
	  else{
	  	$uri = wp_nonce_url( site_url("wp-login.php", 'login'), 'log-out' );
	  	echo "<ul>";
	  	echo "<li><a href='{$uri}&action=logout'>Logout</a></li>";
	  	echo "<li><a href='"."http://localhost/jconnekt/wp/1.0.2/wp-admin/"."'>Site Admin</a></li>";
		echo "</ul>";
	  }
	  
	  echo "</div>";
	  
	  echo $after_widget;
}

function jconnekt_init()
{
	register_sidebar_widget('JConnekt Login', 'widget_jconnekt_login');     
}


add_action("plugins_loaded", "jconnekt_init");
add_action('wp_print_scripts','jconnekt_js');
add_action('login_head','jconnekt_js');
add_action('loop_start','draw_sso');
add_action('login_form','draw_login');
add_action('wp_logout','jconnekt_logout');
