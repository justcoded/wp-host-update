<?php

namespace WpHUp\components {

	class Router
	{
		public static function callAction()
		{
			$request_callback = self::parseRequest();
			
			$controller = new $request_callback[0]();
			call_user_func( array($controller, $request_callback[1]) );
		}
		
		public static function parseRequest()
		{
			$controller = 'index';
			$action = 'index';
			
			// our request can be in post or get, doesn't matter
			if ( !empty($_REQUEST['r']) ) {
				$route = explode('/', $_REQUEST['r'], 2);
				if ( count($route) == 1 ) {
					$route[] = 'index';
				}
				
				$controller = $route[0];
				$action = $route[1];
			}
			
			$controller = '\\WpHUp\\controllers\\' . ucfirst($controller) . 'Controller';
			$action = 'action' . ucfirst($action);
			
			return array($controller, $action);
		}
	}
	
}
