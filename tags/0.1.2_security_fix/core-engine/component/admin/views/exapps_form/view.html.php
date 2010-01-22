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

class JconnectViewExapps_form extends JView{
	public function display(){
		JToolBarHelper::save('saveForm');
		JToolBarHelper::apply("applyForm");

		$exApp=$this->get("exApp");
		$meta=$this->getModel()->getAllMeta($exApp->appID);

		if($exApp->appName){
			//edit... //when exApp is already exists...
			JToolBarHelper::cancel('cancelForm',JText::_('CLOSE'));
			$action="edit";
			try{
				//do user group variable assignment..
				$jcGroupInMap=$this->getModel()->getJCGroupInMap($exApp->appID);
				$this->assignRef('jcGroupInMap',$jcGroupInMap);
				$jcGroupOutMap=$this->getModel()->getJCGroupOutMap($exApp->appID);
				$this->assignRef('jcGroupOutMap',$jcGroupOutMap);
	 			$exAppGroups=$this->getModel()->getExAppGroups($exApp->appID);
				$this->assignRef('exAppGroups',$exAppGroups);
				$joomlaGroups=$this->getModel()->getJoomlaGroups();
				$this->assignRef('joomlaGroups',$joomlaGroups);
			}
			catch(Exception $ex){
				JError::raiseWarning(0,'User Groups Cannot be loaded!');
			}
		}
		else{
			JToolBarHelper::cancel('cancelForm');
			$action="add";
		}
		JToolBarHelper::title(JText::_('EXAPP')."<small>:[$action]</small>","generic.png");


		$this->assignRef("exApp",$exApp);
		$this->assignRef("meta",$meta);
		parent::display();
	}
}