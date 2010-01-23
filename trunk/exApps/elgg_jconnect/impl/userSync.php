<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
class JCElggUserSync extends JCUserSync{

	public function deleteUser($username){
		global $CONFIG;
		$sql="SELECT salt,guid FROM ".$CONFIG->dbprefix."users_entity WHERE username='$username'";
		$user=get_data($sql);
		if(!$user[0]) throw new Exception("username does not exists");

		$sql1="DELETE FROM ".$CONFIG->dbprefix."users_entity WHERE guid=".$user[0]->guid;
		$sql2="DELETE FROM ".$CONFIG->dbprefix."metadata WHERE entity_guid=".$user[0]->guid;
		$sql3="DELETE FROM ".$CONFIG->dbprefix."entities WHERE guid=".$user[0]->guid." OR owner_guid=".$user[0]->guid;

		delete_data($sql1);
		delete_data($sql2);
		delete_data($sql3);

		return true;
	}

	public function getUserCount(){
		global $CONFIG;
		$sql="SELECT COUNT(*) as cnt FROM ".$CONFIG->dbprefix."users_entity";
		$res=get_data_row($sql);
		return $res->cnt;
	}

	public function getUserGroups(){
		return array('admin','user');
	}

	public function getUserDetails($chunkSize,$chunkNo){
		global $CONFIG;
		$sql="SELECT guid,username,email FROM ".$CONFIG->dbprefix."users_entity ORDER BY guid ASC LIMIT ".
		(($chunkNo-1)*$chunkSize) ."," .($chunkSize);

		$result=get_data($sql);
		$users=array();
		$lc=0;
			
		foreach ($result as $id=>$val){
			$group=(JCElggHelper::isAdmin($val->guid))?"admin":'user';
			$arr=array($val->username,$val->email,$group);
			array_push($users,$arr);
		}

		return $users;
	}
	
	public function getUsers($usernameList){
		$userList=array();
		global $CONFIG;
		$sql="SELECT guid,username,email FROM ".$CONFIG->dbprefix."users_entity ".
				"WHERE username IN ('".implode("','",$usernameList)."')";
		
		$result=get_data($sql);
			
		$users=array();
		foreach ($result as $id=>$val){
			$group=(JCElggHelper::isAdmin($val->guid))?"admin":'user';
			$arr=array($val->username,$val->email,$group); 
			array_push($users,$arr);
		}
		
		return $users;
	}
}