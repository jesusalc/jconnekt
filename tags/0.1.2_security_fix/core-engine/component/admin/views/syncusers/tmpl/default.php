<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">

<table class="adminlist">

	<thead>
		<tr>
			<th width='5'><?php echo JText::_('#'); ?></th>
			<th width='5'><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo count( $this->dataList ); ?>);" /></th>
			<th><?php echo JText::_( 'USERNAME' ); ?></th>
			<th width='100'><?php echo JText::_( 'APPNAME' ); ?></th>
			<th width='300'><?php echo JText::_( 'BAN' ); ?></th>
			<th width='300'><?php echo JText::_( 'SYNC_STATE' ); ?></th>
		</tr>
		<tr>
			<td colspan='2'><b><font color='red'><?php echo JText::_('FILTERS');?></font></b></td>
			<td><input type="text" name="usernameFilter" id="usernameFilter" class="text" 
				value=''/></td>
			<td><select name="appNameFilter"  id="appNameFilter">
			<option value="*All*">*<?php echo JText::_('ALL');?>*</option>
			<?php
			$appNames=ExApp::getExAppList();
			foreach ($appNames as $appName){
				echo "<option value='$appName'>$appName</option>";
			}
			?>
			
			</select></td>
			<td>
				<select name='banFilter' id='banFilter'>
					<option value="*All*">*<?php echo JText::_('ALL');?>*</option>
					<option value="BAN"><?php echo JText::_('YES');?></option>
					<option value="OK"><?php echo JText::_('NO');?></option>
				</select>
			</td>
			<td>
				<select name='stateFilter' id='stateFilter'>
					<option value="*All*">*<?php echo JText::_('ALL');?>*</option>
					<option value="NA"><?php echo JText::_('N/A');?></option>
					<option value="exApp"><?php echo JText::_('NEED_VALIDATION_FROM_EXAPP');?></option>
					<option value="OK"><?php echo JText::_('VALIDATED');?></option>
				</select>
				<button value="Do Filter" onclick="doFilter();"><?php echo JText::_('DO_FILTER');?></button>
			</td>
		</tr>
	</thead>
	<?php
	foreach($this->dataList as $id=>$data){
		$send=$data->JID.",".$data->appID;
		$checkBox=JHTML::_("grid.id",$id,$send);

		echo "<tr>";
		echo "<td>".($this->pagination->limitstart + $id+1)."</td>";
		echo "<td>$checkBox</td>";
		echo "<td>$data->username</td>";
		echo "<td>$data->appName</td>";
		echo "<td>";
		if($data->status=="BAN") echo "YES";
		else echo "NO";
		echo "<td>";
		if($data->needSync==1) echo JText::_('NEED_VALIDATION_FROM_EXAPP');
		else if($data->needSync==null) echo JText::_('N/A');
		else if($data->needSync==0) echo JText::_('VALIDATED');
		echo "</td>";
		echo "</tr>";
	}
	echo "<tr align='center' ><td colspan='6'>".$this->pagination->getListFooter()."</td></tr>";
	?>
</table>
	<?php ?></div>

<input type="hidden" name="option" value="com_jconnect" /> <input
	type="hidden" name="task" value="" id="task"/> <input type="hidden"
	name="boxchecked" value="0" /> <input type="hidden" name="controller"
	value="syncUsers" /></form>

<script type="text/javascript">
	function doFilter(){
		var task=document.getElementById("task");
		task.value="display";
	}

	var usernameFilter=document.getElementById('usernameFilter');
	usernameFilter.value='<?php echo $this->usernameFilter;?>';

	var appNameFilter=document.getElementById('appNameFilter');
	appNameFilter.value='<?php echo $this->appNameFilter;?>';

	var stateFilter=document.getElementById('stateFilter');
	stateFilter.value='<?php echo $this->stateFilter;?>';

	var banFilter=document.getElementById('banFilter');
	banFilter.value='<?php echo $this->banFilter;?>';
	
</script>
