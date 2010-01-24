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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

/**
 * Basic details...
 */
JCFactory::register('secKey',datalist_get('secKey'));
JCFactory::register('appName',datalist_get('appName'));
JCFactory::register('joomla_url',datalist_get('joomla_url'));
$elgg_root=$CONFIG->wwwroot;
$elgg_root=(substr($elgg_root,strlen($elgg_root)-1,1)=='/')?$elgg_root:$elgg_root."/";
JCFactory::register('app_url',$elgg_root);
JCFactory::register('api_url',$elgg_root.'mod/elgg_jconnekt/jconnekt_api/');


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
JCFactory::register('auth',new JCElggAuth());


?>