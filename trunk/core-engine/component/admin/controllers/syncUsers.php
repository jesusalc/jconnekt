<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JconnectControllerSyncUsers extends JController{
	public function display(){
		JRequest::setVar("view","syncusers");
		parent::display();
	}
	
	public function close(){
		$this->setRedirect("index.php?option=com_jconnect");
	}
	
	
	public function ban(){
		$cids=JRequest::getVar("cid",array(),"","array");
		foreach ($cids as $cid){
			$inp=explode(",",$cid);
			$su=new SyncUser($inp[0],$inp[1]);
			$su->status="BAN";
			$su->save();
		}
		$this->setRedirect("index.php?option=com_jconnect&controller=syncUsers");
	}
	
	public function deban(){
		$cids=JRequest::getVar("cid",array(),"","array");
		foreach ($cids as $cid){
			$inp=explode(",",$cid);
			$su=new SyncUser($inp[0],$inp[1]);
			$su->status="OK";
			$su->save();
		}
		$this->setRedirect("index.php?option=com_jconnect&controller=syncUsers");
	}
}