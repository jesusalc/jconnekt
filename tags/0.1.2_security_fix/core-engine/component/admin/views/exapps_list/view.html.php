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

class JconnectViewExapps_list extends JView{
	
	public function display($tpl=null){
		JToolBarHelper::title("JConnect - ".JText::_('EXAPPS'),"generic.png");
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::cancel("cancel",JText::_('CLOSE'));
		
		$exApps=$this->getModel()->getExApps();
		$msg="The List goes here..";
		$this->assignRef("exapps",$exApps);
		parent::display($tpl);
	}
}