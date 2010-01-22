<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
	$packagePath=JPATH_BASE.DS."components".DS."com_jconnect".DS."ext".DS;
	$packages=array('system.zip','user.zip','activity.zip');
	
	foreach($packages as $package){
		$res = JInstallerHelper::unpack($packagePath.$package);
		$tmpInstaller = new JInstaller();
		$tmpInstaller->setOverwrite(true);
		if($tmpInstaller->install($res['dir'])) {
			   echo "<font color='green'><b>$package</b> ".JText::_('INSTALLED')."</font><br>";
		} else {
			   echo "<font color='red'><b>$package</b>".JText::_('INSTALL_FAILED')."/font><br>";
		}
	}
	
	
	
	function enablePackage($type,$id,$group=null){
		$db		=& JFactory::getDBO();
		if($type=='plugin'){
			$db->setQuery("UPDATE #__plugins SET published=1 WHERE folder = '$group' AND element = '$id'");
			if(!$db->query()) {
				echo "<font color='red'>$db->stderr()</font><br>";
				return false;
			}
			else{
				echo "<font color='green'><b>$id $group $type ".JText::_('ENABLED')."</font><br>";
				return true;
			}	
		}
		else if($type='module'){
			$db->setQuery("UPDATE #__modules SET published=1 WHERE module = '$id'");
			if(!$db->query()) {
				echo "<font color='red'>$db->stderr()</font><br>";
				return false;
			}
			else{
				echo "<font color='green'><b>$id $group $type ".JText::_('ENABLED')."</font><br>";
				return true;
			}	
		}
	}
	
	enablePackage('plugin','jconnect','system');
	enablePackage('plugin','jconnect','user');
	
?>
	<p></p>
	<div style="font-size: 15px; font-weight: bold; padding: 10px;">
		JConnect Installtion Done ! 
		<a href="index.php?option=com_jconnect"><?php echo "Configure Now!";?></a>
	</div>