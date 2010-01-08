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
JCFactory::register('secKey','xfrhBj9Ofu0X2J5WNBqIJDIGs1sKjG0T::3r6PusEdJDUNly4MRvHVjcwL5');
JCFactory::register('appName','wp');
JCFactory::register('joomla_url','http://localhost/jconnekt/joomla/1.0.2/');
JCFactory::register('app_url','http://localhost/jconnekt/wp/1.0.2/');
JCFactory::register('api_url','http://localhost/jconnekt/wp/1.0.2/wp-content/plugins/wp_jconnekt/jconnekt_api/');
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