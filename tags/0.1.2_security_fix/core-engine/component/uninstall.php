<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

function uninstallPackage($type, $id, $group, $name) {
	$db		=& JFactory::getDBO();
	$result = $id;
	switch($type) {
		case 'plugin':
			$db->setQuery("SELECT id FROM #__plugins WHERE folder = '$group' AND element = '$id'");
			$result = $db->loadResult();
			break;
		case 'module':
			$db->setQuery("SELECT id FROM #__modules WHERE module = '$id'");
			$result = $db->loadResult();
			break;
	}
	if ($result){
		$tmpinstaller = new JInstaller();
		$installer_result = $tmpinstaller->uninstall($type, $result, 0 );
		if($installer_result) { ?>
			<div style="color: green; font-weight: bold;"><?php echo $name." ".JText::_('UNINSTALLED');  ?></div><br>
		<?php } else { ?>
			<div style="color: red; font-weight: bold;"><?php echo $name." ".JText::_('UNINSTALL_FAILD'); ?></div><br>
		<?php }
	}
}

uninstallPackage('module','mod_jconnect', '', 'JConnect Activity Module');
uninstallPackage('plugin','jconnect', 'user', 'JConnect User Plugin');
uninstallPackage('plugin','jconnect', 'system', 'JConnect API');
?>
