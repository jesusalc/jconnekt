<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class JconnectViewOpeniduser extends JView{
	public function display($tmp=null){
		$model=$this->getModel();
		$this->assignRef('username',$model->get('username'));
		$this->assignRef('openid_server',$model->get('openid_server'));
		parent::display($tmp);
	}
}
