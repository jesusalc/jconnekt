<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
class JCElggHelper{
	public static function getInfo(){
		global $CONFIG;
		$sql="SELECT id FROM {$CONFIG->dbprefix}metastrings WHERE string IN ('yes','1')";
		$res=get_data($sql);
		$data=array();
		foreach ($res as $val){array_push($data,$val->id);}
		$value_ids=implode(",",$data);
		$sql="SELECT entity_guid FROM {$CONFIG->dbprefix}metadata md INNER JOIN {$CONFIG->dbprefix}metastrings ms ON md.name_id=ms.id WHERE string='admin' AND value_id IN ($value_ids) ORDER BY entity_guid ASC LIMIT 0,1";
		$res=get_data_row($sql);
		$admin_guid=(int)$res->entity_guid;

		$sql="SELECT guid FROM {$CONFIG->dbprefix}sites_entity LIMIT 0,1";
		$res=get_data_row($sql);
		$site_id=(int)$res->guid;

		$sql="SELECT MAX(guid) as guid FROM {$CONFIG->dbprefix}users_entity";
		$users_guid=(int)get_data_row($sql)->guid;
		$sql="SELECT MAX(guid) as guid FROM {$CONFIG->dbprefix}sites_entity";
		$sites_guid=(int)get_data_row($sql)->guid;
		$sql="SELECT MAX(guid) as guid FROM {$CONFIG->dbprefix}groups_entity";
		$groups_guid=(int)get_data_row($sql)->guid;
		$sql="SELECT MAX(guid) as guid FROM {$CONFIG->dbprefix}objects_entity";
		$objects_guid=(int)get_data_row($sql)->guid;

		$next_guid=max(array($users_guid,$site_guid,$groups_guid,$objects_guid)) +1;

		$rtn=new stdClass();
		$rtn->admin_guid=$admin_guid;
		$rtn->site_guid=$site_id;
		$rtn->next_guid=$next_guid;

		return $rtn;

	}

	public static function addUser($username,$email,$password_clr){
		global $CONFIG;

		$salt=substr(md5(rand()),0,8);
		$password=md5($password_clr.$salt);

		$info=JCElggHelper::getInfo();

		$sql="INSERT INTO {$CONFIG->dbprefix}users_entity (guid,name,username,password,salt,email) VALUES (".
			"$info->next_guid,'$username','$username','$password','$salt','$email'".
			")";
		insert_data($sql);

		$sql="INSERT INTO {$CONFIG->dbprefix}entities (guid,type,subtype,owner_guid,site_guid,container_guid,access_id) VALUES (".
			"$info->next_guid,'user',0,$info->admin_guid,$info->site_guid,$info->admin_guid,2".
			")";

		insert_data($sql);

		$sql="INSERT INTO {$CONFIG->dbprefix}metadata (entity_guid,name_id,value_id,value_type,owner_guid,access_id) VALUES(".
			"$info->next_guid,20,11,'text',$info->admin_guid,2".
			")";

		insert_data($sql);

		return $info->next_guid;
	}

	public static function updateUser($username,$email,$password=null){
		global $CONFIG;
		$sql="SELECT salt FROM ".$CONFIG->dbprefix."users_entity WHERE username='$username'";
		$user=get_data($sql);
		if(!$user[0]) throw new Exception("username does not exists");
		if($password) $password=generate_user_password($user[0],$password);
		$sql="UPDATE ".$CONFIG->dbprefix."users_entity SET ";
		if($password) $sql.=" password='$password' ,";
		if($email) $sql.=" email='$email' ,";
		$sql=substr($sql,0,strlen($sql)-1)." WHERE username='$username'";

		if($email || $password)
		if(!update_data($sql)) throw new Exception('Cannot update - database refused');
	}
	
	public static function isAdmin($userID){
		global $CONFIG;
		$user=get_user($userID);
		return ($user->admin)?true:false; 
	}
}

