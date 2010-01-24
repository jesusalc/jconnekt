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
$url=$vars['url'];
$jconnekt_api_url=(substr($url,strlen($url)-1,1)=="/")? $url."mod/elgg_jconnect/jconnect_api/": 
	$url."/mod/elgg_jconnect/jconnect_api/";
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


 