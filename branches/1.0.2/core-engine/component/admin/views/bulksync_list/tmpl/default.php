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
        	<th width='5'>
        		<?php echo JText::_('ID'); ?>
        	</th>
        	<th width='5'>
        		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->infoList ); ?>);" /> 
        	</th>
            <th >
                <?php echo JText::_( 'APPNAME' ); ?>
            </th>
            <th width='120'>
                <?php echo JText::_( 'TOTAL_USERS' ); ?>
            </th>
            <th width='120'>
                <?php echo JText::_( 'NEW_TO_JC' ); ?>
            </th>
        </tr>             
    </thead>
    <?php 
    	
    	foreach ($this->infoList as $id=>$info){
    		$checkBox=JHTML::_("grid.id",$id,$info->appName);
    		echo "<tr>";
    		
    		echo "<td>".($id+1)."</td>";
    		echo "<td>".$checkBox."</td>";
    		echo "<td>$info->appName</td>";
    		echo "<td>$info->totalUsers</td>";
    		echo "<td>$info->newToJoomlaUsers</td>";
    		
    		echo "</tr>";
    	}
    	
    	
    ?>
    </table>
</div>
 
<input type="hidden" name="option" value="com_jconnect" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="bulkSync" />
 
</form>
