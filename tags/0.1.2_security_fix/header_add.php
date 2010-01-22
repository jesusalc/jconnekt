<?php 
$header="/**\n".
 "* @author		Arunoda Susiripala\n".
 "* @package		jconnect\n".
 "* @subpackage	elgg\n".
 "* @copyright	Arunoda Susiripala\n".
 "* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2\n".
 "*/";
	function readFiles($dirName,$parent=""){
		global $header;
		$dir=opendir($dirName);
		while (false !== ($file = readdir($dir))) {
			if($file==".." || $file==".") continue;
			if(is_dir($parent.$file)==true) {
				$parent2=$parent.$file."/";
				readFiles($parent.$file,$parent2);
				continue;
			}
			if(substr($file,-4)!=".php") continue;
			$file=$parent.$file;
	        add_header($file,$header);
	        echo "done: -> $file<br>";
	    }
	}
	
	//readFiles(".");
	
	function add_header($filename,$header){
		$content=file($filename);
		$content=implode("",$content);
		$php_have=(strstr($content,"<?php")==false)?false:true;
		if(!$php_have) return;
		$header_have=(strstr($content,"<?php\n/*")==false)?false:true;
		$end_header=strpos($content,"*/");

		if($header_have){
			$content="<?php\n".$header. substr($content,$end_header+2);
		}
		else{
			$content="<?php\n $header". substr($content,5);
		}
		
		file_put_contents($filename,$content);
		echo "$filename <br>";
	}
	
	readFiles("exApps/elgg_jconnect","exApps/elgg_jconnect/");
	