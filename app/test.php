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
define('WP_INSTALLING', true);
include APP_PATH . '/inc/functions.php';

// test location for wp config
#define('WP_CONFIG_PATH', dirname(__FILE__) . '/../../demo-latest/wp-config.php');
define('WP_CONFIG_PATH', dirname(__FILE__) . '/../../../wp-ms-wildcard/wp-config.php');
define('ABSPATH', find_wp_abspath() );

include APP_PATH . '/components/Router.php';
include APP_PATH . '/components/Controller.php';
include APP_PATH . '/components/ReplaceHelper.php';
include APP_PATH . '/controllers/PageController.php';
include APP_PATH . '/controllers/ProcessController.php';

// loading wp conf file
$error_action = 'page/configError';
if ( ! is_file(WP_CONFIG_PATH) ) {
	$_REQUEST['r'] = $error_action;
} else {
	try{
		if ( !ABSPATH ) throw new Exception('Unable to find WordPress directory');
		@include_once( WP_CONFIG_PATH );
	} catch (\Exception $ex) {
		$_REQUEST['r'] = $error_action;
	}
}

Router::callAction();
exit;