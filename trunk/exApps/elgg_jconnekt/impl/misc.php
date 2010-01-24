<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

class JCElggMisc extends JCMisc{

	public function getPublicView(){
		global $CONFIG;

		$sql="SELECT performed_by_guid as guid,count(performed_by_guid) as cnt FROM {$CONFIG->dbprefix}system_log log, {$CONFIG->dbprefix}users_entity en  WHERE log.performed_by_guid=en.guid GROUP BY performed_by_guid ORDER BY cnt DESC LIMIT 0,5";
		$html="";
		$html.="<span style='font-weight:bold; font-size:15px;'>Most Active Users</span><p>";
		$html.="<table padding=5 style='padding:7px;'>";

		$res=get_data($sql);
		foreach($res as $val){
			$user=get_user($val->guid);
			$html.="<tr style='padding:4px'>";
			$html.="<td><img src='$CONFIG->url/mod/profile/icondirect.php?&username=$user->username&size=small'/></td>";
			$html.="<td  align='left' valign='top' style='padding-left:2px; font-size: 12px; font-weight:bold;'>$user->name<br><a href='{$CONFIG->url}pg/profile/$user->username'>profile</a>".
			" | <a href='{$CONFIG->url}pg/friends/$user->username'>Friends</a><td>";
			$html.="</tr>";

		}
		$html.="</table>";
		return $html;
	}
	
	public function getPrivateView($username){
		global $CONFIG;
		$html=file("{$CONFIG->wwwroot}mod/elgg_jconnekt/river.php");
		$html=implode("\n",$html);
		return $html;
	}
	
	public function loadSysInfo($meta){
		
		if(isset($meta['JOOMLA_URL'])) datalist_set('joomla_url',$meta['JOOMLA_URL']."");
		if(isset($meta['JC_APPNAME'])) datalist_set('appName',$meta['JC_APPNAME']."");
		
	}
}