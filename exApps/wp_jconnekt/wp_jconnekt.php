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
 
function jconnekt_js(){
	$url='http://localhost/jconnekt/wp/1.0.2/';
	$jconnekt_api_url=(substr($url,strlen($url)-1,1)=="/")? $url."wp-content/plugins/wp_jconnekt/jconnekt_api/": 
	$url."/wp-content/plugins/wp_jconnekt/jconnekt_api/";
	$joomla_url=JCFactory::getJConnect()->joomla_path;
	$app_name=JCFactory::getJConnect()->appName;
	if(!substr($joomla_url,strlen($joomla_url)-1,1)=="/") $joomla_url.="/";
	?>
<div id='jconnekt_sso_box'></div>
<script type="text/javascript" src="<?php echo $jconnekt_api_url;?>jconnekt.js"></script>
<script type="text/javascript">
	var jconnekt=new JConnekt(
			'<?php echo $app_name;?>',
			'<?php echo $jconnekt_api_url?>',
			'<?php echo $joomla_url;?>');
</script>
	<?php 
}

function draw_login(){
	?>
<div id='jconnekt_login_box'></div>
<script type="text/javascript">
	jconnekt.draw_login('jconnekt_login_box');
</script>
	<?php 
}

add_action('wp_print_scripts','jconnekt_js');
add_action('loop_start','draw_login');