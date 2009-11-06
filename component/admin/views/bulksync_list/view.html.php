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

class JconnectViewBulksync_list extends JView{
	
	public function display($tmpl=null){
		JToolBarHelper::title(JText::_('BULK_SYNC'),"generic.png");
		JToolBarHelper::archiveList("startSync",JText::_('START_SYNC'));
		JToolBarHelper::cancel("closeSync",JText::_('CLOSE'));
		$model=$this->getModel();
		
		$this->assignRef("infoList",$model->getInfoList());
		parent::display($tmpl);
	}
}