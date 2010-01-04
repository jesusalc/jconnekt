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
 * This class controlles views of exApps [exAppsList,exAppsForm]
 *
 * @package    JConnect.component.admin.controllers
 */
class JconnectControllerExApps extends JController
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
		try{
			JRequest::setVar("view","exapps_list");
			parent::display();
		}
		catch(Exception $ex){
			JError::raiseError(0,$ex->getMessage());
		}
	}

	function add(){
		//cid with no value is the way model identify this as a newone but not to edit...
		JRequest::setVar("cid",array());
		$this->edit();
	}

	function edit(){

		JRequest::setVar("view","exapps_form");
		parent::display();

	}

	function publish(){
		$appIDs=JRequest::getVar("cid",array(),"post","array");
		$db=JFactory::getDBO();
		$sql="UPDATE #__jc_exApps SET published=NOT(published) WHERE appID=$appIDs[0]";
		$db->Execute($sql);
		$this->setRedirect("index.php?option=com_jconnect&controller=exApps");

		if($db->getErrorNum()){
			throw new Exception($db->getErrorNum() ." :: ". $db->getErrorMsg());
		}
	}

	function unpublish(){
		$this->publish();
	}

	function remove(){
		$model=$this->getModel("exapps_list");
		$msg=null;
		$type=null;
		if(!$model->remove()){
			foreach($model->getError() as $id => $val){
				$msg=$msg.$val."<br>";
			}
			$type="error";
		}
		else{
			$msg="Deletion Succeeded!";
			$type="message";
		}

		$this->setRedirect("index.php?option=com_jconnect&controller=exApps",$msg,$type);
	}

	function cancel(){
		$this->setRedirect("index.php?option=com_jconnect");
	}

	function cancelForm(){
		$this->setRedirect("index.php?option=com_jconnect&controller=exApps");
	}

	function saveForm(){
		$model=$this->getModel("exapps_form");
		$msg=null;
		$type=null;
		if(!$model->store()){
			foreach($model->getError() as $id => $val){
				$msg=$msg.$val."<br>";
			}
			$type="error";
		}
		else{
			$msg="Changes saved successfully";
			$type="message";
		}

		$this->setRedirect("index.php?option=com_jconnect&controller=exApps",$msg,$type);

	}


	function applyForm(){
		$model=$this->getModel("exapps_form");
		$msg=null;
		$type=null;
		if(!$model->store()){
			foreach($model->getError() as $id => $val){
				$msg=$msg.$val."<br>";
			}
			$type="error";
		}
		else{
			$msg="Changes saved successfully";
			$type="message";
		}

		$this->setRedirect("index.php?option=com_jconnect&controller=exApps&task=edit&cid[]=".JRequest::getInt("appID"),$msg,$type);
	}

	function sendInfo(){
		$msg="JConnect Information send to ExApp!";
		$type="message";
		try{
			$appID=JRequest::getInt('appID');
			$appName=JCHelper::getAppName($appID);
			$exApp=new ExApp($appName);
			$scheme = 'http';
			if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
				$scheme .= 's';
			}

		
			$refferer=$_SERVER[HTTP_REFERER];
			
			$refferer=preg_replace("/$scheme:\/\//","",$refferer);
			
			$domain=preg_replace("/\/.*/","",$refferer);
			
			
			$url="$scheme://".$domain.":".getenv('SERVER_PORT').str_replace("/administrator","",getenv('SCRIPT_NAME'));
			
			$meta=array(
				"JOOMLA_URL"=>$url,
				"JC_APPNAME"=>$appName
			);

			$exApp->loadSysInfo($meta);
		}
		catch(Exception $ex){
			$msg=$ex->getMessage();
			$type="error";
		}

		$this->setRedirect("index.php?option=com_jconnect&controller=exApps&task=edit&cid[]=".JRequest::getInt("appID"),$msg,$type);
	}

}