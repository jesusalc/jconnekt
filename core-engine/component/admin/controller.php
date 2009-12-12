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

/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class JconnectController extends JController
{
	
	public function __construct(){
		parent::__construct();
	}
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	
	
	function display()
	{
		if(JC_API==1){
			parent::display();
		}
		else{
			JError::raiseNotice(0,JText::_('API_NOT_ENABLED'));
		}
	}
	
	
	function cancel(){
		$this->setRedirect("index.php");
	}
		
}

