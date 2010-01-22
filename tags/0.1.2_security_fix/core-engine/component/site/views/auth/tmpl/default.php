<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>
<?php 
	$error=JFactory::getSession()->get("LOGIN_ERROR");
	JFactory::getSession()->clear("LOGIN_ERROR");
?>

<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link href="components/com_jconnect/assests/style.css" rel="stylesheet"/>
<style type="text/css">
  <!--
  body { font-family: arial,sans-serif; background-color: #fff; margin-top: 2; }
  .c { width: 4; height: 4; } 
  a:link { color: #00c; } 
  a:visited { color: #551a8b; }
  a:active { color: #f00; }
  .form-noindent { background-color: #fff; border: 1px solid #c3d9ff; }
  --> 
</style>
<style type="text/css"><!--
.gaia.le.lbl { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.fpwd { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.chusr { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.val { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.button { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.rem { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }

.gaia.captchahtml.desc { font-family: arial, sans-serif; font-size: smaller; } 
.gaia.captchahtml.cmt { font-family: arial, sans-serif; font-size: smaller; font-style: italic; }
  
--></style>
  
  <title><?php echo JText::_('JC_LOGIN');?></title>
  <style type="text/css"><!--
  
    body {
      font-family: arial, sans-serif;
      margin: 0;
      padding: 13px 15px 15px;
      
    }
    .body {
      margin: 0;
    }
   
    div.errorbox-good {}

    div.errorbox-bad {} 

    div.errormsg { color: red; font-size: smaller; font-family: arial,sans-serif;}
    font.errormsg { color: red; font-size: smaller; font-family: arial,sans-serif;}
  
    
    div.topbar {
      font-size:smaller;
      margin-right: -5px;
      text-align:right;
      white-space:nowrap;
    }
    div.header {
      margin-bottom: 9px;
      margin-left: -2px;
      position:relative;
      zoom: 1
    }
    div.header img.logo {
      border: 0;
      float:left;
    }
    div.header div.headercontent {
      float:right;
      margin-top:17px;
    }
    div.header:after{
      content:".";
      display:block;
      height:0;
      clear:both;
      visibility:hidden;
    }
    div.pagetitle {
      font-weight:bold;
    }
    
    .footer { 
      color: #666;
      font-size: smaller;
      margin-top: 40px;
      text-align: center;
    }
    
    table#signupform {
      left: -5px;
      top: -7px;
      position:relative;
    }
    table#signupform td{
      padding: 7px 5px;
    }
    table#signupform td table td{
      padding: 1px;
    }
  
    
    
    hr {
      border: 0;
      background-color:#DDDDDD;
      height: 1px;
      width: 100%;
      text-align: left;
      margin: 5px;
    }
    

    
    
  --></style>
</head><body dir="ltr" onload="gaia_setFocus();">
  <div id="main">

<div class="header">
  <img class="logo" src="components/com_jconnect/assests/jclogin.gif" alt="JConnect Login">
</div>
  <div id="maincontent">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
  <td valign="top" width="75%">
<div style="font-size: 83%;">
<span style="font-size:20px;font-weight: bold;">
<?php echo JText::_('JC_LOGIN_HEADING');?>
</span>
<br><br>
<a href='http://jconnect.googlecode.com'>JConnect</a> <?php echo JText::_('JC_LOGIN_LINE_1');?>
<br><br>
<?php echo JText::_('JC_LOGIN_LINE_2');?>
<br><br>
<b><?php echo JText::_('JC_LOGIN_LINE_3');?></b><br><br> 
<?php echo JText::_('JC_LOGIN_LINE_4');?>
</div>
  </td>
  <td style="padding-left: 10px;" valign="top" align="center">
  <div id="rhs">
  <div id="rhs_login_signup_box">
<script>

function gaia_setFocus() {
  var f = null;
  if (document.getElementById) { 
    f = document.getElementById("gaia_loginform");
  } else if (window.gaia_loginform) { 
    f = window.gaia_loginform;
  } 
  if (f) {
    if (f.username && (f.username.value == null || f.username.value == "")) {
      f.username.focus();
    } else if (f.password) {
      f.password.focus();
    } 
  }
}
</script>
<style type="text/css"><!--
  div.errormsg { color: red; font-size: smaller; font-family:arial,sans-serif; }
  font.errormsg { color: red; font-size: smaller; font-family:arial,sans-serif; }  
--></style>
<style type="text/css"><!--
.gaia.le.lbl { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.fpwd { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.chusr { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.val { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.button { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.rem { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }

.gaia.captchahtml.desc { font-family: arial, sans-serif; font-size: smaller; } 
.gaia.captchahtml.cmt { font-family: arial, sans-serif; font-size: smaller; font-style: italic; }
  
--></style>
<form action="index.php" method="post" id="gaia_loginform">
<div id="gaia_loginbox">
<table class="form-noindent" width="100%" border="0" cellpadding="5" cellspacing="3">
  <tbody><tr>
  <td style="text-align: center;" valign="top" bgcolor="#e8eefa" nowrap="nowrap">
  <div class="loginBox">
  <table id="gaia_table" align="center" border="0" cellpadding="1" cellspacing="0">
  <tbody><tr>
<td colspan="2" align="center">
  <font size="-1">
  <?php echo JText::_('SIGN_IN_WITH_UR');?>
  </font>
  <img class="account" src="components/com_jconnect/assests/jcaccount.png" alt="JConnect Account">
  			<?php if($error) {?>
			<div id="error" class="error">
				<?php echo $error;?>
			</div>
			<?php }?>
  <table>
  <tbody><tr>
  <td valign="top">  
  </td>
  <td valign="middle">
  </td>
  </tr>
</tbody></table>
</td>
</tr>
  <script type="text/javascript"><!--
    function onPreCreateAccount() {
    
      return true;
    
    }

    function onPreLogin() {
    
      
      if (window["onlogin"] != null) {
        return onlogin();
      } else {
        return true;
      }
    
    }
  --></script>
<tr>
  <td colspan="2" align="center">
  </td>
</tr>
<tr>
  <td nowrap="nowrap">
  <div align="right">
  <span class="gaia le lbl">
  <?php echo JText::_('USERNAME')?>:
  </span>
  </div>
  </td>
  <td>
  <input name="username" id="username" size="18" value="" class="gaia le val" type="text">
  </td>
</tr>
<tr>
  <td></td>
  <td align="left">
  </td>
</tr>
<tr>
  <td align="right" nowrap="nowrap">
  <span class="gaia le lbl">
  <?php echo JText::_('PASSWORD');?>:
  </span>
  </td>
  <td>
  <input name="password" id="password" size="18" class="gaia le val" type="password">
  </td>
</tr>
<tr>
  <td>
  </td>
  <td align="left">
  </td>
</tr>
  <tr>
  <td valign="top" align="right">
  <input name="persistant" id="persistant" value="yes"  type="checkbox">
  <input type='hidden' name='task' value='login'/>
	<input type='hidden' name='controller' value='auth'/>
	<input type="hidden" name="option" value="com_jconnect" />
	<input type="hidden" name="format" value="raw" />
	<input type='hidden' name='token' value='<?php echo $this->token;?>'/>
  </td>
  <td>
  <label class="gaia le rem">
  <?php echo JText::_('STAY_SIGNED')?>
  </label>
  </td>
</tr>
<tr>
  <td>
  </td>
  <td align="left">
  <input class="gaia le button" name="Login" value="<?php echo JText::_('SIGN_IN');?>" type="submit"> 
  <input class="gaia le button" name="Cancel" value="<?php echo JText::_('CANCEL');?>" type="button" onclick="window.close()">
  </td>
</tr>
<tr id="ga-fprow">
  <td colspan="2" class="gaia le fpwd" valign="bottom" align="center" height="33">
  </td>
</tr>
  </tbody></table>
  </div>
  </td>
  </tr>
</tbody></table>
</div>
<input name="asts" id="asts" value="" type="hidden">
</form>
  </div>
  </div>
  </td>
  </tr>
  </tbody></table>
  </div>
<div class="footer">
  ©2009 JConnect
  -
  <a href="http://jconnect.googlecode.com/"><?php echo JText::_('JC_HOME');?></a>
  -
  <a href="http://www.joomla.org"><?php echo JText::_('JOOMLA_HOME');?></a>
  -
  <a href="http://code.google.com/p/jconnect/w/list"><?php echo JText::_('HELP')?></a>
  -
</div>
  </div>
  </body></html>