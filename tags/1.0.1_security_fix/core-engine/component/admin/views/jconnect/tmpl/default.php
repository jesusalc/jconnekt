<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>

<div style='width:700px; margin: auto; font-size: 15px; font-weight: bold ; text-align: center;'>
	<table width=600px>
		<tr>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=exApps" >
				<img src='components/com_jconnect/images/exApps.png' /></a>
			</td>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=syncUsers" >
				<img src='components/com_jconnect/images/syncUser.png' /></a>
			</td>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=bulkSync" >
				<img src='components/com_jconnect/images/bulkSync.png' /></a>
			</td>
		</tr>
		<tr>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=exApps" >
				<?php echo JText::_('EXAPPS');?></a><br/>
			</td>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=syncUsers" >
				<?php echo JText::_('SYNC_USER_MANAGEMENT');?></a><br/>
			</td>
			<td align="center">
				<a href="index.php?option=com_jconnect&controller=bulkSync" >
			    <?php echo JText::_('BULK_SYNC');?></a><br/>
			</td>
		</tr>
	</table>
</div>

<form  action="index.php" method="post" name="adminForm" id="asminForm">
	<input type="hidden" name="option" value="com_jconnect" />
	<input type="hidden" name="task" value="" />
</form>