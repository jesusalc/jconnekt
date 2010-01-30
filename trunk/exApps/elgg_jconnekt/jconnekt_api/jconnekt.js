//Pure Javascript AJAX Call function..
var xmlhttp
function ajax_get(url, callback) {
	xmlhttp = null;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {// 4 = "loaded"
			if (xmlhttp.status == 200) {// 200 = "OK"
				callback(xmlhttp.responseText);
			} else {
				alert("Problem retrieving data:" + xmlhttp.statusText);
			}
		}
	};
	xmlhttp.open("GET", url, true);
	xmlhttp.send(null);
}


//JConnekt Code begins

var jconnekt_api_url;
var jconnekt_ref;
var sso_div_name;
var sso_current_url;

// sometimes it's possible to jconnekt to auth_deamon.php and server.php
// directly
// so we are going with the caller_url
// all the urls must contains end /
function JConnekt(app_name, api_url, joomla_url, caller_url) {
	jconnekt_api_url = api_url;
	jconnekt_ref = this;
	this.draw_login = function(div_name) {
		var div = document.getElementById(div_name);
		var return_to;
		// return to based on caller_url or api_url
		if (caller_url) {
			return_to = caller_url + "?go=auth_daemon.php";
		} else {
			return_to = api_url + "auth_daemon.php";
		}
		var login_url = joomla_url
				+ "?option=com_jconnect&controller=auth&format=raw&app_name="
				+ app_name + "&return_to=" + return_to;

		var popup = "javascript:popup_jconnekt('" + login_url + "',800,500)";
		div.innerHTML = "<a href=" + popup + "><img style='border:0px;' src='"
				+ api_url + "login.jpg" + "'></img><a>";
	};

	this.draw_sso = function(div_name, page_to_load) {
		var return_to;
		if (caller_url) {
			return_to = caller_url + "?go=auth_daemon.php";
		} else {
			return_to = api_url + "auth_daemon.php?a=b";
		}

		var div = document.getElementById(div_name);
		var src_url = joomla_url
				+ '?option=com_jconnect&controller=auth&task=request_token&app_name='
				+ app_name + '&return_to=' + return_to + '&goto='
				+ page_to_load;
		div.innerHTML = "<iframe style='display:none' width=0 height=0 src='"
				+ src_url + "'></iframe>";
	};

	this.ajax_validator = function(div_name, url) {

		sso_div_name = div_name;
		sso_current_url = url;

		// this calls the actual recursive like function
		check_token(null);
	};
}

function popup_jconnekt(url, width, height) {
	var top = screen.height / 2 - height / 2;
	var left = screen.width / 2 - width / 2;

	window
			.open(
					url,
					'Login',
					'left=' + left + ',scrollbars=no,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}

var last_state = null;
function check_token(data) {

	if (data) {
		var res;
		eval('res=' + data);

		if (res.valid == false) {
			jconnekt_ref.draw_sso(sso_div_name, sso_current_url);
		}
		setTimeout("check_token(null)", 5000);
	} else {
		ajax_get(jconnekt_api_url + 'auth_daemon.php?action=check_token', check_token);
	}
}