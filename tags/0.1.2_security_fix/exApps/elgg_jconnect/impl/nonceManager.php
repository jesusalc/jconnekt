<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
class ElggNonceManager extends NonceManager{
	private $nonce_table;
	function __construct(){
		global $CONFIG;
		$this->nonce_table=$CONFIG->dbprefix."auth_nonce";
		$this->setUp();
	}
	
	function save(){
		$sql="INSERT INTO $this->nonce_table (nonce,timestamp,used) VALUES (".
		"'$this->nonce',$this->timestamp,$this->used) ON DUPLICATE KEY UPDATE ".
		"used=VALUES(used)";

		insert_data($sql);
	}
	
	function load($nonce){
		$sql="SELECT * FROM $this->nonce_table WHERE nonce='$nonce'";
		$res=get_data_row($sql);
		
		if(isset($res) && $res->nonce){
			$this->nonce=$res->nonce;
			$this->timestamp=$res->timestamp;
			$this->used=$res->used;
		}
	}
	
	function delete(){
		$sql="DELETE FROM $this->nonce_table WHERE nonce='$this->nonce'";
		if(!delete_data($sql)) throw new Exception("Deletion Failed!");
	}

	private function setUp(){
		$nonce =
            "CREATE TABLE $this->nonce_table (\n".
            "  nonce VARCHAR(32) NOT NULL,\n".
            "  timestamp INTEGER NOT NULL,\n".
            "  used INTEGER NOT NULL DEFAULT 0,\n".
            "  UNIQUE (nonce)\n".
            ") ";


		$sql="SELECT * FROM $this->nonce_table ";

		try{
			$res=get_data_row($sql);
			
		}
		catch(Exception $ex){
			
			//if No database table installed!
			if(insert_data($nonce)) throw new Exception('Nonce Table installation Failed');;

		}
	}
}

