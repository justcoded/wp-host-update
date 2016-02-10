<?php

define('APP_PATH', dirname(__FILE__));
define('VIEWS_PATH', APP_PATH . '/views');

include APP_PATH . '/inc/pa.php';
include APP_PATH . '/components/Router.php';
include APP_PATH . '/components/Controller.php';
include APP_PATH . '/controllers/IndexController.php';

\WpHUp\components\Router::callAction();
exit;