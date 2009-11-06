<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

class JCOpenIDStore extends Auth_OpenID_OpenIDStore{
	private  $db;
	function __construct(){
		$this->db=JFactory::getDBO();
	}
	
	function supportsCleanup()
    {
        return true;
    }
    
 	function storeAssociation($server_url, $association)
    {
        $sql="INSERT INTO #__jc_assoc_table (server_url,handle,secret,issued,lifetime,assoc_type) VALUES(".
         	"'{$server_url}','{$association->handle}','".addslashes($association->secret)."',".
        	"{$association->issued},{$association->lifetime},'{$association->assoc_type}'".
         	")";
        
        $this->db->Execute($sql);
        if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
    }
    
    function getAssociation($server_url,$handle){
    	$sql="SELECT * FROM #__jc_assoc_table WHERE server_url='$server_url' ";
    	if($handle){
    		$sql.=" AND handle='$handle'";
    	}
    	$sql.=" LIMIT 0,1";
    	$this->db->setQuery($sql);
    	$res=$this->db->loadObject();
    	if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());

    	//var_dump($sql,$res);dd();
    	if(isset($res) && $res->server_url){
    		$assoc= new Auth_OpenID_Association
    		($res->handle,stripslashes($res->secret),$res->issued,$res->lifetime,$res->assoc_type);
    		return $assoc;
    	}
    	return null;
    	
   	    }
    
 	function removeAssociation($server_url, $handle)
    {
        $sql="DELETE FROM #__jc_assoc_table WHERE server_url='$server_url' AND handle='$handle'";
        $this->db->Execute($sql);
        if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
    }
    
    function useNonce($server_url, $timestamp, $salt)
    {
    	$sql="SELECT * FROM #__jc_nonce WHERE server_url='$server_url' AND timestamp=$timestamp AND salt='$salt'";
    	$this->db->setQuery($sql);
    	$res=$this->loadObject();
    	if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
    	 
    	if(isset($res) && $res->server_url){
    		return false;
    	}
    	else{
    		$sql="INSERT INTO #__jc_nonce(server_url,timestamp,salt) VALUES(".
    		"'$server_url',$timestamp,'$salt'".
    		")";

    		$this->db->Execute($sql);
    		if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
			return true;
    	}
    }
    
  	function cleanupNonces()
    {
        $sql="DELETE FROM #__jc_nonce";
        $this->db->Execute($sql);
    	if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
		return true;
    }
    
	function cleanupAssociations()
    {
         $sql="DELETE FROM #__jc_assoc_table";
        $this->db->Execute($sql);
    	if($this->db->getErrorNum()) throw new Exception($this->db->getErrorMsg());
    	return true;
    }
    
	function cleanup()
    {
        return array($this->cleanupNonces(),
                     $this->cleanupAssociations());
    }
    
    function reset(){
    	$this->cleanup();
    }
    
    function loadObject(){}
}