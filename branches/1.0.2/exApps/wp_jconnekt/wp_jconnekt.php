<?php
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

function draw_login(){
	echo "<div id='jconnekt_login_box'></div>";
	echo "<script>jconnekt.draw_login('jconnekt_login_box')</script>";
}

function jconnekt_logout(){
	JCFactory::getJConnect()->deleteLocalToken();
	JCFactory::getJConnect()->logout();
}


add_action('wp_print_scripts','jconnekt_js');
add_action('loop_start','draw_login');
add_action('wp_logout','jconnekt_logout');