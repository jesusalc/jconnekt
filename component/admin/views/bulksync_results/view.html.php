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
class JconnectViewBulksync_results extends JView{
	
	public function display($tmpl=null){
		JToolBarHelper::title(JText::_('BULK_SYNC')." <small> ::[".JText::_('RESULTS')."]</small>","generic.png");
		JToolBarHelper::addNewX("resolveConflicts",JText::_('RESOLVE_CONFLICTS'));
		JToolBarHelper::cancel("closeResults",JText::_('CLOSE'));
		
		$model=$this->getModel();
		$this->assignRef("result",$model->get("result"));
		
		parent::display($tmpl);
	}
}