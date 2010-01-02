<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>
 
<form action="index.php" method="post" name="adminForm" id="adminForm" >
<table>
	<tr>
		<td>
			<div class="col100">
		    <fieldset class="adminform">
		        <legend><?php echo JText::_( 'EXAPP' ); ?></legend>
		        <table>
		        	<tr>
		        		<td width='80%'>
					        <table class="admintable" >
					         <tr>
					            <td width="100" align="right" class="key">
					                <label for="appName">
					                    <?php echo JText::_( 'APPNAME' ); ?>:
					                </label>
					            </td>
					            <td>
					                <input type="text" name="appName" id="appName" value="<?php echo $this->exApp->appName;?>" />
					            </td>
					         </tr>
					         <tr>
					            <td width="100" align="right" class="key">
					                <label for="secretKey">
					                    <?php echo JText::_( 'SEC_KEY' ); ?>:
					                </label>
					            </td>
					            <td>
					                <span id="secretKeyShow"><?php echo $this->exApp->secretKey;?> </span>
					                <input type="hidden" name="secretKey" id="secretKey" value="<?php echo $this->exApp->secretKey;?> " />
					    			<input class="button" type="button" value="<?php echo JText::_('GENERATE');?>" onclick="generateSecret()"/>        
					            </td>
					         </tr>
					         <tr>
					            <td width="100" align="right" class="key">
					                <label for="host">
					                    <?php echo JText::_( 'HOST' ); ?>:
					                </label>
					            </td>
					            <td>
					                <input class="text" type="text" name="host" id="host" size="30" maxlength="255" value="<?php echo $this->exApp->host;?>" />
					            </td>
					         </tr>
					         <tr>
					            <td width="100" align="right" class="key">
					                <label for="path">
					                    <?php echo JText::_( 'PATH' ); ?>:
					                </label>
					            </td>
					            <td>
					                <input class="text" type="text" name="path" id="path" size="75" maxlength="255" value="<?php echo $this->exApp->path;?>" />
					            </td>
					         </tr>
					         <tr>
					            <td width="100" align="right" class="key">
					                <label for="port">
					                    <?php echo JText::_( 'PORT' ); ?>:
					                </label>
					            </td>
					            <td>
					                <input class="text" type="text" name="port" id="port" size="5" maxlength="5" value="<?php echo $this->exApp->port;?>" />
					            </td>
					         </tr>
					    	</table>
		        		</td>
		        		<td >
		        			<?php if(isset($this->exApp->appID) && $this->exApp->appID>0) {?>
		        			<div style='width:200px;margin:auto;'>
		        				<a href='index.php?option=com_jconnect&controller=exApps&task=sendInfo&appID=<?php echo $this->exApp->appID;?>'><font size='6'><?php echo JText::_('SEND_INFO');?></font></a> 
		        			</div>
		        			<?php }?>
		        		</td>
		        	</tr>
		        </table>
		    </fieldset>
		</div>
		</td>
		<td>
			<?php if(isset($this->exApp->appID) && !(isset($this->isError))) {?>
			<div class="col100">
		    <fieldset class="adminform">
		        <legend><?php echo JText::_( 'ADVANCED_CTRL' ); ?></legend>
		        <table class="admintable" >
		        <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'IP' ); ?>:
		                </label>
		            </td>
		            <td>
		                <input type="text" class="text" name="meta[IP]" value="<?php echo (isset($this->meta->IP))?$this->meta->IP:"";?>"/>
		            </td>
		         </tr>
		         <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'ALLOW_IN' ); ?>:
		                </label>
		            </td>
		            <td>
		                <input type="checkbox" id="allow_incoming" class="checkbox"/>
		            </td>
		         </tr>
		         <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'ALLOW_OUT' ); ?>:
		                </label>
		            </td>
		            <td>
		                <input type="checkbox" id="allow_outgoing" class="checkbox" "/>
		            </td>
		         </tr>
		         <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'ALLOW_CREATE_USER' ); ?>:
		                </label>
		            </td>
		            <td>
	                    <?php 
	                		$val=array('allow'=>'allow','deny'=>'deny');
	                		echo JCHelper::getCheckBox($val,"meta[create_user]",$this->meta->create_user);
	                	?>
		                
		            </td>
		         </tr>
		         <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'ALLOW_DELETE_USER' ); ?>:
		                </label>
		            </td>
		            <td>
		                <?php 
	                		$val=array('allow'=>'allow','deny'=>'deny');
	                		echo JCHelper::getCheckBox($val,"meta[delete_user]",$this->meta->delete_user);
	                	?>
		            </td>
		         </tr>
		          <tr>
		            <td width="100" align="right" class="key">
		                <label for="appName">
		                    <?php echo JText::_( 'ALLOW_UPDATE_USER' ); ?>:
		                </label>
		            </td>
		            <td>
		                <?php 
	                		$val=array('allow'=>'allow','deny'=>'deny');
	                		echo JCHelper::getCheckBox($val,"meta[update_user]",$this->meta->update_user);
	                	?>
		            </td>
		         </tr>
		         
		    </table>
		    </fieldset>
		</div>
		<?php }?>
		</td>
	</tr>
	<?php if(isset($this->jcGroupInMap) && !isset($this->isError)){?>
	<tr>
		<td>
			<div class="col100">
		    	<fieldset class="adminform">
		        <legend><?php echo JText::_( 'UG_MAPPING' ); ?></legend>
		        	<table class="admintable" >
		        		<tr><td style='border-right-width: thin;border-right-style: solid;border-right-color: black'valign="top" width='350'>
		        		<h3 style='margin: 0px;'><?php echo JText::_('INCOMING');?></h3>
		        		<table>
		        		<tr>
				            <td width="200" align="right" class="key"> 
				                    <?php echo JText::_( 'EXAPP_UG' ); ?>
				            </td>
				            <td width="200" align="right" class="key">
				                    <?php echo JText::_( 'JOOMLA_UG' ); ?>
				            </td>
				        </tr>
				            <?php 
				            	foreach($this->jcGroupInMap as $exApp=>$joomla){
				            		echo "<tr>".
				            		"<td>$exApp</td>".
				            		"<td>".JCHelper::getCheckBox($this->joomlaGroups,"jcGroupsIn[$exApp]",$joomla)."</td>".
				            		"</tr>";
				            	}
				            ?> 
				        </table>
				        </td>
				        <td valign="top">
				        	<h3 style='margin: 0px;'><?php echo JText::_('OUTGOING');?></h3>
				        	<table>
			        		<tr>
					            <td width="200" align="right" class="key"> 
					                    <?php echo JText::_( 'JOOMLA_UG' ); ?>
					            </td>
					            <td width="200" align="right" class="key">
					                    <?php echo JText::_( 'EXAPP_UG' ); ?>
					            </td>
					        </tr>
					            <?php 
					            	foreach($this->jcGroupOutMap as $joomla=>$exApp){
					            		echo "<tr>".
					            		"<td>$joomla</td>".
					            		"<td>".JCHelper::getCheckBox($this->exAppGroups,"jcGroupsOut[$joomla]",$exApp)."</td>".
					            		"</tr>";
					            	}
					            ?> 
					        </table>
				        </td></tr>
		        	</table>
		        </fieldset>
		    </div>
		</td>
	</tr>
	<?php }?>
	
