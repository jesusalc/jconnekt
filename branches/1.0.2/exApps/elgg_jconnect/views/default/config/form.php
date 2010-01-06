<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ 
?>

<div class="contentWrapper">
	<h3><u>Information For JConnect</u></h3>
	<ul>
		<li><b>Host</b>: <?php echo getenv('HTTP_HOST');?></li>
		<li><b>Path</b>: <?php echo str_replace("http://".getenv('HTTP_HOST'),"",$vars['url']);?>pg/jconnect</li>
		<li><b>Port</b>: <?php echo getenv('SERVER_PORT')?></li>
	</ul>
</div>
<div class="contentWrapper">
	<h3><u>Connection Details</u></h3><p></p>
<?php
		$form = "";
		
		$form .= "<p>" . elgg_echo('Secret Key');
		$form .= elgg_view('input/text',array(
														'internalname' => 'secKey',
														'value' => $vars['secKey'] 
													)) . "</p>";
		
		$form .= "<p>" . elgg_echo('AppName');
		$form .= elgg_view('input/text',array(
														'internalname' => 'appName',
														'value' => $vars['appName']
													)) . "</p>";

		$form .= "<p>" . elgg_echo('Joomla URL');
		$form .= elgg_view('input/text',array(
														'internalname' => 'joomla_url',
														'value' => $vars['joomla_url']
													))  . "</p>";
		$form .= elgg_view('input/submit',array(
														'value' => elgg_echo('update'),
														'internalname' => 'submit',
													));
													
		$wrappedform = elgg_view('input/form',array(
														'body' => $form,
														'method' => 'get',
														'action' => $vars['url'] . "mod/elgg_jconnect/config.php"
										));
										
?>

		<div class="jconnectContainer" ><?php echo $wrappedform; ?></div>
	</div>