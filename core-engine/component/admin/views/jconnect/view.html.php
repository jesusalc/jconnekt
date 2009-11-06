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

class JconnectViewJconnect extends JView{
	public function display($tmp=null){
		JToolBarHelper::title("JConnect","generic.png");
		JToolBarHelper::cancel("cancel",JText::_('CLOSE'));
		parent::display($tmpl);
	}
}
 