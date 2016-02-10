<?php

namespace WpHUp\controllers {

	class PageController extends \WpHUp\components\BaseController
	{
		public function actionIndex()
		{
			include VIEWS_PATH . '/index/index.php';
		}
		
		public function actionErrorTest()
		{
			include VIEWS_PATH . '/index/wp-conf-error.php';
		}
	}
	
}
