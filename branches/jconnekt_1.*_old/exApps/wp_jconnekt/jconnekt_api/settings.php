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

$secret_key=get_option('jconnekt_secret_key');
$app_name=get_option('jconnekt_app_name');
$joomla_url=get_option('jconnekt_joomla_url');
$wp_url=site_url();

if(substr($wp_url,strlen($wp_url)-1,1)!='/') $wp_url.="/";
		
JCFactory::register('secKey',$secret_key);
JCFactory::register('appName',$app_name);
JCFactory::register('joomla_url',$joomla_url);
JCFactory::register('app_url',$wp_url);
JCFactory::register('api_url',$wp_url.'wp-content/plugins/wp_jconnekt/jconnekt_api/');
JCFactory::register('caller','?jconnekt=1');



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