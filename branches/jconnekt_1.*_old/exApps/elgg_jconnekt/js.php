/**
 * This file contains some javascript function using may be from the Joomla...
 * 
 * @version 1.0
 * @package JConnect.exApps.elgg
 * @author Arunoda Susiripala
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public
 *          License version 2
 */

function ajaxPageLoad(url, callBack) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			callBack(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET", url, true);
	xmlhttp.send(null);
}