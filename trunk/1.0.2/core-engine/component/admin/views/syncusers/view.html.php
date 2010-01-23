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

class JconnectViewSyncusers extends JView{
	
	public function display($tmpl=null){
		JToolBarHelper::title(JText::_('SYNC_USER_MANAGE'),"generic.png");
		JToolBarHelper::trash("ban",JText::_('BAN'));
		JToolBarHelper::trash("deban",JText::_('DEBAN'));
		JToolBarHelper::cancel("close",JText::_('CLOSE'));
		
		$data=$this->get('data');
		$pagination=$this->get('pagination');
		$model=$this->getModel();
		$this->assignRef('dataList',$data);
		$this->assignRef('pagination',$pagination);
		$this->assignRef('usernameFilter',$model->getState('usernameFilter'));
		$this->assignRef('appNameFilter',$model->getState('appNameFilter'));
		$this->assignRef('stateFilter',$model->getState('stateFilter'));
		$this->assignRef('banFilter',$model->getState('banFilter'));
		parent::display($tmpl);
	}
}