</table>

 
<div class="clr"></div>


 
<input type="hidden" name="option" value="com_jconnect" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="exApps"/>
<input type="hidden" name="appID" value="<?php echo (isset($this->exApp->appID))?$this->exApp->appID:""; ?>" />
<?php if(isset($this->exApp->appID) && !isset($this->isError)){?>
<input type="hidden" name="meta[allow_incoming]" id="meta_allow_incoming" value="<?php echo $this->meta->allow_incoming;?>" />
<input type="hidden" name="meta[allow_outgoing]" id="meta_allow_outgoing" value="<?php echo $this->meta->allow_outgoing;?>" />
<?php }?>
</form>

<script type="text/javascript">
	function generateSecret(){
		var textBox=document.getElementById("secretKey");
		var chars="abcdefghijklnqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		var key="";
		for(var lc=0;lc<32;lc++){
			var id=Math.random()*chars.length;
			key+=chars.charAt(id);
		}
		key+="::";
		for(var lc=0;lc<25;lc++){
			var id=Math.random()*chars.length;
			key+=chars.charAt(id);
		}
		document.getElementById("secretKeyShow").innerHTML=key;
		textBox.value=key;
	}

	function submitbutton(pressbutton) {
		
		if (pressbutton == 'cancelForm') {
			submitform( pressbutton );
			return;
		}


		var fields=new Array("appName","secretKey","host","path","port");
		if(document.getElementById("task")=="cancelForm");
		for(var lc=0;lc<fields.length;lc++){
			var field=document.getElementById(fields[lc]);
			if(field.value.trim()==""){
				alert(fields[lc] + " should have an value");
				field.focus();
				return false;
			}
		}

		//set the incoming/outgoing values to the hidden field
		var incoming=document.getElementById("allow_incoming");
		var outgoing=document.getElementById("allow_outgoing");

		if(incoming && outgoing){
			document.getElementById("meta_allow_incoming").value=(incoming.checked)?1:0; 
			document.getElementById("meta_allow_outgoing").value=(outgoing.checked)?1:0; 
		}		

		submitform( pressbutton );
	}

	var ai=document.getElementById("allow_incoming");
	if(ai) ai.checked=<?php echo (isset($this->meta->allow_incoming))?$this->meta->allow_incoming:1;?>;
	var ao=document.getElementById("allow_outgoing");
	if(ao) ao.checked=<?php echo (isset($this->meta->allow_outgoing))?$this->meta->allow_outgoing:1;?>;
	
</script>