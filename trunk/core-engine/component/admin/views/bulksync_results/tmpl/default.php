<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">

<h2><?php echo JText::_('RESULT_OF_BULK_SYNC_WITH');?> 
	<font color="red"><i><?php echo $this->result["appName"];?></i></font></h2>
<h3 class="syncHeader" ><?php echo JText::_('USER_STATS');?></h3>
<?php echo JText::_('USERS_SYNC_INTO_JOOMLA');?> : <?php echo $this->result["toJCount"];?> <br></br>

<h3 class="syncHeader" ><?php echo JText::_('EXCEPTIONS');?></h3>
<?php 
	foreach($this->result["exceptions"] as $ex){
		JError::raiseWarning(0,$ex[1]." for [$ex[0]]");
	}
	
	if(sizeof($this->result["exceptions"])<=0){
		echo JText::_('NO_EXCEPTIONS');
	}
?>

<h3 class="syncHeader" ><?php echo JText::_('USERNAME_CONFLICTS')?></h3>
<?php 
$session=JFactory::getSession();
$conflictSize=sizeof($session->get("conflicts",array()));
if($conflictSize<=0){
		echo JText::_('NO_USERNAME_CONFLICTS');
}
else{
?>
	<?php printf(JText::_('FOUND_CONFLICTS'),"<b>$conflictSize</b>")?>
	<br> <?php echo JText::_('SELECT_RESLOVE_OPTION');?>:
	<select name="conflict_option">
		<option value="<?php echo BulkSyncConstants::$PRESERVE_JCONNECT;?>"><?php echo JText::_('PRESERVE_JCONNECT');?></option>
		<option value="<?php echo BulkSyncConstants::$PRESERVE_EXAPP;?>"><?php echo JText::_('PRESERVE_EXAPP');?></option>
		<option value="<?php echo BulkSyncConstants::$BAN_FOR_EXAPP;?>"><?php echo JText::_('BAN_FOR_EXAPP');?></option>
	</select>
<?php }?>

<input type="hidden" name="option" value="com_jconnect" />
<input type="hidden" name="appName" value="<?php echo $this->result["appName"];?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="bulkSync" />
 
</form>
