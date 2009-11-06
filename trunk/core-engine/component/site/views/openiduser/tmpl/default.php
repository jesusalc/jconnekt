<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/ defined('_JEXEC') or die('Restricted access'); ?>

<html>
	<head>
		<?php if($this->username) {?>
			<link rel="openid.server" href="<?php echo $this->openid_server;?>"/>
		<?php }?>
	</head>
	<body>
		<?php if($this->username) {?>
			<h2>OpenID for <?php echo $this->username;?></h2>
		<?php }?>
	</body>
</html>