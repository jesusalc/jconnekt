<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	wp
* @copyright	JConnekt Team
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/

include_once dirname(dirname(__FILE__)).'/impl/userSync.php';
include_once dirname(dirname(__FILE__)).'/impl/misc.php';
include_once dirname(dirname(__FILE__)).'/impl/auth.php';
//require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

/**
 * Basic details...
 */
JCFactory::register('secKey','nBBinshAGYNL6qwTix6Y4vCBbyiCc2ew::szURlCFgA0IBe90dHs3FIXfjk');
JCFactory::register('appName','wp');
JCFactory::register('joomla_url','http://localhost/jconnekt/joomla');
JCFactory::register('app_url','http://localhost/jconnekt/wp/');
JCFactory::register('api_url','http://localhost/jconnekt/wp/wp-content/plugins/wp_jconnekt/jconnekt_api/');
JCFactory::register('caller','jconnekt.php');



/**
 * assion subclass of JCUserSync
 */
JCFactory::register('userSync',new JCElggUserSync());

/**
 * assion subclass of JCMisc
 */
JCFactory::register('misc',new JCElggMisc());

/**
 * assion subclass of JCAuth
 */
JCFactory::register('auth',new JCWPAuth());

?>