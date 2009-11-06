<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access');?>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
        	<th width='5'>
        		<?php echo JText::_('ID'); ?>
        	</th>
        	<th width='5'>
        		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->exapps ); ?>);" /> 
        	</th>
            <th>
                <?php echo JText::_( 'APPNAME' ); ?>
            </th>
            <th width='5'>
				<?php echo JText::_('APP_ENABLED');?>           
            </th>
            <th>
                <?php echo JText::_( 'HOST' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'PATH' ); ?>
            </th>
            <th width='30'>
                <?php echo JText::_( 'PORT' ); ?>
            </th>
        </tr>            
    </thead>
    <?php
    for($lc=0;$lc<sizeof($this->exapps);$lc++){
    	$exApp=$this->exapps[$lc];
    	$checkBox=JHTML::_("grid.id",$lc,$exApp->appID);
    	$link=JRoute::_("index.php?option=com_jconnect&controller=exApps&task=edit&cid[]=".$exApp->appID);
    	$published 	= JHTML::_('grid.published',$exApp ,$lc);
    	
    	echo "<tr>";
    	echo "<td align='center'> " .($lc+1)." </td>";
    	echo "<td align='center'> $checkBox </td>";
    	echo "<td > <a href='$link' >$exApp->appName</a> </td>";
    	echo "<td align='center'>$published</td>";
    	echo "<td > $exApp->host </td>";
    	echo "<td > $exApp->path </td>";
    	echo "<td > $exApp->port </td>";
    	echo "</tr>";
    }
    ?>
    </table>
</div>
 
<input type="hidden" name="option" value="com_jconnect" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="exApps" />
 
</form>
