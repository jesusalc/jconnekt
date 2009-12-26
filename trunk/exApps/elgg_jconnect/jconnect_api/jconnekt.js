function JConnekt(app_name,api_url,joomla_url){
	this.draw_login=function(div_name){
		var div=document.getElementById(div_name);
		var return_to=api_url+"auth_daemon.php";
		var login_url=joomla_url+
			"?option=com_jconnect&controller=auth&format=raw&app_name="+app_name+
			"&return_to="+return_to;
		
		var popup="javascript:popup_jconnekt('"+login_url+"',800,500)";
		div.innerHTML="<a href="+popup+">Login<a>";
	};
	
	this.draw_sso=function(div_name,page_to_load){
		var div=document.getElementById(div_name);
		var src_url=joomla_url + '?option=com_jconnect&controller=auth&task=request_token&app_name='+app_name+
			'&return_to='+api_url+'auth_daemon.php?goto=' + page_to_load;
		div.innerHTML="<iframe width=0 height=0 src='"+src_url+"'></iframe>";
	};
	
	this.ajax_validator=function(){
		
	}
}

function popup_jconnekt(url,width,height){
	var top=screen.height/2-height/2;
	var left=screen.width/2-width/2;
	
	window.open(url,'Login','left='+left+',scrollbars=no,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}