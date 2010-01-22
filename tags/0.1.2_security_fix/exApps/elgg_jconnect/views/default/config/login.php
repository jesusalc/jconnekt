<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

$url=$vars['url'];
$jcLoginUrl=(substr($url,strlen($url)-1,1)=="/")? $url."pg/jconnect/login": 
	$url."/pg/jconnect/login";
?>

<script type="text/javascript">
	function popup(url,width,height){
		var top=screen.height/2-height/2;
		var left=screen.width/2-width/2;
		//window.open(url,'JConnect Login','width='+width+',height='+height);
		window.open(url,'Login','left='+left+',scrollbars=no,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
	}
</script>

<a href="javascript::void(0);" onclick="popup('<?php echo $jcLoginUrl; ?>',800,500)">
<img src="<?php echo $vars['url']; ?>mod/elgg_jconnect/views/default/config/login.png"></img>
</a>




