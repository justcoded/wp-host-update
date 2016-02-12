<?php
/**
 * Special script to simplify DB update when moving WordPress site to another host or folder
 * 
 * @version 1.0
 * @author Alex Prokopenko
 * @package WordPress Host Update
 */

define('APP_PATH', dirname(__FILE__));
define('VIEWS_PATH', APP_PATH . '/views');
define( 'WP_INSTALLING', true );
define('WP_CONFIG_PATH', dirname(__FILE__) . '/../../townhouse/wp-config.php');
// test mode:
define('ABSPATH', dirname(__FILE__) . '/../../townhouse/');

include APP_PATH . '/inc/functions.php';
include APP_PATH . '/components/Router.php';
include APP_PATH . '/components/Controller.php';
include APP_PATH . '/controllers/PageController.php';
include APP_PATH . '/controllers/ProcessController.php';

// loading wp conf file
$error_action = 'page/configError';
if ( ! is_file(WP_CONFIG_PATH) ) {
	$_REQUEST['r'] = $error_action;
} else {
	try{
		@include_once( WP_CONFIG_PATH );
	} catch (\Exception $ex) {
		$_REQUEST['r'] = $error_action;
	}
}

\WpHUp\components\Router::callAction();
exit;