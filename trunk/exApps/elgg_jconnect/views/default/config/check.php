<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2

 * This is used to check login state of given JOOMLA_SESSION and do the things 
 * accrodingly...
 */

 
 ?>
<div id='jconnekt_sso'></div>
<script type="text/javascript">
	setTimeout(jconnekt.ajax_validator('jconnekt_sso'),5000);
</script> 
 <?php if(!$_COOKIE['jconnekt_request_token']){?>
<script type="text/javascript" src="<?php echo $jconnekt_api_url;?>jconnekt.js"></script>
<script type="text/javascript">
	jconnekt.draw_sso('jconnekt_sso','<?php echo $_SERVER['REQUEST_URI'];?>'); 
</script>
 
 <?php }?>

